<!DOCTYPE html>
<html lang="es">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__  . '/../includes/functions.php';

$user = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : null;

?>

<head>
    <?php
    // Escribir todos los links necesarios.
    if (file_exists(__DIR__  . '/../includes/linksHead.php')) {
        include __DIR__  . '/../includes/linksHead.php';
    } else {
        echo '<p>Error: El archivo de linksHead no se encontró.</p>';
        exit; // Detener la ejecución si el archivo no se encuentra.
    }
    ?>

    <title>Iniciar Sesión</title>
</head>

<body>
    <?php
    // Mostrar la barra de navegación según el usuario.
    showNavbar($user);
    ?>

    <div class="container textCenter">
        <h1>Iniciar Sesión</h1>
        <form action="index.php?action=iniciarSesion" method="POST" id="fIniciarSesion" class="fIniciarSesion" role="form" novalidate>
            <input type="hidden" name="form_type" value="iniciarSesion">
            <div>
                <label for="nameUser">
                    <h5>Nombre de Usuario: </h5>
                </label>
                <input type="text" id="nameUser" class="styleInput" name="nameUser" required>
                <p id="errorNameUser" class="error-message"></p>
            </div>
            <div>
                <label for='clave'>
                    <h5>Contraseña: </h5>
                </label>
                <input type='password' id='clave' class="styleInput" name='clave' required>
                <p id="errorClave" class="error-message"></p>
            </div>
            <p id="errorNameUserClave" class="error-message"></p>
            <button type="submit" class="btn btnColor">Iniciar Sesión</button>
        </form>
    </div>

    <?php
    // Mostrar el pie de página.
    showFooter();
    ?>

    <script src="./js/util.js"></script>
    <script src="./js/main.js"></script>
</body>

</html>