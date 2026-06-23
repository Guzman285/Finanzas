<?php

namespace Model;

class Cuenta extends ActiveRecord
{

    protected static $tabla = 'cuentas';
    protected static $idTabla = 'cta_id';
    protected static $columnasDB = [
        'cta_id',
        'cta_nombre',
        'cta_tipo',
        'cta_saldo',
        'cta_banco_id',
        'cta_numero',
        'cta_situacion'
    ];

    public $cta_id        = null;
    public $cta_nombre    = '';
    public $cta_tipo      = 'monetaria';
    public $cta_saldo     = 0.00;
    public $cta_banco_id  = null;
    public $cta_numero    = null;
    public $cta_situacion = 1;

    public function __construct($args = [])
    {
        $this->cta_id        = $args['cta_id']        ?? null;
        $this->cta_nombre    = $args['cta_nombre']    ?? '';
        $this->cta_tipo      = $args['cta_tipo']      ?? 'monetaria';
        $this->cta_saldo     = $args['cta_saldo']     ?? 0.00;
        $this->cta_banco_id  = $args['cta_banco_id']  ?? null;
        $this->cta_numero    = $args['cta_numero']    ?? null;
        $this->cta_situacion = $args['cta_situacion'] ?? 1;
    }
}
