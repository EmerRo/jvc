// Variables globales
let editor = null
let editorContent = ""

// Función para inicializar Quill con configuración mejorada
function initializeEditor(content) {
  const editorContainer = document.getElementById("editor-container")
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
      placeholder: "Escribe aquí los términos y condiciones...",
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
    const editorContainer = document.getElementById("editor-container")
    if (editorContainer) editorContainer.innerHTML = ""
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
        callback("")
      }
    } catch (e) {
      callback("")
    }
  }).fail(() => callback(""))
}

// Función para cargar condiciones de una cotización específica
function loadCotizacionConditions(cotizacionId, callback) {
  $.get(_URL + "/ajs/get/condiciones/cotizacion/" + cotizacionId, (data) => {
    try {
      const parsedData = JSON.parse(data)
      if (parsedData && parsedData.length > 0) {
        const condiciones = parsedData[0].condiciones
        editorContent = condiciones
        callback(condiciones)
      } else {
        loadDefaultConditions(callback)
      }
    } catch (e) {
      callback("")
    }
  }).fail(() => loadDefaultConditions(callback))
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

// Guardar condiciones predeterminadas (para todas las cotizaciones)
function saveDefaultConditions(condiciones) {
  return $.post(_URL + "/ajs/save/condiciones/default", { nombre: condiciones })
}

// Inicialización de eventos cuando el documento está listo
$(document).ready(() => {
  // Abrir modal de agregar
  $(document).off("click", "#add-condiciones").on("click", "#add-condiciones", (e) => {
    e.preventDefault()
    const modal = new bootstrap.Modal(document.getElementById("add-terminos"))
    modal.show()
    if (editorContent) {
      initializeEditor(editorContent)
    } else {
      loadDefaultConditions((condiciones) => initializeEditor(condiciones))
    }
  })

  // Abrir modal de editar
  $(document).off("click", "#edit-condiciones").on("click", "#edit-condiciones", (e) => {
    e.preventDefault()
    const cotizacionId = $("#cotizacion-id").val() || $("#cotizacion").val()
    const modal = new bootstrap.Modal(document.getElementById("edit-terminos"))
    modal.show()
    if (!cotizacionId) {
      if (editorContent) {
        initializeEditor(editorContent)
      } else {
        loadDefaultConditions((condiciones) => initializeEditor(condiciones))
      }
    } else {
      loadCotizacionConditions(cotizacionId, (condiciones) => initializeEditor(condiciones))
    }
  })

  // Guardar desde modal de AGREGAR (nueva cotización)
  $(document).off("click", "#guardar-terminos-add").on("click", "#guardar-terminos-add", () => {
    try {
      const condiciones = getEditorContent()
      editorContent = condiciones
      $("#guardar-terminos-add").prop("disabled", true)

      const guardarPara = $('input[name="guardarCondiciones"]:checked').val()

      const mostrarExito = () => {
        const modalEl = document.getElementById("add-terminos")
        bootstrap.Modal.getInstance(modalEl).hide()
        setTimeout(() => Swal.fire({ title: "Info", text: "Condiciones guardadas correctamente.", icon: "success" }), 500)
      }

      if (guardarPara === "todas") {
        // Guardar default inmediatamente, temp en background
        saveDefaultConditions(condiciones)
          .done(() => {
            mostrarExito()
            saveTemporaryConditions(condiciones) // background, no bloquea
          })
          .fail(() => {
            Swal.fire({ title: "Error", text: "No se pudieron guardar las condiciones predeterminadas.", icon: "error" })
          })
          .always(() => $("#guardar-terminos-add").prop("disabled", false))
      } else {
        // Solo temporal para esta cotización
        saveTemporaryConditions(condiciones)
          .done(() => mostrarExito())
          .fail(() => Swal.fire({ title: "Error", text: "No se pudieron guardar las condiciones.", icon: "error" }))
          .always(() => $("#guardar-terminos-add").prop("disabled", false))
      }
    } catch (e) {
      Swal.fire({ title: "Error", text: "Hubo un problema al guardar: " + e.message, icon: "error" })
      $("#guardar-terminos-add").prop("disabled", false)
    }
  })

  // Guardar desde modal de EDITAR (cotización existente)
  $(document).off("click", "#guardar-terminos").on("click", "#guardar-terminos", () => {
    try {
      const condiciones = getEditorContent()
      editorContent = condiciones
      const cotizacionId = $("#cotizacion-id").val() || $("#cotizacion").val()
      $("#guardar-terminos").prop("disabled", true)

      const guardarPara = $('input[name="guardarCondiciones"]:checked').val()

      const mostrarExito = () => {
        const modalEl = document.getElementById("edit-terminos")
        bootstrap.Modal.getInstance(modalEl).hide()
        setTimeout(() => Swal.fire({ title: "Info", text: "Guardado con éxito!", icon: "success" }), 500)
      }

      if (guardarPara === "todas") {
        // Guardar default inmediatamente
        saveDefaultConditions(condiciones)
          .done(() => {
            mostrarExito()
            // También guardar para esta cotización en background
            if (cotizacionId) {
              saveCotizacionConditions(cotizacionId, condiciones)
            } else {
              saveTemporaryConditions(condiciones)
            }
          })
          .fail(() => {
            Swal.fire({ title: "Error", text: "No se pudieron guardar las condiciones predeterminadas.", icon: "error" })
          })
          .always(() => $("#guardar-terminos").prop("disabled", false))
      } else {
        if (cotizacionId) {
          saveCotizacionConditions(cotizacionId, condiciones)
            .done(() => mostrarExito())
            .fail(() => Swal.fire({ title: "Error", text: "No se pudieron guardar las condiciones.", icon: "error" }))
            .always(() => $("#guardar-terminos").prop("disabled", false))
        } else {
          saveTemporaryConditions(condiciones)
            .done(() => mostrarExito())
            .fail(() => Swal.fire({ title: "Error", text: "No se pudieron guardar las condiciones.", icon: "error" }))
            .always(() => $("#guardar-terminos").prop("disabled", false))
        }
      }
    } catch (e) {
      Swal.fire({ title: "Error", text: "Hubo un problema al guardar: " + e.message, icon: "error" })
      $("#guardar-terminos").prop("disabled", false)
    }
  })

  // Limpiar al cerrar modales
  $("#add-terminos").on("hidden.bs.modal", () => { try { destroyEditor() } catch (e) {} })
  $("#edit-terminos").on("hidden.bs.modal", () => { try { destroyEditor() } catch (e) {} })
})
