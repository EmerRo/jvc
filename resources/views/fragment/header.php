<!-- resources\views\fragment\header.php -->
<header class="jvc-header">
  <nav class="jvc-navbar">
    <div class="jvc-nav-left">
      <button class="jvc-menu-toggle">
        <i data-lucide="menu"></i>
      </button>
    </div>
    
    <div class="jvc-nav-right">
      <button class="jvc-nav-btn jvc-fullscreen-btn" title="Pantalla completa">
        <i data-lucide="maximize"></i>
      </button>
      <!-- Información del usuario -->
      <span class="jvc-user-info">
        <?php
        if(isset($_SESSION['id_rol'])) {
            // Obtener el nombre del usuario
            $nombreCompleto = '';
            
            if(isset($_SESSION['nombres']) && !empty($_SESSION['nombres'])) {
                $nombreCompleto = $_SESSION['nombres'];
                
                // Añadir apellidos si existen
                if(isset($_SESSION['apellidos']) && !empty($_SESSION['apellidos'])) {
                    $nombreCompleto .= ' ' . $_SESSION['apellidos'];
                }
            } else {
                $nombreCompleto = 'Usuario';
            }
            
            // Obtener el rol desde la base de datos
            $rolNombre = isset($_SESSION['rol_nombre']) ? $_SESSION['rol_nombre'] : 'Usuario';
            
            echo '<span class="nombre-usuario">' . $nombreCompleto . '</span> | <span class="role">' . $rolNombre . '</span>';
        } else {
            echo '<span class="nombre-usuario">Usuario</span> | <span class="role">INVITADO</span>';
        }
        ?>
      </span>
      <div class="jvc-dropdown jvc-user-dropdown">
        <button class="jvc-nav-btn jvc-user-btn">
          <i data-lucide="settings"></i>
        </button>
        <div class="jvc-dropdown-content">
          <a href="#" class="jvc-dropdown-item" id="light-mode-switch">
            <i data-lucide="sun"></i>
            Modo claro
          </a>
          <a href="#" class="jvc-dropdown-item" id="dark-mode-switch">
            <i data-lucide="moon"></i>
            Modo oscuro
          </a>
          <div class="dropdown-divider"></div>
          <a id="logout" href="<?= URL::to('/logout') ?>" class="jvc-dropdown-item">
            <i data-lucide="log-out"></i>
            Cerrar sesión
          </a>
        </div>
      </div>
    </div>
  </nav>
</header>

<!-- Incluir Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>

<script src="<?=URL::to('public/assets/js/menu.js')?>"></script>