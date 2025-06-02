// choferes.js. public\js\guia-remision\choferes.js
const $ = jQuery
$(document).ready(() => {
  // Función para cargar choferes
  function cargarChoferes() {
    $.ajax({
      url: _URL + "/ajs/get/chofer",
      type: "GET",
      dataType: "json",
      success: (data) => {
        console.log("Respuesta choferes:", data) // Debug
        if (data.status && Array.isArray(data.data)) {
          actualizarListaChoferes(data.data)
          actualizarSelectChofer(data.data)
        } else {
          console.error("Error en respuesta de choferes:", data)
          alertAdvertencia("Error al cargar los choferes")
        }
      },
      error: (xhr, status, error) => {
        console.error("Error en la petición:", error)
        alertAdvertencia("Error al cargar los choferes")
      },
    })
  }

  // Función para actualizar el select de choferes
  function actualizarSelectChofer(choferes) {
    const select = $("#select_chofer")
    select.empty()
    select.append('<option value="">Seleccione un chofer</option>')

    choferes.forEach((chofer) => {
      select.append(`<option value="${chofer.nombre}" data-id="${chofer.id}">${chofer.nombre}</option>`)
    })
    console.log("Select chofer actualizado con:", choferes) // Debug
  }

  // Evento para agregar/editar chofer
  $("#choferForm").on("submit", (e) => {
    e.preventDefault()
    const nombreChofer = $("#nombreChofer").val().trim()
    const choferId = $("#choferId").val()

    if (!nombreChofer) {
      alertAdvertencia("El nombre del chofer es requerido")
      return
    }

    const url = choferId ? _URL + "/ajs/update/chofer" : _URL + "/ajs/save/chofer"

    const datos = {
      nombre: nombreChofer,
    }

    if (choferId) {
      datos.id = choferId
    }

    $.ajax({
      url: url,
      type: "POST",
      data: datos,
      dataType: "json",
      success: (data) => {
        console.log("Respuesta guardar chofer:", data) // Debug
        if (data.status) {
          $("#nombreChofer").val("")
          $("#choferId").val("")
          cargarChoferes()
          $("#choferModal").modal("hide")
          alertExito("Chofer " + (choferId ? "actualizado" : "guardado") + " correctamente")
        } else {
          alertAdvertencia(data.message || "Error al " + (choferId ? "actualizar" : "guardar") + " el chofer")
        }
      },
      error: (xhr, status, error) => {
        console.error("Error en la petición:", error)
        alertAdvertencia("Error al " + (choferId ? "actualizar" : "guardar") + " el chofer")
      },
    })
  })

  // Función para actualizar la lista de choferes en el modal
  function actualizarListaChoferes(choferes) {
    const lista = $("#listaChoferes")
    lista.empty()

    if (Array.isArray(choferes)) {
      choferes.forEach((chofer) => {
        lista.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center" 
                        data-id="${chofer.id}" 
                        data-valor="${chofer.nombre}">
                        <span class="item-text">${chofer.nombre}</span>
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm btn-editar-chofer me-1" data-id="${chofer.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-eliminar-chofer" data-id="${chofer.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </li>
                `)
      })
    }
  }

  // Click en item de la lista para seleccionar
  $(document).on("click", "#listaChoferes li", function (e) {
    if (!$(e.target).closest(".btn-group").length) {
      const valor = $(this).data("valor")
      if (window.app && window.app.transporte) {
        // aseguramos de que siempre se guarde el nombre
        // Si necesitas guardar el ID en algún lugar
        const id = $(this).data("id")
        window.app.$set(window.app.transporte, "chofer_id", id)
    }
      $("#choferModal").modal("hide")
    }
  })

  // Botón eliminar chofer
  $(document).on("click", ".btn-eliminar-chofer", function () {
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
          url: _URL + "/ajs/delete/chofer",
          type: "POST",
          data: { id: id },
          dataType: "json",
          success: (data) => {
            if (data.status) {
              cargarChoferes()
              alertExito("Chofer eliminado correctamente")
            } else {
              alertAdvertencia(data.message || "Error al eliminar el chofer")
            }
          },
          error: (xhr, status, error) => {
            console.error("Error en la petición:", error)
            alertAdvertencia("Error al eliminar el chofer")
          },
        })
      }
    })
  })

  // Botón editar chofer
  $(document).on("click", ".btn-editar-chofer", function (e) {
    e.stopPropagation()
    const id = $(this).data("id")
    const li = $(this).closest("li")
    const nombre = li.find(".item-text").text()

    $("#nombreChofer").val(nombre)
    $("#choferId").val(id)
  })

  // Cargar choferes al abrir el modal
  $("#choferModal").on("show.bs.modal", () => {
    cargarChoferes()
    $("#nombreChofer").val("")
    $("#choferId").val("")
  })

  // Filtrar choferes al escribir en el input
  $("#buscarChofer").on("keyup", function () {
    const searchTerm = $(this).val().toLowerCase()
    $("#listaChoferes li").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1)
    })
  })

  // Inicializar carga de choferes
  cargarChoferes()
})

