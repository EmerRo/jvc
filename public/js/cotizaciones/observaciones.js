// Variables globales
let editor = null
let editorContent = ""

// Función para inicializar Quill con configuración mejorada
function initializeEditor(content) {
  console.log("Inicializando Quill con contenido:", content)

  const editorContainer = document.getElementById("observaciones-container")
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
      placeholder: "Escribe aquí las observaciones...",
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
    const editorContainer = document.getElementById("observaciones-container")
    if (editorContainer) {
      editorContainer.innerHTML = ""
    }
  }
}

// Función para cargar observaciones por defecto
function loadDefaultObservaciones(callback) {
  $.get(_URL + "/ajs/get/observaciones/default", (data) => {
    try {
      const parsedData = JSON.parse(data)
      if (parsedData && parsedData.length > 0) {
        const observaciones = parsedData[0].detalle
        editorContent = observaciones
        callback(observaciones)
      } else {
        console.warn("No se encontraron observaciones por defecto")
        callback("")
      }
    } catch (e) {
      console.error("Error al parsear observaciones por defecto:", e)
      callback("")
    }
  }).fail(() => {
    console.error("Error al obtener observaciones por defecto")
    callback("")
  })
}

// Función para cargar observaciones de una compra específica
function loadCompraObservaciones(compraId, callback) {
  const url = _URL + "/ajs/get/observaciones/compra/" + compraId
  $.get(url, (data) => {
    try {
      console.log("Datos recibidos:", data)
      const parsedData = JSON.parse(data)
      if (parsedData && parsedData.length > 0) {
        const observaciones = parsedData[0].observaciones
        console.log("Observaciones encontradas:", observaciones)
        editorContent = observaciones
        callback(observaciones)
      } else {
        console.log("No se encontraron observaciones para esta compra, usando observaciones por defecto")
        loadDefaultObservaciones(callback)
      }
    } catch (e) {
      console.error("Error al parsear observaciones:", e)
      callback("")
    }
  }).fail((xhr, status, error) => {
    console.error("Error al obtener observaciones de la compra:", status, error)
    console.log("Respuesta del servidor:", xhr.responseText)
    callback("")
  })
}

// Función para guardar observaciones temporales
function saveTemporaryObservaciones(observaciones) {
  return $.post(_URL + "/ajs/save/observaciones/temp", { observaciones: observaciones })
}

// Función para guardar observaciones de una compra
function saveCompraObservaciones(compraId, observaciones) {
  return $.post(_URL + "/ajs/save/observaciones/compra", {
    compra_id: compraId,
    observaciones: observaciones,
  })
}

// Función para guardar observaciones predeterminadas
function saveDefaultObservaciones(observaciones) {
  return $.post(_URL + "/ajs/save/observaciones/default", { detalle: observaciones })
}

// Inicialización de eventos cuando el documento está listo
$(document).ready(() => {
  // Evento para el botón de agregar observaciones
  $(document)
    .off("click", "#btn-observaciones")
    .on("click", "#btn-observaciones", (e) => {
      e.preventDefault()
      console.log("Botón de agregar observaciones clickeado")

      // Mostrar el modal de agregar
      const modal = new bootstrap.Modal(document.getElementById("add-observaciones"))
      modal.show()

      // Cargar observaciones por defecto o las que ya estén en memoria
      if (editorContent) {
        initializeEditor(editorContent)
      } else {
        loadDefaultObservaciones((observaciones) => {
          initializeEditor(observaciones)
        })
      }
    })

  // Evento para guardar observaciones desde el modal
  $(document)
    .off("click", "#guardar-observaciones-add")
    .on("click", "#guardar-observaciones-add", () => {
      try {
        const observaciones = getEditorContent()
        editorContent = observaciones

        // Deshabilitar el botón mientras se guarda
        $("#guardar-observaciones-add").prop("disabled", true)

        // Obtener la opción seleccionada (compra actual o todas)
        const guardarPara = $('input[name="guardarObservaciones"]:checked').val()

        // Función para mostrar mensaje de éxito y cerrar modal
        const mostrarExito = () => {
          const modalElement = document.getElementById("add-observaciones")
          const modalInstance = bootstrap.Modal.getInstance(modalElement)
          modalInstance.hide()

          // Actualizar el valor en el modelo Vue
          if (app && app._data && app._data.venta) {
            app._data.venta.observacion = observaciones
          }

          setTimeout(() => {
            Swal.fire({
              title: "Info",
              text: "Observaciones guardadas correctamente.",
              icon: "success",
            })
          }, 500)
        }

        // Guardar según la opción seleccionada
        if (guardarPara === "todas") {
          // Guardar para todas las compras (actualizar tabla observacion)
          saveDefaultObservaciones(observaciones)
            .done((data) => {
              console.log("Observaciones predeterminadas actualizadas:", data)
              // También guardar como temporales para la compra actual
              saveTemporaryObservaciones(observaciones)
                .done(() => {
                  mostrarExito()
                })
                .fail((error) => {
                  console.error("Error al guardar observaciones temporales:", error)
                })
            })
            .fail((error) => {
              console.error("Error al guardar observaciones predeterminadas:", error)
              Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las observaciones predeterminadas.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-observaciones-add").prop("disabled", false)
            })
        } else {
          // Guardar solo para la compra actual (temporal)
          saveTemporaryObservaciones(observaciones)
            .done((data) => {
              console.log("Observaciones temporales guardadas:", data)
              mostrarExito()
            })
            .fail((error) => {
              console.error("Error al guardar:", error)
              Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las observaciones.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-observaciones-add").prop("disabled", false)
            })
        }
      } catch (e) {
        console.error("Error al guardar observaciones:", e)
        Swal.fire({
          title: "Error",
          text: "Hubo un problema al guardar las observaciones: " + e.message,
          icon: "error",
        })
        $("#guardar-observaciones-add").prop("disabled", false)
      }
    })

  // Limpiar recursos cuando se cierra el modal
  $("#add-observaciones").on("hidden.bs.modal", () => {
    try {
      destroyEditor()
    } catch (e) {
      console.error("Error al limpiar recursos:", e)
    }
  })
})