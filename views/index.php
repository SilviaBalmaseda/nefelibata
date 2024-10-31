<!DOCTYPE html>
<html lang="es">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../includes/functions.php';

$user = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : null;

// Para la paginación.
$baseUrl = '?';
if (isset($_GET['action'])) {
    $baseUrl .= 'action=' . urlencode($_GET['action']) . '&';
}
if (isset($_GET['searchStory'])) {
    $baseUrl .= 'searchStory=' . urlencode($_GET['searchStory']) . '&';
}
if (isset($_GET['nombre'])) {
    $baseUrl .= 'nombre=' . urlencode($_GET['nombre']) . '&';
}

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

    <title>NEFELIBATA</title>
</head>

<body>
    <?php
    // Mostrar la barra de navegación según el usuario.
    showNavbar($user);
    ?>

    <div class="container">
        <h1 class="textCenter">GENEROS</h1>
        <!-- Mostrar los géneros -->
        <div>
            <ul class="barraGenero">
                <?php if (!empty($generos)) : ?>
                    <?php foreach ($generos as $genero) : ?>
                        <li>
                            <div>
                                <?php if ($genero['Nombre'] != "Ninguno") : ?>
                                    <i class="bi bi-dash"></i>
                                    <a href="index.php?action=selec&nombre=<?php echo htmlspecialchars($genero['Nombre']); ?>">
                                        <?php echo htmlspecialchars($genero['Nombre']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <span>No se encontraron géneros.</span>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Mostrar las historias -->
        <?php if (!empty($storysCards)) : ?>
            <div id="CardsHistorias" class="row row-cols-1 row-cols-md-4 g-4 text-center container">
                <?php if (!empty($storysCards)) : ?>
                    <?php foreach ($storysCards as $story) : ?>
                        <div class="col">
                            <div class="card h-100">
                                <h5 class="card-title"><b><?php echo htmlspecialchars($story['Titulo']); ?></b></h5>
                                <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo htmlspecialchars($story['Nombre']); ?></h6>

                                <?php if ($story['Imagen']) : ?>
                                    <img src='data:image/jpg;base64,<?php echo htmlspecialchars($story['Imagen']); ?>' class="card-img-top img-thumbnail" alt="Portada de la historia.">
                                <?php else: ?>
                                    <img src="./img/sinImagen.png" class="card-img-top img-thumbnail" alt="No hay imagen disponible.">
                                <?php endif; ?>

                                <div class="card-body">
                                    <button type="button"
                                        class="btn-leer"
                                        data-title="<?php echo $story['Titulo']; ?>">
                                        Leer
                                    </button>

                                    <button type="button" id="btnFavorito-<?php echo htmlspecialchars($story['Id']); ?>"
                                        class="btn-favorito"
                                        data-id="<?php echo $story['Id']; ?>"
                                        data-es-favorito="<?php echo $story['esFavorito'] ? 'true' : 'false'; ?>">
                                        <i class="bi <?php echo $story['esFavorito'] ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                                        <?php echo htmlspecialchars($story['NumFavorito']); ?>
                                    </button>

                                    <button type="button" id="btnSinopsis-<?php echo htmlspecialchars($story['Id']); ?>"
                                        class="btn-sinopsis"
                                        data-id="<?php echo $story['Id']; ?>"
                                        data-sinopsis="<?php echo htmlspecialchars($story['Sinopsis']); ?>">
                                        Sinopsis
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <nav aria-label="Paginación">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $paginaActual == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $baseUrl . 'page=' . ($paginaActual - 1); ?>" tabindex="-1">Anterior</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
                        <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo $baseUrl . 'page=' . $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php echo $paginaActual == $totalPaginas ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $baseUrl . 'page=' . ($paginaActual + 1); ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        <?php else : ?>
            <?php if (!empty($errores)): ?>
                <p id="errorSearchStory" class="error-message">
                    <?php echo htmlspecialchars($errores); ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>


    <!-- Modal para mostrar la Sinopsis -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center" id="messageModalLabel">Sinopsis</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="estadoContent"></p>
                    <p id="generoContent"></p>
                    <hr>
                    <p id="sinopsisContent"></p>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btnColor" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Mostrar el pie de página.
    showFooter();
    ?>

    <script src="./js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>