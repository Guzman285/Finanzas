<?php

namespace Model;

class Deuda extends ActiveRecord
{
    protected static $tabla   = 'deudas';
    protected static $idTabla = 'deu_id';
    protected static $columnasDB = [
        'deu_id',
        'deu_descripcion',
        'deu_entidad',
        'deu_tipo',
        'deu_monto_total',
        'deu_monto_pagado',
        'deu_cuota_mensual',
        'deu_limite_credito',
        'deu_tasa_interes',
        'deu_dia_corte',
        'deu_dia_pago',
        'deu_fecha_inicio',
        'deu_fecha_fin_est',
        'deu_cuenta_id',
        'deu_descuento_nomina',
        'deu_situacion'
    ];

    public $deu_id               = null;
    public $deu_descripcion      = '';
    public $deu_entidad          = null;
    public $deu_tipo             = 'fija';
    public $deu_monto_total      = 0.00;
    public $deu_monto_pagado     = 0.00;
    public $deu_cuota_mensual    = 0.00;
    public $deu_limite_credito   = null;
    public $deu_tasa_interes     = 0.0000;
    public $deu_dia_corte        = null;
    public $deu_dia_pago         = null;
    public $deu_fecha_inicio     = null;
    public $deu_fecha_fin_est    = null;
    public $deu_cuenta_id        = null;
    public $deu_descuento_nomina = 0;
    public $deu_situacion        = 1;

    public function __construct($args = [])
    {
        $this->deu_id               = $args['deu_id']               ?? null;
        $this->deu_descripcion      = $args['deu_descripcion']      ?? '';
        $this->deu_entidad          = $args['deu_entidad']          ?? null;
        $this->deu_tipo             = $args['deu_tipo']             ?? 'fija';
        $this->deu_monto_total      = $args['deu_monto_total']      ?? 0.00;
        $this->deu_monto_pagado     = $args['deu_monto_pagado']     ?? 0.00;
        $this->deu_cuota_mensual    = $args['deu_cuota_mensual']    ?? 0.00;
        $this->deu_limite_credito   = $args['deu_limite_credito']   ?? null;
        $this->deu_tasa_interes     = $args['deu_tasa_interes']     ?? 0.0000;
        $this->deu_dia_corte        = $args['deu_dia_corte']        ?? null;
        $this->deu_dia_pago         = $args['deu_dia_pago']         ?? null;
        $this->deu_fecha_inicio     = $args['deu_fecha_inicio']     ?? null;
        $this->deu_fecha_fin_est    = $args['deu_fecha_fin_est']    ?? null;
        $this->deu_cuenta_id        = $args['deu_cuenta_id']        ?? null;
        $this->deu_descuento_nomina = $args['deu_descuento_nomina'] ?? 0;
        $this->deu_situacion        = $args['deu_situacion']        ?? 1;
    }
}
