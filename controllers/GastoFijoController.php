<?php

namespace Controllers;

use MVC\Router;
use Model\GastoFijo;
use Exception;

class GastoFijoController
{
    public static function index(Router $router)
    {
        $router->render('gastos_fijos/index', [
            'titulo' => 'Gastos Fijos'
        ]);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $datos = GastoFijo::fetchArray("
                SELECT
                    gf.gf_id,
                    gf.gf_descripcion,
                    gf.gf_monto_estimado,
                    gf.gf_dia_pago,
                    gf.gf_cuenta_id,
                    gf.gf_categoria_id,
                    c.cta_nombre  AS cuenta_nombre,
                    cat.cat_nombre AS categoria_nombre
                FROM gastos_fijos gf
                INNER JOIN cuentas     c   ON gf.gf_cuenta_id    = c.cta_id
                INNER JOIN categorias  cat ON gf.gf_categoria_id = cat.cat_id
                WHERE gf.gf_situacion = 1
                ORDER BY gf.gf_dia_pago, gf.gf_descripcion
            ");

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => count($datos) . ' gasto/s encontrados',
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
            if (
                empty($_POST['gf_descripcion']) ||
                empty($_POST['gf_cuenta_id'])   ||
                empty($_POST['gf_categoria_id'])
            ) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }
            $gf = new GastoFijo($_POST);
            $gf->crear();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Gasto fijo creado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al crear', 'detalle' => $e->getMessage()]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        try {
            $gf = GastoFijo::find($_POST['gf_id']);
            if (!$gf) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Registro no encontrado']);
                return;
            }
            $gf->sincronizar($_POST);
            $gf->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Gasto fijo actualizado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $gf = GastoFijo::find($_POST['gf_id']);
            if (!$gf) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Registro no encontrado']);
                return;
            }
            $gf->sincronizar(['gf_situacion' => 0]);
            $gf->actualizar();

            echo json_encode(['codigo' => 1, 'mensaje' => 'Gasto fijo eliminado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/gastos_fijos/pagar
    // Registra un movimiento de gasto y descuenta saldo de la cuenta
    public static function pagarAPI()
    {
        getHeadersApi();
        try {
            $gf_id  = (int)($_POST['gf_id']  ?? 0);
            $monto  = (float)($_POST['monto'] ?? 0);
            $fecha  = trim($_POST['fecha']    ?? '');

            if (!$gf_id || $monto <= 0 || !$fecha) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            $gf = GastoFijo::fetchFirst("
                SELECT gf.*, c.cta_saldo
                FROM gastos_fijos gf
                INNER JOIN cuentas c ON gf.gf_cuenta_id = c.cta_id
                WHERE gf.gf_id = {$gf_id}
            ");

            if (!$gf) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Gasto fijo no encontrado']);
                return;
            }

            $db = \ActiveRecord\ActiveRecord::getDB();

            // Insertar movimiento
            $db->prepare("
                INSERT INTO movimientos
                    (mov_id, mov_tipo, mov_descripcion, mov_monto, mov_fecha,
                     mov_cuenta_origen_id, mov_categoria_id, mov_gasto_fijo_id)
                VALUES
                    (seq_movimientos.nextval, 'gasto', :desc, :monto, :fecha,
                     :cuenta_id, :cat_id, :gf_id)
            ")->execute([
                ':desc'      => $gf['gf_descripcion'],
                ':monto'     => $monto,
                ':fecha'     => $fecha,
                ':cuenta_id' => $gf['gf_cuenta_id'],
                ':cat_id'    => $gf['gf_categoria_id'],
                ':gf_id'     => $gf_id
            ]);

            // Descontar saldo
            $db->prepare("
                UPDATE cuentas
                SET cta_saldo = cta_saldo - :monto
                WHERE cta_id = :cta_id
            ")->execute([
                ':monto'  => $monto,
                ':cta_id' => $gf['gf_cuenta_id']
            ]);

            echo json_encode(['codigo' => 1, 'mensaje' => 'Pago registrado correctamente']);
        } catch (Exception $e) {
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar pago', 'detalle' => $e->getMessage()]);
        }
    }
}
