<?php
// controllers/CertificadoController.php

require_once "app/models/CertificadoTemplate.php";

class CertificadoController extends Controller
{
    public function obtenerCertificado()
    {
        // Limpiar cualquier salida previa
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Establecer el tipo de contenido correcto
        header('Content-Type: application/json');
        
        try {
            $certificado = new CertificadoTemplate();
            $resultado = $certificado->obtenerCertificadoActual();
            
            // Depuración - Registrar los datos obtenidos
            error_log("Resultado de obtenerCertificadoActual: " . ($resultado ? "true" : "false"));
            error_log("Título: " . $certificado->getTitulo());
            error_log("Contenido: " . substr($certificado->getContenido(), 0, 100) . "..."); // Mostrar los primeros 100 caracteres
            
            // Obtener las imágenes si existen
            $headerImage = $certificado->getHeaderImage();
            $footerImage = $certificado->getFooterImage();
            
            // Si no hay imágenes en la base de datos, usar las imágenes por defecto
            if (!$headerImage) {
                $headerImage = URL::to('public/img/garantia/header.png');
            }
            
            if (!$footerImage) {
                $footerImage = URL::to('public/img/garantia/footer.png');
            }
            
            // Construir la respuesta JSON
            $response = [
                'success' => true,
                'titulo' => $certificado->getTitulo() ?: 'CERTIFICADO DE GARANTÍA',
                'contenido' => $certificado->getContenido(),
                'header_image' => $headerImage,
                'footer_image' => $footerImage
            ];
            
            // Depuración - Registrar la respuesta JSON
            error_log("Respuesta JSON: " . json_encode($response));
            
            echo json_encode($response);
        } catch (Exception $e) {
            error_log("Error en obtenerCertificado: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit; // Terminar la ejecución para evitar salida adicional
    }
    public function guardarCertificado()
    {
        // Asegurarse de que no haya salida antes de la respuesta JSON
        ob_clean(); // Limpiar cualquier salida previa
        
        header('Content-Type: application/json'); // Establecer el tipo de contenido correcto
        
        try {
            // Validar datos de entrada
            if (!isset($_POST['titulo']) || !isset($_POST['contenido'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan datos requeridos'
                ]);
                exit;
            }
            
            $titulo = $_POST['titulo'];
            $contenido = $_POST['contenido'];
            
            // Validar que el contenido no esté vacío
            if (empty(trim(strip_tags($contenido)))) {
                echo json_encode([
                    'success' => false,
                    'message' => 'El contenido no puede estar vacío'
                ]);
                exit;
            }
            
            // Crear o actualizar el certificado
            $certificado = new CertificadoTemplate();
            $certificado->obtenerCertificadoActual(); // Intentar obtener el certificado existente
            $certificado->setTitulo($titulo);
            $certificado->setContenido($contenido);
            
            // Procesar imagen de encabezado si se ha enviado
            if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                $headerImage = $this->procesarImagen($_FILES['header_image']);
                if ($headerImage) {
                    $certificado->setHeaderImage($headerImage);
                }
            }
            
            // Procesar imagen de pie de página si se ha enviado
            if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                $footerImage = $this->procesarImagen($_FILES['footer_image']);
                if ($footerImage) {
                    $certificado->setFooterImage($footerImage);
                }
            }
            
            // Guardar en la base de datos
            if ($certificado->getId()) {
                $resultado = $certificado->actualizarCertificado();
            } else {
                $resultado = $certificado->guardarCertificado();
            }
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Certificado guardado correctamente',
                    'header_image' => $certificado->getHeaderImage(),
                    'footer_image' => $certificado->getFooterImage()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al guardar el certificado'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit; // Terminar la ejecución para evitar salida adicional
    }
    
    public function vistaPrevia()
    {
        // Asegurarse de que no haya salida antes de la respuesta
        ob_clean();
        
        try {
            // Validar datos de entrada
            if (!isset($_POST['titulo']) || !isset($_POST['contenido'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan datos requeridos'
                ]);
                exit;
            }
            
            $titulo = $_POST['titulo'];
            $contenido = $_POST['contenido'];
            
            // Crear un certificado temporal para la vista previa
            $certificado = new CertificadoTemplate();
            $certificado->obtenerCertificadoActual(); // Obtener el certificado actual para las imágenes
            $certificado->setTitulo($titulo);
            $certificado->setContenido($contenido);
            
            // Procesar imagen de encabezado si se ha enviado
            if (isset($_FILES['header_image']) && $_FILES['header_image']['error'] === UPLOAD_ERR_OK) {
                $headerImage = $this->procesarImagen($_FILES['header_image']);
                if ($headerImage) {
                    $certificado->setHeaderImage($headerImage);
                }
            }
            
            // Procesar imagen de pie de página si se ha enviado
            if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] === UPLOAD_ERR_OK) {
                $footerImage = $this->procesarImagen($_FILES['footer_image']);
                if ($footerImage) {
                    $certificado->setFooterImage($footerImage);
                }
            }
            
            // En lugar de guardar en sesión y redireccionar, generamos el PDF directamente
            // y lo devolvemos como una respuesta base64 que se puede mostrar en un iframe
            
            // Verificar si la clase mPDF está disponible
            if (!class_exists('\Mpdf\Mpdf')) {
                // Si no está disponible, intentar incluir el autoloader de Composer
                $autoloaderPaths = [
                    'utils/lib/mpdf/vendor/autoload.php',
                    'vendor/autoload.php',
                    '../vendor/autoload.php',
                    '../../vendor/autoload.php',
                    dirname(__FILE__) . '/../../../vendor/autoload.php'
                ];
                
                $autoloaderLoaded = false;
                foreach ($autoloaderPaths as $path) {
                    if (file_exists($path)) {
                        require_once $path;
                        $autoloaderLoaded = true;
                        break;
                    }
                }
                
                if (!$autoloaderLoaded || !class_exists('\Mpdf\Mpdf')) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'La biblioteca mPDF no está disponible'
                    ]);
                    exit;
                }
            }
            
            // Configurar el PDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8', 
                'format' => 'A4',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0
            ]);
            $mpdf->SetTitle("Vista Previa - Certificado de Garantía");
            $mpdf->SetAutoPageBreak(true, 0);
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 0, 0, 0, 0);
            $mpdf->WriteHTML('<style>.unselectable {-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;}</style>');
    
            // Obtener las URLs de las imágenes
            $headerImageUrl = $certificado->getHeaderImageUrl();
            $footerImageUrl = $certificado->getFooterImageUrl();
    
            // Construir el HTML del certificado
            $html = "
            <div style='width: 100%; padding: 0; margin: 0;'>
                <img src='" . $headerImageUrl . "' style='width: 100%; margin: 0;'> 
            </div>
            
            <h2 style='text-align: center; color: #000; margin-top: 70px; margin-bottom: 10px;'>" . ($certificado->getTitulo() ?: "CERTIFICADO DE GARANTÍA") . "</h2>
            
            <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
            
            // Agregar el contenido del certificado
            $html .= $certificado->getContenido();
            
            // Agregar información de ejemplo para la vista previa
            $html .= "
            <div style='margin-top:20px'>
                <div style='margin-bottom: 5px;'>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>CLIENTE:</strong> CLIENTE DE EJEMPLO</span>
                </div>
                <div style='margin-bottom: 5px;'>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>PRODUCTO:</strong></span>
                </div>
                <div style='margin-bottom: 5px; '>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>MARCA:</strong> MARCA EJEMPLO</span>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span style='padding-left: 50px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>MODELO:</strong> MODELO EJEMPLO</span>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span style='padding-left: 90px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>SERIE:</strong> 123456789</span>
                </div>
                <div>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>FECHA DE INICIO:</strong> " . date('Y-m-d') . "</span>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span style='padding-left: 50px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>FECHA DE CADUCIDAD:</strong> " . date('Y-m-d', strtotime('+1 year')) . "</span>
                </div>
            </div>
            </div>";
            
            // Posicionamiento absoluto para el footer en la parte inferior
            $html .= "
            <div style='position: absolute; bottom: 0; left: 0; width: 100%;'>
                <img src='" . $footerImageUrl . "' style='width: 100%; margin: 0;'>
            </div>
            ";
    
            $mpdf->WriteHTML($html);
            
            // En lugar de mostrar el PDF, lo guardamos en una variable
            $pdfContent = $mpdf->Output('', 'S');
            
            // Convertir el PDF a base64 para enviarlo como respuesta JSON
            $base64Pdf = base64_encode($pdfContent);
            
            // Devolver la respuesta JSON con el PDF en base64
            echo json_encode([
                'success' => true,
                'pdfBase64' => $base64Pdf
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    public function mostrarVistaPrevia()
    {

        try {
            // Depuración
    error_log("Método mostrarVistaPrevia llamado");
    error_log("Estado de la sesión: " . (session_status() === PHP_SESSION_ACTIVE ? "Activa" : "Inactiva"));
    error_log("Datos de la sesión: " . (isset($_SESSION['certificado_preview']) ? "Presentes" : "Ausentes"));
            // Verificar si hay datos de vista previa en la sesión
            if (!isset($_SESSION['certificado_preview'])) {
                echo "No hay datos para la vista previa";
                return;
            }
            
            $previewData = $_SESSION['certificado_preview'];
            
            // Verificar si la clase mPDF está disponible
            if (!class_exists('\Mpdf\Mpdf')) {
                // Si no está disponible, intentar incluir el autoloader de Composer
                $autoloaderPaths = [
                    'utils/lib/mpdf/vendor/autoload.php',
                    'vendor/autoload.php',
                    '../vendor/autoload.php',
                    '../../vendor/autoload.php',
                    dirname(__FILE__) . '/../../../vendor/autoload.php'
                ];
                
                $autoloaderLoaded = false;
                foreach ($autoloaderPaths as $path) {
                    if (file_exists($path)) {
                        require_once $path;
                        $autoloaderLoaded = true;
                        break;
                    }
                }
                
                if (!$autoloaderLoaded || !class_exists('\Mpdf\Mpdf')) {
                    echo "Error: La biblioteca mPDF no está disponible. Por favor, asegúrate de que está instalada correctamente.";
                    echo "<br>Puedes instalarla ejecutando: <code>composer require mpdf/mpdf</code>";
                    return;
                }
            }
            
            // Crear un objeto de certificado temporal con los datos de la vista previa
            $certificadoTemp = new CertificadoTemplate();
            $certificadoTemp->setTitulo($previewData['titulo']);
            $certificadoTemp->setContenido($previewData['contenido']);
            
            // Configurar las imágenes si existen
            if (isset($previewData['header_image'])) {
                $certificadoTemp->setHeaderImage($previewData['header_image']);
            }
            
            if (isset($previewData['footer_image'])) {
                $certificadoTemp->setFooterImage($previewData['footer_image']);
            }
            
            // Configurar el PDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8', 
                'format' => 'A4',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0
            ]);
            $mpdf->SetTitle("Vista Previa - Certificado de Garantía");
            $mpdf->SetAutoPageBreak(true, 0);
            $mpdf->AddPage('P', '', '', '', '', 0, 0, 0, 0, 0, 0);
            $mpdf->WriteHTML('<style>.unselectable {-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;}</style>');

            // Obtener las URLs de las imágenes
            $headerImageUrl = $certificadoTemp->getHeaderImageUrl();
            $footerImageUrl = $certificadoTemp->getFooterImageUrl();

            // Construir el HTML del certificado
            $html = "
            <div style='width: 100%; padding: 0; margin: 0;'>
                <img src='" . $headerImageUrl . "' style='width: 100%; margin: 0;'> 
            </div>
            
            <h2 style='text-align: center; color: #000; margin-top: 70px; margin-bottom: 10px;'>" . ($certificadoTemp->getTitulo() ?: "CERTIFICADO DE GARANTÍA") . "</h2>
            
            <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
            
            // Agregar el contenido del certificado
            $html .= $certificadoTemp->getContenido();
            
            // Agregar información de ejemplo para la vista previa
            $html .= "
            <div style='margin-top:20px'>
                <div style='margin-bottom: 5px;'>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>CLIENTE:</strong> CLIENTE DE EJEMPLO</span>
                </div>
                <div style='margin-bottom: 5px;'>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>PRODUCTO:</strong></span>
                </div>
                <div style='margin-bottom: 5px; '>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>MARCA:</strong> MARCA EJEMPLO</span>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span style='padding-left: 50px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>MODELO:</strong> MODELO EJEMPLO</span>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span style='padding-left: 90px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>SERIE:</strong> 123456789</span>
                </div>
                <div>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>FECHA DE INICIO:</strong> " . date('Y-m-d') . "</span>
                    <span style='-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <span style='padding-left: 50px; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;' unselectable='on' class='unselectable'><strong>FECHA DE CADUCIDAD:</strong> " . date('Y-m-d', strtotime('+1 year')) . "</span>
                </div>
            </div>
            </div>";
            
            // Posicionamiento absoluto para el footer en la parte inferior
            $html .= "
            <div style='position: absolute; bottom: 0; left: 0; width: 100%;'>
                <img src='" . $footerImageUrl . "' style='width: 100%; margin: 0;'>
            </div>
            ";

            $mpdf->WriteHTML($html);
            $mpdf->Output("Vista_Previa_Certificado.pdf", "I");
        } catch (Exception $e) {
            echo "Error al generar la vista previa: " . $e->getMessage();
        }
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