<?php

class MotivoGuiaController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getMotivo()
    {
        $respuesta = [];

        $sql = "SELECT id, nombre, es_defecto FROM motivos_guia ORDER BY nombre";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode(['status' => true, 'data' => $respuesta]);
    }

    public function getOneMotivo()
    {
        $id = $this->conectar->real_escape_string($_POST['id']);

        $sql = "SELECT id, nombre, es_defecto FROM motivos_guia WHERE id = '$id'";

        $resultado = $this->conectar->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            return json_encode(['status' => true, 'data' => $row]);
        }

        return json_encode(['status' => false, 'message' => 'Motivo no encontrado']);
    }

    public function saveMotivo()
    {
        $nombre = $this->conectar->real_escape_string($_POST['nombre']);
        $sql = "INSERT INTO motivos_guia (nombre) VALUES ('$nombre')";

        if ($this->conectar->query($sql)) {
            $id = $this->conectar->insert_id;
            return json_encode([
                'status' => true,
                'message' => 'Motivo guardado correctamente',

                'data' => ['id' => $id, 'nombre' => $nombre, 'es_defecto' => false]
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al guardar el motivo']);
    }

    public function updateMotivo()
    {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $nombre = $this->conectar->real_escape_string($_POST['nombre']);
        $sql = "UPDATE motivos_guia SET nombre='$nombre' WHERE id ='$id'";

        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Motivo actualizado correctamente'
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al actualizar el motivo']);
    }

    public function deleteMotivo()
    {
        $id = $this->conectar->real_escape_string($_POST['id']);
        $sql = "DELETE FROM motivos_guia WHERE id ='$id'";

        if ($this->conectar->query($sql)) {
            return json_encode([
                'status' => true,
                'message' => 'Motivo eliminado correctamente'
            ]);
        }

        return json_encode(['status' => false, 'message' => 'Error al eliminar el motivo']);
    }
    // Nueva función para establecer un motivo como predeterminado
    public function setDefaultMotivo()
    {
        $id = $this->conectar->real_escape_string($_POST['id']);
        // Convertir el valor de 'is_default' a booleano
        $isDefault = filter_var($_POST['is_default'], FILTER_VALIDATE_BOOLEAN);

        $this->conectar->begin_transaction(); // Iniciar una transacción para asegurar la atomicidad
        try {
            // Paso 1: Desmarcar todos los motivos como predeterminados
            // Esto asegura que solo un motivo pueda ser 'es_defecto = TRUE' a la vez
            $sql_reset = "UPDATE motivos_guia SET es_defecto = FALSE";
            if (!$this->conectar->query($sql_reset)) {
                throw new Exception("Error al resetear motivos por defecto: " . $this->conectar->error);
            }

            // Paso 2: Si se solicitó marcar este motivo como predeterminado, hacerlo
            if ($isDefault) {
                $sql_set = "UPDATE motivos_guia SET es_defecto = TRUE WHERE id = '$id'";
                if (!$this->conectar->query($sql_set)) {
                    throw new Exception("Error al establecer motivo por defecto: " . $this->conectar->error);
                }
            }

            $this->conectar->commit(); // Confirmar la transacción si todo fue bien
            return json_encode([
                'status' => true,
                'message' => $isDefault ? 'Motivo establecido como predeterminado.' : 'Motivo desmarcado como predeterminado.'
            ]);
        } catch (Exception $e) {
            $this->conectar->rollback(); // Revertir la transacción si hubo un error
            return json_encode([
                'status' => false,
                'message' => 'Error al actualizar el motivo predeterminado: ' . $e->getMessage()
            ]);
        }
    }
}