// public\js\guia-remision\chofer-configuraciones.js
$(document).ready(function() {
    console.log('=== INICIANDO SISTEMA DE CONFIGURACIONES ===');
    
    // Variables globales
    let editandoConfiguracion = false;
    let configuracionEditandoId = null;
    
    // PASO 1: Cargar choferes desde la tabla guia_choferes
    function cargarChoferes() {
        console.log('Cargando choferes...');
        $.ajax({
            url: _URL + "/ajs/get/chofer", // Esta ruta debe existir y devolver los choferes
            type: "GET",
            dataType: "json",
            success: function(response) {
                console.log('Respuesta choferes:', response);
                if (response.status && response.data) {
                    llenarSelectChofer(response.data);
                } else {
                    console.error('Error en respuesta de choferes:', response);
                    // Si no hay endpoint para choferes, cargar desde configuraciones
                    cargarChoferesDesdeConfiguraciones();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar choferes:', error);
                // Fallback: cargar desde configuraciones
                cargarChoferesDesdeConfiguraciones();
            }
        });
    }
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
    
    // PASO 2: Fallback - Cargar choferes desde configuraciones
    function cargarChoferesDesdeConfiguraciones() {
        console.log('Cargando choferes desde configuraciones...');
        $.ajax({
            url: _URL + "/ajs/get/conductor/configuraciones",
            type: "GET",
            dataType: "json",
            success: function(response) {
                console.log('Configuraciones cargadas:', response);
                if (response.status && response.data) {
                    // Extraer choferes únicos de las configuraciones
                    const choferesUnicos = {};
                    response.data.forEach(config => {
                        if (!choferesUnicos[config.chofer_id]) {
                            choferesUnicos[config.chofer_id] = {
                                id: config.chofer_id,
                                nombre: config.chofer_nombre,
                                dni: config.chofer_dni
                            };
                        }
                    });
                    
                    const choferes = Object.values(choferesUnicos);
                    console.log('Choferes extraídos:', choferes);
                    llenarSelectChofer(choferes);
                    
                    // También actualizar la tabla del modal
                    actualizarTablaConfiguraciones(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar configuraciones:', error);
            }
        });
    }
    
    // PASO 3: Llenar el select de choferes
    function llenarSelectChofer(choferes) {
        const select = $("#select_chofer");
        select.empty();
        select.append('<option value="">Seleccione un chofer</option>');
        
        choferes.forEach(chofer => {
            select.append(`<option value="${chofer.id}" data-id="${chofer.id}" data-nombre="${chofer.nombre}" data-dni="${chofer.dni}">
                ${chofer.nombre} - ${chofer.dni}
            </option>`);
        });
        
        console.log('Select chofer llenado con', choferes.length, 'choferes');
        
        // REMOVER event listeners anteriores para evitar duplicados
        select.off('change.choferConfig');
        
        // AGREGAR event listener único
        select.on('change.choferConfig', function() {
            const choferId = $(this).val();
            const selectedOption = $(this).find('option:selected');
            const choferNombre = selectedOption.data('nombre');
            const choferDni = selectedOption.data('dni');
            
            console.log('=== CHOFER SELECCIONADO ===');
            console.log('ID:', choferId);
            console.log('Nombre:', choferNombre);
            console.log('DNI:', choferDni);
            
            // Limpiar selects de vehículo y licencia
            $('#select_vehiculo').empty().append('<option value="">Seleccione un vehículo</option>');
            $('#select_licencia').empty().append('<option value="">Seleccione una licencia</option>');
            
            // Actualizar Vue si existe
           if (window.app && choferId) {
        console.log('Actualizando Vue desde select chofer');
        window.app.transporte.chofer_id = choferId;           // ID para BD
        window.app.transporte.chofer_datos = choferNombre;    // Nombre para mostrar
        window.app.transporte.veiculo = '';
        window.app.transporte.chofer_dni = '';
        
        // Forzar actualización
        window.app.$forceUpdate();
        
        console.log('Estado actualizado:', window.app.transporte);
    }
            
            if (choferId && choferId !== '') {
                cargarConfiguracionesChofer(choferId);
            } else {
                // Limpiar Vue si no hay chofer seleccionado
                if (window.app) {
                    window.app.transporte.chofer_id = '';
                    window.app.transporte.chofer_datos = '';
                    window.app.transporte.veiculo = '';
                    window.app.transporte.chofer_dni = '';
                }
            }
        });
    }
    
    // PASO 4: Cargar configuraciones del chofer seleccionado
    function cargarConfiguracionesChofer(choferId) {
        console.log('Cargando configuraciones para chofer ID:', choferId);
        
        $.ajax({
            url: _URL + '/ajs/get/conductor/configuraciones/chofer',
            type: 'POST',
            data: {
                chofer_id: choferId
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta configuraciones chofer:', response);
                
                if (response.status && response.data && response.data.length > 0) {
                    procesarConfiguraciones(response.data);
                } else {
                    console.log('No se encontraron configuraciones para el chofer');
                    mostrarMensajeSinConfiguraciones();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener configuraciones:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                mostrarErrorConfiguraciones();
            }
        });
    }
    
    // PASO 5: Procesar y mostrar configuraciones
    function procesarConfiguraciones(configuraciones) {
        const selectVehiculo = $('#select_vehiculo');
        const selectLicencia = $('#select_licencia');
        
        // Limpiar selects
        selectVehiculo.empty().append('<option value="">Seleccione un vehículo</option>');
        selectLicencia.empty().append('<option value="">Seleccione una licencia</option>');
        
        // Crear sets para evitar duplicados
        const vehiculos = new Set();
        const licencias = new Set();
        
        configuraciones.forEach(function(config) {
            // Agregar vehículos únicos
            if (config.vehiculo_placa) {
                const vehiculoText = config.vehiculo_placa + (config.vehiculo_marca ? ' - ' + config.vehiculo_marca : '');
                vehiculos.add(JSON.stringify({
                    placa: config.vehiculo_placa,
                    texto: vehiculoText,
                    licencia: config.licencia_numero
                }));
            }
            
            // Agregar licencias únicas
            if (config.licencia_numero) {
                licencias.add(JSON.stringify({
                    numero: config.licencia_numero,
                    vehiculo: config.vehiculo_placa
                }));
            }
        });
        
        // Llenar select de vehículos
        vehiculos.forEach(function(vehiculoStr) {
            const vehiculo = JSON.parse(vehiculoStr);
            selectVehiculo.append(`<option value="${vehiculo.placa}" data-licencia="${vehiculo.licencia}">
                ${vehiculo.texto}
            </option>`);
        });
        
        // Llenar select de licencias
        licencias.forEach(function(licenciaStr) {
            const licencia = JSON.parse(licenciaStr);
            selectLicencia.append(`<option value="${licencia.numero}" data-vehiculo="${licencia.vehiculo}">
                ${licencia.numero}
            </option>`);
        });
        
        // Si solo hay una configuración, seleccionarla automáticamente
        if (configuraciones.length === 1) {
            const config = configuraciones[0];
            selectVehiculo.val(config.vehiculo_placa);
            selectLicencia.val(config.licencia_numero);
            
            // Actualizar Vue
            if (window.app) {
                window.app.transporte.veiculo = config.vehiculo_placa;
                window.app.transporte.chofer_dni = config.licencia_numero;
            }
            
            console.log('Configuración única seleccionada automáticamente');
        }
        
        // Event listeners para sincronización
        configurarSincronizacionSelects();
        
        console.log('Configuraciones procesadas exitosamente');
    }
    
    // PASO 6: Configurar sincronización entre selects
    function configurarSincronizacionSelects() {
        const selectVehiculo = $('#select_vehiculo');
        const selectLicencia = $('#select_licencia');
        
        // Remover listeners anteriores
        selectVehiculo.off('change.sync');
        selectLicencia.off('change.sync');
        
        // Sincronizar vehículo -> licencia
        selectVehiculo.on('change.sync', function() {
            const vehiculoSeleccionado = $(this).val();
            const licenciaCorrespondiente = $(this).find('option:selected').data('licencia');
            
            if (vehiculoSeleccionado && licenciaCorrespondiente) {
                selectLicencia.val(licenciaCorrespondiente);
                
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
        
        // Sincronizar licencia -> vehículo
        selectLicencia.on('change.sync', function() {
            const licenciaSeleccionada = $(this).val();
            const vehiculoCorrespondiente = $(this).find('option:selected').data('vehiculo');
            
            if (licenciaSeleccionada && vehiculoCorrespondiente) {
                selectVehiculo.val(vehiculoCorrespondiente);
                
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
    
    // Funciones auxiliares
    function mostrarMensajeSinConfiguraciones() {
        $('#select_vehiculo').append('<option value="">No hay vehículos configurados</option>');
        $('#select_licencia').append('<option value="">No hay licencias configuradas</option>');
    }
    
    function mostrarErrorConfiguraciones() {
        $('#select_vehiculo').append('<option value="">Error al cargar vehículos</option>');
        $('#select_licencia').append('<option value="">Error al cargar licencias</option>');
    }
    
    // GESTIÓN DEL MODAL DE CONFIGURACIONES
    function actualizarTablaConfiguraciones(configuraciones) {
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

    // Agregar este event listener después del event listener de seleccionar
$(document).on('click', '.btn-editar-config', function() {
    const configId = $(this).data('config-id');
    const choferId = $(this).data('chofer-id');
    const choferNombre = $(this).data('chofer-nombre');
    const choferDni = $(this).data('chofer-dni');
    const vehiculoPlaca = $(this).data('vehiculo-placa');
    const vehiculoMarca = $(this).data('vehiculo-marca');
    const licencia = $(this).data('licencia');
    
    console.log('Editando configuración ID:', configId);
    
    // Llenar el formulario con los datos existentes
    $("#dniChofer").val(choferDni);
    $("#nombreCompleto").val(choferNombre);
    $("#placaVehiculo").val(vehiculoPlaca);
    $("#marcaVehiculo").val(vehiculoMarca);
    $("#numeroLicencia").val(licencia);
    $("#choferConfigId").val(configId);
    
    // Activar modo edición
    editandoConfiguracion = true;
    configuracionEditandoId = configId;
    
    // Cambiar el texto del botón
    $("#conductorForm button[type='submit']").html('<i class="fas fa-save"></i> Actualizar Configuración');
    
    // Scroll hacia el formulario para mejor UX
    $(".modal-body").animate({
        scrollTop: 0
    }, 300);
    
    console.log('Modo edición activado para configuración:', configId);
});
// Event listener para eliminar configuración con SweetAlert2
$(document).on('click', '.btn-eliminar-config', function() {
    const configId = $(this).data('config-id');
    const choferNombre = $(this).data('chofer-nombre');
    
    // Confirmar eliminación con SweetAlert2
    Swal.fire({
        title: "¿Está seguro?",
        text: `¿Desea eliminar la configuración del conductor ${choferNombre}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        reverseButtons: true // Para que el botón de cancelar aparezca primero
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading mientras se procesa
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Realizar la eliminación
            $.ajax({
                url: _URL + "/ajs/delete/conductor/configuracion",
                type: "POST",
                data: {
                    config_id: configId
                },
                dataType: "json",
                success: (response) => {
                    Swal.close(); // Cerrar el loading
                    
                    if (response.status) {
                        // Usar alertExito (que ya debe ser SweetAlert2)
                        alertExito("Configuración eliminada correctamente");
                        cargarChoferesDesdeConfiguraciones(); // Recargar la tabla
                    } else {
                        alertAdvertencia(response.message || "Error al eliminar la configuración");
                    }
                },
                error: (xhr, status, error) => {
                    Swal.close(); // Cerrar el loading
                    console.error("Error al eliminar:", error);
                    alertAdvertencia("Error al eliminar la configuración");
                }
            });
        }
    });
});
    
    // Event listeners para el modal
   $(document).on('click', '.btn-seleccionar-config', function() {
    const choferId = $(this).data('chofer-id');
    const choferNombre = $(this).data('chofer-nombre');
    const choferDni = $(this).data('chofer-dni');
    const vehiculoPlaca = $(this).data('vehiculo-placa');
    const licencia = $(this).data('licencia');
    
    // Seleccionar en el formulario principal
    $("#select_chofer").val(choferId).trigger('change.choferConfig');
    
    // Esperar a que se carguen las configuraciones y luego seleccionar
    setTimeout(() => {
        $("#select_vehiculo").val(vehiculoPlaca);
        $("#select_licencia").val(licencia);
        
        // CORREGIDO: Actualizar Vue con todos los datos necesarios
        if (window.app) {
            console.log('Actualizando Vue con datos del chofer');
            window.app.transporte.chofer_id = choferId;           // ID para BD
            window.app.transporte.chofer_datos = choferNombre;    // Nombre para mostrar
            window.app.transporte.chofer_dni = licencia;          // Licencia
            window.app.transporte.veiculo = vehiculoPlaca;        // Placa
            
            // Forzar actualización de Vue
            window.app.$forceUpdate();
            
            console.log('Estado actual de transporte:', window.app.transporte);
        }
    }, 500);
    
    $("#choferModal").modal('hide');
    alertExito(`Conductor seleccionado: ${choferNombre} - Vehículo: ${vehiculoPlaca}`);
});

    
    // Formulario de configuración
    $("#conductorForm").on('submit', function(e) {
        e.preventDefault();
        
        const data = {
            chofer_nombre: $("#nombreCompleto").val().trim(),
            chofer_dni: $("#dniChofer").val().trim(),
            vehiculo_placa: $("#placaVehiculo").val().trim(),
            vehiculo_marca: $("#marcaVehiculo").val().trim(),
            licencia_numero: $("#numeroLicencia").val().trim()
        };

        if (!data.chofer_nombre || !data.chofer_dni || !data.vehiculo_placa || !data.licencia_numero) {
            alertAdvertencia("Por favor, complete todos los campos requeridos");
            return;
        }

        if (data.chofer_dni.length !== 8) {
            alertAdvertencia("El DNI debe tener 8 dígitos");
            return;
        }
        
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
                if (response.status) {
                    cargarChoferesDesdeConfiguraciones(); // Recargar todo
                    limpiarFormulario();
                    resetearModoEdicion();
                    
                    const mensaje = editandoConfiguracion ? "actualizada" : "guardada";
                    alertExito(`Configuración ${mensaje} correctamente`);
                } else {
                    alertAdvertencia(response.message || "Error al procesar la configuración");
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al guardar:", error);
                alertAdvertencia("Error al procesar la configuración");
            }
        });
    });
    
    function limpiarFormulario() {
        $("#nombreCompleto, #dniChofer, #placaVehiculo, #marcaVehiculo, #numeroLicencia, #choferConfigId").val('');
    }
    
    function resetearModoEdicion() {
        editandoConfiguracion = false;
        configuracionEditandoId = null;
        $("#conductorForm button[type='submit']").html('<i class="fas fa-save"></i> Guardar Configuración');
    }
    
    // Cargar al abrir modal
    $("#choferModal").on('show.bs.modal', function() {
        cargarChoferesDesdeConfiguraciones();
        limpiarFormulario();
        resetearModoEdicion();
    });
    
    // Función global para debug
    window.debugChoferConfig = function() {
        console.log('=== DEBUG CHOFER CONFIG ===');
        console.log('Select chofer value:', $("#select_chofer").val());
        console.log('Select vehiculo value:', $("#select_vehiculo").val());
        console.log('Select licencia value:', $("#select_licencia").val());
        if (window.app) {
            console.log('Vue transporte:', window.app.transporte);
        }
    };
      // Función global para buscar DNI (para compatibilidad)
    window.buscarDniChofer = function(dni) {
        buscarDniChofer(dni);
    };
    
    
    // INICIALIZAR
    console.log('Iniciando carga de choferes...');
    cargarChoferes();
});