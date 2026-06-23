<?php

namespace Controllers;

use MVC\Router;
use Model\Categoria;
use Exception;

class CategoriaController
{
    public static function index(Router $router)
    {
        $router->render('categorias/index', [
            'titulo' => 'Control de Categorías'
        ]);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $categorias = Categoria::fetchArray("
                SELECT cat_id, cat_nombre, cat_tipo
                FROM categorias
                WHERE cat_situacion = 1
                ORDER BY cat_tipo, cat_nombre
            ");

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => count($categorias) . ' categoria/s encontradas',
                'datos'   => $categorias
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al obtener categorías',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        try {
            if (empty($_POST['cat_nombre']) || empty($_POST['cat_tipo'])) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }
            $categoria = new Categoria($_POST);
            $categoria->crear();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Categoría creada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al crear categoría',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();
        try {
            $categoria = Categoria::find($_POST['cat_id']);
            if (!$categoria) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Categoría no encontrada']);
                return;
            }
            $categoria->sincronizar($_POST);
            $categoria->actualizar();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Categoría actualizada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al actualizar categoría',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        try {
            $categoria = Categoria::find($_POST['cat_id']);
            if (!$categoria) {
                echo json_encode(['codigo' => 0, 'mensaje' => 'Categoría no encontrada']);
                return;
            }
            $categoria->sincronizar(['cat_situacion' => 0]);
            $categoria->actualizar();

            echo json_encode([
                'codigo'  => 1,
                'mensaje' => 'Categoría eliminada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'codigo'  => 0,
                'mensaje' => 'Error al eliminar categoría',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
