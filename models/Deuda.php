<?php

namespace Model;

class Deuda extends ActiveRecord
{
    protected static $tabla   = 'deudas';
    protected static $idTabla = 'deu_id';
    protected static $columnasDB = [
        'deu_id',
        'deu_descripcion',
        'deu_monto_total',
        'deu_monto_pagado',
        'deu_cuota_mensual',
        'deu_fecha_inicio',
        'deu_fecha_fin_est',
        'deu_cuenta_id',
        'deu_situacion'
    ];

    public $deu_id            = null;
    public $deu_descripcion   = '';
    public $deu_monto_total   = 0.00;
    public $deu_monto_pagado  = 0.00;
    public $deu_cuota_mensual = 0.00;
    public $deu_fecha_inicio  = null;
    public $deu_fecha_fin_est = null;
    public $deu_cuenta_id     = null;
    public $deu_situacion     = 1;

    public function __construct($args = [])
    {
        $this->deu_id            = $args['deu_id']            ?? null;
        $this->deu_descripcion   = $args['deu_descripcion']   ?? '';
        $this->deu_monto_total   = $args['deu_monto_total']   ?? 0.00;
        $this->deu_monto_pagado  = $args['deu_monto_pagado']  ?? 0.00;
        $this->deu_cuota_mensual = $args['deu_cuota_mensual'] ?? 0.00;
        $this->deu_fecha_inicio  = $args['deu_fecha_inicio']  ?? null;
        $this->deu_fecha_fin_est = $args['deu_fecha_fin_est'] ?? null;
        $this->deu_cuenta_id     = $args['deu_cuenta_id']     ?? null;
        $this->deu_situacion     = $args['deu_situacion']     ?? 1;
    }
}
