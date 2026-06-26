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
                    c.cta_limite_credito,
                    c.cta_banco_id,
                    c.cta_numero,
                    b.ban_nombre AS banco_nombre
                FROM cuentas c
                LEFT JOIN bancos b ON c.cta_banco_id = b.ban_id
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
            if (empty($_POST['cta_nombre']) || empty($_POST['cta_tipo'])) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Nombre y tipo son requeridos']);
                return;
            }

            // Tarjeta de crédito: saldo inicia en 0, el monto ingresado es el límite
            if ($_POST['cta_tipo'] === 'tarjeta_credito') {
                $_POST['cta_limite_credito'] = $_POST['cta_saldo'] ?? 0;
                $_POST['cta_saldo'] = 0;
            } else {
                $_POST['cta_limite_credito'] = null;
            }

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
