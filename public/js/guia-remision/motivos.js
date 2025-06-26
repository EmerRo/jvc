// Declare necessary variables

$(document).ready(() => {
  // Función para cargar motivos
  function cargarMotivos() {
    $.ajax({
      url: _URL + "/ajs/get/motivos-guia",
      type: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      success: (response) => {
        try {
          const data = typeof response === "string" ? JSON.parse(response) : response

          if (data.status) {
            actualizarSelectMotivos(data.data)
            actualizarListaMotivos(data.data)
          } else {
            console.error("Error en la respuesta:", data)
            alertAdvertencia("Error al cargar los motivos")
          }
        } catch (e) {
          console.error("Error al procesar la respuesta:", e)
          alertAdvertencia("Error al procesar los motivos")
        }
      },
      error: (xhr, status, error) => {
        console.error("Error en la petición:", error)
        alertAdvertencia("Error al cargar los motivos")
      },
    })
  }

  // Evento para agregar nuevo motivo
  $("#motivoForm").on("submit", (e) => {
    e.preventDefault()
    const nombreMotivo = $("#nombreMotivo").val().trim()

    if (!nombreMotivo) {
      alertAdvertencia("El nombre del motivo es requerido")
      return
    }

    $.ajax({
      url: _URL + "/ajs/save/motivos-guia",
      type: "POST",
      data: { nombre: nombreMotivo },
      success: (response) => {
        try {
          const data = typeof response === "string" ? JSON.parse(response) : response

          if (data.status) {
            $("#nombreMotivo").val("")
            cargarMotivos()
            $("#motivoModal").modal("hide")
            alertExito("Motivo guardado correctamente")
          } else {
            alertAdvertencia(data.message || "Error al guardar el motivo")
          }
        } catch (e) {
          console.error("Error al procesar la respuesta:", e)
          alertAdvertencia("Error al procesar la respuesta")
        }
      },
      error: (xhr, status, error) => {
        console.error("Error en la petición:", error)
        alertAdvertencia("Error al guardar el motivo")
      },
    })
  })

  // Función para eliminar motivo
  $(document).on("click", ".btn-eliminar-motivo", function () {
    const id = $(this).data("id")

    Swal.fire({
      title: "¿Está seguro?",
      text: "Esta acción no se puede deshacer",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: _URL + "/ajs/delete/motivos-guia",
          type: "POST",
          data: { id: id },
          success: (response) => {
            try {
              const data = typeof response === "string" ? JSON.parse(response) : response

              if (data.status) {
                cargarMotivos()
                alertExito("Motivo eliminado correctamente")
              } else {
                alertAdvertencia(data.message || "Error al eliminar el motivo")
              }
            } catch (e) {
              console.error("Error al procesar la respuesta:", e)
              alertAdvertencia("Error al procesar la respuesta")
            }
          },
          error: (xhr, status, error) => {
            console.error("Error en la petición:", error)
            alertAdvertencia("Error al eliminar el motivo")
          },
        })
      }
    })
  })

  // Nueva función para establecer/desestablecer motivo por defecto
  $(document).on("click", ".btn-default-motivo", function () {
    const id = $(this).data("id")
    const isDefault = $(this).data("is-default") === 0 // Si es 0, lo queremos hacer default (true)

    $.ajax({
      url: _URL + "/ajs/set/default-motivo-guia",
      type: "POST",
      data: { id: id, is_default: isDefault },
      success: (response) => {
        try {
          const data = typeof response === "string" ? JSON.parse(response) : response
          if (data.status) {
            alertExito(data.message)
            cargarMotivos() // Recargar para actualizar la UI
          } else {
            alertAdvertencia(data.message || "Error al actualizar el motivo predeterminado.")
          }
        } catch (e) {
          console.error("Error al procesar la respuesta:", e)
          alertAdvertencia("Error al procesar la respuesta del motivo predeterminado.")
        }
      },
      error: (xhr, status, error) => {
        console.error("Error en la petición:", error)
        alertAdvertencia("Error al establecer el motivo predeterminado.")
      },
    })
  })

  // Función para actualizar el select de motivos
  function actualizarSelectMotivos(motivos) {
    const select = $("#select_motivo")
    select.empty()
    select.append('<option value="">Seleccione un motivo</option>')

    let defaultMotivoId = null

    if (Array.isArray(motivos)) {
      motivos.forEach((motivo) => {
        select.append(`<option value="${motivo.id}">${motivo.nombre}</option>`)
        if (Number.parseInt(motivo.es_defecto) === 1) {
          defaultMotivoId = motivo.id
        }
      })
    }

    // Seleccionar el motivo por defecto si existe
    console.log("Motivos recibidos para select:", motivos) // Para ver todos los motivos y sus estados
    console.log("ID del motivo por defecto encontrado:", defaultMotivoId) // Para confirmar el ID que se intenta seleccionar

    if (defaultMotivoId !== null) {
      select.val(defaultMotivoId)
      console.log("Valor del select después de intentar actualizar:", select.val()) // Para ver si se actualizó
    } else {
      console.log("No se encontró ningún motivo por defecto para seleccionar.")
    }
  }

  // Función para actualizar la lista de motivos en el modal
  function actualizarListaMotivos(motivos) {
    const lista = $("#listaMotivos")
    lista.empty()

    if (Array.isArray(motivos)) {
      motivos.forEach((motivo) => {
        const isMotivoDefault = Number.parseInt(motivo.es_defecto) === 1 // Convierte "0" o "1" a número y compara
        const isDefault = isMotivoDefault ? 1 : 0 // Esto es para el data-attribute, sigue siendo 0 o 1
        const btnClass = isMotivoDefault ? "btn-success" : "btn bg-gray-400"
        const iconClass = isMotivoDefault ? "fas fa-star" : "far fa-star"
        const btnText = isMotivoDefault ? "Predeterminado" : "Establecer Predeterminado"

        lista.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        ${motivo.nombre}
                        <div>
                            <button class="btn ${btnClass} btn-sm me-2 btn-default-motivo" data-id="${motivo.id}" data-is-default="${isDefault}" title="${btnText}">
                                <i class="fa ${iconClass}"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar-motivo" data-id="${motivo.id}" title="Eliminar">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </div>
                    </li>
                `)
      })
    }
  }

  // Cargar motivos al iniciar
  cargarMotivos()
})
