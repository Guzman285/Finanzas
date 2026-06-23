<?php

namespace Controllers;

use MVC\Router;
use Model\Banco;
use Exception;

class BancoController
{

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
}
