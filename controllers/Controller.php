<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/DaoEstado.php';
require_once __DIR__ . '/../models/DaoGenero.php';
require_once __DIR__ . '/../models/DaoHistoria_estado.php';
require_once __DIR__ . '/../models/DaoHistoria_genero.php';
require_once __DIR__ . '/../models/DaoHistoria.php';
require_once __DIR__ . '/../models/DaoCapitulo.php';
require_once __DIR__ . '/../models/DaoFavorito.php';
require_once __DIR__ . '/../models/DaoUsuario.php';

class Controller
{
    private $daoEstado;
    private $daoGenero;
    private $daoHistoria_estado;
    private $daoHistoria_genero;
    private $daoHistoria;
    private $daoCapitulo;
    private $daoFavorito;
    private $daoUsuario;

    public function __construct($db)
    {
        $this->daoEstado = new DaoEstado($db);
        $this->daoGenero = new DaoGenero($db);
        $this->daoHistoria_estado = new DaoHistoria_estado($db);
        $this->daoHistoria_genero = new DaoHistoria_genero($db);
        $this->daoHistoria = new DaoHistoria($db);
        $this->daoCapitulo = new DaoCapitulo($db);
        $this->daoFavorito = new DaoFavorito($db);
        $this->daoUsuario = new DaoUsuario($db);
    }

    // Pasa los datos necesarios en la página(géneros, historias, favoritos y paginación).
    private function loadData($generoId = null)
    {
        $historiasPorPagina = 8; // Por defecto.
        $generos = $this->daoGenero->selecGenero() ?: [];

        // Calcular el número total de historias(si introduce género por género también).
        $totalHistorias = $this->daoHistoria->selecNumHistoria($generoId);

        // Calcular el número total de las páginas.
        $totalPaginas = ceil($totalHistorias / $historiasPorPagina);

        // Obtener página actual(por defecto 1).
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Validar página actual.
        if ($paginaActual < 1) {
            $paginaActual = 1;
        } elseif ($paginaActual > $totalPaginas) {
            $paginaActual = $totalPaginas;
        }

        // Calcular el índice de inicio y fin de las historias.
        $inicio = ($paginaActual - 1) * $historiasPorPagina;
        $fin = min($inicio + $historiasPorPagina, $totalHistorias);

        // Obtener historias según el género si lo selecciona o no.
        if ($generoId !== null) {
            $todasLasHistorias = $this->daoHistoria->selecStoryIdGenero($generoId);
        } else {
            $todasLasHistorias = $this->daoHistoria->selecStoryCard() ?: [];
        }

        // Historias actuales en la página.
        $storysCards = array_slice($todasLasHistorias, $inicio, $historiasPorPagina);

        // Obtener el Id del usuario(si no está null).
        $usuarioId = $_SESSION['usuario']['Id'] ?? null;

        // Para poner true o false si el usuario lo tiene como favorito o no.
        if ($usuarioId) {
            foreach ($storysCards as &$story) {
                // Devuelve si la historia es favorita para el usuario.
                $story['esFavorito'] = $this->daoFavorito->esFavorito($usuarioId, $story['Id']);
            }
        } else {
            // Si no hay usuario.
            foreach ($storysCards as &$story) {
                $story['esFavorito'] = false;
            }
        }

        // Retornar los datos necesarios en la página(géneros, historias y paginación).
        return [
            'generos' => $generos,
            'storysCards' => $storysCards,
            'totalPaginas' => $totalPaginas,
            'paginaActual' => $paginaActual
        ];
    }

    // Muestra(pasa datos para) la interfaz de index(página principal).
    public function index()
    {
        // Parámetros para paginación
        $generoName = isset($_GET['nombre']) ? $_GET['nombre'] : null;

        if ($generoName !== null) {
            $generoId = $this->daoGenero->selecGeneroId($generoName);
        } else {
            $generoId = null;
        }

        // Obtener géneros, historias y paginación
        $loadData = $this->loadData($generoId);

        // Pasa las variables a la vista.
        $generos = $loadData['generos'];
        $storysCards = $loadData['storysCards'];
        $totalPaginas = $loadData['totalPaginas'];
        $paginaActual = $loadData['paginaActual'];

        include 'views/index.php';
    }

    // Pasa los datos necesarios en la página(por el buscador).
    private function loadDataSearch($name = null, $todasLasHistorias = null)
    {
        $historiasPorPagina = 8; // Por defecto.
        $generos = $this->daoGenero->selecGenero() ?: [];

        // Calcular el número total de historias que ha buscado.
        $totalHistorias = $this->daoHistoria->selecNumSearchStory($name);

        // Calcular el número total de las páginas.
        $totalPaginas = ceil($totalHistorias / $historiasPorPagina);

        // Obtener página actual(por defecto 1).
        $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        // Validar página actual.
        if ($paginaActual < 1) {
            $paginaActual = 1;
        } elseif ($paginaActual > $totalPaginas) {
            $paginaActual = $totalPaginas;
        }

        // Calcular el índice de inicio y fin de las historias.
        $inicio = ($paginaActual - 1) * $historiasPorPagina;
        $fin = min($inicio + $historiasPorPagina, $totalHistorias);

        // Historias actuales en la página.
        $storysCards = array_slice($todasLasHistorias, $inicio, $historiasPorPagina);

        // Obtener el Id del usuario(si no está null).
        $usuarioId = $_SESSION['usuario']['Id'] ?? null;

        // Para poner true o false si el usuario lo tiene como favorito o no.
        if ($usuarioId) {
            foreach ($storysCards as &$story) {
                // Devuelve si la historia es favorita para el usuario.
                $story['esFavorito'] = $this->daoFavorito->esFavorito($usuarioId, $story['Id']);
            }
        } else {
            // Si no hay usuario.
            foreach ($storysCards as &$story) {
                $story['esFavorito'] = false;
            }
        }

        // Retornar los datos necesarios en la página(géneros, historias y paginación).
        return [
            'generos' => $generos,
            'storysCards' => $storysCards,
            'totalPaginas' => $totalPaginas,
            'paginaActual' => $paginaActual
        ];
    }

    // Para buscar las historias que introduzcan en el buscador.
    public function searchStory()
    {
        $errores = '';
        $storysCards = null;
        $searchStory = trim($_GET['searchStory'] ?? '');

        // Obtener géneros y paginación para la vista.
        $loadData = $this->loadData();

        // Validar si está vacío.
        if (!empty($searchStory)) {
            // Buscar las historias según el título o autor.
            $historias = $this->daoHistoria->selecHistoriaAutor($searchStory);
            if (empty($historias)) {
                $errores = "No se encontraron historias con ese título o autor. Elige otro.";
            } else {
                // Obtener géneros y paginación para la vista.
                $loadData = $this->loadDataSearch($searchStory, $historias);
                $storysCards = $loadData['storysCards'];
            }
        } else {
            $storysCards = $loadData['storysCards'];
        }

        if ($storysCards) {
            // Obtener el Id del usuario.
            $usuarioId = $_SESSION['usuario']['Id'] ?? null;

            // Para poner true o false si el usuario lo tiene como favorito o no.
            if ($usuarioId) {
                foreach ($storysCards as $story) {
                    // Devuelve si la historia es favorita para el usuario.
                    $story['esFavorito'] = $this->daoFavorito->esFavorito($usuarioId, $story['Id']);
                }
            } else {
                // Si no hay usuario.
                foreach ($storysCards as $story) {
                    $story['esFavorito'] = false;
                }
            }

            $totalPaginas = $loadData['totalPaginas'];
            $paginaActual = $loadData['paginaActual'];
        }

        $generos = $loadData['generos'];
        include 'views/index.php';
    }

    // INICIAR SESIÓN
    // Función para validar los datos del formulario de iniciarSesión.
    public function validarIniciarSesion($nameUser, $contra)
    {
        $errores = [];
        $clave = sha1($contra);

        if (empty($nameUser)) {
            $errores['nameUser'] = "El Nombre de Usuario es obligatorio.";
            // Si no está vació, verificar que ese usuario tiene esa contraseña.
        } else if ($this->daoUsuario->checkSession($nameUser, $clave) < 1) {
            // Verificar si existe ese usuario.
            if (!($this->daoUsuario->checkUser($nameUser) > 0)) {
                $errores['nameUserClave'] = "Usuario/Clave incorrectas. Vuelve a intentarlo.";
            }

            // Solo aplicamos el estilo de error a los campos.
            $errores['nameUser'] = $errores['nameUser'] ?? '';
            $errores['clave'] = $errores['clave'] ?? '';
        }

        if (empty($contra)) {
            $errores['clave'] = "La Contraseña es obligatorio.";
        }

        return $errores;
    }

    // Función para devolver los errores del formulario de registro.
    private function validarRegistrar($nameUser, $clave, $email)
    {
        $errores = [];

        // Validar datos.
        if (empty($nameUser)) {
            $errores['nameUser'] = "El Nombre de Usuario es obligatorio.";
        } elseif ($this->daoUsuario->checkUser($nameUser) > 0) {
            $errores['nameUser'] = "Ese Nombre de Usuario ya está en uso. Elige otro.";
        }

        if (empty($clave)) {
            $errores['clave'] = "La Contraseña es obligatorio.";
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = "El formato del email no es válido.";
        }

        return $errores;
    }

    // Comprobar el usuario y la contraseña, después añade una sesión para la página.
    public function iniciarSesion($nameUser = null, $contra = null)
    {
        // Los parámetros están para cuando lo llamo desde el registrar().
        $nameUser = $nameUser ?? $_POST['nameUser'] ?? null;
        $contra = $contra ?? $_POST['clave'] ?? null;

        // Array para errores.
        $errores = $this->validarIniciarSesion($nameUser, $contra);

        // Encriptamos la contraseña, para verificar después.
        $clave = sha1($contra);

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Obtener el ID del usuario.
        $usuarioId = $this->daoUsuario->selecUserId($nameUser);

        // Guardar nombre e ID del usuario.
        if ($usuarioId) {
            $_SESSION['usuario'] = [
                'nombre' => $nameUser,
                'Id' => $usuarioId
            ];
        } else {
            echo json_encode(['success' => false, 'errors' => ['Usuario no encontrado']]);
        }

        // Si no hay errores.
        echo json_encode(['success' => true, 'redirect' => 'index.php']);
    }

    // REGISTRAR
    // Comprueba que no está ese usuario y lo añade a la BBDD, luego llama a 'iniciarSesion()'.
    public function registrar()
    {
        $nameUser = $_POST['nameUser'] ?? '';
        $contra = $_POST['clave'] ?? '';
        $email = $_POST['email'] ?? '';

        // Array para errores.
        $errores = $this->validarRegistrar($nameUser, $contra, $email);

        // Encriptamos la contraseña.
        $clave = sha1($contra);

        // Si hay errores.
        if (!empty($errores)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        try {
            // Si no hay errores, proceder con el registro.
            $this->daoUsuario->createUser($nameUser, $clave, $email);

            // Llamar a iniciarSesion pasando los parámetros.
            $this->iniciarSesion($nameUser, $contra);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
        }
    }

    // OTRAS FUNCIONALIDADES
    // Leer Historia.
    public function leerHistoria()
    {
        // Seleccionamos el título y le quitamos los guiones.
        $title = $_GET['titulo'] ?? null;
        if ($title) {
            $title = str_replace("-", " ", $title);
        }

        // Seleccionamos el id de la historia con ese título.
        $idHistoria = $this->daoHistoria->selecStoryTitle($title) ?? null;

        // Si no hay ID de historia, redirigir a la página de inicio.
        if (!$idHistoria) {
            header("Location: index.php");
            exit();
        }

        // Seleccionar todos los datos de los capítulos con el id de la historia.
        $dataCap = $this->daoCapitulo->selecDataCapitulo($idHistoria);

        $capituloActual = $_GET['capitulo'] ?? null;
        $capitulosDisponibles = array_column($dataCap, 'NumCapitulo'); // Extraer los números de capítulos.

        // Si no hay capítulo o si en esa historia no existe ese NumCapitulo.
        if (!$capituloActual || !in_array($capituloActual, $capitulosDisponibles)) {
            // Actualizar la URL.
            $numCap = $dataCap[0]['NumCapitulo'];
            header("Location: index.php?action=leer&titulo=" . urlencode($_GET['titulo']) . "&capitulo=" . $numCap);
            exit();
        }

        // Seleccionar los datos de la historia.
        $story = $this->daoHistoria->selecStoryId($idHistoria);

        // Si no existe la historia, mostrar error.
        if (!$story) {
            echo "Historia no encontrada.";
            return;
        }

        // Seleccionar solo el capítulo(historia) actual de la historia.
        $cap = $this->daoCapitulo->selecCapIdNum($idHistoria, $capituloActual);
        $capitulo = ($cap) ? $cap : $dataCap[0]['Historia']; // Si no se encuentra la historia, por defecto la primera.

        // El número total de todos los capítulos que son de esa historia.
        $numCaps = count($dataCap);

        // Encontrar el título del capítulo actual.
        foreach ($dataCap as $cap) {
            if ($cap['NumCapitulo'] == $capituloActual) {
                $capituloActualTitulo = $cap['TituloCap'];
                break;
            }
        }

        // Si no se encuentra el capítulo actual, seleccionar el primer capítulo.
        if (empty($capituloActualTitulo) && $numCaps > 0) {
            $capituloActualTitulo = $dataCap[0]['TituloCap'];
        }

        // Verificar si hay capítulos anterior y siguiente(paginación).
        $hayAnterior = ($capituloActual > $dataCap[0]['NumCapitulo']); // Primer capítulo.
        $haySiguiente = ($capituloActual < end($dataCap)['NumCapitulo']); // Último capítulo

        include 'views/leer.php';
    }

    // Añadir o eliminar favorito.
    public function favorito()
    {
        header('Content-Type: application/json');

        $historiaId = $_POST['historiaId'] ?? null;
        $usuarioId = $_SESSION['usuario']['Id'];
        // Para que se seleccione el tipo booleano.
        $esFavorito = isset($_POST['esFavorito']) && $_POST['esFavorito'] === 'true' ? true : false;

        if ($historiaId && $usuarioId) {
            if ($esFavorito) {
                $this->daoFavorito->deleteFavorite($usuarioId, $historiaId);
                $this->daoHistoria->subtractFavorite($historiaId);
                echo json_encode(['success' => true]);
            } else {
                $this->daoFavorito->createFavorite($usuarioId, $historiaId);
                $this->daoHistoria->addFavorite($historiaId);
                echo json_encode(['success' => true]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El Id de la historia o el usuario no son válido']);
        }
    }

    // Devolver el estado y los géneros de esa historia.
    public function returnStatuGen()
    {
        header('Content-Type: application/json');

        $historiaId = $_POST['historiaId'] ?? null;

        if ($historiaId) {
            $idEstado = $this->daoHistoria_estado->selectStatesStory($historiaId) ?? null;
            $nameEstado = $this->daoEstado->selectEstadoId($idEstado) ?? null;
            $idGens = $this->daoHistoria_genero->selectGenreStoryColumn($historiaId) ?? [];

            $nameGen = [];
            foreach ($idGens as $id) {
                // Seleccionamos el nombre.
                $genero = $this->daoGenero->selectGeneroId(intval($id));

                if ($genero && isset($genero['Nombre'])) {
                    array_push($nameGen, $genero['Nombre']); // Añadimos el nombre.
                }
            }

            echo json_encode([
                'success' => true,
                'estado' => $nameEstado,
                'genero' => $nameGen
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontro la historia.']);
        }
    }

    // AJUSTES
    // Destruye la sesión.
    public function ajustes()
    {
        session_destroy();

        header('Location: index.php');
    }
}
