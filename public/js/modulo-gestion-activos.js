// Variables globales para gestión de modales
window.modoEdicion = false;
window.activoEditandoId = null;

// Funciones para gestionar modales superpuestos
function abrirModalRegistro() {
    $("#modalMotivo").modal('hide');
    setTimeout(() => {
        $("#modalRegistroActivo").modal('show');
    }, 300);
}

function abrirModalMotivos() {
    $("#modalRegistroActivo").modal('hide');
    setTimeout(() => {
        $("#modalMotivo").modal('show');
    }, 300);
}

// FUNCIÓN PARA GENERAR EL CONTADOR DE DÍAS UNIFORME
function generarContadorDias(row) {
    const fechaIngreso = row.fecha_ingreso ? new Date(row.fecha_ingreso) : null;
    const fechaActual = new Date();

    // Si está confirmado
    if (row.estado === 'CONFIRMADO') {
        return '<span class="contador-dias confirmado"><i class="fas fa-check"></i> OK</span>';
    }

    // Si no hay fecha de ingreso
    if (!fechaIngreso || row.fecha_ingreso === '0000-00-00') {
        return '<span class="contador-dias sin-fecha">N/A</span>';
    }

    // Calcular días restantes
    const diasRestantes = Math.ceil((fechaIngreso - fechaActual) / (1000 * 60 * 60 * 24));

    if (diasRestantes < 0) {
        // Vencido
        return `<span class="contador-dias vencido"><i class="fas fa-exclamation-triangle"></i> ${diasRestantes}</span>`;
    } else if (diasRestantes <= 3) {
        // Urgente (3 días o menos)
        return `<span class="contador-dias urgente"><i class="fas fa-clock"></i> ${diasRestantes}</span>`;
    } else {
        // Normal (más de 3 días)
        return `<span class="contador-dias normal">${diasRestantes}</span>`;
    }
}

$(document).ready(function () {
    // Instancia de Vue para el modal de registro
    const appModal = new Vue({
        el: "#modalRegistroActivo",
        data: {
            maquinaSerieModal: {
                cliente_Rsocial: "",
                buscar_serie: '',
                num_serie: '',
                marc: '',
                model: '',
                equipo: '',
                num_doc: "",
                fecha_salida: new Date().toISOString().split('T')[0]
            }
        },
        mounted() {
            // Configurar fecha por defecto
            document.getElementById('fecha_salida_modal').value = this.maquinaSerieModal.fecha_salida;

            // Agregar eventos para ocultar mensajes de error al escribir
            $('#modalRegistroActivo input, #modalRegistroActivo select, #modalRegistroActivo textarea').on('input change', function() {
                const id = $(this).attr('id');
                if (id) {
                    $(`#${id}-error`).text('');
                    $(this).removeClass('is-invalid');
                }
            });

            // INICIALIZAR AUTOCOMPLETE DESPUÉS DE QUE EL MODAL ESTÉ MONTADO
            this.$nextTick(() => {
                this.initializeAutocomplete();
            });
        },
        methods: {
            buscarDocumentSSModal() {
                const docLength = this.maquinaSerieModal.num_doc.length;
                if (docLength === 8 || docLength === 11) {
                    $("#loader-menor").show();
                    this.maquinaSerieModal.dir_pos = 1;

                    _ajax("/ajs/prealerta/doc/cliente", "POST", {
                        doc: this.maquinaSerieModal.num_doc
                    }, (resp) => {
                        $("#loader-menor").hide();
                        console.log(resp);

                        if (docLength === 8) {
                            if (resp.success) {
                                this.maquinaSerieModal.cliente_Rsocial = `${resp.nombres} ${resp.apellidoPaterno || ''} ${resp.apellidoMaterno || ''}`;
                            } else {
                                alertAdvertencia("Documento no encontrado");
                            }
                        } else if (docLength === 11) {
                            if (resp.razonSocial) {
                                this.maquinaSerieModal.cliente_Rsocial = resp.razonSocial;
                            } else {
                                alertAdvertencia("RUC no encontrado");
                            }
                        }
                    });
                } else {
                    alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
                }
            },
            limpiarFormularioModal() {
                this.maquinaSerieModal = {
                    cliente_Rsocial: "",
                    buscar_serie: '',
                    num_serie: '',
                    marc: '',
                    model: '',
                    equipo: '',
                    num_doc: "",
                    fecha_salida: new Date().toISOString().split('T')[0]
                };
                $('#motivo_modal').val('');
                $('#fecha_salida_modal').val(this.maquinaSerieModal.fecha_salida);
                $('#fecha_ingreso_modal').val('');
                $('#observaciones_modal').val('');
                // Limpiar errores
                $('#modalRegistroActivo .text-danger').text('');
                $('#modalRegistroActivo .is-invalid').removeClass('is-invalid');

                // Resetear modo edición
                modoEdicion = false;
                activoEditandoId = null;
                $('#modalTitulo').text('Agregar Nuevo Registro de Activo');
                $('#submitRegistroModal').text('Guardar Registro');
            },
            // MÉTODO MEJORADO PARA INICIALIZAR EL AUTOCOMPLETE
            initializeAutocomplete() {
                const self = this;

                // Destruir autocomplete existente si existe
                if ($("#input_buscar_Dataseries_modal").hasClass("ui-autocomplete-input")) {
                    $("#input_buscar_Dataseries_modal").autocomplete("destroy");
                }

                $("#input_buscar_Dataseries_modal").autocomplete({
                    source: function (request, response) {
                        console.log("Autocomplete source called with term:", request.term);
                        $.ajax({
                            url: _URL + "/ajs/buscar/maquina/datos",
                            type: "GET",
                            data: {
                                term: request.term || '',
                                startsWith: true
                            },
                            success: function (data) {
                                console.log("Autocomplete response:", data);
                                let results = JSON.parse(data);
                                if (!request.term) {
                                    response(results);
                                } else {
                                    results = results.filter(item =>
                                        item.label.toString().toLowerCase().startsWith(request.term.toLowerCase())
                                    );
                                    response(results);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Autocomplete error:", error);
                            }
                        });
                    },
                    minLength: 0,
                    appendTo: ".autocomplete-container",
                    position: {
                        my: "left top",
                        at: "left bottom",
                        collision: "flip",
                        within: "#modalRegistroActivo"
                    },
                    select: function (event, ui) {
                        event.preventDefault();
                        console.log("Item selected:", ui.item);
                        self.maquinaSerieModal.buscar_serie = '';
                        self.maquinaSerieModal.num_serie = ui.item.value;
                        self.maquinaSerieModal.marc = ui.item.marca;
                        self.maquinaSerieModal.model = ui.item.modelo;
                        self.maquinaSerieModal.equipo = ui.item.equipo;
                        return false;
                    },
                    open: function() {
                        console.log("Autocomplete opened");
                        $('.ui-autocomplete').css({
                            'z-index': 1060,
                            'max-width': '500px',
                            'min-width': '300px'
                        });
                    },
                    close: function() {
                        console.log("Autocomplete closed");
                    }
                }).on('focus', function() {
                    console.log("Input focused, triggering search");
                    $(this).autocomplete('search', '');
                });
            }
        }
    });

    // Abrir modal de registro
    $("#btnAbrirModalRegistro").click(function(e) {
        e.preventDefault();
        e.stopPropagation();

        appModal.limpiarFormularioModal();
        cargarMotivosModal();
        $("#modalRegistroActivo").modal('show');

        setTimeout(() => {
            appModal.initializeAutocomplete();
        }, 500);

        return false;
    });

    // FUNCIÓN CORREGIDA PARA CARGAR MOTIVOS CON COMPARACIÓN CASE-INSENSITIVE
    function cargarMotivosModal() {
        console.log("Cargando motivos...");
        $.get(_URL + "/ajs/get/motivos", function (data) {
            console.log("Respuesta motivos raw:", data);

            let options = '<option value="">Seleccione un motivo</option>';
            let resp;

            try {
                if (typeof data === 'string') {
                    resp = JSON.parse(data);
                } else {
                    resp = data;
                }

                console.log("Motivos parseados:", resp);

                if (resp.status && resp.data) {
                    resp = resp.data;
                }

                if (Array.isArray(resp)) {
                    $.each(resp, function (i, v) {
                        if (v && v.nombre) {
                            options += `<option value="${v.nombre}">${v.nombre}</option>`;
                        }
                    });
                } else {
                    console.error("La respuesta no es un array:", resp);
                }

            } catch (e) {
                console.error("Error al parsear motivos:", e);
                console.error("Data recibida:", data);
            }

            $('#motivo_modal').html(options);
            console.log("Options generadas:", options);

        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar los motivos: " + textStatus, errorThrown);
            console.error("Response:", jqXHR.responseText);
            alert("No se pudo cargar los motivos. Por favor, intenta nuevamente.");
        });
    }

    // FUNCIÓN PARA CARGAR DATOS EN MODO EDICIÓN CON SELECCIÓN CORRECTA DE MOTIVO
    function cargarDatosParaEdicion(datos) {
        console.log("Cargando datos para edición:", datos);

        // Cargar datos básicos
        appModal.maquinaSerieModal.cliente_Rsocial = datos.cliente_razon_social;
        appModal.maquinaSerieModal.marc = datos.marca;
        appModal.maquinaSerieModal.model = datos.modelo;
        appModal.maquinaSerieModal.num_serie = datos.numero_serie;
        appModal.maquinaSerieModal.equipo = datos.equipo;

        $('#fecha_salida_modal').val(datos.fecha_salida);
        $('#fecha_ingreso_modal').val(datos.fecha_ingreso);
        $('#observaciones_modal').val(datos.observaciones || '');

        // CARGAR MOTIVOS Y LUEGO SELECCIONAR EL CORRECTO
        cargarMotivosModal();

        // Esperar a que se carguen los motivos y luego seleccionar
        setTimeout(() => {
            console.log("Intentando seleccionar motivo:", datos.motivo);

            // Buscar el motivo de forma case-insensitive
            let motivoEncontrado = false;
            $('#motivo_modal option').each(function() {
                const optionValue = $(this).val();
                const optionText = $(this).text();

                console.log("Comparando:", datos.motivo, "con", optionValue, "y", optionText);

                // Comparación case-insensitive
                if (optionValue.toLowerCase() === datos.motivo.toLowerCase() ||
                    optionText.toLowerCase() === datos.motivo.toLowerCase()) {
                    $(this).prop('selected', true);
                    $('#motivo_modal').val(optionValue);
                    motivoEncontrado = true;
                    console.log("Motivo seleccionado:", optionValue);
                    return false; // break del each
                }
            });

            if (!motivoEncontrado) {
                console.warn("No se encontró el motivo:", datos.motivo);
                console.log("Opciones disponibles:", $('#motivo_modal option').map(function() {
                    return $(this).val();
                }).get());
            }

            // Trigger change event para asegurar que se actualice
            $('#motivo_modal').trigger('change');
        }, 500);
    }

    function mostrarErrorValidacionModal(campo, mensaje) {
        $(`#${campo}-error`).text(mensaje);
        $(`#${campo}`).addClass('is-invalid');
    }

    function limpiarErroresModal() {
        $('#modalRegistroActivo .text-danger').text('');
        $('#modalRegistroActivo .is-invalid').removeClass('is-invalid');
    }

    // Enviar formulario del modal (AGREGAR O EDITAR)
    $("#submitRegistroModal").click(function () {
        limpiarErroresModal();
        $(this).prop('disabled', true);

        const data = {
            cliente_razon_social: appModal.maquinaSerieModal.cliente_Rsocial,
            marca: appModal.maquinaSerieModal.marc,
            modelo: appModal.maquinaSerieModal.model,
            numero_serie: appModal.maquinaSerieModal.num_serie,
            equipo: appModal.maquinaSerieModal.equipo,
            motivo: $('#motivo_modal').val(),
            fecha_salida: $('#fecha_salida_modal').val(),
            fecha_ingreso: $('#fecha_ingreso_modal').val(),
            observaciones: $('#observaciones_modal').val()
        };

        // Si estamos en modo edición, agregar el ID
        if (modoEdicion && activoEditandoId) {
            data.id = activoEditandoId;
        }

        let errores = {};
        if (!data.cliente_razon_social) errores.cliente_razon_social_modal = "El nombre del cliente es requerido";
        if (!data.marca) errores.marca_modal = "La marca es requerida";
        if (!data.modelo) errores.modelo_modal = "El modelo es requerido";
        if (!data.numero_serie) errores.numero_serie_modal = "El número de serie es requerido";
        if (!data.equipo) errores.equipo_modal = "El equipo es requerido";
        if (!data.motivo) errores.motivo_modal = "El motivo es requerido";

        if (Object.keys(errores).length > 0) {
            for (let campo in errores) {
                mostrarErrorValidacionModal(campo, errores[campo]);
            }
            $(this).prop('disabled', false);
            return;
        }

        // Determinar URL y mensaje según el modo
        const url = modoEdicion ?
            _URL + "/ajs/gestion/activos/update" :
            _URL + "/ajs/gestion/activos/add";
        const successMessage = modoEdicion ? "Activo actualizado correctamente" : "Activo agregado correctamente";

        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function (response) {
                console.log("Full response:", response);
                try {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse.res) {
                        Swal.fire({
                            title: "¡Éxito!",
                            text: jsonResponse.msg || successMessage,
                            icon: "success",
                            confirmButtonText: "OK",
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#modalRegistroActivo").modal('hide');
                                tabla_clientes.ajax.reload(null, false);
                                appModal.limpiarFormularioModal();
                            }
                        });
                    } else {
                        if (jsonResponse.errores) {
                            for (let campo in jsonResponse.errores) {
                                mostrarErrorValidacionModal(campo + '_modal', jsonResponse.errores[campo]);
                            }
                        } else {
                            Swal.fire({
                                title: "¡Error!",
                                text: jsonResponse.msg,
                                icon: "error",
                                customClass: {
                                    container: 'swal-high-zindex'
                                }
                            });
                        }
                    }
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    Swal.fire({
                        title: "¡Error!",
                        text: "Hubo un problema al procesar la respuesta del servidor.",
                        icon: "error",
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                Swal.fire({
                    title: "¡Error!",
                    text: "No se pudo procesar la solicitud. Intenta nuevamente.",
                    icon: "error",
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
            },
            complete: function () {
                $("#submitRegistroModal").prop('disabled', false);
            }
        });
    });

    // DataTable para la gestión de activos
    tabla_clientes = $("#tabla_clientes").DataTable({
        paging: true,
        bFilter: true,
        ordering: true,
        searching: true,
        destroy: true,
        "responsive": true,
        "scrollX": false,
        "autoWidth": false,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        ajax: {
            url: _URL + "/ajs/gestion/activos/render",
            method: "POST",
            dataSrc: ""
        },
        language: {
            url: _URL + "/ServerSide/Spanish.json"
        },
        columns: [
            { data: "numero", class: "text-center" },
            { data: "cliente_razon_social", class: "text-center" },
            { data: "motivo", class: "text-center" },
            { data: "fecha_salida", class: "text-center" },
            { data: "fecha_ingreso", class: "text-center" },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    return generarContadorDias(row);
                }
            },
            {
                data: null,
                class: "text-center",
                render: function (data, type, row) {
                    let confirmarBtn = '';
                    let estadoIcon = '';

                    if (row.estado === 'CONFIRMADO') {
                        estadoIcon = `<button class="btn btn-sm btn-success" disabled title="Activo en Oficina">
                        <i class="fas fa-check-circle"></i>
                        </button>`;
                    } else {
                        confirmarBtn = `
                        <button data-id="${row.id}"
                        class="btn btn-sm btn-info btnConfirmar"
                        title="Confirmar llegada a oficina">
                        <i class="fas fa-check-circle"></i>
                        </button>`;
                    }

                    return `<div class="btn-group btn-group-sm">
                        <button class="btn btn-info btn-ver" data-id="${row.id}" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button data-id="${row.id}" class="btn btn-warning btnEditar" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button data-id="${row.id}" class="btn btn-danger btnBorrar" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                        ${confirmarBtn}
                        ${estadoIcon}
                    </div>`;
                }
            }
        ]
    });

    // Manejador para el botón ver detalles
    $("#tabla_clientes").on("click", ".btn-ver", function () {
        const id = $(this).data("id");

        $.ajax({
            url: _URL + "/ajs/gestion/activos/obtener",
            type: "POST",
            data: { id: id },
            success: function (response) {
                const data = JSON.parse(response);

                $("#modalDetalles").data("activo-id", id);

                const year = new Date().getFullYear();
                const correlativo = `${String(id).padStart(6, '0')}/${year}`;

                $("#correlativo").text(`N° ${correlativo}`);
                $("#correlativo-grande").text(`GESTIÓN DE ACTIVOS N° ${correlativo}`);

                $("#detalle-cliente").text(data.cliente_razon_social);
                $("#detalle-marca").text(data.marca);
                $("#detalle-modelo").text(data.modelo);
                $("#detalle-equipo").text(data.equipo);
                $("#detalle-serie").text(data.numero_serie);
                $("#detalle-motivo").text(data.motivo);
                $("#detalle-ingreso").text(data.fecha_ingreso || 'Pendiente');
                $("#detalle-salida").text(data.fecha_salida);
                $("#detalle-observaciones").text(data.observaciones || 'Sin observaciones');

                // Mostrar estado
                if (data.estado === 'CONFIRMADO') {
                    $("#detalle-estado").html('<span class="estado-oficina">ESTÁ EN OFICINA</span>');
                } else {
                    $("#detalle-estado").html('<span class="estado-no-oficina">NO ESTÁ EN OFICINA</span>');
                }

                $("#modalDetalles").modal('show');
            }
        });
    });

    // Manejador para el botón EDITAR
    $("#tabla_clientes").on("click", ".btnEditar", function () {
        const id = $(this).data("id");

        // Configurar modo edición
        modoEdicion = true;
        activoEditandoId = id;

        // Cambiar título y botón
        $('#modalTitulo').text('Editar Registro de Activo');
        $('#submitRegistroModal').text('Actualizar Registro');

        $.ajax({
            url: _URL + "/ajs/gestion/activos/obtener",
            type: "POST",
            data: { id: id },
            success: function (response) {
                const data = JSON.parse(response);
                console.log("Datos para editar:", data);

                // Cargar datos en el formulario
                cargarDatosParaEdicion(data);

                // Mostrar modal
                $("#modalRegistroActivo").modal('show');

                // Reinicializar autocomplete
                setTimeout(() => {
                    appModal.initializeAutocomplete();
                }, 500);
            },
            error: function() {
                Swal.fire({
                    title: "Error",
                    text: "No se pudieron cargar los datos del activo",
                    icon: "error",
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
            }
        });
    });

    // Manejador para el botón de descarga
    $("#btnDescargarPDF").click(function () {
        const id = $("#modalDetalles").data("activo-id");
        if (id) {
            window.location.href = `${_URL}/gestion/activos/descargar-pdf/${id}`;
        } else {
            Swal.fire({
                title: "Error",
                text: "No se pudo identificar el activo",
                icon: "error",
                customClass: {
                    container: 'swal-high-zindex'
                }
            });
        }
    });

    // Acción para eliminar registro
    $("#tabla_clientes").on("click", ".btnBorrar", function () {
        const id = $(this).data("id");
        Swal.fire({
            title: "¿Deseas borrar el registro?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si",
            customClass: {
                container: 'swal-high-zindex'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + "/ajs/gestion/activos/delete",
                    type: "post",
                    data: { idDelete: id },
                    success: function (resp) {
                        tabla_clientes.ajax.reload(null, false);
                        Swal.fire({
                            title: "¡Buen trabajo!",
                            text: "Registro Borrado Exitosamente",
                            icon: "success",
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                    }
                });
            }
        });
    });

    // Confirmar llegada a oficina
    $("#tabla_clientes").on("click", ".btnConfirmar", function () {
        const id = $(this).data("id");
        const row = tabla_clientes.row($(this).closest('tr')).data();

        const confirmarActivo = (fecha = null) => {
            const datos = { id: id };
            if (fecha) {
                datos.fecha_ingreso = fecha;
            }

            $.ajax({
                url: _URL + "/ajs/gestion/activos/confirmar",
                type: "POST",
                data: datos,
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        Swal.fire({
                            title: "¡Confirmado!",
                            text: "El activo ha sido marcado como recibido en oficina",
                            icon: "success",
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                        tabla_clientes.ajax.reload(null, false);
                    } else if (data.requiresFechaIngreso) {
                        Swal.fire({
                            title: "Fecha de Ingreso Requerida",
                            html: `
                        <p>Este activo no tiene fecha de ingreso registrada.</p>
                        <input type="date" id="fecha_ingreso" class="swal2-input" value="${new Date().toISOString().split('T')[0]}">
                    `,
                            showCancelButton: true,
                            confirmButtonText: "Confirmar",
                            cancelButtonText: "Cancelar",
                            customClass: {
                                container: 'swal-high-zindex'
                            },
                            preConfirm: () => {
                                const fecha = document.getElementById('fecha_ingreso').value;
                                if (!fecha) {
                                    Swal.showValidationMessage('Por favor seleccione una fecha de ingreso');
                                    return false;
                                }
                                return fecha;
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                confirmarActivo(result.value);
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: data.error || "No se pudo actualizar el estado del activo",
                            icon: "error",
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Hubo un problema al comunicarse con el servidor",
                        icon: "error",
                        customClass: {
                            container: 'swal-high-zindex'
                        }
                    });
                }
            });
        };

        Swal.fire({
            title: "¿Confirmar llegada a oficina?",
            text: "Esta acción marcará el activo como recibido en oficina",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, confirmar",
            cancelButtonText: "Cancelar",
            customClass: {
                container: 'swal-high-zindex'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                confirmarActivo();
            }
        });
    });

    function verificarFechasVencimiento() {
        const tabla = $("#tabla_clientes").DataTable();
        const datos = tabla.data().toArray();
        let alertasMostradas = 0;

        datos.forEach(row => {
            if (row.estado !== 'CONFIRMADO' && row.fecha_ingreso) {
                const fechaIngreso = new Date(row.fecha_ingreso);
                const fechaActual = new Date();
                const diasRestantes = Math.ceil((fechaIngreso - fechaActual) / (1000 * 60 * 60 * 24));

                if (alertasMostradas < 3) {
                    if (diasRestantes < 0) {
                        Swal.fire({
                            title: '¡Alerta de vencimiento!',
                            html: `El activo de <strong>${row.cliente_razon_social}</strong> tiene la fecha de ingreso vencida.<br>
                        Fecha programada: ${row.fecha_ingreso}`,
                            icon: 'error',
                            confirmButtonText: 'Entendido',
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                        alertasMostradas++;
                    } else if (diasRestantes <= 3) {
                        Swal.fire({
                            title: '¡Próximo vencimiento!',
                            html: `El activo de <strong>${row.cliente_razon_social}</strong> debe ingresar en ${diasRestantes} días.<br>
                        Fecha programada: ${row.fecha_ingreso}`,
                            icon: 'warning',
                            confirmButtonText: 'Entendido',
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                        alertasMostradas++;
                    }
                }
            }
        });
    }

    // Ejecutar verificación al cargar
    verificarFechasVencimiento();
    setInterval(verificarFechasVencimiento, 24 * 60 * 60 * 1000);

    // Tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // --- LÓGICA PARA GESTIONAR MOTIVOS ---
    function cargarMotivosEnTabla() {
        $.get(_URL + "/ajs/get/motivos", function (data) {
            let resp = typeof data === 'string' ? JSON.parse(data) : data;
            if (resp.status && resp.data) resp = resp.data;
            let html = '';
            $.each(resp, function (i, v) {
                html += `<tr data-id="${v.id}">
                    <td class="motivo-nombre">${v.nombre}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btnEditarMotivo" title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btnEliminarMotivo" title="Eliminar">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
            $('#tablaMotivos tbody').html(html);
        });
    }

    function cargarMotivosEnSelect() {
        $.get(_URL + "/ajs/get/motivos", function (data) {
            let resp = typeof data === 'string' ? JSON.parse(data) : data;
            if (resp.status && resp.data) resp = resp.data;
            let options = '<option value="">Seleccione un motivo</option>';
            $.each(resp, function (i, v) {
                options += `<option value="${v.nombre}">${v.nombre}</option>`;
            });
            $('#motivo_modal').html(options);
        });
    }

    // Gestión de eventos del modal de motivos
    $('#modalMotivo').on('show.bs.modal', function () {
        cargarMotivosEnTabla();
        $('#motivo_nombre').val('');
    });

    $('#modalMotivo').on('hidden.bs.modal', function () {
        // Limpiar backdrop residual
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');

        // Mostrar modal de registro nuevamente
        setTimeout(() => {
            $("#modalRegistroActivo").modal('show');
            cargarMotivosEnSelect();
        }, 300);
    });

    // Agregar motivo
    $('#btnAgregarMotivo').click(function () {
        let nombre = $('#motivo_nombre').val().trim();
        if (!nombre) {
            Swal.fire({
                title: 'Error',
                text: 'Ingrese un nombre de motivo',
                icon: 'warning',
                customClass: {
                    container: 'swal-high-zindex'
                }
            });
            return;
        }
        $.post(_URL + "/ajs/save/motivos", { nombre }, function (data) {
            let resp = typeof data === 'string' ? JSON.parse(data) : data;
            if (resp.status) {
                cargarMotivosEnTabla();
                $('#motivo_nombre').val('');
                cargarMotivosEnSelect();
                Swal.fire({
                    title: 'Éxito',
                    text: 'Motivo agregado correctamente',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: resp.message || 'No se pudo guardar',
                    icon: 'error',
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
            }
        });
    });

    // Edición inline
    $('#tablaMotivos').on('click', '.btnEditarMotivo', function () {
        let $tr = $(this).closest('tr');
        let id = $tr.data('id');
        let nombreActual = $tr.find('.motivo-nombre').text();

        if ($tr.hasClass('editing')) return;

        $tr.addClass('editing');
        $tr.find('.motivo-nombre').html(`<input type="text" class="form-control form-control-sm inputEditMotivo" value="${nombreActual}">`);
        $(this).hide();
        let $guardar = $(`<button class="btn btn-success btn-sm btnGuardarMotivo" title="Guardar"><i class="fa fa-check"></i></button>`);
        let $cancelar = $(`<button class="btn btn-secondary btn-sm btnCancelarEdicionMotivo" title="Cancelar"><i class="fa fa-times"></i></button>`);
        $(this).after($guardar, $cancelar);
    });

    // Guardar edición inline
    $('#tablaMotivos').on('click', '.btnGuardarMotivo', function () {
        let $tr = $(this).closest('tr');
        let id = $tr.data('id');
        let nuevoNombre = $tr.find('.inputEditMotivo').val().trim();
        if (!nuevoNombre) {
            Swal.fire({
                title: 'Error',
                text: 'El nombre no puede estar vacío',
                icon: 'warning',
                customClass: {
                    container: 'swal-high-zindex'
                }
            });
            return;
        }
        $.post(_URL + "/ajs/update/motivos", { id, nombre: nuevoNombre }, function (data) {
            let resp = typeof data === 'string' ? JSON.parse(data) : data;
            if (resp.status) {
                cargarMotivosEnTabla();
                cargarMotivosEnSelect();
                Swal.fire({
                    title: 'Éxito',
                    text: 'Motivo actualizado correctamente',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: resp.message || 'No se pudo actualizar',
                    icon: 'error',
                    customClass: {
                        container: 'swal-high-zindex'
                    }
                });
            }
        });
    });

    // Cancelar edición inline
    $('#tablaMotivos').on('click', '.btnCancelarEdicionMotivo', function () {
        cargarMotivosEnTabla();
    });

    // Eliminar motivo
    $('#tablaMotivos').on('click', '.btnEliminarMotivo', function () {
        let $tr = $(this).closest('tr');
        let id = $tr.data('id');
        let nombre = $tr.find('.motivo-nombre').text();

        Swal.fire({
            title: '¿Eliminar motivo?',
            text: `¿Estás seguro de eliminar "${nombre}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                container: 'swal-high-zindex'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(_URL + "/ajs/delete/motivos", { id }, function (data) {
                    let resp = typeof data === 'string' ? JSON.parse(data) : data;
                    if (resp.status) {
                        cargarMotivosEnTabla();
                        cargarMotivosEnSelect();
                        Swal.fire({
                            title: 'Eliminado',
                            text: 'Motivo eliminado correctamente',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: resp.message || 'No se pudo eliminar',
                            icon: 'error',
                            customClass: {
                                container: 'swal-high-zindex'
                            }
                        });
                    }
                });
            }
        });
    });

    // Cargar motivos en el select al cargar la página
    cargarMotivosEnSelect();
});
