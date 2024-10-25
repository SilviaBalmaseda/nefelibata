<?php

require_once __DIR__ . '/../entities/Estado.php';

class DaoEstado
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Devuelve el número total según el nombre pasado.
    public function checkEstado($nameEstado)
    {
        $stmt = $this->db->prepare("SELECT COUNT(Nombre) AS statu FROM estado WHERE Nombre = ?");
        $stmt->execute([$nameEstado]);
        $result = $stmt->fetch();
        return $result['statu'];
    }

    // Seleccionar el último id.
    public function maxId() 
    {
        $stmt = $this->db->query("SELECT MAX(IdUsuario) AS last_id FROM estado");
        return $stmt->fetchColumn();
    }

    // Inserta un nuevo estado.
    public function createEstado($nameGenre)
    {
        $id = $this->maxId();
        $stmt = $this->db->prepare("INSERT INTO estado (IdEstado, Nombre) VALUES (?, ?)");
        return $stmt->execute([$id, $nameGenre]);
    }

    // Selecciona todos los datos de la tabla estado.
    public function selecEstado()
    {
        $stmt = $this->db->query("SELECT * FROM estado");
        return $stmt->fetchAll();
    }

    // Selecciona el nombre del id pasado.
    public function selectEstadoId($idEstado)
    {
        $stmt = $this->db->prepare("SELECT Nombre FROM estado WHERE IdEstado = ?");
        $stmt->execute([$idEstado]);
        $result = $stmt->fetch();
        return $result['Nombre'];
    }

    // Elimina el estado según el id pasado.
    public function deleteEstado($id)
    {
        $stmt = $this->db->prepare("DELETE FROM estado WHERE IdEstado = ?");
        return $stmt->execute([$id]);
    }
}
