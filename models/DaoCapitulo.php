<?php

require_once __DIR__ . '/../entities/Capitulo.php';

class DaoCapitulo
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Devuelve el número total de capítulos que tiene el id de la historia pasada.
    public function selecNumCaps($historiaId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(NumCapitulo) AS cap FROM capitulo WHERE HistoriaId = ?");
        $stmt->execute([$historiaId]);
        $result = $stmt->fetch();
        return $result['cap'];
    }

    // Devuelve el NumCapitulo que tiene el id del capítulo pasado.
    public function selecNumCapitulo($idCapitulo)
    {
        $stmt = $this->db->prepare("SELECT NumCapitulo FROM capitulo WHERE IdCapitulo = ?");
        $stmt->execute([$idCapitulo]);
        $result = $stmt->fetch();
        return $result['NumCapitulo'];
    }

    // Seleccionar el último id.
    public function maxId() 
    {
        $stmt = $this->db->query("SELECT MAX(IdCapitulo) AS last_id FROM capitulo");
        return $stmt->fetchColumn();
    }

    // Inserta un nuevo capítulo.
    public function createCapitulo($historiaId, $numCapitulo, $tituloCap, $historia)
    {
        $id = ($this->maxId()) ? $this->maxId() + 1 : 1;
        $stmt = $this->db->prepare("INSERT INTO capitulo (IdCapitulo, HistoriaId, NumCapitulo, TituloCap, Historia) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$id, $historiaId, $numCapitulo, $tituloCap, $historia]);
    }

    // Devuelve los datos de los capítulos con el id de la historia pasado.
    public function selecDataCapitulo($historiaId)
    {
        $stmt = $this->db->prepare("SELECT IdCapitulo, NumCapitulo, TituloCap, Historia FROM capitulo WHERE HistoriaId = ?");
        $stmt->execute([$historiaId]);
        return $stmt->fetchAll();
    }

    // Seleccionar la historia con el id de la historia y el número del capítulo.
    public function selecCapIdNum($idHistoria, $numCap)
    {
        $stmt = $this->db->prepare("SELECT Historia as story FROM capitulo WHERE HistoriaId = ? AND NumCapitulo = ?");
        $stmt->execute([$idHistoria, $numCap]);
        $result = $stmt->fetch();
        return ($result) ? $result['story'] : false; // Si no se encuentra el capítulo devolver false.
    }
    
    // Seleccionar los datos del capítulo del id pasado.
    function selecDataCap($idCapitulo)
    {
        $stmt = $this->db->prepare("SELECT TituloCap, Historia FROM capitulo WHERE IdCapitulo = ?");
        $stmt->execute([$idCapitulo]);
        return $stmt->fetch();
    }

    // Actualizar capítulo.
    public function updateCap($idCapitulo, $titleCap, $historia)
    {
        $stmt = $this->db->prepare("UPDATE capitulo SET TituloCap = ?, Historia = ? WHERE IdCapitulo = ?");
        return $stmt->execute([$titleCap, $historia, $idCapitulo]);
    }

    // Borrar capítulo con ese id pasado.
    public function deleteCap($idCapitulo)
    {
        $stmt = $this->db->prepare("DELETE FROM capitulo WHERE IdCapitulo = ?");
        return $stmt->execute([$idCapitulo]);
    }
}
