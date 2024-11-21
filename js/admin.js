document.addEventListener("DOMContentLoaded", function () {
  // console.log("admin.js cargado");

  // Obtener los formularios directamente.
  const fCreateGenre = document.getElementById("fCreateGenre");
  const fCreateStatus = document.getElementById("fCreateStatus");
  const fDeleteUser = document.getElementById("fDeleteUser");
  const btnBuscarUser = document.getElementById("btnBuscarUser");
  const btnDeleteUser = document.getElementById("btnDeleteUser");
  const fDeleteGenero = document.getElementById("fDeleteGenero");
  const fDeleteStatus = document.getElementById("fDeleteStatus");
  const fDeleteHistoria = document.getElementById("fDeleteHistoria");
  const btnBuscarHistoria = document.getElementById("btnBuscarHistoria");
  const btnDeleteHistoria = document.getElementById("btnDeleteHistoria");

  const confirmModal = new bootstrap.Modal(
    document.getElementById("confirmModal")
  );
  const successModal = new bootstrap.Modal(
    document.getElementById("successModal")
  );
  // Para mostrar el mensaeje en el modal.
  const resultMessage = document.getElementById("resultMessage");

  let currentForm;
  let formData;

  if (fCreateGenre) {
    fCreateGenre.addEventListener("submit", function (event) {
      event.preventDefault();
      handleSubmit(fCreateGenre, "validateGenre", "createGenre");
    });
  }

  if (fCreateStatus) {
    fCreateStatus.addEventListener("submit", function (event) {
      event.preventDefault();
      handleSubmit(fCreateStatus, "validateStatus", "createStatus");
    });
  }

  if (btnBuscarUser) {
    btnBuscarUser.addEventListener("click", function (event) {
      event.preventDefault();
      const fDeleteUser = document.getElementById("fDeleteUser");
      if (fDeleteUser) handleSearch(fDeleteUser, "searchUser");
    });
  }

  if (btnDeleteUser) {
    fDeleteUser.addEventListener("submit", function (event) {
      event.preventDefault();
      handleSubmit(fDeleteUser, "validateDeleteUser", "deleteUser");
    });
  }

  if (fDeleteGenero) {
    fDeleteGenero.addEventListener("submit", function (event) {
      event.preventDefault();
      handleSubmit(fDeleteGenero, "validateDeleteGenre", "deleteGenero");
    });
  }

  if (fDeleteStatus) {
    fDeleteStatus.addEventListener("submit", function (event) {
      event.preventDefault();
      handleSubmit(fDeleteStatus, "validateDeleteStatus", "deleteStatus");
    });
  }

  if (btnBuscarHistoria) {
    btnBuscarHistoria.addEventListener("click", function (event) {
      event.preventDefault();
      const fDeleteHistoria = document.getElementById("fDeleteHistoria");
      if (fDeleteHistoria) handleSearch(fDeleteHistoria, "searchStory");
    });
  }

  if (btnDeleteHistoria) {
    fDeleteHistoria.addEventListener("submit", function (event) {
      event.preventDefault();
      handleSubmit(fDeleteHistoria, "validateDeleteStory", "deleteStoryAdmin");
    });
  }

  // Función para manejar el submit(crear o eliminar).
  function handleSubmit(form, formTypeValidate, formTypeSubmit) {
    currentForm = form;
    formData = new FormData(form);
    formData.append("formType", formTypeValidate);

    // Validación del formulario.
    validateForm(form);

    // Elimina posibles listeners anteriores para evitar duplicados.
    const confirmButton = document.getElementById("confirmSubmit");
    confirmButton.replaceWith(confirmButton.cloneNode(true));

    // Mostrar modal de como ha ido la operación.
    document
      .getElementById("confirmSubmit")
      .addEventListener("click", function () {
        confirmModal.hide();
        formData.append("formType", formTypeSubmit);

        fetch("index.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.text())
          .then((text) => {
            try {
              const data = JSON.parse(text);
              if (data.success) {
                resultMessage.textContent =
                  data.message || "Operación exitosa.";
                successModal.show();
                currentForm.reset();

                document
                  .getElementById("closeSubmit")
                  .addEventListener("click", function () {
                    // Redirigir si hay una URL de redirección.
                    if (data.redirect) {
                      window.location.href = data.redirect;
                    }
                  });
              } else {
                resultMessage.textContent =
                  data.message || "Error en la operación.";
                successModal.show();
              }
            } catch (error) {
              console.error("Error: Respuesta no JSON", text);
            }
          })
          .catch((error) => {
            console.error("Error capturado:", error);
          });
      });
  }

  // Función para validar los datos en JavaScript con modal.
  function validateForm(form) {
    clearValidationMessages(form);
    clearValidationStyles(form);

    let hasError = false;
    const fields = form.querySelectorAll("input, select"); // Seleccionar todos los inputs y selects.

    fields.forEach((field) => {
      const errorElement = document.getElementById(
        "error" + capitalize(field.id)
      );
      // Validar datos.
      if (field.type === "text" && !field.value.trim()) {
        errorElement.textContent = `El campo ${field.placeholder} es obligatorio.`;
        field.classList.add("input-error");
        hasError = true;
      } else if (field.type === "select-multiple" && !field.value) {
        errorElement.textContent = `Debes seleccionar al menos una opción.`;
        field.classList.add("input-error");
        hasError = true;
      } else {
        field.classList.add("input-success");
      }
    });

    // Si hay algún error parar.
    if (hasError) return;

    fetch("index.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          return response.json().then((data) => {
            throw new Error(data.errors || "Error inesperado del servidor");
          });
        }
        return response.json();
      })
      .then((data) => {
        // Si no hay errores en el servidor mostrar modal, si hay mostrar errores.
        if (data.success) {
          confirmModal.show();
        } else {
          Object.keys(data.errors).forEach((error) => {
            const errorField = document.querySelector(`[id=${error}]`);
            if (errorField) {
              const errorElement = document.getElementById(
                "error" + capitalize(errorField.id)
              );
              errorElement.textContent = data.errors[error];
              // Si hay estilo de success eliminar lo y añadir el de error.
              deleteClass(errorField, "input-success");
              errorField.classList.add("input-error");
            }
          });
        }
      })
      .catch((error) => {
        console.error("Error capturado:", error);
      });
  }

  // Función para manejar las busquedas.
  function handleSearch(form, formTypeSubmit) {
    currentForm = form;
    const formData = new FormData(form);
    formData.append("formType", formTypeSubmit);

    // Validación en JavaScript.
    if (validateSearch(form)) return;

    // Enviar solicitud POST para búsqueda.
    fetch("index.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          // Si la respuesta no es exitosa, manejar el error.
          return response.json().then((data) => {
            throw new Error(data.errors || "Error inesperado del servidor");
          });
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          // Si la búsqueda es exitosa.
          displaySearch(form, data.dropdown);
        } else {
          // Mostrar errores del servidor si existen.
          displayErrors(data.errors);
        }
      })
      .catch((error) => {
        console.error("Error capturado:", error);
      });
  }

  // Función para validar los datos en JavaScript para las búsquedas.
  function validateSearch(form) {
    clearValidationMessages(form);
    clearValidationStyles(form);

    let hasError = false;
    const fields = form.querySelectorAll("input"); // Seleccionar todos los inputs.

    fields.forEach((field) => {
      const errorElement = document.getElementById(
        "error" + capitalize(field.id)
      );
      // Validar datos.
      if (field.type === "text" && !field.value.trim()) {
        errorElement.textContent = `El campo ${field.placeholder} es obligatorio.`;
        field.classList.add("input-error");
        hasError = true;
      } else {
        field.classList.add("input-success");
      }
    });

    return hasError;
  }

  // Función para mostrar el desplegable y el botón(eliminar usuarios o historias).
  function displaySearch(form, dropdown) {
    const field = form.querySelector("input");
    let search = "search" + capitalize(field.id);
    const searchResults = document.getElementById(search);

    // Para que se pueda ver el desplegable y el botón.
    let fields = document.querySelectorAll("." + search);

    // Personalizar los elementos según el tipo(usuarios o historias).
    let label = "";
    let selectName = "";
    let errorId = "";

    if (field.id === "nameDelUser") {
      label = "usuario(s)";
      selectName = "selecDelUsuario";
      errorId = "errorSelecDelUsuario";
    } else if (field.id === "nameDelHistoria") {
      label = "historia(s)";
      selectName = "selecDelHistoria";
      errorId = "errorSelecDelHistoria";
    }

    searchResults.innerHTML = ""; // Limpiar cualquier resultado previo.

    let deleteDropdown = `<label class="formuLabel" for="${selectName}">Seleccione ${label}: </label>
      <select name="${selectName}[]" class="form-select" id="${selectName}" aria-describedby="${selectName}" multiple>`;

    // Rellenar el desplegable con los usuarios encontrados.
    dropdown.forEach((elements) => {
      deleteDropdown += `<option value="${elements.Id}">`;
      field.id === "nameDelUser"
        ? (deleteDropdown += `${elements.Nombre}`)
        : (deleteDropdown += `'${elements.Titulo}' - '${elements.Nombre}'`);
      deleteDropdown += `</option>`;
    });

    deleteDropdown += `</select>
      <p id="${errorId}" class="error-message"></p>`;

    // Mostrar el desplegable y el botón.
    fields.forEach((field) => {
      field.style.display = "inline-block";
    });

    // Insertar lo en la página.
    searchResults.insertAdjacentHTML("beforeend", deleteDropdown);
  }

  // Función para mostrar los errores del servidor.
  function displayErrors(errors) {
    Object.keys(errors).forEach((error) => {
      const errorField = document.getElementById(error);
      if (errorField) {
        const errorElement = document.getElementById(
          "error" + capitalize(errorField.id)
        );
        let dropdown = document.getElementById(
          "search" + capitalize(errorField.id)
        );

        // Si está el estilo sin errores(verde), quitar lo.
        deleteClass(errorField, "input-success");

        // Ocultar el botón y eliminar el desplegable.
        if (dropdown) {
          let fields = document.querySelectorAll(
            ".search" + capitalize(errorField.id)
          );

          dropdown.innerHTML = ""; // Borrar el desplegable.

          fields.forEach((field) => {
            field.style.display = "none";
          });
        }
        errorElement.textContent = errors[error];
        errorField.classList.add("input-error");
      }
    });
  }
});
