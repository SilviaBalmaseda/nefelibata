<?php

require_once __DIR__  .  '/config/db.php';
require_once __DIR__  .  '/controllers/Controller.php';
require_once __DIR__  .  '/controllers/ControllerAdmin.php';
require_once __DIR__  .  '/controllers/ControllerStory.php';

$controller = new Controller($pdo);
$controllerAdmin = new ControllerAdmin($pdo);
$controllerStory = new ControllerStory($pdo);

$formType = $_POST['formType'] ?? null;
$action = $_GET['action'] ?? 'index';

if ($formType) {
    switch ($formType) {
        case 'iniciarSesion':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->iniciarSesion();
            }
            break;
        case 'registrar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->registrar();
            }
            break;
        case 'favorito':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->favorito();
            }
            break;
        case 'returnStatuGen':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->returnStatuGen();
            }
            break;
        case 'validateCreate':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->validateCreate();
            }
            break;
        case 'validateEditStory':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->validateEditStory();
            }
            break;
        case 'createStory':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->createStory();
            }
            break;
        case 'editStory':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->editStory();
            }
            break;
        case 'deleteStory':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->deleteStory();
            }
            break;
        case 'searchDataStory':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->searchDataStory();
            }
            break;
        case 'searchDataCap':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->searchDataCap();
            }
            break;
        case 'validateCreateCap':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->validateCreateCap();
            }
            break;
        case 'validateEditCap':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->validateEditCap();
            }
            break;
        case 'validateDeleteCap':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->validateDeleteCap();
            }
            break;
        case 'createCap':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->createCap();
            }
            break;
        case 'editCap':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->editCap();
            }
            break;
        case 'deleteCap':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerStory->deleteCap();
            }
            break;
        case 'validateGenre':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->validarCreateGenre();
            }
            break;
        case 'validateStatus':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->validarCreateStatus();
            }
            break;
        case 'createGenre':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->crearGenero();
            }
            break;
        case 'createStatus':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->crearEstado();
            }
            break;
        case 'searchUser':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->buscarUsuario();
            }
            break;
        case 'validateDeleteUser':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->validarDeleteUser();
            }
            break;
        case 'deleteUser':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->eliminarUsuario();
            }
            break;
        case 'validateDeleteGenre':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->validarDeleteGenre();
            }
            break;
        case 'validateDeleteStatus':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->validarDeleteStatus();
            }
            break;
        case 'deleteGenero':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->eliminarGenero();
            }
            break;
        case 'deleteStatus':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->eliminarEstado();
            }
            break;
        case 'searchStory':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->buscarHistoria();
            }
            break;
        case 'validateDeleteStory':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->validarDeleteStory();
            }
            break;
        case 'deleteStoryAdmin':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controllerAdmin->eliminarHistoria();
            }
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tipo de formulario no válido']);
            break;
    }
} else {
    switch ($action) {
        case 'fSearchStory':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->searchStory();
            } else {
                include 'views/index.php';
            }
            break;
        case 'leer':
            $controller->leerHistoria();
            break;
        case 'crearHistoria':
            $controllerStory->showCrearHistoria();
            break;
        case 'iniciarSesion':
            include 'views/iniciarSesion.php';
            break;
        case 'admin':
            $controllerAdmin->admin();
            break;
        case 'registrar':
            include 'views/registrar.php';
            break;
        case 'ajustes':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->ajustes();
            } else {
                include 'views/ajustes.php';
            }
            break;
        default:
            $controller->index();
            break;
    }
}
