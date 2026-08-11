<!-- resources\views\fragment\nav-bar.php -->
<?php
// Incluir el helper de módulos
require_once "app/models/ModulosHelper.php";

// Función de log mejorada para debugging
function logError($message, $data = null)
{
    $log_message = "[" . date('Y-m-d H:i:s') . "] - {$message}";
    if ($data !== null) {
        $log_message .= " - Data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    error_log($log_message);
}

// Verificar si estamos en la página de login
$current_path = $_SERVER['REQUEST_URI'];
if (strpos($current_path, '/login') !== false) {
    return;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_fac']) || !isset($_SESSION['rol'])) {
    header('Location: ' . URL::to('/login'));
    exit;
}

// Variables iniciales
$usuario_id = $_SESSION['usuario_fac'];
$rol_id = $_SESSION['rol'];
$modulos = [];

try {
    $conexion = new Conexion();
    $conectar = $conexion->getConexion();

    if (!$conectar) {
        throw new Exception("No hay conexión a la base de datos");
    }

    // Obtener módulos usando el helper
    $modulos = ModulosHelper::obtenerModulosParaRol($rol_id, $conectar);

} catch (Exception $e) {
    logError("Error en navbar: " . $e->getMessage());
    $modulos = [];
}
?>

<!-- navbar -->
<aside class="jvc-sidebar">
    <div class="jvc-sidebar-header">
        <div class="jvc-logo-container">
            <img src="<?= URL::to('public/assets/images/holaaa.jpg') ?>" alt="JVC Logo"
                class="jvc-sidebar-logo jvc-logo-expanded">
            <img src="<?= URL::to('public/assets/images/logo2JVC.png') ?>" alt="JVC Logo Collapsed"
                class="jvc-sidebar-logo jvc-logo-collapsed">
        </div>
    </div>

    <ul class="jvc-sidebar-menu">
        <!-- Dashboard siempre visible -->
        <li class="jvc-sidebar-item">
            <a href="/" class="jvc-sidebar-link">
                <i class="ri-dashboard-3-line"></i>
                <span>DASHBOARD</span>
            </a>
        </li>

        <li class="menu-title">Módulos</li>

        <?php foreach ($modulos as $modulo): ?>
            <?php if ($modulo['id'] !== 'dashboard'): ?>
                <?php $esModuloTaller = $modulo['id'] === 'taller'; ?>
                <li class="jvc-sidebar-item">
                    <?php if (!empty($modulo['submodulos'])): ?>
                        <!-- Módulo con submenús -->
                        <a href="#" class="jvc-sidebar-link jvc-sidebar-dropdown-toggle">
                            <i class="<?= htmlspecialchars($modulo['icono']) ?>"></i>
                            <span><?= htmlspecialchars($modulo['nombre']) ?></span>
                            <?php if ($esModuloTaller): ?>
                                <span class="notification-container">
                                    <i class="ri-notification-3-line notification-icon"></i>
                                    <span class="notification-badge" id="tallerNotificaciones" hidden style="display: none;">0</span>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="jvc-sidebar-dropdown" data-title="<?= htmlspecialchars($modulo['nombre']) ?>">
                            <?php foreach ($modulo['submodulos'] as $submodulo): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($submodulo['ruta']) ?>"
                                        class="jvc-sidebar-link jvc-sidebar-dropdown-item">
                                        <?= htmlspecialchars($submodulo['nombre']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <!-- Módulo sin submenús -->
                        <a href="<?= htmlspecialchars($modulo['ruta']) ?>" class="jvc-sidebar-link">
                            <i class="<?= htmlspecialchars($modulo['icono']) ?>"></i>
                            <span><?= htmlspecialchars($modulo['nombre']) ?></span>
                            <?php if ($esModuloTaller): ?>
                                <span class="notification-container">
                                    <i class="ri-notification-3-line notification-icon"></i>
                                    <span class="notification-badge" id="tallerNotificaciones" hidden style="display: none;">0</span>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</aside>

<script>
(function () {
    const badge = document.getElementById('tallerNotificaciones');

    if (!badge) {
        return;
    }

    badge.hidden = true;

    if (window.tallerNotificationBadgePoller) {
        window.tallerNotificationBadgePoller.refresh();
        return;
    }

    let consultando = false;

    async function actualizarContadorTaller() {
        if (consultando) {
            return;
        }

        consultando = true;

        try {
            const respuesta = await fetch(_URL + '/ajs/taller/pendientes-cotizacion/contar', {
                method: 'POST',
                headers: {
                    'token-app': localStorage.getItem('_token') || ''
                },
                credentials: 'same-origin'
            });

            if (!respuesta.ok) {
                throw new Error('request_failed');
            }

            const datos = await respuesta.json();
            if (!Number.isInteger(datos.count) || datos.count < 0) {
                throw new Error('invalid_response');
            }

            badge.textContent = datos.count > 99 ? '99+' : String(datos.count);
            badge.hidden = datos.count === 0;
            badge.style.display = datos.count > 0 ? 'flex' : 'none';
            badge.closest('.notification-container').classList.toggle('has-notifications', datos.count > 0);
        } catch (error) {
            badge.hidden = true;
            badge.style.display = 'none';
            badge.closest('.notification-container').classList.remove('has-notifications');
            console.error('No se pudo actualizar el contador de Taller.');
        } finally {
            consultando = false;
        }
    }

    window.tallerNotificationBadgePoller = {
        refresh: actualizarContadorTaller,
        intervalId: setInterval(actualizarContadorTaller, 30000)
    };

    actualizarContadorTaller();
})();
</script>

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
