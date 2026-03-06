/**
 * Cliente WebSocket para JVC
 * ⚠️ SOLO FUNCIONA EN LOCALHOST (desarrollo)
 *
 * Uso:
 * 1. Incluir este archivo en tu HTML
 * 2. El cliente se conecta automáticamente si estás en localhost
 * 3. Usa window.JVCWebSocket para interactuar
 */

(function() {
    'use strict';

    // ✅ Detectar si estamos en localhost
    const esLocalhost = (
        window.location.hostname === 'localhost' ||
        window.location.hostname === '127.0.0.1' ||
        window.location.hostname === '[::1]'
    );

    if (!esLocalhost) {
        // console.log('🔒 WebSocket deshabilitado: Solo funciona en localhost');
        return; // NO inicializar en producción
    }

    console.log('🚀 Inicializando WebSocket (modo desarrollo)');

    class JVCWebSocketClient {
        constructor() {
            this.ws = null;
            this.reconnectInterval = 5000; // 5 segundos
            this.reconnectTimer = null;
            this.isConnected = false;
            this.callbacks = {
                onConnect: [],
                onDisconnect: [],
                onMessage: [],
                onError: []
            };
            this.autoReconnect = true;

            this.connect();
        }

        /**
         * Conectar al servidor WebSocket
         */
        connect() {
            if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                console.log('⚠️ Ya estás conectado');
                return;
            }

            try {
                console.log('🔌 Conectando a ws://localhost:8080...');
                this.ws = new WebSocket('ws://localhost:8080');

                this.ws.onopen = () => this.handleOpen();
                this.ws.onmessage = (event) => this.handleMessage(event);
                this.ws.onclose = () => this.handleClose();
                this.ws.onerror = (error) => this.handleError(error);

            } catch (error) {
                console.error('❌ Error al conectar:', error);
                this.scheduleReconnect();
            }
        }

        /**
         * Conexión establecida
         */
        handleOpen() {
            console.log('✅ Conectado al servidor WebSocket');
            this.isConnected = true;

            // Cancelar reconexión si estaba programada
            if (this.reconnectTimer) {
                clearTimeout(this.reconnectTimer);
                this.reconnectTimer = null;
            }

            // Autenticar usuario si existe sesión
            if (typeof _SESSION !== 'undefined' && _SESSION.id_usuario) {
                this.autenticar(_SESSION.id_usuario, _SESSION.nombre_usuario || 'Usuario');
            }

            // Ejecutar callbacks
            this.callbacks.onConnect.forEach(cb => cb());

            // Mostrar notificación
            this.mostrarNotificacion('Conectado', 'WebSocket conectado correctamente', 'success');
        }

        /**
         * Mensaje recibido del servidor
         */
        handleMessage(event) {
            try {
                const data = JSON.parse(event.data);
                console.log('📩 Mensaje recibido:', data);

                // Ejecutar callbacks personalizados
                this.callbacks.onMessage.forEach(cb => cb(data));

                // Manejar tipos de mensajes
                switch (data.tipo) {
                    case 'auth_ok':
                        console.log('✓ Autenticado correctamente');
                        break;

                    case 'notificacion':
                        this.mostrarNotificacion(
                            data.titulo,
                            data.mensaje,
                            data.icono || 'info'
                        );
                        break;

                    case 'venta_nueva':
                        this.handleVentaNueva(data);
                        break;

                    case 'stock_bajo':
                        this.handleStockBajo(data);
                        break;

                    case 'pong':
                        // Respuesta al ping (mantener vivo)
                        break;

                    default:
                        console.log('⚠️ Tipo de mensaje no manejado:', data.tipo);
                }

            } catch (error) {
                console.error('❌ Error procesando mensaje:', error);
            }
        }

        /**
         * Conexión cerrada
         */
        handleClose() {
            console.log('🔌 Desconectado del servidor WebSocket');
            this.isConnected = false;

            // Ejecutar callbacks
            this.callbacks.onDisconnect.forEach(cb => cb());

            // Programar reconexión automática
            if (this.autoReconnect) {
                this.scheduleReconnect();
            }
        }

        /**
         * Error en la conexión
         */
        handleError(error) {
            console.error('❌ Error WebSocket:', error);

            // Ejecutar callbacks
            this.callbacks.onError.forEach(cb => cb(error));
        }

        /**
         * Programar reconexión automática
         */
        scheduleReconnect() {
            if (this.reconnectTimer) {
                return; // Ya hay una reconexión programada
            }

            console.log(`⏱️ Reconectando en ${this.reconnectInterval / 1000} segundos...`);

            this.reconnectTimer = setTimeout(() => {
                this.reconnectTimer = null;
                this.connect();
            }, this.reconnectInterval);
        }

        /**
         * Autenticar usuario
         */
        autenticar(id_usuario, nombre) {
            this.send({
                tipo: 'auth',
                id_usuario: id_usuario,
                nombre: nombre
            });
        }

        /**
         * Enviar mensaje al servidor
         */
        send(data) {
            if (!this.isConnected) {
                console.warn('⚠️ No conectado. Mensaje no enviado:', data);
                return false;
            }

            try {
                this.ws.send(JSON.stringify(data));
                return true;
            } catch (error) {
                console.error('❌ Error enviando mensaje:', error);
                return false;
            }
        }

        /**
         * Enviar notificación a todos
         */
        enviarNotificacion(titulo, mensaje, icono = 'info') {
            return this.send({
                tipo: 'notificacion',
                titulo: titulo,
                mensaje: mensaje,
                icono: icono
            });
        }

        /**
         * Notificar nueva venta
         */
        notificarVentaNueva(venta_id, cliente, total) {
            return this.send({
                tipo: 'venta_nueva',
                venta_id: venta_id,
                cliente: cliente,
                total: total
            });
        }

        /**
         * Alerta de stock bajo
         */
        alertarStockBajo(producto, cantidad) {
            return this.send({
                tipo: 'stock_bajo',
                producto: producto,
                cantidad: cantidad
            });
        }

        /**
         * Manejar nueva venta (callback)
         */
        handleVentaNueva(data) {
            this.mostrarNotificacion(
                '💰 Nueva Venta',
                `Cliente: ${data.cliente}\nTotal: S/ ${data.total}`,
                'success'
            );
        }

        /**
         * Manejar stock bajo (callback)
         */
        handleStockBajo(data) {
            this.mostrarNotificacion(
                '⚠️ Stock Bajo',
                `Producto: ${data.producto}\nCantidad: ${data.cantidad}`,
                'warning'
            );
        }

        /**
         * Mostrar notificación visual
         */
        mostrarNotificacion(titulo, mensaje, tipo = 'info') {
            // Usar SweetAlert2 si está disponible
            if (typeof Swal !== 'undefined') {
                const iconMap = {
                    'success': 'success',
                    'error': 'error',
                    'warning': 'warning',
                    'info': 'info'
                };

                Swal.fire({
                    title: titulo,
                    text: mensaje,
                    icon: iconMap[tipo] || 'info',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            } else {
                // Fallback a console.log
                console.log(`${tipo.toUpperCase()}: ${titulo} - ${mensaje}`);
            }
        }

        /**
         * Agregar callback personalizado
         */
        on(evento, callback) {
            if (this.callbacks[evento]) {
                this.callbacks[evento].push(callback);
            }
        }

        /**
         * Desconectar manualmente
         */
        disconnect() {
            this.autoReconnect = false;
            if (this.ws) {
                this.ws.close();
            }
        }
    }

    // Crear instancia global
    window.JVCWebSocket = new JVCWebSocketClient();

    console.log('✓ Cliente WebSocket disponible en: window.JVCWebSocket');

})();
