<?php
// Incluir el archivo de configuración correcto
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../app/helpers/ImageStorage.php';

// Función para procesar las fotos
function procesarFotos($idCoti, $fotos) {
    $conexion = new Conexion();
    $db = $conexion->getConexion();

    $uploadedFiles = [];

    foreach ($fotos['tmp_name'] as $key => $tmp_name) {
        try {
            $singleFile = [
                'tmp_name' => $tmp_name,
                'name'     => $fotos['name'][$key],
                'error'    => $fotos['error'][$key],
                'size'     => $fotos['size'][$key],
            ];
            $fileName = ImageStorage::save($singleFile, 'cotizaciones-taller');
            $uploadedFiles[] = $fileName;

            // Optimizar imagen después de subirla
            $targetPath = __DIR__ . '/../' . ImageStorage::BASE_PATH . 'cotizaciones-taller/' . $fileName;
            optimizarImagen($targetPath);
        } catch (RuntimeException $e) {
            // Continuar con el siguiente archivo si uno falla
            continue;
        }
    }

    if (!empty($uploadedFiles)) {
        $fotosJson = json_encode($uploadedFiles);
        
        $stmt = $db->prepare("INSERT INTO taller_cotizaciones_fotos (id_cotizacion, nombre_foto) VALUES (?, ?)");
        $stmt->bind_param("is", $idCoti, $fotosJson);
        $stmt->execute();
        $stmt->close();
    }

    $conexion->closeConexion();
    return true;
}

function optimizarImagen($rutaImagen) {
    // La función de optimización se mantiene igual
    $info = getimagesize($rutaImagen);
    if ($info === false) return false;

    switch ($info[2]) {
        case IMAGETYPE_JPEG:
            $imagen = imagecreatefromjpeg($rutaImagen);
            break;
        case IMAGETYPE_PNG:
            $imagen = imagecreatefrompng($rutaImagen);
            break;
        default:
            return false;
    }

    $maxWidth = 800;
    $maxHeight = 600;
    
    $width = imagesx($imagen);
    $height = imagesy($imagen);
    
    if ($width > $maxWidth || $height > $maxHeight) {
        $ratio = min($maxWidth/$width, $maxHeight/$height);
        $new_width = $width * $ratio;
        $new_height = $height * $ratio;
        
        $nueva_imagen = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($nueva_imagen, $imagen, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        imagejpeg($nueva_imagen, $rutaImagen, 75);
        imagedestroy($nueva_imagen);
    }
    
    imagedestroy($imagen);
    return true;
}

// Verificar si se llama desde línea de comandos
if (php_sapi_name() === 'cli') {
    if ($argc < 3) {
        die("Uso: php procesar_fotos.php [id_cotizacion] [ruta_archivo_fotos]\n");
    }
    
    $idCoti = $argv[1];
    $fotosTemp = json_decode(file_get_contents($argv[2]), true);
    procesarFotos($idCoti, $fotosTemp);
}