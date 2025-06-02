<?php
// app/http/controllers/CartaPDF.php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Carta.php";
require_once "app/models/CartaTemplate.php";

class CartaPDF extends Controller
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
    
    public function generarCartaPDF($id_carta)
    {
        $carta = new Carta();
        $carta->obtenerCarta($id_carta);

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

        $this->mpdf->SetTitle($carta->getTitulo());
        $this->mpdf->SetAutoPageBreak(false);
        $this->mpdf->AddPage();

        // Obtener las URLs de las imágenes
        $headerImageUrl = $carta->getHeaderImage();
        $footerImageUrl = $carta->getFooterImage();

        // Si no hay imágenes específicas, usar las de la plantilla
        if (!$headerImageUrl || !$footerImageUrl) {
            $template = new CartaTemplate();
            $template->obtenerTemplateActual();
            
            if (!$headerImageUrl) {
                $headerImageUrl = $template->getHeaderImageUrl();
            }
            
            if (!$footerImageUrl) {
                $footerImageUrl = $template->getFooterImageUrl();
            }
        }

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
            <img src="' . $headerImageUrl . '" alt="Encabezado">
        </div>
        
        <div class="content">
            <div class="title">' . $carta->getTitulo() . '</div>
            <div class="text">' . $carta->getContenido() . '</div>
        </div>
        
        <div class="footer">
            <img src="' . $footerImageUrl . '" alt="Pie de página">
        </div>';

        $this->mpdf->WriteHTML($html);
        $this->mpdf->Output("Carta_" . $carta->getTipo() . ".pdf", "I");
    }
    
    // Método para generar una vista previa temporal
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