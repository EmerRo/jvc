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

// Autocompletado para el campo de agregar cliente
$("#input_datos_cliente").autocomplete({
    source: _URL + "/ajs/buscar/cliente/datos",
    minLength: 2,
    search: function () {
        // No abrir dropdown si el cursor no está al final (usuario corrigiendo dígitos)
        if (this.selectionStart < this.value.length) return false;
    },
    select: function (event, ui) {
        event.preventDefault();
        $('#cliente_ruc_dni').val(ui.item.datos);
        $('#cliente_documento').val(ui.item.documento);
        $('#input_datos_cliente').val(ui.item.documento);
    }
});

// Autocompletado para el campo de editar cliente
$("#input_datos_cliente_u").autocomplete({
    source: _URL + "/ajs/buscar/cliente/datos",
    minLength: 2,
    search: function () {
        if (this.selectionStart < this.value.length) return false;
    },
    select: function (event, ui) {
        event.preventDefault();
        $('#cliente_ruc_dni_u').val(ui.item.datos);
        $('#cliente_documento_u').val(ui.item.documento);
        $('#input_datos_cliente_u').val(ui.item.documento);
    }
});