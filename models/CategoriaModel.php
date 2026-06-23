<?php

class CategoriaModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function buscar(): array
    {
        $sql = "SELECT cat_id, cat_nombre, cat_tipo
                FROM categorias
                WHERE cat_situacion = 1
                ORDER BY cat_tipo, cat_nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar(array $data): bool
    {
        $sql  = "INSERT INTO categorias (cat_nombre, cat_tipo)
                 VALUES (:cat_nombre, :cat_tipo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cat_nombre' => $data['cat_nombre'],
            ':cat_tipo'   => $data['cat_tipo'],
        ]);
    }

    public function modificar(array $data): bool
    {
        $sql  = "UPDATE categorias
                 SET cat_nombre = :cat_nombre,
                     cat_tipo   = :cat_tipo
                 WHERE cat_id = :cat_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cat_nombre' => $data['cat_nombre'],
            ':cat_tipo'   => $data['cat_tipo'],
            ':cat_id'     => $data['cat_id'],
        ]);
    }

    public function eliminar(array $data): bool
    {
        $sql  = "UPDATE categorias
                 SET cat_situacion = 0
                 WHERE cat_id = :cat_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':cat_id' => $data['cat_id']]);
    }
}
