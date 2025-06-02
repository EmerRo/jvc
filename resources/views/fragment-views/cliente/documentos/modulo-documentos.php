<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script> -->
<!-- resources\views\fragment-views\cliente\documentos\modulo-documentos.php -->
<!-- Sistema de Gestión de Archivos Técnicos -->
<script>
    // Función para cargar PDF.js de manera asíncrona
    function loadPdfJs() {
        return new Promise((resolve, reject) => {
            // Verificar si ya está cargado
            if (window.pdfjsLib && typeof window.pdfjsLib.getDocument === 'function') {
                console.log('PDF.js ya está cargado');
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
                
                // Esperar un poco para asegurarse de que todo esté inicializado
                setTimeout(resolve, 100);
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    // Cargar PDF.js
    loadPdfJs().then(() => {
        console.log('PDF.js cargado correctamente');
    }).catch(err => {
        console.error('Error al cargar PDF.js:', err);
    });
</script>

<div class="container-fluid py-4">
    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="archivosTabsContent">
        <!-- FICHAS TÉCNICAS -->
        <div class="tab-pane fade show active" id="fichas" role="tabpanel" aria-labelledby="fichas-tab">
            <?php include 'componentes/fichas-tecnicas.php'; ?>
        </div>
    </div>
</div>

<!-- Modales compartidos -->
<?php include 'componentes/modales.php'; ?>

<!-- Al final del archivo modulo-documentos.php -->
<script src="<?= URL::to('public/js/modulo-documentos/modulo-documentos.js') ?>?v=<?= time() ?>"></script>