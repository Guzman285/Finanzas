<?php

namespace Controllers;

use MVC\Router;
use Model\Deuda;
use Exception;

class DeudaController
{
    public static function index(Router $router)
    {
        $router->render('deudas/index', [
            'titulo' => 'Control de Deudas'
        ]);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $datos = Deuda::fetchArray("
                SELECT
                    d.deu_id,
                    d.deu_descripcion,
                    d.deu_monto_total,
                    d.deu_monto_pagado,
                    ROUND(d.deu_monto_total - d.deu_monto_pagado, 2) AS deu_saldo_pendiente,
                    d.deu_cuota_mensual,
                    d.deu_fecha_inicio,
                    d.deu_fecha_fin_est,
                    d.deu_cuenta_id,
                    c.cta_nombre AS cuenta_nombre
                FROM deudas d
                INNER JOIN cuentas c ON d.deu_cuenta_id = c.cta_id
                WHERE d.deu_situacion = 1
                ORDER BY d.deu_fecha_inicio DESC
            ");

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => count($datos) . ' deuda/s encontradas',
                'datos'   => $datos
            ]);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al buscar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        try {
            if (empty($_POST['deu_descripcion']) || empty($_POST['deu_cuenta_id']) || empty($_POST['deu_fecha_inicio'])) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }
            // deu_fecha_fin_est es opcional
            if (empty($_POST['deu_fecha_fin_est'])) $_POST['deu_fecha_fin_est'] = null;

            $deuda = new Deuda($_POST);
            $deuda->crear();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Deuda creada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear', 'detalle' => $e->getMessage()]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        try {
            $deuda = Deuda::find($_POST['deu_id']);
            if (!$deuda) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Deuda no encontrada']);
                return;
            }
            if (empty($_POST['deu_fecha_fin_est'])) $_POST['deu_fecha_fin_est'] = null;
            $deuda->sincronizar($_POST);
            $deuda->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Deuda actualizada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $deuda = Deuda::find($_POST['deu_id']);
            if (!$deuda) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Deuda no encontrada']);
                return;
            }
            $deuda->sincronizar(['deu_situacion' => 0]);
            $deuda->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Deuda eliminada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/deudas/abonar
    // Registra un abono: crea movimiento de gasto, suma a deu_monto_pagado y descuenta saldo de cuenta
    public static function abonarAPI()
    {
        getHeadersApi();
        try {
            $deu_id = (int)($_POST['deu_id'] ?? 0);
            $monto  = (float)($_POST['monto'] ?? 0);
            $fecha  = trim($_POST['fecha'] ?? '');

            if (!$deu_id || $monto <= 0 || !$fecha) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            $deuda = Deuda::fetchFirst("
                SELECT d.*, c.cta_saldo
                FROM deudas d
                INNER JOIN cuentas c ON d.deu_cuenta_id = c.cta_id
                WHERE d.deu_id = {$deu_id}
            ");

            if (!$deuda) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Deuda no encontrada']);
                return;
            }

            $pendiente = round($deuda['deu_monto_total'] - $deuda['deu_monto_pagado'], 2);
            if ($monto > $pendiente) {
                echo json_encode(['codigo' => 0, 'mensaje' => "El abono (Q{$monto}) supera el saldo pendiente (Q{$pendiente})"]);
                return;
            }

            // Categoría Deuda (cat_nombre = 'Deuda')
            $cat = \Model\Categoria::fetchFirst("SELECT cat_id FROM categorias WHERE cat_nombre = 'Deuda' AND cat_situacion = 1");
            $cat_id = $cat ? $cat['cat_id'] : null;

            $db = \ActiveRecord\ActiveRecord::getDB();

            // Insertar movimiento
            $db->prepare("
                INSERT INTO movimientos
                    (mov_id, mov_tipo, mov_descripcion, mov_monto, mov_fecha,
                     mov_cuenta_origen_id, mov_categoria_id, mov_deuda_id)
                VALUES
                    (seq_movimientos.nextval, 'gasto', :desc, :monto, :fecha,
                     :cuenta_id, :cat_id, :deu_id)
            ")->execute([
                ':desc'      => 'Abono: ' . $deuda['deu_descripcion'],
                ':monto'     => $monto,
                ':fecha'     => $fecha,
                ':cuenta_id' => $deuda['deu_cuenta_id'],
                ':cat_id'    => $cat_id,
                ':deu_id'    => $deu_id
            ]);

            // Actualizar monto pagado
            $db->prepare("
                UPDATE deudas
                SET deu_monto_pagado = deu_monto_pagado + :monto
                WHERE deu_id = :deu_id
            ")->execute([':monto' => $monto, ':deu_id' => $deu_id]);

            // Descontar saldo de cuenta
            $db->prepare("
                UPDATE cuentas
                SET cta_saldo = cta_saldo - :monto
                WHERE cta_id = :cta_id
            ")->execute([':monto' => $monto, ':cta_id' => $deuda['deu_cuenta_id']]);

            echo json_encode(['codigo' => 1, 'mensaje' => 'Abono registrado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar abono', 'detalle' => $e->getMessage()]);
        }
    }
}
