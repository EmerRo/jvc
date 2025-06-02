// observaciones-integration.js - Versión corregida
$(document).ready(function() {
    // Verificar si estamos en la página de cotizaciones/órdenes de trabajo
    if ($("#container-vue").length > 0 && $("#modal-observaciones").length > 0) {
        console.log("Inicializando integración de observaciones");
        
        // Variable para almacenar las observaciones temporalmente
        let observacionesTemp = "";
        
        // Cargar observaciones cuando se muestra el modal
        $('#modal-observaciones').on('shown.bs.modal', function () {
            console.log("Modal de observaciones mostrado");
            
            // Primero intentar cargar desde la sesión temporal
            if (observacionesTemp) {
                $("#observaciones-textarea").val(observacionesTemp);
                return;
            }
            
            // Si no hay en sesión temporal, cargar desde el servidor
            const idCotizacion = new URLSearchParams(window.location.search).get("id");
            if (idCotizacion) {
                console.log("Cargando observaciones para cotización ID:", idCotizacion);
                
                $.ajax({
                    url: _URL + "/ajs/taller/observaciones/get",
                    type: "POST",
                    data: { id_cotizacion: idCotizacion },
                    success: function(response) {
                        console.log("Observaciones cargadas:", response);
                        
                        // Extraer el texto plano de las observaciones HTML
                        let observacionesTexto = "";
                        if (response && response.res && response.observaciones) {
                            // Crear un elemento temporal para extraer el texto
                            const temp = document.createElement("div");
                            temp.innerHTML = response.observaciones;
                            observacionesTexto = temp.textContent || temp.innerText || response.observaciones;
                        }
                        
                        // Establecer el texto en el textarea
                        $("#observaciones-textarea").val(observacionesTexto);
                        // Guardar en variable temporal
                        observacionesTemp = observacionesTexto;
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar observaciones:", error);
                    }
                });
            }
        });

        // Guardar observaciones en variable temporal cuando se cierra el modal
        $('#modal-observaciones').on('hidden.bs.modal', function () {
            observacionesTemp = $("#observaciones-textarea").val();
            console.log("Observaciones guardadas en memoria temporal:", observacionesTemp);
        });

        // Modificar el comportamiento del botón guardar en el modal
        $("#guardar-observaciones").off("click").on("click", function() {
            console.log("Guardando observaciones temporalmente...");
            
            observacionesTemp = $("#observaciones-textarea").val();
            
            // Guardar en sesión temporal en el servidor
            $.ajax({
                url: _URL + "/ajs/taller/observaciones/temp",
                type: "POST",
                data: {
                    observaciones: observacionesTemp
                },
                success: function(response) {
                    console.log("Observaciones guardadas en sesión temporal:", response);
                    Swal.fire({
                        icon: "success",
                        title: "Éxito",
                        text: "Observaciones guardadas temporalmente. Se guardarán definitivamente al guardar la cotización."
                    });
                    $("#modal-observaciones").modal("hide");
                },
                error: function(xhr, status, error) {
                    console.error("Error al guardar observaciones en sesión:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Error al guardar las observaciones temporalmente"
                    });
                }
            });
        });

        // Modificar el método guardarVenta para incluir las observaciones
        if (window.app && window.app.guardarVenta) {
            // Guardar la referencia al método original
            const originalGuardarVenta = window.app.guardarVenta;
            
            // Reemplazar con nuestra versión que incluye las observaciones
            window.app.guardarVenta = function() {
                // Capturar las observaciones temporales
                const observaciones = observacionesTemp;
                
                // Si hay observaciones, guardarlas en la sesión temporal
                if (observaciones) {
                    $.ajax({
                        url: _URL + "/ajs/taller/observaciones/temp",
                        type: "POST",
                        data: {
                            observaciones: observaciones
                        },
                        async: false, // Hacer síncrono para asegurar que se guarde antes de continuar
                        success: function(response) {
                            console.log("Observaciones guardadas en sesión temporal antes de guardar cotización");
                        },
                        error: function(xhr, status, error) {
                            console.error("Error al guardar observaciones en sesión:", error);
                        }
                    });
                }
                
                // Llamar al método original
                return originalGuardarVenta.apply(this, arguments);
            };
            
            console.log("Método guardarVenta modificado para incluir observaciones");
        }
    }
});