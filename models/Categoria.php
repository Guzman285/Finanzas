<?php

namespace Model;

class Categoria extends ActiveRecord
{
    protected static $tabla    = 'categorias';
    protected static $idTabla  = 'cat_id';
    protected static $columnasDB = [
        'cat_id',
        'cat_nombre',
        'cat_tipo',
        'cat_situacion'
    ];

    public $cat_id        = null;
    public $cat_nombre    = '';
    public $cat_tipo      = '';
    public $cat_situacion = 1;

    public function __construct($args = [])
    {
        $this->cat_id        = $args['cat_id']        ?? null;
        $this->cat_nombre    = $args['cat_nombre']    ?? '';
        $this->cat_tipo      = $args['cat_tipo']      ?? '';
        $this->cat_situacion = $args['cat_situacion'] ?? 1;
    }
}
