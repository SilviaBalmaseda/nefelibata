<?php

require_once __DIR__ . '/../entities/Historia_estado.php';

class DaoHistoria_estado
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Asignar el estado a la historia.
    public function asignarEstadoHistoria($historiaId, $estadoId)
    {
        $stmt = $this->db->prepare("INSERT INTO historia_estado (HistoriaId, EstadoId) VALUES (?, ?)");
        return $stmt->execute([$historiaId, $estadoId]);
    }

    // Desasignar el estado de la historia.
    public function desasignarAllEstadoHistoria($historiaId)
    {
        $stmt = $this->db->prepare("DELETE FROM historia_estado WHERE HistoriaId=?");
        return $stmt->execute([$historiaId]);
    }

    // Devolver si está asignado(1) el estado o no(0).
    public function checkHistoriaEstado($historiaId, $estadoId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM historia_estado WHERE HistoriaId = ? AND EstadoId = ?");
        $stmt->execute([$historiaId, $estadoId]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Seleccionar el id del estado de esa historia.
    public function selectStatesStory($historiaId)
    {
        $stmt = $this->db->prepare("SELECT EstadoId AS EstadoId FROM historia_estado WHERE HistoriaId = ?");
        $stmt->execute([$historiaId]);
        $result = $stmt->fetch();
        return $result['EstadoId'];
    }
}
