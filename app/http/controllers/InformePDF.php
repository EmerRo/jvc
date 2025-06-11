

<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Informe.php";
require_once "app/models/InformeTemplate.php";

class InformePDF extends Controller
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
            'margin_footer' => 0,
        ]);
        $this->conexion = (new Conexion())->getConexion();
    }
    
// REEMPLAZAR el método generarInformePDF completo
public function generarInformePDF($id_informe)
{
    $informe = new Informe();
    $informe->setIdInforme($id_informe);
    $informe->obtenerInforme();

    // Generar número correlativo
    $numeroCorrelativo = $informe->generarNumeroCorrelativo($informe->getTipo());
    
    $this->mpdf->SetTitle($informe->getTitulo() . " " . $numeroCorrelativo);
    
    // Obtener las URLs de las imágenes
    $headerImageUrl = $informe->getHeaderImage();
    $footerImageUrl = $informe->getFooterImage();

    // Si no hay imágenes específicas, usar las de la plantilla
    if (!$headerImageUrl || !$footerImageUrl) {
        $template = new InformeTemplate();
        $template->obtenerTemplateActual();
        
        if (!$headerImageUrl) {
            $headerImageUrl = $template->getHeaderImageUrl();
        }
        
        if (!$footerImageUrl) {
            $footerImageUrl = $template->getFooterImageUrl();
        }
    }

    // Definir el HTML del encabezado y pie de página
    $headerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $headerImageUrl . "' style='width: 100%; margin: 0;'>
    </div>";
    
    $footerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $footerImageUrl . "' style='width: 100%; margin: 0;'>
    </div>";

    // Configurar el encabezado y pie de página
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->SetHTMLFooter($footerHTML);
    
    // Configurar márgenes
    $headerHeight = 50;
    $footerHeight = 30;
    
    $this->mpdf->SetMargins(15, 15, $headerHeight);
    $this->mpdf->SetAutoPageBreak(true, $footerHeight);
    
    // Añadir la página
    $this->mpdf->AddPage();
    
    // Construir el HTML del contenido con la información completa
    $html = "
    <div style='margin-top: 30px;'></div>
    
    <!-- Información del informe -->
<div style='text-align: center; margin-bottom: 30px;'>
        <h1 style='color: #000; font-size: 14pt; margin-bottom: 10px; '>" . strtoupper($informe->getTitulo()) . " " . $numeroCorrelativo . "</h1>
     <!-- <h2 style='color: #000; font-size: 12pt; margin-bottom: 5px;'>" . $informe->getTitulo() . "</h2> -->
    </div>
    
    <!-- Información de la empresa y cliente -->
    <div style='margin: 0 15mm; margin-bottom: 5px;'>
        <table style='width: 100%; border-collapse: collapse; font-size: 12px;'>
            <tr>
                <td style='width: 15%; font-weight: bold; padding: 5px 0;'>DE:</td>
                <td style='width: 85%; padding: 5px 0;'>" . ($informe->getEmpresaRazonSocial() ?: 'COMERCIAL & INDUSTRIAL J.V.C. S.A.C.') . "</td>
            </tr>";
    
    // Agregar información del cliente si existe
    if ($informe->getClienteNombre()) {
        $html .= "
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>A:</td>
                <td style='padding: 5px 0;'>" . $informe->getClienteNombre() . "  </td>
            </tr>";
             
    }
    
    $html .= "
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Documento:</td>
                <td style='padding: 5px 0;'>" . $informe->getClienteDocumento() . "</td>
            </tr>
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Asunto:</td>
                <td style='padding: 5px 0;'>" . $informe->getTitulo() . "</td>
            </tr>
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Tipo:</td>
                <td style='padding: 5px 0;'>" . $informe->getTipo() . "</td>
            </tr>
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Fecha:</td>
               <td style='padding: 5px 0;'>" . date('d \d\e F \d\e\l Y', strtotime($informe->getFechaCreacion())) . "</td>
            </tr>
        </table>
    </div>
    
    <hr style='margin: 0 15mm; border: none; border-top: 1px solid #ccc;'>
    
    <!-- Contenido del informe -->
    <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
    
    // Agregar el contenido del informe
    $html .= $informe->getContenido();
    $html .= "</div>";

    $this->mpdf->WriteHTML($html);
    $this->mpdf->Output("Informe_" . $numeroCorrelativo . ".pdf", "I");
}
// REEMPLAZAR el método generarVistaPreviaPDF completo
public function generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image)
{
    // Para vista previa, generar un número correlativo de ejemplo
    $anio = date('Y');
    $numeroEjemplo = "NRO.XXX-$anio-JVC";
    
    $this->mpdf->SetTitle($titulo . " " . $numeroEjemplo);
    
    // Definir el HTML del encabezado y pie de página
    $headerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $header_image . "' style='width: 100%; margin: 0;'>
    </div>";
    
    $footerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $footer_image . "' style='width: 100%; margin: 0;'>
    </div>";

    // Configurar el encabezado y pie de página
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->SetHTMLFooter($footerHTML);
    
    $headerHeight = 50;
    $footerHeight = 30;
    
    $this->mpdf->SetMargins(0, 0, $headerHeight);
    $this->mpdf->SetAutoPageBreak(true, $footerHeight);
    
    // Añadir la página
    $this->mpdf->AddPage();
    
    // Construir el HTML del contenido
    $html = "
    <div style='margin-top: 30px;'></div>
    
    <!-- Información del informe -->
    <div style='text-align: center; margin-bottom: 30px;'>
        <h1 style='color: #000; font-size: 18pt; margin-bottom: 10px;'>VISTA PREVIA " . $numeroEjemplo . "</h1>
        <h2 style='color: #000; font-size: 16pt; margin-bottom: 20px;'>" . $titulo . "</h2>
    </div>
    
    <!-- Información de ejemplo -->
    <div style='margin: 0 15mm; margin-bottom: 20px;'>
        <table style='width: 100%; border-collapse: collapse; font-size: 12px;'>
            <tr>
                <td style='width: 15%; font-weight: bold; padding: 5px 0;'>DE:</td>
                <td style='width: 85%; padding: 5px 0;'>COMERCIAL & INDUSTRIAL J.V.C. S.A.C.</td>
            </tr>
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>A:</td>
                <td style='padding: 5px 0;'>CLIENTE DE EJEMPLO</td>
            </tr>
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Asunto:</td>
                <td style='padding: 5px 0;'>" . $titulo . "</td>
            </tr>
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Fecha:</td>
                <td style='padding: 5px 0;'>" . date('d \d\e F \d\e\l Y') . "</td>
            </tr>
        </table>
    </div>
    
    <hr style='margin: 20px 15mm; border: none; border-top: 1px solid #ccc;'>
    
    <!-- Contenido del informe -->
    <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
    
    // Agregar el contenido del informe
    $html .= $contenido;
    $html .= "</div>";

    $this->mpdf->WriteHTML($html);
    
    // Devolver el PDF como base64 para la vista previa
    return base64_encode($this->mpdf->Output('', 'S'));
}
}