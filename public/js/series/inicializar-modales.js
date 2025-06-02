
    //public\js\series\inicializar-modales.js  Inicialización
    $("#modalMarca").on("show.bs.modal", cargarTablaMarcas);
    $("#modalModelo").on("show.bs.modal", cargarTablaModelos);
    $("#modalEquipo").on("show.bs.modal", cargarTablaEquipos);

    // Seleccionar marca desde el modal
    $(document).on("click", "#tablaMarcas tr", function () {
        const marcaId = $(this).data("id");
        const nombreMarca = $(this).find(".nombre-campo").text();
    
        // Determinar qué campo debe actualizarse
        if ($("#modalMarca").data("target-input")) {
            const targetInput = $("#modalMarca").data("target-input");
            // Verificar si es un select o un input
            if ($(targetInput).is("select")) {
                // Si no existe la opción, crearla
                if ($(targetInput).find(`option[value="${marcaId}"]`).length === 0) {
                    $(targetInput).append(`<option value="${marcaId}">${nombreMarca}</option>`);
                }
                $(targetInput).val(marcaId).trigger('change');
            } else {
                $(targetInput).val(nombreMarca);
            }
        } else {
            // Por defecto, actualizar el campo común
            if ($("#marca_comun").is("select")) {
                // Si no existe la opción, crearla
                if ($("#marca_comun").find(`option[value="${marcaId}"]`).length === 0) {
                    $("#marca_comun").append(`<option value="${marcaId}">${nombreMarca}</option>`);
                }
                $("#marca_comun").val(marcaId).trigger('change');
            } else {
                $("#marca_comun").val(nombreMarca);
            }
        }
    
        $("#modalMarca").modal("hide");
    });

   // Inicialización de modales
    $("#modalMarca").on("show.bs.modal", function () {
        cargarTablaMarcas();
    });

    $("#modalModelo").on("show.bs.modal", function () {
        cargarTablaModelos();
    });

    $("#modalEquipo").on("show.bs.modal", function () {
        cargarTablaEquipos();
    });
    // Limpiar formulario al abrir el modal de agregar
$('#modalAgregar').on('show.bs.modal', function () {
    limpiarFormularioRegistro();
});