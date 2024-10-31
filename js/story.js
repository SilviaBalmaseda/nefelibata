document.addEventListener("DOMContentLoaded", function () {
  // console.log("story.js cargado");

  // Obtener los formularios directamente.
  const fCreateStory = document.getElementById("fCreateStory");
  const fEditStory = document.getElementById("fEditStory");
  const fDeleteStory = document.getElementById("fDeleteStory");

  const confirmModal = new bootstrap.Modal(
    document.getElementById("confirmModal")
  );
  const successModal = new bootstrap.Modal(
    document.getElementById("successModal")
  );

  const resultMessage = document.getElementById("resultMessage");

  let currentForm;
  let formData;

  // Formulario para crear la historia.
  if (fCreateStory) {
    fCreateStory.addEventListener("submit", function (event) {
      event.preventDefault();
      handleSubmit(fCreateStory, "validateCreate", "createStory");
    });
  }

  // Formulario para eliminar la historia.
  if (fDeleteStory) {
    const deleteButtons = document.querySelectorAll(".btnDelete");

    deleteButtons.forEach((button) => {
      button.addEventListener("click", function (event) {
        event.preventDefault();

        const idHistoria = this.getAttribute("data-id");

        // Mostrar el modal de confirmación.
        confirmModal.show();

        // Limpiar el evento anterior del botón de confirmación.
        const confirmSubmitButton = document.getElementById("confirmSubmit");
        confirmSubmitButton.replaceWith(confirmSubmitButton.cloneNode(true));

        // Confirmar eliminación en el modal.
        document.getElementById("confirmSubmit").addEventListener(
          "click",
          function () {
            confirmModal.hide();

            const formData = new FormData();
            formData.append("idHistoria", idHistoria);
            formData.append("formType", "deleteStory");

            // Hacemos el fetch para eliminar la historia
            fetch("index.php", {
              method: "POST",
              body: formData,
            })
              .then((response) => response.json()) // Parseamos directamente el JSON
              .then((data) => {
                if (data.success) {
                  resultMessage.textContent =
                    data.message || "Operación exitosa.";
                  successModal.show();

                  // Redirigir después del éxito
                  document
                    .getElementById("closeSubmit")
                    .addEventListener("click", function () {
                      if (data.redirect) {
                        window.location.href = data.redirect;
                      }
                    });
                } else {
                  resultMessage.textContent =
                    data.message || "Error en la operación.";
                  successModal.show();
                }
              })
              .catch((error) => {
                console.error("Error capturado:", error);
              });
          },
          { once: true }
        ); // Asegurarnos de que solo escuchemos el clic una vez
      });
    });
  }

  // Formulario de edición.
  if (fEditStory) {
    // Desplegable de capítulos
    const capituloSelect = document.getElementById("capituloSelect");

    // Seleccionamos los botones de editar(para seleccionar la Historia).
    document.querySelectorAll(".btnEditStorys").forEach((button) => {
      button.addEventListener("click", function () {
        const idHistoria = this.getAttribute("data-id"); // id de la historia seleccionada.

        const formData = new FormData();
        formData.append("formType", "searchDataStory"); // Enviar el tipo de formulario (acción)
        formData.append("idHistoria", idHistoria); // Enviar el id de la historia a editar

        // Hacer fetch utilizando POST
        fetch("index.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Asignar los valores a los inputs de texto y textarea
              document.getElementById("titulo").value = data.Titulo;
              document.getElementById("sinopsis").value = data.Sinopsis;
              document.getElementById("imagen").src =
                "data:image/jpg;base64," + data.Imagen;

              // Seleccionar el estado actual
              document.getElementById("estado").value = data.EstadoId;

              // Todos los géneros.
              const generoSelect = document.getElementById("generoSelect");
              // Extraer solo los IDs de los géneros seleccionados.
              const generosSeleccionados = data.Generos.map(
                (genero) => genero.GeneroId
              );

              // Recorrer las opciones del select y marcar las seleccionadas.
              for (let option of generoSelect.options) {
                if (generosSeleccionados.includes(Number(option.value))) {
                  option.selected = true; // Marcar la opción como seleccionada.
                } else {
                  option.selected = false; // Asegurarse de que las no seleccionadas no estén marcadas.
                }
              }

              // Mostrar el formulario (suponiendo que esté inicialmente oculto).
              if (
                deleteClass(
                  document.getElementById("fEditStoryContainer"),
                  "d-none"
                )
              )
                document.getElementById("divStorys").classList.add("d-none");

              // Para el desplegable con los Capítulos.
              data.Capitulos.forEach((capitulo) => {
                const option = document.createElement("option");
                option.value = capitulo.IdCapitulo; // Aquí asumo que IdCapitulo es el identificador
                option.textContent = `Capítulo ${capitulo.NumCapitulo}: ${capitulo.TituloCap}`; // Mostrar el título del capítulo
                capituloSelect.appendChild(option);
              });

              // Para guardar el id de la historia seleccionada.
              document.getElementById("hiddenId").value = idHistoria;
            } else {
              alert("ERROR: " + data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      });
    });

    // Mostrar el desplegable de Capítulos.
    document
      .getElementById("btnEditarCapitulo")
      .addEventListener("click", function () {
        if (deleteClass(document.getElementById("sectionCap"), "d-none"))
          document.getElementById("btnEditarCapitulo").classList.add("d-none");
      });

    // Mostrar el formulario del Capítulo(Crear).
    document
      .getElementById("btnCreateCapitulo")
      .addEventListener("click", function () {
        clearFields("fEditCap");

        // Mostrar el formulario(div).
        deleteClass(document.getElementById("fActionCap"), "d-none");
        // Mostrar botón para Crear capítulo y ocultar el de Editar.
        deleteClass(document.getElementById("btnAddCap"), "d-none");
        document.getElementById("btnEditCap").classList.add("d-none");
      });

    // Crear Capítulo.
    document
      .getElementById("btnAddCap")
      .addEventListener("click", function (event) {
        event.preventDefault();

        let historia = document.getElementById("historia");

        if (validateClient([historia])) return;

        const confirmButton = document.getElementById("confirmSubmit");
        confirmButton.replaceWith(confirmButton.cloneNode(true));

        let historiaId = document.getElementById("hiddenId").value;

        let formData = new FormData();
        formData.append("formType", "validateCreateCap");
        formData.append("historia", historia.value);
        formData.append("historiaId", historiaId);

        // Validar lado Servidor.
        validateServer(formData);

        formData.append("formType", "createCap");
        formData.append(
          "tituloCap",
          document.getElementById("tituloCap").value
        );

        // Realizar la creación(si acepta el modal).
        confirmarModal(formData);
      });

    // Mostrar el formulario del Capítulo(Editar).
    document
      .getElementById("btnEditCapitulo")
      .addEventListener("click", function () {
        // Limpiar los campos y errores.
        clearElements("sectionCap");

        let selectCap = document.getElementById("capituloSelect");

        // Validar lado Cliente.
        if (validateClient([selectCap])) return;

        // Mostrar el formulario(para editar).
        deleteClass(document.getElementById("fActionCap"), "d-none");

        // Para que se muestre la información del capítulo seleccionado.
        let formData = new FormData();
        formData.append("formType", "searchDataCap");
        formData.append("idCapitulo", selectCap.value);

        fetch("index.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Asignar los valores.
              document.getElementById("tituloCap").value = data.TituloCap;
              document.getElementById("historia").value = data.Historia;
            } else {
              alert("Error: " + data.message);
            }
          })
          .catch((error) => console.error("Error:", error));

        // Mostrar botón para Editar capítulo y ocultar el de crear.
        deleteClass(document.getElementById("btnEditCap"), "d-none");
        document.getElementById("btnAddCap").classList.add("d-none");
      });

    // Editar Capítulo.
    document
      .getElementById("btnEditCap")
      .addEventListener("click", function (event) {
        event.preventDefault();
        let selectCap = document.getElementById("capituloSelect");
        let historia = document.getElementById("historia");

        // Validar lado Cliente.
        if (validateClient([selectCap, historia])) return;

        // Elimina posibles listeners anteriores para evitar duplicados
        const confirmButton = document.getElementById("confirmSubmit");
        confirmButton.replaceWith(confirmButton.cloneNode(true));

        let formData = new FormData();
        formData.append("formType", "validateEditCap");
        formData.append("idCapitulo", selectCap.value);
        formData.append("historia", historia.value);

        // Validar lado Servidor.
        validateServer(formData);

        formData.append("formType", "editCap");
        formData.append(
          "tituloCap",
          document.getElementById("tituloCap").value
        );

        // Realizar la edición(si acepta el modal.)
        confirmarModal(formData);
      });

    // Eliminar Capítulo.
    document
      .getElementById("btnDeleteCap")
      .addEventListener("click", function (event) {
        event.preventDefault();
        // Limpiar los campos y errores.
        clearElements("sectionCap");

        let selectCap = document.getElementById("capituloSelect");

        // Validar lado Cliente.
        if (validateClient([selectCap])) return;

        // Elimina posibles listeners anteriores para evitar duplicados
        const confirmButton = document.getElementById("confirmSubmit");
        confirmButton.replaceWith(confirmButton.cloneNode(true));

        let formData = new FormData();
        formData.append("formType", "validateDeleteCap");
        formData.append("idCapitulo", selectCap.value);

        // Validar lado Servidor.
        validateServer(formData);

        formData.append("formType", "deleteCap");

        // Realizar la edición(si acepta el modal.)
        confirmarModal(formData);
      });

    // Validar datos en el lado Cliente.
    function validateClient(elementos) {
      let hasError = false;

      elementos.forEach((elemento) => {
        const errorElement = document.getElementById(
          "error" + capitalize(elemento.id)
        );
        if (elemento.type === "text" && !elemento.value.trim()) {
          errorElement.textContent = `El campo ${elemento.placeholder} es obligatorio.`;
          elemento.classList.add("input-error");
          hasError = true;
        } else if (elemento.type === "textarea" && !elemento.value.trim()) {
          errorElement.textContent = `El campo ${elemento.placeholder} es obligatorio.`;
          elemento.classList.add("input-error");
          hasError = true;
        } else if (elemento.type === "select-one" && !elemento.value) {
          errorElement.textContent = `Debes seleccionar al menos una opción.`;
          elemento.classList.add("input-error");
          hasError = true;
        } else {
          elemento.classList.add("input-success");
        }
      });

      return hasError;
    }

    // Validar datos en el lado Servidor.
    function validateServer(formData) {
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
          // console.log(response.text());

          return response.json();
        })
        .then((data) => {
          if (data.success) {
            confirmModal.show();
          } else {
            console.log(data);

            Object.keys(data.errors).forEach((error) => {
              const errorField = document.querySelector(`[id=${error}]`);
              if (errorField) {
                const errorElement = document.getElementById(
                  "error" + capitalize(errorField.id)
                );
                errorElement.textContent = data.errors[error];
                errorField.classList.add("input-error");
              }
            });
          }
        })
        .catch((error) => {
          console.error("Error capturado:", error);
        });
    }

    // Realizar la acción si acepta el modal de confirmación.
    function confirmarModal(formData) {
      document
        .getElementById("confirmSubmit")
        .addEventListener("click", function () {
          confirmModal.hide();

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

    // Limpiar los campos.
    function clearFields(element) {
      document
        .getElementById(element)
        .querySelectorAll("input, textarea")
        .forEach((field) => (field.value = ""));
    }

    // Limpiar los campos de errores.
    function clearElements(element) {
      let clear = document.getElementById(element);
      clear
        .querySelectorAll(".error-message")
        .forEach((error) => (error.textContent = ""));

      clear
        .querySelectorAll("input, select")
        .forEach((field) =>
          field.classList.remove("input-error", "input-success")
        );
    }

    // Devuelve true o false si se la eliminado bien la clase pasada del elemento pasado.
    function deleteClass(element, clase) {
      if (element.classList.contains(clase)) {
        element.classList.remove(clase);
        return true;
      }
      return false;
    }

    // Actualizar los datos generales de la historia.
    document
      .getElementById("btnUpdateHistoria")
      .addEventListener("click", function (event) {
        event.preventDefault();
        // Formulario de edición de la Historia.
        handleSubmit(
          document.getElementById("fEditStory"),
          "validateEditStory",
          "editStory",
          document.getElementById("hiddenId").value
        );
      });
  }

  // Para validar los datos y realizar la acción.
  function handleSubmit(form, formTypeValidate, formTypeSubmit, idHistoria) {
    clearValidationMessages(form);
    clearValidationStyles(form);

    currentForm = form;
    formData = new FormData(form);
    if (idHistoria) formData.append("idHistoria", idHistoria);
    formData.append("formType", formTypeValidate);

    // Validación del formulario.
    validateClientForm(form);

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
          .then((response) => {
            if (!response.ok) {
              throw new Error("Error en la respuesta del servidor");
            }
            return response.json();
          })
          .then((data) => {
            if (data.success) {
              resultMessage.textContent = data.message || "Operación exitosa.";
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
          })
          .catch((error) => {
            console.error("Error capturado:", error);
          });
      });
  }

  // Función para validar los datos en cliente y servidor.
  function validateClientForm(form) {
    let clientErrors = {};

    // Seleccionar solo los input requeridos.
    const fields = form.querySelectorAll("input[required], textarea[required]");

    fields.forEach((field) => {
      const errorElement = document.getElementById(
        "error" + capitalize(field.id)
      );

      // validar los datos.
      if (field.type === "text" && !field.value.trim()) {
        errorElement.textContent = `El campo ${field.placeholder} es obligatorio.`;
        field.classList.add("input-error");
        clientErrors[field.id] = errorElement.textContent;
      } else if (field.type === "textarea" && !field.value.trim()) {
        errorElement.textContent = `Tienes que introducir la historia.`;
        field.classList.add("input-error");
        clientErrors[field.id] = errorElement.textContent;
      } else {
        field.classList.add("input-success");
      }
    });

    // Servidor
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
        if (data.success) {
          confirmModal.show();
        } else {
          displayErrors(clientErrors, data.errors);
        }
      })
      .catch((error) => {
        console.error("Error capturado:", error);
      });
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

  // Limpiar mensajes de errores.
  function clearValidationMessages(form) {
    form
      .querySelectorAll(".error-message")
      .forEach((error) => (error.textContent = ""));
  }

  // Limpiar estilos.
  function clearValidationStyles(form) {
    form
      .querySelectorAll("input, select")
      .forEach((field) =>
        field.classList.remove("input-error", "input-success")
      );
  }

  // Devuelve la primera letra en mayúsculas.
  function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
});
