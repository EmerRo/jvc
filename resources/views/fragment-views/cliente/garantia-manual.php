<!-- resources\views\fragment-views\cliente\garantia-manual.php -->
<div id="content-vue-modals" class="container-fluid">
    <div class="warranty-container bg-white py-4 shadow-sm rounded-3"
        style="max-width: 1000px; margin: 0 auto; border: 1px solid #e0e0e0;">
        <div class="text-end mb-4 px-4">
            <a href="/jvc/garantia" class="btn border-rojo text-rojo bg-white">
                <i class="fa fa-arrow-left me-2"></i> Regresar
            </a>
        </div>

        <div class="text-center mb-5">
            <div class="d-inline-block mb-2" style="background-color: #dc3545; padding: 15px; border-radius: 50%;">
                <i class="fa fa-shield-alt fa-2x text-white"></i>
            </div>
            <h2 class="warranty-title fw-bold" style="color: #dc3545; letter-spacing: 1px;">REGISTRO DE GARANTÍA</h2>
        </div>

        <div class="alert alert-success" id="alertSuccess"
            style="display: none; background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
            <i class="fa fa-check-circle me-2"></i> Garantía registrada con éxito.
        </div>
        <form class="px-4">
            <!-- Botones de selección de método de búsqueda -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <div class="btn-group" role="group" aria-label="Método de búsqueda">
                            <button type="button" id="btn_buscar_serie" class="btn btn-outline-danger active">
                                <i class="fa fa-barcode me-2"></i>Buscar por Serie
                            </button>
                            <button type="button" id="btn_buscar_cliente" class="btn btn-outline-danger">
                                <i class="fa fa-users me-2"></i>Buscar por Cliente
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje informativo -->
            <div class="alert alert-info mb-4">
                <i class="fa fa-info-circle me-2"></i>
                <strong>Información:</strong> Seleccione un método de búsqueda para registrar una garantía.
            </div>

            <div class="row mb-4">
                <!-- Campo de búsqueda por serie (visible por defecto) -->
                <div id="grupo_buscar_serie" class="col-md-4">
                    <label for="input_buscar_Dataseries" class="form-label fw-bold">
                        <i class="fa fa-search me-2" style="color: #dc3545;"></i> Buscar Serie <span
                            style="color: #dc3545;">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-barcode"></i>
                        </span>
                        <input id="input_buscar_Dataseries" v-model="garantia.buscar_serie" type="text"
                            placeholder="Ingrese número de serie" class="form-control"
                            style="border-left: none; box-shadow: none;">
                    </div>
                </div>

                <!-- Campo de búsqueda por cliente (oculto inicialmente) -->
                <div id="grupo_buscar_cliente" class="col-md-4" style="display: none;">
                    <label for="input_buscar_cliente" class="form-label fw-bold">
                        <i class="fa fa-users me-2" style="color: #dc3545;"></i> Buscar Cliente <span
                            style="color: #dc3545;">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-building"></i>
                        </span>
                        <input id="input_buscar_cliente" v-model="garantia.buscar_cliente" type="text"
                            placeholder="Ingrese nombre del cliente" class="form-control"
                            style="border-left: none; box-shadow: none;">
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="cliente" class="form-label fw-bold">
                        <i class="fa fa-user me-2" style="color: #dc3545;"></i> Nombre/Razón Social <span
                            style="color: #dc3545;">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-id-card"></i>
                        </span>
                        <input v-model="garantia.cliente_nombre" type="text" placeholder="Nombre del cliente"
                            class="form-control" style="border-left: none; box-shadow: none;" name="cliente"
                            id="cliente">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="numero_serie" class="form-label fw-bold">
                        <i class="fa fa-hashtag me-2" style="color: #dc3545;"></i> Número De Serie <span
                            style="color: #dc3545;">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-qrcode"></i>
                        </span>
                        <input v-model="garantia.num_serie" type="text" placeholder="Número de serie"
                            class="form-control" style="border-left: none; box-shadow: none;" name="numero_serie"
                            id="numero_serie" required>
                    </div>
                </div>
            </div>

        <!-- Series seleccionadas (para selección múltiple) -->
<div id="series_seleccionadas_container" class="row mb-4" style="display: none;">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Series seleccionadas</h6>
                    <span class="badge bg-danger" id="contador_series_seleccionadas">0</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-hover mb-0">
                        <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                            <tr>
                                <th>Número de Serie</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Equipo</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="series_seleccionadas_tbody">
                            <!-- Aquí se agregarán dinámicamente las series seleccionadas -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="marca" class="form-label fw-bold">
                        <i class="fa fa-tag me-2" style="color: #dc3545;"></i> Marca
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-trademark"></i>
                        </span>
                        <input v-model="garantia.marc" type="text" placeholder="Marca" class="form-control"
                            style="border-left: none; box-shadow: none;" name="marca" id="marca">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="modelo" class="form-label fw-bold">
                        <i class="fa fa-cube me-2" style="color: #dc3545;"></i> Modelo
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-cogs"></i>
                        </span>
                        <input v-model="garantia.model" type="text" placeholder="Modelo" name="modelo"
                            class="form-control" style="border-left: none; box-shadow: none;" id="modelo">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="equipo" class="form-label fw-bold">
                        <i class="fa fa-laptop me-2" style="color: #dc3545;"></i> Equipo
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-desktop"></i>
                        </span>
                        <input v-model="garantia.equipo" type="text" name="equipo" placeholder="Equipo"
                            class="form-control" style="border-left: none; box-shadow: none;" id="equipo">
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="guia_remision" class="form-label fw-bold">
                        <i class="fa fa-file-alt me-2" style="color: #dc3545;"></i> Guía De Remisión
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-clipboard-list"></i>
                        </span>
                        <input v-model="garantia.guiaRemision" type="text" name="guia_remision"
                            placeholder="Ingrese la guía de remisión" class="form-control"
                            style="border-left: none; box-shadow: none;" id="guia_remision">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label fw-bold">
                        <i class="fa fa-calendar-plus me-2" style="color: #dc3545;"></i> Fecha De Inicio
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-calendar-day"></i>
                        </span>
                        <input v-model="garantia.fechaInicio" type="date" name="fecha_inicio" class="form-control"
                            style="border-left: none; box-shadow: none;" id="fecha_inicio">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="fecha_caducidad" class="form-label fw-bold">
                        <i class="fa fa-calendar-times me-2" style="color: #dc3545;"></i> Fecha De Caducidad
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" style="background-color: #f8f9fa; border-right: none;">
                            <i class="fa fa-calendar-check"></i>
                        </span>
                        <input v-model="garantia.fechaCaducidad" name="fecha_caducidad" type="date" class="form-control"
                            style="border-left: none; box-shadow: none;" id="fecha_caducidad">
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 mb-3">
                <button type="button" id="submitRegistro" class="btn px-5 py-2"
                    style="background-color: #dc3545; color: white; font-weight: bold; border-radius: 30px; min-width: 200px; box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);">
                    <i class="fa fa-save me-2"></i> Guardar Garantía
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Establecer fechas por defecto
        const hoy = new Date();
        const unAñoDespues = new Date();
        unAñoDespues.setFullYear(hoy.getFullYear() + 1);

        // Formatear fechas para input type="date" (YYYY-MM-DD)
        const formatearFecha = (fecha) => {
            const año = fecha.getFullYear();
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');
            return `${año}-${mes}-${dia}`;
        };

        // Inicialización de Vue
        const app = new Vue({
            el: "#content-vue-modals",
            data: {
                garantia: {
                    cliente_nombre: '',
                    buscar_serie: '',
                    buscar_cliente: '',
                    num_serie: '',
                    marc: '',
                    model: '',
                    equipo: '',
                    guiaRemision: '',
                    fechaInicio: formatearFecha(hoy),
                    fechaCaducidad: formatearFecha(unAñoDespues)
                },
                series_seleccionadas: [] // Array para almacenar múltiples series seleccionadas
            }
        });

        // Efecto hover para los campos de entrada
        $(".input-group").hover(
            function () {
                $(this).css("box-shadow", "0 0 5px rgba(220, 53, 69, 0.3)");
                $(this).find(".input-group-text").css("background-color", "#f1f1f1");
            },
            function () {
                $(this).css("box-shadow", "none");
                $(this).find(".input-group-text").css("background-color", "#f8f9fa");
            }
        );

        // Configuración del autocompletado
        $("#input_buscar_Dataseries")
            .on("focus", function () {
                // Al hacer focus, enviar una solicitud vacía para obtener todas las series
                $(this).autocomplete("search", "");
            })
            .on("click", function () {
                // Al hacer click, también mostrar todas las series
                // Esto soluciona el problema cuando ya se ha seleccionado una serie
                $(this).autocomplete("search", "");
            })
            .autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: _URL + "/ajs/buscar/serie/datos",
                        type: "GET",
                        data: { term: request.term },
                        success: function (data) {
                            try {
                                const series = JSON.parse(data);
                                response(series);
                            } catch (e) {
                                console.error("Error al parsear datos:", e);
                                response([]);
                            }
                        },
                        error: function () {
                            response([]);
                        }
                    });
                },
                minLength: 0, // Importante: permite mostrar todos los resultados sin escribir nada
                select: function (event, ui) {
                    event.preventDefault();
                    app.garantia.cliente_nombre = ui.item.cliente_ruc_dni || '';
                    app.garantia.num_serie = ui.item.label || '';
                    app.garantia.marc = ui.item.marca_nombre || ''; // Usar el nombre de la marca
                    app.garantia.model = ui.item.modelo_nombre || ''; // Usar el nombre del modelo
                    app.garantia.equipo = ui.item.equipo_nombre || '';
                    app.garantia.buscar_serie = ''; // Limpiar el campo de búsqueda

                    // Cerrar el autocompletado después de seleccionar
                    $(this).autocomplete("close");

                    // Quitar el foco del campo para evitar que se abra nuevamente
                    $(this).blur();
                },
                open: function () {
                    // Ajustar el ancho del menú desplegable para que coincida con el campo de entrada
                    $(this).autocomplete("widget").css({
                        "max-height": "250px",
                        "overflow-y": "auto",
                        "overflow-x": "hidden",
                        "width": $(this).outerWidth() + "px"
                    });
                }
            });

        // Asegurarse de que el autocompletado se muestre siempre al hacer clic
        $(document).on("click", function (event) {
            // Si se hace clic fuera del campo y del menú de autocompletado
            if (!$(event.target).closest("#input_buscar_Dataseries, .ui-autocomplete").length) {
                // Cerrar el autocompletado si está abierto
                $("#input_buscar_Dataseries").autocomplete("close");
            }
        });
        // Configuración del autocompletado para buscar por cliente
        $("#input_buscar_cliente")
            .on("focus", function () {
                // Al hacer focus, enviar una solicitud vacía para obtener todos los clientes
                $(this).autocomplete("search", "");
            })
            .on("click", function () {
                // Al hacer click, también mostrar todos los clientes
                $(this).autocomplete("search", "");
            })
            .autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: _URL + "/ajs/buscar/cliente/serie",
                        type: "GET",
                        data: { term: request.term },
                        success: function (data) {
                            try {
                                const clientes = JSON.parse(data);
                                response(clientes);
                            } catch (e) {
                                console.error("Error al parsear datos de clientes:", e);
                                response([]);
                            }
                        },
                        error: function () {
                            response([]);
                        }
                    });
                },
                minLength: 0,
                select: function (event, ui) {
                    event.preventDefault();
                    app.garantia.cliente_nombre = ui.item.label || '';
                    app.garantia.buscar_cliente = ''; // Limpiar el campo de búsqueda

                    // Mostrar modal para seleccionar serie específica
                    mostrarModalSeleccionSerie(ui.item.id);

                    // Cerrar el autocompletado después de seleccionar
                    $(this).autocomplete("close");
                    $(this).blur();
                },
                open: function () {
                    $(this).autocomplete("widget").css({
                        "max-height": "250px",
                        "overflow-y": "auto",
                        "overflow-x": "hidden",
                        "width": $(this).outerWidth() + "px"
                    });
                }
            });

        // Modificar la función mostrarModalSeleccionSerie para añadir la opción "Seleccionar todos"
        function mostrarModalSeleccionSerie(clienteId) {
            $.ajax({
                url: _URL + "/ajs/buscar/series/cliente",
                type: "GET",
                data: { cliente_id: clienteId },
                success: function (data) {
                    try {
                        const series = JSON.parse(data);

                        // Si no hay series, mostrar mensaje
                        if (series.length === 0) {
                            Swal.fire({
                                icon: "info",
                                title: "Sin series",
                                text: "Este cliente no tiene series registradas",
                                confirmButtonColor: "#dc3545"
                            });
                            return;
                        }

                        // Contar cuántas series están disponibles (no en garantía)
                        const seriesDisponibles = series.filter(serie => serie.estado !== 'en_garantia').length;

                        // Construir HTML para el modal
                        let seriesHtml = '';
                        series.forEach(serie => {
                            // Verificar si la serie ya está en garantía
                            const enGarantia = serie.estado === 'en_garantia';
                            const estadoTexto = enGarantia ? 'En Garantía' : 'Disponible';
                            const estadoClase = enGarantia ? 'bg-danger text-white' : 'bg-success text-white';

                            seriesHtml += `
                    <tr>
                        <td>${serie.numero_serie}</td>
                        <td>${serie.marca_nombre || 'No especificado'}</td>
                        <td>${serie.modelo_nombre || 'No especificado'}</td>
                        <td>${serie.equipo_nombre || 'No especificado'}</td>
                        <td><span class="badge ${estadoClase} px-2 py-1">${estadoTexto}</span></td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input seleccionar-serie-checkbox" type="checkbox" 
                                    value="${serie.numero_serie}" 
                                    data-serie="${serie.numero_serie}"
                                    data-marca="${serie.marca_nombre || ''}"
                                    data-modelo="${serie.modelo_nombre || ''}"
                                    data-equipo="${serie.equipo_nombre || ''}"
                                    ${enGarantia ? 'disabled' : ''}>
                                <label class="form-check-label">Seleccionar</label>
                            </div>
                        </td>
                    </tr>
                `;
                        });

                        // Crear y mostrar el modal
                        const modalHtml = `
                <div class="modal fade" id="modalSeleccionSerie" tabindex="-1" role="dialog" aria-labelledby="modalSeleccionSerieLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #dc3545; color: white;">
                                <h5 class="modal-title" id="modalSeleccionSerieLabel">Seleccionar Series</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i>
                                    Seleccione las series disponibles para registrar en garantía.
                                    Las series que ya están en garantía aparecen deshabilitadas.
                                </div>
                                
                                <!-- Botón para seleccionar todas las series disponibles -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="seleccionar_todas_series">
                                        <label class="form-check-label fw-bold" for="seleccionar_todas_series">
                                            Seleccionar todas las series disponibles (${seriesDisponibles})
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Número de Serie</th>
                                                <th>Marca</th>
                                                <th>Modelo</th>
                                                <th>Equipo</th>
                                                <th>Estado</th>
                                                <th>Seleccionar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${seriesHtml}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-danger" id="confirmar_seleccion_series">Confirmar Selección</button>
                            </div>
                        </div>
                    </div>
                </div>
                `;

                        // Añadir el modal al DOM y mostrarlo
                        $('body').append(modalHtml);
                        $('#modalSeleccionSerie').modal('show');

                        // Inicializar tooltips
                        $('[data-bs-toggle="tooltip"]').tooltip();

                        // Manejar el checkbox "Seleccionar todas"
                        $('#seleccionar_todas_series').change(function () {
                            // Si el checkbox está marcado, seleccionar todas las series disponibles
                            // Si no está marcado, deseleccionar todas
                            const seleccionarTodas = $(this).prop('checked');

                            // Seleccionar o deseleccionar solo las series que no están deshabilitadas
                            $('.seleccionar-serie-checkbox:not(:disabled)').prop('checked', seleccionarTodas);
                        });

                        // Actualizar el estado del checkbox "Seleccionar todas" cuando se cambian los checkboxes individuales
                        $(document).on('change', '.seleccionar-serie-checkbox', function () {
                            const todasSeleccionadas = $('.seleccionar-serie-checkbox:not(:disabled)').length ===
                                $('.seleccionar-serie-checkbox:not(:disabled):checked').length;
                            $('#seleccionar_todas_series').prop('checked', todasSeleccionadas);
                        });

                        // Manejar la confirmación de selección de series
                        $('#confirmar_seleccion_series').click(function () {
                            const seriesSeleccionadas = [];

                            // Recopilar todas las series seleccionadas
                            $('.seleccionar-serie-checkbox:checked').each(function () {
                                const checkbox = $(this);
                                seriesSeleccionadas.push({
                                    numero_serie: checkbox.data('serie'),
                                    marca: checkbox.data('marca'),
                                    modelo: checkbox.data('modelo'),
                                    equipo: checkbox.data('equipo')
                                });
                            });

                            if (seriesSeleccionadas.length === 0) {
                                Swal.fire({
                                    icon: "warning",
                                    title: "Selección vacía",
                                    text: "Por favor, seleccione al menos una serie",
                                    confirmButtonColor: "#dc3545"
                                });
                                return;
                            }

                            // Guardar las series seleccionadas
                            app.series_seleccionadas = seriesSeleccionadas;

                            // Si solo hay una serie seleccionada, llenar los campos del formulario
                            if (seriesSeleccionadas.length === 1) {
                                const serie = seriesSeleccionadas[0];
                                app.garantia.num_serie = serie.numero_serie;
                                app.garantia.marc = serie.marca;
                                app.garantia.model = serie.modelo;
                                app.garantia.equipo = serie.equipo;

                                // Ocultar el contenedor de series seleccionadas
                                $('#series_seleccionadas_container').hide();
                            } else {
                                // Si hay múltiples series, mostrar la tabla de series seleccionadas
                                app.garantia.num_serie = seriesSeleccionadas.map(s => s.numero_serie).join(', ');

                                // Actualizar la tabla de series seleccionadas
                                actualizarTablaSeries(seriesSeleccionadas);

                                // Mostrar el contenedor de series seleccionadas
                                $('#series_seleccionadas_container').show();
                            }

                            $('#modalSeleccionSerie').modal('hide');
                        });

                        // Eliminar el modal del DOM después de cerrarlo
                        $('#modalSeleccionSerie').on('hidden.bs.modal', function () {
                            $(this).remove();
                        });

                    } catch (e) {
                        console.error("Error al parsear datos de series:", e);
                        Swal.fire({
                            icon: "error",
                            title: "¡Error!",
                            text: "Error al cargar las series del cliente",
                            confirmButtonColor: "#dc3545"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: "No se pudieron cargar las series del cliente",
                        confirmButtonColor: "#dc3545"
                    });
                }
            });
        }

        // Función para actualizar la tabla de series seleccionadas
        function actualizarTablaSeries(series) {
            const tbody = $('#series_seleccionadas_tbody');
            tbody.empty();

            series.forEach((serie, index) => {
                tbody.append(`
                    <tr>
                        <td>${serie.numero_serie}</td>
                        <td>${serie.marca || 'No especificado'}</td>
                        <td>${serie.modelo || 'No especificado'}</td>
                        <td>${serie.equipo || 'No especificado'}</td>
                        <td>
                            <button class="btn btn-sm btn-danger eliminar-serie" data-index="${index}">
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            // Actualizar contador
            $('#contador_series_seleccionadas').text(series.length);
        }

        // Manejar eliminación de series de la tabla
        $(document).on('click', '.eliminar-serie', function () {
            const index = $(this).data('index');
            app.series_seleccionadas.splice(index, 1);

            if (app.series_seleccionadas.length === 0) {
                // Si no quedan series, ocultar la tabla
                $('#series_seleccionadas_container').hide();
                app.garantia.num_serie = '';
            } else if (app.series_seleccionadas.length === 1) {
                // Si queda solo una serie, llenar los campos del formulario
                const serie = app.series_seleccionadas[0];
                app.garantia.num_serie = serie.numero_serie;
                app.garantia.marc = serie.marca;
                app.garantia.model = serie.modelo;
                app.garantia.equipo = serie.equipo;

                // Ocultar el contenedor de series seleccionadas
                $('#series_seleccionadas_container').hide();
            } else {
                // Actualizar el campo de número de serie y la tabla
                app.garantia.num_serie = app.series_seleccionadas.map(s => s.numero_serie).join(', ');
                actualizarTablaSeries(app.series_seleccionadas);
            }
        });

        // Manejar cambio entre métodos de búsqueda
        $("#btn_buscar_serie").click(function () {
            $(this).addClass("active");
            $("#btn_buscar_cliente").removeClass("active");
            $("#grupo_buscar_serie").show();
            $("#grupo_buscar_cliente").hide();

            // Limpiar campos de búsqueda por cliente
            app.garantia.buscar_cliente = '';

            // Limpiar series seleccionadas
            app.series_seleccionadas = [];
            $('#series_seleccionadas_container').hide();
        });

        $("#btn_buscar_cliente").click(function () {
            $(this).addClass("active");
            $("#btn_buscar_serie").removeClass("active");
            $("#grupo_buscar_serie").hide();
            $("#grupo_buscar_cliente").show();

            // Limpiar campos de búsqueda por serie
            app.garantia.buscar_serie = '';
        });

        // Validación antes de guardar
     // Reemplazar este bloque en el evento click del botón submitRegistro
$("#submitRegistro").click(function () {
    // Validar que los campos obligatorios no estén vacíos
    if (!app.garantia.cliente_nombre || !app.garantia.num_serie || !app.garantia.fechaInicio || !app.garantia.fechaCaducidad) {
        Swal.fire({
            icon: "error",
            title: "¡Error!",
            text: "Por favor complete todos los campos obligatorios.",
            confirmButtonColor: "#dc3545"
        });
        return; // Detener la ejecución si hay campos vacíos
    }

    // Mostrar indicador de carga
    Swal.fire({
        title: "Registrando garantía",
        html: "Por favor espere mientras se registra la garantía...",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Preparar los datos para enviar
    let data = {
        cliente_nombre: app.garantia.cliente_nombre,
        guia_remision: app.garantia.guiaRemision,
        fecha_inicio: app.garantia.fechaInicio,
        fecha_caducidad: app.garantia.fechaCaducidad
    };

    // Si hay múltiples series seleccionadas, enviarlas como un array
    if (app.series_seleccionadas.length > 1) {
        // Convertir el array de objetos a un string JSON
        data.series = JSON.stringify(app.series_seleccionadas.map(s => s.numero_serie));
        data.numero_serie = app.garantia.num_serie; // Mantener también el campo num_serie para compatibilidad
    } else {
        // Si es una sola serie, enviar como antes
        data.numero_serie = app.garantia.num_serie;
    }

    console.log("Datos enviados:", data);

    // Enviar una sola solicitud AJAX con todas las series
    $.ajax({
        url: _URL + "/ajs/garantia/add",
        type: "POST",
        data: data,
        success: function (response) {
            try {
                const res = JSON.parse(response);
                if (res.res) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Éxito!",
                        text: res.msg,
                        confirmButtonColor: "#dc3545",
                        allowOutsideClick: false
                    }).then((result) => {
                        window.location.href = _URL + "/garantia";
                    });

                    // Limpiar los campos después de un registro exitoso
                    app.garantia.cliente_nombre = '';
                    app.garantia.buscar_serie = '';
                    app.garantia.num_serie = '';
                    app.garantia.marc = '';
                    app.garantia.model = '';
                    app.garantia.equipo = '';
                    app.garantia.guiaRemision = '';
                    app.garantia.fechaInicio = formatearFecha(hoy);
                    app.garantia.fechaCaducidad = formatearFecha(unAñoDespues);
                    app.series_seleccionadas = [];
                    $('#series_seleccionadas_container').hide();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "¡Error!",
                        text: res.msg,
                        confirmButtonColor: "#dc3545"
                    });
                }
            } catch (e) {
                console.error("Error al procesar la respuesta:", e);
                Swal.fire({
                    icon: "error",
                    title: "¡Error!",
                    text: "Ocurrió un error al procesar la respuesta del servidor.",
                    confirmButtonColor: "#dc3545"
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
                icon: "error",
                title: "¡Error!",
                text: "No se pudo registrar. Intenta nuevamente.",
                confirmButtonColor: "#dc3545"
            });
        }
    });
});
    });
</script>