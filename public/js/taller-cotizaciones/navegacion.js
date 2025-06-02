// Agregar el manejo del evento popstate para la navegación del navegador
window.addEventListener('popstate', function(event) {
    // Obtener la URL actual
    const currentUrl = window.location.href;
    
    // Verificar si estamos en la página de edición
    if (currentUrl.includes('/edt/coti/taller')) {
      // Obtener el ID de la cotización de la URL
      const urlParams = new URLSearchParams(window.location.search);
      const cotizacionId = urlParams.get('id');
      
      if (cotizacionId) {
        // Si existe una instancia de Vue, recargar los datos
        if (window.app) {
          window.app.cargarDatosCotizacion(cotizacionId);
        }
      }
    } else if (currentUrl.includes('/taller/coti/view')) {
      // Si volvemos a la vista de lista, recargar la tabla
      if (window.tabla) {
        window.tabla.ajax.reload();
      }
    }
  });
  