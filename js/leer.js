document.addEventListener("DOMContentLoaded", function () {
  // console.log("leer.js cargado");

  // Seleccionar el botón de capítulos y los elementos de la lista del desplegable.
  const dropdownButton = document.getElementById("dropdownCapitulo");
  const dropdownItems = document.querySelectorAll(".dropdown-item");

  // Seleccionar los botones de navegación.
  const btnAnterior = document.getElementById("btn-anterior");
  const btnSiguiente = document.getElementById("btn-siguiente");

  // Obtener la URL actual y los parámetros.
  const currentUrl = new URL(window.location.href);
  const titulo = currentUrl.searchParams.get("titulo");
  const capituloActual = parseInt(currentUrl.searchParams.get("capitulo"));

  // Función para cambiar el capítulo.
  function changeCap(capitulo) {
    window.location.href = `index.php?action=leer&titulo=${titulo}&capitulo=${capitulo}`;
  }

  // Cambiar de capítulo al seleccionar el desplegable.
  dropdownItems.forEach(function (field) {
    field.addEventListener("click", function (event) {
      event.preventDefault();
      const changeC = parseInt(this.getAttribute("data-num-capitulo"));

      // Cambiar el nombre en el desplegable por el capítulo seleccionado.
      dropdownButton.innerText = "Capítulo " + this.innerText.trim();

      // Redirigir la url.
      changeCap(changeC);
    });
  });

  // Guardar los capítulos disponibles desde los elementos del desplegable.
  const capitulosDisponibles = Array.from(dropdownItems).map(function (item) {
    return parseInt(item.getAttribute("data-num-capitulo"));
  });

  // Dirigirse al capítulo anterior.
  if (btnAnterior && !btnAnterior.classList.contains("disabled")) {
    btnAnterior.addEventListener("click", function () {
      const indexActual = capitulosDisponibles.indexOf(capituloActual);
      if (indexActual > 0) {
        const capituloAnterior = capitulosDisponibles[indexActual - 1];
        changeCap(capituloAnterior);
      }
    });
  }

  // Dirigirse al siguiente capítulo.
  if (btnSiguiente && !btnSiguiente.classList.contains("disabled")) {
    btnSiguiente.addEventListener("click", function () {
      const indexActual = capitulosDisponibles.indexOf(capituloActual);
      if (indexActual < capitulosDisponibles.length - 1) {
        const capituloSiguiente = capitulosDisponibles[indexActual + 1];
        changeCap(capituloSiguiente);
      }
    });
  }
});
