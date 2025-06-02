<!-- resources\views\fragment\nav-bar.php -->
<?php
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

    // Consulta mejorada para obtener módulos y submódulos
    if ($rol_id == 1) {
        // Para el rol ADMIN, mostrar todos los módulos y submódulos
        $sql = "SELECT 
                    m.*,
                    s.submodulo_id,
                    s.nombre as submodulo_nombre,
                    s.ruta as submodulo_ruta
                FROM modulos m
                LEFT JOIN submodulos s ON m.modulo_id = s.modulo_id
                ORDER BY m.modulo_id, s.nombre";
    } else {
        // Para otros roles, mostrar solo los módulos y submódulos permitidos
        $sql = "SELECT 
                    m.*,
                    s.submodulo_id,
                    s.nombre as submodulo_nombre,
                    s.ruta as submodulo_ruta
                FROM modulos m
                INNER JOIN rol_modulo rm ON m.modulo_id = rm.modulo_id
                LEFT JOIN submodulos s ON m.modulo_id = s.modulo_id
                LEFT JOIN rol_submodulo rs ON s.submodulo_id = rs.submodulo_id
                WHERE rm.rol_id = ? 
                AND (rs.rol_id = ? OR s.submodulo_id IS NULL)
                ORDER BY m.modulo_id, s.nombre";
    }

    if ($rol_id == 1) {
        $result = mysqli_query($conectar, $sql);
    } else {
        $stmt = mysqli_prepare($conectar, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $rol_id, $rol_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if (!$result) {
        throw new Exception("Error en consulta: " . mysqli_error($conectar));
    }

    // Procesar resultados y organizar módulos y submódulos
    $modulos = [];
    $temp_modulo = null;

    while ($row = mysqli_fetch_assoc($result)) {
        if ($temp_modulo === null || $temp_modulo['modulo_id'] !== $row['modulo_id']) {
            if ($temp_modulo !== null) {
                $modulos[] = $temp_modulo;
            }

            $temp_modulo = [
                'modulo_id' => $row['modulo_id'],
                'nombre' => $row['nombre'],
                'ruta' => $row['ruta'],
                'icono' => $row['icono'],
                'submodulos' => []
            ];
        }

        if ($row['submodulo_id'] !== null) {
            $temp_modulo['submodulos'][] = [
                'id' => $row['submodulo_id'],
                'nombre' => $row['submodulo_nombre'],
                'ruta' => $row['submodulo_ruta']
            ];
        }
    }

    if ($temp_modulo !== null) {
        $modulos[] = $temp_modulo;
    }

    // AGREGA EL CÓDIGO DE ORDENAMIENTO AQUÍ, JUSTO DESPUÉS DE PROCESAR LOS RESULTADOS
    // Ordenar específicamente los submódulos del módulo FACTURACIÓN (modulo_id = 2)
foreach ($modulos as &$modulo) {
    if ($modulo['modulo_id'] == 2) { // FACTURACIÓN
        // Orden deseado de los submódulos
        $orden_deseado = [
            'Ventas',
            'Guías Remisión',
            'Notas Electrónicas'
        ];
        
        // Ordenar los submódulos según el orden deseado
        usort($modulo['submodulos'], function($a, $b) use ($orden_deseado) {
            $pos_a = array_search($a['nombre'], $orden_deseado);
            $pos_b = array_search($b['nombre'], $orden_deseado);
            
            // Si no se encuentra en la lista, ponerlo al final
            if ($pos_a === false) $pos_a = 999;
            if ($pos_b === false) $pos_b = 999;
            
            return $pos_a - $pos_b;
        });
    } 
    else if ($modulo['modulo_id'] == 8) { // ALMACÉN
        // Orden deseado de los submódulos para ALMACÉN
        $orden_deseado = [
            'Kardex',
            'Repuestos',
            'Intercambio de productos'
        ];
        
        // Ordenar los submódulos según el orden deseado
        usort($modulo['submodulos'], function($a, $b) use ($orden_deseado) {
            $pos_a = array_search($a['nombre'], $orden_deseado);
            $pos_b = array_search($b['nombre'], $orden_deseado);
            
            // Si no se encuentra en la lista, ponerlo al final
            if ($pos_a === false) $pos_a = 999;
            if ($pos_b === false) $pos_b = 999;
            
            return $pos_a - $pos_b;
        });
    }
    else if ($modulo['nombre'] === 'DOCUMENTOS') { // DOCUMENTOS (por nombre en lugar de ID)
        // Orden deseado de los submódulos para DOCUMENTOS
        $orden_deseado = [
            'Ficha técnica',
            'Informe',
            'Cartas',
            'Constancias',
            'Archivos internos',
            'Otros'
        ];
        
        // Ordenar los submódulos según el orden deseado
        usort($modulo['submodulos'], function($a, $b) use ($orden_deseado) {
            $pos_a = array_search($a['nombre'], $orden_deseado);
            $pos_b = array_search($b['nombre'], $orden_deseado);
            
            // Si no se encuentra en la lista, ponerlo al final
            if ($pos_a === false) $pos_a = 999;
            if ($pos_b === false) $pos_b = 999;
            
            return $pos_a - $pos_b;
        });
    }
}

    // Código de depuración (opcional) - Comentar en producción
    /*
    echo "<pre style='position:fixed;top:0;right:0;background:white;z-index:9999;padding:10px;'>";
    echo "Módulos cargados: " . count($modulos) . "<br>";
    foreach ($modulos as $m) {
        echo "ID: " . $m['modulo_id'] . " - Nombre: " . $m['nombre'] . "<br>";
    }
    echo "</pre>";
    */

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

        <?php 
        // Primero, verificar si DOCUMENTOS está en la lista de módulos
        $documentos_existe = false;
        $modulo_documentos = null;

        foreach ($modulos as $m) {
            if ($m['nombre'] === 'DOCUMENTOS') {
                $documentos_existe = true;
                $modulo_documentos = $m;
                break;
            }
        }

        // Agregar un array para rastrear los módulos ya renderizados
        $modulos_renderizados = [];

        foreach ($modulos as $modulo): 
            // Verificar si este módulo ya ha sido renderizado por nombre
            if ($modulo['nombre'] !== 'DASHBOARD' && !in_array($modulo['nombre'], $modulos_renderizados)): 
                // Agregar el nombre del módulo al array de módulos renderizados
                $modulos_renderizados[] = $modulo['nombre'];
        ?>
            <li class="jvc-sidebar-item">
                <?php if (!empty($modulo['submodulos'])): ?>
                    <!-- Módulo con submenús -->
                    <a href="#" class="jvc-sidebar-link jvc-sidebar-dropdown-toggle">
                        <i class="<?= htmlspecialchars($modulo['icono']) ?>"></i>
                        <span><?= htmlspecialchars($modulo['nombre']) ?></span>
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
                        <?php if ($modulo['nombre'] == 'TALLER'): ?>
                            <div class="notification-container">
                                <i class="ri-notification-3-line notification-icon"></i>
                                <span class="notification-badge" id="tallerNotificaciones">0</span>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endif; endforeach; ?>

       <?php 
// Si DOCUMENTOS existe pero no se mostró, mostrarlo ahora
if ($documentos_existe && !in_array('DOCUMENTOS', $modulos_renderizados)):
    // Aplicar el ordenamiento a los submódulos de DOCUMENTOS antes de renderizarlo
    if (!empty($modulo_documentos['submodulos'])) {
        $orden_deseado = [
            'Ficha técnica',
            'Informe',
            'Cartas',
            'Constancias',
            'Archivos internos',
            'Otros'
        ];
        
        usort($modulo_documentos['submodulos'], function($a, $b) use ($orden_deseado) {
            $pos_a = array_search($a['nombre'], $orden_deseado);
            $pos_b = array_search($b['nombre'], $orden_deseado);
            
            // Si no se encuentra en la lista, ponerlo al final
            if ($pos_a === false) $pos_a = 999;
            if ($pos_b === false) $pos_b = 999;
            
            return $pos_a - $pos_b;
        });
    }
?>
    <li class="jvc-sidebar-item">
        <?php if (!empty($modulo_documentos['submodulos'])): ?>
            <!-- DOCUMENTOS con submenús -->
            <a href="#" class="jvc-sidebar-link jvc-sidebar-dropdown-toggle">
                <i class="<?= htmlspecialchars($modulo_documentos['icono']) ?>"></i>
                <span>DOCUMENTOS</span>
            </a>
            <ul class="jvc-sidebar-dropdown" data-title="DOCUMENTOS">
                <?php foreach ($modulo_documentos['submodulos'] as $submodulo): ?>
                    <li>
                        <a href="<?= htmlspecialchars($submodulo['ruta']) ?>"
                            class="jvc-sidebar-link jvc-sidebar-dropdown-item">
                            <?= htmlspecialchars($submodulo['nombre']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <!-- DOCUMENTOS sin submenús -->
            <a href="<?= htmlspecialchars($modulo_documentos['ruta']) ?>" class="jvc-sidebar-link">
                <i class="<?= htmlspecialchars($modulo_documentos['icono']) ?>"></i>
                <span>DOCUMENTOS</span>
            </a>
        <?php endif; ?>
    </li>
<?php endif; ?>
    </ul>
</aside>

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

<script>
    // Sistema de notificaciones para el taller
    function actualizarNotificacionesTaller() {
        $.ajax({
            url: _URL + "/ajs/prealerta/obtenerNotificaciones",
            type: "GET",
            dataType: "json",
            success: function (response) {
                console.log("Respuesta de notificaciones:", response);
                const badge = $("#tallerNotificaciones");
                const container = $(".notification-container");

                if (response.count && response.count > 0) {
                    badge.text(response.count).show();
                    container.addClass("has-notifications");
                } else {
                    badge.text("0").hide();
                    container.removeClass("has-notifications");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al obtener notificaciones:", error);
            }
        });
    }

    $(document).ready(function () {
        actualizarNotificacionesTaller();

        if (window.location.pathname === __URL + "/taller") {
            $.ajax({
                url: _URL + "/ajs/prealerta/marcarComoVisto",
                type: "POST",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $("#tallerNotificaciones").hide();
                        $(".notification-container").removeClass("has-notifications");
                        actualizarNotificacionesTaller();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error al marcar como visto:", error);
                }
            });
        }
    });

    $(document).on('preAlertaGuardada', function () {
        actualizarNotificacionesTaller();
    });
</script>