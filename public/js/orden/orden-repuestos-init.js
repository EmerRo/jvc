// public/js/orden/orden-repuestos-init.js
// jQuery event handlers for repuestos page
// Depende de: jQuery, DataTables, app (Vue instance)

$(document).ready(function () {
    // ===== FILTROS EXCLUSIVOS (JVC / IMPLE / REP) =====
    $(".filter-option").on("change", function () {
        $(".filter-option").not(this).prop("checked", false);
        if (datatable) {
            datatable.ajax.reload();
        }
    });

    // ===== IMPORTAR EXCEL =====
    $("#file-import-exel").on("change", function (e) {
        var formData = new FormData();
        formData.append("file", e.target.files[0]);
        $.ajax({
            url: _URL + "/ajs/data/repuesto/add/exel",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (resp) {
                if (resp) {
                    app.listaRep = resp;
                    app.almacenImportacionRep = app.almacen;
                    $("#importarModal").modal("show");
                } else {
                    alertAdvertencia("No se pudieron leer los datos del archivo");
                }
            }
        });
    });

    // ===== BOTONES DE ACCIÓN EN DATATABLE =====

    // Restock directo
    $(document).on("click", "#datatable .btn-re-stock", function () {
        var id = $(this).data("id");
        app.restock.cod = id;
        $("#modal-re-stock").modal("show");
    });

    // Reporte PDF por repuesto
    $(document).on("click", "#generarreporteRep", function () {
        var id = app.edt.cod_rep;
        window.open(_URL + "/reporte/repuestos/pdf/" + id, "_blank");
    });

    $(document).on("click", "#datatable .btn-reporte", function () {
        var id = $(this).data("id");
        window.open(_URL + "/reporte/repuestos/pdf/" + id, "_blank");
    });

    // Precios
    $(document).on("click", "#datatable .btn-ver-precios", function () {
        var id = $(this).data("id");
        _ajax("/ajs/cargar/repuestos/precios", "POST", { id: id }, function (resp) {
            try {
                var data = JSON.parse(resp);
                var html = "<table class='table table-sm'><thead><tr><th>Precio</th><th>Unidad</th></tr></thead><tbody>";
                data.forEach(function (p) {
                    html += "<tr><td>" + p.precio + "</td><td>" + p.unidad + "</td></tr>";
                });
                html += "</tbody></table>";
                Swal.fire({ title: "Precios", html: html, confirmButtonText: "Cerrar" });
            } catch (e) {
                console.log(resp);
            }
        });
    });

    // Editar
    $(document).on("click", "#datatable .btn-edt", function () {
        var id = $(this).data("id");
        var tipo = $(this).data("tipo");
        _ajax("/ajs/data/repuesto/info", "POST", { id: id, tipo: tipo }, function (resp) {
            app.setInfo(resp);
            $("#modal-edt-rep").modal("show");
        });
    });

    // Grid view edit buttons
    $(document).on("click", ".btn-edt-grid", function () {
        var id = $(this).data("id");
        $("#modal-edt-rep").modal("show");
        _ajax("/ajs/data/repuesto/info", "POST", { id: id }, function (resp) {
            app.setInfo(resp);
        });
    });

    // Barcode on edit modal shown
    $("#modal-edt-rep").on("shown.bs.modal", function () {
        app.toggleBarcodeEdit();
    });

    // ===== CHECKBOX ELIMINAR CON LOCALSTORAGE =====
    $(document).on("click", ".btnCheckEliminar", function () {
        var id = $(this).val();
        var idChecks = JSON.parse(localStorage.getItem("idChecks") || "[]");
        if ($(this).is(":checked")) {
            if (idChecks.indexOf(id) === -1) idChecks.push(id);
        } else {
            idChecks = idChecks.filter(function (v) { return v != id; });
        }
        localStorage.setItem("idChecks", JSON.stringify(idChecks));
    });

    $(document).on("click", ".btnSeleccionarTodos", function () {
        var checked = $(this).is(":checked");
        $(".btnCheckEliminar").each(function () {
            $(this).prop("checked", checked).trigger("click");
        });
    });

    // ===== BORRAR MÚLTIPLE =====
    $(document).on("click", ".btnBorrar", function () {
        var idChecks = JSON.parse(localStorage.getItem("idChecks") || "[]");
        if (idChecks.length === 0) {
            alertAdvertencia("Seleccione al menos un repuesto");
            return;
        }
        Swal.fire({
            title: "¿Eliminar " + idChecks.length + " repuesto(s)?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#C1272D",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then(function (result) {
            if (result.isConfirmed) {
                _ajax("/ajs/data/repuesto/delete", "POST", { ids: JSON.stringify(idChecks) }, function (resp) {
                    if (resp == "nice") {
                        localStorage.removeItem("idChecks");
                        app.changeAlmacenById(app.almacen);
                        alertExito("Repuestos eliminados");
                    } else {
                        alertAdvertencia("Error al eliminar");
                    }
                });
            }
        });
    });

    // ===== ABRIR MODAL AGREGAR =====
    $(document).on("click", "#add-rep", function () {
        app.cargarUnidades();
        app.cargarCategorias();
        _get("/ajs/get/subcategorias/rep", function (resp) {
            app.subcategorias = JSON.parse(resp);
        });
        $("#modal-add-repuesto").modal("show");
    });

    // ===== SELECT CATEGORÍA EN AGREGAR =====
    $(document).on("change", "#select_categorias_rep", function () {
        app.reg.categoria = $(this).val();
        app.cargarSubcategorias();
    });

    // ===== AUTOCOMPLETES =====
    $("#buscar-repuesto-stock").autocomplete({
        source: function (request, response) {
            $.getJSON(_URL + "/ajs/cargar/repuestos/" + almacenCod, { term: request.term }, response);
        },
        select: function (event, ui) {
            app.stockData.repuesto_id = ui.item.id;
            app.stockData.repuesto_nombre = ui.item.label;
            app.stockData.stock_actual = ui.item.stock;
            app.stockData.cantidad_ingresar = '';
            $("#buscar-repuesto-stock").val(ui.item.label);
            return false;
        }
    });

    $("#buscar-repuesto-disminuir").autocomplete({
        source: function (request, response) {
            $.getJSON(_URL + "/ajs/cargar/repuestos/" + almacenCod, { term: request.term }, response);
        },
        select: function (event, ui) {
            app.disminuirDataRepuesto.repuesto_id = ui.item.id;
            app.disminuirDataRepuesto.repuesto_nombre = ui.item.label;
            app.disminuirDataRepuesto.stock_actual = ui.item.stock;
            app.disminuirDataRepuesto.unidad = ui.item.unidad;
            app.disminuirDataRepuesto.cantidad_disminuir = '';
            app.disminuirDataRepuesto.observaciones = '';
            $("#buscar-repuesto-disminuir").val(ui.item.label);
            return false;
        }
    });

    $("#buscar-repuesto-traslado").autocomplete({
        source: function (request, response) {
            $.getJSON(_URL + "/ajs/cargar/repuestos/" + almacenCod, { term: request.term }, response);
        },
        select: function (event, ui) {
            var existe = app.trasladoDataRepuesto.repuestos.some(function (r) {
                return r.repuesto_id == ui.item.id;
            });
            if (!existe) {
                app.trasladoDataRepuesto.repuestos.push({
                    repuesto_id: ui.item.id,
                    nombre: ui.item.label,
                    cantidad: 1
                });
            }
            app.trasladoDataRepuesto.repuesto_id = '';
            $(this).val('');
            return false;
        }
    });

    // ===== CLEANUP MODALS =====
    $("#modal-aumentar-stock").on("hidden.bs.modal", function () {
        app.stockData = { repuesto_id: '', stock_actual: '', cantidad_ingresar: '', repuesto_nombre: '' };
        $("#buscar-repuesto-stock").val('');
    });
    $("#modal-disminuir-stock-repuesto").on("hidden.bs.modal", function () {
        app.disminuirDataRepuesto = {
            repuesto_id: '', stock_actual: '', cantidad_disminuir: '',
            repuesto_nombre: '', unidad: '', observaciones: ''
        };
        $("#buscar-repuesto-disminuir").val('');
    });
    $("#modal-traslado-almacenes-repuesto").on("hidden.bs.modal", function () {
        app.trasladoDataRepuesto = {
            almacen_origen: '', almacen_destino: '', repuesto_id: '',
            repuestos: [], nota: ''
        };
        $("#buscar-repuesto-traslado").val('');
    });

    // ===== DROPDOWN DELEGATES =====
    $(document).on("click", "#add-rep-dropdown", function () { $("#add-rep").click(); });
    $(document).on("click", ".btnBorrar-dropdown", function () { $(".btnBorrar").click(); });

    // ===== HISTORIAL STOCK =====
    $("#modal-historial-stock-repuesto").on("shown.bs.modal", function () {
        app.cargarHistorialStock();
    });
    $("#modal-historial-stock-repuesto").on("hidden.bs.modal", function () {
        if ($.fn.DataTable.isDataTable("#tabla-historial-stock-repuesto")) {
            $("#tabla-historial-stock-repuesto").DataTable().destroy();
        }
    });

    // ===== VISTA TABLA / GRID =====
    $("#btn-table-view").on("click", function () {
        $("#table-view").show();
        $("#grid-view").hide();
        $(this).removeClass("bg-white text-rojo").addClass("bg-rojo text-white");
        $("#btn-grid-view").removeClass("bg-rojo text-white").addClass("bg-white text-rojo");
    });
    $("#btn-grid-view").on("click", function () {
        $("#table-view").hide();
        $("#grid-view").show();
        $(this).removeClass("bg-white text-rojo").addClass("bg-rojo text-white");
        $("#btn-table-view").removeClass("bg-rojo text-white").addClass("bg-white text-rojo");
    });
});
