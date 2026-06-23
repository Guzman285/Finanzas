<?php

namespace Model;

class DeudaMovimiento extends ActiveRecord
{
    protected static $tabla   = 'deuda_movimientos';
    protected static $idTabla = 'dm_id';
    protected static $columnasDB = [
        'dm_id',
        'dm_deu_id',
        'dm_tipo',
        'dm_fecha',
        'dm_descripcion',
        'dm_monto_total',
        'dm_abono_capital',
        'dm_interes',
        'dm_cuenta_id',
        'dm_mov_id',
        'dm_situacion'
    ];

    public $dm_id            = null;
    public $dm_deu_id        = null;
    public $dm_tipo          = 'pago';
    public $dm_fecha         = null;
    public $dm_descripcion   = '';
    public $dm_monto_total   = 0.00;
    public $dm_abono_capital = 0.00;
    public $dm_interes       = 0.00;
    public $dm_cuenta_id     = null;
    public $dm_mov_id        = null;
    public $dm_situacion     = 1;

    public function __construct($args = [])
    {
        $this->dm_id            = $args['dm_id']            ?? null;
        $this->dm_deu_id        = $args['dm_deu_id']        ?? null;
        $this->dm_tipo          = $args['dm_tipo']          ?? 'pago';
        $this->dm_fecha         = $args['dm_fecha']         ?? null;
        $this->dm_descripcion   = $args['dm_descripcion']   ?? '';
        $this->dm_monto_total   = $args['dm_monto_total']   ?? 0.00;
        $this->dm_abono_capital = $args['dm_abono_capital'] ?? 0.00;
        $this->dm_interes       = $args['dm_interes']       ?? 0.00;
        $this->dm_cuenta_id     = $args['dm_cuenta_id']     ?? null;
        $this->dm_mov_id        = $args['dm_mov_id']        ?? null;
        $this->dm_situacion     = $args['dm_situacion']     ?? 1;
    }
}
