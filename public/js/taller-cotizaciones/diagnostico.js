//public\js\taller-cotizaciones\diagnostico.js
let diagnosticoEditor = null
let diagnosticoEditorContent = ""
let tipoGuardadoDiagnostico = "individual"

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

  try {
    diagnosticoEditor = new window.Quill("#editor-content-diagnostico", {
      modules: {
        toolbar: "#toolbar-diagnostico",
        history: {
          delay: 2000,
          maxStack: 500,
          userOnly: true,
        },
      },
      theme: "snow",
      placeholder: "Escribe aquí el diagnóstico...",
    })

    const formattedContent = formatDiagnosticoContent(content)
    console.log("Contenido formateado para diagnóstico:", formattedContent)
    diagnosticoEditor.root.innerHTML = formattedContent
  } catch (error) {
    console.error("Error al inicializar Quill para diagnóstico:", error)
    return
  }
}

function formatDiagnosticoContent(content) {
  if (!content) return ""
  content = content.trim()
  content = content.replace(/&amp;/g, "&")
  let lines = content.split("\n")
  lines = lines.filter((line) => line.trim() !== "")
  const formattedContent = lines
    .map((line) => {
      line = line.trim()
      if (line.startsWith("•")) {
        return "<p>" + line + "</p>"
      }
      return "<p>• " + line + "</p>"
    })
    .join("")
  return formattedContent
}

function getDiagnosticoEditorContent() {
  if (!diagnosticoEditor) {
    throw new Error("Editor de diagnóstico no inicializado correctamente")
  }
  let content = diagnosticoEditor.root.innerHTML
  content = content.replace(/<p>/g, "").replace(/<\/p>/g, "\n")
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

function destroyDiagnosticoEditor() {
  if (diagnosticoEditor) {
    diagnosticoEditor = null
    const editorContainer = document.getElementById("editor-container-diagnostico")
    if (editorContainer) {
      editorContainer.innerHTML = ""
    }
  }
}

function mostrarModalDiagnostico() {
  const modalHtml = `
    <div class="modal fade" id="modal-diagnostico-opciones" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-rojo text-white">
            <h1 class="modal-title fs-5">Agregar Diagnóstico</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="editor-container-diagnostico"></div>
            
            <div class="mt-4 p-3 border rounded">
              <h6 class="mb-3">Opciones de guardado:</h6>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipoGuardadoDiagnostico" id="guardarDiagnosticoIndividual" value="individual" checked>
                <label class="form-check-label" for="guardarDiagnosticoIndividual">
                  <strong>Guardar solo para esta cotización</strong>
                </label>
              </div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="tipoGuardadoDiagnostico" id="guardarDiagnosticoGlobal" value="global">
                <label class="form-check-label" for="guardarDiagnosticoGlobal">
                  <strong>Guardar para todas las cotizaciones</strong>
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" id="guardar-diagnostico-opciones" class="btn bg-rojo text-white">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>
  `

  const modalAnterior = document.getElementById("modal-diagnostico-opciones")
  if (modalAnterior) {
    modalAnterior.remove()
  }

  document.body.insertAdjacentHTML("beforeend", modalHtml)
  const modal = new window.bootstrap.Modal(document.getElementById("modal-diagnostico-opciones"))
  modal.show()
}

// Event listeners usando jQuery como en cotizaciones.add
$(document).ready(() => {
  console.log("Inicializando diagnóstico para taller")

  // Event listener para el botón de diagnóstico
  $(document).on("click", "#add-diagnostico", (e) => {
    e.preventDefault()
    console.log("Botón de diagnóstico clickeado")

    const cotizacionId = $("#cotizacion-id").val() || $("#cotizacion").val()
    console.log("ID de cotización para diagnóstico:", cotizacionId)

    mostrarModalDiagnostico()

    setTimeout(() => {
      if (!cotizacionId) {
        console.log("Modo: Creación de nuevo diagnóstico")

        if (sessionStorage.getItem("temp_diagnostico_taller")) {
          console.log("Cargando diagnóstico temporal de sessionStorage")
          const tempDiagnostico = sessionStorage.getItem("temp_diagnostico_taller")
          diagnosticoEditorContent = tempDiagnostico
          initializeDiagnosticoEditor(tempDiagnostico)
        } else {
          $.ajax({
            url: _URL +"/ajas/get/diagnostico/repuestos",
            type: "GET",
            dataType: "json",
            success: (data) => {
              console.log("Datos de diagnóstico recibidos:", data)
              try {
                const parsedData = Array.isArray(data) ? data : [data]
                if (parsedData && parsedData.length > 0) {
                  const diagnostico = parsedData[0].nombre
                  diagnosticoEditorContent = diagnostico
                  initializeDiagnosticoEditor(diagnostico)
                } else {
                  console.warn("No se encontraron diagnósticos por defecto")
                  initializeDiagnosticoEditor("")
                }
              } catch (e) {
                console.error("Error al procesar diagnósticos por defecto:", e)
                initializeDiagnosticoEditor("")
              }
            },
            error: (xhr, status, error) => {
              console.error("Error al obtener diagnósticos por defecto:", error)
              initializeDiagnosticoEditor("")
            },
          })
        }
      } else {
        console.log("Modo: Edición de diagnóstico existente, ID:", cotizacionId)
        $.ajax({
          url: `/ajas/get/taller/diagnosticos/cotizacion/${cotizacionId}`,
          type: "GET",
          dataType: "json",
          success: (data) => {
            console.log("Datos de diagnóstico específico recibidos:", data)
            try {
              const parsedData = Array.isArray(data) ? data : [data]
              if (parsedData && parsedData.length > 0 && parsedData[0].diagnostico) {
                const diagnostico = parsedData[0].diagnostico
                console.log("Diagnóstico encontrado:", diagnostico)
                diagnosticoEditorContent = diagnostico
                initializeDiagnosticoEditor(diagnostico)
              } else {
                console.log("No se encontró diagnóstico para esta cotización, usando diagnóstico por defecto")
                $.ajax({
                  url: "/ajas/get/diagnostico/repuestos",
                  type: "GET",
                  dataType: "json",
                  success: (defaultData) => {
                    console.log("Datos de diagnóstico por defecto:", defaultData)
                    try {
                      const parsedDefaultData = Array.isArray(defaultData) ? defaultData : [defaultData]
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
                  },
                  error: () => {
                    console.error("Error al obtener diagnósticos por defecto")
                    initializeDiagnosticoEditor("")
                  },
                })
              }
            } catch (e) {
              console.error("Error al parsear diagnóstico:", e)
              initializeDiagnosticoEditor("")
            }
          },
          error: (xhr, status, error) => {
            console.error("Error al obtener diagnóstico de la cotización:", error)
            initializeDiagnosticoEditor("")
          },
        })
      }
    }, 300)
  })

  // Event listener para guardar diagnóstico
  $(document).on("click", "#guardar-diagnostico-opciones", () => {
    try {
      const diagnostico = getDiagnosticoEditorContent()
      diagnosticoEditorContent = diagnostico
      const cotizacionId = $("#cotizacion-id").val()
      tipoGuardadoDiagnostico = $('input[name="tipoGuardadoDiagnostico"]:checked').val()

      console.log("Guardando diagnóstico. ID de cotización:", cotizacionId)
      console.log("Tipo de guardado:", tipoGuardadoDiagnostico)
      console.log("Contenido a guardar:", diagnostico)

      $("#guardar-diagnostico-opciones").prop("disabled", true)

      if (tipoGuardadoDiagnostico === "global") {
        $.post("/jvc/ajas/save/taller/diagnosticos/global", {
          diagnostico: diagnostico,
          nombre: "Diagnóstico actualizado " + new Date().toLocaleDateString(),
        })
          .done((data) => {
            console.log("Diagnóstico global guardado:", data)

            if (cotizacionId) {
              $.post("/ajas/save/taller/diagnosticos/cotizacion", {
                cotizacion_id: cotizacionId,
                diagnostico: diagnostico,
              })
            } else {
              sessionStorage.setItem("temp_diagnostico_taller", diagnostico)
            }

            const modalElement = document.getElementById("modal-diagnostico-opciones")
            if (modalElement) {
              const modalInstance = window.bootstrap.Modal.getInstance(modalElement)
              if (modalInstance) modalInstance.hide()
            }

            setTimeout(() => {
              window.Swal.fire({
                title: "Éxito",
                text: "Diagnóstico guardado como plantilla global y aplicado a esta cotización.",
                icon: "success",
              })
            }, 500)
          })
          .fail((error) => {
            console.error("Error al guardar diagnóstico global:", error)
            window.Swal.fire({
              title: "Error",
              text: "No se pudo guardar el diagnóstico global.",
              icon: "error",
            })
          })
          .always(() => {
            $("#guardar-diagnostico-opciones").prop("disabled", false)
          })
      } else {
        if (!cotizacionId) {
          sessionStorage.setItem("temp_diagnostico_taller", diagnostico)
          console.log("Diagnóstico guardado en sessionStorage")

          $.post("/ajas/save/taller/diagnosticos/temp", { diagnostico: diagnostico })
            .done((data) => {
              console.log("Diagnóstico temporal guardado en sesión PHP:", data)
              const modalElement = document.getElementById("modal-diagnostico-opciones")
              if (modalElement) {
                const modalInstance = window.bootstrap.Modal.getInstance(modalElement)
                if (modalInstance) modalInstance.hide()
              }
              if (window.app) {
                window.app.venta.diagnostico = diagnostico
              }
              setTimeout(() => {
                window.Swal.fire({
                  title: "Info",
                  text: "Diagnóstico guardado temporalmente.",
                  icon: "success",
                })
              }, 500)
            })
            .fail((error) => {
              console.error("Error al guardar diagnóstico:", error)
              window.Swal.fire({
                title: "Error",
                text: "No se pudo guardar el diagnóstico temporal.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-diagnostico-opciones").prop("disabled", false)
            })
        } else {
          $.post("/ajas/save/taller/diagnosticos/cotizacion", {
            cotizacion_id: cotizacionId,
            diagnostico: diagnostico,
          })
            .done((data) => {
              console.log("Diagnóstico de cotización guardado:", data)
              const modalElement = document.getElementById("modal-diagnostico-opciones")
              if (modalElement) {
                const modalInstance = window.bootstrap.Modal.getInstance(modalElement)
                if (modalInstance) modalInstance.hide()
              }

              sessionStorage.removeItem("temp_diagnostico_taller")

              setTimeout(() => {
                window.Swal.fire({
                  title: "Info",
                  text: "Guardado con éxito!",
                  icon: "success",
                })
              }, 500)
            })
            .fail((error) => {
              console.error("Error al guardar diagnóstico:", error)
              window.Swal.fire({
                title: "Error",
                text: "No se pudo guardar el diagnóstico.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-diagnostico-opciones").prop("disabled", false)
            })
        }
      }
    } catch (e) {
      console.error("Error al guardar diagnóstico:", e)
      window.Swal.fire({
        title: "Error",
        text: "Hubo un problema al guardar el diagnóstico: " + e.message,
        icon: "error",
      })
      $("#guardar-diagnostico-opciones").prop("disabled", false)
    }
  })

  // Limpiar recursos cuando se cierra el modal
  $(document).on("hidden.bs.modal", "#modal-diagnostico-opciones", () => {
    try {
      destroyDiagnosticoEditor()
    } catch (e) {
      console.error("Error al limpiar recursos del diagnóstico:", e)
    }
  })

  // Limpiar sessionStorage cuando se recarga la página
  $(window).on("beforeunload", () => {
    const cotizacionId = $("#cotizacion-id").val()
    if (!cotizacionId) {
      sessionStorage.removeItem("temp_diagnostico_taller")
    }
  })
})
