<?php

class NavbarController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function getNavbarHtml()
    {
        if (!isset($_SESSION['usuario_fac']) || !isset($_SESSION['rol'])) {
            return json_encode(['error' => 'No autorizado']);
        }

        $rol_id = $_SESSION['rol'];
        
        try {
            // Obtener módulos con permisos
            if ($rol_id == 1) {
                // ADMIN - todos los módulos
                $sql = "SELECT DISTINCT m.modulo_id, m.nombre, m.ruta, m.icono
                        FROM modulos m 
                        WHERE m.modulo_id != 1 
                        ORDER BY m.modulo_id";
                $result = mysqli_query($this->conectar, $sql);
            } else {
                // OTROS ROLES - solo con permisos
                $sql = "SELECT DISTINCT m.modulo_id, m.nombre, m.ruta, m.icono
                        FROM modulos m 
                        INNER JOIN rol_modulo rm ON m.modulo_id = rm.modulo_id 
                        WHERE rm.rol_id = ? AND m.modulo_id != 1 
                        ORDER BY m.modulo_id";
                $stmt = mysqli_prepare($this->conectar, $sql);
                mysqli_stmt_bind_param($stmt, "i", $rol_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            }

            $html = '';
            while ($modulo = mysqli_fetch_assoc($result)) {
                // Obtener submódulos
                if ($rol_id == 1) {
                    $sql_sub = "SELECT submodulo_id, nombre, ruta 
                                FROM submodulos 
                                WHERE modulo_id = ? 
                                ORDER BY nombre";
                    $stmt_sub = mysqli_prepare($this->conectar, $sql_sub);
                    mysqli_stmt_bind_param($stmt_sub, "i", $modulo['modulo_id']);
                } else {
                    $sql_sub = "SELECT DISTINCT s.submodulo_id, s.nombre, s.ruta 
                                FROM submodulos s 
                                INNER JOIN rol_submodulo rs ON s.submodulo_id = rs.submodulo_id 
                                WHERE s.modulo_id = ? AND rs.rol_id = ? 
                                ORDER BY s.nombre";
                    $stmt_sub = mysqli_prepare($this->conectar, $sql_sub);
                    mysqli_stmt_bind_param($stmt_sub, "ii", $modulo['modulo_id'], $rol_id);
                }
                
                mysqli_stmt_execute($stmt_sub);
                $result_sub = mysqli_stmt_get_result($stmt_sub);
                
                $submodulos = [];
                while ($sub = mysqli_fetch_assoc($result_sub)) {
                    $submodulos[] = $sub;
                }

                // Generar HTML
                $html .= '<li class="jvc-sidebar-item">';
                
                if (!empty($submodulos)) {
                    // Con submenús
                    $html .= '<a href="#" class="jvc-sidebar-link jvc-sidebar-dropdown-toggle">';
                    $html .= '<i class="' . htmlspecialchars($modulo['icono']) . '"></i>';
                    $html .= '<span>' . htmlspecialchars($modulo['nombre']) . '</span>';
                    $html .= '</a>';
                    $html .= '<ul class="jvc-sidebar-dropdown">';
                    
                    foreach ($submodulos as $sub) {
                        $html .= '<li>';
                        $html .= '<a href="' . htmlspecialchars($sub['ruta']) . '" class="jvc-sidebar-link jvc-sidebar-dropdown-item">';
                        $html .= htmlspecialchars($sub['nombre']);
                        $html .= '</a>';
                        $html .= '</li>';
                    }
                    
                    $html .= '</ul>';
                } else {
                    // Sin submenús
                    $html .= '<a href="' . htmlspecialchars($modulo['ruta']) . '" class="jvc-sidebar-link">';
                    $html .= '<i class="' . htmlspecialchars($modulo['icono']) . '"></i>';
                    $html .= '<span>' . htmlspecialchars($modulo['nombre']) . '</span>';
                    $html .= '</a>';
                }
                
                $html .= '</li>';
            }

            return json_encode(['html' => $html, 'count' => mysqli_num_rows($result)]);

        } catch (Exception $e) {
            error_log("Error en navbar: " . $e->getMessage());
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}
