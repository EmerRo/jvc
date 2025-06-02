<?php
// app/http/controllers/ArchivoInternoPDF.php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/ArchivoInterno.php";
require_once "app/models/ArchivoInternoPlantilla.php";

class ArchivoInternoPDF extends Controller
{
    private $mpdf;

    public function __construct()
    {
        // Configuración modificada para eliminar márgenes por defecto
        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);
        $this->conexion = (new Conexion())->getConexion();
    }
    
    public function generarArchivoInternoPDF($id_archivo)
    {
        $archivo = new ArchivoInterno();
        $archivo->obtenerArchivoInterno($id_archivo);
        
        // Si es un PDF subido, simplemente mostrarlo
        if ($archivo->getEsPdfSubido() && $archivo->getArchivoPdf()) {
            // Extraer el contenido base64 (eliminar el prefijo data:application/pdf;base64,)
            $pdfData = $archivo->getArchivoPdf();
            if (strpos($pdfData, 'data:application/pdf;base64,') === 0) {
                $pdfData = substr($pdfData, 28);
            }
            
            // Decodificar el PDF
            $pdfBinary = base64_decode($pdfData);
            
            // Enviar el PDF al navegador
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $archivo->getTitulo() . '.pdf"');
            echo $pdfBinary;
            exit;
        }
        
        // Si es un documento creado, generar el PDF
        $this->mpdf->SetTitle($archivo->getTitulo());
        $this->mpdf->SetAutoPageBreak(true, 0);
        $this->mpdf->AddPage('P', '', '', '', '', 0, 0, 0, 0, 0, 0);
        $this->mpdf->WriteHTML('<style>.unselectable {-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;}</style>');

        // Obtener las URLs de las imágenes
        $headerImageUrl = $archivo->getHeaderImage();
        $footerImageUrl = $archivo->getFooterImage();

        // Si no hay imágenes específicas, usar las de la plantilla
        if (!$headerImageUrl || !$footerImageUrl) {
            $template = new ArchivoInternoPlantilla();
            $template->obtenerTemplateActual();
            
            if (!$headerImageUrl) {
                $headerImageUrl = $template->getHeaderImageUrl();
            }
            
            if (!$footerImageUrl) {
                $footerImageUrl = $template->getFooterImageUrl();
            }
        }

        // Construir el HTML del documento con imágenes a ancho completo
        $html = "
        <div style='width: 100%; padding: 0; margin: 0;'>
            <img src='" . $headerImageUrl . "' style='width: 100%; margin: 0;'> 
        </div>
        
        <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
        
        // Agregar el contenido del documento
        $html .= $archivo->getContenido();
        
        // Posicionamiento absoluto para el footer en la parte inferior
        $html .= "
        <div style='position: absolute; bottom: 0; left: 0; width: 100%;'>
            <img src='" . $footerImageUrl . "' style='width: 100%; margin: 0;'>
        </div>
        ";

        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output($archivo->getTitulo() . ".pdf", "I");
    }
    
   public function generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image)
    {
        // Reinicializar mPDF para asegurar configuración limpia
        $this->mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        $this->mpdf->SetTitle($titulo);
        $this->mpdf->SetAutoPageBreak(false);
        $this->mpdf->AddPage();

        // Construir el HTML con una estructura que garantice que el pie de página esté en la parte inferior
        $html = '
        <style>
            @page {
                margin: 0;
                padding: 0;
            }
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                position: relative;
            }
            .header {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .header img {
                width: 100%;
                display: block;
            }
            .content {
                margin: 70px 15mm 100px 15mm;
                min-height: 400px;
            }
            .title {
                text-align: center;
                color: #000;
                margin-bottom: 20px;
                font-size: 18pt;
                font-weight: bold;
            }
            .text {
                font-size: 12pt;
                text-align: justify;
            }
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .footer img {
                width: 100%;
                display: block;
            }
        </style>
        
        <div class="header">
            <img src="' . $header_image . '" alt="Encabezado">
        </div>
        
        <div class="content">
            <div class="title">' . $titulo . '</div>
            <div class="text">' . $contenido . '</div>
        </div>
        
        <div class="footer">
            <img src="' . $footer_image . '" alt="Pie de página">
        </div>';

        $this->mpdf->WriteHTML($html);
        
        // Devolver el PDF como base64 para la vista previa
        return base64_encode($this->mpdf->Output('', 'S'));
    }
}