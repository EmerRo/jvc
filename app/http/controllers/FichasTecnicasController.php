<?php

require_once "app/models/GestionArchivo.php";
require_once "app/models/GestionAdjunto.php";
require_once "app/models/Producto.php";

class FichasTecnicasController extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }



    public function listarFichas()
    {
        // Inicializar respuesta con res=true por defecto
        $respuesta = ["res" => true, "fichas" => []];

        try {
            $termino = isset($_POST['termino']) ? $_POST['termino'] : null;
            $id_producto = isset($_POST['id_producto']) ? $_POST['id_producto'] : null;

            $archivo = new GestionArchivo();
            $id_empresa = isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12;
            $sucursal = isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : 1;

            $archivo->setIdEmpresa($id_empresa);
            $archivo->setSucursal($sucursal);

            // Construir la consulta base
            $sql = "SELECT a.*, 
                    (SELECT ga.nombre_adjunto FROM gestion_adjuntos ga WHERE ga.id_archivo = a.id_archivo AND ga.es_principal = '1' LIMIT 1) as adjunto_principal,
                    (SELECT ga.tipo_adjunto FROM gestion_adjuntos ga WHERE ga.id_archivo = a.id_archivo AND ga.es_principal = '1' LIMIT 1) as tipo_adjunto,
                    (SELECT ga.ruta_adjunto FROM gestion_adjuntos ga WHERE ga.id_archivo = a.id_archivo AND ga.es_principal = '1' LIMIT 1) as ruta_adjunto,
                    (SELECT p.nombre FROM productos p WHERE p.id_producto = a.id_producto) as nombre_producto
                    FROM gestion_archivos a 
                    WHERE a.id_empresa = '$id_empresa' 
                    AND a.sucursal = '$sucursal' 
                    AND a.estado = '1' 
                    AND a.tipo = 'ficha_tecnica'";

            // Agregar filtros adicionales si existen
            if ($termino) {
                $sql .= " AND (a.titulo LIKE '%$termino%' OR EXISTS (SELECT 1 FROM productos p WHERE p.id_producto = a.id_producto AND p.nombre LIKE '%$termino%'))";
            }

            if ($id_producto) {
                $sql .= " AND a.id_producto = '$id_producto'";
            }

            // Ordenar por fecha de actualización
            $sql .= " ORDER BY a.fecha_actualizacion DESC";

            $resultado = $this->conexion->query($sql);

            if ($resultado) {
                // IMPORTANTE: Siempre mantener res=true, incluso si no hay resultados
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        $respuesta["fichas"][] = $row;
                    }
                }
                // No es necesario volver a establecer res=true aquí, ya está establecido arriba
            } else {
                // Si la consulta falla, establecer res=false
                $respuesta["res"] = false;
                $respuesta["error"] = "Error en la consulta: " . $this->conexion->error;
            }
        } catch (Exception $e) {
            // En caso de excepción, establecer res=false
            $respuesta["res"] = false;
            $respuesta["error"] = $e->getMessage();
        }

        // IMPORTANTE: Establecer los headers correctos para JSON
        header('Content-Type: application/json');

        // Devolver la respuesta JSON
        return json_encode($respuesta);
    }

    public function guardarFicha()
    {
        $respuesta = ["res" => false];

        try {
            $this->conexion->begin_transaction();

            // Datos de la ficha técnica
            $titulo = $_POST['titulo'];
            $id_producto = isset($_POST['id_producto']) && !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;

            // Crear el archivo
            $archivo = new GestionArchivo();
            $archivo->setTitulo($titulo);
            $archivo->setTipo('ficha_tecnica');
            $archivo->setIdProducto($id_producto);
            $archivo->setVersion('1.0');
            $archivo->setEstado('1');
            $archivo->setIdEmpresa(isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12);
            $archivo->setSucursal(isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : 1);

            $id_archivo = $archivo->insertar();

            if (!$id_archivo) {
                throw new Exception("Error al guardar la ficha técnica");
            }

            // NUEVO: Procesar archivos adjuntos y capturar información de compresión
            $infoCompresion = $this->procesarAdjuntos($id_archivo);

            $this->conexion->commit();
            $respuesta["res"] = true;
            $respuesta["id_archivo"] = $id_archivo;
            // NUEVO: Incluir información de compresión en la respuesta
            if (!empty($infoCompresion)) {
                $respuesta["compresion_info"] = $infoCompresion;
            }
        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function obtenerFicha()
    {
        $respuesta = ["res" => false];

        try {
            $id_archivo = $_POST['id_archivo'];

            // Obtener datos de la ficha
            $sql = "SELECT a.*, 
                    (SELECT p.nombre FROM productos p WHERE p.id_producto = a.id_producto) as nombre_producto 
                    FROM gestion_archivos a 
                    WHERE a.id_archivo = '$id_archivo' AND a.tipo = 'ficha_tecnica'";

            $ficha = $this->conexion->query($sql)->fetch_assoc();

            if (!$ficha) {
                throw new Exception("Ficha técnica no encontrada");
            }

            // Obtener adjuntos de la ficha
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_archivo);
            $adjuntos = $adjuntoObj->listarPorArchivo();

            $listaAdjuntos = [];
            if ($adjuntos && $adjuntos->num_rows > 0) {
                while ($row = $adjuntos->fetch_assoc()) {
                    $listaAdjuntos[] = $row;
                }
            }

            $respuesta["res"] = true;
            $respuesta["ficha"] = $ficha;
            $respuesta["adjuntos"] = $listaAdjuntos;

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function eliminarFicha()
    {
        $respuesta = ["res" => false];

        try {
            $id_archivo = $_POST['id_archivo'];

            $archivo = new GestionArchivo();
            $archivo->setIdArchivo($id_archivo);

            if ($archivo->eliminar()) {
                $respuesta["res"] = true;
            } else {
                throw new Exception("Error al eliminar la ficha técnica");
            }

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function compartirWhatsApp()
    {
        $respuesta = ["res" => false];

        try {
            $id_archivo = $_POST['id_archivo'];
            $telefono = $_POST['telefono'];

            // Validar el número de teléfono
            if (!preg_match('/^[0-9]{9}$/', $telefono)) {
                throw new Exception("Número de teléfono inválido");
            }

            // Obtener la ficha técnica
            $archivo = new GestionArchivo();
            $archivo->setIdArchivo($id_archivo);
            if (!$archivo->obtenerDatos()) {
                throw new Exception("Ficha técnica no encontrada");
            }

            // Obtener el PDF principal
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_archivo);
            $adjuntoObj->setTipoAdjunto('pdf');
            $adjuntos = $adjuntoObj->listarPorTipo();

            if ($adjuntos && $adjuntos->num_rows > 0) {
                $adjunto = $adjuntos->fetch_assoc();
                $rutaArchivo = $adjunto['ruta_adjunto'];

                // URL completa del archivo
                $urlBase = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                $urlArchivo = $urlBase . '/' . $rutaArchivo;

                // Mensaje para WhatsApp
                $mensaje = "Ficha Técnica: " . $archivo->getTitulo() . "\n\n";
                $mensaje .= "Descargar PDF: " . $urlArchivo;

                // Codificar el mensaje para URL
                $mensajeCodificado = urlencode($mensaje);

                // URL para WhatsApp API
                $whatsappUrl = "https://api.whatsapp.com/send?phone=51$telefono&text=$mensajeCodificado";

                $respuesta["res"] = true;
                $respuesta["whatsapp_url"] = $whatsappUrl;
            } else {
                throw new Exception("No se encontró el PDF de la ficha técnica");
            }

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    private function procesarAdjuntos($id_archivo)
    {
        $infoCompresion = []; // NUEVO: Array para almacenar información de compresión

        // Procesar archivo PDF - AGREGAR VALIDACIÓN DE 4MB
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == UPLOAD_ERR_OK) {
            // NUEVO: Validar tamaño de PDF (4MB máximo)
            if ($_FILES['pdf']['size'] > 4 * 1024 * 1024) {
                throw new Exception("El archivo PDF excede el tamaño máximo de 4MB");
            }
            $this->guardarAdjunto($id_archivo, $_FILES['pdf'], 'pdf', true);
        }

        // Procesar archivo editable - MODIFICAR PARA INCLUIR EXCEL Y VALIDAR 4MB
        if (isset($_FILES['editable']) && $_FILES['editable']['error'] == UPLOAD_ERR_OK) {
            // NUEVO: Validar extensiones permitidas (agregar Excel)
            $extension = strtolower(pathinfo($_FILES['editable']['name'], PATHINFO_EXTENSION));
            $extensionesPermitidas = ['xlsx', 'xls', 'cdr', 'psd', 'ai'];

            if (!in_array($extension, $extensionesPermitidas)) {
                throw new Exception("Tipo de archivo no permitido. Solo se permiten: Excel (.xlsx, .xls), Corel (.cdr), Photoshop (.psd), Illustrator (.ai)");
            }

            // NUEVO: Validar tamaño de archivo editable (4MB máximo)
            if ($_FILES['editable']['size'] > 4 * 1024 * 1024) {
                throw new Exception("El archivo editable excede el tamaño máximo de 4MB");
            }

            $this->guardarAdjunto($id_archivo, $_FILES['editable'], 'editable', false);
        }

        // Procesar imágenes - MODIFICAR PARA COMPRIMIR Y VALIDAR 2MB Y MÁXIMO 3 IMÁGENES
        if (isset($_FILES['imagenes'])) {
            $total = count($_FILES['imagenes']['name']);

            // NUEVO: Validar máximo 3 imágenes
            if ($total > 3) {
                throw new Exception("Solo se pueden subir máximo 3 imágenes");
            }

            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['imagenes']['error'][$i] == UPLOAD_ERR_OK) {
                    $archivo = [
                        'name' => $_FILES['imagenes']['name'][$i],
                        'type' => $_FILES['imagenes']['type'][$i],
                        'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
                        'error' => $_FILES['imagenes']['error'][$i],
                        'size' => $_FILES['imagenes']['size'][$i]
                    ];

                    // NUEVO: Comprimir imagen antes de guardar
                    $archivoComprimido = $this->comprimirImagen($archivo);

                    // NUEVO: Validar que después de comprimir no exceda 2MB
                    if ($archivoComprimido['size'] > 2 * 1024 * 1024) {
                        throw new Exception("La imagen {$archivo['name']} es demasiado grande incluso después de comprimir");
                    }

                    // NUEVO: Guardar información de compresión
                    if ($archivoComprimido['compressed']) {
                        $infoCompresion[] = [
                            'nombre' => $archivo['name'],
                            'tamaño_original' => $archivoComprimido['original_size'],
                            'tamaño_final' => $archivoComprimido['size'],
                            'comprimida' => true
                        ];
                    }

                    $this->guardarAdjunto($id_archivo, $archivoComprimido, 'imagen', false);
                }
            }
        }

        // Guardar link de YouTube si existe
        if (isset($_POST['youtube_link']) && !empty($_POST['youtube_link'])) {
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_archivo);
            $adjuntoObj->setNombreAdjunto($_POST['youtube_link']);
            $adjuntoObj->setTipoAdjunto('youtube');
            $adjuntoObj->setRutaAdjunto($_POST['youtube_link']);
            $adjuntoObj->setEsPrincipal('0');
            $adjuntoObj->insertar();
        }

        // NUEVO: Retornar información de compresión
        return $infoCompresion;
    }

    private function guardarAdjunto($id_archivo, $archivo, $tipo, $esPrincipal)
    {
        $nombreOriginal = $archivo['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;

        // NUEVO: Para imágenes comprimidas, usar la extensión correcta
        if ($tipo === 'imagen' && isset($archivo['compressed']) && $archivo['compressed']) {
            $extension = 'jpg'; // Las imágenes comprimidas siempre son JPG
            $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        }

        // Definir la carpeta según el tipo de archivo
        $carpeta = 'files/gestion_archivos/' . $tipo . '/';

        // Crear la carpeta si no existe
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $rutaCompleta = $carpeta . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_archivo);
            $adjuntoObj->setNombreAdjunto($nombreOriginal);
            $adjuntoObj->setTipoAdjunto($tipo);
            $adjuntoObj->setRutaAdjunto($rutaCompleta);
            $adjuntoObj->setEsPrincipal($esPrincipal ? '1' : '0');

            $resultado = $adjuntoObj->insertar();

            // NUEVO: Si es una imagen comprimida, limpiar archivo temporal
            if (isset($archivo['compressed']) && $archivo['compressed'] && file_exists($archivo['tmp_name'])) {
                unlink($archivo['tmp_name']);
            }

            return $resultado;

        }

        return false;
    }
    private function comprimirImagen($archivo)
    {
        // Verificar que sea una imagen
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            throw new Exception("Tipo de imagen no permitido");
        }

        // Crear imagen desde el archivo temporal
        switch ($archivo['type']) {
            case 'image/jpeg':
            case 'image/jpg':
                $imagenOriginal = imagecreatefromjpeg($archivo['tmp_name']);
                break;
            case 'image/png':
                $imagenOriginal = imagecreatefrompng($archivo['tmp_name']);
                break;
            case 'image/gif':
                $imagenOriginal = imagecreatefromgif($archivo['tmp_name']);
                break;
            default:
                throw new Exception("Formato de imagen no soportado");
        }

        if (!$imagenOriginal) {
            throw new Exception("No se pudo procesar la imagen");
        }

        // Obtener dimensiones originales
        $anchoOriginal = imagesx($imagenOriginal);
        $altoOriginal = imagesy($imagenOriginal);

        // Definir dimensiones máximas
        $maxAncho = 1920;
        $maxAlto = 1080;

        // Calcular nuevas dimensiones manteniendo proporción
        $ratio = min($maxAncho / $anchoOriginal, $maxAlto / $altoOriginal);

        // Si la imagen ya es más pequeña, no redimensionar
        if ($ratio >= 1) {
            $nuevoAncho = $anchoOriginal;
            $nuevoAlto = $altoOriginal;
        } else {
            $nuevoAncho = round($anchoOriginal * $ratio);
            $nuevoAlto = round($altoOriginal * $ratio);
        }

        // Crear nueva imagen redimensionada
        $imagenNueva = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        // Preservar transparencia para PNG
        if ($archivo['type'] == 'image/png') {
            imagealphablending($imagenNueva, false);
            imagesavealpha($imagenNueva, true);
            $transparente = imagecolorallocatealpha($imagenNueva, 255, 255, 255, 127);
            imagefill($imagenNueva, 0, 0, $transparente);
        }

        // Redimensionar imagen
        imagecopyresampled($imagenNueva, $imagenOriginal, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

        // Crear archivo temporal para la imagen comprimida
        $archivoTemporal = tempnam(sys_get_temp_dir(), 'img_compressed_');

        // Guardar imagen comprimida (siempre como JPEG para mejor compresión)
        imagejpeg($imagenNueva, $archivoTemporal, 80); // Calidad 80%

        // Limpiar memoria
        imagedestroy($imagenOriginal);
        imagedestroy($imagenNueva);

        // Obtener el tamaño del archivo comprimido
        $nuevoTamaño = filesize($archivoTemporal);

        // Modificar la extensión del nombre del archivo a .jpg
        $nombreSinExtension = pathinfo($archivo['name'], PATHINFO_FILENAME);
        $nuevoNombre = $nombreSinExtension . '_compressed.jpg';

        // Retornar array con la información del archivo comprimido
        return [
            'name' => $nuevoNombre,
            'type' => 'image/jpeg',
            'tmp_name' => $archivoTemporal,
            'error' => 0,
            'size' => $nuevoTamaño,
            'original_size' => $archivo['size'], // Para mostrar información de compresión
            'compressed' => $nuevoTamaño < $archivo['size']
        ];
    }

}