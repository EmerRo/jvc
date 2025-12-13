# 🚀 WebSocket en Tiempo Real - JVC

> ⚠️ **IMPORTANTE**: Este sistema **SOLO funciona en LOCALHOST** (desarrollo). NO está configurado para producción.

## 📋 Tabla de Contenidos

1. [¿Qué es esto?](#qué-es-esto)
2. [Instalación](#instalación)
3. [Iniciar el Servidor](#iniciar-el-servidor)
4. [Cómo Usar](#cómo-usar)
5. [Ejemplos de Uso](#ejemplos-de-uso)
6. [Solución de Problemas](#solución-de-problemas)

---

## 🎯 ¿Qué es esto?

Este sistema implementa **WebSockets** para comunicación en tiempo real entre el servidor y los clientes. Permite:

- ✅ **Notificaciones instantáneas** sin recargar la página
- ✅ **Alertas en tiempo real** (nuevas ventas, stock bajo, etc.)
- ✅ **Sincronización** entre múltiples usuarios
- ✅ **Actualizaciones automáticas** de dashboards

**Tecnología utilizada:**
- **Backend**: Ratchet (PHP WebSocket Server)
- **Frontend**: WebSocket API nativa + jQuery
- **Puerto**: 8080
- **Entorno**: Solo localhost

---

## 📦 Instalación

### Paso 1: Verificar Requisitos

```bash
# Verificar PHP (debe ser 7.4 o superior)
php -v

# Verificar Composer
composer --version
```

### Paso 2: Instalar Dependencias

Ya está instalado! Si necesitas reinstalar:

```bash
cd C:\xampp\htdocs\jvc
composer install
```

---

## 🎬 Iniciar el Servidor

### Método 1: PowerShell (Recomendado)

```powershell
# Abrir PowerShell en C:\xampp\htdocs\jvc
cd C:\xampp\htdocs\jvc

# Iniciar servidor
php server/websocket-server.php
```

### Método 2: CMD

```cmd
cd C:\xampp\htdocs\jvc
php server\websocket-server.php
```

### Método 3: Git Bash

```bash
cd /c/xampp/htdocs/jvc
php server/websocket-server.php
```

**✅ Deberías ver:**

```
╔════════════════════════════════════════════════════╗
║   Servidor WebSocket JVC - MODO DESARROLLO        ║
╚════════════════════════════════════════════════════╝

Puerto: 8080
URL: ws://localhost:8080
Entorno: SOLO LOCALHOST

✓ Servidor WebSocket iniciado en ws://localhost:8080
⚠ Solo funciona en LOCALHOST (desarrollo)
==========================================
✓ Servidor iniciado correctamente
✓ Esperando conexiones...
```

### Detener el Servidor

Presiona `Ctrl + C` en la terminal donde está corriendo.

---

## 💻 Cómo Usar

### Opción 1: Demo Interactivo

1. **Inicia el servidor WebSocket** (ver arriba)
2. **Abre en tu navegador**: `http://localhost/jvc/server/EJEMPLO-USO.html`
3. **Haz clic en "Conectar"**
4. **Prueba los botones** para enviar mensajes

### Opción 2: En tu Aplicación

El cliente WebSocket ya está incluido automáticamente en tu aplicación (solo en localhost).

**Consola del navegador:**

```javascript
// Ver el objeto WebSocket
console.log(window.JVCWebSocket);

// Verificar conexión
console.log(window.JVCWebSocket.isConnected); // true o false

// Enviar notificación
window.JVCWebSocket.enviarNotificacion('Título', 'Mensaje', 'success');

// Notificar nueva venta
window.JVCWebSocket.notificarVentaNueva(123, 'Juan Pérez', 150.50);

// Alerta de stock bajo
window.JVCWebSocket.alertarStockBajo('Laptop HP', 2);
```

---

## 🔥 Ejemplos de Uso

### Ejemplo 1: Notificar Venta Nueva

**En tu controlador de ventas (PHP):**

No necesitas hacer nada en PHP. El cliente JavaScript detectará automáticamente y enviará la notificación.

**En tu JavaScript al crear venta:**

```javascript
// Después de guardar la venta exitosamente
if (window.JVCWebSocket && window.JVCWebSocket.isConnected) {
    window.JVCWebSocket.notificarVentaNueva(
        venta_id,           // ID de la venta
        nombre_cliente,     // Nombre del cliente
        total               // Total de la venta
    );
}
```

### Ejemplo 2: Alertar Stock Bajo

```javascript
// Al detectar stock bajo de un producto
function verificarStock(producto, stock_actual) {
    if (stock_actual <= 5 && window.JVCWebSocket) {
        window.JVCWebSocket.alertarStockBajo(producto, stock_actual);
    }
}
```

### Ejemplo 3: Notificación Personalizada

```javascript
// Notificación genérica
window.JVCWebSocket.enviarNotificacion(
    'Pago Recibido',                    // Título
    'Se recibió el pago de la factura 001-123',  // Mensaje
    'success'                           // Tipo: success, error, warning, info
);
```

### Ejemplo 4: Escuchar Eventos Personalizados

```javascript
// Agregar listener para cuando se conecta
window.JVCWebSocket.on('onConnect', function() {
    console.log('¡Conectado al servidor WebSocket!');
    // Hacer algo cuando se conecta
});

// Agregar listener para mensajes
window.JVCWebSocket.on('onMessage', function(data) {
    console.log('Mensaje recibido:', data);

    // Manejar tipos personalizados
    if (data.tipo === 'actualizacion_dashboard') {
        // Actualizar dashboard
        actualizarGraficos();
    }
});
```

### Ejemplo 5: Integración con Ventas Existentes

**Archivo:** `resources/views/fragment-views/cliente/ventas.php`

```javascript
// Al final del script donde guardas la venta
success: function(resp) {
    if (resp.res) {
        alertExito("Venta registrada correctamente");

        // ✅ AGREGAR ESTO: Notificar venta nueva en tiempo real
        if (window.JVCWebSocket && window.JVCWebSocket.isConnected) {
            window.JVCWebSocket.notificarVentaNueva(
                resp.venta_id,
                $('#cliente_nombre').val(),
                $('#total_venta').val()
            );
        }

        location.reload();
    }
}
```

---

## 🛠️ Solución de Problemas

### Problema 1: "Error: puerto 8080 ya está en uso"

**Solución:**

```bash
# Encontrar qué proceso usa el puerto 8080
netstat -ano | findstr :8080

# Matar el proceso (reemplaza PID con el número que te dio)
taskkill /PID [número] /F
```

### Problema 2: "WebSocket no conecta"

**Verificar:**

1. ✅ ¿El servidor WebSocket está corriendo? (terminal abierta con `php server/websocket-server.php`)
2. ✅ ¿Estás en `localhost` o `127.0.0.1`? (No funciona con IP local como 192.168.x.x)
3. ✅ ¿El puerto 8080 está bloqueado por firewall?

**Abrir puerto en Windows:**

```powershell
# Ejecutar PowerShell como Administrador
New-NetFirewallRule -DisplayName "WebSocket JVC" -Direction Inbound -LocalPort 8080 -Protocol TCP -Action Allow
```

### Problema 3: "No veo el log de conexiones"

Abre la **consola del navegador** (F12 → Console) y busca:

```
🚀 Inicializando WebSocket (modo desarrollo)
🔌 Conectando a ws://localhost:8080...
✅ Conectado al servidor WebSocket
```

Si ves `🔒 WebSocket deshabilitado: Solo funciona en localhost`, significa que no estás en localhost.

### Problema 4: Mensajes no se reciben

1. Verifica que el servidor muestre: `→ Mensaje enviado a X cliente(s)`
2. Abre múltiples pestañas del navegador para probar
3. Revisa la consola del navegador para errores

---

## 📁 Estructura de Archivos

```
C:\xampp\htdocs\jvc\
├── server/
│   ├── websocket-server.php          # Servidor principal
│   ├── classes/
│   │   └── NotificacionesHandler.php # Lógica del servidor
│   ├── EJEMPLO-USO.html               # Demo interactivo
│   └── README-WEBSOCKET.md            # Este archivo
├── public/assets/js/
│   └── websocket-client.js            # Cliente JavaScript
└── resources/views/fragment/
    └── footer.php                      # Incluye el cliente (modificado)
```

---

## 🎓 Conceptos Básicos

### ¿Qué es WebSocket?

WebSocket es un protocolo de comunicación **bidireccional** y **persistente** entre cliente y servidor. A diferencia de AJAX:

| Característica | AJAX | WebSocket |
|---------------|------|-----------|
| Conexión | Por petición | Persistente |
| Dirección | Cliente → Servidor | ↔️ Bidireccional |
| Tiempo real | ❌ No | ✅ Sí |
| Polling | ✅ Requiere | ❌ No requiere |

### ¿Cómo funciona?

```
1. Cliente se conecta → ws://localhost:8080
2. Servidor acepta conexión ✅
3. Conexión permanece abierta 🔌
4. Cualquiera puede enviar mensajes ↔️
5. Mensajes llegan instantáneamente ⚡
```

---

## 🚀 Próximos Pasos

### Funcionalidades Sugeridas

1. **Chat interno** entre usuarios
2. **Notificaciones de pedidos** en tiempo real
3. **Actualización automática** de inventario
4. **Alertas de pagos** pendientes
5. **Dashboard en vivo** con métricas actualizadas

### Personalizar Mensajes

Edita: `server/classes/NotificacionesHandler.php`

```php
case 'mi_tipo_personalizado':
    $this->broadcast([
        'tipo' => 'mi_tipo_personalizado',
        'datos' => $data['datos'],
        'timestamp' => time()
    ]);
    break;
```

---

## ⚠️ Recordatorios Importantes

1. **Solo funciona en LOCALHOST** - No usar en producción sin configuración adicional
2. **Servidor debe estar corriendo** - `php server/websocket-server.php`
3. **Puerto 8080** debe estar libre
4. **No cerrar la terminal** donde corre el servidor WebSocket

---

## 📞 Soporte

Si tienes problemas:

1. Revisa los logs del servidor (terminal donde corre)
2. Revisa la consola del navegador (F12)
3. Verifica que estés en `localhost`
4. Reinicia el servidor WebSocket

---

**¡Listo para usar!** 🎉

Abre `http://localhost/jvc/server/EJEMPLO-USO.html` y empieza a probar.
