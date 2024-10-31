<?php

require_once __DIR__ . '/../entities/Usuario.php';

class DaoUsuario
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Insertar un nuevo usuario.
    public function createUser($nameUser, $clave, $email)
    {
        $stmt = $this->db->prepare("INSERT INTO usuario (Nombre, Clave, Email) VALUES (?, ?, ?)");
        return $stmt->execute([$nameUser, $clave, $email]);
    }

    // Devolver el número total que tengan el nombre y la clave igual a los pasados.
    public function checkSession($nameUser, $clave)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS userPass FROM usuario WHERE Nombre = ? AND Clave = ?");
        $stmt->execute([$nameUser, $clave]);
        $result = $stmt->fetch();
        return $result['userPass'];
    }

    // Devolver el número total con el mismo nombre pasado.
    public function checkUser($nameUser)
    {
        $stmt = $this->db->prepare("SELECT COUNT(Nombre) AS user FROM usuario WHERE Nombre = ?");
        $stmt->execute([$nameUser]);
        $result = $stmt->fetch();
        return $result['user'];
    }

    // Seleccionar todos los nombres de usuarios(menos admin) que contengan el nombre pasado.
    public function selecUsuario($nombre)
    {
        $stmt = $this->db->prepare("SELECT IdUsuario AS Id, Nombre FROM usuario WHERE Nombre LIKE ? AND IdUsuario != 1");
        $stmt->execute(["%$nombre%"]);
        return $stmt->fetchAll();
    }

    // Devuelve el Id del usuario o null si no lo encuentra.
    public function selecUserId($autor)
    {
        $stmt = $this->db->prepare("SELECT IdUsuario FROM usuario WHERE Nombre = ?");
        $stmt->execute([$autor]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : null;
    }

    // Elimina el usuario con ese Id.
    public function deleteUser($idUser)
    {
        $stmt = $this->db->prepare("DELETE FROM usuario WHERE IdUsuario = ?");
        return $stmt->execute([$idUser]);
    }
}
