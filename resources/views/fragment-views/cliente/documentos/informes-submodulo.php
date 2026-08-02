<!-- resources\views\fragment-views\cliente\documentos\modulo-documentos.php -->
<!-- Sistema de Gestión de Archivos Técnicos -->
 <!-- Añade esto en el head de tu layout principal (por ejemplo, en header.php o layout.php) -->
<script src="<?= URL::to('public/lib/pdfjs/pdf.min.js') ?>"></script>
<script>
    // Configurar el worker de PDF.js
    window.pdfjsLib = window.pdfjsLib || {};
    window.pdfjsLib.GlobalWorkerOptions = window.pdfjsLib.GlobalWorkerOptions || {};
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= URL::to('public/lib/pdfjs/pdf.worker.min.js') ?>';
</script>
<div class="container-fluid py-4">
   
    
    <!-- Pestañas de navegación principal -->
  
    
    <!-- Contenido de las pestañas -->
    <div class="tab-content" id="archivosTabsContent">
        <!-- FICHAS TÉCNICAS -->
        <div class="tab-pane fade show active" id="fichas" role="tabpanel" aria-labelledby="fichas-tab">
            <?php include 'componentes/informes.php'; ?>
        </div>
        
    </div>
</div>

<!-- Modales compartidos -->
<?php include 'componentes/modales.php'; ?>

<!-- Al final del archivo modulo-documentos.php -->
<script src="<?= URL::to('public/js/modulo-documentos/modulo-documentos.js') ?>?v=<?= time() ?>"></script>