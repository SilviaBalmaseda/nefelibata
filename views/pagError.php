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

    <title>Error</title>
</head>

<body>
    <?php
    // Mostrar la barra de navegación según el usuario.
    showNavbar($user);
    ?>

    <div class="container textCenter">
        <h1>Página de <strong class="text-danger">Error</strong></h1>

        <?php
        // Obtener el código de error.
        $error = $error ?? '';

        switch ($error) {
            case 'userStory':
                echo "<h3>Solo pueden entrar los <strong class='text-danger'>Usuarios Registrados</strong> a está sección.</h3>";
                break;
            case 'userAdmin':
                echo "<h3>Solo pueden entrar los <strong class='text-danger'>Administradores</strong> a está sección.</h3>";
                break;
            default:
                echo "<h3 class='text-danger'>Ha ocurrido un problema. Por favor, inténtelo de nuevo.</h3>";
                break;
        }
        ?>
    </div>

    <?php
    // Mostrar el pie de página.
    showFooter();
    ?>

    <script src="./js/util.js"></script>
    <script src="./js/main.js"></script>
</body>

</html>