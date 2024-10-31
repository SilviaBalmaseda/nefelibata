document.addEventListener("DOMContentLoaded", function () {
  // console.log("main.js cargado");

  // Obtener los formularios directamente.
  const fIniciarSesion = document.getElementById("fIniciarSesion");
  const fRegistrar = document.getElementById("fRegistrar");
  // Seleccionar todos los botones de favorito.
  const buttonsFavoritos = document.querySelectorAll(".btn-favorito");
  // Seleccionar todos los botones de sinopsis.
  const buttonsSinopsis = document.querySelectorAll(".btn-sinopsis");
  // Seleccionamos todos los botones de leer.
  const leerButtons = document.querySelectorAll(".btn-leer");

  let formData;

  if (fRegistrar) {
    fRegistrar.addEventListener("submit", function (event) {
      event.preventDefault();
      formData = new FormData(fRegistrar);
      formData.append("formType", "registrar");
      submitForm(fRegistrar);
    });
  }

  if (fIniciarSesion) {
    fIniciarSesion.addEventListener("submit", function (event) {
      event.preventDefault();
      formData = new FormData(fIniciarSesion);
      formData.append("formType", "iniciarSesion");
      submitForm(fIniciarSesion);
    });
  }

  // Añadir un listener a cada botón(Favorito).
  buttonsFavoritos.forEach((button) => {
    button.addEventListener("click", function () {
      // Obtener el id de la historia y si es favorito o no
      const historiaId = this.getAttribute("data-id");
      const esFavorito = this.getAttribute("data-es-favorito") === "true";

      // Crear un objeto FormData para enviar los datos
      const formData = new FormData();
      formData.append("formType", "favorito");
      formData.append("historiaId", historiaId);
      formData.append("esFavorito", esFavorito);

      // Realizar la solicitud para cambiar el estado de favorito
      fetch("index.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            return response.text().then((text) => {
              throw new Error(text);
            });
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            // Actualizar visualmente el botón de favoritos
            const icon = this.querySelector("i");
            const numFavorito = parseInt(this.textContent.trim());

            if (esFavorito) {
              // Si era favorito, ahora no lo es: cambiar icono y restar uno
              icon.classList.remove("bi-star-fill");
              icon.classList.add("bi-star");
              this.setAttribute("data-es-favorito", "false");
              this.innerHTML = `<i class="bi bi-star"></i> ${numFavorito - 1}`;
            } else {
              // Si no era favorito, ahora lo es: cambiar icono y sumar uno
              icon.classList.remove("bi-star");
              icon.classList.add("bi-star-fill");
              this.setAttribute("data-es-favorito", "true");
              this.innerHTML = `<i class="bi bi-star-fill"></i> ${
                numFavorito + 1
              }`;
            }
          } else {
            // Mostrar errores del servidor.
            console.error("Error al cambiar el favorito:", data.errors);
          }
        })
        .catch((error) => {
          console.error("Error capturado:", error);
        });
    });
  });

  // Añadir un listener a cada botón(Sinopsis).
  buttonsSinopsis.forEach((button) => {
    button.addEventListener("click", function () {
      // Obtener el id de la historia(para devolver el estado y género).
      const historiaId = this.getAttribute("data-id");
      const sinopsis = this.getAttribute("data-sinopsis");

      // Título del modal.
      document.getElementById("messageModalLabel").textContent =
        "Sinopsis de la historia";

      // Inserta el estado, género y la sinopsis en el contenido del modal
      let sinopsisContent = document.getElementById("sinopsisContent");

      sinopsis
        ? (sinopsisContent.textContent = "Sinopsis: " + sinopsis)
        : (sinopsisContent.textContent = "Esta historia no tiene Sinopsis.");

      formData = new FormData();
      formData.append("formType", "returnStatuGen");
      formData.append("historiaId", historiaId);

      fetch("index.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            return response.text().then((text) => {
              throw new Error(text);
            });
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            let estadoContent = document.getElementById("estadoContent");
            let generoContent = document.getElementById("generoContent");

            let estado = data.estado;
            let gens = data.genero.join(", ");

            estado
              ? (estadoContent.textContent = "Estado: " + estado)
              : (estadoContent.textContent = "No se encontro el Estado.");

            gens
              ? (generoContent.textContent = "Géneros: " + gens)
              : (generoContent.textContent = "No se encontraron los géneros.");
          } else {
            // Mostrar los errores del servidor sin sobrescribir los del cliente
            console.log(data.message);
          }
        })
        .catch((error) => {
          console.error("Error capturado:", error);
        });

      const messageModal = new bootstrap.Modal(
        document.getElementById("messageModal")
      );

      // Se muestra el modal.
      messageModal.show();
    });
  });

  // Añadir un evento de click a cada botón(Leer historia).
  leerButtons.forEach(function (button) {
    button.addEventListener("click", function () {
      // Obtener el título de la historia desde el atributo data.
      const storyTitle = this.getAttribute("data-title");

      // Le cambiamos los espacios por los guiones.
      const titulo = storyTitle.trim().replaceAll(" ", "-");

      // Redirigir a la página de lectura de la historia con el título seleccionado(capítulo por defecto).
      // window.location.href = `index.php?action=leer&titulo=${titulo}&capitulo=1`;
      window.location.href = `index.php?action=leer&titulo=${titulo}`;
    });
  });

  // Función para manejar el submit(registrar o iniciarSesion).
  function submitForm(form) {
    clearValidationMessages(form);
    clearValidationStyles(form);

    const formData = new FormData(form);
    formData.append(
      "formType",
      form.id === "fRegistrar" ? "registrar" : "iniciarSesion"
    );

    // Validación del cliente.
    let clientErrors = validateClientForm(form);

    // Enviar los datos al servidor.
    fetch("index.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          return response.text().then((text) => {
            throw new Error(text);
          });
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          if (data.redirect) {
            // Redirigir si hay una URL de redirección.
            window.location.href = data.redirect;
          }
        } else {
          // Mostrar los errores del servidor sin sobrescribir los del cliente.
          displayErrors(clientErrors, data.errors);
        }
      })
      .catch((error) => {
        console.error("Error capturado:", error);
      });
  }

  // Función para validar los datos en cliente y servidor.
  function validateClientForm(form) {
    let clientErrors = {};

    // Seleccionar todos los input.
    const fields = form.querySelectorAll("input");

    fields.forEach((field) => {
      const errorElement = document.getElementById(
        "error" + capitalize(field.id)
      );

      // validar los datos.
      if (field.hasAttribute("required") && !field.value.trim()) {
        errorElement.textContent = `El campo ${field.placeholder} es obligatorio.`;
        field.classList.add("input-error");
        clientErrors[field.id] = errorElement.textContent;
      } else if (
        field.type === "email" &&
        field.value.trim() &&
        !validateEmail(field.value.trim())
      ) {
        errorElement.textContent = "El formato del email es incorrecto.";
        field.classList.add("input-error");
        clientErrors[field.id] = errorElement.textContent;
      } else {
        field.classList.add("input-success");
      }
    });

    return clientErrors;
  }

  // Mostrar los errores del cliente y servidor.
  function displayErrors(clientErrors, serverErrors) {
    // Mostrar los errores del cliente.
    Object.keys(clientErrors).forEach((key) => {
      const errorElement = document.getElementById("error" + capitalize(key));
      if (errorElement) {
        errorElement.textContent = clientErrors[key];
        document.getElementById(key).classList.add("input-error");
      }
    });

    // Mostrar los errores del servidor.
    Object.keys(serverErrors).forEach((key) => {
      const errorElement = document.getElementById("error" + capitalize(key));
      const inputElement = document.getElementById(key);

      if (errorElement) {
        if (!clientErrors[key]) {
          // Mostrar el error del servidor solo si no hay errores del cliente para ese campo.
          errorElement.textContent = serverErrors[key];

          if (inputElement !== null) {
            if (inputElement.classList.contains("input-success")) {
              inputElement.classList.remove("input-success");
              inputElement.classList.add("input-error");
            }
          }
        }
      }
    });
  }

  // Limpia los mensajes de error.
  function clearValidationMessages(form) {
    const errors = form.querySelectorAll(".error-message");
    errors.forEach((error) => {
      error.textContent = "";
    });
  }

  // Limpia los estilos.
  function clearValidationStyles(form) {
    const fields = form.querySelectorAll("input, textarea, select");
    fields.forEach((field) => {
      field.classList.remove("input-error", "input-success");
    });
  }

  // Devuelve la primera letra en mayúsculas.
  function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  // valida si el string pasado es formato Email.
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
  }
});
