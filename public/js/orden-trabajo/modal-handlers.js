// Manejadores de modales
$(document).ready(function() {
    // Inicialización de modales
    $("#modalMarca").on('show.bs.modal', cargarTablaMarcas);
    $("#modalModelo").on('show.bs.modal', cargarTablaModelos);
    $("#modalEquipo").on('show.bs.modal', cargarTablaEquipos);
    $("#modalTecnico").on('show.bs.modal', cargarTablaTecnicos);

    // Aplicar efecto a modales secundarios
    ['modalMarca', 'modalModelo', 'modalEquipo', 'modalTecnico'].forEach(handleModalBackdrop);

    // Event listeners para edición en línea
    $(document).on('click', '.editar-marca, .editar-modelo, .editar-equipo, .editar-tecnico', function() {
        const td = $(this).closest('tr').find('.nombre-campo');
        const tipo = $(this).attr('class').split('-')[1];
        habilitarEdicionEnLinea(td, tipo);
    });

    // Cancelar edición
    $(document).on('click', '.btn-cancelar-edicion', function() {
        const td = $(this).closest('td');
        const textoOriginal = td.parent().find('.nombre-campo').text();
        cancelarEdicion(td, textoOriginal);
    });

    // Set current date when Add modal opens
    $("#modalAgregar").on('show.bs.modal', function () {
        const today = new Date().toISOString().split('T')[0];
        $("#fecha_ingreso").val(today);
        app.generarNumeroSerie();
    });

    // Cargar selects iniciales
    Promise.all([
        cargarSelect('modelos', '#modelo'),
        cargarSelect('marcas', '#marca'),
        cargarSelect('equipos', '#equipo'),
        cargarSelect('modelos', '#edit_modelo'),
        cargarSelect('marcas', '#edit_marca'),
        cargarSelect('equipos', '#edit_equipo'),
        cargarSelect('tecnicos', '#atencion_Encargado')
    ]).catch(error => {
        console.error("Error al cargar los selects:", error);
        mostrarAlerta('Error', 'No se pudieron cargar algunos datos. Por favor, recarga la página.', 'error');
    });
});

