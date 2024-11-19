<?php

require_once __DIR__ . '/../entities/Genero.php';

class DaoGenero
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Devuelve el número total de géneros con ese nombre.
    public function checkGenero($nameGenero)
    {
        $stmt = $this->db->prepare("SELECT COUNT(Nombre) AS genre FROM genero WHERE Nombre = ?");
        $stmt->execute([$nameGenero]);
        $result = $stmt->fetch();
        return $result['genre'];
    }

    // Inserta un nuevo género.
    public function createGenero($nameGenre)
    {
        $stmt = $this->db->prepare("INSERT INTO genero (Nombre) VALUES (?)");
        return $stmt->execute([$nameGenre]);
    }

    // Seleccionar todos los datos de la tabla género.
    public function selecGenero()
    {
        $stmt = $this->db->query("SELECT * FROM genero");
        return $stmt->fetchAll();
    }

    // Selecciona el nombre del id pasado.
    public function selectGeneroId($idGenero)
    {
        $stmt = $this->db->prepare("SELECT Nombre FROM genero WHERE IdGenero = ?");
        $stmt->execute([$idGenero]);
        return $stmt->fetch();
    }

    // Devolver el Id del género con ese Nombre.
    public function selecGeneroId($generoName)
    {
        $stmt = $this->db->prepare("SELECT IdGenero FROM genero WHERE Nombre = ?");
        $stmt->execute([$generoName]);
        $result = $stmt->fetch();
        return $result['IdGenero'];
    }

    // Eliminar el género con ese id.
    public function deleteGenero($id)
    {
        $stmt = $this->db->prepare("DELETE FROM genero WHERE IdGenero = ?");
        return $stmt->execute([$id]);
    }

}
