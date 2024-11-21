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

    // Devuelve el número total de estados que hay.
    public function checkNumEstado()
    {
        $stmt = $this->db->query("SELECT COUNT(Nombre) AS num FROM estado");
        $result = $stmt->fetch();
        return $result['num'];
    }

    // Inserta un nuevo estado.
    public function createEstado($nameGenre)
    {
        $stmt = $this->db->prepare("INSERT INTO estado (Nombre) VALUES (?)");
        return $stmt->execute([$nameGenre]);
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
