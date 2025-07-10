<?php

class TallerFoto
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function manejar($idCoti, $fotos, $fotosEquipo = [])
    {
        if (!isset($fotos) || !is_array($fotos) || empty($fotos['name'])) {
            error_log("No hay fotos para procesar en la cotización ID: " . $idCoti);
            return;
        }

        try {
            $uploadDir = dirname(dirname(__DIR__)) . '/../public/assets/img/cotizaciones/';

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception("No se pudo crear el directorio para las fotos");
                }
            }

            if (!is_writable($uploadDir)) {
                throw new Exception("El directorio de fotos no tiene permisos de escritura");
            }

            $uploadedFiles = [];
            $errors = [];

            foreach ($fotos['tmp_name'] as $key => $tmp_name) {
                if ($fotos['error'][$key] === UPLOAD_ERR_OK) {
                    $extension = pathinfo($fotos['name'][$key], PATHINFO_EXTENSION);
                    $fileName = uniqid('img_') . '_' . time() . '.' . $extension;
                    $targetFilePath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmp_name, $targetFilePath)) {
                        $uploadedFiles[] = [
                            'nombre' => $fileName,
                            'equipo_index' => isset($fotosEquipo[$key]) ? intval($fotosEquipo[$key]) : 0
                        ];
                    } else {
                        $errors[] = "No se pudo mover el archivo: " . $fotos['name'][$key];
                    }
                } else {
                    $errors[] = "Error al subir el archivo: " . $fotos['name'][$key];
                }
            }

            if (!empty($uploadedFiles)) {
                $this->conectar->begin_transaction();

                try {
                    // Verificar si la columna equipo_index existe
                    $columnExists = $this->checkIfColumnExists('taller_cotizaciones_fotos', 'equipo_index');

                    foreach ($uploadedFiles as $file) {
                        if ($columnExists) {
                            $sqlInsert = "INSERT INTO taller_cotizaciones_fotos (id_cotizacion, nombre_foto, equipo_index) VALUES (?, ?, ?)";
                            $stmtInsert = $this->conectar->prepare($sqlInsert);
                            if ($stmtInsert === false) {
                                throw new Exception("Error preparando la consulta INSERT: " . $this->conectar->error);
                            }

                            if (!$stmtInsert->bind_param("isi", $idCoti, $file['nombre'], $file['equipo_index'])) {
                                throw new Exception("Error al vincular parámetros: " . $stmtInsert->error);
                            }
                        } else {
                            $sqlInsert = "INSERT INTO taller_cotizaciones_fotos (id_cotizacion, nombre_foto) VALUES (?, ?)";
                            $stmtInsert = $this->conectar->prepare($sqlInsert);
                            if ($stmtInsert === false) {
                                throw new Exception("Error preparando la consulta INSERT: " . $this->conectar->error);
                            }

                            if (!$stmtInsert->bind_param("is", $idCoti, $file['nombre'])) {
                                throw new Exception("Error al vincular parámetros: " . $stmtInsert->error);
                            }
                        }

                        if (!$stmtInsert->execute()) {
                            throw new Exception("Error al ejecutar la inserción: " . $stmtInsert->error);
                        }

                        $stmtInsert->close();
                    }

                    $this->conectar->commit();
                    error_log("Fotos guardadas correctamente para la cotización ID: " . $idCoti);
                } catch (Exception $e) {
                    $this->conectar->rollback();
                    foreach ($uploadedFiles as $file) {
                        $filePath = $uploadDir . $file['nombre'];
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                    throw $e;
                }
            }

            if (!empty($errors)) {
                error_log("Errores al procesar fotos: " . implode(", ", $errors));
            }

        } catch (Exception $e) {
            error_log("Error en manejarFotos: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerPorCotizacion($id_cotizacion)
    {
        $sql = "SELECT * FROM taller_cotizaciones_fotos WHERE id_cotizacion = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $id_cotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function eliminar($id_cotizacion, $nombre_foto, $equipo_index = null)
    {
        // 1. Eliminar el archivo físico
        $ruta_foto = dirname(dirname(__DIR__)) . '/../public/assets/img/cotizaciones/' . $nombre_foto;
        if (file_exists($ruta_foto)) {
            if (!unlink($ruta_foto)) {
                throw new Exception('No se pudo eliminar el archivo físico');
            }
        }

        // 2. Eliminar de la base de datos
        $sql = "DELETE FROM taller_cotizaciones_fotos 
                WHERE id_cotizacion = ? 
                AND nombre_foto = ?";

        if ($equipo_index !== null) {
            $sql .= " AND equipo_index = ?";
        }

        $stmt = $this->conectar->prepare($sql);

        if ($equipo_index !== null) {
            $stmt->bind_param("isi", $id_cotizacion, $nombre_foto, $equipo_index);
        } else {
            $stmt->bind_param("is", $id_cotizacion, $nombre_foto);
        }

        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar el registro de la base de datos');
        }

        return true;
    }

    private function checkIfColumnExists($table, $column)
    {
        $sql = "SHOW COLUMNS FROM $table LIKE '$column'";
        $result = $this->conectar->query($sql);
        return $result->num_rows > 0;
    }
}