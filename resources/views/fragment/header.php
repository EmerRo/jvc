<!-- resources\views\fragment\header.php -->
<header class="jvc-header">
  <nav class="jvc-navbar">
    <div class="jvc-nav-left">
      <button class="jvc-menu-toggle">
        <i data-lucide="menu"></i>
      </button>
    </div>
    
    <div class="jvc-nav-right">
      <!-- <button class="jvc-nav-btn jvc-fullscreen-btn" title="Pantalla completa">
        <i data-lucide="maximize"></i>
      </button> -->
      <!-- Información del usuario con foto de perfil -->
      <div class="jvc-user-info">
        <?php
        if(isset($_SESSION['id_rol'])) {
            // Obtener la foto de perfil del usuario desde la sesión
            $fotoPerfilUsuario = URL::to(DEFAULT_USER_AVATAR); // Foto por defecto

            if(isset($_SESSION['foto_perfil']) && !empty($_SESSION['foto_perfil'])) {
                $foto = $_SESSION['foto_perfil'];
                $fotoPerfilUsuario = (strpos($foto, '/') !== false) ? URL::to($foto) : URL::to('img/usuarios/' . $foto);
            }

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

            echo '<img src="' . $fotoPerfilUsuario . '" alt="Foto de perfil" class="jvc-user-avatar">';
            echo '<span class="jvc-user-text">';
            echo '<span class="nombre-usuario">' . $nombreCompleto . '</span> | <span class="role">' . $rolNombre . '</span>';
            echo '</span>';
        } else {
            echo '<img src="' . URL::to(DEFAULT_USER_AVATAR) . '" alt="Foto de perfil" class="jvc-user-avatar">';
            echo '<span class="jvc-user-text">';
            echo '<span class="nombre-usuario">Usuario</span> | <span class="role">INVITADO</span>';
            echo '</span>';
        }
        ?>
      </div>
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

<!-- Estilos para la foto de perfil -->
<style>
  .jvc-user-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .jvc-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: border-color 0.3s ease;
    flex-shrink: 0;
  }

  .jvc-user-avatar:hover {
    border-color: rgba(255, 255, 255, 0.6);
  }

  .jvc-user-text {
    font-size: 14px;
    line-height: 1.3;
    color: inherit;
  }

  .nombre-usuario {
    font-weight: 500;
  }

  .role {
    opacity: 0.8;
  }

  @media (max-width: 768px) {
    .jvc-user-text {
      display: none;
    }
    .jvc-user-avatar {
      width: 34px;
      height: 34px;
    }
    .jvc-user-info {
      gap: 5px;
    }
  }
</style>

<!-- Incluir Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>

<script src="<?=URL::to('public/assets/js/menu.js')?>?v=<?= time() ?>"> </script>