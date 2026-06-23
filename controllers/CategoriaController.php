<?php

class CategoriaController extends AppController
{
    private CategoriaModel $model;

    public function __construct()
    {
        $this->model = new CategoriaModel();
    }

    // GET /categorias
    public function index(): void
    {
        $this->render('categorias/index', ['titulo' => 'Categorías']);
    }

    // GET /API/categorias/buscar
    public function buscarAPI(): void
    {
        try {
            $datos = $this->model->buscar();
            $this->json([
                'codigo'  => 1,
                'mensaje' => 'OK',
                'datos'   => $datos,
            ]);
        } catch (Exception $e) {
            $this->json(['codigo' => 0, 'mensaje' => 'Error al buscar', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/categorias/guardar
    public function guardarAPI(): void
    {
        try {
            $data = [
                'cat_nombre' => trim($_POST['cat_nombre'] ?? ''),
                'cat_tipo'   => trim($_POST['cat_tipo']   ?? ''),
            ];

            if ($data['cat_nombre'] === '' || $data['cat_tipo'] === '') {
                $this->json(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            $ok = $this->model->guardar($data);
            $this->json([
                'codigo'  => $ok ? 1 : 0,
                'mensaje' => $ok ? 'Categoría guardada' : 'No se pudo guardar',
            ]);
        } catch (Exception $e) {
            $this->json(['codigo' => 0, 'mensaje' => 'Error al guardar', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/categorias/modificar
    public function modificarAPI(): void
    {
        try {
            $data = [
                'cat_id'     => (int)($_POST['cat_id']     ?? 0),
                'cat_nombre' => trim($_POST['cat_nombre'] ?? ''),
                'cat_tipo'   => trim($_POST['cat_tipo']   ?? ''),
            ];

            if (!$data['cat_id'] || $data['cat_nombre'] === '' || $data['cat_tipo'] === '') {
                $this->json(['codigo' => 0, 'mensaje' => 'Datos incompletos']);
                return;
            }

            $ok = $this->model->modificar($data);
            $this->json([
                'codigo'  => $ok ? 1 : 0,
                'mensaje' => $ok ? 'Categoría modificada' : 'No se pudo modificar',
            ]);
        } catch (Exception $e) {
            $this->json(['codigo' => 0, 'mensaje' => 'Error al modificar', 'detalle' => $e->getMessage()]);
        }
    }

    // POST /API/categorias/eliminar
    public function eliminarAPI(): void
    {
        try {
            $data = ['cat_id' => (int)($_POST['cat_id'] ?? 0)];

            if (!$data['cat_id']) {
                $this->json(['codigo' => 0, 'mensaje' => 'ID inválido']);
                return;
            }

            $ok = $this->model->eliminar($data);
            $this->json([
                'codigo'  => $ok ? 1 : 0,
                'mensaje' => $ok ? 'Categoría eliminada' : 'No se pudo eliminar',
            ]);
        } catch (Exception $e) {
            $this->json(['codigo' => 0, 'mensaje' => 'Error al eliminar', 'detalle' => $e->getMessage()]);
        }
    }
}
