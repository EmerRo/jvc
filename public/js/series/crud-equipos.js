//  public\js\series\crud-equipos.js
 // Función para mostrar alertas con SweetAlert2
 function mostrarAlerta(titulo, mensaje, tipo) {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo,
        confirmButtonText: "Aceptar",
        confirmButtonColor: "#3085d6",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
    });
}

// Función para confirmar eliminación
function confirmarEliminacion(mensaje) {
    return Swal.fire({
        title: "¿Está seguro?",
        text: mensaje,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        customClass: {
            confirmButton: "btn btn-danger me-2",
            cancelButton: "btn btn-secondary",
        },
        buttonsStyling: false,
    });
}

// Función genérica para habilitar edición en línea
function habilitarEdicionEnLinea(td, tipo) {
    const texto = td.text();
    const id = td.parent().data("id");
    td.html(`
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" value="${texto}">
            <button class="btn btn-success btn-guardar-${tipo}" data-id="${id}">
                <i class="fa fa-check"></i>
            </button>
            <button class="btn btn-danger btn-cancelar-edicion">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `);
}

// Función genérica para cancelar edición
function cancelarEdicion(td, texto) {
    td.html(texto);
}
        // Event listeners para edición en línea
        $(document).on("click", ".editar-marca", function () {
            const td = $(this).closest("tr").find(".nombre-campo");
            habilitarEdicionEnLinea(td, "marca");
        });

        $(document).on("click", ".editar-modelo", function () {
            var $tr      = $(this).closest("tr");
            var id       = $tr.data("id");
            var marcaId  = $tr.data("marca-id") || '';
            var nombre   = $tr.find(".nombre-campo").text();

            var opcionesMarca = '<option value="">-- Marca --</option>' +
                _allMarcas.map(m => `<option value="${m.id}" ${String(m.id)===String(marcaId)?'selected':''}>${m.nombre}</option>`).join('');

            $tr.find(".marca-campo").html(`<select class="form-select form-select-sm edit-marca-sel" style="min-width:100px">${opcionesMarca}</select>`);
            $tr.find(".nombre-campo").html(`
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" value="${nombre}">
                    <button class="btn btn-success btn-guardar-modelo" data-id="${id}"><i class="fa fa-check"></i></button>
                    <button class="btn btn-danger btn-cancelar-edicion"><i class="fa fa-times"></i></button>
                </div>`);
        });

        $(document).on("click", ".editar-equipo", function () {
            var $tr      = $(this).closest("tr");
            var id       = $tr.data("id");
            var marcaId  = $tr.data("marca-id")  || '';
            var modeloId = $tr.data("modelo-id") || '';
            var nombre   = $tr.find(".nombre-campo").text();

            var opcionesMarca = '<option value="">-- Marca --</option>' +
                _allMarcas.map(m => `<option value="${m.id}" ${String(m.id)===String(marcaId)?'selected':''}>${m.nombre}</option>`).join('');

            var modelosFiltrados = marcaId ? _allModelos.filter(m => String(m.marca_id) === String(marcaId)) : _allModelos;
            var opcionesModelo = '<option value="">-- Modelo --</option>' +
                modelosFiltrados.map(m => `<option value="${m.id}" ${String(m.id)===String(modeloId)?'selected':''}>${m.nombre}</option>`).join('');

            $tr.find(".marca-campo").html(`<select class="form-select form-select-sm edit-marca-eq-sel" style="min-width:100px">${opcionesMarca}</select>`);
            $tr.find(".modelo-campo").html(`<select class="form-select form-select-sm edit-modelo-eq-sel" style="min-width:100px">${opcionesModelo}</select>`);
            $tr.find(".nombre-campo").html(`
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" value="${nombre}">
                    <button class="btn btn-success btn-guardar-equipo" data-id="${id}"><i class="fa fa-check"></i></button>
                    <button class="btn btn-danger btn-cancelar-edicion"><i class="fa fa-times"></i></button>
                </div>`);
        });

        // Cascade en inline-edit equipo: marca → modelos
        $(document).on("change", ".edit-marca-eq-sel", function () {
            var marcaId = $(this).val();
            var modelosFiltrados = marcaId ? _allModelos.filter(m => String(m.marca_id) === String(marcaId)) : _allModelos;
            var opcionesModelo = '<option value="">-- Modelo --</option>' +
                modelosFiltrados.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
            $(this).closest("tr").find(".edit-modelo-eq-sel").html(opcionesModelo);
        });

        // Guardar edición en línea
        $(document).on("click", ".btn-guardar-marca", function () {
            const id = $(this).data("id");
            const td = $(this).closest("td");
            const nuevoNombre = td.find("input").val();

            $.ajax({
                url: _URL + "/ajs/update/marcas",
                type: "POST",
                data: { id: id, nombre: nuevoNombre },
                success: (response) => {
                    cargarTablaMarcas();
                    mostrarAlerta("Éxito", "Marca actualizada correctamente", "success");
                },
                error: () => {
                    mostrarAlerta("Error", "No se pudo actualizar la marca", "error");
                },
            });
        });

        $(document).on("click", ".btn-guardar-modelo", function () {
            var id       = $(this).data("id");
            var $tr      = $(this).closest("tr");
            var nombre   = $tr.find(".nombre-campo input").val();
            var marcaId  = $tr.find(".edit-marca-sel").val() || '';

            $.ajax({
                url: _URL + "/ajs/update/modelos",
                type: "POST",
                data: { id: id, nombre: nombre, marca_id: marcaId },
                success: function () {
                    cargarTablaModelos();
                    cargarTodosLosDatos(); // refrescar memoria para cascade
                    mostrarAlerta("Éxito", "Modelo actualizado correctamente", "success");
                },
                error: function () {
                    mostrarAlerta("Error", "No se pudo actualizar el modelo", "error");
                },
            });
        });

        $(document).on("click", ".btn-guardar-equipo", function () {
            var id       = $(this).data("id");
            var $tr      = $(this).closest("tr");
            var nombre   = $tr.find(".nombre-campo input").val();
            var marcaId  = $tr.find(".edit-marca-eq-sel").val()  || '';
            var modeloId = $tr.find(".edit-modelo-eq-sel").val() || '';

            $.ajax({
                url: _URL + "/ajs/update/equipos",
                type: "POST",
                data: { id: id, nombre: nombre, marca_id: marcaId, modelo_id: modeloId },
                success: function () {
                    cargarTablaEquipos();
                    cargarTodosLosDatos();
                    mostrarAlerta("Éxito", "Equipo actualizado correctamente", "success");
                },
                error: function () {
                    mostrarAlerta("Error", "No se pudo actualizar el equipo", "error");
                },
            });
        });

        // Cancelar edición
        $(document).on("click", ".btn-cancelar-edicion", function () {
            const td = $(this).closest("td");
            const textoOriginal = td.parent().find(".nombre-campo").text();
            cancelarEdicion(td, textoOriginal);
        });

        // Agregar nueva marca
        $("#btnAgregarMarca").click(() => {
            const nombre = $("#marca_nombre").val();
            if (!nombre) {
                mostrarAlerta("Error", "Por favor ingrese un nombre de marca", "error");
                return;
            }

            $.ajax({
                url: _URL + "/ajs/save/marcas",
                type: "POST",
                data: { nombre: nombre },
                success: (response) => {
                    $("#marca_nombre").val("");
                    cargarTablaMarcas();
                    mostrarAlerta("Éxito", "Marca agregada correctamente", "success");
                },
                error: () => {
                    mostrarAlerta("Error", "No se pudo agregar la marca", "error");
                },
            });
        });

        // Agregar nuevo modelo
        $("#btnAgregarModelo").click(function () {
            var nombre  = $("#modelo_nombre").val().trim();
            var marcaId = $("#modelo_marca_id").val() || '';
            if (!nombre) {
                mostrarAlerta("Error", "Por favor ingrese un nombre de modelo", "error");
                return;
            }

            $.ajax({
                url: _URL + "/ajs/save/modelos",
                type: "POST",
                data: { nombre: nombre, marca_id: marcaId },
                success: function () {
                    $("#modelo_nombre").val("");
                    cargarTablaModelos();
                    cargarTodosLosDatos();
                    mostrarAlerta("Éxito", "Modelo agregado correctamente", "success");
                },
                error: function () {
                    mostrarAlerta("Error", "No se pudo agregar el modelo", "error");
                },
            });
        });

        // Agregar nuevo equipo
        $("#btnAgregarEquipo").click(function () {
            var nombre   = $("#equipo_nombre").val().trim();
            var marcaId  = $("#equipo_marca_id").val()  || '';
            var modeloId = $("#equipo_modelo_id").val() || '';
            if (!nombre) {
                mostrarAlerta("Error", "Por favor ingrese un nombre de equipo", "error");
                return;
            }

            $.ajax({
                url: _URL + "/ajs/save/equipos",
                type: "POST",
                data: { nombre: nombre, marca_id: marcaId, modelo_id: modeloId },
                success: function () {
                    $("#equipo_nombre").val("");
                    cargarTablaEquipos();
                    cargarTodosLosDatos();
                    mostrarAlerta("Éxito", "Equipo agregado correctamente", "success");
                },
                error: function () {
                    mostrarAlerta("Error", "No se pudo agregar el equipo", "error");
                },
            });
        });

        // Eliminar registros
        $(document).on("click", ".eliminar-marca", function () {
            const id = $(this).closest("tr").data("id");
            confirmarEliminacion("¿Está seguro de eliminar esta marca?").then(
                (result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: _URL + "/ajs/delete/marcas",
                            type: "POST",
                            data: { id: id },
                            success: () => {
                                cargarTablaMarcas();
                                mostrarAlerta(
                                    "Éxito",
                                    "Marca eliminada correctamente",
                                    "success"
                                );
                            },
                            error: () => {
                                mostrarAlerta("Error", "No se pudo eliminar la marca", "error");
                            },
                        });
                    }
                }
            );
        });

        $(document).on("click", ".eliminar-modelo", function () {
            const id = $(this).closest("tr").data("id");
            confirmarEliminacion("¿Está seguro de eliminar este modelo?").then(
                (result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: _URL + "/ajs/delete/modelos",
                            type: "POST",
                            data: { id: id },
                            success: function () {
                                cargarTablaModelos();
                                cargarTodosLosDatos();
                                mostrarAlerta("Éxito", "Modelo eliminado correctamente", "success");
                            },
                            error: function () {
                                mostrarAlerta("Error", "No se pudo eliminar el modelo", "error");
                            },
                        });
                    }
                }
            );
        });

        $(document).on("click", ".eliminar-equipo", function () {
            const id = $(this).closest("tr").data("id");
            confirmarEliminacion("¿Está seguro de eliminar este equipo?").then(
                (result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: _URL + "/ajs/delete/equipos",
                            type: "POST",
                            data: { id: id },
                            success: function () {
                                cargarTablaEquipos();
                                cargarTodosLosDatos();
                                mostrarAlerta("Éxito", "Equipo eliminado correctamente", "success");
                            },
                            error: function () {
                                mostrarAlerta("Error", "No se pudo eliminar el equipo", "error");
                            },
                        });
                    }
                }
            );
        });

        // Al abrir los modales de gestión, inicializar sus selects
        $('#modalModelo').on('show.bs.modal', function () {
            if (_datosListos) inicializarSelectsModales();
        });
        $('#modalEquipo').on('show.bs.modal', function () {
            if (_datosListos) inicializarSelectsModales();
        });