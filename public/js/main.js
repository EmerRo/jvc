var temp;

$(document).ready(function () {
    $(".jvc-sidebar").on("click", ".jvc-sidebar-link", function (evt) {
        const href = $(this).attr('href');

        // Si el enlace es solo un ancla '#' (para desplegables), no hagas nada.
        if (href === '#') {
            // El comportamiento del desplegable es manejado por otro script en nav-bar.php
            return;
        }

        // Muestra el loader para dar feedback visual antes de la recarga
        $("#loader-menor").show();

        // No se usa evt.preventDefault(), por lo que el enlace seguirá su curso
        // y recargará la página a la nueva URL.
    });

    $("#contenedor-app").on("click", ".button-link", function (evt) {
        // Muestra el loader para dar feedback visual antes de la recarga
        $("#loader-menor").show();

        // No se usa evt.preventDefault(), el enlace seguirá su curso normal.
    });
});

// FUNCIÓN PARA LIMPIAR MÓDULOS ACTIVOS
function limpiarModulosActivos() {
    console.log('Limpiando módulos activos antes de navegación...');
    
    // DESHABILITADO: Interferencia con Vue en módulos de ventas
    // Verificar si estamos en una ruta de ventas
    if (window.location.href.includes('/ventas')) {
        console.log('🎯 Saltando limpieza para módulo de ventas');
        return;
    }
    
    // CORRECCIÓN: Limpiar filtro de taller antes de navegar
    if (typeof window.limpiarFiltroTaller === 'function') {
        window.limpiarFiltroTaller();
    }
    
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