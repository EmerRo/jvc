<div id="client" class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Agregar Registro</h1>
        <a id="enlaceGestion" href="#" class="btn btn-warning">Ir a Gestión de Activos</a>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form id="frmClientesAgregar">
                <input type="hidden" name="origen" value="Ord Servicio">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input_datos_cliente" class="form-label">DNI o RUC <span style="color:red">(*)</span></label>
                            <div class="input-group">
                                <input id="input_datos_cliente" v-model="maquinaSerie.num_doc" type="text"
                                    placeholder="Ingrese Documento" class="form-control" maxlength="11">
                                <button @click="buscarDocumentSS" class="btn btn-primary" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                            <p id="input_datos_cliente-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="input_buscar_Dataseries" class="form-label">
                                Buscar Serie<span class="text-danger"> (*)</span>
                            </label>
                            <input id="input_buscar_Dataseries" v-model="maquinaSerie.buscar_serie" type="text"
                                placeholder="Ingrese Serie" class="form-control">
                            <p id="input_buscar_Dataseries-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" v-model="maquinaSerie.marc" class="form-control" id="marca" name="marca"
                                readonly>
                            <p id="marca-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input id="modelo" v-model="maquinaSerie.model" name="modelo" class="form-control"
                                readonly>
                            <p id="modelo-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="equipo" class="form-label">Equipo</label>
                            <input type="text" v-model="maquinaSerie.equipo" class="form-control" id="equipo"
                                name="equipo" readonly>
                            <p id="equipo-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cliente_razon_social" class="form-label">Cliente </label>
                            <input v-model="maquinaSerie.cliente_Rsocial" type="text" placeholder="Nombre del cliente"
                                class="form-control" autocomplete="off" id="cliente_razon_social"
                                name="cliente_razon_social">
                            <p id="cliente_razon_social-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="numero_serie" class="form-label">Número De Serie</label>
                            <input type="text" v-model="maquinaSerie.num_serie" class="form-control" id="numero_serie"
                                name="numero_serie">
                            <p id="numero_serie-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <select class="form-select" id="motivo" name="motivo"></select>
                            <p id="motivo-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                     
                        <div class="mb-3">
                            <label for="fecha_salida" class="form-label">Fecha De Salida</label>
                            <input type="date" class="form-control" id="fecha_salida" name="fecha_salida">
                            <p id="fecha_salida-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_ingreso" class="form-label">Fecha De Ingreso</label>
                            <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso">
                            <p id="fecha_ingreso-error" class="text-danger mt-1 mb-0"></p>
                        </div>


                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                            <p id="observaciones-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" id="submitRegistro" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
       document.getElementById('enlaceGestion').href = `${_URL}/gestion/activos`;

</script>
<script>
    var _URL = (function() {
        let pathname = window.location.pathname; // Obtiene la ruta actual (ej: /jvc/index.html)
        let parts = pathname.split('/'); // Divide la ruta en partes
        if (parts.length > 1) {
            return '/' + parts[1]; // Devuelve la primera parte de la ruta (ej: /jvc)
        }
        return ''; // Si no hay ruta base, devuelve una cadena vacía
    })();

    console.log("Ruta base detectada:", _URL); // Para verificar la ruta base
    // Asignar la URL completa al enlace usando jQuery
    
</script>
<script>
    $(document).ready(function () {
        const app = new Vue({
            el: "#client",
            data: {
                maquinaSerie: {
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
                document.getElementById('fecha_salida').value = this.maquinaSerie.fecha_salida;
                
                // Agregar eventos para ocultar mensajes de error al escribir
                $('input, select, textarea').on('input change', function() {
                    const id = $(this).attr('id');
                    if (id) {
                        $(`#${id}-error`).text('');
                        $(this).removeClass('is-invalid');
                    }
                });
            },
            methods: {
                buscarDocumentSS() {
                    const docLength = this.maquinaSerie.num_doc.length;
                    if (docLength === 8 || docLength === 11) {
                        $("#loader-menor").show();
                        this.maquinaSerie.dir_pos = 1;

                        _ajax("/ajs/prealerta/doc/cliente", "POST", {
                            doc: this.maquinaSerie.num_doc
                        }, (resp) => {
                            $("#loader-menor").hide();
                            console.log(resp);

                            if (docLength === 8) {
                                if (resp.success) {
                                    this.maquinaSerie.cliente_Rsocial = `${resp.nombres} ${resp.apellidoPaterno || ''} ${resp.apellidoMaterno || ''}`;
                                } else {
                                    alertAdvertencia("Documento no encontrado");
                                }
                            } else if (docLength === 11) {
                                if (resp.razonSocial) {
                                    this.maquinaSerie.cliente_Rsocial = resp.razonSocial;
                                } else {
                                    alertAdvertencia("RUC no encontrado");
                                }
                            }
                        });
                    } else {
                        alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
                    }
                },
            }
        });
        

        // Cargar motivos en el select
        $.get(_URL + "/ajs/get/motivos", function (data) {
            let options = '<option value="">Seleccione un motivo</option>';
            let resp = JSON.parse(data);
            $.each(resp, function (i, v) {
                options += `<option value="${v.nombre}">${v.nombre}</option>`;
            });
            $('#motivo').html(options);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar los motivos: " + textStatus, errorThrown);
            alert("No se pudo cargar los motivos. Por favor, intenta nuevamente.");
        });
        $("#input_buscar_Dataseries").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: _URL + "/ajs/buscar/maquina/datos",
            type: "GET",
            data: {
                term: request.term || '', // Envía término vacío si no hay entrada
                startsWith: true
            },
            success: function (data) {
                let results = JSON.parse(data);
                // Si no hay término de búsqueda, mostrar todos los resultados
                if (!request.term) {
                    response(results);
                } else {
                    // Filtrar resultados si hay término de búsqueda
                    results = results.filter(item =>
                        item.label.toString().toLowerCase().startsWith(request.term.toLowerCase())
                    );
                    response(results);
                }
            }
        });
    },
    minLength: 0, // Permite mostrar resultados sin escribir nada
    select: function (event, ui) {
        event.preventDefault();
        app.maquinaSerie.buscar_serie = '';
        app.maquinaSerie.num_serie = ui.item.value;
        app.maquinaSerie.marc = ui.item.marca;
        app.maquinaSerie.model = ui.item.modelo;
        app.maquinaSerie.equipo = ui.item.equipo;
    }
}).on('focus', function() {
    // Muestra todos los resultados al hacer clic en el input
    $(this).autocomplete('search', '');
});

        function mostrarErrorValidacion(campo, mensaje) {
            $(`#${campo}-error`).text(mensaje);
            $(`#${campo}`).addClass('is-invalid');
        }

        function limpiarErrores() {
            $('.text-danger').text('');
            $('.is-invalid').removeClass('is-invalid');
        }

        $("#submitRegistro").click(function () {
            limpiarErrores();
            $(this).prop('disabled', true);

            const data = {
                cliente_razon_social: app.maquinaSerie.cliente_Rsocial,
                marca: app.maquinaSerie.marc,
                modelo: app.maquinaSerie.model,
                numero_serie: app.maquinaSerie.num_serie,
                equipo: app.maquinaSerie.equipo,
                motivo: $('#motivo').val(),
                fecha_salida: $('#fecha_salida').val(),
                fecha_ingreso: $('#fecha_ingreso').val(),
                observaciones: $('#observaciones').val()
            };

            let errores = {};
            if (!data.cliente_razon_social) errores.cliente_razon_social = "El nombre del cliente es requerido";
            if (!data.marca) errores.marca = "La marca es requerida";
            if (!data.modelo) errores.modelo = "El modelo es requerido";
            if (!data.numero_serie) errores.numero_serie = "El número de serie es requerido";
            if (!data.equipo) errores.equipo = "El equipo es requerido";
            if (!data.motivo) errores.motivo = "El motivo es requerido";

            if (Object.keys(errores).length > 0) {
                for (let campo in errores) {
                    mostrarErrorValidacion(campo, errores[campo]);
                }
                $(this).prop('disabled', false);
                return;
            }

            $.ajax({
                url: _URL + "/ajs/gestion/activos/add",
                type: "POST", 
                data: data,
                success: function (response) {
                    console.log("Full response:", response);
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.res) {
                            Swal.fire({
                                title: "¡Éxito!",
                                text: jsonResponse.msg,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "/gestion/activos";
                                }
                            });
                        } else {
                            if (jsonResponse.errores) {
                                for (let campo in jsonResponse.errores) {
                                    mostrarErrorValidacion(campo, jsonResponse.errores[campo]);
                                }
                            } else {
                                Swal.fire("¡Error!", jsonResponse.msg, "error");
                            }
                        }
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                        Swal.fire("¡Error!", "Hubo un problema al procesar la respuesta del servidor.", "error");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error:", textStatus, errorThrown);
                    Swal.fire("¡Error!", "No se pudo registrar. Intenta nuevamente.", "error");
                },
                complete: function () {
                    $("#submitRegistro").prop('disabled', false);
                    if (jsonResponse && jsonResponse.res) {
                        app.maquinaSerie = {
                            cliente_Rsocial: "",
                            buscar_serie: '',
                            num_serie: '',
                            marc: '',
                            model: '',
                            equipo: '',
                            num_doc: "",
                        };
                        $('#motivo').val('');
                        $('#fecha_salida').val('');
                        $('#fecha_ingreso').val('');
                        $('#observaciones').val('');
                    }
                }
            });
        });
    });
</script>

