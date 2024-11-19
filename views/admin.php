<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../includes/functions.php';

$user = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : null;

?>

<!DOCTYPE html>
<html lang="es">

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

    <title>Admin</title>
</head>

<body>
    <?php
    // Mostrar la barra de navegación según el usuario.
    showNavbar($user);
    ?>

    <div class="container">
        <h1 class="textCenter">Admin</h1>
        <section id="mainArea" class="row row-cols-1 row-cols-md-3 g-4 text-center">
            <!-- Crear Género -->
            <div class="col">
                <div class="card h-100">
                    <div class="card-text">
                        <form name="fCreateGenre" role="form" class="row g-3 formu" id="fCreateGenre" action="index.php?action=admin" method="POST" novalidate>
                            <label for="nameGenero">
                                <h3>Crear Género: </h3>
                            </label>

                            <input type="text" class="form-control" id="nameGenero" name="nameGenero" placeholder="Nombre del Género" required>
                            <p id="errorNameGenero" class="error-message"></p>

                            <button class="btn btn-success" id="btnCreateGenero" type="submit">
                                <i class="bi bi-plus-circle btnAdmin"> CREAR GÉNERO </i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Crear Estado -->
            <div class="col">
                <div class="card h-100">
                    <div class="card-text">
                        <form name="fCreateStatus" role="form" class="row g-3 formu" id="fCreateStatus" action="index.php?action=admin" method="POST" novalidate>
                            <label for="nameStatus">
                                <h3>Crear Estado: </h3>
                            </label>

                            <input type="text" class="form-control" id="nameStatus" name="nameStatus" placeholder="Nombre del Estado" required>
                            <p id="errorNameStatus" class="error-message"></p>

                            <button class="btn btn-success" id="btnCreateStatus" type="submit">
                                <i class="bi bi-plus-circle btnAdmin"> CREAR ESTADO </i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Eliminar Usuario -->
            <div class="col">
                <div class="card h-100">
                    <div class="card-text">
                        <form name="fDeleteUser" role="form" class="row g-3 formu" id="fDeleteUser" action="index.php?action=admin" method="POST" novalidate>
                            <label for="nameDelUser">
                                <h3>Eliminar Usuarios: </h3>
                            </label>

                            <input type="text" class="form-control" id="nameDelUser" name="nameDelUser" placeholder="Nombre del Usuario" required>
                            <p id="errorNameDelUser" class="error-message"></p>

                            <button class="btn btnColor" id="btnBuscarUser" type="submit" name="operation" value="searchUser">
                                <i class="bi bi-search btnAdmin"> BUSCAR USUARIO </i>
                            </button>

                            <div id="searchNameDelUser" class="searchNameDelUser notVisible"></div>

                            <button class="btn btnDelete searchNameDelUser notVisible" id="btnDeleteUser" type="submit" name="operation" value="deleteUser">
                                <i class="bi bi-x-square btnAdmin"> ELIMINAR USUARIO </i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Eliminar Géneros -->
            <div class="col">
                <div class="card h-100">
                    <div class="card-text">
                        <form name="fDeleteGenero" role="form" class="row g-3 formu" id="fDeleteGenero" action="index.php?action=admin" method="POST" novalidate>
                            <label for="selecDelGen">
                                <h3>Eliminar Géneros: </h3>
                            </label>

                            <?php if (!empty($data['generos'])) : ?>
                                <select name="selecDelGen[]" class="form-select" id="selecDelGen" aria-describedby="selecDelGen" multiple>
                                    <?php foreach ($data['generos'] as $gen) : ?>
                                        <?php if ($gen['IdGenero'] !== 1) : ?>
                                            <option value="<?php echo htmlspecialchars($gen['IdGenero']); ?>">
                                                <?php echo htmlspecialchars($gen['Nombre']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    No se encontraron los géneros.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <p id="errorSelecDelGen" class="error-message"></p>

                            <button class="btn btnDelete" id="btnDeleteGenero" type="submit">
                                <i class="bi bi-x-square btnAdmin"> ELIMINAR GÉNERO </i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Eliminar Estados -->
            <div class="col">
                <div class="card h-100">
                    <div class="card-text">
                        <form name="fDeleteStatus" role="form" class="row g-3 formu" id="fDeleteStatus" action="index.php?action=admin" method="POST" novalidate>
                            <label for="selecDelStatus">
                                <h3>Eliminar Estados: </h3>
                            </label>

                            <?php if (!empty($data['estados'])) : ?>
                                <select name="selecDelStatus[]" class="form-select" id="selecDelStatus" aria-describedby="selecDelStatus" multiple>
                                    <?php foreach ($data['estados'] as $status) : ?>
                                        <option value="<?php echo htmlspecialchars($status['IdEstado']); ?>">
                                            <?php echo htmlspecialchars($status['Nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    No se encontraron los estados.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <p id="errorSelecDelStatus" class="error-message"></p>

                            <button class="btn btnDelete" id="btnDeleteStatus" type="submit">
                                <i class="bi bi-x-square btnAdmin"> ELIMINAR ESTADO </i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Eliminar Historias -->
            <div class="col">
                <div class="card h-100">
                    <div class="card-text">
                        <form name="fDeleteHistoria" role="form" class="row g-3 formu" id="fDeleteHistoria" action="index.php?action=admin" method="POST" novalidate>
                            <label for="nameDelHistoria">
                                <h3>Eliminar Historias: </h3>
                            </label>

                            <input type="text" class="form-control" id="nameDelHistoria" name="nameDelHistoria" placeholder="Nombre de la Historia" required>
                            <p id="errorNameDelHistoria" class="error-message"></p>

                            <button class="btn btnColor" id="btnBuscarHistoria" type="submit" name="operation" value="searchStory">
                                <i class="bi bi-search btnAdmin"> BUSCAR HISTORIA/AUTOR </i>
                            </button>

                            <div id="searchNameDelHistoria" class="searchNameDelHistoria notVisible"></div>

                            <button class="btn btnDelete searchNameDelHistoria notVisible" id="btnDeleteHistoria" type="submit" name="operation" value="deleteStory">
                                <i class="bi bi-x-square btnAdmin"> ELIMINAR HISTORIA </i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title  w-100 text-center" id="confirmModalLabel">Confirmar envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas realizar está acción?
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="confirmSubmit" class="btn btn-primary">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de éxito o error -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title  w-100 text-center" id="successModalLabel">Éxito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="resultMessage"></p>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" id="closeSubmit" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Mostrar el pie de página.
    showFooter();
    ?>

    <script src="./js/util.js"></script>
    <script src="./js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>