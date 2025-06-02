/* public\js\series\buscar-cliente.js 
Función para buscar cliente por DNI/RUC */
  $('#btn_buscar_cliente').click(function () {
    const docNum = $('#input_datos_cliente').val().trim();
    const docLength = docNum.length;

    if (docLength === 8 || docLength === 11) {
        $("#loader-menor").show();

        _ajax("/ajs/prealerta/doc/cliente", "POST", {
            doc: docNum
        },
            function (resp) {
                $("#loader-menor").hide();
                if (docLength === 8) {
                    if (resp.success) {
                        const nombreCompleto = resp.nombres + ' ' +
                            (resp.apellidoPaterno ? resp.apellidoPaterno : '') + ' ' +
                            (resp.apellidoMaterno ? resp.apellidoMaterno : '');
                        $('#cliente_ruc_dni').val(nombreCompleto);
                        $('#cliente_documento').val(docNum);
                    } else {
                        alertAdvertencia("Documento no encontrado");
                    }
                } else if (docLength === 11) {
                    if (resp.razonSocial) {
                        $('#cliente_ruc_dni').val(resp.razonSocial);
                        $('#cliente_documento').val(docNum);
                    } else {
                        alertAdvertencia("RUC no encontrado");
                    }
                }
            });
    } else {
        alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
    }
});

// Función para buscar cliente por DNI/RUC (edición)
$('#btn_buscar_cliente_u').click(function () {
    const docNum = $('#input_datos_cliente_u').val().trim();
    const docLength = docNum.length;

    if (docLength === 8 || docLength === 11) {
        $("#loader-menor").show();

        _ajax("/ajs/prealerta/doc/cliente", "POST", {
            doc: docNum
        },
            function (resp) {
                $("#loader-menor").hide();
                if (docLength === 8) {
                    if (resp.success) {
                        const nombreCompleto = resp.nombres + ' ' +
                            (resp.apellidoPaterno ? resp.apellidoPaterno : '') + ' ' +
                            (resp.apellidoMaterno ? resp.apellidoMaterno : '');
                        $('#cliente_ruc_dni_u').val(nombreCompleto);
                        $('#cliente_documento_u').val(docNum);
                    } else {
                        alertAdvertencia("Documento no encontrado");
                    }
                } else if (docLength === 11) {
                    if (resp.razonSocial) {
                        $('#cliente_ruc_dni_u').val(resp.razonSocial);
                        $('#cliente_documento_u').val(docNum);
                    } else {
                        alertAdvertencia("RUC no encontrado");
                    }
                }
            });
    } else {
        alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
    }
});