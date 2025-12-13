<?php
namespace JVC\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Handler para notificaciones en tiempo real
 * Solo funciona en localhost
 */
class NotificacionesHandler implements MessageComponentInterface {
    protected $clients;
    protected $usuarios;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->usuarios = [];
        echo "✓ Servidor WebSocket iniciado en ws://localhost:8080\n";
        echo "⚠ Solo funciona en LOCALHOST (desarrollo)\n";
        echo "==========================================\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Almacenar la nueva conexión
        $this->clients->attach($conn);

        echo sprintf(
            "[%s] Nueva conexión: ID #%d (Total: %d)\n",
            date('H:i:s'),
            $conn->resourceId,
            count($this->clients)
        );
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (!$data) {
            echo "✗ Mensaje inválido recibido\n";
            return;
        }

        echo sprintf(
            "[%s] Mensaje de #%d: %s\n",
            date('H:i:s'),
            $from->resourceId,
            $data['tipo'] ?? 'desconocido'
        );

        switch ($data['tipo'] ?? '') {
            case 'auth':
                // Registrar usuario autenticado
                $this->usuarios[$from->resourceId] = [
                    'id_usuario' => $data['id_usuario'] ?? null,
                    'nombre' => $data['nombre'] ?? 'Anónimo',
                    'conn' => $from
                ];

                // Confirmar autenticación
                $from->send(json_encode([
                    'tipo' => 'auth_ok',
                    'mensaje' => 'Autenticado correctamente'
                ]));

                echo "✓ Usuario autenticado: {$data['nombre']}\n";
                break;

            case 'notificacion':
                // Enviar notificación a todos los clientes
                $this->broadcast([
                    'tipo' => 'notificacion',
                    'titulo' => $data['titulo'] ?? 'Notificación',
                    'mensaje' => $data['mensaje'] ?? '',
                    'icono' => $data['icono'] ?? 'info',
                    'timestamp' => time()
                ], $from);
                break;

            case 'venta_nueva':
                // Notificar nueva venta a todos
                $this->broadcast([
                    'tipo' => 'venta_nueva',
                    'venta_id' => $data['venta_id'] ?? null,
                    'cliente' => $data['cliente'] ?? '',
                    'total' => $data['total'] ?? 0,
                    'timestamp' => time()
                ]);
                break;

            case 'stock_bajo':
                // Alerta de stock bajo
                $this->broadcast([
                    'tipo' => 'stock_bajo',
                    'producto' => $data['producto'] ?? '',
                    'cantidad' => $data['cantidad'] ?? 0,
                    'timestamp' => time()
                ]);
                break;

            case 'ping':
                // Responder pong (mantener conexión viva)
                $from->send(json_encode(['tipo' => 'pong']));
                break;

            default:
                echo "⚠ Tipo de mensaje desconocido: {$data['tipo']}\n";
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Remover la conexión
        $this->clients->detach($conn);

        // Remover de usuarios autenticados
        if (isset($this->usuarios[$conn->resourceId])) {
            $nombre = $this->usuarios[$conn->resourceId]['nombre'];
            unset($this->usuarios[$conn->resourceId]);
            echo sprintf(
                "[%s] ✗ Desconectado: %s - ID #%d (Total: %d)\n",
                date('H:i:s'),
                $nombre,
                $conn->resourceId,
                count($this->clients)
            );
        } else {
            echo sprintf(
                "[%s] ✗ Desconectado: ID #%d (Total: %d)\n",
                date('H:i:s'),
                $conn->resourceId,
                count($this->clients)
            );
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "✗ Error: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Enviar mensaje a todos los clientes conectados
     */
    private function broadcast($data, ConnectionInterface $except = null) {
        $mensaje = json_encode($data);
        $enviados = 0;

        foreach ($this->clients as $client) {
            if ($except && $client === $except) {
                continue; // No enviar al remitente
            }
            $client->send($mensaje);
            $enviados++;
        }

        echo "→ Mensaje enviado a {$enviados} cliente(s)\n";
    }

    /**
     * Enviar mensaje a un usuario específico
     */
    public function enviarAUsuario($id_usuario, $data) {
        foreach ($this->usuarios as $usuario) {
            if ($usuario['id_usuario'] == $id_usuario) {
                $usuario['conn']->send(json_encode($data));
                return true;
            }
        }
        return false;
    }
}
