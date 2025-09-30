<?php
require_once "../app/helpers/ThumbnailHelper.php";
require_once "../app/config/Conexion.php";

set_time_limit(0);
ini_set('memory_limit', '512M');

echo "Iniciando generación de thumbnails...\n";

$conexion = (new Conexion())->getConexion();
$sql = "SELECT DISTINCT imagen FROM productos WHERE imagen IS NOT NULL AND imagen != '' AND id_empresa = 12";
$result = $conexion->query($sql);

$total = $result->num_rows;
$procesados = 0;
$exitosos = 0;
$errores = 0;

echo "Total de imágenes encontradas: $total\n\n";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = $row['imagen'];
        $procesados++;

        echo "[$procesados/$total] Procesando: $imagePath\n";

        if (file_exists($imagePath)) {
            $thumbnailPath = ThumbnailHelper::getThumbnailPath($imagePath);

            if (!file_exists($thumbnailPath)) {
                $fullThumbnailPath = $thumbnailPath;

                if (ThumbnailHelper::generateThumbnail($imagePath, $fullThumbnailPath)) {
                    $exitosos++;
                    $originalSize = filesize($imagePath);
                    $thumbnailSize = filesize($fullThumbnailPath);
                    $reduction = round((($originalSize - $thumbnailSize) / $originalSize) * 100, 2);
                    echo "  ✓ Thumbnail creado exitosamente (reducción: {$reduction}%)\n";
                } else {
                    $errores++;
                    echo "  ✗ Error al crear thumbnail\n";
                }
            } else {
                echo "  - Thumbnail ya existe\n";
            }
        } else {
            $errores++;
            echo "  ✗ Archivo no encontrado\n";
        }

        echo "\n";
    }
}

echo "=== RESUMEN ===\n";
echo "Total procesadas: $procesados\n";
echo "Thumbnails creados: $exitosos\n";
echo "Errores: $errores\n";

if ($exitosos > 0) {
    echo "\nThumbnails generados en: public/img/productos/thumbnails/\n";
    echo "El sistema ahora cargará imágenes optimizadas de ~2-3KB en lugar de ~200KB\n";
}

echo "\nProceso completado.\n";
?>