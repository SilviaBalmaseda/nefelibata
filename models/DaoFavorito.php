<?php

require_once __DIR__ . '/../entities/Favorito.php';

class DaoFavorito
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Inserta favorito.
    public function createFavorite($usuarioId, $historiaId)
    {
        $stmt = $this->db->prepare("INSERT INTO favorito (UsuarioId, HistoriaId) VALUES (?, ?)");
        return $stmt->execute([$usuarioId, $historiaId]);
    }

    // Devuelve true(mayor que 0) si existe o false(0) si no.
    public function esFavorito($usuarioId, $historiaId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM favorito WHERE UsuarioId = ? AND HistoriaId = ?");
        $stmt->execute([$usuarioId, $historiaId]);
        return $stmt->fetchColumn() > 0;
    }

    // Seleccionar los ids de las historias con favorito de ese usuario.
    public function selectFavoriteUser($usuarioId)
    {
        $stmt = $this->db->prepare("SELECT HistoriaId FROM favorito WHERE UsuarioId = ?");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // FETCH_COLUMN para obtener un array.
    }

    // Borrar favorito con ese id de usuario pasado.
    public function deleteFavoriteUser($usuarioId)
    {
        $stmt = $this->db->prepare("DELETE FROM favorito WHERE UsuarioId = ?");
        return $stmt->execute([$usuarioId]);
    }

    // Borrar favorito con ese id de historia pasado.
    public function deleteFavoriteStory($storyId)
    {
        $stmt = $this->db->prepare("DELETE FROM favorito WHERE HistoriaId = ?");
        return $stmt->execute([$storyId]);
    }

    // Borrar favorito con ese id pasado.
    public function deleteFavorite($usuarioId, $historiaId)
    {
        $stmt = $this->db->prepare("DELETE FROM favorito WHERE UsuarioId = ? AND HistoriaId = ?");
        return $stmt->execute([$usuarioId, $historiaId]);
    }
}
