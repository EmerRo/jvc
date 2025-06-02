

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
            'margin_footer' => 0
        ]);
        $this->conexion = (new Conexion())->getConexion();
    }
    
public function generarInformePDF($id_informe)
{
    $informe = new Informe();
    $informe->setIdInforme($id_informe);
    $informe->obtenerInforme();

    $this->mpdf->SetTitle($informe->getTitulo());
    
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
    $headerHeight = 50; // altura aproximada en mm
    $footerHeight = 30; // altura aproximada en mm
    
    // Corregir el uso de SetMargins (izquierda, derecha, superior)
    $this->mpdf->SetMargins(15, 15, $headerHeight);
    
    // Configurar el salto de página automático
    $this->mpdf->SetAutoPageBreak(true, $footerHeight);
    
    // Añadir la página
    $this->mpdf->AddPage();
    
    // HTML con márgenes laterales para el título
    $html = "
    <div style='margin-top: 30px;'></div>  <!-- Espacio adicional después del encabezado -->
    
    <h2 style='text-align: center; color: #000; margin-top: 20px; margin-bottom: 20px; margin-left: 15mm; margin-right: 15mm; font-size: 16pt;'>" . $informe->getTitulo() . "</h2>
    
    <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
    
    // Agregar el contenido del informe
    $html .= $informe->getContenido();
    $html .= "</div>";

    $this->mpdf->WriteHTML($html);
    $this->mpdf->Output("Informe_" . $informe->getTipo() . ".pdf", "I");
}
public function generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image)
{
    $this->mpdf->SetTitle($titulo);
    
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
    
    // Aumentar el valor del margen superior para dar más espacio
    $headerHeight = 50; // aumentado de 40 a 50mm
    $footerHeight = 30; // altura aproximada en mm
    
    // Corregido: solo 3 parámetros para SetMargins
    $this->mpdf->SetMargins(0, 0, $headerHeight);
    
    // Configurar el salto de página automático
    $this->mpdf->SetAutoPageBreak(true, $footerHeight);
    
    // Añadir la página
    $this->mpdf->AddPage();
    
    // Construir el HTML del contenido con más espacio después del encabezado
    $html = "
    <div style='margin-top: 30px;'></div>  <!-- Espacio adicional después del encabezado -->
    
    <h2 style='text-align: center; color: #000; margin-top: 20px; margin-bottom: 20px; font-size: 16pt;'>" . $titulo . "</h2>
    
    <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
    
    // Agregar el contenido del informe
    $html .= $contenido;
    $html .= "</div>";

    $this->mpdf->WriteHTML($html);
    
    // Devolver el PDF como base64 para la vista previa
    return base64_encode($this->mpdf->Output('', 'S'));
}
}