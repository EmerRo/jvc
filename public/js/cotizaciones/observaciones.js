// Variables globales
let editor = null
let editorContent = ""

// Función para inicializar Quill con configuración mejorada
function initializeEditor(content) {
  const editorContainer = document.getElementById("observaciones-container")
  if (!editorContainer) return

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

  try {
    editor = new Quill("#editor-content", {
      modules: {
        toolbar: "#toolbar",
        history: {
          delay: 2000,
          maxStack: 500,
          userOnly: true,
        },
      },
      theme: "snow",
      placeholder: "Escribe aquí las observaciones...",
      formats: [
        "bold", "italic", "underline", "strike",
        "align", "list", "link", "color",
        "background", "font", "header",
      ],
    })

    // Establecer contenido a través del modelo interno de Quill (actualiza Delta + DOM)
    const html = formatDatabaseContent(content)
    if (html) {
      editor.clipboard.dangerouslyPasteHTML(0, html)
    }
  } catch (error) {
    console.error("Error al inicializar Quill:", error)
  }
}

// Convierte texto almacenado a HTML para Quill.
// Si ya es HTML (viene de una sesión anterior) lo pasa directo.
// Si es texto plano con •, lo convierte a lista.
function formatDatabaseContent(content) {
  if (!content) return ""
  content = content.trim().replace(/&amp;/g, "&")
  if (content.startsWith("<")) return content
  const lines = content.split("\n").filter(line => line.trim() !== "")
  const items = lines.map(line => {
    const text = line.trim().startsWith("•") ? line.trim().substring(1).trim() : line.trim()
    return "<li>" + text + "</li>"
  }).join("")
  return "<ul>" + items + "</ul>"
}

// Devuelve el HTML completo del editor — preserva negrita, colores, resaltado, etc.
function getEditorContent() {
  if (!editor) throw new Error("Editor no inicializado correctamente")
  return editor.root.innerHTML
}

// Función para destruir el editor
function destroyEditor() {
  if (editor) {
    editor = null
    const editorContainer = document.getElementById("observaciones-container")
    if (editorContainer) editorContainer.innerHTML = ""
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
        callback("")
      }
    } catch (e) {
      callback("")
    }
  }).fail(() => callback(""))
}

// Función para cargar observaciones de una compra específica
function loadCompraObservaciones(compraId, callback) {
  $.get(_URL + "/ajs/get/observaciones/compra/" + compraId, (data) => {
    try {
      const parsedData = JSON.parse(data)
      if (parsedData && parsedData.length > 0) {
        const observaciones = parsedData[0].observaciones
        editorContent = observaciones
        callback(observaciones)
      } else {
        loadDefaultObservaciones(callback)
      }
    } catch (e) {
      callback("")
    }
  }).fail(() => loadDefaultObservaciones(callback))
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
  // Abrir modal de agregar
  $(document)
    .off("click", "#btn-observaciones")
    .on("click", "#btn-observaciones", (e) => {
      e.preventDefault()
      const modal = new bootstrap.Modal(document.getElementById("add-observaciones"))
      modal.show()
      if (editorContent) {
        initializeEditor(editorContent)
      } else {
        loadDefaultObservaciones((observaciones) => initializeEditor(observaciones))
      }
    })

  // Guardar observaciones desde el modal
  $(document)
    .off("click", "#guardar-observaciones-add")
    .on("click", "#guardar-observaciones-add", () => {
      try {
        const observaciones = getEditorContent()
        editorContent = observaciones
        $("#guardar-observaciones-add").prop("disabled", true)

        const guardarPara = $('input[name="guardarObservaciones"]:checked').val()

        const mostrarExito = () => {
          const modalEl = document.getElementById("add-observaciones")
          bootstrap.Modal.getInstance(modalEl).hide()

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

        if (guardarPara === "todas") {
          // Guardar default inmediatamente, temp en background
          saveDefaultObservaciones(observaciones)
            .done(() => {
              mostrarExito()
              saveTemporaryObservaciones(observaciones) // background, no bloquea
            })
            .fail(() => {
              Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las observaciones predeterminadas.",
                icon: "error",
              })
            })
            .always(() => $("#guardar-observaciones-add").prop("disabled", false))
        } else {
          // Solo temporal para esta compra
          saveTemporaryObservaciones(observaciones)
            .done(() => mostrarExito())
            .fail(() => {
              Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las observaciones.",
                icon: "error",
              })
            })
            .always(() => $("#guardar-observaciones-add").prop("disabled", false))
        }
      } catch (e) {
        Swal.fire({
          title: "Error",
          text: "Hubo un problema al guardar las observaciones: " + e.message,
          icon: "error",
        })
        $("#guardar-observaciones-add").prop("disabled", false)
      }
    })

  // Limpiar al cerrar el modal
  $("#add-observaciones").on("hidden.bs.modal", () => {
    try {
      destroyEditor()
    } catch (e) {}
  })
})
