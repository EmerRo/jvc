$(document).ready(function() {
    // Botones para reportes de inventario en la página principal
    $('#btn-reporte-inventario-pdf').on('click', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const idCotizacion = urlParams.get('id');
        
        if (idCotizacion) {
            window.open(_URL + '/r/taller/inventario/' + idCotizacion, '_blank');
        } else if (app && app.venta && app.venta.id_cotizacion) {
            window.open(_URL + '/r/taller/inventario/' + app.venta.id_cotizacion, '_blank');
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Primero debe guardar la cotización para generar el reporte'
            });
        }
    });
    
    $('#btn-reporte-inventario-excel').on('click', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const idCotizacion = urlParams.get('id');
        
        if (idCotizacion) {
            window.open(_URL + '/r/taller/inventario/excel/' + idCotizacion, '_blank');
        } else if (app && app.venta && app.venta.id_cotizacion) {
            window.open(_URL + '/r/taller/inventario/excel/' + app.venta.id_cotizacion, '_blank');
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Primero debe guardar la cotización para generar el reporte'
            });
        }
    });
    
    // Botones para reportes de inventario en el modal de éxito
    $('#btn-inventario-pdf-modal, #btn-inventario-excel-modal').on('click', function(e) {
        e.preventDefault();
        
        const idCotizacion = $('#cotizacion-numero').text().split('/')[0];
        const isPDF = $(this).attr('id') === 'btn-inventario-pdf-modal';
        
        if (idCotizacion) {
            if (isPDF) {
                window.open(_URL + '/r/taller/inventario/' + idCotizacion, '_blank');
            } else {
                window.open(_URL + '/r/taller/inventario/excel/' + idCotizacion, '_blank');
            }
        }
    });
});