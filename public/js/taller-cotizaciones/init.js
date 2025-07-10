// public\js\taller-cotizaciones\init.js
$(document).ready(function() {
    // Inicialización de componentes UI
    $("#input_datos_cliente").autocomplete({
        source: _URL + "/ajs/buscar/cliente/datos",
        minLength: 2,
        select: function (event, ui) {
            event.preventDefault();
            app._data.venta.dir_pos = 1;
            app._data.venta.nom_cli = ui.item.datos;
            app._data.venta.num_doc = ui.item.documento;
            app._data.venta.dir_cli = ui.item.direccion;
        }
    });

    // Manejo de eventos de teclas
    $("#example-text-input").on('keypress', function (e) {
        if (e.which == 13) {
            $("#submit-a-product").click();
            $("#input_buscar_productos").focus();
        }
    });

    // Inicialización del botón de fotos
    setTimeout(function () {
        $('.btn-foto').on('click', function (e) {
            e.preventDefault();
            $("#modalFotos").modal("show");
        });
    }, 1000);
});