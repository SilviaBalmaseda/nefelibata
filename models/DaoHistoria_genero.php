<?php

require_once __DIR__ . '/../entities/Historia_genero.php';

class DaoHistoria_genero
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Asignar esa historia a ese género.
    public function asignarGeneroHistoria($historiaId, $generoId)
    {
        $stmt = $this->db->prepare("INSERT INTO historia_genero (HistoriaId, GeneroId) VALUES (?, ?)");
        return $stmt->execute([$historiaId, $generoId]);
    }

    // Desasignar ese género de esa historia.
    public function desasignarGeneroHistoria($historiaId, $generoId)
    {
        $stmt = $this->db->prepare("DELETE FROM historia_genero WHERE HistoriaId=? AND GeneroId=?");
        return $stmt->execute([$historiaId, $generoId]);
    }

    // Desasignar todos los géneros de esa historia.
    public function desasignarAllGeneroHistoria($historiaId)
    {
        $stmt = $this->db->prepare("DELETE FROM historia_genero WHERE HistoriaId=?");
        return $stmt->execute([$historiaId]);
    }

    // Devolver si está asignado(1) el género o no(0).
    public function checkHistoriaGenero($historiaId, $generoId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM historia_genero WHERE HistoriaId = ? AND GeneroId = ?");
        $stmt->execute([$historiaId, $generoId]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Seleccionar los géneros de esa historia.
    public function selectGenreStory($historiaId)
    {
        $stmt = $this->db->prepare("SELECT GeneroId FROM historia_genero WHERE HistoriaId = ?");
        $stmt->execute([$historiaId]);
        return $stmt->fetchAll();
    }

    // Seleccionar los ids de los géneros de esa historia.
    public function selectGenreStoryColumn($historiaId)
    {
        $stmt = $this->db->prepare("SELECT GeneroId FROM historia_genero WHERE HistoriaId = ?");
        $stmt->execute([$historiaId]);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Devuelve solo la columna 'GeneroId'
        return $result;
    }
}
