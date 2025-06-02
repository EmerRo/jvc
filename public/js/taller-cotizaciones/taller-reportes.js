$(document).ready(() => {
   
  
    // Botón para descargar reporte PDF
    $("#btnDescargarReportePDF").click(() => {
      const tipoOrden = $("#reporteTipoOrden").val()
      const periodo = $("#reportePeriodo").val()
  
      // Mostrar indicador de carga
      Swal.fire({
        title: "Generando reporte...",
        text: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading()
        },
      })
  
      // Verificar si hay datos disponibles para el período seleccionado
      $.ajax({
        url: _URL + "/reportes/verificar-datos",
        type: "GET",
        data: {
          tipo_orden: tipoOrden,
          periodo: periodo,
        },
        success: (response) => {
          Swal.close()
  
          try {
            const data = typeof response === "string" ? JSON.parse(response) : response
  
            if (data.success) {
              // Si hay datos, abrir el reporte en una nueva ventana
              window.open(_URL + `/reportes/inventario/pdf?tipo_orden=${tipoOrden}&periodo=${periodo}`, "_blank")
              $("#modalReportesInventario").modal("hide")
            } else {
              // Si no hay datos, mostrar mensaje
              Swal.fire({
                icon: "warning",
                title: "Sin datos",
                text: data.message || "No hay datos disponibles para el período seleccionado.",
              })
            }
          } catch (error) {
            console.error("Error al procesar la respuesta:", error)
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al procesar la respuesta del servidor",
            })
          }
        },
        error: () => {
          Swal.close()
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al verificar los datos disponibles",
          })
        },
      })
    })
  
    // Botón para descargar reporte Excel
    $("#btnDescargarReporteExcel").click(() => {
      const tipoOrden = $("#reporteTipoOrden").val()
      const periodo = $("#reportePeriodo").val()
  
      // Mostrar indicador de carga
      Swal.fire({
        title: "Generando reporte...",
        text: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading()
        },
      })
  
      // Verificar si hay datos disponibles para el período seleccionado
      $.ajax({
        url: _URL + "/reportes/verificar-datos",
        type: "GET",
        data: {
          tipo_orden: tipoOrden,
          periodo: periodo,
        },
        success: (response) => {
          Swal.close()
  
          try {
            const data = typeof response === "string" ? JSON.parse(response) : response
  
            if (data.success) {
              // Si hay datos, abrir el reporte en una nueva ventana
              window.open(_URL + `/reportes/inventario/excel?tipo_orden=${tipoOrden}&periodo=${periodo}`, "_blank")
              $("#modalReportesInventario").modal("hide")
            } else {
              // Si no hay datos, mostrar mensaje
              Swal.fire({
                icon: "warning",
                title: "Sin datos",
                text: data.message || "No hay datos disponibles para el período seleccionado.",
              })
            }
          } catch (error) {
            console.error("Error al procesar la respuesta:", error)
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al procesar la respuesta del servidor",
            })
          }
        },
        error: () => {
          Swal.close()
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al verificar los datos disponibles",
          })
        },
      })
    })
  })
  
  