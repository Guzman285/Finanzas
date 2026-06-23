<?php

namespace Controllers;

use MVC\Router;
use Model\Deuda;
use Model\DeudaMovimiento;
use Model\Categoria;
use Exception;

class DeudaController
{
    public static function index(Router $router)
    {
        $router->render('deudas/index', [
            'titulo' => 'Control de Deudas'
        ]);
    }

    // GET /API/deudas/buscar
    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $datos = Deuda::fetchArray("
                SELECT
                    d.deu_id,
                    d.deu_descripcion,
                    d.deu_entidad,
                    d.deu_tipo,
                    d.deu_monto_total,
                    d.deu_monto_pagado,
                    ROUND(d.deu_monto_total - d.deu_monto_pagado, 2) AS deu_saldo_pendiente,
                    d.deu_cuota_mensual,
                    d.deu_limite_credito,
                    d.deu_tasa_interes,
                    d.deu_dia_corte,
                    d.deu_dia_pago,
                    d.deu_fecha_inicio,
                    d.deu_fecha_fin_est,
                    d.deu_cuenta_id,
                    d.deu_descuento_nomina,
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

    // POST /API/deudas/guardar
    public static function guardarAPI()
    {
        getHeadersApi();
        try {
            if (empty($_POST['deu_descripcion']) || empty($_POST['deu_cuenta_id']) || empty($_POST['deu_fecha_inicio']) || empty($_POST['deu_tipo'])) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            // Opcionales
            if (empty($_POST['deu_fecha_fin_est']))  $_POST['deu_fecha_fin_est']  = null;
            if (empty($_POST['deu_entidad']))         $_POST['deu_entidad']         = null;
            if (empty($_POST['deu_limite_credito']))  $_POST['deu_limite_credito']  = null;
            if (empty($_POST['deu_dia_corte']))       $_POST['deu_dia_corte']       = null;
            if (empty($_POST['deu_dia_pago']))        $_POST['deu_dia_pago']        = null;
            if (!isset($_POST['deu_descuento_nomina'])) $_POST['deu_descuento_nomina'] = 0;

            $deuda = new Deuda($_POST);
            $deuda->crear();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Deuda creada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/deudas/modificar
    public static function modificarAPI()
    {
        getHeadersApi();
        try {
            $deuda = Deuda::find($_POST['deu_id']);
            if (!$deuda) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Deuda no encontrada']);
                return;
            }

            if (empty($_POST['deu_fecha_fin_est']))  $_POST['deu_fecha_fin_est']  = null;
            if (empty($_POST['deu_entidad']))         $_POST['deu_entidad']         = null;
            if (empty($_POST['deu_limite_credito']))  $_POST['deu_limite_credito']  = null;
            if (empty($_POST['deu_dia_corte']))       $_POST['deu_dia_corte']       = null;
            if (empty($_POST['deu_dia_pago']))        $_POST['deu_dia_pago']        = null;
            if (!isset($_POST['deu_descuento_nomina'])) $_POST['deu_descuento_nomina'] = 0;

            $deuda->sincronizar($_POST);
            $deuda->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Deuda actualizada correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/deudas/eliminar
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

    // GET /API/deudas/movimientos?deu_id=X
    public static function movimientosAPI()
    {
        getHeadersApi();
        try {
            $deu_id = (int)($_GET['deu_id'] ?? 0);
            if (!$deu_id) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'ID requerido']);
                return;
            }

            $datos = DeudaMovimiento::fetchArray("
                SELECT
                    dm.dm_id,
                    dm.dm_tipo,
                    dm.dm_fecha,
                    dm.dm_descripcion,
                    dm.dm_monto_total,
                    dm.dm_abono_capital,
                    dm.dm_interes,
                    dm.dm_cuenta_id,
                    c.cta_nombre AS cuenta_nombre
                FROM deuda_movimientos dm
                LEFT JOIN cuentas c ON dm.dm_cuenta_id = c.cta_id
                WHERE dm.dm_deu_id = {$deu_id}
                  AND dm.dm_situacion = 1
                ORDER BY dm.dm_fecha DESC
            ");

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => count($datos) . ' movimiento/s',
                'datos'   => $datos
            ]);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al buscar movimientos', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/deudas/pago
    // Pago con desglose capital + interés (del estado de cuenta)
    public static function pagoAPI()
    {
        getHeadersApi();
        try {
            $deu_id  = (int)($_POST['deu_id']        ?? 0);
            $total   = (float)($_POST['dm_monto_total']   ?? 0);
            $capital = (float)($_POST['dm_abono_capital'] ?? 0);
            $interes = (float)($_POST['dm_interes']       ?? 0);
            $fecha   = trim($_POST['dm_fecha']            ?? '');
            $desc    = trim($_POST['dm_descripcion']      ?? '');

            if (!$deu_id || $total <= 0 || !$fecha || !$desc) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            if (round($capital + $interes, 2) > round($total + 0.01, 2)) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Capital + Interés no puede superar el monto total del pago']);
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

            $db = \ActiveRecord\ActiveRecord::getDB();

            // 1. Insertar movimiento de gasto (lo que salió de la cuenta)
            $cat = Categoria::fetchFirst("SELECT cat_id FROM categorias WHERE cat_nombre = 'Deuda' AND cat_situacion = 1");
            $cat_id = $cat ? $cat['cat_id'] : null;

            $db->prepare("
                INSERT INTO movimientos
                    (mov_id, mov_tipo, mov_descripcion, mov_monto, mov_fecha,
                     mov_cuenta_origen_id, mov_categoria_id, mov_deuda_id)
                VALUES
                    (seq_movimientos.nextval, 'gasto', :desc, :monto, :fecha,
                     :cuenta_id, :cat_id, :deu_id)
            ")->execute([
                ':desc'      => $desc,
                ':monto'     => $total,
                ':fecha'     => $fecha,
                ':cuenta_id' => $deuda['deu_cuenta_id'],
                ':cat_id'    => $cat_id,
                ':deu_id'    => $deu_id
            ]);

            $mov_id = $db->lastInsertId();

            // 2. Registrar en deuda_movimientos con desglose
            $dm = new DeudaMovimiento([
                'dm_deu_id'        => $deu_id,
                'dm_tipo'          => 'pago',
                'dm_fecha'         => $fecha,
                'dm_descripcion'   => $desc,
                'dm_monto_total'   => $total,
                'dm_abono_capital' => $capital,
                'dm_interes'       => $interes,
                'dm_cuenta_id'     => $deuda['deu_cuenta_id'],
                'dm_mov_id'        => $mov_id
            ]);
            $dm->crear();

            // 3. Actualizar capital pendiente según tipo de deuda
            if ($deuda['deu_tipo'] === 'revolving') {
                // Tarjeta: el capital reduce deu_monto_total
                $db->prepare("
                    UPDATE deudas
                    SET deu_monto_total = deu_monto_total - :capital
                    WHERE deu_id = :deu_id
                ")->execute([':capital' => $capital, ':deu_id' => $deu_id]);
            } else {
                // Fija: suma a monto pagado
                $db->prepare("
                    UPDATE deudas
                    SET deu_monto_pagado = deu_monto_pagado + :capital
                    WHERE deu_id = :deu_id
                ")->execute([':capital' => $capital, ':deu_id' => $deu_id]);
            }

            // 4. Descontar saldo de la cuenta bancaria
            $db->prepare("
                UPDATE cuentas SET cta_saldo = cta_saldo - :monto WHERE cta_id = :cta_id
            ")->execute([':monto' => $total, ':cta_id' => $deuda['deu_cuenta_id']]);

            echo json_encode(['codigo' => 1, 'mensaje' => 'Pago registrado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar pago', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/deudas/consumo
    // Nuevo cargo/consumo con tarjeta (sube la deuda)
    public static function consumoAPI()
    {
        getHeadersApi();
        try {
            $deu_id = (int)($_POST['deu_id']          ?? 0);
            $monto  = (float)($_POST['dm_monto_total'] ?? 0);
            $fecha  = trim($_POST['dm_fecha']          ?? '');
            $desc   = trim($_POST['dm_descripcion']    ?? '');

            if (!$deu_id || $monto <= 0 || !$fecha || !$desc) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            $deuda = Deuda::find($deu_id);
            if (!$deuda) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Deuda no encontrada']);
                return;
            }

            if ($deuda->deu_tipo !== 'revolving') {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Solo se pueden registrar consumos en deudas de tipo revolving (tarjeta)']);
                return;
            }

            $db = \ActiveRecord\ActiveRecord::getDB();

            // Categoría consumo tarjeta
            $cat = Categoria::fetchFirst("SELECT cat_id FROM categorias WHERE cat_nombre = 'Consumo Tarjeta' AND cat_situacion = 1");
            $cat_id = $cat ? $cat['cat_id'] : null;

            // 1. Movimiento de gasto (el consumo afecta la cuenta tarjeta como gasto)
            $db->prepare("
                INSERT INTO movimientos
                    (mov_id, mov_tipo, mov_descripcion, mov_monto, mov_fecha,
                     mov_cuenta_origen_id, mov_categoria_id, mov_deuda_id)
                VALUES
                    (seq_movimientos.nextval, 'gasto', :desc, :monto, :fecha,
                     :cuenta_id, :cat_id, :deu_id)
            ")->execute([
                ':desc'      => $desc,
                ':monto'     => $monto,
                ':fecha'     => $fecha,
                ':cuenta_id' => $deuda->deu_cuenta_id,
                ':cat_id'    => $cat_id,
                ':deu_id'    => $deu_id
            ]);

            $mov_id = $db->lastInsertId();

            // 2. Registrar en deuda_movimientos
            $dm = new DeudaMovimiento([
                'dm_deu_id'      => $deu_id,
                'dm_tipo'        => 'consumo',
                'dm_fecha'       => $fecha,
                'dm_descripcion' => $desc,
                'dm_monto_total' => $monto,
                'dm_cuenta_id'   => $deuda->deu_cuenta_id,
                'dm_mov_id'      => $mov_id
            ]);
            $dm->crear();

            // 3. Sube deu_monto_total (aumenta la deuda)
            $db->prepare("
                UPDATE deudas SET deu_monto_total = deu_monto_total + :monto WHERE deu_id = :deu_id
            ")->execute([':monto' => $monto, ':deu_id' => $deu_id]);

            echo json_encode(['codigo' => 1, 'mensaje' => 'Consumo registrado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar consumo', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/deudas/interes
    // Cargo de interés mensual (sube la deuda sin pago)
    public static function interesAPI()
    {
        getHeadersApi();
        try {
            $deu_id = (int)($_POST['deu_id']          ?? 0);
            $monto  = (float)($_POST['dm_monto_total'] ?? 0);
            $fecha  = trim($_POST['dm_fecha']          ?? '');
            $desc   = trim($_POST['dm_descripcion']    ?? 'Cargo de interés mensual');

            if (!$deu_id || $monto <= 0 || !$fecha) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            $deuda = Deuda::find($deu_id);
            if (!$deuda) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Deuda no encontrada']);
                return;
            }

            $db = \ActiveRecord\ActiveRecord::getDB();

            // 1. Registrar en deuda_movimientos (no genera movimiento bancario)
            $dm = new DeudaMovimiento([
                'dm_deu_id'      => $deu_id,
                'dm_tipo'        => 'interes',
                'dm_fecha'       => $fecha,
                'dm_descripcion' => $desc,
                'dm_monto_total' => $monto
            ]);
            $dm->crear();

            // 2. Sube deu_monto_total
            $db->prepare("
                UPDATE deudas SET deu_monto_total = deu_monto_total + :monto WHERE deu_id = :deu_id
            ")->execute([':monto' => $monto, ':deu_id' => $deu_id]);

            echo json_encode(['codigo' => 1, 'mensaje' => 'Interés registrado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar interés', 'detalle' => $e->getMessage()]);
        }
    }
}
