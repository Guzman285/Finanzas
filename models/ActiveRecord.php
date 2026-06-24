<?php
namespace Model;
use PDO;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    protected static $idTabla = '';

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD
    public static function setDB($database) {
        self::$db = $database;
    }

    // Exponer la conexion PDO
    public static function getDB() {
        return self::$db;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        $id = static::$idTabla ?? 'id';
        if(!is_null($this->$id)) {
            $resultado = $this->actualizar();
        } else {
            $resultado = $this->crear();
        }
        return $resultado;
    }

    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id = []) {
        $idQuery = static::$idTabla ?? 'id';
        $query = "SELECT * FROM " . static::$tabla;

        if(is_array(static::$idTabla)){
            foreach (static::$idTabla as $key => $value) {
                if($value == reset(static::$idTabla)){
                    $query .= " WHERE $value = " . self::$db->quote($id[$value]);
                } else {
                    $query .= " AND $value = " . self::$db->quote($id[$value]);
                }
            }
        } else {
            $query .= " WHERE $idQuery = $id";
        }
                
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Informix no soporta LIMIT — usar FETCH FIRST n ROWS ONLY
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " FETCH FIRST " . (int)$limite . " ROWS ONLY";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Busqueda Where con Columna 
    public static function where($columna, $valor, $condicion = '=') {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columna} ${condicion} '${valor}'";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // SQL para Consultas Avanzadas.
    public static function SQL($consulta) {
        $resultado = self::$db->query($consulta);
        return $resultado;
    }

    // Crea un nuevo registro
    public function crear() {
        $atributos = $this->sanitizarAtributos();

        $query  = "INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (";
        $query .= join(", ", array_values($atributos));
        $query .= ")";

        $resultado = self::$db->exec($query);

        return [
            'resultado' => $resultado,
            'id'        => self::$db->lastInsertId(static::$tabla)
        ];
    }

    public function actualizar() {
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}={$value}";
        }
        $id = static::$idTabla ?? 'id';

        $query  = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);

        if(is_array(static::$idTabla)){
            foreach (static::$idTabla as $key => $value) {
                if($value == reset(static::$idTabla)){
                    $query .= " WHERE $value = " . self::$db->quote($this->$value);
                } else {
                    $query .= " AND $value = " . self::$db->quote($this->$value);
                }
            }
        } else {
            $query .= " WHERE " . $id . " = " . self::$db->quote($this->$id);
        }

        $resultado = self::$db->exec($query);
        return ['resultado' => $resultado];
    }

    // Eliminar un registro
    public function eliminar() {
        $idQuery = static::$idTabla ?? 'id';
        $query = "DELETE FROM " . static::$tabla . " WHERE $idQuery = " . self::$db->quote($this->id);
        $resultado = self::$db->exec($query);
        return $resultado;
    }

    public static function consultarSQL($query) {
        $resultado = self::$db->query($query);

        $array = [];
        while($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $array[] = static::crearObjeto($registro);
        }

        $resultado->closeCursor();
        return $array;
    }

    // utf8_encode() deprecada en PHP 8.2 — reemplazada por mb_convert_encoding()
    private static function sanitizarFila(array $fila): array {
        $out = [];
        foreach ($fila as $k => $v) {
            $out[$k] = is_string($v)
                ? mb_convert_encoding($v, 'UTF-8', 'UTF-8')
                : $v;
        }
        return $out;
    }

    public static function fetchArray($query) {
        $resultado = self::$db->query($query);
        $respuesta = $resultado->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($respuesta as $value) {
            $data[] = array_change_key_case(self::sanitizarFila($value));
        }
        $resultado->closeCursor();
        return $data;
    }

    public static function fetchFirst($query) {
        $resultado = self::$db->query($query);
        $respuesta = $resultado->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($respuesta as $value) {
            $data[] = array_change_key_case(self::sanitizarFila($value));
        }
        $resultado->closeCursor();
        return array_shift($data);
    }

    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value) {
            $key = strtolower($key);
            if(property_exists($objeto, $key)) {
                $objeto->$key = is_string($value)
                    ? mb_convert_encoding($value, 'UTF-8', 'UTF-8')
                    : $value;
            }
        }

        return $objeto;
    }

    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            $columna = strtolower($columna);
            if($columna === 'id' || $columna === static::$idTabla) continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->quote($value);
        }
        return $sanitizado;
    }

    public function sincronizar($args = []) { 
        foreach($args as $key => $value) {
            if(property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}
