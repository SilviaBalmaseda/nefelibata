// console.log("util cargado");

// Función para obtener una cookie por su nombre.
function getCookie(name) {
  const nameEQ = name + "=";
  const ca = document.cookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}

// Función para establecer una cookie.
function setCookie(name, value, days) {
  const d = new Date();
  d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toUTCString();
  document.cookie = name + "=" + value + ";" + expires + ";path=/";
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
    .querySelectorAll("input, textarea, select")
    .forEach((field) => field.classList.remove("input-error", "input-success"));
}

// Devuelve la primera letra en mayúsculas del string pasado.
function capitalize(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}
