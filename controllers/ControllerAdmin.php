<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/DaoGenero.php';
require_once __DIR__ . '/../models/DaoEstado.php';
require_once __DIR__ . '/../models/DaoHistoria.php';
require_once __DIR__ . '/../models/DaoCapitulo.php';
require_once __DIR__ . '/../models/DaoFavorito.php';
require_once __DIR__ . '/../models/DaoUsuario.php';

class ControllerAdmin
{
    private $daoGenero;
    private $daoEstado;
    private $daoHistoria;
    private $daoCapitulo;
    private $daoFavorito;
    private $daoUsuario;

    public function __construct($db)
    {
        $this->daoGenero = new DaoGenero($db);
        $this->daoEstado = new DaoEstado($db);
        $this->daoHistoria = new DaoHistoria($db);
        $this->daoCapitulo = new DaoCapitulo($db);
        $this->daoFavorito = new DaoFavorito($db);
        $this->daoUsuario = new DaoUsuario($db);
    }

    // Devuelve un array con los datos necesarios para el Admin.
    private function loadAdminData()
    {
        // Array con los nombres de los géneros y estados que hay.
        $generos = $this->daoGenero->selecGenero() ?: [];
        $estados = $this->daoEstado->selecEstado() ?: [];

        // Array con algunos datos de las historias que hay.
        $historias = $this->daoHistoria->selecHistoria() ?: [];

        return [
            'generos' => $generos,
            'estados' => $estados,
            'historias' => $historias
        ];
    }

    // Muestra(pasa datos para) la interfaz de admin.
    public function admin()
    {
        $data = $this->loadAdminData();
        include 'views/admin.php';
    }

    // Función para validar los datos del formulario de Crear Género.
    public function validarCreateGenre()
    {
        header('Content-Type: application/json');

        $nameGenero = trim($_POST['nameGenero']);
        $errores = [];

        // Validar datos.
        if (empty($nameGenero)) {
            $errores['nameGenero'] = "El Nombre del Género es obligatorio.";
        } elseif ($this->daoGenero->checkGenero($nameGenero) > 0) {
            $errores['nameGenero'] = "Ese Nombre de Género ya está en uso. Elige otro.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Función para validar los datos del formulario de Crear Estado.
    public function validarCreateStatus()
    {
        header('Content-Type: application/json');

        $nameStatus = trim($_POST['nameStatus']);
        $errores = [];

        // Validar datos.
        if (empty($nameStatus)) {
            $errores['nameStatus'] = "El Nombre del Estado es obligatorio.";
        } elseif ($this->daoEstado->checkEstado($nameStatus) > 0) {
            $errores['nameStatus'] = "Ese Nombre de Estado ya está en uso. Elige otro.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Función para crear un nuevo Género.
    public function crearGenero()
    {
        $nameGenero = trim($_POST['nameGenero']);

        $this->daoGenero->createGenero($nameGenero);

        // Si no hay errores.
        echo json_encode(['success' => true, 'redirect' => 'index.php?action=admin', 'message' => 'El Género ha sido CREADO con éxito.']);
    }

    // Función para crear un nuevo Estado.
    public function crearEstado()
    {
        $nameStatus = trim($_POST['nameStatus']);

        $this->daoEstado->createEstado($nameStatus);

        // Si no hay errores.
        echo json_encode(['success' => true, 'redirect' => 'index.php?action=admin', 'message' => 'El Estado ha sido CREADO con éxito.']);
    }

    // Busca los usuarios con un nombre(parecido) al que ha pasado.
    public function buscarUsuario()
    {
        header('Content-Type: application/json');

        $nameDelUser = trim($_POST['nameDelUser']);
        $errores = [];

        // Validar datos.
        if (!isset($nameDelUser) || empty($nameDelUser)) {
            $errores['nameDelUser'] = "Tienes que introducir un nombre para buscar.";
        } else {
            $usuarios = $this->daoUsuario->selecUsuario($nameDelUser);
            if (empty($usuarios)) {
                $errores['nameDelUser'] = "No se encontraron usuarios con ese nombre. Elige otro.";
            }
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true, 'dropdown' => $usuarios]);
    }

    // Función para validar el formulario de Eliminar Usuario.
    public function validarDeleteUser()
    {
        header('Content-Type: application/json');

        $selecDelUsuario = isset($_POST['selecDelUsuario']) ? $_POST['selecDelUsuario'] : [];
        $errores = [];

        // Validar dato.
        if (empty($selecDelUsuario)) {
            $errores['selecDelUsuario'] = "Tienes que seleccionar algún Usuario para eliminar lo.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Elimina los usuarios seleccionados.
    public function eliminarUsuario()
    {
        header('Content-Type: application/json');

        $selecDelUsuario = $_POST['selecDelUsuario'];

        foreach ($selecDelUsuario as $idUser) {
            // Restar favorito de la tabla historia si le ha dado el usuario.
            $idsFav = $this->daoFavorito->selectFavoriteUser($idUser);
            if ($idsFav) {
                foreach ($idsFav as $idStory) {
                    $this->daoHistoria->subtractFavorite($idStory);
                }
            }
            // Eliminar favoritos.
            $this->daoFavorito->deleteFavoriteUser($idUser);

            // Eliminar historias y capítulos si el usuario tiene.
            $idHistorias = $this->daoHistoria->selectIdStotyUser($idUser);
            if ($idHistorias) {
                foreach ($idHistorias as $historiaId) {
                    $this->daoCapitulo->deleteCapStoryId($historiaId);
                    $this->daoHistoria->deleteHistoria($historiaId);
                }
            }

            // Eliminar usuario.
            $this->daoUsuario->deleteUser($idUser);
        }

        // Si no hay errores.
        echo json_encode(['success' => true, 'redirect' => 'index.php?action=admin', 'message' => 'Usuario(s) eliminado(s) con éxito.']);
    }

    // Función para validar el formulario de Eliminar Género.
    public function validarDeleteGenre()
    {
        header('Content-Type: application/json');

        $selecDelGenero = isset($_POST['selecDelGen']) ? $_POST['selecDelGen'] : [];
        $errores = [];

        // Validar datos.
        if (empty($selecDelGenero)) {
            $errores['selecDelStatus'] = "Tienes que seleccionar algún Género para eliminar lo.";
        } else if (($this->daoGenero->checkNumGenero() - 1) <= count($selecDelGenero)) {
            // Si el número de géneros seleccionados es mayor o igual al total de géneros.
            $errores['selecDelGen'] = "No puedes eliminar todos los géneros, mínimo tiene que quedar 1.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            exit;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
        exit;
    }

    // Función para validar el formulario de Eliminar Estado.
    public function validarDeleteStatus()
    {
        header('Content-Type: application/json');

        $selecDelEstado = isset($_POST['selecDelStatus']) ? $_POST['selecDelStatus'] : [];
        $errores = [];

        // Validar datos.
        if (empty($selecDelEstado)) {
            $errores['selecDelStatus'] = "Tienes que seleccionar algún Estado para eliminar lo.";
        } else if ($this->daoEstado->checkNumEstado() <= count($selecDelEstado)) {
            // Si el número de estados seleccionados es mayor o igual al total de estados.
            $errores['selecDelStatus'] = "No puedes eliminar todos los estados, mínimo tiene que quedar 1.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Elimina los géneros seleccionados.
    public function eliminarGenero()
    {
        header('Content-Type: application/json');

        $selecDelGenero = $_POST['selecDelGen'];

        $message = "Género(s) eliminado(s) con éxito.";

        foreach ($selecDelGenero as $idGenero) {
            if (($this->daoGenero->checkNumGenero() - 1) > 1) {
                $this->daoGenero->deleteGenero($idGenero);
            } else {
                // Si es el último género que queda.
                $message = "Se han eliminado todos los géneros menos 1, obligatoriamente tiene que haber 1 género.";
                break;
            }
        }

        // Si no hay errores.
        echo json_encode(['success' => true, 'redirect' => 'index.php?action=admin', 'message' => $message]);
    }

    // Elimina los estados seleccionados.
    public function eliminarEstado()
    {
        header('Content-Type: application/json');

        $selecDelEstado = $_POST['selecDelStatus'];

        $message = "Estado(s) eliminado(s) con éxito.";

        foreach ($selecDelEstado as $idEstado) {
            if ($this->daoEstado->checkNumEstado() > 1) {
                $this->daoEstado->deleteEstado($idEstado);
            } else {
                // Si es el último estado que queda.
                $message = "Se han eliminado todos los estados menos 1, obligatoriamente tiene que haber 1 estado.";
                break;
            }
        }

        // Si no hay errores.
        echo json_encode(['success' => true, 'redirect' => 'index.php?action=admin', 'message' => $message]);
    }

    // Busca las historias y los usuarios con un nombre(parecido) al que ha pasado.
    public function buscarHistoria()
    {
        header('Content-Type: application/json');

        $nameDelHistoria = trim($_POST['nameDelHistoria']);
        $errores = [];

        // Validar datos.
        if (!isset($nameDelHistoria) || empty($nameDelHistoria)) {
            $errores['nameDelHistoria'] = "Tienes que introducir un nombre para buscar.";
        } else {
            $historias = $this->daoHistoria->selecHistoriaAutor($nameDelHistoria);
            if (empty($historias)) {
                $errores['nameDelHistoria'] = "No se encontraron historias con ese nombre. Elige otro.";
            }
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true, 'dropdown' => $historias]);
    }

    // Función para validar el formulario de Eliminar Historia.
    public function validarDeleteStory()
    {
        header('Content-Type: application/json');

        $selecDelHistoria = isset($_POST['selecDelHistoria']) ? $_POST['selecDelHistoria'] : [];
        $errores = [];

        // Validar datos.
        if (empty($selecDelHistoria)) {
            $errores['selecDelHistoria'] = "Tienes que seleccionar alguna Historia para eliminar la.";
        } else if ($this->daoHistoria->checkNumStory() <= count($selecDelHistoria)) {
            // Si el número de historias seleccionados es mayor o igual al total de historias.
            $errores['selecDelHistoria'] = "No puedes eliminar todas las historias, mínimo tiene que quedar 1.";
        }

        // Si hay errores.
        if (!empty($errores)) {
            echo json_encode(['success' => false, 'errors' => $errores]);
            return;
        }

        // Si no hay errores.
        echo json_encode(['success' => true]);
    }

    // Elimina las historias seleccionadas.
    public function eliminarHistoria()
    {
        header('Content-Type: application/json');

        $selecDelHistoria = $_POST['selecDelHistoria'];

        $message = "Historia(s) eliminada(s) con éxito.";

        foreach ($selecDelHistoria as $historiaId) {
            if ($this->daoHistoria->checkNumStory() > 1) {
                // Eliminar favoritos.
                $this->daoFavorito->deleteFavoriteStory($historiaId);
                $this->daoCapitulo->deleteCapStoryId($historiaId);
                $this->daoHistoria->deleteHistoria($historiaId);
            } else {
                // Si es la última historia que queda.
                $message = "Se han eliminado todas las historias menos 1, obligatoriamente tiene que haber 1 historia.";
                break;
            }
        }

        // Si no hay errores.
        echo json_encode(['success' => true, 'redirect' => 'index.php?action=admin', 'message' => $message]);
        exit;
    }
}
