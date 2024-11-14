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

    <title>Registrarse</title>
</head>

<body>
    <?php
    // Mostrar la barra de navegación según el usuario.
    showNavbar($user);
    ?>

    <div class="container textCenter">
        <h1>Registrarse</h1>
        <form action="index.php?action=registrar" method="POST" id="fRegistrar" class="fRegistrar" role="form" novalidate>
            <input type="hidden" name="form_type" value="registrar">
            <div>
                <label for="nameUser">
                    <h5>Nombre de Usuario: </h5>
                </label>
                <input type="text" id="nameUser" class="styleInput" name="nameUser" placeholder="Nombre de Usuario" required>
                <div id="errorNameUser" class="error-message"></div>
            </div>
            <div>
                <label for="clave">
                    <h5>Contraseña: </h5>
                </label>
                <input type="password" id="clave" class="styleInput" name="clave" placeholder="Contraseña" required>
                <div id="errorClave" class="error-message"></div>
            </div>
            <div>
                <label for="email">
                    <h5>Email: </h5>
                </label>
                <input type="email" id="email" class="styleInput" name="email" placeholder="nefelibata@gmail.com" title="nombre + @ + terminación">
                <div id="errorEmail" class="error-message"></div>
            </div>
            <button type="submit" class="btn btnColor">Registrarse</button>
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