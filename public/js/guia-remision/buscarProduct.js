
function formatearPrecio(precio) {
    return parseFloat(precio).toFixed(2);
}

$(function () {
    initializeAutocomplete();
});

function initializeAutocomplete() {
    $("#input_buscar_productos").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: _URL + `/ajs/cargar/productos/${appguia.producto.almacen}`,
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    var filtered = data.filter(function (item) {
                        var searchLower = request.term.toLowerCase();
                        return item.codigo_pp.toLowerCase().indexOf(searchLower) !== -1 ||
                            item.nombre.toLowerCase().indexOf(searchLower) !== -1;
                    });
                    response(filtered);
                }
            });
        },
        minLength: 1,
        appendTo: "#modalBuscarProductos",
        select: function (event, ui) {
            event.preventDefault();

            const producto = appguia.producto;
            producto.productoid = ui.item.codigo;
            producto.codigo_pp = ui.item.codigo_pp;
            producto.descripcion = `${ui.item.codigo_pp} | ${ui.item.nombre}`;
            producto.nom_prod = ui.item.nombre;
            producto.nombre = ui.item.nombre;
            producto.cantidad = '';
            producto.stock = ui.item.cnt;
            producto.precio = formatearPrecio(ui.item.precio);
            producto.precio2 = formatearPrecio(ui.item.precio2);
            producto.precio_unidad = formatearPrecio(ui.item.precio_unidad);
            producto.codigo = ui.item.codigo;
            producto.costo = ui.item.costo;
            producto.precioVenta = formatearPrecio(ui.item.precio_unidad);

            appguia.precioProductos = [
                { precio: producto.precio },
                { precio: producto.precio2 },
                { precio: producto.precio_unidad }
            ];

            $('#input_buscar_productos').val("");
            $("#cantidad-input").focus();
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li>")
            .append(`<div class='autocomplete-item'>
        <span class='producto-codigo'>${item.codigo_pp}</span>
        <span class='producto-nombre'>${item.nombre}</span>
    </div>`)
            .appendTo(ul);
    };
}