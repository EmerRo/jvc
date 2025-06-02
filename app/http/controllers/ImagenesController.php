<?php

class ImagenesController extends Controller {
    private $conectar;
    
    public function __construct() {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function guardarImagenes() {
        error_log("Iniciando guardarImagenes");
        error_log("FILES recibido: " . print_r($_FILES, true));

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['images'])) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
            $uploadedFiles = [];
            
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    error_log("Error al crear directorio: " . $uploadDir);
                    return json_encode(['success' => false, 'message' => 'Error al crear directorio de uploads']);
                }
            }
            
            if (!is_writable($uploadDir)) {
                error_log("El directorio no tiene permisos de escritura: " . $uploadDir);
                return json_encode(['success' => false, 'message' => 'Error de permisos en el servidor']);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = $_FILES['images']['name'][$key];
                    $fileType = $_FILES['images']['type'][$key];
                    
                    if (strpos($fileType, 'image/') === 0) {
                        $newFileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $fileName);
                        $destination = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($tmp_name, $destination)) {
                            $uploadedFiles[] = '/uploads/' . $newFileName;
                        } else {
                            error_log("Error al mover archivo: " . $tmp_name . " a " . $destination);
                        }
                    }
                } else {
                    error_log("Error en archivo: " . $key . " - Código: " . $_FILES['images']['error'][$key]);
                }
            }
            
            if (count($uploadedFiles) > 0) {
                try {
                    $imageSet = implode(',', $uploadedFiles);
                    
                    $stmt = $this->conectar->prepare("INSERT INTO imagen_sets (imagenes) VALUES (?)");
                    if (!$stmt) {
                        error_log("Error en prepare: " . $this->conectar->error);
                        return json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta']);
                    }
                    
                    $stmt->bind_param("s", $imageSet);
                    
                    if ($stmt->execute()) {
                        error_log("Imágenes guardadas correctamente: " . $imageSet);
                        return json_encode([
                            'success' => true, 
                            'message' => 'Imágenes guardadas correctamente',
                            'files' => $uploadedFiles
                        ]);
                    } else {
                        error_log("Error en execute: " . $stmt->error);
                        return json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos']);
                    }
                } catch (Exception $e) {
                    error_log("Excepción: " . $e->getMessage());
                    return json_encode(['success' => false, 'message' => 'Error en el servidor']);
                }
            } else {
                return json_encode(['success' => false, 'message' => 'No se pudieron procesar las imágenes']);
            }
        }
        
        return json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
}