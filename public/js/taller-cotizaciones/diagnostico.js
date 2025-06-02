// Variables globales con nombres únicos para evitar conflictos
let diagnosticoEditor = null
let diagnosticoEditorContent = ""

// Función para inicializar Quill con configuración mejorada
function initializeDiagnosticoEditor(content) {
  console.log("Inicializando Quill para diagnóstico con contenido:", content)

  const editorContainer = document.getElementById("editor-container-diagnostico")
  if (!editorContainer) {
    console.error("Contenedor del editor de diagnóstico no encontrado")
    return
  }

  editorContainer.innerHTML = `
        <div id="toolbar-diagnostico">
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
        <div id="editor-content-diagnostico" style="min-height: 200px;"></div>
    `

  // Configurar Quill con nuevas opciones
  try {
    diagnosticoEditor = new Quill("#editor-content-diagnostico", {
      modules: {
        toolbar: "#toolbar-diagnostico",
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
      placeholder: "Escribe aquí el diagnóstico...",
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
    const formattedContent = formatDiagnosticoContent(content)
    console.log("Contenido formateado para diagnóstico:", formattedContent)
    diagnosticoEditor.root.innerHTML = formattedContent

    // Agregar evento para manejar el pegado de texto
    diagnosticoEditor.root.addEventListener("paste", (e) => {
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
      const range = diagnosticoEditor.getSelection()
      if (range) {
        diagnosticoEditor.insertText(range.index, formattedText)
      }
    })
  } catch (error) {
    console.error("Error al inicializar Quill para diagnóstico:", error)
    return
  }
}

// Función para formatear el contenido de la base de datos
function formatDiagnosticoContent(content) {
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
function getDiagnosticoEditorContent() {
  if (!diagnosticoEditor) {
    throw new Error("Editor de diagnóstico no inicializado correctamente")
  }

  // Obtenemos el HTML del editor
  let content = diagnosticoEditor.root.innerHTML

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
function destroyDiagnosticoEditor() {
  if (diagnosticoEditor) {
    diagnosticoEditor = null
    const editorContainer = document.getElementById("editor-container-diagnostico")
    if (editorContainer) {
      editorContainer.innerHTML = ""
    }
  }
}

// Evento para el botón de editar diagnóstico
$(document).ready(() => {
  $(document)
    .off("click", "#edit-diagnostico")
    .on("click", "#edit-diagnostico", (e) => {
      e.preventDefault()
      console.log("Botón de editar diagnóstico clickeado")

      const cotizacionId = $("#cotizacion-id").val() || $("#cotizacion").val()
      console.log("ID de cotización para diagnóstico:", cotizacionId)

      // Mostrar el modal
      const modal = new bootstrap.Modal(document.getElementById("modal-diagnostico"))
      modal.show()

      if (!cotizacionId) {
        // Si no hay ID de cotización, estamos en modo de creación
        console.log("Modo: Creación de nuevo diagnóstico")

        // Verificar si hay diagnóstico temporal en sesión
        if (sessionStorage.getItem("temp_diagnostico_taller")) {
          console.log("Cargando diagnóstico temporal de sessionStorage")
          const tempDiagnostico = sessionStorage.getItem("temp_diagnostico_taller")
          diagnosticoEditorContent = tempDiagnostico
          initializeDiagnosticoEditor(tempDiagnostico)
        } else {
          // Si no hay diagnóstico temporal, cargar el diagnóstico por defecto de diagnostico_repuestos
          $.get("/jvc/ajas/get/diagnostico/repuestos", (data) => {
            try {
              const parsedData = JSON.parse(data)
              if (parsedData && parsedData.length > 0) {
                const diagnostico = parsedData[0].nombre
                diagnosticoEditorContent = diagnostico
                initializeDiagnosticoEditor(diagnostico)
              } else {
                console.warn("No se encontraron diagnósticos por defecto")
                initializeDiagnosticoEditor("")
              }
            } catch (e) {
              console.error("Error al parsear diagnósticos por defecto:", e)
              initializeDiagnosticoEditor("")
            }
          }).fail(() => {
            console.error("Error al obtener diagnósticos por defecto")
            initializeDiagnosticoEditor("")
          })
        }
      } else {
        // Si hay ID de cotización, estamos en modo de edición
        console.log("Modo: Edición de diagnóstico existente, ID:", cotizacionId)
        const url = "/jvc/ajas/get/taller/diagnosticos/cotizacion/" + cotizacionId
        $.get(url, (data) => {
          try {
            console.log("Datos recibidos para diagnóstico:", data)
            const parsedData = JSON.parse(data)
            if (parsedData && parsedData.length > 0) {
              const diagnostico = parsedData[0].diagnostico
              console.log("Diagnóstico encontrado:", diagnostico)
              diagnosticoEditorContent = diagnostico
              initializeDiagnosticoEditor(diagnostico)
            } else {
              console.log("No se encontró diagnóstico para esta cotización, usando diagnóstico por defecto")
              $.get("/jvc/ajas/get/diagnostico/repuestos", (defaultData) => {
                try {
                  const parsedDefaultData = JSON.parse(defaultData)
                  if (parsedDefaultData && parsedDefaultData.length > 0) {
                    const diagnostico = parsedDefaultData[0].nombre
                    diagnosticoEditorContent = diagnostico
                    initializeDiagnosticoEditor(diagnostico)
                  } else {
                    console.warn("No se encontraron diagnósticos por defecto")
                    initializeDiagnosticoEditor("")
                  }
                } catch (e) {
                  console.error("Error al parsear diagnósticos por defecto:", e)
                  initializeDiagnosticoEditor("")
                }
              }).fail(() => {
                console.error("Error al obtener diagnósticos por defecto")
                initializeDiagnosticoEditor("")
              })
            }
          } catch (e) {
            console.error("Error al parsear diagnóstico:", e)
            initializeDiagnosticoEditor("")
          }
        }).fail((xhr, status, error) => {
          console.error("Error al obtener diagnóstico de la cotización:", status, error)
          console.log("Respuesta del servidor:", xhr.responseText)
          initializeDiagnosticoEditor("")
        })
      }
    })

  // Evento para guardar diagnóstico
  $(document)
    .off("click", "#guardar-diagnostico")
    .on("click", "#guardar-diagnostico", () => {
      try {
        const diagnostico = getDiagnosticoEditorContent()
        diagnosticoEditorContent = diagnostico
        const cotizacionId = $("#cotizacion-id").val()

        console.log("Guardando diagnóstico. ID de cotización:", cotizacionId)
        console.log("Contenido a guardar:", diagnostico)

        // Deshabilitar el botón mientras se guarda
        $("#guardar-diagnostico").prop("disabled", true)

        if (!cotizacionId) {
          // Si no hay ID de cotización, guardamos en sessionStorage
          sessionStorage.setItem("temp_diagnostico_taller", diagnostico)
          console.log("Diagnóstico guardado en sessionStorage")

          // También enviamos al servidor para guardar en sesión PHP
          $.post("/jvc/ajas/save/taller/diagnosticos/temp", { diagnostico: diagnostico })
            .done((data) => {
              console.log("Diagnóstico temporal guardado en sesión PHP:", data)
              const modalElement = document.getElementById("modal-diagnostico")
              const modalInstance = bootstrap.Modal.getInstance(modalElement)
              modalInstance.hide()
              if (window.app) {
                window.app.venta.diagnostico = diagnostico
              }
              setTimeout(() => {
                Swal.fire({
                  title: "Info",
                  text: "Diagnóstico guardado temporalmente.",
                  icon: "success",
                })
              }, 500)
            })
            .fail((error) => {
              console.error("Error al guardar diagnóstico:", error)
              Swal.fire({
                title: "Error",
                text: "No se pudo guardar el diagnóstico temporal.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-diagnostico").prop("disabled", false)
            })
        } else {
          // Si hay ID de cotización, guardamos directamente en la base de datos
          $.post("/jvc/ajas/save/taller/diagnosticos/cotizacion", {
            cotizacion_id: cotizacionId,
            diagnostico: diagnostico,
          })
            .done((data) => {
              console.log("Diagnóstico de cotización guardado:", data)
              const modalElement = document.getElementById("modal-diagnostico")
              const modalInstance = bootstrap.Modal.getInstance(modalElement)
              modalInstance.hide()

              // Limpiar sessionStorage después de guardar
              sessionStorage.removeItem("temp_diagnostico_taller")

              setTimeout(() => {
                Swal.fire({
                  title: "Info",
                  text: "Guardado con éxito!",
                  icon: "success",
                })
              }, 500)
            })
            .fail((error) => {
              console.error("Error al guardar diagnóstico:", error)
              Swal.fire({
                title: "Error",
                text: "No se pudo guardar el diagnóstico.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-diagnostico").prop("disabled", false)
            })
        }
      } catch (e) {
        console.error("Error al guardar diagnóstico:", e)
        Swal.fire({
          title: "Error",
          text: "Hubo un problema al guardar el diagnóstico: " + e.message,
          icon: "error",
        })
        $("#guardar-diagnostico").prop("disabled", false)
      }
    })

  // Limpiar recursos cuando se cierra el modal
  $("#modal-diagnostico").on("hidden.bs.modal", () => {
    try {
      destroyDiagnosticoEditor()
    } catch (e) {
      console.error("Error al limpiar recursos del diagnóstico:", e)
    }
  })

  // Limpiar sessionStorage cuando se recarga la página
  $(window).on("beforeunload", () => {
    // Limpiar solo si no estamos en modo de edición (no hay ID de cotización)
    if (!$("#cotizacion-id").val()) {
      sessionStorage.removeItem("temp_diagnostico_taller")
    }
  })
})

