// cargar-repuestos.js
// Manejo de carga de repuestos
$(document).ready(function() {
    // Verificar si el usuario puede ver precios (esta variable debe ser definida en PHP)

    
    $("#input_buscar_productos").autocomplete({
        source: _URL + `/ajs/cargar/repuestos/${app.producto.almacen}`,
        minLength: 1,
        select: function (event, ui) {
            event.preventDefault();
            console.log(ui.item);
            app.producto.productoid = ui.item.codigo;
            app.producto.codigo_pp = ui.item.codigo_pp;
            app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre;
            app.producto.nom_prod = ui.item.nombre;
            app.producto.cantidad = '1';
            app.producto.stock = ui.item.cnt;
            
            // Asignar precios solo si el usuario tiene permisos
            if (puedeVerPrecios) {
                app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2);
                app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2);
                app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio_unidad + "").toFixed(2);
                app.producto.precioVenta = parseFloat(ui.item.precio + "").toFixed(2);
            } else {
                // Si no tiene permisos, asignar valores en 0
                app.producto.precio = '0.00';
                app.producto.precio2 = '0.00';
                app.producto.precio_unidad = '0.00';
                app.producto.precioVenta = '0.00';
            }
            
            app.producto.codigo = ui.item.codigo;
            app.producto.codigo_prod = ui.item.codigo_pp;
            app.producto.costo = ui.item.costo;
            
            let array = [{
                precio: app.producto.precio
            },
            {
                precio: app.producto.precio2
            },
            {
                precio: app.producto.precio_unidad
            }];

            app.precioProductos = array;
            $('#input_buscar_productos').val("");
            $("#example-text-input").focus();
        }
    });
});