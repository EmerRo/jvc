<?php

require_once 'app/models/Notificacion.php';

class NotificacionController extends Controller
{
    private $notificacion;
    private $conectar;

    public function __construct()
    {
        $this->notificacion = new Notificacion();
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Obtener notificaciones no leídas para el usuario actual
     */
    public function obtenerNoLeidas()
    {
        try {
            // Verificar sesión activa
            if (!isset($_SESSION['nombres'])) {
                echo json_encode(['error' => 'Sesión no válida']);
                return;
            }

            // Construir nombre completo del usuario
            $usuario_actual = $_SESSION['nombres'];
            if (isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                $usuario_actual .= ' ' . $_SESSION['apellidos'];
            }
            $notificaciones = $this->notificacion->obtenerNoLeidas($usuario_actual);

            header('Content-Type: application/json');
            echo json_encode($notificaciones);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::obtenerNoLeidas(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener notificaciones']);
        }
    }

    /**
     * Contar notificaciones no leídas
     */
    public function contarNoLeidas()
    {
        try {
            if (!isset($_SESSION['nombres'])) {
                echo json_encode(['error' => 'Sesión no válida']);
                return;
            }

            $usuario_actual = $_SESSION['nombres'];
            if (isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                $usuario_actual .= ' ' . $_SESSION['apellidos'];
            }
            $cantidad = $this->notificacion->contarNoLeidas($usuario_actual);

            header('Content-Type: application/json');
            echo json_encode(['cantidad' => $cantidad]);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::contarNoLeidas(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al contar notificaciones']);
        }
    }

    /**
     * Marcar una notificación como leída
     */
    public function marcarComoLeida()
    {
        try {
            if (!isset($_POST['id_notificacion'])) {
                echo json_encode(['success' => false, 'error' => 'ID de notificación no proporcionado']);
                return;
            }

            if (!isset($_SESSION['nombres'])) {
                echo json_encode(['success' => false, 'error' => 'Sesión no válida']);
                return;
            }

            $id_notificacion = filter_var($_POST['id_notificacion'], FILTER_SANITIZE_NUMBER_INT);
            $usuario_actual = $_SESSION['nombres'];
            if (isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                $usuario_actual .= ' ' . $_SESSION['apellidos'];
            }

            $result = $this->notificacion->marcarComoLeida($id_notificacion, $usuario_actual);

            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::marcarComoLeida(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error al marcar como leída']);
        }
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas()
    {
        try {
            if (!isset($_SESSION['nombres'])) {
                echo json_encode(['success' => false, 'error' => 'Sesión no válida']);
                return;
            }

            $usuario_actual = $_SESSION['nombres'];
            if (isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                $usuario_actual .= ' ' . $_SESSION['apellidos'];
            }
            $result = $this->notificacion->marcarTodasComoLeidas($usuario_actual);

            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::marcarTodasComoLeidas(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error al marcar todas como leídas']);
        }
    }

    /**
     * Obtener todas las notificaciones (historial)
     */
    public function obtenerTodas()
    {
        try {
            if (!isset($_SESSION['nombres'])) {
                echo json_encode(['error' => 'Sesión no válida']);
                return;
            }

            $limite = isset($_POST['limite']) ? filter_var($_POST['limite'], FILTER_SANITIZE_NUMBER_INT) : 50;
            $usuario_actual = $_SESSION['nombres'];
            if (isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                $usuario_actual .= ' ' . $_SESSION['apellidos'];
            }

            $notificaciones = $this->notificacion->obtenerTodas($usuario_actual, $limite);

            header('Content-Type: application/json');
            echo json_encode($notificaciones);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::obtenerTodas(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener historial de notificaciones']);
        }
    }

    /**
     * Server-Sent Events para notificaciones en tiempo real
     */
    public function streamNotificaciones()
    {
        try {
            // Verificar sesión
            if (!isset($_SESSION['nombres'])) {
                header('HTTP/1.1 403 Forbidden');
                exit('Sesión no válida');
            }

            // Configurar headers para Server-Sent Events
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Cache-Control');

            // Evitar timeout
            set_time_limit(0);
            ignore_user_abort(false);

            // Construir nombre completo del usuario
            $usuario_actual = $_SESSION['nombres'];
            if (isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                $usuario_actual .= ' ' . $_SESSION['apellidos'];
            }
            $usuario_id = $_SESSION['usuario_fac'] ?? 0;

            // Registrar usuario como online
            $session_token = session_id();
            $this->notificacion->registrarUsuarioOnline($usuario_id, $usuario_actual, $session_token);

            $ultima_verificacion = time();

            while (!connection_aborted()) {
                $tiempo_actual = time();

                // Verificar notificaciones cada 3 segundos
                if ($tiempo_actual - $ultima_verificacion >= 3) {
                    $notificaciones = $this->notificacion->obtenerNoLeidas($usuario_actual);

                    if (!empty($notificaciones)) {
                        // Enviar notificaciones
                        echo "data: " . json_encode([
                            'type' => 'notifications',
                            'data' => $notificaciones,
                            'timestamp' => date('Y-m-d H:i:s')
                        ]) . "\n\n";

                        ob_flush();
                        flush();
                    }

                    // Enviar heartbeat cada 30 segundos
                    if ($tiempo_actual % 30 === 0) {
                        echo "data: " . json_encode([
                            'type' => 'heartbeat',
                            'timestamp' => date('Y-m-d H:i:s')
                        ]) . "\n\n";

                        ob_flush();
                        flush();
                    }

                    $ultima_verificacion = $tiempo_actual;
                }

                // Dormir 1 segundo antes de la siguiente iteración
                sleep(1);

                // Verificar si la conexión sigue activa
                if (connection_aborted()) {
                    break;
                }
            }

            // Cleanup al desconectar
            $this->notificacion->eliminarUsuarioOnline($usuario_id);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::streamNotificaciones(): " . $e->getMessage());

            // Cleanup en caso de error
            if (isset($usuario_id)) {
                $this->notificacion->eliminarUsuarioOnline($usuario_id);
            }
        }
    }

    /**
     * Crear una notificación (para uso interno del sistema)
     */
    public function crear()
    {
        try {
            if (!isset($_POST['tipo']) || !isset($_POST['mensaje']) ||
                !isset($_POST['modulo_origen']) || !isset($_POST['registro_id'])) {
                echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                return;
            }

            $tipo = Tools::onlyTextNoHtml($_POST['tipo'] ?? '');
            $mensaje = Tools::onlyTextNoHtml($_POST['mensaje'] ?? '');
            $modulo_origen = Tools::onlyTextNoHtml($_POST['modulo_origen'] ?? '');
            $registro_id = filter_var($_POST['registro_id'], FILTER_SANITIZE_NUMBER_INT);
            $usuario_destino = isset($_POST['usuario_destino']) ?
                Tools::onlyTextNoHtml($_POST['usuario_destino']) : null;

            $usuario_origen = $_SESSION['nombres'];
            if (isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                $usuario_origen .= ' ' . $_SESSION['apellidos'];
            }

            $id_notificacion = $this->notificacion->crear(
                $tipo,
                $mensaje,
                $usuario_origen,
                $modulo_origen,
                $registro_id,
                $usuario_destino
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => ($id_notificacion !== false),
                'id_notificacion' => $id_notificacion
            ]);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::crear(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error al crear notificación']);
        }
    }

    /**
     * Limpiar notificaciones antiguas (para mantenimiento)
     */
    public function limpiarAntiguas()
    {
        try {
            // Solo admin puede ejecutar esta función
            if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
                echo json_encode(['success' => false, 'error' => 'No tiene permisos']);
                return;
            }

            $dias = isset($_POST['dias']) ? filter_var($_POST['dias'], FILTER_SANITIZE_NUMBER_INT) : 30;
            $result = $this->notificacion->limpiarAntiguas($dias);

            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::limpiarAntiguas(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Error al limpiar notificaciones']);
        }
    }

    /**
     * Obtener usuarios online
     */
    public function obtenerUsuariosOnline()
    {
        try {
            $usuarios = $this->notificacion->obtenerUsuariosOnline();

            header('Content-Type: application/json');
            echo json_encode($usuarios);

        } catch (Exception $e) {
            error_log("Error en NotificacionController::obtenerUsuariosOnline(): " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error al obtener usuarios online']);
        }
    }
}