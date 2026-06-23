<?php

namespace Model;

class Banco extends ActiveRecord
{

    protected static $tabla = 'bancos';
    protected static $idTabla = 'ban_id';
    protected static $columnasDB = [
        'ban_id',
        'ban_nombre',
        'ban_situacion'
    ];

    public $ban_id        = null;
    public $ban_nombre    = '';
    public $ban_situacion = 1;

    public function __construct($args = [])
    {
        $this->ban_id        = $args['ban_id']        ?? null;
        $this->ban_nombre    = $args['ban_nombre']    ?? '';
        $this->ban_situacion = $args['ban_situacion'] ?? 1;
    }
}
