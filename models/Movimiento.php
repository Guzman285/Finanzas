<?php

namespace Model;

class Movimiento extends ActiveRecord
{
    protected static $tabla      = 'movimientos';
    protected static $idTabla    = 'mov_id';
    protected static $columnasDB = [
        'mov_id',
        'mov_tipo',
        'mov_descripcion',
        'mov_monto',
        'mov_fecha',
        'mov_cuenta_origen_id',
        'mov_cuenta_destino_id',
        'mov_categoria_id',
        'mov_gasto_fijo_id',
        'mov_deuda_id',
        'mov_situacion'
    ];

    public $mov_id                = null;
    public $mov_tipo              = '';
    public $mov_descripcion       = '';
    public $mov_monto             = 0.00;
    public $mov_fecha             = '';
    public $mov_cuenta_origen_id  = null;
    public $mov_cuenta_destino_id = null;
    public $mov_categoria_id      = null;
    public $mov_gasto_fijo_id     = null;
    public $mov_deuda_id          = null;
    public $mov_situacion         = 1;

    public function __construct($args = [])
    {
        $this->mov_id                = $args['mov_id']                ?? null;
        $this->mov_tipo              = $args['mov_tipo']              ?? '';
        $this->mov_descripcion       = $args['mov_descripcion']       ?? '';
        $this->mov_monto             = $args['mov_monto']             ?? 0.00;
        $this->mov_fecha             = $args['mov_fecha']             ?? '';
        $this->mov_cuenta_origen_id  = $args['mov_cuenta_origen_id']  ?? null;
        $this->mov_cuenta_destino_id = $args['mov_cuenta_destino_id'] ?? null;
        $this->mov_categoria_id      = $args['mov_categoria_id']      ?? null;
        $this->mov_gasto_fijo_id     = $args['mov_gasto_fijo_id']     ?? null;
        $this->mov_deuda_id          = $args['mov_deuda_id']          ?? null;
        $this->mov_situacion         = $args['mov_situacion']         ?? 1;
    }
}
