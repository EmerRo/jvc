<?php
/**
 * Servidor WebSocket para JVC
 * ⚠️ SOLO PARA DESARROLLO LOCAL - NO USAR EN PRODUCCIÓN
 *
 * Iniciar servidor:
 * php server/websocket-server.php
 *
 * Detener servidor:
 * Ctrl + C
 */

// Solo mostrar advertencia si se detecta que puede estar en producción
// La seguridad real está en que el servidor solo escucha en 127.0.0.1 (línea 48)
if (php_sapi_name() !== 'cli') {
    die("⚠️ ERROR: Este servidor debe ejecutarse desde la línea de comandos (CLI)\n");
}

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/classes/NotificacionesHandler.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use JVC\WebSocket\NotificacionesHandler;

echo "\n";
echo "╔════════════════════════════════════════════════════╗\n";
echo "║   Servidor WebSocket JVC - MODO DESARROLLO        ║\n";
echo "╚════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Puerto: 8080\n";
echo "URL: ws://localhost:8080\n";
echo "Entorno: SOLO LOCALHOST\n";
echo "\n";
echo "Presiona Ctrl+C para detener el servidor\n";
echo "==========================================\n\n";

try {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new NotificacionesHandler()
            )
        ),
        8080,
        '127.0.0.1' // Solo escuchar en localhost
    );

    echo "✓ Servidor iniciado correctamente\n";
    echo "✓ Esperando conexiones...\n\n";

    $server->run();

} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nPosibles soluciones:\n";
    echo "  1. Verifica que el puerto 8080 esté libre\n";
    echo "  2. Ejecuta como administrador si es necesario\n";
    echo "  3. Cierra otros programas que usen el puerto 8080\n\n";
    exit(1);
}
