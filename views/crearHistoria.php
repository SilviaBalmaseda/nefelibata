<!DOCTYPE html>
<html lang="es">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__  . '/../includes/functions.php';

$user = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : null;

$operation = $_GET['operation'] ?? '';

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
    <title>Crear Historia</title>
</head>

<body>
    <?php
    // Mostrar la barra de navegación según el usuario.
    showNavbar($user);
    ?>

    <div class="container">
        <div id="formCreateH">
            <h1 class="textCenter">Mis Historias</h1>
            <section class="textCenter" id="formHeadStory">
                <a class="btn btn-success" href="index.php?action=crearHistoria&operation=createStory"><i class="bi bi-plus-circle btnAdmin"> CREAR HISTORIA</i></a>
                <a class="btn btnColor" href="index.php?action=crearHistoria&operation=editStory"><i class="bi bi-pencil-square"> EDITAR HISTORIA</i></a>
                <a class="btn btnDelete" href="index.php?action=crearHistoria&operation=deleteStory"><i class="bi bi-x-square btnAdmin"> ELIMINAR HISTORIA</i></a>
            </section>
        </div>

        <div id="formMainStory" class="createStyle">
            <?php if ($operation === 'createStory'): ?>
                <form name="fCreateStory" id="fCreateStory" role="form" class="container" enctype="multipart/form-data" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="titulo" class="form-label">
                            <h4>Título: </h4>
                        </label>
                        <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Titulo de la Historia" required>
                        <p id="errorTitulo" class="error-message"></p>
                    </div>

                    <div class="mb-3">
                        <label for="portada" class="form-label">
                            <h4>Portada: </h4>
                        </label>
                        <input type="file" id="portada" name="portada" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="tituloCap" class="form-label">
                            <h4>Título Capítulo: </h4>
                        </label>
                        <input type="text" id="tituloCap" name="tituloCap" class="form-control" placeholder="Título del capítulo">
                    </div>

                    <div class="mb-3">
                        <label for="sinopsis" class="form-label">
                            <h4>Sinopsis:</h4>
                        </label>
                        <textarea type="text" class="form-control" name="sinopsis" id="sinopsis" placeholder="Escribe aquí la Sinopsis" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="historia" class="form-label">
                            <h4>Historia:</h4>
                        </label>
                        <textarea type="text" class="form-control" name="historia" id="historia" placeholder="Escribir aquí la Historia" rows="10" required></textarea>
                        <p id="errorHistoria" class="error-message"></p>
                    </div>

                    <div class="row mb-3">
                        <div class="form-floating col-12 col-md-6 mb-3 mb-md-0">
                            <select class="form-select" name="estado" id="estado" aria-label="Etiqueta flotante de estado">
                                <?php foreach ($data['estados'] as $status) : ?>
                                    <option value="<?php echo htmlspecialchars($status['IdEstado']); ?>"><?php echo htmlspecialchars($status['Nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="estado">Selecciona un estado</label>
                        </div>

                        <div class="form-floating col-12 col-md-6">
                            <select name="genero[]" id="generoSelect" class="form-select genSelect" multiple aria-label="Etiqueta flotante de género">
                                <?php foreach ($data['generos'] as $gen) : ?>
                                    <option value="<?php echo htmlspecialchars($gen['IdGenero']); ?>"><?php echo htmlspecialchars($gen['Nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingSelect">Selecciona un género</label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button id="btnCreateStory" class="btn btn-success" type="submit" name="operation" value="btnCreateStory">Crear Historia <i class="bi bi-clipboard2-plus"></i></button>
                    </div>
                </form>
            <?php elseif ($operation === 'editStory'): ?>
                <div id="divStorys" class="row row-cols-1 row-cols-md-4 g-4 text-center container">
                    <?php if (!empty($data['autorStory'])) : ?>
                        <?php foreach ($data['autorStory'] as $story) : ?>
                            <div class="col">
                                <div class="card h-100">
                                    <h5 class="card-title"><b><?php echo htmlspecialchars($story['Titulo']); ?></b></h5>

                                    <?php if ($story['Imagen']) : ?>
                                        <img src='data:image/jpg;base64,<?php echo htmlspecialchars($story['Imagen']); ?>' class="card-img-top img-thumbnail" alt="Portada de la historia.">
                                    <?php else: ?>
                                        <img src="./img/sinImagen.png" class="card-img-top img-thumbnail" alt="No hay imagen disponible.">
                                    <?php endif; ?>

                                    <div class="card-body">
                                        <button type="button" class="btn btnColor btnEditStorys" data-id="<?php echo $story['IdHistoria']; ?>">Editar Historia <i class="bi bi-pencil-square"></i></button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span>No tienes Historias para editar.</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="fEditStoryContainer" class="container mt-4 d-none">
                    <input type="hidden" id="hiddenId" name="hiddenId" value="">
                    <form name="fEditStory" id="fEditStory" role="form" enctype="multipart/form-data" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="titulo" class="form-label">
                                <h4>Título: </h4>
                            </label>
                            <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Titulo de la Historia" required>
                            <p id="errorTitulo" class="error-message"></p>
                        </div>

                        <div class="mb-3">
                            <label for="portada" class="form-label">
                                <h4>Portada: </h4>
                            </label>
                            <input type="file" id="portada" name="portada" class="form-control">
                            <img id="imagen" src="" alt="Portada de la historia" class="img-thumbnail tamImg">
                        </div>

                        <div class="mb-3">
                            <label for="sinopsis" class="form-label">
                                <h4>Sinopsis:</h4>
                            </label>
                            <textarea type="text" class="form-control" name="sinopsis" id="sinopsis" placeholder="Escribe aquí la Sinopsis" rows="5"></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="form-floating col-12 col-md-6 mb-3 mb-md-0">
                                <select class="form-select" name="estado" id="estado" aria-label="Etiqueta flotante de estado">
                                    <?php foreach ($data['estados'] as $status) : ?>
                                        <option value="<?php echo htmlspecialchars($status['IdEstado']); ?>"><?php echo htmlspecialchars($status['Nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="estado">Selecciona un estado</label>
                            </div>

                            <div class="form-floating col-12 col-md-6">
                                <select name="genero[]" id="generoSelect" class="form-select genSelect" multiple aria-label="Etiqueta flotante de género">
                                    <?php foreach ($data['generos'] as $gen) : ?>
                                        <option value="<?php echo htmlspecialchars($gen['IdGenero']); ?>"><?php echo htmlspecialchars($gen['Nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingSelect">Selecciona un género</label>
                            </div>
                        </div>

                        <div id="btnsCapStory" class="d-flex justify-content-evenly">
                            <button type="button" id="btnUpdateHistoria" class="btn btn-info">Actualizar Historia <i class="bi bi-cloud-upload"></i></button>
                            <button type="button" id="btnEditarCapitulo" class="btn btnColor"><i class="bi bi-files"> Editar Capítulos</i></button>
                        </div>
                    </form>

                    <div id="sectionCap" class="mt-4 d-none">
                        <div class="mb-3">
                            <label for="capituloSelect" class="form-label">
                                <h4>Selecciona un Capítulo:</h4>
                            </label>
                            <select id="capituloSelect" name="capitulo" class="form-select">
                                <option value="">Selecciona un capítulo</option>
                                <!-- El resto de opciónes son dinámicas en el js -->
                            </select>
                            <p id="errorCapituloSelect" class="error-message"></p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" id="btnCreateCapitulo" class="btn btn-success"><i class="bi bi-plus-circle btnAdmin"> Crear Capítulo</i></button>
                            <button type="button" id="btnEditCapitulo" class="btn btnColor"><i class="bi bi-pencil-square"> Editar Capítulo</i></button>
                            <button type="submit" id="btnDeleteCap" class="btn btnDelete">Eliminar Capítulo <i class="bi bi-trash3"></i></button>
                        </div>
                    </div>

                    <div id="fActionCap" class="mt-4 d-none">
                        <form name="fEditCap" id="fEditCap" role="form" enctype="multipart/form-data" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="tituloCap" class="form-label">
                                    <h4>Título Capítulo: </h4>
                                </label>
                                <input type="text" id="tituloCap" name="tituloCap" class="form-control" placeholder="Título del capítulo">
                            </div>

                            <div class="mb-3">
                                <label for="historia" class="form-label">
                                    <h4>Historia del Capítulo:</h4>
                                </label>
                                <textarea type="text" class="form-control" name="historia" id="historia" placeholder="Historia del capítulo" rows="10" require></textarea>
                                <p id="errorHistoria" class="error-message"></p>
                            </div>

                            <div class="text-center">
                                <button id="btnAddCap" class="btn btn-success d-none" type="submit">Crear Capítulo <i class="bi bi-clipboard2-plus"></i></button>
                                <button id="btnEditCap" class="btn btn-info d-none" type="submit">Actualizar Capítulo <i class="bi bi-cloud-upload"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php elseif ($operation === 'deleteStory'): ?>
                <form name="fDeleteStory" id="fDeleteStory" role="form" class="container" enctype="multipart/form-data" method="POST" novalidate>
                    <div class="row row-cols-1 row-cols-md-4 g-4 text-center container">
                        <?php if (!empty($data['autorStory'])) : ?>
                            <?php foreach ($data['autorStory'] as $story) : ?>
                                <div class="col">
                                    <div class="card h-100">
                                        <h5 class="card-title"><b><?php echo htmlspecialchars($story['Titulo']); ?></b></h5>

                                        <?php if ($story['Imagen']) : ?>
                                            <img src='data:image/jpg;base64,<?php echo htmlspecialchars($story['Imagen']); ?>' class="card-img-top img-thumbnail" alt="Portada de la historia.">
                                        <?php else: ?>
                                            <img src="./img/sinImagen.png" class="card-img-top img-thumbnail" alt="No hay imagen disponible.">
                                        <?php endif; ?>

                                        <div class="card-body">
                                            <input type="hidden" name="idHistoria" id="idHistoria" value="<?php echo $story['IdHistoria']; ?>">
                                            <button type="submit" class="btn btnDelete" data-id="<?php echo $story['IdHistoria']; ?>">Eliminar Historia <i class="bi bi-trash3"></i></button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="alert alert-danger alert-dismissible fade show center" role="alert">
                                <span>No tienes Historias para eliminar.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="confirmModalLabel">Confirmar envío</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas enviar el formulario?
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
                    <h5 class="modal-title w-100 text-center" id="successModalLabel">Éxito</h5>
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

    <script src="./js/story.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>