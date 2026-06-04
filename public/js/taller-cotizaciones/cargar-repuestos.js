// cargar-repuestos.js
// Manejo de carga de repuestos
$(document).ready(function() {

    const $selectAlmacen = $('#selector_almacen_taller');
    const $inputBuscar = $('#input_buscar_productos');
    const $almacenHelp = $('#almacen-help-text');

    // ─── Cargar almacenes en el select y preseleccionar el principal ──────────
    $.get(_URL + '/ajs/almacenes/listar', { _t: Date.now() }, function (resp) {
        // El endpoint devuelve JSON con Content-Type: text/html, así que parseamos manual
        if (typeof resp === 'string') {
            try { resp = JSON.parse(resp); } catch (e) { resp = null; }
        }
        if (!resp || !resp.estado || !Array.isArray(resp.almacenes)) {
            $selectAlmacen.html('<option value="">Error al cargar</option>');
            return;
        }
        const principal = resp.almacenes.find(a => Number(a.principal) === 1) || resp.almacenes[0];
        const idPrincipal = principal ? String(principal.id_almacen) : '';
        $selectAlmacen.empty();
        resp.almacenes.forEach(function (alm) {
            const marca = Number(alm.principal) === 1 ? ' ★' : '';
            $selectAlmacen.append(
                '<option value="' + alm.id_almacen + '">' + alm.nombre + marca + '</option>'
            );
        });
        if (idPrincipal) {
            $selectAlmacen.val(idPrincipal);
            if (app && app.producto) app.producto.almacen = idPrincipal;
        }
        inicializarAutocomplete();
    });

    // ─── Re-inicializar autocomplete cuando cambia el almacén ──────────────────
    $selectAlmacen.on('change', function () {
        if (app && app.producto) app.producto.almacen = String($(this).val() || '');
        inicializarAutocomplete();
    });

    // ─── Autocomplete del input de búsqueda ───────────────────────────────────
    function inicializarAutocomplete() {
        const almacen = $selectAlmacen.val() || '';
        $inputBuscar.prop('disabled', !almacen);
        $almacenHelp.html(almacen
            ? '<i class="fa fa-check-circle text-success"></i> Listo para buscar'
            : '<i class="fa fa-info-circle text-muted"></i> Selecciona un almacén');
        if (!almacen) return;

        if ($inputBuscar.data('ui-autocomplete')) {
            $inputBuscar.autocomplete('destroy');
        }

        $inputBuscar.autocomplete({
            source: _URL + '/ajs/cargar/repuestos/' + almacen,
            minLength: 1,
            select: function (event, ui) {
                event.preventDefault();
                app.producto.productoid = ui.item.codigo;
                app.producto.codigo_pp = ui.item.codigo_pp;
                app.producto.descripcion = ui.item.codigo + " | " + ui.item.nombre;
                app.producto.nom_prod = ui.item.nombre;
                app.producto.cantidad = '1';
                app.producto.stock = ui.item.cnt;

                if (puedeVerPrecios) {
                    app.producto.precio = ui.item.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio + "").toFixed(2);
                    app.producto.precio2 = ui.item.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio2 + "").toFixed(2);
                    app.producto.precio_unidad = ui.item.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(ui.item.precio_unidad + "").toFixed(2);
                    app.producto.precioVenta = parseFloat(ui.item.precio + "").toFixed(2);
                } else {
                    app.producto.precio = '0.00';
                    app.producto.precio2 = '0.00';
                    app.producto.precio_unidad = '0.00';
                    app.producto.precioVenta = '0.00';
                }

                app.producto.codigo = ui.item.codigo;
                app.producto.codigo_prod = ui.item.codigo_pp;
                app.producto.costo = ui.item.costo;

                app.precioProductos = [
                    { precio: app.producto.precio },
                    { precio: app.producto.precio2 },
                    { precio: app.producto.precio_unidad }
                ];

                $inputBuscar.val("");
                $("#example-text-input").focus();
            }
        });
    }
});