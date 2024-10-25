<?php
function showNavbar($user = null)
{
    if (file_exists(__DIR__ . '/navbar.php')) {
        include __DIR__  . '/navbar.php';
    } else {
        echo '<p>Error: El archivo de navbar no se encontró.</p>';
        exit; // Detener la ejecución si el archivo no se encuentra.
    }
}

function showFooter()
{
    if (file_exists(__DIR__ . '/footer.php')) {
        include __DIR__  . '/footer.php';
    } else {
        echo '<p>Error: El archivo de footer no se encontró.</p>';
        exit; // Detener la ejecución si el archivo no se encuentra.
    }
}