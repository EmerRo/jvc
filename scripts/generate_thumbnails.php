<?php
require_once "../app/helpers/ThumbnailHelper.php";
require_once "../app/config/Conexion.php";

set_time_limit(0);
ini_set('memory_limit', '512M');

$conexion = (new Conexion())->getConexion();
$sql = "SELECT DISTINCT imagen FROM productos WHERE imagen IS NOT NULL AND imagen != '' AND id_empresa = 12";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = $row['imagen'];

        if (file_exists($imagePath)) {
            $thumbnailPath = ThumbnailHelper::getThumbnailPath($imagePath);

            if (!file_exists($thumbnailPath)) {
                ThumbnailHelper::generateThumbnail($imagePath, $thumbnailPath);
            }
        }
    }
}
?>