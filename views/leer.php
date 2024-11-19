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

    <title>Leer <?php echo htmlspecialchars($story['Titulo']); ?></title>
</head>

<body class="page-body">
    <?php
    // Mostrar la barra de navegación según el usuario.
    showNavbar($user);
    ?>

    <div class="container mt-5">
        <div class="d-flex">
            <div class="text-center maxAncho align-self-center">
                <h1><?php echo htmlspecialchars($story['Titulo']); ?></h1>
            </div>

            <div class="d-flex ms-auto">
                <div class="d-flex flex-column align-items-end">
                    <div class="text-center mb-2">
                        <?php if ($story['Imagen']) : ?>
                            <img src='data:image/jpg;base64,<?php echo htmlspecialchars($story['Imagen']); ?>' class="story-image" alt="Portada de la historia.">
                        <?php else: ?>
                            <img src="./img/sinImagen.png" class="story-image" alt="No hay imagen disponible.">
                        <?php endif; ?>
                    </div>

                    <!-- Desplegable de los capítulos -->
                    <div class="dropdown chapter-dropdown">
                        <button class="btn btn-secondary dropdown-toggle chapter-select-btn" type="button" id="dropdownCapitulo" data-bs-toggle="dropdown" aria-expanded="false">
                            Capítulo: <?php echo htmlspecialchars($capituloActualTitulo); ?>
                        </button>
                        <ul id="capituloSelect" class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownCapitulo">
                            <?php foreach ($dataCap as $cap) : ?>
                                <li>
                                    <a class="dropdown-item <?php echo $cap['NumCapitulo'] == $capituloActual ? 'active' : ''; ?>" href="#" data-num-capitulo="<?php echo $cap['NumCapitulo']; ?>">
                                        <?php echo htmlspecialchars($cap['TituloCap']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor de la historia -->
        <div class="story-container p-5 mb-5">
            <h2 class="chapter-title text-center mb-4 maxAncho styleTitle">
                <?php echo htmlspecialchars($capituloActualTitulo); ?>
            </h2>
            <p class="story-text maxAncho">
                <?php echo nl2br(htmlspecialchars($capitulo)); ?>
            </p>
        </div>

        <!-- Botones de navegación -->
        <div class="d-flex justify-content-between mb-5 maxAnchoBtn">
            <button id="btn-anterior" class="btn  btn-lg btn-anterior btn-nav <?php echo !$hayAnterior ? 'disabled btn-outline-primary' : 'btn-primary'; ?>">
                <i class="bi bi-skip-backward-fill"></i> Anterior
            </button>

            <button id="btn-siguiente" class="btn btn-lg btn-siguiente btn-nav <?php echo !$haySiguiente ? 'disabled btn-outline-primary' : 'btn-primary'; ?>">
                Siguiente <i class="bi bi-skip-forward-fill"></i>
            </button>
        </div>
    </div>

    <?php
    // Mostrar el pie de página.
    showFooter();
    ?>

    <script src="./js/leer.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>