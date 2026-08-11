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
                    term: request.term || '',
                    startsWith: true
                },
                success: function (data) {
                    let results = JSON.parse(data);
                    if (!request.term) {
                        response(results);
                    } else {
                        results = results.filter(item =>
                            item.label.toString().toLowerCase().startsWith(request.term.toLowerCase())
                        );
                        response(results);
                    }
                }
            });
        },
        minLength: 0,
        select: function (event, ui) {
            event.preventDefault();
            app.maquinaSerie.buscar_serie = '';
            app.maquinaSerie.num_serie = ui.item.value;
            app.maquinaSerie.marc = ui.item.marca;
            app.maquinaSerie.model = ui.item.modelo;
            app.maquinaSerie.equipo = ui.item.equipo;
        }
    }).on('focus', function() {
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

        var jsonResponse = null;

        $.ajax({
            url: _URL + "/ajs/gestion/activos/add",
            type: "POST",
            data: data,
            success: function (response) {
                console.log("Full response:", response);
                try {
                    jsonResponse = JSON.parse(response);
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
