$(document).ready(() => {
    // Cargar configuraciones de conductores
    cargarConfiguracionesConductores();
    
    // Variable para controlar si estamos editando
    let editandoConfiguracion = false;
    let configuracionEditandoId = null;
    
    // Función para cargar configuraciones
    function cargarConfiguracionesConductores() {
        $.ajax({
            url: _URL + "/ajs/get/conductor/configuraciones",
            type: "GET",
            dataType: "json",
            success: (data) => {
                console.log("Configuraciones cargadas:", data);
                if (data.status) {
                    actualizarListaConfiguraciones(data.data);
                    actualizarSelectChofer(data.data);
                } else {
                    console.error("Error en respuesta:", data);
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al cargar configuraciones:", error);
                console.error("Respuesta del servidor:", xhr.responseText);
            }
        });
    }

    // Función para actualizar la lista de configuraciones CON SCROLL
    function actualizarListaConfiguraciones(configuraciones) {
        const tablaBody = $("#tablaConfiguraciones");
        tablaBody.empty();
    
        if (Array.isArray(configuraciones) && configuraciones.length > 0) {
            configuraciones.forEach((config) => {
                tablaBody.append(`
                    <tr>
                        <td>${config.chofer_nombre}</td>
                        <td>${config.chofer_dni}</td>
                        <td>${config.vehiculo_placa}</td>
                        <td>${config.vehiculo_marca || 'Sin marca'}</td>
                        <td>${config.licencia_numero}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-success text-white btn-seleccionar-config" 
                                        data-config-id="${config.id}"
                                        data-chofer-id="${config.chofer_id}"
                                        data-chofer-nombre="${config.chofer_nombre}"
                                        data-chofer-dni="${config.chofer_dni}"
                                        data-vehiculo-placa="${config.vehiculo_placa}"
                                        data-licencia="${config.licencia_numero}"
                                        title="Seleccionar">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-warning text-white btn-editar-config" 
                                        data-config-id="${config.id}"
                                        data-chofer-id="${config.chofer_id}"
                                        data-chofer-nombre="${config.chofer_nombre}"
                                        data-chofer-dni="${config.chofer_dni}"
                                        data-vehiculo-placa="${config.vehiculo_placa}"
                                        data-vehiculo-marca="${config.vehiculo_marca || ''}"
                                        data-licencia="${config.licencia_numero}"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger text-white btn-eliminar-config" 
                                        data-config-id="${config.id}"
                                        data-chofer-nombre="${config.chofer_nombre}"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            });
        } else {
            tablaBody.append('<tr><td colspan="6" class="text-center">No hay configuraciones registradas.</td></tr>');
        }
    }
    
    // CORREGIDO: Actualizar select de chofer con VALUES correctos
    function actualizarSelectChofer(configuraciones) {
        const select = $("#select_chofer");
        const selectVehiculo = $("#select_vehiculo");
        const selectLicencia = $("#select_licencia");
        
        select.empty();
        selectVehiculo.empty();
        selectLicencia.empty();
        
        select.append('<option value="">Seleccione un conductor</option>');
        selectVehiculo.append('<option value="">Seleccione un vehículo</option>');
        selectLicencia.append('<option value="">Seleccione una licencia</option>');
        
        // Agrupar por chofer para evitar duplicados
        const choferes = {};
        configuraciones.forEach(config => {
            if (!choferes[config.chofer_id]) {
                choferes[config.chofer_id] = {
                    id: config.chofer_id,
                    nombre: config.chofer_nombre,
                    dni: config.chofer_dni,
                    configuraciones: []
                };
            }
            choferes[config.chofer_id].configuraciones.push(config);
        });
        
        Object.values(choferes).forEach(chofer => {
            // CORREGIDO: El VALUE debe ser el ID, no el nombre
            select.append(`<option value="${chofer.id}" 
                data-nombre="${chofer.nombre}" 
                data-dni="${chofer.dni}">
                ${chofer.nombre} - ${chofer.dni}
            </option>`);
        });
        
        // CORREGIDO: Event listener simplificado y corregido
        select.off('change').on('change', function() {
            const choferId = $(this).val(); // Ahora esto SÍ será el ID
            const choferOption = $(this).find('option:selected');
            const choferNombre = choferOption.data('nombre');
            const choferDni = choferOption.data('dni');
            
            console.log('=== EVENT LISTENER DISPARADO ===');
            console.log('Chofer ID seleccionado:', choferId);
            console.log('Chofer nombre:', choferNombre);
            console.log('Chofer DNI:', choferDni);
            
            // Actualizar Vue con los datos del chofer seleccionado
            if (window.app && choferId) {
                window.app.transporte.chofer_id = choferId;
                window.app.transporte.chofer_datos = choferNombre;
                // Limpiar vehículo y licencia inicialmente
                window.app.transporte.veiculo = '';
                window.app.transporte.chofer_dni = '';
            }
            
            if (choferId) {
                console.log('Enviando petición para chofer_id:', choferId);
                
                // Buscar configuraciones del chofer seleccionado
                $.ajax({
                    url: _URL + "/ajs/get/conductor/configuraciones/chofer",
                    type: "POST",
                    data: { chofer_id: choferId },
                    dataType: "json",
                    success: (response) => {
                        console.log('Respuesta configuraciones chofer:', response);
                        
                        if (response.status && response.data.length > 0) {
                            // Limpiar selects
                            selectVehiculo.empty();
                            selectLicencia.empty();
                            
                            selectVehiculo.append('<option value="">Seleccione un vehículo</option>');
                            selectLicencia.append('<option value="">Seleccione una licencia</option>');
                            
                            // Llenar opciones de vehículos y licencias
                            response.data.forEach(config => {
                                selectVehiculo.append(`<option value="${config.vehiculo_placa}" 
                                    data-config-id="${config.id}" 
                                    data-licencia="${config.licencia_numero}">
                                    ${config.vehiculo_placa} - ${config.vehiculo_marca || 'Sin marca'}
                                </option>`);
                                
                                selectLicencia.append(`<option value="${config.licencia_numero}" 
                                    data-config-id="${config.id}" 
                                    data-vehiculo="${config.vehiculo_placa}">
                                    ${config.licencia_numero}
                                </option>`);
                            });
                            
                            // Si solo hay una configuración, seleccionarla automáticamente
                            if (response.data.length === 1) {
                                const config = response.data[0];
                                console.log('Seleccionando automáticamente configuración única:', config);
                                
                                // Seleccionar en los selects
                                selectVehiculo.val(config.vehiculo_placa);
                                selectLicencia.val(config.licencia_numero);
                                
                                // Actualizar Vue
                                if (window.app) {
                                    window.app.transporte.veiculo = config.vehiculo_placa;
                                    window.app.transporte.chofer_dni = config.licencia_numero;
                                    
                                    console.log('Estado Vue actualizado:', window.app.transporte);
                                }
                            }
                        } else {
                            console.log('No se encontraron configuraciones para el chofer');
                        }
                    },
                    error: (xhr, status, error) => {
                        console.error("Error al cargar configuraciones del chofer:", error);
                    }
                });
            } else {
                // Limpiar selects y Vue si no hay chofer seleccionado
                selectVehiculo.empty().append('<option value="">Seleccione un vehículo</option>');
                selectLicencia.empty().append('<option value="">Seleccione una licencia</option>');
                
                if (window.app) {
                    window.app.transporte.chofer_id = '';
                    window.app.transporte.chofer_datos = '';
                    window.app.transporte.veiculo = '';
                    window.app.transporte.chofer_dni = '';
                }
            }
        });

        // Event listener para cuando cambie el vehículo - sincronizar licencia
        selectVehiculo.off('change').on('change', function() {
            const vehiculoSeleccionado = $(this).val();
            const licenciaCorrespondiente = $(this).find('option:selected').data('licencia');
            
            console.log('Vehículo cambiado:', vehiculoSeleccionado, 'Licencia correspondiente:', licenciaCorrespondiente);
            
            if (vehiculoSeleccionado && licenciaCorrespondiente) {
                selectLicencia.val(licenciaCorrespondiente);
                
                // Actualizar Vue
                if (window.app) {
                    window.app.transporte.veiculo = vehiculoSeleccionado;
                    window.app.transporte.chofer_dni = licenciaCorrespondiente;
                }
            } else if (!vehiculoSeleccionado) {
                selectLicencia.val("");
                if (window.app) {
                    window.app.transporte.veiculo = "";
                    window.app.transporte.chofer_dni = "";
                }
            }
        });

        // Event listener para cuando cambie la licencia - sincronizar vehículo
        selectLicencia.off('change').on('change', function() {
            const licenciaSeleccionada = $(this).val();
            const vehiculoCorrespondiente = $(this).find('option:selected').data('vehiculo');
            
            console.log('Licencia cambiada:', licenciaSeleccionada, 'Vehículo correspondiente:', vehiculoCorrespondiente);
            
            if (licenciaSeleccionada && vehiculoCorrespondiente) {
                selectVehiculo.val(vehiculoCorrespondiente);
                
                // Actualizar Vue
                if (window.app) {
                    window.app.transporte.veiculo = vehiculoCorrespondiente;
                    window.app.transporte.chofer_dni = licenciaSeleccionada;
                }
            } else if (!licenciaSeleccionada) {
                selectVehiculo.val("");
                if (window.app) {
                    window.app.transporte.veiculo = "";
                    window.app.transporte.chofer_dni = "";
                }
            }
        });
    }

    // Event listener para seleccionar configuración CORREGIDO
    $(document).on('click', '.btn-seleccionar-config', function() {
        const configId = $(this).data('config-id');
        const choferId = $(this).data('chofer-id');
        const choferNombre = $(this).data('chofer-nombre');
        const choferDni = $(this).data('chofer-dni');
        const vehiculoPlaca = $(this).data('vehiculo-placa');
        const licencia = $(this).data('licencia');
        
        // Actualizar la instancia de Vue
        if (window.app) {
            window.app.transporte.chofer_id = choferId;
            window.app.transporte.chofer_datos = choferNombre;
            window.app.transporte.chofer_dni = licencia; // La licencia va en chofer_dni
            window.app.transporte.veiculo = vehiculoPlaca;
        }
        
        // También actualizar los selects del formulario principal
        $("#select_chofer").val(choferId);
        
        // Cargar las opciones del chofer y luego seleccionar los valores específicos
        $.ajax({
            url: _URL + "/ajs/get/conductor/configuraciones/chofer",
            type: "POST",
            data: { chofer_id: choferId },
            dataType: "json",
            success: (response) => {
                if (response.status && response.data.length > 0) {
                    const selectVehiculo = $("#select_vehiculo");
                    const selectLicencia = $("#select_licencia");
                    
                    // Limpiar y llenar opciones
                    selectVehiculo.empty().append('<option value="">Seleccione un vehículo</option>');
                    selectLicencia.empty().append('<option value="">Seleccione una licencia</option>');
                    
                    response.data.forEach(config => {
                        selectVehiculo.append(`<option value="${config.vehiculo_placa}" 
                            data-config-id="${config.id}" 
                            data-licencia="${config.licencia_numero}">
                            ${config.vehiculo_placa} - ${config.vehiculo_marca || 'Sin marca'}
                        </option>`);
                        
                        selectLicencia.append(`<option value="${config.licencia_numero}" 
                            data-config-id="${config.id}" 
                            data-vehiculo="${config.vehiculo_placa}">
                            ${config.licencia_numero}
                        </option>`);
                    });
                    
                    // Seleccionar los valores específicos
                    selectVehiculo.val(vehiculoPlaca);
                    selectLicencia.val(licencia);
                }
            }
        });
        
        $("#choferModal").modal('hide');
        alertExito(`Conductor seleccionado: ${choferNombre} - Vehículo: ${vehiculoPlaca}`);
    });

    // Event listener para EDITAR configuración
    $(document).on('click', '.btn-editar-config', function() {
        editandoConfiguracion = true;
        configuracionEditandoId = $(this).data('config-id');
        
        // Llenar el formulario con los datos existentes
        $("#choferConfigId").val(configuracionEditandoId);
        $("#dniChofer").val($(this).data('chofer-dni'));
        $("#nombreCompleto").val($(this).data('chofer-nombre'));
        $("#placaVehiculo").val($(this).data('vehiculo-placa'));
        $("#marcaVehiculo").val($(this).data('vehiculo-marca'));
        $("#numeroLicencia").val($(this).data('licencia'));
        
        // Cambiar el texto del botón
        $("#conductorForm button[type='submit']").html('<i class="fas fa-save"></i> Actualizar Configuración');
        
        // Scroll hacia el formulario
        $("#conductorForm")[0].scrollIntoView({ behavior: 'smooth' });
    });

    // Event listener para ELIMINAR configuración
    $(document).on('click', '.btn-eliminar-config', function() {
        const configId = $(this).data('config-id');
        const choferNombre = $(this).data('chofer-nombre');
        
        // Confirmación con SweetAlert o confirm nativo
        if (confirm(`¿Estás seguro de eliminar la configuración de ${choferNombre}?`)) {
            eliminarConfiguracion(configId);
        }
    });

    // Función para eliminar configuración
    function eliminarConfiguracion(configId) {
        $.ajax({
            url: _URL + "/ajs/delete/conductor/configuracion",
            type: "POST",
            data: { config_id: configId },
            dataType: "json",
            success: (response) => {
                if (response.status) {
                    cargarConfiguracionesConductores();
                    alertExito("Configuración eliminada correctamente");
                } else {
                    alertAdvertencia(response.message || "Error al eliminar la configuración");
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al eliminar:", error);
                alertAdvertencia("Error al eliminar la configuración");
            }
        });
    }

    // Agregar funcionalidad de búsqueda de DNI
    $("#dniChofer").on('blur', function() {
        const dni = $(this).val().trim();
        if (dni.length === 8) {
            buscarDniChofer(dni);
        }
    });

    // Función para buscar DNI del chofer
    function buscarDniChofer(dni) {
        $("#loader-menor").show();
        
        $.ajax({
            url: _URL + "/ajs/prealerta/doc/cliente",
            type: "POST",
            data: { doc: dni },
            dataType: "json",
            success: (resp) => {
                $("#loader-menor").hide();
                
                if (resp.success) {
                    const nombreCompleto = resp.nombres + ' ' + (resp.apellidoPaterno || '') + ' ' + (resp.apellidoMaterno || '');
                    $("#nombreCompleto").val(nombreCompleto);
                } else {
                    console.log("DNI no encontrado en RENIEC");
                }
            },
            error: (xhr, status, error) => {
                $("#loader-menor").hide();
                console.error("Error al buscar DNI:", error);
            }
        });
    }
    
    // Formulario de conductor MEJORADO
    $("#conductorForm").on('submit', function(e) {
        e.preventDefault();
        
        const data = {
            chofer_nombre: $("#nombreCompleto").val().trim(),
            chofer_dni: $("#dniChofer").val().trim(),
            vehiculo_placa: $("#placaVehiculo").val().trim(),
            vehiculo_marca: $("#marcaVehiculo").val().trim(),
            licencia_numero: $("#numeroLicencia").val().trim()
        };

        // Validaciones
        if (!data.chofer_nombre || !data.chofer_dni || !data.vehiculo_placa || !data.licencia_numero) {
            alertAdvertencia("Por favor, complete todos los campos requeridos");
            return;
        }

        if (data.chofer_dni.length !== 8) {
            alertAdvertencia("El DNI debe tener 8 dígitos");
            return;
        }
        
        // Determinar si es edición o creación
        let url = _URL + "/ajs/save/conductor/configuracion";
        if (editandoConfiguracion && configuracionEditandoId) {
            url = _URL + "/ajs/update/conductor/configuracion";
            data.config_id = configuracionEditandoId;
        }
        
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "json",
            success: (response) => {
                console.log("Respuesta guardar:", response);
                if (response.status) {
                    cargarConfiguracionesConductores();
                    limpiarFormularioConductor();
                    resetearModoEdicion();
                    
                    const mensaje = editandoConfiguracion ? "actualizada" : "guardada";
                    alertExito(`Configuración de conductor ${mensaje} correctamente`);
                } else {
                    alertAdvertencia(response.message || "Error al procesar la configuración");
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al guardar:", error);
                console.error("Respuesta del servidor:", xhr.responseText);
                alertAdvertencia("Error al procesar la configuración");
            }
        });
    });
    
    // Función para limpiar formulario
    function limpiarFormularioConductor() {
        $("#nombreCompleto").val('');
        $("#dniChofer").val('');
        $("#placaVehiculo").val('');
        $("#marcaVehiculo").val('');
        $("#numeroLicencia").val('');
        $("#choferConfigId").val('');
    }
    
    // Función para resetear modo edición
    function resetearModoEdicion() {
        editandoConfiguracion = false;
        configuracionEditandoId = null;
        $("#conductorForm button[type='submit']").html('<i class="fas fa-save"></i> Guardar Configuración');
    }

    // Cargar configuraciones al abrir el modal
    $("#choferModal").on('show.bs.modal', function() {
        cargarConfiguracionesConductores();
        limpiarFormularioConductor();
        resetearModoEdicion();
    });

    // Función global para buscar DNI (para compatibilidad)
    window.buscarDniChofer = function(dni) {
        buscarDniChofer(dni);
    };
    
    // AGREGADO: Función para debug y verificar sincronización
    function verificarSincronizacionVue() {
        if (window.app) {
            console.log("Estado actual de transporte:", window.app.transporte);
            console.log("Chofer select:", $("#select_chofer").val());
            console.log("Vehículo select:", $("#select_vehiculo").val());
            console.log("Licencia select:", $("#select_licencia").val());
        }
    }

    // AGREGADO: Hacer disponible globalmente para debug
    window.verificarSincronizacionVue = verificarSincronizacionVue;
});