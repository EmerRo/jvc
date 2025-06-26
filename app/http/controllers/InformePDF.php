

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
    
    // Obtener las URLs de las imágenes de la plantilla (para encabezado/pie)
    $template = new InformeTemplate();
    $template->obtenerTemplateActual();
    $headerImageUrl = $template->getHeaderImageUrl();
    $footerImageUrl = $template->getFooterImageUrl();

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
    
    // OBTENER INFORMACIÓN DEL USUARIO LOGUEADO
    $usuario_actual = [];
    if (isset($_SESSION['usuario_id'])) {
        $query = "SELECT 
                       u.nombres,
                       u.telefono,
                       r.nombre as rol
                     FROM usuarios u
                     INNER JOIN roles r ON r.rol_id = u.id_rol
                     WHERE u.usuario_id = " . $_SESSION['usuario_id'];
        
        $result = $this->conexion->query($query);
        if ($result && $result->num_rows > 0) {
            $usuario_actual = $result->fetch_assoc();
        }
    }
    
    // VERIFICAR SI HAY IMÁGENES DEL INFORME
    $tieneImagenes = ($informe->getHeaderImage() || $informe->getFooterImage());
    
    // PÁGINA 1: Contenido del informe
    $this->mpdf->AddPage();
    
    // HTML de la primera página (contenido actual)
    $html = "
    <div style='margin-top: 30px;'></div>
    
    <!-- Información del informe -->
    <div style='text-align: center; margin-bottom: 30px;'>
        <h1 style='color: #000; font-size: 14pt; margin-bottom: 10px;'>" . strtoupper($informe->getTitulo()) . " " . $numeroCorrelativo . "</h1>
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
                <td style='padding: 5px 0;'>" . $informe->getClienteNombre() . "</td>
            </tr>";
    }
    
    $html .= "
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Documento:</td>
                <td style='padding: 5px 0;'>" . $informe->getClienteDocumento() . "</td>
            </tr>
            <tr>
                <td style='font-weight: bold; padding: 5px 0;'>Dirigido a:</td>
                <td style='padding: 5px 0;'>" . $informe->getPersonaEntregar() . "</td>
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

    // ⚠️ INFORMACIÓN DE CONTACTO SOLO SI NO HAY IMÁGENES
    if (!$tieneImagenes) {
        $html .= "
        <div style='margin-top: 30px; padding: 0 15mm;'>
            <p style='font-size: 12px; margin: 0; padding: 0;'>Esperando vernos favorecidos con su preferencia, nos despedimos.</p>
            <p style='font-size: 12px; margin: 3px 0 0 0; padding: 0;'>Atentamente,</p>
            
            <div style='width: 100%; clear: both; padding-top: 20px;'>
                <table style='width: 100%;'>
                    <tr>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>" . ($usuario_actual['nombres'] ?? 'Usuario vendedor') . "</strong><br>
                            <strong>" . ($usuario_actual['rol'] ?? 'ADMIN') . "</strong><br>
                            Teléfono: 355-4701<br>
                            Cel: " . ($usuario_actual['telefono'] ?? '993321920') . "
                        </td>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>Judy Rodriguez N.</strong><br>
                            <strong>Gerente General</strong><br>
                            Teléfono: 355-4701
                        </td>
                    </tr>
                </table>
            </div>
        </div>";
    }

    $this->mpdf->WriteHTML($html);
    
    // 🖼️ PÁGINA 2: Imágenes del informe + INFORMACIÓN DE CONTACTO (si existen imágenes)
    if ($tieneImagenes) {
        $this->mpdf->AddPage();
        
        $htmlImagenes = "<div style='margin-top: 30px;'></div>
        <div style='text-align: center; margin-bottom: 30px;'>
            <h2 style='color: #000; font-size: 14pt;'>ANEXOS - IMÁGENES</h2>
        </div>";
        
        // MOSTRAR IMÁGENES LADO A LADO
        $htmlImagenes .= "<div style='margin: 0 15mm;'>";
        
        if ($informe->getHeaderImage() && $informe->getFooterImage()) {
            // Ambas imágenes - lado a lado
            $htmlImagenes .= "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='width: 50%; text-align: center; padding-right: 10px; vertical-align: top;'>
                     <!--   <h3 style='font-size: 12pt; margin-bottom: 15px;'>Imagen 1</h3> -->
                        <img src='" . $informe->getHeaderImage() . "' style='max-width: 100%; max-height: 250px; margin: 0 auto;'>
                    </td>
                    <td style='width: 50%; text-align: center; padding-left: 10px; vertical-align: top;'>
                        <!--  <h3 style='font-size: 12pt; margin-bottom: 15px;'>Imagen 2</h3> -->
                        <img src='" . $informe->getFooterImage() . "' style='max-width: 100%; max-height: 250px; margin: 0 auto;'>
                    </td>
                </tr>
            </table>";
        } else if ($informe->getHeaderImage()) {
            // Solo primera imagen
            $htmlImagenes .= "
            <div style='text-align: center; margin-bottom: 30px;'>
              <!--  <h3 style='font-size: 12pt; margin-bottom: 15px;'>Imagen 1</h3>-->
                <img src='" . $informe->getHeaderImage() . "' style='max-width: 100%; max-height: 300px; margin: 0 auto;'>
            </div>";
        } else if ($informe->getFooterImage()) {
            // Solo segunda imagen
            $htmlImagenes .= "
            <div style='text-align: center; margin-bottom: 30px;'>
              <!--  <h3 style='font-size: 12pt; margin-bottom: 15px;'>Imagen 2</h3> -->
                <img src='" . $informe->getFooterImage() . "' style='max-width: 100%; max-height: 300px; margin: 0 auto;'>
            </div>";
        }
        
        // ✅ INFORMACIÓN DE CONTACTO EN LA 2DA PÁGINA (CON IMÁGENES)
        $htmlImagenes .= "
        <div style='margin-top: 40px; padding: 0;'>
            <p style='font-size: 12px; margin: 0; padding: 0;'>Esperando vernos favorecidos con su preferencia, nos despedimos.</p>
            <p style='font-size: 12px; margin: 3px 0 0 0; padding: 0;'>Atentamente,</p>
            
            <div style='width: 100%; clear: both; padding-top: 20px;'>
                <table style='width: 100%;'>
                    <tr>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>" . ($usuario_actual['nombres'] ?? 'Usuario vendedor') . "</strong><br>
                            <strong>" . ($usuario_actual['rol'] ?? 'ADMIN') . "</strong><br>
                            Teléfono: 355-4701<br>
                            Cel: " . ($usuario_actual['telefono'] ?? '993321920') . "
                        </td>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>Judy Rodriguez N.</strong><br>
                            <strong>Gerente General</strong><br>
                            Teléfono: 355-4701
                        </td>
                    </tr>
                </table>
            </div>
        </div>";
        
        // // AGREGAR LÍNEA DE FIRMA
        // $htmlImagenes .= "
        // <div style='margin-top: 40px; text-align: center;'>
        //     <div style='border-top: 1px solid #000; width: 300px; margin: 0 auto;'></div>
        //     <p style='margin-top: 10px; font-size: 12px; font-weight: bold;'>FIRMA</p>
        // </div>";
        
        $htmlImagenes .= "</div>";
        
        $this->mpdf->WriteHTML($htmlImagenes);
    }

    $this->mpdf->Output("Informe_" . $numeroCorrelativo . ".pdf", "I");
}
// REEMPLAZAR el método generarVistaPreviaPDF completo
public function generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image, $imagen1_informe = null, $imagen2_informe = null)
{
    // Para vista previa, generar un número correlativo de ejemplo
    $anio = date('Y');
    $numeroEjemplo = "NRO.XXX-$anio-JVC";
    
    $this->mpdf->SetTitle($titulo . " " . $numeroEjemplo);
    
    // Definir el HTML del encabezado y pie de página (membretes)
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
    
    $this->mpdf->SetMargins(15, 15, $headerHeight);
    $this->mpdf->SetAutoPageBreak(true, $footerHeight);
    
    // VERIFICAR SI HAY IMÁGENES DEL INFORME
    $tieneImagenes = ($imagen1_informe || $imagen2_informe);
    
    // PÁGINA 1: Contenido del informe
    $this->mpdf->AddPage();
    
    // HTML de la primera página
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

    // ⚠️ INFORMACIÓN DE CONTACTO SOLO SI NO HAY IMÁGENES
    if (!$tieneImagenes) {
        $html .= "
        <div style='margin-top: 30px; padding: 0 15mm;'>
            <p style='font-size: 12px; margin: 0; padding: 0;'>Esperando vernos favorecidos con su preferencia, nos despedimos.</p>
            <p style='font-size: 12px; margin: 3px 0 0 0; padding: 0;'>Atentamente,</p>
            
            <div style='width: 100%; clear: both; padding-top: 20px;'>
                <table style='width: 100%;'>
                    <tr>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>Usuario Ejemplo</strong><br>
                            <strong>ADMIN</strong><br>
                            Teléfono: 355-4701<br>
                            Cel: 993321920
                        </td>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>Judy Rodriguez N.</strong><br>
                            <strong>Gerente General</strong><br>
                            Teléfono: 355-4701
                        </td>
                    </tr>
                </table>
            </div>
        </div>";
    }

    $this->mpdf->WriteHTML($html);
    
    // 🖼️ PÁGINA 2: Imágenes del informe + INFORMACIÓN DE CONTACTO (si existen imágenes)
    if ($tieneImagenes) {
        $this->mpdf->AddPage();
        
        $htmlImagenes = "<div style='margin-top: 30px;'></div>
        <div style='text-align: center; margin-bottom: 30px;'>
            <h2 style='color: #000; font-size: 14pt;'>ANEXOS - IMÁGENES</h2>
        </div>";
        
        // MOSTRAR IMÁGENES LADO A LADO
        $htmlImagenes .= "<div style='margin: 0 15mm;'>";
        
        if ($imagen1_informe && $imagen2_informe) {
            // Ambas imágenes - lado a lado
            $htmlImagenes .= "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='width: 50%; text-align: center; padding-right: 10px; vertical-align: top;'>
                        <img src='" . $imagen1_informe . "' style='max-width: 100%; max-height: 250px; margin: 0 auto;'>
                    </td>
                    <td style='width: 50%; text-align: center; padding-left: 10px; vertical-align: top;'>
                        <img src='" . $imagen2_informe . "' style='max-width: 100%; max-height: 250px; margin: 0 auto;'>
                    </td>
                </tr>
            </table>";
        } else if ($imagen1_informe) {
            // Solo primera imagen
            $htmlImagenes .= "
            <div style='text-align: center; margin-bottom: 30px;'>
                <img src='" . $imagen1_informe . "' style='max-width: 100%; max-height: 300px; margin: 0 auto;'>
            </div>";
        } else if ($imagen2_informe) {
            // Solo segunda imagen
            $htmlImagenes .= "
            <div style='text-align: center; margin-bottom: 30px;'>
                <img src='" . $imagen2_informe . "' style='max-width: 100%; max-height: 300px; margin: 0 auto;'>
            </div>";
        }
        
        // ✅ INFORMACIÓN DE CONTACTO EN LA 2DA PÁGINA (CON IMÁGENES)
        $htmlImagenes .= "
        <div style='margin-top: 40px; padding: 0;'>
            <p style='font-size: 12px; margin: 0; padding: 0;'>Esperando vernos favorecidos con su preferencia, nos despedimos.</p>
            <p style='font-size: 12px; margin: 3px 0 0 0; padding: 0;'>Atentamente,</p>
            
            <div style='width: 100%; clear: both; padding-top: 20px;'>
                <table style='width: 100%;'>
                    <tr>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>Usuario Ejemplo</strong><br>
                            <strong>ADMIN</strong><br>
                            Teléfono: 355-4701<br>
                            Cel: 993321920
                        </td>
                        <td style='font-size: 9px; width: 50%; text-align: center; color: #033668'>
                            <strong>Judy Rodriguez N.</strong><br>
                            <strong>Gerente General</strong><br>
                            Teléfono: 355-4701
                        </td>
                    </tr>
                </table>
            </div>
        </div>";
        
        $htmlImagenes .= "</div>";
        
        $this->mpdf->WriteHTML($htmlImagenes);
    }
    
    // Devolver el PDF como base64 para la vista previa
    return base64_encode($this->mpdf->Output('', 'S'));
}

}