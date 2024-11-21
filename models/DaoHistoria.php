<?php

require_once __DIR__ . '/../entities/Historia.php';

class DaoHistoria
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Devolver el número total de historias que tienen ese título.
    public function checkStory($title)
    {
        $stmt = $this->db->prepare("SELECT COUNT(Titulo) AS title FROM historia WHERE Titulo = ?");
        $stmt->execute([$title]);
        $result = $stmt->fetch();
        return $result['title'];
    }
    
    // Devuelve el número total de historias que hay.
    public function checkNumStory()
    {
        $stmt = $this->db->query("SELECT COUNT(Titulo) AS num FROM historia");
        $result = $stmt->fetch();
        return $result['num'];
    }

    // Devolver si ese título pertenece a la misma historia que el id(true=1/false=0).
    public function checkStoryId($title, $idHistoria)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM historia WHERE Titulo = ? AND IdHistoria = ?");
        $stmt->execute([$title, $idHistoria]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Inserta Historia.
    public function createStory($titulo, $autor, $sinopsis, $imagen)
    {
        $stmt = $this->db->prepare("INSERT INTO historia (Titulo, UsuarioId, Sinopsis, Imagen) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $autor, $sinopsis, $imagen]);
        return $this->db->lastInsertId(); // Devuelve el ID de la historia creada.
    }

    // Selecciona algunos datos de todas las historias.
    public function selecHistoria()
    {
        $stmt = $this->db->query("SELECT IdHistoria, Titulo, UsuarioId FROM historia");
        return $stmt->fetchAll();
    }

    // Selecciona el id de la historia con ese título.
    public function selecStoryTitle($title)
    {
        $stmt = $this->db->prepare("SELECT IdHistoria AS id FROM historia WHERE Titulo = ?");
        $stmt->execute([$title]);
        $result = $stmt->fetch();
        return $result['id'];
    }

    // Selecciona algunos datos de la historia con ese usuario.
    public function selecAutorStory($autor)
    {
        $stmt = $this->db->prepare("SELECT h.IdHistoria, h.Titulo, h.Sinopsis, h.Imagen
                                    FROM historia h
                                    JOIN usuario u ON h.UsuarioId = u.IdUsuario
                                    WHERE u.Nombre = ?");
        $stmt->execute([$autor]);
        return $stmt->fetchAll();
    }

    // Seleccionar todos los datos de todas las historias y el autor.
    public function selecStoryCard()
    {
        $stmt = $this->db->query("SELECT h.IdHistoria AS Id, h.Titulo, h.Sinopsis, h.Imagen, h.NumFavorito, u.Nombre 
                                    FROM historia h
                                    JOIN usuario u ON h.UsuarioId = u.IdUsuario");
        return $stmt->fetchAll();
    }

    // Selecciona algunos datos de la historia con ese Id.
    public function selecStoryId($idHistoria)
    {
        $stmt = $this->db->prepare("SELECT Titulo, Sinopsis, Imagen FROM historia WHERE IdHistoria = ?");
        $stmt->execute([$idHistoria]);
        return $stmt->fetch();
    }

    // Selecciona datos de la historia con un título o autor parecido al pasado.
    public function selecHistoriaAutor($nombre)
    {
        $stmt = $this->db->prepare("SELECT h.IdHistoria AS Id, h.Titulo, h.Sinopsis, h.Imagen, h.NumFavorito, u.Nombre
                                    FROM historia h
                                    JOIN usuario u ON h.UsuarioId = u.IdUsuario
                                    WHERE h.Titulo LIKE ? OR u.Nombre LIKE ?");
        $stmt->execute(["%$nombre%", "%$nombre%"]);
        return $stmt->fetchAll();
    }

    // Devuelve algunos datos de la historia y el usuario con el Id de género pasado.
    public function selecStoryIdGenero($idGenero)
    {
        $stmt = $this->db->prepare("SELECT h.IdHistoria AS Id, h.Titulo, h.Sinopsis, h.Imagen, h.NumFavorito, u.Nombre 
                                    FROM historia h
                                    JOIN historia_genero g ON h.IdHistoria = g.HistoriaId
                                    JOIN usuario u ON h.UsuarioId = u.IdUsuario
                                    WHERE g.GeneroId = ?");
        $stmt->execute([$idGenero]);
        return $stmt->fetchAll();
    }

    // Devuelve el número de historias que hay, puede ser en función del género.
    public function selecNumHistoria($generoId)
    {
        $consulta = "SELECT COUNT(DISTINCT h.IdHistoria) AS num FROM historia h ";

        if ($generoId !== null) {
            $consulta .= " JOIN historia_genero g ON h.IdHistoria = g.HistoriaId WHERE g.GeneroId = ?";
        }

        $stmt = $this->db->prepare($consulta);
        if ($generoId !== null) {
            $stmt->execute([$generoId]);
        } else {
            $stmt->execute();
        }
        $result = $stmt->fetch();
        return $result['num'];
    }

    // Devuelve el número de historias con ese nombre o autor.
    public function selecNumSearchStory($name)
    {
        $stmt = $this->db->prepare("SELECT COUNT(DISTINCT h.IdHistoria) AS num 
                                    FROM historia h 
                                    JOIN usuario u ON h.UsuarioId = u.IdUsuario
                                    WHERE h.Titulo LIKE ? OR u.Nombre LIKE ?");
        $stmt->execute(["%$name%", "%$name%"]);
        $result = $stmt->fetch();
        return $result['num'];
    }

    // Añadir 1 a favorito.
    public function addFavorite($historiaId)
    {
        $stmt = $this->db->prepare("UPDATE historia SET NumFavorito = NumFavorito + 1 WHERE IdHistoria = ?");
        return $stmt->execute([$historiaId]);
    }

    // Restar 1 a favorito.
    public function subtractFavorite($historiaId)
    {
        $stmt = $this->db->prepare("UPDATE historia SET NumFavorito = (NumFavorito - 1) WHERE IdHistoria = ?");
        return $stmt->execute([$historiaId]);
    }

    // Actualizar la Historia completa con ese Id.
    public function updateStory($title, $sinopsis, $imagen, $idHistoria)
    {
        $stmt = $this->db->prepare("UPDATE historia SET Titulo = ?, Sinopsis = ?, Imagen = ? WHERE IdHistoria = ?");
        return $stmt->execute([$title, $sinopsis, $imagen, $idHistoria]);
    }

    // Actualizar la Historia sin la imagen con ese Id.
    public function updateStoryNoImage($title, $sinopsis, $idHistoria)
    {
        $stmt = $this->db->prepare("UPDATE historia SET Titulo = ?, Sinopsis = ? WHERE IdHistoria = ?");
        return $stmt->execute([$title, $sinopsis, $idHistoria]);
    }

    // Seleccionar los id de las historias con ese usuario.
    public function selectIdStotyUser($usuarioId)
    {
        $stmt = $this->db->prepare("SELECT IdHistoria FROM historia WHERE UsuarioId = ?");
        $stmt->execute([$usuarioId]);
        return $stmt->fetch();
    }

    // Eliminar Historia con ese Id.
    public function deleteHistoria($id)
    {
        $stmt = $this->db->prepare("DELETE FROM historia WHERE IdHistoria = ?");
        return $stmt->execute([$id]);
    }
}
