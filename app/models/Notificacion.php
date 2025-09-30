<?php

class Notificacion
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Crear una nueva notificación
     */
    public function crear($tipo, $mensaje, $usuario_origen, $modulo_origen, $registro_id, $usuario_destino = null)
    {
        try {
            $sql = "INSERT INTO notificaciones (tipo, mensaje, usuario_origen, usuario_destino, modulo_origen, registro_id)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("sssssi", $tipo, $mensaje, $usuario_origen, $usuario_destino, $modulo_origen, $registro_id);

            if ($stmt->execute()) {
                $id = $this->conectar->insert_id;
                return $id;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en Notificacion::crear(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener notificaciones no leídas para un usuario
     */
    public function obtenerNoLeidas($usuario_destino = null)
    {
        try {
            if ($usuario_destino) {
                // Notificaciones específicas para el usuario o broadcast (usuario_destino = NULL)
                $sql = "SELECT * FROM notificaciones
                        WHERE (usuario_destino = ? OR usuario_destino IS NULL)
                        AND leida = 0
                        ORDER BY created_at DESC
                        LIMIT 50";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("s", $usuario_destino);
            } else {
                // Todas las notificaciones no leídas (para admin)
                $sql = "SELECT * FROM notificaciones
                        WHERE leida = 0
                        ORDER BY created_at DESC
                        LIMIT 50";
                $stmt = $this->conectar->prepare($sql);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $notificaciones = [];
            while ($row = $result->fetch_assoc()) {
                $notificaciones[] = $row;
            }

            return $notificaciones;
        } catch (Exception $e) {
            error_log("Error en Notificacion::obtenerNoLeidas(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Marcar una notificación como leída
     */
    public function marcarComoLeida($id_notificacion, $usuario_destino = null)
    {
        try {
            if ($usuario_destino) {
                // Solo puede marcar como leída si es su notificación o es broadcast
                $sql = "UPDATE notificaciones
                        SET leida = 1, updated_at = CURRENT_TIMESTAMP
                        WHERE id_notificacion = ?
                        AND (usuario_destino = ? OR usuario_destino IS NULL)";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("is", $id_notificacion, $usuario_destino);
            } else {
                // Admin puede marcar cualquier notificación
                $sql = "UPDATE notificaciones
                        SET leida = 1, updated_at = CURRENT_TIMESTAMP
                        WHERE id_notificacion = ?";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("i", $id_notificacion);
            }

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en Notificacion::marcarComoLeida(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marcar todas las notificaciones como leídas para un usuario
     */
    public function marcarTodasComoLeidas($usuario_destino)
    {
        try {
            $sql = "UPDATE notificaciones
                    SET leida = 1, updated_at = CURRENT_TIMESTAMP
                    WHERE (usuario_destino = ? OR usuario_destino IS NULL)
                    AND leida = 0";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("s", $usuario_destino);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en Notificacion::marcarTodasComoLeidas(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todas las notificaciones (leídas y no leídas) para un usuario
     */
    public function obtenerTodas($usuario_destino, $limite = 100)
    {
        try {
            $sql = "SELECT * FROM notificaciones
                    WHERE (usuario_destino = ? OR usuario_destino IS NULL)
                    ORDER BY created_at DESC
                    LIMIT ?";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("si", $usuario_destino, $limite);
            $stmt->execute();
            $result = $stmt->get_result();

            $notificaciones = [];
            while ($row = $result->fetch_assoc()) {
                $notificaciones[] = $row;
            }

            return $notificaciones;
        } catch (Exception $e) {
            error_log("Error en Notificacion::obtenerTodas(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar notificaciones no leídas para un usuario
     */
    public function contarNoLeidas($usuario_destino)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM notificaciones
                    WHERE (usuario_destino = ? OR usuario_destino IS NULL)
                    AND leida = 0";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("s", $usuario_destino);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return (int) $row['total'];
        } catch (Exception $e) {
            error_log("Error en Notificacion::contarNoLeidas(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Eliminar notificaciones antiguas (para limpieza periódica)
     */
    public function limpiarAntiguas($dias = 30)
    {
        try {
            $sql = "DELETE FROM notificaciones
                    WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $dias);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en Notificacion::limpiarAntiguas(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registrar usuario online (para Server-Sent Events)
     */
    public function registrarUsuarioOnline($usuario_id, $usuario_nombre, $session_token)
    {
        try {
            $sql = "INSERT INTO usuarios_online (usuario_id, usuario_nombre, session_token, last_activity)
                    VALUES (?, ?, ?, CURRENT_TIMESTAMP)
                    ON DUPLICATE KEY UPDATE
                    usuario_nombre = VALUES(usuario_nombre),
                    session_token = VALUES(session_token),
                    last_activity = CURRENT_TIMESTAMP";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("iss", $usuario_id, $usuario_nombre, $session_token);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en Notificacion::registrarUsuarioOnline(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar usuario offline
     */
    public function eliminarUsuarioOnline($usuario_id)
    {
        try {
            $sql = "DELETE FROM usuarios_online WHERE usuario_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $usuario_id);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en Notificacion::eliminarUsuarioOnline(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener usuarios online
     */
    public function obtenerUsuariosOnline()
    {
        try {
            // Usuarios activos en los últimos 5 minutos
            $sql = "SELECT * FROM usuarios_online
                    WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";

            $result = $this->conectar->query($sql);

            $usuarios = [];
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }

            return $usuarios;
        } catch (Exception $e) {
            error_log("Error en Notificacion::obtenerUsuariosOnline(): " . $e->getMessage());
            return [];
        }
    }
}