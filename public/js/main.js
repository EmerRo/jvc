var temp;

$(document).ready(function () {
    $(".jvc-sidebar").on("click", ".jvc-sidebar-link", function (evt) {
        if ($(this).attr('href') === '#') {
            return;
        }
        evt.preventDefault();
        
        // LIMPIAR MÓDULOS ACTIVOS ANTES DE NAVEGAR
        limpiarModulosActivos();
        
        $("#loader-menor").show();
        const url_ter = $(evt.currentTarget).attr('href');
        const titulo = $(evt.currentTarget).attr('title');
        console.log(url_ter, titulo);
        renombrarURL(url_ter, titulo);
        _ajaxDOM(url_ter, 'contenedor-app');
    });

    $("#contenedor-app").on("click", ".button-link", function (evt) {
        evt.preventDefault();
        
        // LIMPIAR MÓDULOS ACTIVOS ANTES DE NAVEGAR
        limpiarModulosActivos();
        
        const url_ter = $(evt.currentTarget).attr('href');
        const titulo = $(evt.currentTarget).attr('title');
        renombrarURL(url_ter, titulo);
        _ajaxDOM(url_ter, 'contenedor-app');
    });
});

// FUNCIÓN PARA LIMPIAR MÓDULOS ACTIVOS
function limpiarModulosActivos() {
    console.log('Limpiando módulos activos antes de navegación...');
    
    // Limpiar módulos de documentos
    if (typeof limpiarTodosLosModulos === 'function') {
        limpiarTodosLosModulos();
    }
    
    // Limpiar eventos globales de jQuery UI si existen
    if ($.ui) {
        $(document).off('.ui-autocomplete');
        $('.ui-autocomplete').remove();
    }
    
    // Limpiar modales de Bootstrap que puedan estar abiertos
    $('.modal').modal('hide');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    
    // Limpiar eventos de Quill si existen
    if (typeof Quill !== 'undefined') {
        $('.ql-toolbar').remove();
        $('.ql-editor').remove();
    }
    
    // Limpiar eventos globales del documento
    $(document).off('.informes');
    $(document).off('.documentos');
    $(document).off('.cartas');
    $(document).off('.constancias');
    
    console.log('Módulos activos limpiados');
}

function reselc_estadop() {
    $(".menu-link").removeClass("active");
}