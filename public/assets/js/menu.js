// public\assets\js\menu.js
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".jvc-sidebar")
    const mainContent = document.querySelector(".main-content")
    const menuToggle = document.querySelector(".jvc-menu-toggle")
    const dropdownToggles = document.querySelectorAll(".jvc-sidebar-dropdown-toggle")
    const userDropdown = document.querySelector(".jvc-user-dropdown")
    const header = document.querySelector('.jvc-header')
    const body = document.body
  
    // Agregar títulos para tooltips
    document.querySelectorAll(".jvc-sidebar-item").forEach((item) => {
      const link = item.querySelector(".jvc-sidebar-link")
      const span = link?.querySelector("span")
      if (link && span) {
        const text = span.textContent
        item.setAttribute("data-title", text)
      }
  
      // Manejar posición del dropdown
      item.addEventListener("mouseenter", (e) => {
        if (sidebar.classList.contains("collapsed")) {
          const dropdown = item.querySelector(".jvc-sidebar-dropdown")
          if (dropdown) {
            const rect = item.getBoundingClientRect()
            dropdown.style.top = `${rect.top}px`
          }
        }
      })
    })
  
    // Toggle Sidebar
    menuToggle.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        if (window.innerWidth <= 1024) {
            // Comportamiento móvil
            document.body.classList.toggle("sidebar-open");
            sidebar.classList.toggle("show-mobile");
        } else {
            // Comportamiento desktop
            sidebar.classList.toggle("collapsed");
            document.body.classList.toggle("sidebar-collapsed");
        }
    });
  
    // Cerrar sidebar móvil al hacer clic fuera
    document.addEventListener("click", (e) => {
      if (window.innerWidth <= 1024) {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                document.body.classList.remove("sidebar-open");
                sidebar.classList.remove("show-mobile");
            }
        }
    });
  
    // Manejar dropdowns del sidebar
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            const item = toggle.closest(".jvc-sidebar-item");
            
            // Cerrar otros dropdowns
            dropdownToggles.forEach((otherToggle) => {
                const otherItem = otherToggle.closest(".jvc-sidebar-item");
                if (otherItem !== item) {
                    otherItem.classList.remove("active");
                }
            });
            
            item.classList.toggle("active");
        });
    });
  
    // User Dropdown
    const userBtn = document.querySelector(".jvc-user-btn")
    if (userBtn) {
      userBtn.addEventListener("click", (e) => {
        e.stopPropagation()
        userDropdown?.classList.toggle("active")
      })
    }
  
    // Cerrar user dropdown al hacer click fuera
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".jvc-user-dropdown") && !e.target.closest(".jvc-user-btn")) {
        userDropdown?.classList.remove("active")
      }
    })
  
    // Responsive
    window.addEventListener("resize", () => {
        if (window.innerWidth > 1024) {
            document.body.classList.remove("sidebar-open");
            sidebar.classList.remove("show-mobile");
        }
    });

    const documentosPaths = new Set([
        '/documentos',
        '/documentos/informe',
        '/documentos/cartas',
        '/documentos/constancias',
        '/documentos/archivos/internos',
        '/documentos/otros'
    ]);

    const normalizarPath = (value) => {
        if (!value || value === '#') return null;

        try {
            const pathname = new URL(value, window.location.origin).pathname;
            return pathname === '/' ? '/' : pathname.replace(/\/+$/, '');
        } catch (error) {
            return null;
        }
    };

    const obtenerMejorCoincidencia = (links, currentPath, exactPaths = new Set()) => {
        return Array.from(links).reduce((bestMatch, link) => {
            const hrefPath = normalizarPath(link.getAttribute('href'));
            if (!hrefPath) return bestMatch;

            const isExactPath = exactPaths.has(hrefPath);
            const isActive = currentPath === hrefPath
                || (!isExactPath && hrefPath !== '/' && currentPath.startsWith(hrefPath + '/'));

            if (!isActive || (bestMatch && bestMatch.path.length >= hrefPath.length)) {
                return bestMatch;
            }

            return { link, path: hrefPath };
        }, null);
    };

    const recalcularMenuActivo = () => {
        const currentPath = normalizarPath(window.location.href);
        const directLinks = document.querySelectorAll(
            '.jvc-sidebar-link:not(.jvc-sidebar-dropdown-item):not(.jvc-sidebar-dropdown-toggle)'
        );
        const submenuLinks = document.querySelectorAll('.jvc-sidebar-dropdown-item');
        const activeSubmenuParents = new Set(
            Array.from(submenuLinks)
                .filter(link => link.classList.contains('active'))
                .map(link => link.closest('.jvc-sidebar-item'))
                .filter(Boolean)
        );

        directLinks.forEach(link => link.classList.remove('active'));
        submenuLinks.forEach(link => link.classList.remove('active'));
        activeSubmenuParents.forEach(parent => parent.classList.remove('active'));

        const directMatch = obtenerMejorCoincidencia(directLinks, currentPath);
        directMatch?.link.classList.add('active');

        const submenuMatch = obtenerMejorCoincidencia(submenuLinks, currentPath, documentosPaths);
        if (submenuMatch) {
            submenuMatch.link.classList.add('active');
            submenuMatch.link.closest('.jvc-sidebar-item')?.classList.add('active');
        }
    };

    recalcularMenuActivo();
    window.addEventListener('popstate', recalcularMenuActivo);
  })
