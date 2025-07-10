//public\js\taller-cotizaciones\terminos-condiciones.js
let editor = null
let editorContent = ""
let tipoGuardado = "individual"

// Función para inicializar Quill con configuración mejorada
function initializeEditor(content) {
  console.log("Inicializando Quill con contenido:", content)

  const editorContainer = document.getElementById("editor-container-terminos")
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

  try {
    editor = new window.Quill("#editor-content", {
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
    })

    const formattedContent = formatDatabaseContent(content)
    console.log("Contenido formateado:", formattedContent)
    editor.root.innerHTML = formattedContent
  } catch (error) {
    console.error("Error al inicializar Quill:", error)
    return
  }
}

function formatDatabaseContent(content) {
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

function getEditorContent() {
  if (!editor) {
    throw new Error("Editor no inicializado correctamente")
  }
  let content = editor.root.innerHTML
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

function destroyEditor() {
  if (editor) {
    editor = null
    const editorContainer = document.getElementById("editor-container-terminos")
    if (editorContainer) {
      editorContainer.innerHTML = ""
    }
  }
}

function mostrarModalTerminos() {
  const modalHtml = `
    <div class="modal fade" id="modal-terminos-opciones" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-rojo text-white">
            <h1 class="modal-title fs-5">Agregar Términos y Condiciones</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="editor-container-terminos"></div>
            
            <div class="mt-4 p-3 border rounded">
              <h6 class="mb-3">Opciones de guardado:</h6>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipoGuardado" id="guardarIndividual" value="individual" checked>
                <label class="form-check-label" for="guardarIndividual">
                  <strong>Guardar solo para esta cotización</strong>
                </label>
              </div>
              <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="tipoGuardado" id="guardarGlobal" value="global">
                <label class="form-check-label" for="guardarGlobal">
                  <strong>Guardar para todas las cotizaciones</strong>
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn border-rojo" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" id="guardar-terminos-opciones" class="btn bg-rojo text-white">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>
  `

  const modalAnterior = document.getElementById("modal-terminos-opciones")
  if (modalAnterior) {
    modalAnterior.remove()
  }

  document.body.insertAdjacentHTML("beforeend", modalHtml)
  const modal = new window.bootstrap.Modal(document.getElementById("modal-terminos-opciones"))
  modal.show()
}

// Event listeners usando jQuery como en cotizaciones.add
window.jQuery(document).ready(($) => {
  console.log("Inicializando términos y condiciones para taller")

  // Event listener para el botón de condiciones
  $(document).on("click", "#add-condiciones", (e) => {
    e.preventDefault()
    console.log("Botón de condiciones clickeado")

    const cotizacionId = $("#cotizacion-id").val() || $("#cotizacion").val()
    console.log("ID de cotización:", cotizacionId)

    mostrarModalTerminos()

    setTimeout(() => {
      if (!cotizacionId) {
        console.log("Modo: Creación de nueva cotización")

        if (sessionStorage.getItem("temp_condiciones_taller")) {
          console.log("Cargando condiciones temporales de sessionStorage")
          const tempCondiciones = sessionStorage.getItem("temp_condiciones_taller")
          editorContent = tempCondiciones
          initializeEditor(tempCondiciones)
        } else {
          $.ajax({
            url: _URL + "/ajs/get/terminos/repuestos",
            type: "GET",
            dataType: "json",
            success: (data) => {
              console.log("Datos de términos recibidos:", data)
              try {
                const parsedData = Array.isArray(data) ? data : [data]
                if (parsedData && parsedData.length > 0) {
                  const condiciones = parsedData[0].nombre
                  editorContent = condiciones
                  initializeEditor(condiciones)
                } else {
                  console.warn("No se encontraron condiciones por defecto")
                  initializeEditor("")
                }
              } catch (e) {
                console.error("Error al procesar condiciones por defecto:", e)
                initializeEditor("")
              }
            },
            error: (xhr, status, error) => {
              console.error("Error al obtener condiciones por defecto:", error)
              initializeEditor("")
            },
          })
        }
      } else {
        console.log("Modo: Edición de cotización existente, ID:", cotizacionId)
        $.ajax({
          url: `/ajs/get/taller/condiciones/cotizacion/${cotizacionId}`,
          type: "GET",
          dataType: "json",
          success: (data) => {
            console.log("Datos de condiciones específicas recibidos:", data)
            try {
              const parsedData = Array.isArray(data) ? data : [data]
              if (parsedData && parsedData.length > 0 && parsedData[0].condiciones) {
                const condiciones = parsedData[0].condiciones
                console.log("Condiciones encontradas:", condiciones)
                editorContent = condiciones
                initializeEditor(condiciones)
              } else {
                console.log("No se encontraron condiciones para esta cotización, usando condiciones por defecto")
                $.ajax({
                  url: "/ajs/get/terminos/repuestos",
                  type: "GET",
                  dataType: "json",
                  success: (defaultData) => {
                    console.log("Datos por defecto:", defaultData)
                    try {
                      const parsedDefaultData = Array.isArray(defaultData) ? defaultData : [defaultData]
                      if (parsedDefaultData && parsedDefaultData.length > 0) {
                        const condiciones = parsedDefaultData[0].nombre
                        editorContent = condiciones
                        initializeEditor(condiciones)
                      } else {
                        console.warn("No se encontraron condiciones por defecto")
                        initializeEditor("")
                      }
                    } catch (e) {
                      console.error("Error al parsear condiciones por defecto:", e)
                      initializeEditor("")
                    }
                  },
                  error: () => {
                    console.error("Error al obtener condiciones por defecto")
                    initializeEditor("")
                  },
                })
              }
            } catch (e) {
              console.error("Error al parsear condiciones:", e)
              initializeEditor("")
            }
          },
          error: (xhr, status, error) => {
            console.error("Error al obtener condiciones de la cotización:", error)
            initializeEditor("")
          },
        })
      }
    }, 300)
  })

  // Event listener para guardar condiciones
  $(document).on("click", "#guardar-terminos-opciones", () => {
    try {
      const condiciones = getEditorContent()
      editorContent = condiciones
      const cotizacionId = $("#cotizacion-id").val()
      tipoGuardado = $('input[name="tipoGuardado"]:checked').val()

      console.log("Guardando condiciones. ID de cotización:", cotizacionId)
      console.log("Tipo de guardado:", tipoGuardado)
      console.log("Contenido a guardar:", condiciones)

      $("#guardar-terminos-opciones").prop("disabled", true)

      if (tipoGuardado === "global") {
        $.post("/jvc/ajs/save/taller/condiciones/global", {
          condiciones: condiciones,
          nombre: "Términos actualizados " + new Date().toLocaleDateString(),
        })
          .done((data) => {
            console.log("Condiciones globales guardadas:", data)

            if (cotizacionId) {
              $.post("/ajs/save/taller/condiciones/cotizacion", {
                cotizacion_id: cotizacionId,
                condiciones: condiciones,
              })
            } else {
              sessionStorage.setItem("temp_condiciones_taller", condiciones)
            }

            const modalElement = document.getElementById("modal-terminos-opciones")
            if (modalElement) {
              const modalInstance = window.bootstrap.Modal.getInstance(modalElement)
              if (modalInstance) modalInstance.hide()
            }

            setTimeout(() => {
              window.Swal.fire({
                title: "Éxito",
                text: "Condiciones guardadas como plantilla global y aplicadas a esta cotización.",
                icon: "success",
              })
            }, 500)
          })
          .fail((error) => {
            console.error("Error al guardar condiciones globales:", error)
            window.Swal.fire({
              title: "Error",
              text: "No se pudieron guardar las condiciones globales.",
              icon: "error",
            })
          })
          .always(() => {
            $("#guardar-terminos-opciones").prop("disabled", false)
          })
      } else {
        if (!cotizacionId) {
          sessionStorage.setItem("temp_condiciones_taller", condiciones)
          console.log("Condiciones guardadas en sessionStorage")

          $.post("/ajs/save/taller/condiciones/temp", { condiciones: condiciones })
            .done((data) => {
              console.log("Condiciones temporales guardadas en sesión PHP:", data)
              if (window.app) {
                window.app.venta.condiciones = condiciones
              }
              const modalElement = document.getElementById("modal-terminos-opciones")
              if (modalElement) {
                const modalInstance = window.bootstrap.Modal.getInstance(modalElement)
                if (modalInstance) modalInstance.hide()
              }

              setTimeout(() => {
                window.Swal.fire({
                  title: "Info",
                  text: "Condiciones guardadas temporalmente.",
                  icon: "success",
                })
              }, 500)
            })
            .fail((error) => {
              console.error("Error al guardar:", error)
              window.Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las condiciones temporales.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-terminos-opciones").prop("disabled", false)
            })
        } else {
          $.post("/ajs/save/taller/condiciones/cotizacion", {
            cotizacion_id: cotizacionId,
            condiciones: condiciones,
          })
            .done((data) => {
              console.log("Condiciones de cotización guardadas:", data)
              const modalElement = document.getElementById("modal-terminos-opciones")
              if (modalElement) {
                const modalInstance = window.bootstrap.Modal.getInstance(modalElement)
                if (modalInstance) modalInstance.hide()
              }

              if (window.app) {
                window.app.venta.condiciones = condiciones
              }
              sessionStorage.removeItem("temp_condiciones_taller")

              setTimeout(() => {
                window.Swal.fire({
                  title: "Info",
                  text: "Guardado con éxito!",
                  icon: "success",
                })
              }, 500)
            })
            .fail((error) => {
              console.error("Error al guardar:", error)
              window.Swal.fire({
                title: "Error",
                text: "No se pudieron guardar las condiciones.",
                icon: "error",
              })
            })
            .always(() => {
              $("#guardar-terminos-opciones").prop("disabled", false)
            })
        }
      }
    } catch (e) {
      console.error("Error al guardar términos:", e)
      window.Swal.fire({
        title: "Error",
        text: "Hubo un problema al guardar las condiciones: " + e.message,
        icon: "error",
      })
      $("#guardar-terminos-opciones").prop("disabled", false)
    }
  })

  // Limpiar recursos cuando se cierra el modal
  $(document).on("hidden.bs.modal", "#modal-terminos-opciones", () => {
    try {
      destroyEditor()
    } catch (e) {
      console.error("Error al limpiar recursos:", e)
    }
  })

  // Limpiar sessionStorage cuando se recarga la página
  $(window).on("beforeunload", () => {
    const cotizacionId = $("#cotizacion-id").val()
    if (!cotizacionId) {
      sessionStorage.removeItem("temp_condiciones_taller")
    }
  })
})
