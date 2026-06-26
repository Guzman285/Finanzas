<?php

namespace Model;

class GastoFijo extends ActiveRecord
{
    protected static $tabla    = 'gastos_fijos';
    protected static $idTabla  = 'gf_id';
    protected static $columnasDB = [
        'gf_id',
        'gf_descripcion',
        'gf_monto_estimado',
        'gf_dia_pago',
        'gf_categoria_id',
        'gf_situacion'
        // gf_cuenta_id excluido del CRUD — se asigna solo al momento de pagar
    ];

    public $gf_id             = null;
    public $gf_descripcion    = '';
    public $gf_monto_estimado = 0.00;
    public $gf_dia_pago       = 1;
    public $gf_cuenta_id      = null;
    public $gf_categoria_id   = null;
    public $gf_situacion      = 1;

    public function __construct($args = [])
    {
        $this->gf_id             = $args['gf_id']             ?? null;
        $this->gf_descripcion    = $args['gf_descripcion']    ?? '';
        $this->gf_monto_estimado = $args['gf_monto_estimado'] ?? 0.00;
        $this->gf_dia_pago       = $args['gf_dia_pago']       ?? 1;
        $this->gf_cuenta_id      = $args['gf_cuenta_id']      ?? null;
        $this->gf_categoria_id   = $args['gf_categoria_id']   ?? null;
        $this->gf_situacion      = $args['gf_situacion']      ?? 1;
    }
}
