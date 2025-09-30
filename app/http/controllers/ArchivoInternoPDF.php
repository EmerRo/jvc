<?php
// app/http/controllers/ArchivoInternoPDF.php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/vendor/autoload.php';
require_once "app/models/ArchivoInterno.php";
require_once "app/models/ArchivoInternoPlantilla.php";

class ArchivoInternoPDF extends Controller
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
    
   public function generarArchivoInternoPDF($id_carta)
    {
        $carta = new ArchivoInterno();
        $carta->obtenerArchivoInterno($id_carta);

        // Generar número correlativo
        $numeroCorrelativo = $carta->generarNumeroCorrelativo($carta->getTipo());
        
        $this->mpdf->SetTitle($carta->getTitulo() . " " . $numeroCorrelativo);
        
        // Obtener las URLs de las imágenes
        $headerImageUrl = $carta->getHeaderImage();
        $footerImageUrl = $carta->getFooterImage();
        
        // Optimizar imágenes para evitar errores de memoria
        $headerImageUrl = $this->optimizarImagenParaPDF($headerImageUrl);
        $footerImageUrl = $this->optimizarImagenParaPDF($footerImageUrl);

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
        
        <!-- Información de la carta -->
        <div style='text-align: center; margin-bottom: 30px;'>
            <h1 style='color: #000; font-size: 14pt; margin-bottom: 10px; '>" . strtoupper($carta->getTitulo()) . " " . $numeroCorrelativo . "</h1>
        </div>
        
        <!-- Información de la empresa y cliente -->
        <div style='margin: 0 15mm; margin-bottom: 5px;'>
            <table style='width: 100%; border-collapse: collapse; font-size: 12px;'>
                <tr>
                    <td style='width: 15%; font-weight: bold; padding: 5px 0;'>DE:</td>
                    <td style='width: 85%; padding: 5px 0;'>COMERCIAL & INDUSTRIAL J.V.C. S.A.C.</td>
                </tr>";
        
        // Agregar información del cliente si existe
        if ($carta->getIdCliente()) {
            // Obtener información del cliente
            $stmt = $this->conexion->prepare("SELECT datos, documento, direccion FROM clientes WHERE id_cliente = ?");
            $clienteId = $carta->getIdCliente();
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
                    <td style='padding: 5px 0;'>" . $carta->getTitulo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Tipo:</td>
                    <td style='padding: 5px 0;'>" . $carta->getTipo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Fecha:</td>
                    <td style='padding: 5px 0;'>" . date('d \d\e F \d\e\l Y', strtotime($carta->getFechaCreacion())) . "</td>
                </tr>
            </table>
        </div>
        
        <hr style='margin: 0 15mm; border: none; border-top: 1px solid #ccc;'>
        
        <!-- Contenido de la carta -->
        <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
        
        // Agregar el contenido de la carta
        $html .= $carta->getContenido();
        $html .= "</div>";

        $this->escribirHTMLEnFragmentos($html);
        $this->mpdf->Output("ArchivoInterno_" . $numeroCorrelativo . ".pdf", "I");
    }
    
  public function generarVistaPreviaPDF($titulo, $contenido, $header_image, $footer_image)
    {
        // Para vista previa, generar un número correlativo de ejemplo
        $anio = date('Y');
        $numeroEjemplo = "NRO.XXX-$anio-JVC";
        
        $this->mpdf->SetTitle($titulo . " " . $numeroEjemplo);
        
        // Optimizar imágenes para evitar errores de memoria
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
        
        <!-- Información de la carta -->
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
        
        <!-- Contenido de la carta -->
        <div style='font-size: 12px; text-align: justify; padding: 10px; margin: 0 15mm;'>";
        
        // Agregar el contenido de la carta
        $html .= $contenido;
        $html .= "</div>";

        $this->escribirHTMLEnFragmentos($html);
        
        // Devolver el PDF como base64 para la vista previa
        return base64_encode($this->mpdf->Output('', 'S'));
    }
    
    /**
     * Escribe HTML en fragmentos para evitar errores de pcre.backtrack_limit
     */
    private function escribirHTMLEnFragmentos($html)
    {
        $fragmentos = $this->dividirHTMLEnFragmentos($html, 8000); // Tamaño más conservador
        
        foreach ($fragmentos as $fragmento) {
            if (trim($fragmento)) {
                $this->mpdf->WriteHTML($fragmento);
            }
        }
    }
    
    /**
     * Divide el HTML en fragmentos manejables
     */
    private function dividirHTMLEnFragmentos($html, $tamanoMaximo = 10000)
    {
        // Si el HTML es pequeño, devolverlo tal como está
        if (strlen($html) <= $tamanoMaximo) {
            return [$html];
        }
        
        $fragmentos = [];
        $tagsAbiertos = [];
        $posicionActual = 0;
        $longitudHtml = strlen($html);
        
        while ($posicionActual < $longitudHtml) {
            $finFragmento = min($posicionActual + $tamanoMaximo, $longitudHtml);
            
            // Buscar el final de una etiqueta para evitar cortarla
            if ($finFragmento < $longitudHtml) {
                $ultimoTag = $this->encontrarUltimoTagCompleto($html, $posicionActual, $finFragmento);
                if ($ultimoTag !== false) {
                    $finFragmento = $ultimoTag;
                }
            }
            
            $fragmentoActual = substr($html, $posicionActual, $finFragmento - $posicionActual);
            
            // Encontrar tags en este fragmento
            $tagsEnFragmento = $this->extraerTags($fragmentoActual);
            
            // Abrir tags necesarios al inicio
            $htmlFragmento = $this->abrirTagsNecesarios($tagsAbiertos) . $fragmentoActual;
            
            // Actualizar lista de tags abiertos
            foreach ($tagsEnFragmento['abiertos'] as $tag) {
                $tagsAbiertos[] = $tag;
            }
            
            foreach ($tagsEnFragmento['cerrados'] as $tag) {
                $tagsAbiertos = $this->removerTagDeLista($tagsAbiertos, $tag);
            }
            
            // Cerrar tags al final del fragmento
            $htmlFragmento .= $this->cerrarTagsTemporalmente(array_reverse($tagsAbiertos));
            
            $fragmentos[] = $htmlFragmento;
            $posicionActual = $finFragmento;
        }
        
        return $fragmentos;
    }
    
    /**
     * Encuentra el último tag completo antes del límite
     */
    private function encontrarUltimoTagCompleto($html, $inicio, $fin)
    {
        $ultimaPosicion = false;
        $posicionActual = $inicio;
        
        while (($pos = strpos($html, '>', $posicionActual)) !== false && $pos < $fin) {
            $ultimaPosicion = $pos + 1;
            $posicionActual = $pos + 1;
        }
        
        return $ultimaPosicion;
    }
    
    /**
     * Extrae tags abiertos y cerrados de un fragmento HTML
     */
    private function extraerTags($html)
    {
        $tagsAbiertos = [];
        $tagsCerrados = [];
        
        // Usar una expresión regular más simple para evitar problemas de backtrack
        if (preg_match_all('/<\/?([a-zA-Z][a-zA-Z0-9]*)[^>]*>/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $index => $match) {
                $tagCompleto = $match[0];
                $nombreTag = strtolower($matches[1][$index][0]);
                
                if (strpos($tagCompleto, '</') === 0) {
                    // Tag de cierre
                    $tagsCerrados[] = $nombreTag;
                } elseif (substr($tagCompleto, -2) !== '/>' && !in_array($nombreTag, ['img', 'br', 'hr', 'input', 'meta', 'link'])) {
                    // Tag de apertura (no auto-cerrado)
                    $tagsAbiertos[] = $nombreTag;
                }
            }
        }
        
        return ['abiertos' => $tagsAbiertos, 'cerrados' => $tagsCerrados];
    }
    
    /**
     * Genera HTML para abrir tags necesarios
     */
    private function abrirTagsNecesarios($tags)
    {
        $html = '';
        foreach ($tags as $tag) {
            $html .= "<$tag>";
        }
        return $html;
    }
    
    /**
     * Genera HTML para cerrar tags temporalmente
     */
    private function cerrarTagsTemporalmente($tags)
    {
        $html = '';
        foreach ($tags as $tag) {
            $html .= "</$tag>";
        }
        return $html;
    }
    
    /**
     * Remueve un tag específico de la lista de tags abiertos
     */
    private function removerTagDeLista($lista, $tagARemover)
    {
        $nuevaLista = [];
        $removido = false;
        
        // Remover de atrás hacia adelante (LIFO)
        for ($i = count($lista) - 1; $i >= 0; $i--) {
            if (!$removido && $lista[$i] === $tagARemover) {
                $removido = true;
                continue;
            }
            array_unshift($nuevaLista, $lista[$i]);
        }
        
        return $nuevaLista;
    }
    
    /**
     * Optimiza una imagen para PDF reduciendo su tamaño si es necesario
     */
    private function optimizarImagenParaPDF($rutaImagen)
    {
        // Si no hay imagen o es una URL, devolverla tal como está
        if (empty($rutaImagen) || filter_var($rutaImagen, FILTER_VALIDATE_URL)) {
            return $rutaImagen;
        }
        
        // Construir ruta absoluta si es necesaria
        $rutaAbsoluta = $rutaImagen;
        if (!file_exists($rutaAbsoluta)) {
            // Si la ruta no existe, probar con ruta relativa desde el directorio raíz del proyecto
            $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . '/jvc/' . ltrim($rutaImagen, '/');
        }
        
        // Si el archivo no existe, devolver la ruta original
        if (!file_exists($rutaAbsoluta)) {
            return $rutaImagen;
        }
        
        try {
            // Obtener información de la imagen
            $infoImagen = getimagesize($rutaAbsoluta);
            if ($infoImagen === false) {
                return $rutaImagen;
            }
            
            $ancho = $infoImagen[0];
            $alto = $infoImagen[1];
            $tipo = $infoImagen[2];
            
            // Verificar si la imagen necesita optimización
            // (más de 1200px de ancho o más de 2MB)
            $tamanoArchivo = filesize($rutaAbsoluta);
            $necesitaOptimizacion = $ancho > 1200 || $tamanoArchivo > 2097152; // 2MB
            
            if (!$necesitaOptimizacion) {
                return $rutaImagen;
            }
            
            // Crear imagen desde el archivo original
            $imagenOriginal = null;
            switch ($tipo) {
                case IMAGETYPE_JPEG:
                    $imagenOriginal = imagecreatefromjpeg($rutaAbsoluta);
                    break;
                case IMAGETYPE_PNG:
                    $imagenOriginal = imagecreatefrompng($rutaAbsoluta);
                    break;
                case IMAGETYPE_GIF:
                    $imagenOriginal = imagecreatefromgif($rutaAbsoluta);
                    break;
                default:
                    return $rutaImagen; // Tipo no soportado
            }
            
            if ($imagenOriginal === false) {
                return $rutaImagen;
            }
            
            // Calcular nuevas dimensiones (máximo 1200px de ancho)
            $nuevoAncho = min(1200, $ancho);
            $nuevoAlto = intval(($alto * $nuevoAncho) / $ancho);
            
            // Crear nueva imagen redimensionada
            $imagenOptimizada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
            
            // Preservar transparencia para PNG
            if ($tipo == IMAGETYPE_PNG) {
                imagealphablending($imagenOptimizada, false);
                imagesavealpha($imagenOptimizada, true);
                $transparente = imagecolorallocatealpha($imagenOptimizada, 255, 255, 255, 127);
                imagefill($imagenOptimizada, 0, 0, $transparente);
            }
            
            // Redimensionar imagen
            imagecopyresampled(
                $imagenOptimizada, $imagenOriginal,
                0, 0, 0, 0,
                $nuevoAncho, $nuevoAlto, $ancho, $alto
            );
            
            // Generar nombre para archivo optimizado
            $infoRuta = pathinfo($rutaAbsoluta);
            $rutaOptimizada = $infoRuta['dirname'] . '/' . $infoRuta['filename'] . '_optimized.' . $infoRuta['extension'];
            
            // Guardar imagen optimizada
            $guardadoExitoso = false;
            switch ($tipo) {
                case IMAGETYPE_JPEG:
                    $guardadoExitoso = imagejpeg($imagenOptimizada, $rutaOptimizada, 85);
                    break;
                case IMAGETYPE_PNG:
                    $guardadoExitoso = imagepng($imagenOptimizada, $rutaOptimizada, 6);
                    break;
                case IMAGETYPE_GIF:
                    $guardadoExitoso = imagegif($imagenOptimizada, $rutaOptimizada);
                    break;
            }
            
            // Liberar memoria
            imagedestroy($imagenOriginal);
            imagedestroy($imagenOptimizada);
            
            if ($guardadoExitoso) {
                // Devolver ruta relativa de la imagen optimizada
                return str_replace($_SERVER['DOCUMENT_ROOT'] . '/jvc/', '', $rutaOptimizada);
            }
            
        } catch (Exception $e) {
            // En caso de error, devolver imagen original
            error_log("Error optimizando imagen: " . $e->getMessage());
        }
        
        return $rutaImagen;
    }

    // Método para generar PDF como base64 (para vista previa)
    public function generarArchivoInternoPDFBase64($id_archivo)
    {
        try {
            // Evitar que notices/warnings rompan la salida del PDF
            if (function_exists('ini_set')) {
                @ini_set('display_errors', '0');
                @ini_set('html_errors', '0');
                @ini_set('zlib.output_compression', '0');
            }

            $archivo = new ArchivoInterno();
            $archivo->obtenerArchivo($id_archivo);

            // Generar número correlativo
            $numeroCorrelativo = $archivo->generarNumeroCorrelativo($archivo->getTipo());
            
            $this->mpdf->SetTitle($archivo->getTitulo() . " " . $numeroCorrelativo);
            
            // Obtener las URLs de las imágenes
            $headerImageUrl = $archivo->getHeaderImageUrl();
            $footerImageUrl = $archivo->getFooterImageUrl();

            // Si no hay imágenes específicas, usar las de la plantilla
            if (!$headerImageUrl || !$footerImageUrl) {
                $template = new ArchivoInternoTemplate();
                $template->obtenerTemplateActual();
                
                if (!$headerImageUrl) {
                    $headerImageUrl = $template->getHeaderImageUrl();
                }
                if (!$footerImageUrl) {
                    $footerImageUrl = $template->getFooterImageUrl();
                }
            }

            // Generar el HTML del PDF
            $html = $this->generarHTMLArchivoInterno($archivo, $headerImageUrl, $footerImageUrl, $numeroCorrelativo);
            
            // Escribir HTML en el PDF
            $this->mpdf->WriteHTML($html);
            
            // Generar el PDF como string base64
            $pdfContent = $this->mpdf->Output('', 'S');
            return base64_encode($pdfContent);
            
        } catch (Exception $e) {
            error_log("Error generando PDF base64 de archivo interno: " . $e->getMessage());
            throw new Exception("Error al generar el PDF: " . $e->getMessage());
        }
    }

    // Método auxiliar para generar HTML de archivo interno
    private function generarHTMLArchivoInterno($archivo, $headerImageUrl, $footerImageUrl, $numeroCorrelativo)
    {
        $html = $this->generarEncabezado($headerImageUrl);
        
        // Agregar contenido específico de archivo interno
        $html .= "
        <div style='padding: 20px 15mm; min-height: calc(100vh - 200px);'>
            <table style='width: 100%; border-spacing: 0; font-family: Arial, sans-serif;'>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Archivo N°:</td>
                    <td style='padding: 5px 0;'>" . $numeroCorrelativo . "</td>
                </tr>";
        
        // Agregar información del cliente si existe
        if ($archivo->getIdCliente()) {
            $stmt = $this->conexion->prepare("SELECT datos, documento, direccion FROM clientes WHERE id_cliente = ?");
            $clienteId = $archivo->getIdCliente();
            $stmt->bind_param("i", $clienteId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $html .= "
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Cliente:</td>
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
                    <td style='padding: 5px 0;'>" . $archivo->getTitulo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Tipo:</td>
                    <td style='padding: 5px 0;'>" . $archivo->getTipo() . "</td>
                </tr>
                <tr>
                    <td style='font-weight: bold; padding: 5px 0;'>Fecha:</td>
                    <td style='padding: 5px 0;'>" . date('d \d\e F \d\e\l Y', strtotime($archivo->getFechaCreacion())) . "</td>
                </tr>
            </table>
        </div>
        
        <hr style='margin: 0 15mm; border: none; border-top: 1px solid #ccc;'>
        
        <div style='padding: 20px 15mm; line-height: 1.6;'>
            " . $archivo->getContenido() . "
        </div>";
        
        $html .= $this->generarPie($footerImageUrl);
        
        return $html;
    }
}