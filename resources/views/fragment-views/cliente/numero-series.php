<!-- resources\views\fragment-views\cliente\numero-series.php -->
<link rel="stylesheet" href="<?= URL::to('/public/css/numero-series.css') ?>?v=<?= time() ?>">



<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">REGISTRO DE NÚMERO DE SERIES</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);">
            <div class="card-title-desc text-end" style="padding: 20px 10px 0 0;">
                <button onclick="descarFunccc()" class="btn border-rojo me-2">
                    <i class="fa fa-file-excel"></i> Descargar Registros Excel
                </button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar"
                    class="btn bg-rojo text-white">
                    <i class="fa fa-plus"></i> Añadir Registro
                </button>
            </div>
            <!-- NUEVO: Mensaje informativo sobre eliminación y garantías -->
            <!-- <div class="alert alert-info mx-3 mt-2 mb-0" style="border-radius: 10px;">
                <i class="fa fa-info-circle me-2"></i>
                <strong>Información:</strong> 
                <ul class="mb-0 mt-1" style="padding-left: 20px;">
                    <li>Al eliminar un registro se verificarán automáticamente las garantías relacionadas.</li>
                    <li>Si existen garantías, se mostrará una advertencia antes de proceder con la eliminación.</li>
                    <li>Los botones de garantía se bloquean automáticamente cuando ya existe una garantía registrada.</li>
                    <li>Los botones bloqueados muestran un icono de candado <i class="fa fa-lock text-muted"></i> y no son clickeables.</li>
                </ul>
            </div> -->
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table nowrap table-sm table-bordered text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>N°</th>
                                        <th>Cliente</th>
                                        <th>Cantidad de Equipos</th>
                                        <th>Fecha De Creación</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Ver Detalles -->
          
            <?php include __DIR__ . '/modals/series-detalle-ver-modal.php'; ?>

            <!-- Modal Agregar Registro -->
          <?php include __DIR__ . '/modals/series-add-registro.php'; ?>

            <!-- Modal Actualizar Serie -->
           <?php include __DIR__ . '/modals/series-actualizar.php'; ?>

            <!-- Modal Marca -->
           <?php include __DIR__ . '/modals/series-marca.php'; ?>

            <!-- Modal Modelo -->
              <?php include __DIR__ . '/modals/series-modelo.php'; ?>
              
              <!-- Modal Equipo -->
              <?php include __DIR__ . '/modals/series-equipo.php'; ?>
             
        </div>
    </div>
</div>

<script>
    // FUNCIONES GLOBALES - Deben estar fuera de $(document).ready()
    
    // Función para descargar registros Excel
    function descarFunccc() {
        window.open(_URL + `/reporte/registros/excel?texto=${$("#buscar_registros").val()}`)
    }

    // NUEVO: Función para verificar garantías antes de eliminar (GLOBAL)
    function verificarGarantiasAntesDeEliminar(idRegistro) {
        $.ajax({
            url: _URL + "/ajs/verificar/garantias",
            method: "POST",
            data: { id: idRegistro },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (response.tiene_garantias) {
                        // Mostrar alerta de confirmación con información de garantías
                        const garantiasInfo = response.garantias_info;
                        Swal.fire({
                            title: '⚠️ ¡Atención!',
                            html: `
                                <div class="text-start">
                                    <p><strong>Este registro tiene garantías relacionadas:</strong></p>
                                    <ul class="text-start">
                                        <li>Total de garantías: <strong>${garantiasInfo.total}</strong></li>
                                        <li>Números de garantía: <strong>${garantiasInfo.numeros.join(', ')}</strong></li>
                                    </ul>
                                    <p class="text-danger mt-3">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <strong>¡ADVERTENCIA!</strong> Al eliminar este registro se eliminarán también todas las garantías relacionadas.
                                    </p>
                                    <p class="text-muted">¿Está seguro que desea continuar?</p>
                                </div>
                            `,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar todo',
                            cancelButtonText: 'Cancelar',
                            width: '500px'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Proceder con la eliminación
                                eliminarRegistro(idRegistro);
                            }
                        });
                    } else {
                        // No tiene garantías, proceder directamente
                        Swal.fire({
                            title: '¿Está seguro?',
                            text: 'Esta acción no se puede deshacer.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                eliminarRegistro(idRegistro);
                            }
                        });
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.error || "Error al verificar garantías",
                        icon: "error"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Error al conectar con el servidor",
                    icon: "error"
                });
            }
        });
    }

    // NUEVO: Función para eliminar el registro (GLOBAL)
    function eliminarRegistro(idRegistro) {
        $.ajax({
            url: _URL + "/ajs/delete/numeroseries",
            method: "POST",
            data: { id: idRegistro },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: "¡Eliminado!",
                        text: "El registro ha sido eliminado exitosamente",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Recargar la tabla
                        $('#tabla_clientes').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.error || "Error al eliminar el registro",
                        icon: "error"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "Error al conectar con el servidor",
                    icon: "error"
                });
            }
        });
    }

    // Función global para cargar almacenes en el selector del modal
    function cargarAlmacenesSelector() {
        const $sel = $('#selector_almacen');
        if (!$sel.length) return;
        if ($sel.data('cargado')) return; // ya cargado
        $sel.html('<option value="">Cargando almacenes...</option>');
        $.ajax({
            url: _URL + '/ajs/almacenes/listar',
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res || !res.estado || !Array.isArray(res.almacenes)) {
                    $sel.html('<option value="">Error al cargar</option>');
                    return;
                }
                $sel.empty();
                res.almacenes.forEach(function (alm) {
                    const marca = alm.principal == 1 ? ' ★' : '';
                    $sel.append('<option value="' + alm.id_almacen + '">' + alm.nombre + marca + '</option>');
                });
                // Si no hay selección, elegir el principal
                if (!$sel.val()) {
                    const principal = res.almacenes.find(a => a.principal == 1) || res.almacenes[0];
                    if (principal) $sel.val(String(principal.id_almacen));
                }
                $sel.data('cargado', true);
            },
            error: function () {
                $sel.html('<option value="">Error al cargar</option>');
            }
        });
    }

    $(document).ready(function () {
        // Establecer la fecha actual por defecto
        const fechaActual = new Date().toISOString().split('T')[0];
        $('#fecha_creacion').val(fechaActual);

        // Cargar el último número de serie al iniciar
        cargarUltimoNumeroSerie();

        // Manejar el checkbox de cliente
        $('#tiene_cliente').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_cliente').removeClass('oculta');
                // Limpiar campos cuando se habilita registro con cliente
                $('#cliente_ruc_dni').val('');
                $('#cliente_documento').val('');
                $('#input_datos_cliente').val('');
                // Habilitar campos para edición
                $('#input_datos_cliente').prop('readonly', false);
                $('#cliente_ruc_dni').prop('readonly', false);
            } else {
                // No ocultar la sección, sino llenar con datos de la empresa
                $('#seccion_cliente').removeClass('oculta');
                // Llenar con datos de la empresa
                $('#input_datos_cliente').val('20538381978');
                $('#cliente_documento').val('20538381978');
                $('#cliente_ruc_dni').val('COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');
                // Deshabilitar campos para evitar edición
                $('#input_datos_cliente').prop('readonly', true);
                $('#cliente_ruc_dni').prop('readonly', true);
            }
        });

        // Manejar el checkbox de cliente (edición)
        $('#tiene_cliente_u').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_cliente_u').removeClass('oculta');
                // Limpiar campos cuando se habilita registro con cliente
                $('#cliente_ruc_dni_u').val('');
                $('#cliente_documento_u').val('');
                $('#input_datos_cliente_u').val('');
                // Habilitar campos para edición
                $('#input_datos_cliente_u').prop('readonly', false);
                $('#cliente_ruc_dni_u').prop('readonly', false);
            } else {
                // No ocultar la sección, sino llenar con datos de la empresa
                $('#seccion_cliente_u').removeClass('oculta');
                // Llenar con datos de la empresa
                $('#input_datos_cliente_u').val('20538381978');
                $('#cliente_documento_u').val('20538381978');
                $('#cliente_ruc_dni_u').val('COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');
                // Deshabilitar campos para evitar edición
                $('#input_datos_cliente_u').prop('readonly', true);
                $('#cliente_ruc_dni_u').prop('readonly', true);
            }
        });

        // Evento para cargar el último número de serie al abrir el modal de agregar
        $('[data-bs-target="#modalAgregar"]').on('click', function () {
            cargarUltimoNumeroSerie();
            actualizarTituloModal(); // <CHANGE> Agregar llamada para actualizar título
            // Inicializar estado del checkbox y campos
            inicializarEstadoModal();
            // Cargar almacenes en el selector (solo si está vacío)
            cargarAlmacenesSelector();
        });

        // También disparar cuando el modal ya está mostrándose (por si se reabre)
        $('#modalAgregar').on('shown.bs.modal', function () {
            cargarAlmacenesSelector();
        });

        // Al cambiar de almacén, limpiar los inputs de producto para forzar nueva búsqueda
        $('#selector_almacen').on('change', function () {
            $('.input-buscar-producto').each(function () {
                $(this).val('');
                $(this).closest('.input-group').find('.input-id-producto').val('');
                $(this).closest('.col-md-12').find('.producto-seleccionado-info').empty();
            });
        });

        // Evento para cargar el último número de serie al abrir el modal de editar
        $('#tabla_clientes').on('click', '.btnEditar', function () {
            cargarUltimoNumeroSerie();
        });

        // Inicializar DataTable con la configuración original
        var tabla_clientes = $("#tabla_clientes").DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "scrollX": false,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "order": [[3, "desc"]], // Ordenar por fecha de creación (columna 3) en orden descendente
            "ajax": {
                "url": _URL + "/ajs/get/numeroseries",
                "dataSrc": "",
                "error": function (xhr, error, thrown) {
                    console.error("Error en la respuesta del servidor:", error, thrown);
                    console.log("Respuesta completa:", xhr.responseText);
                    $("#tabla_clientes tbody").html('<tr><td colspan="6" class="text-center">Error al cargar los datos. Por favor, intente nuevamente.</td></tr>');
                }
            },
            "columns": [
                {
                    "data": "numero",
                    "render": function (data, type, row) {
                        return 'NS-' + String(data).padStart(2, '0');
                    }
                },
                {
                    "data": "cliente_ruc_dni",
                    "render": function (data, type, row) {
                        // Si no tiene cliente, mostrar "Registro Interno"
                        if (!data || data === '' || data === null) {
                            return '<span class="text-primary"><i class="fa fa-building"></i> Registro Interno (JVC)</span>';
                        }
                        return data;
                    }
                },
                { "data": "cantidad_equipos" },
                { "data": "fecha_creacion" },
                {
                    // NUEVO: Columna estado del lote
                    "data": "estado_lote",
                    "render": function (data, type, row) {
                        const estado = data || 'borrador';
                        // Estilos inline para garantizar contraste legible (el tema del sistema pisa bg-secondary)
                        let bg = '#6c757d';   // gris oscuro (borrador)
                        let texto = 'Borrador';
                        let icono = 'fa-pencil';
                        if (estado === 'completado') {
                            bg = '#198754';   // verde
                            texto = 'Completado';
                            icono = 'fa-check-circle';
                        } else if (estado === 'anulado') {
                            bg = '#dc3545';   // rojo
                            texto = 'Anulado';
                            icono = 'fa-ban';
                        }
                        let extra = '';
                        if (row.convertido_de_externo == 1) {
                            extra = ' <i class="fa fa-exchange-alt text-info" title="Convertido de externo a interno"></i>';
                        }
                        return `<span class="badge" style="background-color:${bg} !important;color:#ffffff !important;padding:6px 10px;font-size:12px;font-weight:500;"><i class="fa ${icono} me-1"></i>${texto}</span>${extra}`;
                    }
                },
                {
                    "data": null,
                    "render": function (data, type, row) {
                        const estado = row.estado_lote || 'borrador';
                        const esBorrador = estado === 'borrador';
                        const disabledEdit = esBorrador ? '' : 'disabled style="opacity:0.4;cursor:not-allowed"';
                        const tituloEdit = esBorrador
                            ? 'Editar'
                            : 'No se puede editar (lote ' + estado + ')';
                        const btnCompletar = esBorrador
                            ? `<button data-id="${Number(row.id)}" class="btn btn-sm btn-success btnCompletarLote" title="Completar lote (impacta stock)">
                                   <i class="fa fa-check-circle"></i>
                               </button>`
                            : '';
                        return `
                            <div class="text-center">
                                <div class="btn-group btn-sm">
                                    <button data-id="${Number(row.id)}" class="btn btn-sm btn-info btnVerDetalles" title="Ver detalles">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button data-id="${Number(row.id)}" class="btn btn-sm btn-warning btnEditar" title="${tituloEdit}" ${disabledEdit}>
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    ${btnCompletar}
                                    <button data-id="${Number(row.id)}" class="btn btn-sm btn-danger btnBorrar" title="Eliminar">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <a data-id="${Number(row.id)}" class="btn btn-sm btnGarantia" title="Crear Garantía" style="margin: 0; padding: 0; background-color: #DBE8F0;">
                                        <i class="ri-shield-check-line text-danger" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 0; margin: 0;font-size: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                    }
                }
            ],
            "language": {
                "url": "ServerSide/Spanish.json"
            }
        });

        // Función para verificar si un número de serie existe
        function verificarNumeroSerie(numeroSerie, callback) {
            if (!numeroSerie || numeroSerie.trim() === '') {
                callback(false);
                return;
            }

            $.ajax({
                url: _URL + "/ajs/verificar/numeroserie",
                method: "POST",
                data: { numero_serie: numeroSerie },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        callback(response.existe);
                    } else {
                        callback(false);
                    }
                },
                error: function () {
                    callback(false);
                }
            });
        }

        // Función para mostrar feedback visual
        function mostrarFeedbackSerie(input, existe) {
            const feedbackContainer = input.siblings('.feedback-container');
            input.removeClass('is-valid is-invalid numero-serie-generado');
            feedbackContainer.empty();

            if (existe) {
                input.addClass('is-invalid');
                feedbackContainer.html('<div class="invalid-feedback d-block">Este número de serie ya existe en la base de datos.</div>');
                return false;
            } else if (input.val().trim() !== '') {
                input.addClass('is-valid');
                feedbackContainer.html('<div class="valid-feedback d-block">Número de serie disponible.</div>');
                return true;
            }
            return true;
        }

        // Función para generar número de serie único
        function generarNumeroSerie(inputElement) {
            $.ajax({
                url: _URL + "/ajs/generar/numeroserie",
                method: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        // Asignar el número generado al input
                        inputElement.val(response.numero_serie);
                        inputElement.addClass('is-valid numero-serie-generado');
                        inputElement.siblings('.feedback-container').html('<div class="valid-feedback d-block">Número de serie generado automáticamente.</div>');
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.error || "No se pudo generar el número de serie",
                            icon: "error"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Error al conectar con el servidor",
                        icon: "error"
                    });
                }
            });
        }

        // Event listener para los botones de generar serie
        $(document).on('click', '.btn-generar-serie', function () {
            const inputElement = $(this).siblings('input[name$="[numero_serie]"]');
            generarNumeroSerie(inputElement);
        });


        // Validación para equipos individuales
        let typingTimer;
        $(document).on('input', 'input[name$="[numero_serie]"]', function () {
            const input = $(this);
            clearTimeout(typingTimer);

            // Eliminar clases y feedback previos mientras se escribe
            input.removeClass('is-valid is-invalid');
            input.siblings('.invalid-feedback, .valid-feedback').remove();

            typingTimer = setTimeout(function () {
                const numeroSerie = input.val().trim();
                if (numeroSerie) {
                    verificarNumeroSerie(numeroSerie, function (existe) {
                        mostrarFeedbackSerie(input, existe);
                    });

                    // Verificar series repetidas en equipos individuales
                    let todasLasSeries = [];
                    $('input[name$="[numero_serie]"]').each(function () {
                        const valor = $(this).val().trim();
                        if (valor) {
                            todasLasSeries.push(valor);
                        }
                    });

                    const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje'),
                        $('#series_repetidas_equipos_lista')
                    );
                }
            }, 500);
        });

        // También validar al perder el foco
        $(document).on('blur', 'input[name$="[numero_serie]"]', function () {
            const input = $(this);
            const numeroSerie = input.val().trim();

            if (numeroSerie) {
                verificarNumeroSerie(numeroSerie, function (existe) {
                    mostrarFeedbackSerie(input, existe);
                });

                // Verificar series repetidas en equipos individuales
                let todasLasSeries = [];
                $('input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                mostrarMensajeSeriesRepetidas(
                    seriesRepetidas,
                    $('#series_repetidas_equipos_mensaje'),
                    $('#series_repetidas_equipos_lista')
                );
            }
        });

        // Validación para equipos individuales en edición
        $(document).on('input', '#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]', function () {
            const input = $(this);
            clearTimeout(typingTimer);

            input.removeClass('is-valid is-invalid');
            input.siblings('.invalid-feedback, .valid-feedback').remove();

            typingTimer = setTimeout(function () {
                const numeroSerie = input.val().trim();
                if (numeroSerie) {
                    verificarNumeroSerie(numeroSerie, function (existe) {
                        mostrarFeedbackSerie(input, existe);
                    });

                    let todasLasSeries = [];
                    $('#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]').each(function () {
                        const valor = $(this).val().trim();
                        if (valor) {
                            todasLasSeries.push(valor);
                        }
                    });

                    const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje_u'),
                        $('#series_repetidas_equipos_lista_u')
                    );
                }
            }, 500);
        });

        // También validar al perder el foco en edición
        $(document).on('blur', '#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]', function () {
            const input = $(this);
            const numeroSerie = input.val().trim();

            if (numeroSerie) {
                verificarNumeroSerie(numeroSerie, function (existe) {
                    mostrarFeedbackSerie(input, existe);
                });

                let todasLasSeries = [];
                $('#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                mostrarMensajeSeriesRepetidas(
                    seriesRepetidas,
                    $('#series_repetidas_equipos_mensaje_u'),
                    $('#series_repetidas_equipos_lista_u')
                );
            }
        });

        // Validación para máquinas idénticas (textarea)
        $('#series_masivas').on('input', function () {
            const textarea = $(this);
            clearTimeout(typingTimer);

            $('#series_duplicadas_mensaje').remove();

            typingTimer = setTimeout(function () {
                const series = procesarSeriesMasivas(textarea.val());

                if (series.length > 0) {
                    const seriesRepetidas = verificarSeriesRepetidas(series);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_mensaje'),
                        $('#series_repetidas_lista'),
                        textarea
                    );

                    let seriesVerificadas = 0;
                    let seriesDuplicadas = [];

                    series.forEach(function (serie) {
                        verificarNumeroSerie(serie, function (existe) {
                            seriesVerificadas++;

                            if (existe) {
                                seriesDuplicadas.push(serie);
                            }

                            if (seriesVerificadas === series.length) {
                                if (seriesDuplicadas.length > 0) {
                                    $('#series_duplicadas_mensaje').remove();
                                    textarea.after(`
                                        <div id="series_duplicadas_mensaje" class="series-duplicadas">
                                            <i class="fa fa-exclamation-triangle"></i> 
                                            Las siguientes series ya existen en la base de datos: <strong>${seriesDuplicadas.join(', ')}</strong>
                                        </div>
                                    `);
                                }
                            }
                        });
                    });
                } else {
                    $('#series_repetidas_mensaje').hide();
                    textarea.removeClass('has-duplicates');
                }
            }, 800);
        });

        // También para el formulario de edición
        $('#series_masivas_u').on('input', function () {
            const textarea = $(this);
            clearTimeout(typingTimer);

            $('#series_duplicadas_mensaje_u').remove();

            typingTimer = setTimeout(function () {
                const series = procesarSeriesMasivas(textarea.val());

                if (series.length > 0) {
                    const seriesRepetidas = verificarSeriesRepetidas(series);
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_mensaje_u'),
                        $('#series_repetidas_lista_u'),
                        textarea
                    );

                    let seriesVerificadas = 0;
                    let seriesDuplicadas = [];

                    series.forEach(function (serie) {
                        verificarNumeroSerie(serie, function (existe) {
                            seriesVerificadas++;

                            if (existe) {
                                seriesDuplicadas.push(serie);
                            }

                            if (seriesVerificadas === series.length) {
                                if (seriesDuplicadas.length > 0) {
                                    $('#series_duplicadas_mensaje_u').remove();
                                    textarea.after(`
                                        <div id="series_duplicadas_mensaje_u" class="series-duplicadas">
                                            <i class="fa fa-exclamation-triangle"></i> 
                                            Las siguientes series ya existen en la base de datos: <strong>${seriesDuplicadas.join(', ')}</strong>
                                        </div>
                                    `);
                                }
                            }
                        });
                    });
                } else {
                    $('#series_repetidas_mensaje_u').hide();
                    textarea.removeClass('has-duplicates');
                }
            }, 800);
        });

        // Actualizar contador de series en tiempo real
        $('#series_masivas').on('input', function () {
            const series = procesarSeriesMasivas($(this).val());
            $('#contador_series').text(series.length);
            validarCantidadSeries();
        });

        // Actualizar validación cuando cambia la cantidad de equipos
        $('#cantidad_equipos').on('change', function () {
            const ultimoNumero = parseInt($("#ultimo_numero_serie").val());

            if (!isNaN(ultimoNumero)) {
                generarSeriesMasivas(ultimoNumero);
            }
        });

        // Manejar el checkbox de máquinas idénticas
        $('#maquinas_identicas').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_maquinas_identicas').show();
                $('#seccion_equipos_individuales').hide();
                $('#seccion_agregar_equipo').hide();
                $('#series_repetidas_equipos_mensaje').hide();

                const ultimoNumero = parseInt($("#ultimo_numero_serie").val());
                if (!isNaN(ultimoNumero)) {
                    generarSeriesMasivas(ultimoNumero);
                }
            } else {
                $('#seccion_maquinas_identicas').hide();
                $('#seccion_equipos_individuales').show();
                $('#seccion_agregar_equipo').show();
                $('#series_repetidas_mensaje').hide();
            }
        });

        // Manejar el checkbox de máquinas idénticas (edición)
        $('#maquinas_identicas_u').change(function () {
            if ($(this).is(':checked')) {
                $('#seccion_maquinas_identicas_u').show();
                $('#seccion_equipos_individuales_u').hide();
                $('#seccion_agregar_equipo_u').hide();
                $('#series_repetidas_equipos_mensaje_u').hide();
            } else {
                $('#seccion_maquinas_identicas_u').hide();
                $('#seccion_equipos_individuales_u').show();
                $('#seccion_agregar_equipo_u').show();
                $('#series_repetidas_mensaje_u').hide();
            }
        });

        // Eliminar equipo
        $(document).on('click', '.btn-eliminar-equipo', function () {
            $(this).closest('.equipo-item').remove();

            $('#equipos_container .equipo-item').each(function (index) {
                $(this).find('.card-title').text(`Equipo ${index + 1}`);
                $(this).find('input').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                });
            });

            $('#contador_equipos').text($('#equipos_container .equipo-item').length);

            setTimeout(function () {
                let todasLasSeries = [];
                $('input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                if (seriesRepetidas.length === 0) {
                    $('#series_repetidas_equipos_mensaje').hide();
                } else {
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje'),
                        $('#series_repetidas_equipos_lista')
                    );
                }
            }, 100);
        });

        // Ver detalles
        $('#tabla_clientes').on('click', '.btnVerDetalles', function () {
            var idRegistro = $(this).data('id');
            $.ajax({
                url: _URL + "/ajs/getOne/numeroseries",
                method: "POST",
                data: { id: idRegistro },
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.data && response.data.length > 0) {
                        const registro = response.data[0];

                        // Mostrar cliente o "Registro Interno"
                        const clienteTexto = registro.cliente_ruc_dni && registro.cliente_ruc_dni !== ''
                            ? registro.cliente_ruc_dni
                            : 'Registro Interno (COMERCIAL & INDUSTRIAL J. V. C. S.A.C.)';
                        $('#detalle_cliente').text(clienteTexto);
                        $('#detalle_fecha').text(registro.fecha_creacion);

                        // NUEVO: badge del estado del lote
                        const estadoLote = registro.estado_lote || 'borrador';
                        let claseEL = 'bg-secondary';
                        let textoEL = 'Borrador';
                        if (estadoLote === 'completado') { claseEL = 'bg-success'; textoEL = 'Completado'; }
                        else if (estadoLote === 'anulado') { claseEL = 'bg-danger'; textoEL = 'Anulado'; }
                        $('#detalle_estado_lote').html(`<span class="badge ${claseEL}">${textoEL}</span>`);

                        $('#detalle_equipos').empty();
                        if (registro.equipos && registro.equipos.length > 0) {
                            registro.equipos.forEach((equipo, index) => {
                                const estado = equipo.estado || 'disponible';
                                const estadoTexto = estado === 'en_garantia' ? 'En Garantía' : 'Disponible';
                                const estadoClase = estado === 'en_garantia' ? 'bg-danger text-white' : 'bg-success text-white';

                                // NUEVO: vínculo con producto del almacén
                                const productoTxt = equipo.producto_codigo
                                    ? `<span class="badge bg-info text-white" title="${equipo.producto_nombre || ''}">${equipo.producto_codigo}</span>`
                                    : '<span class="text-muted small">Sin vincular</span>';

                                $('#detalle_equipos').append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${productoTxt}</td>
                                        <td>${equipo.marca_nombre || equipo.marca || ''}</td>
                                        <td>${equipo.modelo_nombre || equipo.modelo || ''}</td>
                                        <td>${equipo.equipo_nombre || equipo.equipo || ''}</td>
                                        <td>${equipo.numero_serie || ''}</td>
                                        <td><span class="badge ${estadoClase} px-2 py-1">${estadoTexto}</span></td>
                                    </tr>
                                `);
                            });
                        } else {
                            $('#detalle_equipos').append('<tr><td colspan="7" class="text-center">No hay equipos registrados</td></tr>');
                        }

                        $('#modalDetalles').modal('show');
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.error || "Error al cargar los detalles del registro",
                            icon: "error"
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudieron obtener los detalles del registro",
                        icon: "error"
                    });
                }
            });
        });



        // Eliminar equipo nuevo
        $(document).on('click', '.btn-eliminar-equipo-nuevo', function () {
            $(this).closest('.equipo-item').remove();

            $('#equipos_container_u .equipo-item').each(function (index) {
                $(this).find('.card-title').text(`Equipo nuevo ${index + 1}`);
                $(this).find('input').each(function () {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                    }
                });
            });

            const numEquiposNuevos = $('#equipos_container_u .equipo-item').length;
            $('#contador_equipos_nuevos').text(numEquiposNuevos);

            if (numEquiposNuevos === 0) {
                $('#no_equipos_nuevos_message').show();
            }

            setTimeout(function () {
                let todasLasSeries = [];
                $('#equipos_existentes input[name$="[numero_serie]"], #equipos_container_u input[name$="[numero_serie]"]').each(function () {
                    const valor = $(this).val().trim();
                    if (valor) {
                        todasLasSeries.push(valor);
                    }
                });

                const seriesRepetidas = verificarSeriesRepetidas(todasLasSeries);
                if (seriesRepetidas.length === 0) {
                    $('#series_repetidas_equipos_mensaje_u').hide();
                } else {
                    mostrarMensajeSeriesRepetidas(
                        seriesRepetidas,
                        $('#series_repetidas_equipos_mensaje_u'),
                        $('#series_repetidas_equipos_lista_u')
                    );
                }
            }, 100);
        });

        // Actualizar contador de series en tiempo real (edición)
        $('#series_masivas_u').on('input', function () {
            const series = procesarSeriesMasivas($(this).val());
            $('#contador_series_u').text(series.length);

            const cantidadEquipos = parseInt($('#cantidad_equipos_nuevos').val());
            if (series.length !== cantidadEquipos) {
                $(this).closest('.row').find('.series-counter').addClass('error');
                $('#error_series_u').show();
            } else {
                $(this).closest('.row').find('.series-counter').removeClass('error');
                $('#error_series_u').hide();
            }
        });

        // Actualizar validación cuando cambia la cantidad de equipos (edición)
        $('#cantidad_equipos_nuevos').on('change', function () {
            const series = procesarSeriesMasivas($('#series_masivas_u').val());
            const cantidadEquipos = parseInt($(this).val());

            if (series.length !== cantidadEquipos) {
                $('.series-counter').addClass('error');
                $('#error_series_u').show();
            } else {
                $('.series-counter').removeClass('error');
                $('#error_series_u').hide();
            }
        });
        // <CHANGE> Función para actualizar el título del modal con el próximo número
        function actualizarTituloModal() {
            $.ajax({
                url: _URL + "/ajs/get/proximonumero/series",
                type: "GET",
                success: function (response) {
                    const data = JSON.parse(response);
                    const proximoNumero = String(data.proximo_numero).padStart(2, '0');
                    $('#tituloModalAgregar').text('Agregar Registro N° ' + proximoNumero);
                },
                error: function () {
                    console.log("Error al obtener próximo número");
                    $('#tituloModalAgregar').text('Agregar Registro'); // Mantener título original si hay error
                }
            });
        }
        // <CHANGE> Resetear título cuando se cierra el modal
        $('#modalAgregar').on('hidden.bs.modal', function () {
            $('#tituloModalAgregar').text('Agregar Registro');
        });

        // Función para inicializar el estado del modal
        function inicializarEstadoModal() {
            const tieneCliente = $('#tiene_cliente').is(':checked');
            if (!tieneCliente) {
                // Si está desmarcado, llenar con datos de empresa
                $('#input_datos_cliente').val('20538381978');
                $('#cliente_documento').val('20538381978');
                $('#cliente_ruc_dni').val('COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');
                $('#input_datos_cliente').prop('readonly', true);
                $('#cliente_ruc_dni').prop('readonly', true);
            } else {
                // Si está marcado, limpiar campos y habilitar edición
                $('#cliente_ruc_dni').val('');
                $('#cliente_documento').val('');
                $('#input_datos_cliente').val('');
                $('#input_datos_cliente').prop('readonly', false);
                $('#cliente_ruc_dni').prop('readonly', false);
            }
        }

    });

            // Agregar este código al final del script en numero-series.php
        $(document).on('click', '.btnGarantia', function () {
            const id = $(this).data('id');
            window.location.href = _URL + '/garantia/add?id=' + id;
        });

        // NUEVO: Evento para el botón eliminar con verificación de garantías
        $('#tabla_clientes').on('click', '.btnBorrar', function () {
            const idRegistro = $(this).data('id');
            verificarGarantiasAntesDeEliminar(idRegistro);
        });

        // NUEVO: Función para verificar y bloquear botones de garantía
        function verificarYBloquearGarantias() {
            $('#tabla_clientes tbody tr').each(function() {
                const row = $(this);
                const idRegistro = row.find('.btnBorrar').data('id');
                const btnGarantia = row.find('.btnGarantia');
                
                if (idRegistro && btnGarantia.length > 0) {
                    $.ajax({
                        url: _URL + "/ajs/verificar/garantias",
                        method: "POST",
                        data: { id: idRegistro },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success && response.tiene_garantias) {
                                // Bloquear el botón de garantía
                                btnGarantia.addClass('btn-garantia-bloqueado');
                                btnGarantia.prop('disabled', true);
                                btnGarantia.attr('title', 'Ya tiene garantía registrada');
                                
                                // Cambiar el icono y estilo
                                btnGarantia.html('<i class="fa fa-lock"></i>');
                                btnGarantia.removeClass('btn-success').addClass('btn-secondary');
                            } else {
                                // Desbloquear el botón si no tiene garantías
                                btnGarantia.removeClass('btn-garantia-bloqueado');
                                btnGarantia.prop('disabled', false);
                                btnGarantia.attr('title', 'Agregar garantía');
                                
                                // Restaurar el icono y estilo original
                                btnGarantia.html('<i class="fa fa-shield"></i>');
                                btnGarantia.removeClass('btn-secondary').addClass('btn-success');
                            }
                        },
                        error: function() {
                            console.log('Error verificando garantías para registro:', idRegistro);
                        }
                    });
                }
            });
        }

        // NUEVO: Función para actualizar un botón específico de garantía
        function actualizarBotonGarantia(idRegistro, tieneGarantia) {
            const row = $(`#tabla_clientes tbody tr:has(.btnBorrar[data-id="${idRegistro}"])`);
            const btnGarantia = row.find('.btnGarantia');
            
            if (btnGarantia.length > 0) {
                if (tieneGarantia) {
                    // Bloquear el botón
                    btnGarantia.addClass('btn-garantia-bloqueado');
                    btnGarantia.prop('disabled', true);
                    btnGarantia.attr('title', 'Ya tiene garantía registrada');
                    btnGarantia.html('<i class="fa fa-lock"></i>');
                    btnGarantia.removeClass('btn-success').addClass('btn-secondary');
                    
                    // Agregar indicador visual de estado
                    let indicadorEstado = row.find('.indicador-garantia');
                    if (indicadorEstado.length === 0) {
                        indicadorEstado = $('<span class="indicador-garantia badge bg-success ms-2" title="Tiene garantía"><i class="fa fa-check"></i></span>');
                        row.find('.btnGarantia').after(indicadorEstado);
                    }
                } else {
                    // Desbloquear el botón
                    btnGarantia.removeClass('btn-garantia-bloqueado');
                    btnGarantia.prop('disabled', false);
                    btnGarantia.attr('title', 'Agregar garantía');
                    btnGarantia.html('<i class="fa fa-shield"></i>');
                    btnGarantia.removeClass('btn-secondary').addClass('btn-success');
                    
                    // Remover indicador visual de estado
                    row.find('.indicador-garantia').remove();
                }
            }
        }

        // NUEVO: Llamar a la verificación después de cargar la tabla
        $('#tabla_clientes').on('draw.dt', function() {
            setTimeout(verificarYBloquearGarantias, 100);
        });

        // NUEVO: También verificar al cargar la página inicialmente
        $(document).ready(function() {
            setTimeout(verificarYBloquearGarantias, 500);
        });

        // NUEVO: Evento para actualizar botones cuando se recarga la tabla
        $('#tabla_clientes').on('xhr.dt', function() {
            setTimeout(verificarYBloquearGarantias, 200);
        });

        // NUEVO: Evento para actualizar botones después de operaciones CRUD
        $(document).on('serieActualizada', function(e, idRegistro) {
            // Verificar garantías para el registro específico
            setTimeout(function() {
                $.ajax({
                    url: _URL + "/ajs/verificar/garantias",
                    method: "POST",
                    data: { id: idRegistro },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            actualizarBotonGarantia(idRegistro, response.tiene_garantias);
                        }
                    }
                });
            }, 300);
        });

        // ============================================================
        // NUEVO BLOQUE: Autocomplete de productos del almacén + Completar lote
        // ============================================================

        // Inicializar autocomplete sobre cualquier .input-buscar-producto
        // Usamos delegación: cada vez que se enfoca un input se aplica autocomplete
        // si todavía no lo tenía.
        $(document).on('focus', '.input-buscar-producto', function () {
            const $input = $(this);
            if ($input.data('ui-autocomplete')) return; // ya inicializado

            $input.autocomplete({
                source: function (request, response) {
                    const almacen = $('#selector_almacen').val() || '';
                    $.ajax({
                        url: _URL + "/ajs/cargar/productos/" + almacen,
                        method: "GET",
                        dataType: "json",
                        data: { term: request.term },
                        success: function (data) {
                            if (typeof data === 'string') { try { data = JSON.parse(data); } catch(e){} }
                            response(Array.isArray(data) ? data : []);
                        },
                        error: function () {
                            response([]);
                        }
                    });
                },
                minLength: 2,
                select: function (event, ui) {
                    event.preventDefault();
                    const item = ui.item;
                    $input.val(item.codigo_pp + ' | ' + item.nombre);
                    $input.closest('.input-group').find('.input-id-producto').val(item.codigo);
                    const stockTxt = (item.cnt !== undefined && item.cnt !== null) ? item.cnt : '?';
                    $input.closest('.col-md-12').find('.producto-seleccionado-info')
                        .html(`<i class="fa fa-check text-success me-1"></i>Stock actual: <strong>${stockTxt}</strong>`);
                },
                focus: function (event, ui) {
                    event.preventDefault();
                    $input.val(ui.item.codigo_pp + ' | ' + ui.item.nombre);
                }
            });
        });

        // Si el usuario borra el texto, también limpiar el id_producto
        $(document).on('input', '.input-buscar-producto', function () {
            if ($(this).val().trim() === '') {
                $(this).closest('.input-group').find('.input-id-producto').val('');
                $(this).closest('.col-md-12').find('.producto-seleccionado-info').empty();
            }
        });

        // ----------- Completar lote: pedir resumen, mostrar modal y confirmar -----------
        $('#tabla_clientes').on('click', '.btnCompletarLote', function () {
            const idLote = $(this).data('id');
            $.ajax({
                url: _URL + "/ajs/resumen/lote/ns",
                method: "POST",
                data: { id: idLote },
                dataType: 'json',
                success: function (resp) {
                    if (!resp.success) {
                        Swal.fire({ title: 'Error', text: resp.error || 'No se pudo obtener el resumen del lote', icon: 'error' });
                        return;
                    }
                    const d = resp.data;
                    const lote = d.lote;
                    const numeroNS = 'NS-' + String(lote.numero).padStart(2, '0');
                    const tipoBadge = d.es_interno
                        ? '<span class="badge bg-primary">INTERNO</span>'
                        : '<span class="badge bg-warning text-dark">EXTERNO</span>';
                    const cliente = lote.cliente_ruc_dni || 'Registro Interno (JVC)';

                    // Construir tabla de productos a afectar
                    let filas = '';
                    if (d.productos_a_afectar && d.productos_a_afectar.length > 0) {
                        d.productos_a_afectar.forEach(p => {
                            const nuevo = parseInt(p.stock_actual || 0) + parseInt(p.cantidad_lote || 0);
                            const stockNuevoTxt = d.es_interno
                                ? `<strong class="text-success">${nuevo}</strong>`
                                : `<span class="text-muted">${p.stock_actual} (sin cambio)</span>`;
                            filas += `
                                <tr>
                                    <td>${p.codigo || ''}</td>
                                    <td class="text-start">${p.nombre || ''}</td>
                                    <td>${p.stock_actual || 0}</td>
                                    <td>+${p.cantidad_lote}</td>
                                    <td>${stockNuevoTxt}</td>
                                </tr>`;
                        });
                    } else {
                        filas = '<tr><td colspan="5" class="text-center text-muted">No hay equipos vinculados a productos del almacén</td></tr>';
                    }

                    const aviso = d.equipos_sin_vincular > 0
                        ? `<div class="alert alert-warning mt-2"><i class="fa fa-exclamation-triangle me-1"></i>${d.equipos_sin_vincular} equipo(s) NO están vinculados a un producto del almacén. Solo se registrarán las series, sin afectar stock.</div>`
                        : '';

                    const html = `
                        <div class="text-start">
                            <div class="row mb-2">
                                <div class="col-md-6"><strong>Lote:</strong> ${numeroNS}</div>
                                <div class="col-md-6"><strong>Tipo:</strong> ${tipoBadge}</div>
                            </div>
                            <div class="mb-2"><strong>Cliente:</strong> ${cliente}</div>
                            <div class="mb-2"><strong>Total equipos:</strong> ${d.total_equipos}</div>
                            ${d.es_interno
                                ? '<div class="alert alert-success mb-2"><i class="fa fa-arrow-up me-1"></i>Como es <strong>INTERNO</strong>, el stock del almacén se <strong>aumentará</strong>.</div>'
                                : '<div class="alert alert-info mb-2"><i class="fa fa-info-circle me-1"></i>Como es <strong>EXTERNO</strong>, el stock <strong>NO se modifica</strong>. Solo se registra el movimiento en kardex.</div>'}
                            <table class="table table-sm table-bordered text-center">
                                <thead class="table-light">
                                    <tr><th>Código</th><th>Producto</th><th>Stock Actual</th><th>+Lote</th><th>Stock Nuevo</th></tr>
                                </thead>
                                <tbody>${filas}</tbody>
                            </table>
                            ${aviso}
                        </div>
                    `;

                    Swal.fire({
                        title: '¿Completar lote ' + numeroNS + '?',
                        html: html,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, completar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#28a745',
                        width: '700px'
                    }).then((res) => {
                        if (!res.isConfirmed) return;
                        $.ajax({
                            url: _URL + "/ajs/completar/lote/ns",
                            method: "POST",
                            data: { id: idLote },
                            dataType: 'json',
                            success: function (r) {
                                if (r.success) {
                                    Swal.fire({ title: 'Listo', text: r.mensaje || 'Lote completado correctamente', icon: 'success' });
                                    $("#tabla_clientes").DataTable().ajax.reload();
                                } else {
                                    Swal.fire({ title: 'Error', text: r.error || 'No se pudo completar el lote', icon: 'error' });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({ title: 'Error', text: 'Error al completar el lote: ' + xhr.statusText, icon: 'error' });
                            }
                        });
                    });
                },
                error: function () {
                    Swal.fire({ title: 'Error', text: 'No se pudo conectar al servidor', icon: 'error' });
                }
            });
        });

</script>
<script src="<?= URL::to('public/js/series/funciones-comunes.js') ?>?v=<?= time() ?>"></script>
<!-- js para buscar cliente por DNI O RUC -->
<script src="<?= URL::to('public/js/series/buscar-cliente.js') ?>?v=<?= time() ?>"></script>
<!-- cargar datos en los selects para marca, modelo y equipo -->
<script src="<?= URL::to('public/js/series/cargar-selects.js') ?>?v=<?= time() ?>"></script>
<!-- cargar datos en las tablas de los modales para marca, modelo y equipo -->
<script src="<?= URL::to('public/js/series/cargar-equipo-tablas.js') ?>?v=<?= time() ?>"></script>
<!-- inicializacion de modales de marca, etc -->
<script src="<?= URL::to('public/js/series/inicializar-modales.js') ?>?v=<?= time() ?>"></script>
<!-- crud para el registro -->
<script src="<?= URL::to('public/js/series/crud-registro.js') ?>?v=<?= time() ?>"></script>
<!-- crud para el registro independiente de marca, modelo y equipo -->
<script src="<?= URL::to('public/js/series/crud-equipos.js') ?>?v=<?= time() ?>"></script>