// Variables globales
let editor = null
let editorContent = ""

// Función para inicializar Quill con configuración mejorada
function initializeEditor(content) {
  console.log("Inicializando Quill con contenido:", content)

  const editorContainer = document.getElementById("editor-container")
  if (!editorContainer) {
    console.error("Contenedor del editor no encontrado")
    return
  }

  editorContainer.innerHTML = `
        <div id="toolbar">
            <span class="ql-formats">
                <select class="ql-header">
                    <option value="">Normal</option>
                    <option value="1">Título 1</option>
                    <option value="2">Título 2</option>
                </select>
                <select class="ql-font">
                    <option selected>Default</option>
                    <option value="serif">Serif</option>
                    <option value="monospace">Monospace</option>
                </select>
            </span>
            <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
            </span>
            <span class="ql-formats">
                <select class="ql-color"></select>
                <select class="ql-background"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
                <select class="ql-align">
                    <option selected></option>
                    <option value="center"></option>
                    <option value="right"></option>
                    <option value="justify"></option>
                </select>
            </span>
            <span class="ql-formats">
                <button class="ql-link"></button>
                <button class="ql-clean"></button>
            </span>
        </div>
        <div id="editor-content" style="min-height: 200px;"></div>
    `

  // Configurar Quill con nuevas opciones
  try {
    editor = new Quill("#editor-content", {
      modules: {
        toolbar: "#toolbar",
        keyboard: {
          bindings: {
            // Configuración personalizada para la tecla Enter
            enter: {
              key: 13,
              handler: function (range) {
                // Obtener el contenido de la línea actual
                const currentLine = this.quill.getLine(range.index)[0]
                const format = currentLine ? currentLine.formats() : {}

                // Insertar nueva línea con viñeta
                this.quill.insertText(range.index, "\n• ", format)

                // Mover el cursor después de la viñeta
                this.quill.setSelection(range.index + 3)

                // Prevenir el comportamiento por defecto
                return false
              },
            },
          },
        },
        history: {
          delay: 2000,
          maxStack: 500,
          userOnly: true,
        },
      },
      theme: "snow",
      placeholder: "Escribe aquí los términos y condiciones...",
      formats: [
        "bold",
        "italic",
        "underline",
        "strike",
        "align",
        "list",
        "bullet",
        "link",
        "color",
        "background",
        "font",
        "header",
      ],
    })

    // Establecer el contenido inicial formateado
    const formattedContent = formatDatabaseContent(content)
    console.log("Contenido formateado:", formattedContent)
    editor.root.innerHTML = formattedContent

    // Agregar evento para manejar el pegado de texto
    editor.root.addEventListener("paste", (e) => {
      e.preventDefault()

      // Obtener el texto plano del portapapeles
      const text = e.clipboardData.getData("text/plain")

      // Formatear el texto pegado con viñetas
      const formattedText = text
        .split("\n")
        .map((line) => line.trim())
        .filter((line) => line)
        .map((line) => (line.startsWith("•") ? line : `• ${line}`))
        .join("\n")

      // Insertar el texto formateado
      const range = editor.getSelection()
      if (range) {
        editor.insertText(range.index, formattedText)
      }
    })
  } catch (error) {
    console.error("Error al inicializar Quill:", error)
    return
  }
}

// Función para formatear el contenido de la base de datos
function formatDatabaseContent(content) {
  if (!content) return ""

  // Limpiamos el contenido inicial
  content = content.trim()

  // Reemplazamos los caracteres especiales HTML
  content = content.replace(/&amp;/g, "&")

  // Dividimos el texto por saltos de línea
  let lines = content.split("\n")

  // Filtramos líneas vacías y procesamos cada línea
  lines = lines.filter((line) => line.trim() !== "")

  // Formateamos cada línea con el bullet y un espacio
  const formattedContent = lines
    .map((line) => {
      line = line.trim()
      // Si la línea ya comienza con una viñeta, la dejamos como está
      if (line.startsWith("•")) {
        return "<p>" + line + "</p>"
      }
      // Si no tiene viñeta, le añadimos una
      return "<p>• " + line + "</p>"
    })
    .join("")

  return formattedContent
}

// Función para obtener el contenido del editor
function getEditorContent() {
  if (!editor) {
    throw new Error("Editor no inicializado correctamente")
  }

  // Obtenemos el HTML del editor
  let content = editor.root.innerHTML

  // Reemplazamos las etiquetas <p> por saltos de línea
  content = content.replace(/<p>/g, "").replace(/<\/p>/g, "\n")

  // Nos aseguramos que cada línea comience con una viñeta
  content = content
    .split("\n")
    .map((line) => {
      line = line.trim()
      if (line && !line.startsWith("•")) {
        line = "• " + line
      }
      return line
    })
    .join("\n")

  return content
}

// Función para destruir el editor
function destroyEditor() {
  if (editor) {
    editor = null
    const editorContainer = document.getElementById("editor-container")
    if (editorContainer) {
      editorContainer.innerHTML = ""
    }
  }
}

// Función para cargar condiciones por defecto
function loadDefaultConditions(callback) {
  $.get(_URL + "/ajs/get/condiciones/default", (data) => {
    try {
      const parsedData = JSON.parse(data)
      if (parsedData && parsedData.length > 0) {
        const condiciones = parsedData[0].nombre
        editorContent = condiciones
        callback(condiciones)
      } else {
        console.warn("No se encontraron condiciones por defecto")
        callback("")
      }
    } catch (e) {
      console.error("Error al parsear condiciones por defecto:", e)
      callback("")
    }
  }).fail(() => {
    console.error("Error al obtener condiciones por defecto")
    callback("")
  })
}

// Función para cargar condiciones de una cotización específica
function loadCotizacionConditions(cotizacionId, callback) {
  const url = _URL + "/ajs/get/condiciones/cotizacion/" + cotizacionId
  $.get(url, (data) => {
    try {
      console.log("Datos recibidos:", data)
      const parsedData = JSON.parse(data)
      if (parsedData && parsedData.length > 0) {
        const condiciones = parsedData[0].condiciones
        console.log("Condiciones encontradas:", condiciones)
        editorContent = condiciones
        callback(condiciones)
      } else {
        console.log("No se encontraron condiciones para esta cotización, usando condiciones por defecto")
        loadDefaultConditions(callback)
      }
    } catch (e) {
      console.error("Error al parsear condiciones:", e)
      callback("")
    }
  }).fail((xhr, status, error) => {
    console.error("Error al obtener condiciones de la cotización:", status, error)
    console.log("Respuesta del servidor:", xhr.responseText)
    callback("")
  })
}

// Función para guardar condiciones temporales
function saveTemporaryConditions(condiciones) {
  return $.post(_URL + "/ajs/save/condiciones/temp", { condiciones: condiciones })
}

// Función para guardar condiciones de una cotización
function saveCotizacionConditions(cotizacionId, condiciones) {
  return $.post(_URL + "/ajs/save/condiciones/cotizacion", {
    cotizacion_id: cotizacionId,
    condiciones: condiciones,
  })
}

// Nueva función para guardar condiciones predeterminadas
function saveDefaultConditions(condiciones) {
  return $.post(_URL + "/ajs/save/condiciones/default", { nombre: condiciones })
}

// Inicialización de eventos cuando el documento está listo
$(document).ready(() => {
  // Evento para el botón de agregar condiciones (en la página de agregar cotización)
  $(document)
    .off("click", "#add-condiciones")
    .on("click", "#add-condiciones", (e) => {
      e.preventDefault()
      console.log("Botón de agregar condiciones clickeado")

      // Mostrar el modal de agregar
      const modal = new bootstrap.Modal(document.getElementById("add-terminos"))
      modal.show()

      // Cargar condiciones por defecto o las que ya estén en memoria
      if (editorContent) {
        initializeEditor(editorContent)
      } else {
        loadDefaultConditions((condiciones) => {
          initializeEditor(condiciones)
        })
      }
    })

  // Evento para el botón de editar condiciones (en la página de editar cotización)
  $(document)
    .off("click", "#edit-condiciones")
    .on("click", "#edit-condiciones", (e) => {
      e.preventDefault()
      console.log("Botón de editar condiciones clickeado")

      const cotizacionId = $("#cotizacion-id").val() || $("#cotizacion").val()
      console.log("ID de cotización:", cotizacionId)

      // Mostrar el modal
      const modal = new bootstrap.Modal(document.getElementById("edit-terminos"))
      modal.show()

      if (!cotizacionId) {
        // Si no hay ID de cotización, estamos en modo de creación
        console.log("Modo: Creación de nueva cotización")
        if (editorContent) {
          initializeEditor(editorContent)
        } else {
          loadDefaultConditions((condiciones) => {
            initializeEditor(condiciones)
          })
        }
      } else {
        // Si hay ID de cotización, estamos en modo de edición
        console.log("Modo: Edición de cotización existente, ID:", cotizacionId)
        loadCotizacionConditions(cotizacionId, (condiciones) => {
          initializeEditor(condiciones)
        })
      }
    })

  // Evento para guardar condiciones desde el modal de agregar
  $(document)
    .off("click", "#guardar-terminos-add")
    .on("click", "#guardar-terminos-add", () => {
      try {
        const condiciones = getEditorContent()
        editorContent = condiciones

        // Deshabilitar el botón mientras se guarda
        $("#guardar-terminos-add").prop("disabled", true)

        // Obtener la opción seleccionada (cotización actual o todas)
        const guardarPara = $('input[name="guardarCondiciones"]:checked').val()

        // Función para mostrar mensaje de éxito y cerrar modal
        const mostrarExito = () => {
          const modalElement = document.getElementById("add-terminos")
          const modalInstance = bootstrap.Modal.getInstance(modalElement)
          modalInstance.hide()

          setTimeout(() => {
            Swal.fire({
              title: "Info",
              text: "Condiciones guardadas correctamente.",
              icon: "success",
            })
          }, 500)
        }

        // Guardar según la opción seleccionada
        if (guardarPara === "todas") {
          // Guardar para todas las cotizaciones (actualizar tabla condicion)
          saveDefaultConditions(condiciones)
            .done((data) => {
              console.log("Condiciones predeterminadas actualizadas:", data)
              // También guardar como temporales para la cotización actual
              saveTemporaryConditions(condiciones)
                .done(() => {
                  mostrarExito()
                })
                .fail((error) => {
                  console.error("Error al guardar condiciones temporales:", error)
                })
            })
            .fail((error) => {
              console.error("Error al guardar condiciones predeterminadas:", error)
              Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las condiciones predeterminadas.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-terminos-add").prop("disabled", false)
            })
        } else {
          // Guardar solo para la cotización actual (temporal)
          saveTemporaryConditions(condiciones)
            .done((data) => {
              console.log("Condiciones temporales guardadas:", data)
              mostrarExito()
            })
            .fail((error) => {
              console.error("Error al guardar:", error)
              Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las condiciones.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-terminos-add").prop("disabled", false)
            })
        }
      } catch (e) {
        console.error("Error al guardar términos:", e)
        Swal.fire({
          title: "Error",
          text: "Hubo un problema al guardar las condiciones: " + e.message,
          icon: "error",
        })
        $("#guardar-terminos-add").prop("disabled", false)
      }
    })

  // Evento para guardar condiciones desde el modal de editar
  $(document)
    .off("click", "#guardar-terminos")
    .on("click", "#guardar-terminos", () => {
      try {
        const condiciones = getEditorContent()
        editorContent = condiciones
        const cotizacionId = $("#cotizacion-id").val() || $("#cotizacion").val()

        console.log("Guardando condiciones. ID de cotización:", cotizacionId)
        console.log("Contenido a guardar:", condiciones)

        // Deshabilitar el botón mientras se guarda
        $("#guardar-terminos").prop("disabled", true)

        // Obtener la opción seleccionada (cotización actual o todas)
        const guardarPara = $('input[name="guardarCondiciones"]:checked').val()

        // Función para mostrar mensaje de éxito y cerrar modal
        const mostrarExito = () => {
          const modalElement = document.getElementById("edit-terminos")
          const modalInstance = bootstrap.Modal.getInstance(modalElement)
          modalInstance.hide()

          setTimeout(() => {
            Swal.fire({
              title: "Info",
              text: "Guardado con éxito!",
              icon: "success",
            })
          }, 500)
        }

        if (guardarPara === "todas") {
          // Guardar para todas las cotizaciones
          saveDefaultConditions(condiciones)
            .done((data) => {
              console.log("Condiciones predeterminadas actualizadas:", data)

              // Si hay ID de cotización, también guardar para esta cotización específica
              if (cotizacionId) {
                saveCotizacionConditions(cotizacionId, condiciones)
                  .done(() => {
                    mostrarExito()
                  })
                  .fail((error) => {
                    console.error("Error al guardar para cotización específica:", error)
                  })
              } else {
                // Si no hay ID, guardar como temporal
                saveTemporaryConditions(condiciones)
                  .done(() => {
                    mostrarExito()
                  })
                  .fail((error) => {
                    console.error("Error al guardar condiciones temporales:", error)
                  })
              }
            })
            .fail((error) => {
              console.error("Error al guardar condiciones predeterminadas:", error)
              Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las condiciones predeterminadas.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-terminos").prop("disabled", false)
            })
        } else {
          // Guardar solo para la cotización actual
          if (!cotizacionId) {
            // Si no hay ID, guardar como temporal
            saveTemporaryConditions(condiciones)
              .done((data) => {
                console.log("Condiciones temporales guardadas:", data)
                mostrarExito()
              })
              .fail((error) => {
                console.error("Error al guardar:", error)
                Swal.fire({
                  title: "Error",
                  text: "No se pudieron guardar las condiciones.",
                  icon: "error",
                })
              })
              .always(() => {
                $("#guardar-terminos").prop("disabled", false)
              })
          } else {
            // Si hay ID, guardar para la cotización específica
            saveCotizacionConditions(cotizacionId, condiciones)
              .done((data) => {
                console.log("Condiciones de cotización guardadas:", data)
                mostrarExito()
              })
              .fail((error) => {
                console.error("Error al guardar:", error)
                Swal.fire({
                  title: "Error",
                  text: "No se pudieron guardar las condiciones.",
                  icon: "error",
                })
              })
              .always(() => {
                $("#guardar-terminos").prop("disabled", false)
              })
          }
        }
      } catch (e) {
        console.error("Error al guardar términos:", e)
        Swal.fire({
          title: "Error",
          text: "Hubo un problema al guardar las condiciones: " + e.message,
          icon: "error",
        })
        $("#guardar-terminos").prop("disabled", false)
      }
    })

  // Limpiar recursos cuando se cierra el modal de agregar
  $("#add-terminos").on("hidden.bs.modal", () => {
    try {
      destroyEditor()
    } catch (e) {
      console.error("Error al limpiar recursos:", e)
    }
  })

  // Limpiar recursos cuando se cierra el modal de editar
  $("#edit-terminos").on("hidden.bs.modal", () => {
    try {
      destroyEditor()
    } catch (e) {
      console.error("Error al limpiar recursos:", e)
    }
  })
})
