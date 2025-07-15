<!-- resources\views\fragment-views\cliente\documentos\modulo-documentos.php -->
<!-- Sistema de Gestión de Archivos Técnicos -->
<script>
    // Función para cargar PDF.js de manera asíncrona
    function loadPdfJs() {
        return new Promise((resolve, reject) => {
            // Verificar si ya está cargado
            if (window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function') {
                // console.log('PDF.js ya está cargado');
                resolve();
                return;
            }
            
            // Cargar el script principal de PDF.js
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js';
            script.onload = function() {
                // Una vez cargado el script principal, configurar el worker
                window.pdfjsLib = window.pdfjsLib || {};
                window.pdfjsLib.GlobalWorkerOptions = window.pdfjsLib.GlobalWorkerOptions || {};
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
                
                // Esperar un poco para asegurar que todo esté inicializado
                setTimeout(resolve, 100);
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    // Cargar PDF.js solo si no está ya cargado
    if (!window.pdfjsLib) {
        loadPdfJs().then(() => {
            // console.log('PDF.js cargado correctamente
        }).catch(err => {
            console.error('Error al cargar PDF.js:', err);
        });
    } else {
        console.log('PDF.js ya disponible');
    }

    // Función que se ejecuta cuando el módulo se carga
    function inicializarModuloDocumentos() {
        // console.log('Inicializando módulo de documentos...');
        
        // Si el módulo ya existe, reinicializarlo
        if (window.ModuloDocumentos) {
            // console.log('Reinicializando ModuloDocumentos existente...');
            window.ModuloDocumentos.reinicializar();
        }
        
        // Verificar si las funciones de fichas técnicas están disponibles
        setTimeout(() => {
            if (typeof window.cargarFichas === 'function') {
                // console.log('Función cargarFichas disponible, inicializando...');
                window.cargarFichas();
            } else {
                // console.log('Función cargarFichas no disponible aún, esperando...');
                // Intentar nuevamente después de un delay
                setTimeout(() => {
                    if (typeof window.cargarFichas === 'function') {
                        window.cargarFichas();
                    }
                }, 500);
            }
        }, 200);
    }

    // Ejecutar cuando el contenido esté listo
    document.addEventListener('DOMContentLoaded', inicializarModuloDocumentos);
    
    // Si el DOM ya está cargado (navegación AJAX), ejecutar inmediatamente
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializarModuloDocumentos);
    } else {
        // Pequeño delay para asegurar que el contenido se haya insertado
        setTimeout(inicializarModuloDocumentos, 100);
    }
</script>

<div class="container-fluid py-4">
    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="archivosTabsContent">
        <!-- FICHAS TÉCNICAS -->
        <div class="tab-pane fade show active" id="fichas" role="tabpanel" aria-labelledby="fichas-tab">
            <!-- Incluir el contenido directamente -->
            <?php include 'componentes/fichas-tecnicas.php'; ?>
        </div>
    </div>
</div>

<!-- Modales compartidos -->
<?php include 'componentes/modales.php'; ?>

<!-- Script del módulo - cargar solo una vez -->
<script>
    // Verificar si el script ya fue cargado para evitar duplicados
    if (!window.moduloDocumentosScriptCargado) {
        window.moduloDocumentosScriptCargado = true;
        
        // Crear y cargar el script dinámicamente
        const script = document.createElement('script');
        script.src = '<?= URL::to('public/js/modulo-documentos/modulo-documentos.js') ?>?v=<?= time() ?>';
        script.onload = function() {
            // console.log('Script modulo-documentos.js cargado');
            // Reinicializar el módulo después de cargar el script
            if (window.ModuloDocumentos && !window.ModuloDocumentos.inicializado) {
                window.ModuloDocumentos.init();
            }
        };
        script.onerror = function() {
            console.error('Error al cargar modulo-documentos.js');
        };
        document.head.appendChild(script);
    } else {
        // console.log('Script modulo-documentos.js ya estaba cargado');
        // Si el script ya estaba cargado, solo reinicializar
        if (window.ModuloDocumentos) {
            window.ModuloDocumentos.reinicializar();
        }
    }
</script>