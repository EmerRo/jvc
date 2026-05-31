<?php
// app/http/controllers/ConstanciaPDF.php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/Constancia.php";
require_once "app/models/ConstanciaPlantilla.php";

class ConstanciaPDF extends Controller
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
            'margin_footer' => 0
        ]);
        $this->conexion = (new Conexion())->getConexion();
    }
     public function generarConstanciaPDF($id_constancia)
    {
        $constancia = new Constancia();
        $constancia->obtenerConstancia($id_constancia);

        // Generar número correlativo
        $numeroCorrelativo = $constancia->generarNumeroCorrelativo($constancia->getTipo());
        
        $this->mpdf->SetTitle($constancia->getTitulo() . " " . $numeroCorrelativo);
        
        // Obtener las URLs de las imágenes
        $headerImageUrl = $constancia->getHeaderImage();
        $footerImageUrl = $constancia->getFooterImage();

        // Si no hay imágenes específicas, usar las de la plantilla
        if (!$headerImageUrl || !$footerImageUrl) {
            $template = new ConstanciaPlantilla();
            $template->obtenerTemplateActual();
            
            if (!$headerImageUrl) {
                $headerImageUrl = $template->getHeaderImageUrl();
            }
            
            if (!$footerImageUrl) {
                $footerImageUrl = $template->getFooterImageUrl();
            }
        }

        // Optimizar imágenes antes de usarlas
        $headerImageUrl = $this->optimizarImagenParaPDF($headerImageUrl);
        $footerImageUrl = $this->optimizarImagenParaPDF($footerImageUrl);

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
        
        <!-- Información de la constancia -->
        <div style='text-align: center; margin-bottom: 30px;'>
            <h1 style='color: #000; font-size: 14pt; margin-bottom: 10px; '>" . strtoupper($constancia->getTitulo()) . " " . $numeroCorrelativo . "</h1>
        </div>
        
        <!-- Información de la empresa y cliente -->
        <div style='margin: 0 15mm; margin-bottom: 5px;'>
            <table style='width: 100%; border-collapse: collapse; font-size: 12px;'>
                <tr>
                    <td style='width: 15%; font-weight: bold; padding: 5px 0;'>DE:</td>
                    <td style='width: 85%; padding: 5px 0;'>COMERCIAL & INDUSTRIAL J.V.C. S.A.C.</td>
                </tr>";
        
        // Agregar información del cliente si existe
        if ($constancia->getIdCliente()) {
            // Obtener información del cliente
            $stmt = $this->conexion->prepare("SELECT datos, documento, direccion FROM clientes WHERE id_cliente = ?");
            $clienteId = $constancia->getIdCliente();
            $stmt->bind_param("i", $clienteId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $html .= "
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>A:</td>
                    <td style='padding: 5px 0;'>" . $row['datos'] . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Documento:</td>
                    <td style='padding: 5px 0;'>" . $row['documento'] . "</td>
                </tr>";
                
                if (!empty($row['direccion'])) {
                    $html .= "
                    <tr>
                        <td style='font-weight: bold; padding: 5px 0;'>Dirección:</td>
                        <td style='padding: 5px 0;'>" . $row['direccion'] . "</td>
                    </tr>";
                }
            }
        }
        
        $html .= "
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Asunto:</td>
                    <td style='padding: 5px 0;'>" . $constancia->getTitulo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Tipo:</td>
                    <td style='padding: 5px 0;'>" . $constancia->getTipo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Fecha:</td>
                    <td style='padding: 5px 0;'>" . date('d \d\e F \d\e\l Y', strtotime($constancia->getFechaCreacion())) . "</td>
                </tr>
            </table>
        </div>
        
        <hr style='margin: 0 15mm; border: none; border-top: 1px solid #ccc;'>
        
        <!-- Contenido de la constancia -->
        <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
        
        // Agregar el contenido de la constancia
        $html .= $constancia->getContenido();
        $html .= "</div>";

        // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
        $this->escribirHTMLEnFragmentos($html);
        $this->mpdf->Output("Constancia_" . $numeroCorrelativo . ".pdf", "I");
    }
    

      public function generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image)
    {
        // Para vista previa, generar un número correlativo de ejemplo
        $anio = date('Y');
        $numeroEjemplo = "NRO.XXX-$anio-JVC";
        
        $this->mpdf->SetTitle($titulo . " " . $numeroEjemplo);
        
        // Optimizar imágenes antes de usarlas
        $header_image = $this->optimizarImagenParaPDF($header_image);
        $footer_image = $this->optimizarImagenParaPDF($footer_image);
        
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
        
        $this->mpdf->SetMargins(15, 15, $headerHeight);
        $this->mpdf->SetAutoPageBreak(true, $footerHeight);
        
        // Añadir la página
        $this->mpdf->AddPage();
        
        // Construir el HTML del contenido
        $html = "
        <div style='margin-top: 30px;'></div>
        
        <!-- Información de la constancia -->
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
        
        <!-- Contenido de la constancia -->
        <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
        
        // Agregar el contenido de la constancia
        $html .= $contenido;
        $html .= "</div>";

        // Dividir el HTML en fragmentos más pequeños para evitar pcre.backtrack_limit
        $this->escribirHTMLEnFragmentos($html);
        
        // Devolver el PDF como base64 para la vista previa
        return base64_encode($this->mpdf->Output('', 'S'));
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
    
    // Métodos auxiliares (mismos que en CartaPDF)
    private function optimizacionLigera($imagenUrl, $parts, $imageData)
    {
        $image = imagecreatefromstring($imageData);
        if ($image === false) return $imagenUrl;
        
        $image = $this->aplicarFiltrosCalidad($image);
        
        $isPng = strpos($parts[0], 'png') !== false;
        $isTransparent = $this->tieneTransparencia($image);
        
        ob_start();
        if ($isPng && $isTransparent) {
            imagepng($image, null, 5);
        } else {
            imagejpeg($image, null, 92);
        }
        $optimizedData = ob_get_clean();
        
        imagedestroy($image);
        
        $mimeType = ($isPng && $isTransparent) ? 'image/png' : 'image/jpeg';
        return 'data:' . $mimeType . ';base64,' . base64_encode($optimizedData);
    }
    
    private function aplicarFiltrosCalidad($image)
    {
        $sharpenMatrix = array(array(0, -1, 0), array(-1, 5, -1), array(0, -1, 0));
        imageconvolution($image, $sharpenMatrix, 1, 0);
        imagefilter($image, IMG_FILTER_CONTRAST, -5);
        return $image;
    }
    
    private function calcularDimensionesOptimas($width, $height, $fileSize)
    {
        $maxWidth = 1200;
        $maxHeight = 400;
        $sizeFactor = $fileSize / (1024 * 1024);
        
        if ($sizeFactor > 3) { $maxWidth = 1000; $maxHeight = 350; }
        elseif ($sizeFactor < 1) { $maxWidth = 1400; $maxHeight = 450; }
        
        $needsResize = ($width > $maxWidth || $height > $maxHeight || $sizeFactor > 2);
        
        if (!$needsResize) return ['resize' => false];
        
        $ratioW = $maxWidth / $width;
        $ratioH = $maxHeight / $height;
        $ratio = min($ratioW, $ratioH, 1.0);
        
        return ['resize' => true, 'newWidth' => intval($width * $ratio), 'newHeight' => intval($height * $ratio)];
    }
    
    private function redimensionarConCalidadMaxima($image, $width, $height, $newWidth, $newHeight, $isPng)
    {
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        
        if ($isPng) {
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefill($resizedImage, 0, 0, $transparent);
        } else {
            $white = imagecolorallocate($resizedImage, 255, 255, 255);
            imagefill($resizedImage, 0, 0, $white);
        }
        
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        return $resizedImage;
    }
    
    private function determinarFormatoOptimo($originalMime, $image)
    {
        $isPng = strpos($originalMime, 'png') !== false;
        $hasTransparency = $this->tieneTransparencia($image);
        
        if ($isPng && $hasTransparency) {
            return ['format' => 'png', 'mime' => 'image/png'];
        }
        return ['format' => 'jpeg', 'mime' => 'image/jpeg'];
    }
    
    private function generarImagenOptimizada($image, $formato)
    {
        ob_start();
        if ($formato['format'] === 'png') {
            imagepng($image, null, 3);
        } else {
            imagejpeg($image, null, 95);
        }
        return ob_get_clean();
    }
    
    private function tieneTransparencia($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $samplePoints = min(100, $width * $height);
        
        for ($i = 0; $i < $samplePoints; $i++) {
            $x = rand(0, $width - 1);
            $y = rand(0, $height - 1);
            $color = imagecolorat($image, $x, $y);
            $alpha = ($color & 0x7F000000) >> 24;
            
            if ($alpha > 0) return true;
        }
        return false;
    }

    // Método para generar PDF como base64 (para vista previa)
    public function generarConstanciaPDFBase64($id_constancia)
    {
        try {
            // Evitar que notices/warnings rompan la salida del PDF
            if (function_exists('ini_set')) {
                @ini_set('display_errors', '0');
                @ini_set('html_errors', '0');
                @ini_set('zlib.output_compression', '0');
            }

            $constancia = new Constancia();
            $constancia->obtenerConstancia($id_constancia);

            // Generar número correlativo
            $numeroCorrelativo = $constancia->generarNumeroCorrelativo($constancia->getTipo());
            
            $this->mpdf->SetTitle($constancia->getTitulo() . " " . $numeroCorrelativo);
            
            // Obtener las URLs de las imágenes
            $headerImageUrl = $constancia->getHeaderImageUrl();
            $footerImageUrl = $constancia->getFooterImageUrl();

            // Si no hay imágenes específicas, usar las de la plantilla
            if (!$headerImageUrl || !$footerImageUrl) {
                $template = new ConstanciaPlantilla();
                $template->obtenerTemplateActual();
                
                if (!$headerImageUrl) {
                    $headerImageUrl = $template->getHeaderImageUrl();
                }
                if (!$footerImageUrl) {
                    $footerImageUrl = $template->getFooterImageUrl();
                }
            }

            // Generar el HTML del PDF
            $html = $this->generarHTMLConstancia($constancia, $headerImageUrl, $footerImageUrl, $numeroCorrelativo);
            
            // Escribir HTML en el PDF
            $this->mpdf->WriteHTML($html);
            
            // Generar el PDF como string base64
            $pdfContent = $this->mpdf->Output('', 'S');
            return base64_encode($pdfContent);
            
        } catch (Exception $e) {
            error_log("Error generando PDF base64 de constancia: " . $e->getMessage());
            throw new Exception("Error al generar el PDF: " . $e->getMessage());
        }
    }

    // Método auxiliar para generar HTML de constancia
    private function generarHTMLConstancia($constancia, $headerImageUrl, $footerImageUrl, $numeroCorrelativo)
    {
        $html = $this->generarEncabezado($headerImageUrl);
        
        // Agregar contenido específico de constancia
        $html .= "
        <div style='padding: 20px 15mm; min-height: calc(100vh - 200px);'>
            <table style='width: 100%; border-spacing: 0; font-family: Arial, sans-serif;'>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Constancia N°:</td>
                    <td style='padding: 5px 0;'>" . $numeroCorrelativo . "</td>
                </tr>";
        
        // Agregar información del cliente si existe
        if ($constancia->getIdCliente()) {
            $stmt = $this->conexion->prepare("SELECT datos, documento, direccion FROM clientes WHERE id_cliente = ?");
            $clienteId = $constancia->getIdCliente();
            $stmt->bind_param("i", $clienteId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $html .= "
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>A:</td>
                    <td style='padding: 5px 0;'>" . $row['datos'] . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Documento:</td>
                    <td style='padding: 5px 0;'>" . $row['documento'] . "</td>
                </tr>";
                
                if (!empty($row['direccion'])) {
                    $html .= "
                    <tr>
                        <td style='font-weight: bold; padding: 5px 0;'>Dirección:</td>
                        <td style='padding: 5px 0;'>" . $row['direccion'] . "</td>
                    </tr>";
                }
            }
        }
        
        $html .= "
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Asunto:</td>
                    <td style='padding: 5px 0;'>" . $constancia->getTitulo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Tipo:</td>
                    <td style='padding: 5px 0;'>" . $constancia->getTipo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Fecha:</td>
                    <td style='padding: 5px 0;'>" . date('d \d\e F \d\e\l Y', strtotime($constancia->getFechaCreacion())) . "</td>
                </tr>
            </table>
        </div>
        
        <hr style='margin: 0 15mm; border: none; border-top: 1px solid #ccc;'>
        
        <div style='padding: 20px 15mm; line-height: 1.6;'>
            " . $constancia->getContenido() . "
        </div>";
        
        $html .= $this->generarPie($footerImageUrl);
        
        return $html;
    }
}