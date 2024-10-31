<?php

require_once __DIR__ . '/../entities/Favorito.php';

class DaoFavorito
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Crear favorito.
    public function createFavorite($usuarioId, $historiaId)
    {
        $stmt = $this->db->prepare("INSERT INTO favorito (UsuarioId, HistoriaId) VALUES (?, ?)");
        return $stmt->execute([$usuarioId, $historiaId]);
    }

    // Devuelve true(1) si existe o false(0) si no.
    public function esFavorito($usuarioId, $historiaId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM favorito WHERE UsuarioId = ? AND HistoriaId = ?");
        $stmt->execute([$usuarioId, $historiaId]);
        return $stmt->fetchColumn() > 0;
    }

    // Seleccionar los ids de las historias con favorito de ese usuario.
    public function selectFavoriteUser($usuarioId) {
        $stmt = $this->db->prepare("SELECT HistoriaId FROM favorito WHERE UsuarioId = ?");
        $stmt->execute([$usuarioId]);
        return $stmt->fetch();
    }

    // Borrar favorito con ese id de usuario pasado.
    public function deleteFavoriteUser($usuarioId)
    {
        $stmt = $this->db->prepare("DELETE FROM favorito WHERE UsuarioId = ?");
        return $stmt->execute([$usuarioId]);
    }

    // Borrar favorito con esos ids pasados.
    public function deleteFavorite($usuarioId, $historiaId)
    {
        $stmt = $this->db->prepare("DELETE FROM favorito WHERE UsuarioId = ? AND HistoriaId = ?");
        return $stmt->execute([$usuarioId, $historiaId]);
    }
}
