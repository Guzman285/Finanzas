<?php
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\CuentaController;
use Controllers\BancoController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// Inicio
$router->get('/', [AppController::class, 'index']);

// Cuentas
$router->get('/cuentas', [CuentaController::class, 'index']);
$router->get('/API/cuentas/buscar',     [CuentaController::class, 'buscarAPI']);
$router->post('/API/cuentas/guardar',   [CuentaController::class, 'guardarAPI']);
$router->post('/API/cuentas/modificar', [CuentaController::class, 'modificarAPI']);
$router->post('/API/cuentas/eliminar',  [CuentaController::class, 'eliminarAPI']);

// Bancos
$router->get('/API/bancos/buscar', [BancoController::class, 'buscarAPI']);

// Comprueba y valida las rutas
$router->comprobarRutas();
