<?php

require_once 'app/helpers/ImageStorage.php';

class TallerFoto
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function manejar($idCoti, $fotos, $fotosEquipo = [])
    {
        error_log("DEBUG: manejar() called for cotizacion ID: " . $idCoti);
        error_log("DEBUG: Fotos array content: " . print_r($fotos, true));

        if (!isset($fotos) || !is_array($fotos) || empty($fotos['name'][0])) { // Check name[0] for actual files
            error_log("DEBUG: No files received or \$fotos array is empty/malformed (name[0] check).");
            return;
        }

        try {
            $uploadedFiles = [];
            $errors = [];

            foreach ($fotos['tmp_name'] as $key => $tmp_name) {
                error_log("DEBUG: Processing file key: " . $key . ", name: " . $fotos['name'][$key] . ", error: " . $fotos['error'][$key]);
                if ($fotos['error'][$key] === UPLOAD_ERR_OK) {
                    try {
                        $singleFile = [
                            'tmp_name' => $tmp_name,
                            'name'     => $fotos['name'][$key],
                            'error'    => $fotos['error'][$key],
                            'size'     => $fotos['size'][$key],
                        ];
                        $fileName = ImageStorage::save($singleFile, 'cotizaciones-taller');
                        error_log("DEBUG: File saved via ImageStorage: " . $fileName);
                        $uploadedFiles[] = [
                            'nombre'       => $fileName,
                            'equipo_index' => isset($fotosEquipo[$key]) ? intval($fotosEquipo[$key]) : 0
                        ];
                    } catch (RuntimeException $e) {
                        error_log("ERROR: ImageStorage save failed: " . $e->getMessage());
                        $errors[] = "No se pudo guardar el archivo: " . $fotos['name'][$key] . " - " . $e->getMessage();
                    }
                } else {
                    error_log("ERROR: Upload error for file " . $fotos['name'][$key] . ": " . $fotos['error'][$key] . " (See PHP upload error codes for details).");
                    $errors[] = "Error al subir el archivo: " . $fotos['name'][$key] . " (Código: " . $fotos['error'][$key] . ")";
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
                    error_log("DEBUG: Fotos guardadas correctamente en DB para cotización ID: " . $idCoti);
                } catch (Exception $e) {
                    $this->conectar->rollback();
                    error_log("ERROR: Database transaction failed. Rolling back and deleting uploaded files.");
                    foreach ($uploadedFiles as $file) {
                        ImageStorage::delete('cotizaciones-taller', $file['nombre']);
                        error_log("DEBUG: Deleted partially uploaded file: " . $file['nombre']);
                    }
                    throw $e; // Re-throw the exception after cleanup
                }
            } else {
                error_log("DEBUG: No files were successfully uploaded to the server.");
            }

            if (!empty($errors)) {
                error_log("WARNING: Errors encountered during file processing: " . implode(", ", $errors));
            }

        } catch (Exception $e) {
            error_log("CRITICAL ERROR in manejarFotos: " . $e->getMessage() . " (Trace: " . $e->getTraceAsString() . ")");
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
        ImageStorage::delete('cotizaciones-taller', $nombre_foto);

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