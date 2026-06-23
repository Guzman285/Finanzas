<?php

namespace Controllers;

use MVC\Router;
use Model\Banco;
use Exception;

class BancoController
{

    public static function index(Router $router)
    {
        $router->render('bancos/index', [
            'titulo' => 'Control de Bancos'
        ]);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $bancos = Banco::fetchArray("
                SELECT ban_id, ban_nombre
                FROM bancos
                WHERE ban_situacion = 1
                ORDER BY ban_nombre
            ");

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => count($bancos) . ' banco/s encontrados',
                'datos'   => $bancos
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al obtener bancos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        try {
            if (empty($_POST['ban_nombre'])) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre es requerido']);
                return;
            }
            $banco = new Banco($_POST);
            $banco->crear();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Banco creado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al crear banco',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        try {
            $banco = Banco::find($_POST['ban_id']);
            if (!$banco) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Banco no encontrado']);
                return;
            }
            $banco->sincronizar($_POST);
            $banco->actualizar();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Banco actualizado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al actualizar banco',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $banco = Banco::find($_POST['ban_id']);
            if (!$banco) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Banco no encontrado']);
                return;
            }
            $banco->sincronizar(['ban_situacion' => 0]);
            $banco->actualizar();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Banco eliminado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al eliminar banco',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
