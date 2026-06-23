<?php
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\CuentaController;
use Controllers\BancoController;
use Controllers\CategoriaController;
use Controllers\GastoFijoController;
use Controllers\DeudaController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// Inicio
$router->get('/', [AppController::class, 'index']);

// Cuentas
$router->get('/cuentas',                [CuentaController::class, 'index']);
$router->get('/API/cuentas/buscar',     [CuentaController::class, 'buscarAPI']);
$router->post('/API/cuentas/guardar',   [CuentaController::class, 'guardarAPI']);
$router->post('/API/cuentas/modificar', [CuentaController::class, 'modificarAPI']);
$router->post('/API/cuentas/eliminar',  [CuentaController::class, 'eliminarAPI']);

// Bancos
$router->get('/bancos',                  [BancoController::class, 'index']);
$router->get('/API/bancos/buscar',       [BancoController::class, 'buscarAPI']);
$router->post('/API/bancos/guardar',     [BancoController::class, 'guardarAPI']);
$router->post('/API/bancos/modificar',   [BancoController::class, 'modificarAPI']);
$router->post('/API/bancos/eliminar',    [BancoController::class, 'eliminarAPI']);

// Categorías
$router->get('/categorias',                [CategoriaController::class, 'index']);
$router->get('/API/categorias/buscar',     [CategoriaController::class, 'buscarAPI']);
$router->post('/API/categorias/guardar',   [CategoriaController::class, 'guardarAPI']);
$router->post('/API/categorias/modificar', [CategoriaController::class, 'modificarAPI']);
$router->post('/API/categorias/eliminar',  [CategoriaController::class, 'eliminarAPI']);

// Gastos Fijos
$router->get('/gastos_fijos',                [GastoFijoController::class, 'index']);
$router->get('/API/gastos_fijos/buscar',     [GastoFijoController::class, 'buscarAPI']);
$router->post('/API/gastos_fijos/guardar',   [GastoFijoController::class, 'guardarAPI']);
$router->post('/API/gastos_fijos/modificar', [GastoFijoController::class, 'modificarAPI']);
$router->post('/API/gastos_fijos/eliminar',  [GastoFijoController::class, 'eliminarAPI']);
$router->post('/API/gastos_fijos/pagar',     [GastoFijoController::class, 'pagarAPI']);

// Deudas
$router->get('/deudas',                [DeudaController::class, 'index']);
$router->get('/API/deudas/buscar',     [DeudaController::class, 'buscarAPI']);
$router->post('/API/deudas/guardar',   [DeudaController::class, 'guardarAPI']);
$router->post('/API/deudas/modificar', [DeudaController::class, 'modificarAPI']);
$router->post('/API/deudas/eliminar',  [DeudaController::class, 'eliminarAPI']);
$router->post('/API/deudas/abonar',    [DeudaController::class, 'abonarAPI']);

// Comprueba y valida las rutas
$router->comprobarRutas();
