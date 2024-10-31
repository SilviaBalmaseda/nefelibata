<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/DaoGenero.php';
require_once __DIR__ . '/../models/DaoEstado.php';
require_once __DIR__ . '/../models/DaoHistoria_estado.php';
require_once __DIR__ . '/../models/DaoHistoria_genero.php';
require_once __DIR__ . '/../models/DaoHistoria.php';
require_once __DIR__ . '/../models/DaoCapitulo.php';

class ControllerStory
{
    private $daoGenero;
    private $daoEstado;
    private $daoHistoria_estado;
    private $daoHistoria_genero;
    private $daoHistoria;
    private $daoCapitulo;
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->daoGenero = new DaoGenero($db);
        $this->daoEstado = new DaoEstado($db);
        $this->daoHistoria_estado = new DaoHistoria_estado($db);
        $this->daoHistoria_genero = new DaoHistoria_genero($db);
        $this->daoHistoria = new DaoHistoria($db);
        $this->daoCapitulo = new DaoCapitulo($db);
    }

    // Devuelve un array con los datos necesarios para CrearHistoria.
    private function loadCreateStoryData()
    {
        // Array con los nombres de los generos y estados que hay.
        $generos = $this->daoGenero->selecGenero() ?: [];
        $estados = $this->daoEstado->selecEstado() ?: [];

        // Array con algunos datos de las historias del usuario que está en la sesión.
        $autorStory = $this->daoHistoria->selecAutorStory($_SESSION['usuario']['nombre']) ?: [];

        return [
            'generos' => $generos,
            'estados' => $estados,
            'autorStory' => $autorStory
        ];
    }

    // Muestra(pasa datos para) la interfaz de crearHistoria.
    public function showCrearHistoria()
    {
        $data = $this->loadCreateStoryData();
        include 'views/crearHistoria.php';
    }

    // Validar datos para la creación de la historia(completa).
    public function validateCreate()
    {
        header('Content-Type: application/json');

        $errores = [];

        // Validar datos.
        if (isset($_POST['titulo']) && !empty($_POST['titulo'])) {
            $title = $_POST['titulo'];

            // Comprobar si el título ya está.
            if ($this->daoHistoria->checkStory($title) > 0) {
                $errores['titulo'] = "El Título ya está. Prueba otro.";
            }
        } else {
            $errores['titulo'] = "El Título de la historia es obligatorio.";
        }

        if (!(isset($_POST['historia'])) || empty($_POST['historia'])) {
            $errores['historia'] = "Tienes que escribir una Historia obligatoriamente.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Validar el título de la historia.
    public function validateEditStory()
    {
        header('Content-Type: application/json');

        $errores = [];

        // Validar datos.
        if (isset($_POST['titulo']) && !empty($_POST['titulo'])) {
            $title = $_POST['titulo'];

            // Comprobar si el título ya está.
            if ($this->daoHistoria->checkStory($title) > 0) {
                $idHistoria = trim($_POST['idHistoria']);
                // Comprobar que sea distinto al que se está editando.
                if ($this->daoHistoria->checkStoryId($title, $idHistoria) === 0) {
                    $errores['titulo'] = "El Título ya está. Prueba otro.";
                }
            }
        } else {
            $errores['titulo'] = "El Título de la historia es obligatorio.";
        }

        // Si no hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Crear Historia(y primer capítulo).
    public function createStory()
    {
        header('Content-Type: application/json');

        try {
            $this->pdo->beginTransaction();

            $autorId = $_SESSION['usuario']['Id'];
            $titulo = trim($_POST['titulo']);
            // Comprueba si existe la imgagen y verifica que no hubo errores en la subida del archivo.
            if (isset($_FILES['portada']) && $_FILES['portada']['error'] === UPLOAD_ERR_OK) {
                $archivoImag = $_FILES['portada']['tmp_name'];
                $conte = file_get_contents($archivoImag);
                $portada = base64_encode($conte);
            } else {
                $portada = null;
            }
            $tituloCap = (isset($_POST['tituloCap']) && !empty($_POST['tituloCap'])) ? trim($_POST['tituloCap']) : "1";
            $sinopsis = (isset($_POST['sinopsis']) && !empty($_POST['sinopsis'])) ? trim($_POST['sinopsis']) : null;
            $historia = trim($_POST['historia']);
            $genero = isset($_POST['genero']) ? $_POST['genero'] : [1]; // Género por defecto.
            $estado = isset($_POST['estado']) ? $_POST['estado'] : [1]; // Estado por defecto.

            $historiaId = $this->daoHistoria->createStory($titulo, $autorId, $sinopsis, $portada);
            // $numCapitulo = $daoCapitulo->selecNumCaps($historiaId) + 1;
            $numCapitulo = 1;
            $capituloId = $this->daoCapitulo->createCapitulo($historiaId, $numCapitulo, $tituloCap, $historia);

            // Se asignan los generos a la historia.
            foreach ($genero as $gen) {
                $this->daoHistoria_genero->asignarGeneroHistoria($historiaId, $gen);
            }

            // Si ha seleccionado varios generos y el 'Ninguno', se elimina el 'Ninguno'.
            if (count($genero) > 1) {
                // Si hay más géneros y han seleccionado 'Ninguno', se elimina.
                if (in_array(1, $genero)) { 
                    $this->daoHistoria_genero->desasignarGeneroHistoria($historiaId, 1);
                }
            }

            // Se asigna los estados a la historia.
            $this->daoHistoria_estado->asignarEstadoHistoria($historiaId, $estado);

            $this->pdo->commit();

            // Solo enviar la respuesta JSON al final, sin ninguna salida previa.
            echo json_encode(['success' => true, 'redirect' => 'index.php?action=crearHistoria&operation=createStory', 'message' => 'La Historia ha sido creado con éxito.']);
            exit;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // Editar capítulo seleccionado.
    public function editStory()
    {
        header('Content-Type: application/json');

        try {
            $this->pdo->beginTransaction();

            $idHistoria = trim($_POST['idHistoria']);

            $titulo = trim($_POST['titulo']);
            // Comprueba si existe la imgagen y verifica que no hubo errores en la subida del archivo.
            if (isset($_FILES['portada']) && $_FILES['portada']['error'] === UPLOAD_ERR_OK) {
                $archivoImag = $_FILES['portada']['tmp_name'];
                $conte = file_get_contents($archivoImag);
                $portada = base64_encode($conte);
            } else {
                $portada = null;
            }
            $sinopsis = (isset($_POST['sinopsis']) && !empty($_POST['sinopsis'])) ? trim($_POST['sinopsis']) : null;
            $genero = isset($_POST['genero']) ? $_POST['genero'] : [1]; // Género por defecto.
            $estado = isset($_POST['estado']) ? $_POST['estado'] : [1]; // Estado por defecto.

            // Actualizar datos de la historia.
            if ($portada !== null) {
                $this->daoHistoria->updateStory($titulo, $sinopsis, $portada, $idHistoria);
            } else {
                $this->daoHistoria->updateStoryNoImage($titulo, $sinopsis, $idHistoria);
            }

            // Desasignamos todos los género y estado de la historia.
            $this->daoHistoria_genero->desasignarAllGeneroHistoria($idHistoria);
            $this->daoHistoria_estado->desasignarAllEstadoHistoria($idHistoria);


            // Se asignan los generos a la historia.
            foreach ($genero as $gen) {
                if ($this->daoHistoria_genero->checkHistoriaGenero($idHistoria, $gen) === 0) {
                    $this->daoHistoria_genero->asignarGeneroHistoria($idHistoria, $gen);
                }
            }

            // Si ha seleccionado varios generos y el 'Ninguno', se elimina el 'Ninguno'.
            if (count($genero) > 1) {
                if (in_array(1, $genero)) {
                    $this->daoHistoria_genero->desasignarGeneroHistoria($idHistoria, 1);
                }
            }

            // Se asigna el estado a la historia.
            if ($this->daoHistoria_estado->checkHistoriaEstado($idHistoria, $estado) !== 1) {
                $this->daoHistoria_estado->asignarEstadoHistoria($idHistoria, $estado);
            }
            
            $this->pdo->commit();

            // Si no hay errores.
            echo json_encode(['success' => true, 'redirect' => 'index.php?action=crearHistoria&operation=editStory', 'message' => 'La historia se ACTUALIZO correctamente.']);
            exit;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // Elimina la Historia seleccionada.
    public function deleteStory()
    {
        header('Content-Type: application/json');

        try {
            $idHistoria = $_POST['idHistoria'];

            $this->daoHistoria->deleteHistoria($idHistoria);

            echo json_encode(['success' => true, 'redirect' => 'index.php?action=crearHistoria&operation=deleteStory', 'message' => 'La Historia ha sido ELIMINADA con éxito.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    // Buscar datos generales de la Historia.
    public function searchDataStory()
    {
        if (isset($_POST['idHistoria'])) {
            $idHistoria = $_POST['idHistoria'];

            $story = $this->daoHistoria->selecStoryId($idHistoria);

            if ($story) {
                $estado = $this->daoHistoria_estado->selectStatesStory($idHistoria);
                $generos = $this->daoHistoria_genero->selectGenreStory($idHistoria);
                $capitulos = $this->daoCapitulo->selecDataCapitulo($idHistoria);

                // Devolver los datos de la historia, estado, géneros, y los capítulos.
                echo json_encode([
                    'success' => true,
                    'Titulo' => $story['Titulo'],
                    'Sinopsis' => $story['Sinopsis'],
                    'Imagen' => $story['Imagen'],
                    'EstadoId' => $estado,
                    'Generos' => $generos,
                    'Capitulos' => $capitulos
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Historia no encontrada']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Id de historia no proporcionado']);
        }
    }

    // Buscar datos del capítulo.
    public function searchDataCap()
    {
        if (isset($_POST['idCapitulo'])) {
            $idCapitulo = $_POST['idCapitulo'];

            $cap = $this->daoCapitulo->selecDataCap($idCapitulo);

            if ($cap) {
                // Devolver los datos del capítulo.
                echo json_encode([
                    'success' => true,
                    'TituloCap' => $cap['TituloCap'],
                    'Historia' => $cap['Historia']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Capítulo no encontrado']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Id del capítulo no proporcionado']);
        }
    }

    // Validar el id y la historia del capítulo para crear.
    public function validateCreateCap()
    {
        header('Content-Type: application/json');

        $errores = [];

        // Validar datos.
        if (!isset($_POST['historiaId']) || !is_numeric($_POST['historiaId'])) {
            $errores['historiaId'] = "El Id de la historia no es válido.";
        }

        if (!(isset($_POST['historia'])) || empty($_POST['historia'])) {
            $errores['historia'] = "Tienes que escribir una Historia obligatoriamente.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Validar el id y la historia del capítulo para editar.
    public function validateEditCap()
    {
        header('Content-Type: application/json');

        $errores = [];

        // Validar datos.
        if (empty($_POST['idCapitulo'])) {
            $errores['capituloSelect'] = "Tienes que seleccionar un capítulo";
        }

        if (!(isset($_POST['historia'])) || empty($_POST['historia'])) {
            $errores['historia'] = "Tienes que escribir una Historia obligatoriamente.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Validar el id del capítulo para borrar.
    public function validateDeleteCap()
    {
        header('Content-Type: application/json');

        $errores = [];

        $idCapitulo = $_POST['idCapitulo'];

        // Validar dato.
        if (empty($idCapitulo)) {
            $errores['capituloSelect'] = "Tienes que seleccionar un capítulo";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Crear un capítulo nuevo.
    public function createCap()
    {
        header('Content-Type: application/json');

        $historiaId = $_POST['historiaId'];
        $numCapitulo = $this->daoCapitulo->selecNumCaps($historiaId) + 1;
        $tituloCap = (isset($_POST['tituloCap']) && !empty($_POST['tituloCap'])) ? trim($_POST['tituloCap']) : "$numCapitulo";
        $historia = trim($_POST['historia']);

        $this->daoCapitulo->createCapitulo($historiaId, $numCapitulo, $tituloCap, $historia);

        echo json_encode(['success' => true, 'redirect' => 'index.php?action=crearHistoria&operation=editStory', 'message' => 'El capítulo se CREO correctamente.']);
    }

    // Editar capítulo seleccionado.
    public function editCap()
    {
        header('Content-Type: application/json');

        $idCapitulo = $_POST['idCapitulo'];
        $numCapitulo = $this->daoCapitulo->selecNumCapitulo($idCapitulo); // Si no introduce un título se le insertará el número que le corresponda.
        $tituloCap = (isset($_POST['tituloCap']) && !empty($_POST['tituloCap'])) ? trim($_POST['tituloCap']) : "$numCapitulo";
        $historia = trim($_POST['historia']);

        $this->daoCapitulo->updateCap($idCapitulo, $tituloCap, $historia);

        echo json_encode(['success' => true, 'redirect' => 'index.php?action=crearHistoria&operation=editStory', 'message' => 'El capítulo se ACTUALIZO correctamente.']);
    }

    // Elimina el capítulo seleccionado.
    public function deleteCap()
    {
        header('Content-Type: application/json');

        $idCapitulo = $_POST['idCapitulo'];

        $this->daoCapitulo->deleteCap($idCapitulo);

        echo json_encode(['success' => true, 'redirect' => 'index.php?action=crearHistoria&operation=editStory', 'message' => 'El capítulo se ELIMINO correctamente.']);
    }
}
