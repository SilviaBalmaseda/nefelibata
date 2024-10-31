<header class="header">
    <nav id="navbar" class="navbar navbar-expand-lg">
        <div class="container-fluid barraNav">
            <div class="bloque1" id="bloque1">
                <a class="navbar-brand" href="index.php">
                    <img src="./img/1.png" class="img-fluid peque" alt="icono Negelibata" />
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <div class="nav-item bloque2" id="bloque2">
                        <form name="fSearchStory" role="search" class="d-flex" action="index.php" method="GET" novalidate>
                            <input name="action" type="hidden" value="fSearchStory"> <!-- Para que llame la función en el controlador -->
                            <input name="searchStory" class="form-control me-2" type="search" placeholder="Buscar" aria-label="Search" required>
                            <button type="submit" class="btn input-group-prepend" id="btnSearch">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </button>
                        </form>
                    </div>

                    <?php if ($user != null): ?>
                        <div class="nav-item bloque3" id="bloque3">
                            <a class="nav-link" href="index.php?action=crearHistoria&operation=createStory">Crear Historia</a>
                        </div>
                        <?php if ($user === 'admin'): ?>
                            <div class="nav-item bloque4" id="bloque4">
                                <a class="nav-link" href="index.php?action=admin">Admin</a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="bloque5" id="bloque5">
                <div id="sesion">
                    <?php if ($user != null): ?>
                        <a class="nav-link" href="index.php?action=ajustes">Ajustes</a>
                    <?php else: ?>
                        <a class="nav-link" href="index.php?action=iniciarSesion">Iniciar Sesión</a>
                        <a class="nav-link" href="index.php?action=registrar">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>