<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Informe.php";
require_once "app/models/InformeTemplate.php";

class InformePDF extends Controller
{
    private $mpdf;
    private $conexion;

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
    try {
        // Evitar que notices/warnings rompan la salida del PDF
        if (function_exists('ini_set')) {
            @ini_set('display_errors', '0');
            @ini_set('html_errors', '0');
            @ini_set('zlib.output_compression', '0');
        }

        $informe = new Informe();
        $informe->setIdInforme($id_informe);
        $informe->obtenerInforme();

    $numeroCorrelativo = $this->formatearNumeroInforme($informe->getNumero(), $informe->getFechaCreacion());
    $fechaInforme = $this->formatearFechaEspanol($informe->getFechaCreacion());
    
    $this->mpdf->SetTitle($informe->getTitulo() . " " . $numeroCorrelativo);
    
    // Obtener las URLs de las imágenes de la plantilla (para encabezado/pie)
    $template = new InformeTemplate();
    $template->obtenerTemplateActual();
    $headerImageUrl = $template->getHeaderImageUrl();
    $footerImageUrl = $template->getFooterImageUrl();
    
    // Optimizar imágenes antes de usarlas
    $headerImageUrl = $this->optimizarImagenParaPDF($headerImageUrl);
    $footerImageUrl = $this->optimizarImagenParaPDF($footerImageUrl);

    // Definir el HTML del encabezado y pie de página
    $headerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $this->escaparFuenteImagen($headerImageUrl) . "' style='width: 100%; margin: 0;'>
    </div>";
    
    $footerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $this->escaparFuenteImagen($footerImageUrl) . "' style='width: 100%; margin: 0;'>
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
    $imagen1Path = $informe->getImagen1RutaPDF();
    $imagen2Path = $informe->getImagen2RutaPDF();
    $tieneImagenes = (bool)($imagen1Path || $imagen2Path);
    
    // PÁGINA 1: Contenido del informe
    $this->mpdf->AddPage();
    
    // HTML de la primera página (contenido actual)
    $html = "
    <div style='margin-top: 30px;'></div>
    
    <!-- Información del informe -->
    <div style='text-align: center; margin-bottom: 30px;'>
        <h1 style='color: #000; font-size: 14pt; margin-bottom: 10px;'>" . $numeroCorrelativo . "</h1>
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
               <td style='padding: 5px 0;'>" . $fechaInforme . "</td>
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

    // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
    $this->escribirHTMLEnFragmentos($html);
    
    // 🖼️ PÁGINA 2: Imágenes del informe + INFORMACIÓN DE CONTACTO (si existen imágenes)
    if ($tieneImagenes) {
        $this->mpdf->AddPage();
        
        $htmlImagenes = "<div style='margin-top: 30px;'></div>
        <div style='text-align: center; margin-bottom: 30px;'>
            <h2 style='color: #000; font-size: 14pt;'>ANEXOS - IMÁGENES</h2>
        </div>";
        
        // MOSTRAR IMÁGENES LADO A LADO - OPTIMIZADO PARA VELOCIDAD
        $htmlImagenes .= "<div style='margin: 0 15mm;'>";
        
        $htmlImagenes .= $this->generarHtmlImagenes($imagen1Path, $imagen2Path);
        
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
        
        // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
        $this->escribirHTMLEnFragmentos($htmlImagenes);
    }

        $this->mpdf->Output("Informe_" . $numeroCorrelativo . ".pdf", "I");
        
    } catch (Exception $e) {
        // En caso de error, mostrar mensaje simple
        error_log("Error generando PDF de informe: " . $e->getMessage());
        header('Content-Type: text/plain');
        echo "Error generando PDF: " . $e->getMessage();
    }
}

// Método para generar PDF como base64 (para vista previa en navegador)
public function generarInformePDFBase64($id_informe)
{
    $informe = new Informe();
    $informe->setIdInforme($id_informe);
    $informe->obtenerInforme();

    $numeroCorrelativo = $this->formatearNumeroInforme($informe->getNumero(), $informe->getFechaCreacion());
    $fechaInforme = $this->formatearFechaEspanol($informe->getFechaCreacion());
    
    $this->mpdf->SetTitle($informe->getTitulo() . " " . $numeroCorrelativo);
    
    // Obtener las URLs de las imágenes de la plantilla (para encabezado/pie)
    $template = new InformeTemplate();
    $template->obtenerTemplateActual();
    $headerImageUrl = $template->getHeaderImageUrl();
    $footerImageUrl = $template->getFooterImageUrl();
    
    // Optimizar imágenes antes de usarlas
    $headerImageUrl = $this->optimizarImagenParaPDF($headerImageUrl);
    $footerImageUrl = $this->optimizarImagenParaPDF($footerImageUrl);

    // Definir el HTML del encabezado y pie de página
    $headerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $this->escaparFuenteImagen($headerImageUrl) . "' style='width: 100%; margin: 0;'>
    </div>";
    
    $footerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $this->escaparFuenteImagen($footerImageUrl) . "' style='width: 100%; margin: 0;'>
    </div>";

    // Configurar el encabezado y pie de página
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->SetHTMLFooter($footerHTML);
    
    // Configurar márgenes
    $headerHeight = 50;
    $footerHeight = 30;
    
    $this->mpdf->SetMargins(15, 15, $headerHeight);
    $this->mpdf->SetAutoPageBreak(true, $footerHeight);
    
    // VERIFICAR SI HAY IMÁGENES DEL INFORME
    $imagen1Path = $informe->getImagen1RutaPDF();
    $imagen2Path = $informe->getImagen2RutaPDF();
    $tieneImagenes = (bool)($imagen1Path || $imagen2Path);
    
    // PÁGINA 1: Contenido del informe
    $this->mpdf->AddPage();
    
    // HTML de la primera página (contenido actual)
    $html = "
    <div style='margin-top: 30px;'></div>
    
    <!-- Información del informe -->
    <div style='text-align: center; margin-bottom: 30px;'>
        <h1 style='color: #000; font-size: 14pt; margin-bottom: 10px;'>" . $numeroCorrelativo . "</h1>
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
               <td style='padding: 5px 0;'>" . $fechaInforme . "</td>
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

    // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
    $this->escribirHTMLEnFragmentos($html);
    
    // 🖼️ PÁGINA 2: Imágenes del informe + INFORMACIÓN DE CONTACTO (si existen imágenes)
    if ($tieneImagenes) {
        $this->mpdf->AddPage();
        
        $htmlImagenes = "<div style='margin-top: 30px;'></div>
        <div style='text-align: center; margin-bottom: 30px;'>
            <h2 style='color: #000; font-size: 14pt;'>ANEXOS - IMÁGENES</h2>
        </div>";
        
        // MOSTRAR IMÁGENES LADO A LADO - OPTIMIZADO PARA VELOCIDAD
        $htmlImagenes .= "<div style='margin: 0 15mm;'>";
        
        $htmlImagenes .= $this->generarHtmlImagenes($imagen1Path, $imagen2Path);
        
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
                        <td style='font-size: 9px; width: 50%; text-align: 20px;'>
                            <strong>Judy Rodriguez N.</strong><br>
                            <strong>Gerente General</strong><br>
                            Teléfono: 355-4701
                        </td>
                    </tr>
                </table>
            </div>
        </div>";
        
        $htmlImagenes .= "</div>";
        
        // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
        $this->escribirHTMLEnFragmentos($htmlImagenes);
    }
    
    // Devolver el PDF como base64 para la vista previa
    return base64_encode($this->mpdf->Output('', 'S'));
}
// REEMPLAZAR el método generarVistaPreviaPDF completo
public function generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image, $imagen1_informe = null, $imagen2_informe = null)
{
    // Para vista previa, generar un número correlativo de ejemplo
    $anio = date('Y');
    $numeroEjemplo = "INFORME NRO.XXX-$anio-JVC";
    $fechaEjemplo = $this->formatearFechaEspanol(date('Y-m-d'));
    
    $this->mpdf->SetTitle($titulo . " " . $numeroEjemplo);
    
    // Optimizar imágenes antes de usarlas
    $header_image = $this->optimizarImagenParaPDF($header_image);
    $footer_image = $this->optimizarImagenParaPDF($footer_image);
    
    // Definir el HTML del encabezado y pie de página (membretes)
    $headerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $this->escaparFuenteImagen($header_image) . "' style='width: 100%; margin: 0;'>
    </div>";
    
    $footerHTML = "<div style='width: 100%; padding: 0; margin: 0;'>
        <img src='" . $this->escaparFuenteImagen($footer_image) . "' style='width: 100%; margin: 0;'>
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
                 <td style='padding: 5px 0;'>" . $fechaEjemplo . "</td>
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

    // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
    $this->escribirHTMLEnFragmentos($html);
    
    // 🖼️ PÁGINA 2: Imágenes del informe + INFORMACIÓN DE CONTACTO (si existen imágenes)
    if ($tieneImagenes) {
        $this->mpdf->AddPage();
        
        $htmlImagenes = "<div style='margin-top: 30px;'></div>
        <div style='text-align: center; margin-bottom: 30px;'>
            <h2 style='color: #000; font-size: 14pt;'>ANEXOS - IMÁGENES</h2>
        </div>";
        
        // MOSTRAR IMÁGENES LADO A LADO
        $htmlImagenes .= "<div style='margin: 0 15mm;'>";
        
        $htmlImagenes .= $this->generarHtmlImagenes($imagen1_informe, $imagen2_informe);
        
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
        
        // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
        $this->escribirHTMLEnFragmentos($htmlImagenes);
    }
    
    // Devolver el PDF como base64 para la vista previa
    return base64_encode($this->mpdf->Output('', 'S'));
}

    private function formatearNumeroInforme($numero, $fechaCreacion)
    {
        $fecha = new DateTimeImmutable($fechaCreacion, new DateTimeZone('America/Lima'));
        return sprintf('INFORME NRO.%03d-%04d-JVC', (int)$numero, (int)$fecha->format('Y'));
    }

    private function escaparFuenteImagen($fuente)
    {
        return htmlspecialchars((string)$fuente, ENT_QUOTES, 'UTF-8');
    }

    private function generarHtmlImagenes($imagen1, $imagen2)
    {
        $imagen1 = $imagen1 ? $this->escaparFuenteImagen($imagen1) : null;
        $imagen2 = $imagen2 ? $this->escaparFuenteImagen($imagen2) : null;

        if ($imagen1 && $imagen2) {
            return "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='width: 50%; text-align: center; padding-right: 10px; vertical-align: top;'>
                        <img src='" . $imagen1 . "' style='max-width: 100%; max-height: 250px; margin: 0 auto;'>
                    </td>
                    <td style='width: 50%; text-align: center; padding-left: 10px; vertical-align: top;'>
                        <img src='" . $imagen2 . "' style='max-width: 100%; max-height: 250px; margin: 0 auto;'>
                    </td>
                </tr>
            </table>";
        }

        $imagen = $imagen1 ?: $imagen2;
        if (!$imagen) {
            return '';
        }

        return "
            <div style='text-align: center; margin-bottom: 30px;'>
                <img src='" . $imagen . "' style='max-width: 100%; max-height: 300px; margin: 0 auto;'>
            </div>";
    }

    private function formatearFechaEspanol($fecha)
    {
        $zonaHoraria = new DateTimeZone('America/Lima');
        $fechaDocumento = new DateTimeImmutable($fecha, $zonaHoraria);

        if (class_exists('IntlDateFormatter')) {
            $formateador = new IntlDateFormatter(
                'es_PE',
                IntlDateFormatter::NONE,
                IntlDateFormatter::NONE,
                'America/Lima',
                IntlDateFormatter::GREGORIAN,
                "dd 'de' MMMM 'del' yyyy"
            );
            $fechaFormateada = $formateador->format($fechaDocumento);
            if ($fechaFormateada !== false) {
                return function_exists('mb_strtolower')
                    ? mb_strtolower($fechaFormateada, 'UTF-8')
                    : strtolower($fechaFormateada);
            }
        }

        $meses = [
            1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];

        return sprintf(
            '%02d de %s del %04d',
            (int)$fechaDocumento->format('d'),
            $meses[(int)$fechaDocumento->format('n')],
            (int)$fechaDocumento->format('Y')
        );
    }

    /**
     * Escribe HTML en fragmentos más pequeños para evitar el error pcre.backtrack_limit
     */
    private function escribirHTMLEnFragmentos($html)
    {
        // Tamaño máximo por fragmento (50KB)
        $maxSize = 50000;
        
        // Si el HTML es pequeño, escribirlo directamente
        if (strlen($html) <= $maxSize) {
            $this->mpdf->WriteHTML($html);
            return;
        }
        
        // Dividir el HTML en fragmentos respetando las etiquetas
        $fragmentos = $this->dividirHTMLEnFragmentos($html, $maxSize);
        
        foreach ($fragmentos as $fragmento) {
            if (!empty(trim($fragmento))) {
                $this->mpdf->WriteHTML($fragmento);
            }
        }
    }
    
    /**
     * Divide el HTML en fragmentos respetando las etiquetas HTML
     */
    private function dividirHTMLEnFragmentos($html, $maxSize)
    {
        $fragmentos = [];
        $posicionActual = 0;
        $longitudHTML = strlen($html);
        
        while ($posicionActual < $longitudHTML) {
            $finFragmento = min($posicionActual + $maxSize, $longitudHTML);
            
            // Si no estamos al final del HTML, buscar un punto de corte seguro
            if ($finFragmento < $longitudHTML) {
                // Buscar el final de una etiqueta o un espacio
                $ultimoEspacio = strrpos(substr($html, $posicionActual, $maxSize), ' ');
                $ultimaEtiqueta = strrpos(substr($html, $posicionActual, $maxSize), '>');
                
                // Usar el punto de corte más tardío que sea seguro
                $puntoCorte = max($ultimoEspacio, $ultimaEtiqueta);
                
                if ($puntoCorte !== false && $puntoCorte > 100) {
                    $finFragmento = $posicionActual + $puntoCorte + 1;
                }
            }
            
            $fragmento = substr($html, $posicionActual, $finFragmento - $posicionActual);
            $fragmentos[] = $fragmento;
            $posicionActual = $finFragmento;
        }
        
        return $fragmentos;
    }
    
    /**
     * Optimiza una imagen usando técnicas avanzadas para mantener máxima calidad
     */
    private function optimizarImagenParaPDF($imagenUrl)
    {
        // Si no es una imagen base64, devolverla tal como está
        if (!$imagenUrl || strpos($imagenUrl, 'data:image/') !== 0) {
            return $imagenUrl;
        }
        
        try {
            // Extraer los datos base64
            $parts = explode(',', $imagenUrl);
            if (count($parts) !== 2) {
                return $imagenUrl;
            }
            
            $imageData = base64_decode($parts[1]);
            $originalSize = strlen($imageData);
            
            // Si la imagen ya es pequeña (menos de 300KB), aplicar solo optimización ligera
            if ($originalSize < 300 * 1024) {
                return $this->optimizacionLigera($imagenUrl, $parts, $imageData);
            }
            
            $image = imagecreatefromstring($imageData);
            
            if ($image === false) {
                return $imagenUrl;
            }
            
            // Aplicar filtros de mejora de calidad ANTES del redimensionado
            $image = $this->aplicarFiltrosCalidad($image);
            
            // Obtener dimensiones originales
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Calcular dimensiones óptimas de manera inteligente
            $dimensiones = $this->calcularDimensionesOptimas($width, $height, $originalSize);
            
            // Redimensionar solo si es beneficioso
            if ($dimensiones['resize']) {
                $image = $this->redimensionarConCalidadMaxima($image, $width, $height, 
                    $dimensiones['newWidth'], $dimensiones['newHeight'], strpos($parts[0], 'png') !== false);
            }
            
            // Determinar el mejor formato de salida
            $formatoOptimo = $this->determinarFormatoOptimo($parts[0], $image);
            
            // Generar imagen optimizada con la mejor calidad posible
            $optimizedData = $this->generarImagenOptimizada($image, $formatoOptimo);
            
            imagedestroy($image);
            
            // Verificar que la optimización sea efectiva
            $newSize = strlen($optimizedData);
            if ($newSize >= $originalSize * 0.9 && $originalSize < 2 * 1024 * 1024) {
                // Si no se redujo significativamente y es < 2MB, usar original
                return $imagenUrl;
            }
            
            return 'data:' . $formatoOptimo['mime'] . ';base64,' . base64_encode($optimizedData);
            
        } catch (Exception $e) {
            error_log("Error optimizando imagen: " . $e->getMessage());
            return $imagenUrl;
        }
    }
    
    /**
     * Aplica optimización ligera para imágenes pequeñas
     */
    private function optimizacionLigera($imagenUrl, $parts, $imageData)
    {
        $image = imagecreatefromstring($imageData);
        if ($image === false) return $imagenUrl;
        
        // Solo aplicar filtros de mejora sin redimensionar
        $image = $this->aplicarFiltrosCalidad($image);
        
        $isPng = strpos($parts[0], 'png') !== false;
        $isTransparent = $this->tieneTransparencia($image);
        
        ob_start();
        if ($isPng && $isTransparent) {
            imagepng($image, null, 5); // Menos compresión para mejor calidad
        } else {
            imagejpeg($image, null, 92); // Calidad muy alta
        }
        $optimizedData = ob_get_clean();
        
        imagedestroy($image);
        
        $mimeType = ($isPng && $isTransparent) ? 'image/png' : 'image/jpeg';
        return 'data:' . $mimeType . ';base64,' . base64_encode($optimizedData);
    }
    
    /**
     * Aplica filtros para mejorar la calidad de la imagen
     */
    private function aplicarFiltrosCalidad($image)
    {
        // Aplicar filtro de nitidez suave
        $sharpenMatrix = array(
            array(0, -1, 0),
            array(-1, 5, -1),
            array(0, -1, 0)
        );
        $divisor = 1;
        $offset = 0;
        imageconvolution($image, $sharpenMatrix, $divisor, $offset);
        
        // Mejorar contraste ligeramente
        imagefilter($image, IMG_FILTER_CONTRAST, -5);
        
        return $image;
    }
    
    /**
     * Calcula las dimensiones óptimas basado en contenido y tamaño
     */
    private function calcularDimensionesOptimas($width, $height, $fileSize)
    {
        // Límites más generosos para mejor calidad
        $maxWidth = 1200;
        $maxHeight = 400;
        
        // Factor de decisión basado en el tamaño del archivo
        $sizeFactor = $fileSize / (1024 * 1024); // MB
        
        // Ajustar límites dinámicamente
        if ($sizeFactor > 3) {
            $maxWidth = 1000;
            $maxHeight = 350;
        } elseif ($sizeFactor < 1) {
            $maxWidth = 1400;
            $maxHeight = 450;
        }
        
        $needsResize = ($width > $maxWidth || $height > $maxHeight || $sizeFactor > 2);
        
        if (!$needsResize) {
            return ['resize' => false];
        }
        
        // Calcular ratio manteniendo proporción
        $ratioW = $maxWidth / $width;
        $ratioH = $maxHeight / $height;
        $ratio = min($ratioW, $ratioH, 1.0);
        
        return [
            'resize' => true,
            'newWidth' => intval($width * $ratio),
            'newHeight' => intval($height * $ratio)
        ];
    }
    
    /**
     * Redimensiona con la máxima calidad posible
     */
    private function redimensionarConCalidadMaxima($image, $width, $height, $newWidth, $newHeight, $isPng)
    {
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Configurar para máxima calidad
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        
        if ($isPng) {
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefill($resizedImage, 0, 0, $transparent);
        } else {
            // Fondo blanco para JPEG
            $white = imagecolorallocate($resizedImage, 255, 255, 255);
            imagefill($resizedImage, 0, 0, $white);
        }
        
        // Usar el mejor algoritmo de redimensionado
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        imagedestroy($image);
        return $resizedImage;
    }
    
    /**
     * Determina el formato óptimo para la salida
     */
    private function determinarFormatoOptimo($originalMime, $image)
    {
        $isPng = strpos($originalMime, 'png') !== false;
        $hasTransparency = $this->tieneTransparencia($image);
        
        if ($isPng && $hasTransparency) {
            return ['format' => 'png', 'mime' => 'image/png'];
        }
        
        // Para membretes sin transparencia, JPEG suele ser mejor
        return ['format' => 'jpeg', 'mime' => 'image/jpeg'];
    }
    
    /**
     * Genera la imagen optimizada con la mejor calidad
     */
    private function generarImagenOptimizada($image, $formato)
    {
        ob_start();
        
        if ($formato['format'] === 'png') {
            // PNG con compresión mínima para máxima calidad
            imagepng($image, null, 3);
        } else {
            // JPEG con calidad muy alta
            imagejpeg($image, null, 95);
        }
        
        return ob_get_clean();
    }
    
    /**
     * Detecta si una imagen tiene transparencia
     */
    private function tieneTransparencia($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Muestrear algunos puntos para detectar transparencia
        $samplePoints = min(100, $width * $height); // Máximo 100 puntos
        
        for ($i = 0; $i < $samplePoints; $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            $color = imagecolorat($image, $x, $y);
            $alpha = ($color & 0x7F000000) >> 24;
            
            if ($alpha > 0) {
                return true; // Tiene transparencia
            }
        }
        
        return false;
    }

}
