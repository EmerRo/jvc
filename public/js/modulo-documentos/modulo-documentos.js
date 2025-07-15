// public/js/modulo-documentos/modulo-documentos.js
// Objeto para manejar la navegación principal

// Usar window para evitar problemas de redeclaración
window.ModuloDocumentos = window.ModuloDocumentos || {
  // Mantener registro de los módulos inicializados
  modulosInicializados: {
    ficha_tecnica: false,
  },

  // Flag para evitar múltiples inicializaciones
  inicializado: false,

  init: function () {
    if (this.inicializado) {
      console.log("Módulo de documentos ya inicializado, omitiendo reinicialización.");
      return;
    }
    
    console.log("Inicializando módulo de documentos...");
    this.inicializado = true;
    
    // Inicializar eventos si es necesario
    this.inicializarEventos();
  },

  // Función para inicializar eventos
  inicializarEventos: function() {
    // console.log("Inicializando eventos del módulo de documentos...");
    // Aquí puedes agregar event listeners específicos del módulo
  },

  // Función para reinicializar cuando se navega de vuelta al módulo
  reinicializar: function() {
    // console.log("Reinicializando módulo de documentos...");
    
    // Limpiar estados anteriores
    this.limpiar();
    
    // Resetear flags
    this.modulosInicializados.ficha_tecnica = false;
    this.inicializado = false;
    
    // Reinicializar
    this.init();
    
    // Verificar si necesitamos inicializar las funciones de fichas técnicas
    this.verificarFichasTecnicas();
  },

  // Función para verificar y inicializar fichas técnicas
  verificarFichasTecnicas: function() {
    // console.log("Verificando funciones de fichas técnicas...");
    
    // Esperar a que las funciones estén disponibles
    const intentarInicializar = () => {
      if (typeof window.cargarFichas === 'function') {
        // console.log('Función cargarFichas disponible, inicializando fichas técnicas...');
        
        if (!this.modulosInicializados.ficha_tecnica) {
          this.modulosInicializados.ficha_tecnica = true;
          
          // Pequeño delay para asegurar que el DOM esté listo
          setTimeout(() => {
            window.cargarFichas();
          }, 100);
        }
      } else {
        // console.log('Función cargarFichas no disponible aún, reintentando...');
        // Reintentar después de un delay
        setTimeout(intentarInicializar, 200);
      }
    };
    
    intentarInicializar();
  },

  // Función para cargar archivos según el tipo
  cargarArchivos: function (tipo) {
    // console.log("Cargando archivos de tipo:", tipo);

    // Evitar inicializar el mismo módulo múltiples veces
    if (this.modulosInicializados[tipo]) {
      console.log(
        `El módulo ${tipo} ya está inicializado, omitiendo reinicialización.`
      );
      return;
    }

    // Dependiendo del tipo, inicializar el módulo correspondiente
    switch (tipo) {
      case "ficha_tecnica":
        console.log("Inicializando módulo de fichas técnicas...");
        this.modulosInicializados.ficha_tecnica = true;
        
        // Verificar si existe la función cargarFichas
        if (typeof window.cargarFichas === 'function') {
          setTimeout(() => {
            window.cargarFichas();
          }, 100);
        } else {
          console.log('Función cargarFichas no disponible aún');
        }
        break;

      default:
        console.log("Tipo de archivo no reconocido:", tipo);
    }
  },

  // Función para limpiar el módulo cuando se navega fuera
  limpiar: function() {
    console.log("Limpiando módulo de documentos...");
    
    // Limpiar tareas de renderizado de PDF si existen
    if (typeof window.limpiarTareasRenderizado === 'function') {
      window.limpiarTareasRenderizado();
    }
    
    // Limpiar eventos específicos del módulo de fichas técnicas
    if (typeof window.limpiarEventosDuplicados === 'function') {
      window.limpiarEventosDuplicados();
    }
    
    // Resetear estados
    this.modulosInicializados.ficha_tecnica = false;
    this.inicializado = false;
    
    // console.log("Módulo de documentos limpiado");
  },

  // Función para manejar errores
  manejarError: function(error, contexto) {
    console.error(`Error en ${contexto}:`, error);
    
    // Mostrar mensaje de error al usuario si es necesario
    const contenedor = document.getElementById('fichas');
    if (contenedor && contexto.includes('fichas')) {
      contenedor.innerHTML = `
        <div class="alert alert-danger" role="alert">
          <h4 class="alert-heading">Error</h4>
          <p>Ocurrió un error al cargar el contenido. Por favor, intenta nuevamente.</p>
          <button class="btn btn-outline-danger" onclick="window.ModuloDocumentos.reinicializar()">
            Reintentar
          </button>
        </div>
      `;
    }
  }
};

// Inicializar cuando el DOM esté listo, pero solo si no está ya inicializado
document.addEventListener("DOMContentLoaded", function() {
  if (!window.ModuloDocumentos.inicializado) {
    window.ModuloDocumentos.init();
  }
});

// Función global para cargar archivos
function cargarArchivos(tipo) {
  if (window.ModuloDocumentos) {
    window.ModuloDocumentos.cargarArchivos(tipo);
  } else {
    console.error('ModuloDocumentos no está disponible');
  }
}

// Función global para reinicializar el módulo
function reinicializarModuloDocumentos() {
  if (window.ModuloDocumentos) {
    window.ModuloDocumentos.reinicializar();
  } else {
    console.error('ModuloDocumentos no está disponible para reinicializar');
  }
}

// Función global para limpiar el módulo
function limpiarModuloDocumentos() {
  if (window.ModuloDocumentos) {
    window.ModuloDocumentos.limpiar();
  }
}