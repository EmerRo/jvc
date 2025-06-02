<?php
// Modificación de la clase CertificadoGarantia

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Garantia.php";
require_once "app/models/CertificadoTemplate.php";

class CertificadoGarantia extends Controller
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
    
public function garantiaCertificado($id_garantia)
{
    $garantia = new Garantia();
    $garantia->setIdGarantia($id_garantia);
    $garantia->obtenerGarantia();

    // Obtener la plantilla del certificado
    $certificadoTemplate = new CertificadoTemplate();
    $certificadoTemplate->obtenerCertificadoActual();

    // Generar el título con formato GARANTIA 000001/2025
    $anioActual = date('Y');
    $numeroFormateado = str_pad($id_garantia, 6, '0', STR_PAD_LEFT);
    $tituloPersonalizado = "GARANTIA {$numeroFormateado}/{$anioActual}";

    // Configuración de mPDF para mantener el aspecto original
    $this->mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8', 
        'format' => 'A4',
        'margin_left' => 0,
        'margin_right' => 0,
        'margin_top' => 48,    // Espacio para el encabezado
        'margin_bottom' => 35, // Espacio para el pie de página
        'margin_header' => 0,
        'margin_footer' => 0
    ]);
    
    $this->mpdf->SetTitle("Certificado de Garantía");
    
    // Obtener las URLs de las imágenes desde la plantilla
    $headerImageUrl = $certificadoTemplate->getHeaderImageUrl();
    $footerImageUrl = $certificadoTemplate->getFooterImageUrl();
    
    // Definir el encabezado HTML que se repetirá en cada página
    $headerHTML = '<div style="width: 100%; text-align: center; margin: 0; padding: 0;">
                      <img src="' . $headerImageUrl . '" style="width: 100%; margin: 0; padding: 0;">
                   </div>';
    
    // Definir el pie de página HTML que se repetirá en cada página
    $footerHTML = '<div style="width: 100%; text-align: center; margin: 0; padding: 0;">
                      <img src="' . $footerImageUrl . '" style="width: 100%; margin: 0; padding: 0;">
                   </div>';
    
    // Establecer el encabezado y pie de página
    $this->mpdf->SetHTMLHeader($headerHTML);
    $this->mpdf->SetHTMLFooter($footerHTML);
    
    // Activar el salto de página automático
    $this->mpdf->SetAutoPageBreak(true, 35);
    
    // Iniciar el contenido HTML con margen interno para el contenido
    $html = '
    <!-- Aplicamos un div contenedor con padding para todo el contenido -->
    <div style="padding: 0 30px;">
        <!-- Agregamos margen superior para separar del encabezado -->
        <h2 style="text-align: center; color: #000; margin-top: 25px; margin-bottom: 30px;">' . $tituloPersonalizado . '</h2>
        
        <div style="font-size: 12px; text-align: justify;">';
        
    // Agregar el contenido personalizado del certificado si existe
    if ($certificadoTemplate->getContenido()) {
        $html .= $certificadoTemplate->getContenido();
    } else {
        // Contenido por defecto si no hay plantilla personalizada
        $html .= '
        <p style="margin: 0;"><strong>COMERCIAL & INDUSTRIAL J.V.C. S.A.C.</strong> Garantiza estas Máquinas de uso Industrial, por el término de 12 meses a partir de la fecha de compra, presentando este Certificado de Garantía y la Factura original dentro del plazo antes mencionado.</p>
        <p style="margin: 0;"> Esta garantía cubre todo defecto o falla de fabricación y/o ensamblaje que pudiera producirse en las máquinas.</p>
        <p><strong>COMERCIAL & INDUSTRIAL J.V.C. S.A.C.</strong> asegura que estos Equipos cumple con las normas de seguridad vigentes.</p>
        <p>Las condiciones de uso, instalación y mantenimiento necesarias de este equipo deberán hacerse siguiendo y respetando las especificaciones técnicas, instalación, indicación, y consejo que se formulan en el Manual de Instrucciones que forma parte de esta garantía.</p>
        <p><strong>La presente Garantía dejará de tener validez cuando:</strong></p>
        <ol style="padding-left: 20px; margin: 0; list-style-type: lower-alpha;">
            <li>La etiqueta de identificación y/o número de serie hubiera sido dañado, alterado o quitado.</li>
            <li>Hayan intervenido personas ajenas al Servicio Técnico de la Firma.</li>
            <li>No se presente la factura de compra, o la misma tuviera enmiendas y/o faltare la fecha de compra.</li>
            <li>Se verifique que los daños fueron causados por cualquier factor ajeno al uso normal del equipo.</li>
            <li>Se verifique que los daños se hayan producido por el transporte después de la compra, golpes o accidentes de cualquier naturaleza.</li>
            <li>Se verifique mala manipulación del equipo y/o mal uso del mismo.</li>
            <li>El usuario no realice el mantenimiento preventivo y/o correctivo del equipo anualmente para una buena durabilidad del motor.</li>
        </ol>
        <p>En caso de falla del equipo, el consumidor deberá llamar a nuestro <strong>CENTRO DE SERVICIO TÉCNICO: 980088015.</strong> Cuando el examen realizado por nuestro Personal Técnico sobre el producto y la documentación pertinente, determine que rigen los términos de la garantía, el mismo será reparado sin cargo alguno.</p>';
    }
    
    // Agregar la información específica de la garantía con mejor espaciado
    $html .= '
    <div style="margin-top: 30px;">
        <div style="margin-bottom: 10px;">
            <span style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;" unselectable="on" class="unselectable"><strong>CLIENTE:</strong> ' . $garantia->getCliente() . '</span>
        </div>
        <div style="margin-bottom: 10px;">
            <span style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;" unselectable="on" class="unselectable"><strong>EQUIPO:</strong></span>
        </div>';
    
    // Obtener todas las series asociadas a esta garantía
    $series = $garantia->obtenerSeries();
    
    if (empty($series)) {
        // Si no hay series múltiples, mostrar la información básica con mejor espaciado
        $html .= '
        <div style="margin-bottom: 10px; display: flex; flex-wrap: wrap;">
            <div style="min-width: 250px; margin-bottom: 5px;">
                <span style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;" unselectable="on" class="unselectable"><strong>MARCA:</strong> ' . $garantia->getMarcaNombre() . '</span>
            </div>
            <div style="min-width: 250px; margin-bottom: 5px;">
                <span style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;" unselectable="on" class="unselectable"><strong>MODELO:</strong> ' . $garantia->getModeloNombre() . '</span>
            </div>
            <div style="min-width: 250px; margin-bottom: 5px;">
                <span style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;" unselectable="on" class="unselectable"><strong>SERIE:</strong> ' . $garantia->getNumeroSerie() . '</span>
            </div>
        </div>';
    } else {
        // Si hay múltiples series, mostrarlas en una tabla con mejor espaciado
        $html .= '
        <div style="margin-bottom: 15px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: center;">N°</th>
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">MARCA</th>
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">MODELO</th>
                        <th style="border: 1px solid #ddd; padding: 6px; text-align: left;">SERIE</th>
                    </tr>
                </thead>
                <tbody>';
        
        $contador = 1;
        foreach ($series as $serie) {
            $html .= '
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 6px; text-align: center;">' . $contador . '</td>
                        <td style="border: 1px solid #ddd; padding: 6px;">' . $serie['marca_nombre'] . '</td>
                        <td style="border: 1px solid #ddd; padding: 6px;">' . $serie['modelo_nombre'] . '</td>
                        <td style="border: 1px solid #ddd; padding: 6px;">' . $serie['numero_serie'] . '</td>
                    </tr>';
            $contador++;
        }
        
        $html .= '
                </tbody>
            </table>
        </div>';
    }
    
    // Fechas con mejor alineación y espaciado
    $html .= '
        <div style="display: flex; flex-wrap: wrap; margin-top: 15px;">
            <div style="min-width: 260px; margin-bottom: 5px;">
                <span style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;" unselectable="on" class="unselectable"><strong>FECHA DE INICIO:</strong> ' . $garantia->getFechaInicio() . '</span>
            </div>
            <div style="min-width: 260px; margin-bottom: 5px;">
                <span style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;" unselectable="on" class="unselectable"><strong>FECHA DE CADUCIDAD:</strong> ' . $garantia->getFechaCaducidad() . '</span>
            </div>
        </div>
    </div>
    </div>
    </div>'; // Cerramos los tres divs contenedores

    // Escribir el HTML al PDF
    $this->mpdf->WriteHTML($html);
    
    // Generar el PDF
    $this->mpdf->Output("Certificado_Garantia.pdf", "I");
}
    // Nuevo método para subir y guardar imágenes
    public function guardarImagenes()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirigir o mostrar error
            return;
        }
        
        $certificadoId = isset($_POST['certificado_id']) ? $_POST['certificado_id'] : null;
        if (!$certificadoId) {
            // Manejar error: ID de certificado no proporcionado
            return;
        }
        
        $certificado = new CertificadoTemplate();
        $certificado->setId($certificadoId);
        $certificado->obtenerCertificadoActual();
        
        // Procesar imagen de encabezado
        if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
            $headerImage = $this->procesarImagen($_FILES['header_image']);
            if ($headerImage) {
                $certificado->setHeaderImage($headerImage);
            }
        }
        
        // Procesar imagen de pie de página
        if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
            $footerImage = $this->procesarImagen($_FILES['footer_image']);
            if ($footerImage) {
                $certificado->setFooterImage($footerImage);
            }
        }
        
        // Guardar los cambios
        $certificado->actualizarCertificado();
        
        // Redirigir a la página de éxito o mostrar mensaje
        // ...
    }
    
    // Método auxiliar para procesar imágenes
    private function procesarImagen($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        // Leer el archivo y convertirlo a base64
        $imageData = file_get_contents($file['tmp_name']);
        $base64 = 'data:' . $file['type'] . ';base64,' . base64_encode($imageData);
        
        return $base64;
    }
}