<?php

namespace Controllers;

use MVC\Router;
use Model\Cuenta;
use Exception;

class CuentaController
{

    public static function index(Router $router)
    {
        $router->render('cuentas/index', [
            'titulo' => 'Control de Cuentas'
        ]);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $cuentas = Cuenta::fetchArray("
                SELECT 
                    c.cta_id,
                    c.cta_nombre,
                    c.cta_tipo,
                    c.cta_saldo,
                    c.cta_banco_id,
                    b.cta_nombre AS banco_nombre
                FROM cuentas c
                LEFT JOIN cuentas b ON c.cta_banco_id = b.cta_id
                WHERE c.cta_situacion = 1
                ORDER BY c.cta_id
            ");

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => count($cuentas) . ' cuenta/s encontradas',
                'datos'   => $cuentas
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al obtener cuentas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        try {
            $cuenta = new Cuenta($_POST);
            $cuenta->crear();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Cuenta creada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al crear cuenta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        try {
            $cuenta = Cuenta::find($_POST['cta_id']);
            if (!$cuenta) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Cuenta no encontrada']);
                return;
            }
            $cuenta->sincronizar($_POST);
            $cuenta->actualizar();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Cuenta actualizada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al actualizar cuenta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $cuenta = Cuenta::find($_POST['cta_id']);
            if (!$cuenta) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Cuenta no encontrada']);
                return;
            }
            $cuenta->sincronizar(['cta_situacion' => 0]);
            $cuenta->actualizar();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Cuenta eliminada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al eliminar cuenta',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
