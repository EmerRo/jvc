// public/js/modulo-documentos/modulo-documentos.js
// Objeto para manejar la navegación principal
const ModuloDocumentos = {
  // Mantener registro de los módulos inicializados
  modulosInicializados: {
    ficha_tecnica: false,
  },

  init: function () {
    console.log("Inicializando módulo de documentos...");
    // Inicializar eventos si es necesario
  },

  // Función para cargar archivos según el tipo
  cargarArchivos: function (tipo) {
    console.log("Cargando archivos de tipo:", tipo);

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
        // Ya se carga automáticamente al cargar la página
        this.modulosInicializados.ficha_tecnica = true;
        break;

      default:
        console.log("Tipo de archivo no reconocido:", tipo);
    }
  },
};

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => ModuloDocumentos.init());

// Función global para cargar archivos
function cargarArchivos(tipo) {
  ModuloDocumentos.cargarArchivos(tipo);
}
