// public\js\guia-remision\conductor.js

$(document).ready(() => {
    // Cargar datos iniciales
    ;["chofer", "vehiculo", "licencia"].forEach((tipo) => {
      cargarItems(tipo)
    })
  
    // Estilos CSS para hover
    $("<style>")
      .text(`
          .list-group-item {
              cursor: pointer;
              transition: background-color 0.3s ease;
          }
          .list-group-item:hover {
              background-color: #f8f9fa;
          }
      `)
      .appendTo("head")
  
    // Funciones genéricas para manejar las operaciones CRUD
    function cargarItems(tipo) {
      $.ajax({
        url: _URL + "/ajs/get/" + tipo,
        type: "GET",
        dataType: "json",
        success: (data) => {
          console.log(`Respuesta ${tipo}:`, data) // Debug
          if (data.status) {
            actualizarLista(tipo, data.data)
            actualizarSelect(tipo, data.data)
          } else {
            console.error(`Error al cargar ${tipo}:`, data)
            alertAdvertencia(`Error al cargar los ${tipo}s`)
          }
        },
        error: (xhr, status, error) => {
          console.error(`Error en petición ${tipo}:`, error)
          alertAdvertencia(`Error al cargar los ${tipo}s`)
        },
      })
    }
    function actualizarSelect(tipo, items) {
      const select = $(`#select_${tipo}`)
      select.empty()
      select.append(`<option value="">Seleccione un ${tipo}</option>`)
  
      if (Array.isArray(items)) {
        items.forEach((item) => {
          let valor = ""
          let texto = ""
  
          switch (tipo) {
            case "chofer":
              valor = item.nombre
              texto = item.nombre
              break
            case "vehiculo":
              valor = item.placa
              texto = item.placa
              break
            case "licencia":
              valor = item.numero
              texto = item.numero
              break
          }
  
          if (valor) {
            select.append(`<option value="${valor}">${texto}</option>`)
          }
        })
      }
  
      // Debug
      console.log(`Actualizando select ${tipo}:`, items)
    }
  
    function actualizarVueData(tipo, valor) {
      // Obtener la instancia de Vue
      const app = window.app
      if (!app) {
        console.error("No se encontró la instancia de Vue")
        return
      }
  
      // Actualizar el valor correspondiente en el objeto transporte
      switch (tipo) {
        case "vehiculo":
          app.transporte.veiculo = valor
          break
        case "licencia":
          app.transporte.chofer_dni = valor
          break
      }
    }
  
    function guardarItem(tipo, datos) {
      const url = datos.id ? _URL + "/ajs/update/" + tipo : _URL + "/ajs/save/" + tipo
  
      $.ajax({
        url: url,
        type: "POST",
        data: datos,
        dataType: "json",
        success: (data) => {
          if (data.status) {
            $(`#${tipo}Modal`).modal("hide")
            cargarItems(tipo)
            limpiarFormulario(tipo)
            alertExito(tipo.charAt(0).toUpperCase() + tipo.slice(1) + " guardado correctamente")
          }
        },
        error: (xhr, status, error) => {
          alertAdvertencia("Error al guardar")
        },
      })
    }
  
    function eliminarItem(tipo, id) {
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
            url: _URL + "/ajs/delete/" + tipo,
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: (data) => {
              if (data.status) {
                cargarItems(tipo)
                alertExito(tipo.charAt(0).toUpperCase() + tipo.slice(1) + " eliminado correctamente")
              }
            },
          })
        }
      })
    }
  
    function actualizarLista(tipo, items) {
      const lista = $(`#lista${tipo.charAt(0).toUpperCase() + tipo.slice(1)}s`)
      lista.empty()
  
      if (Array.isArray(items)) {
        items.forEach((item) => {
          const nombreCampo = tipo === "vehiculo" ? "placa" : "numero"
  
          lista.append(`
                  <li class="list-group-item d-flex justify-content-between align-items-center" 
                      data-id="${item.id}" 
                      data-valor="${item[nombreCampo]}">
                      <span class="item-text">${item[nombreCampo]}</span>
                      <div class="btn-group">
                          <button class="btn btn-warning btn-sm btn-editar me-1" data-tipo="${tipo}" data-id="${item.id}">
                              <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-danger btn-sm btn-eliminar" data-tipo="${tipo}" data-id="${item.id}">
                              <i class="fas fa-trash"></i>
                          </button>
                      </div>
                  </li>
              `)
        })
      }
    }
  
    function limpiarFormulario(tipo) {
      const inputId = tipo === "vehiculo" ? "placaVehiculo" : "numeroLicencia"
      $(`#${inputId}`).val("")
      $(`#${tipo}Id`).val("")
    }
    // Event Listeners para los formularios
    ;["vehiculo", "licencia"].forEach((tipo) => {
      // Manejar envío de formulario
      $(`#${tipo}Form`).on("submit", (e) => {
        e.preventDefault()
        const input = $(`#${tipo === "vehiculo" ? "placaVehiculo" : "numeroLicencia"}`)
        const valor = input.val().trim()
  
        if (valor) {
          const datos = {}
          const campo = tipo === "chofer" ? "nombre" : tipo === "vehiculo" ? "placa" : "numero"
          datos[campo] = valor
          guardarItem(tipo, datos)
        }
      })
  
      // Click en item de la lista
      $(document).on("click", `#lista${tipo.charAt(0).toUpperCase() + tipo.slice(1)}s li`, function (e) {
        if (!$(e.target).closest(".btn-group").length) {
          const valor = $(this).data("valor")
          actualizarVueData(tipo, valor)
          $(`#${tipo}Modal`).modal("hide")
        }
      })
  
      // Botón editar
      $(document).on("click", `.btn-editar[data-tipo="${tipo}"]`, function (e) {
        e.stopPropagation()
        const id = $(this).data("id")
        const li = $(this).closest("li")
        const valor = li.find(".item-text").text()
  
        const inputId = tipo === "vehiculo" ? "placaVehiculo" : "numeroLicencia"
        $(`#${inputId}`).val(valor)
        $(`#${tipo}Id`).val(id)
      })
  
      // Cargar items al abrir el modal
      $(`#${tipo}Modal`).on("show.bs.modal", () => {
        cargarItems(tipo)
        limpiarFormulario(tipo)
      })
    })
  
    // Event Listener para botones de eliminar
    $(document).on("click", ".btn-eliminar", function (e) {
      e.stopPropagation()
      const tipo = $(this).data("tipo")
      const id = $(this).data("id")
      eliminarItem(tipo, id)
    })
  })
  
  