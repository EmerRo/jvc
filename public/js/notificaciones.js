/**
 * Gestor de Notificaciones en Tiempo Real
 * Sistema de notificaciones para JVC sin frameworks adicionales
 */
class NotificationManager {
    constructor() {
        this.bellIcon = null;
        this.badge = null;
        this.dropdown = null;
        this.list = null;
        this.notifications = [];
        this.isDropdownOpen = false;
        this.eventSource = null;
        this.heartbeatInterval = null;
        this.pollingInterval = null;
        this.isInitialized = false;

        // Configuración
        this.config = {
            pollingInterval: 30000, // 30 segundos backup polling
            heartbeatInterval: 60000, // 1 minuto heartbeat
            maxNotifications: 50,
            toastDuration: 4000
        };

        this.init();
    }

    init() {
        if (this.isInitialized) {
            return;
        }

        try {
            // Verificar si ya existe el HTML del bell
            this.findOrCreateElements();

            // Configurar event listeners
            this.setupEventListeners();

            // Cargar notificaciones iniciales
            this.loadNotifications();

            // Configurar Server-Sent Events
            this.setupSSE();

            // Backup: polling
            this.setupPolling();

            this.isInitialized = true;
            console.log('NotificationManager inicializado correctamente');

        } catch (error) {
            console.error('Error al inicializar NotificationManager:', error);
        }
    }

    findOrCreateElements() {
        // Buscar elementos existentes
        this.bellIcon = document.getElementById('notificationBell');
        this.badge = document.getElementById('notificationBadge');
        this.dropdown = document.getElementById('notificationDropdown');
        this.list = document.getElementById('notificationList');

        console.log('Elementos encontrados:', {
            bellIcon: !!this.bellIcon,
            badge: !!this.badge,
            dropdown: !!this.dropdown,
            list: !!this.list
        });

        // Si no existen, crear la estructura básica
        if (!this.bellIcon) {
            console.log('Creando estructura de notificaciones...');
            this.createNotificationStructure();
        }
    }

    createNotificationStructure() {
        // Buscar el header o navbar donde insertar las notificaciones
        const header = document.querySelector('.main-header') ||
                      document.querySelector('.navbar') ||
                      document.querySelector('header') ||
                      document.body;

        if (!header) {
            console.warn('No se encontró header para insertar notificaciones');
            return;
        }

        // Crear estructura HTML
        const notificationHTML = `
            <div class="notification-bell" id="notificationBell" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <i class="fa fa-bell fa-lg" style="color: #C1272D; cursor: pointer; font-size: 24px;"></i>
                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>

                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h6 class="mb-0">Notificaciones</h6>
                        <button class="btn btn-sm btn-link" id="markAllRead">Marcar todas como leídas</button>
                    </div>
                    <div id="notificationList" class="notification-list">
                        <!-- Las notificaciones se cargan aquí dinámicamente -->
                    </div>
                    <div class="notification-footer">
                        <small class="text-muted">Últimas notificaciones</small>
                    </div>
                </div>
            </div>
        `;

        header.insertAdjacentHTML('beforeend', notificationHTML);

        // Actualizar referencias
        this.bellIcon = document.getElementById('notificationBell');
        this.badge = document.getElementById('notificationBadge');
        this.dropdown = document.getElementById('notificationDropdown');
        this.list = document.getElementById('notificationList');
    }

    setupEventListeners() {
        console.log('Configurando event listeners...');

        if (!this.bellIcon) {
            console.error('No se encontró el icono de campana');
            return;
        }

        // Toggle dropdown
        this.bellIcon.addEventListener('click', (e) => {
            console.log('Click en campana detectado');
            e.stopPropagation();
            e.preventDefault();
            this.toggleDropdown();
        });

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', (e) => {
            if (this.dropdown && !this.dropdown.contains(e.target) && !this.bellIcon.contains(e.target)) {
                this.closeDropdown();
            }
        });

        // Marcar todas como leídas
        const markAllBtn = document.getElementById('markAllRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => {
                console.log('Marcando todas como leídas...');
                this.markAllAsRead();
            });
        }

        // Detectar cuando la ventana se enfoca para recargar notificaciones
        window.addEventListener('focus', () => {
            this.loadNotifications();
        });

        // Cleanup al cerrar la ventana
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });

        console.log('Event listeners configurados correctamente');
    }

    setupSSE() {
        // Temporalmente deshabilitado el SSE para evitar problemas
        console.log('SSE temporalmente deshabilitado, usando solo polling');
        return;

        /*
        if (typeof(EventSource) === "undefined") {
            console.warn('EventSource no soportado, usando solo polling');
            return;
        }

        try {
            this.eventSource = new EventSource(_URL + '/stream/notificaciones');

            this.eventSource.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);

                    if (data.type === 'notifications') {
                        this.updateNotifications(data.data);
                    } else if (data.type === 'heartbeat') {
                        console.debug('SSE heartbeat recibido');
                    }
                } catch (e) {
                    console.error('Error parsing SSE data:', e);
                }
            };

            this.eventSource.onerror = (error) => {
                console.warn('SSE connection error, falling back to polling');
                this.eventSource.close();
                this.eventSource = null;

                // Aumentar frecuencia de polling si SSE falla
                this.setupPolling(15000); // 15 segundos
            };

            this.eventSource.onopen = () => {
                console.log('SSE connection established');
            };

        } catch (error) {
            console.error('Error setting up SSE:', error);
        }
        */
    }

    setupPolling(interval = null) {
        // Limpiar polling anterior
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }

        const pollingTime = interval || this.config.pollingInterval;

        this.pollingInterval = setInterval(() => {
            // Solo hacer polling si SSE no está activo
            if (!this.eventSource || this.eventSource.readyState !== EventSource.OPEN) {
                this.loadNotifications();
            }
        }, pollingTime);
    }

    loadNotifications() {
        console.log('Cargando notificaciones desde:', _URL + '/ajs/notificaciones/no-leidas');

        fetch(_URL + '/ajs/notificaciones/no-leidas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Respuesta recibida:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (Array.isArray(data)) {
                this.updateNotifications(data);
            } else if (data.error) {
                console.error('Error from server:', data.error);
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
    }

    updateNotifications(notifications) {
        // Detectar nuevas notificaciones
        const newNotifications = notifications.filter(newNotif =>
            !this.notifications.some(oldNotif =>
                oldNotif.id_notificacion === newNotif.id_notificacion
            )
        );

        this.notifications = notifications;
        this.updateBadge();
        this.renderNotifications();

        // Mostrar toast para notificaciones muy recientes
        newNotifications.forEach(notification => {
            if (this.isRecentNotification(notification)) {
                this.showNewNotificationToast(notification);
            }
        });
    }

    updateBadge() {
        if (!this.badge) return;

        const count = this.notifications.length;
        if (count > 0) {
            this.badge.textContent = count > 99 ? '99+' : count;
            this.badge.style.display = 'flex';

            // Animación de pulso para llamar la atención
            this.badge.style.animation = 'pulse 1s infinite';
        } else {
            this.badge.style.display = 'none';
        }
    }

    renderNotifications() {
        if (!this.list) return;

        if (this.notifications.length === 0) {
            this.list.innerHTML = `
                <div class="notification-empty">
                    <i class="fa fa-bell-slash fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No hay notificaciones</p>
                </div>
            `;
            return;
        }

        this.list.innerHTML = this.notifications.map(notification => `
            <div class="notification-item ${notification.leida ? 'read' : 'unread'}"
                 data-id="${notification.id_notificacion}"
                 data-tipo="${notification.tipo}"
                 data-registro-id="${notification.registro_id}">
                <div class="notification-content">
                    <div class="notification-icon">
                        <i class="fa fa-${this.getIconByType(notification.tipo)} ${this.getColorByType(notification.tipo)}"></i>
                    </div>
                    <div class="notification-body">
                        <div class="notification-message">${notification.mensaje}</div>
                        <div class="notification-meta">
                            <small class="text-muted">
                                <i class="fa fa-clock me-1"></i>${this.timeAgo(notification.created_at)}
                            </small>
                            <small class="text-muted ms-2">
                                <i class="fa fa-tag me-1"></i>${notification.modulo_origen}
                            </small>
                        </div>
                    </div>
                </div>
                ${!notification.leida ? '<div class="notification-mark-read" title="Marcar como leída"><i class="fa fa-check"></i></div>' : ''}
            </div>
        `).join('');

        // Agregar event listeners a las notificaciones
        this.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (e.target.closest('.notification-mark-read')) {
                    // Solo marcar como leída
                    const id = item.dataset.id;
                    this.markAsRead(id);
                } else {
                    // Marcar como leída y navegar
                    const id = item.dataset.id;
                    const tipo = item.dataset.tipo;
                    const registroId = item.dataset.registroId;

                    this.markAsRead(id);
                    this.handleNotificationClick(tipo, registroId);
                }
            });
        });
    }

    toggleDropdown() {
        console.log('toggleDropdown llamado');

        if (!this.dropdown) {
            console.error('Dropdown no encontrado');
            return;
        }

        this.isDropdownOpen = !this.isDropdownOpen;
        console.log('Dropdown abierto:', this.isDropdownOpen);

        this.dropdown.style.display = this.isDropdownOpen ? 'block' : 'none';
        console.log('Display del dropdown:', this.dropdown.style.display);

        if (this.isDropdownOpen) {
            // Recargar notificaciones al abrir
            console.log('Cargando notificaciones...');
            this.loadNotifications();
        }
    }

    closeDropdown() {
        if (!this.dropdown) return;

        this.isDropdownOpen = false;
        this.dropdown.style.display = 'none';
    }

    markAsRead(id) {
        fetch(_URL + '/ajs/notificaciones/marcar-leida', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_notificacion=${id}`,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remover la notificación de la lista local
                this.notifications = this.notifications.filter(n => n.id_notificacion != id);
                this.updateBadge();
                this.renderNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    markAllAsRead() {
        fetch(_URL + '/ajs/notificaciones/marcar-todas-leidas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.notifications = [];
                this.updateBadge();
                this.renderNotifications();
                this.closeDropdown();
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }

    handleNotificationClick(tipo, registroId) {
        this.closeDropdown();

        // Navegar según el tipo de notificación
        switch (tipo) {
            case 'orden_trabajo':
                this.navigateToTaller();
                break;
            case 'orden_servicio':
                this.navigateToServicios();
                break;
            case 'cotizacion':
                this.navigateToCotizaciones();
                break;
            case 'venta':
                this.navigateToVentas();
                break;
            default:
                console.log('Tipo de notificación no manejado:', tipo);
        }
    }

    navigateToTaller() {
        if (window.location.hash) {
            window.location.hash = '#/taller';
        } else {
            window.location.href = _URL + '/taller';
        }
    }

    navigateToServicios() {
        if (window.location.hash) {
            window.location.hash = '#/servicio/prealerta';
        } else {
            window.location.href = _URL + '/servicio/prealerta';
        }
    }

    navigateToCotizaciones() {
        if (window.location.hash) {
            window.location.hash = '#/cotizaciones';
        } else {
            window.location.href = _URL + '/cotizaciones';
        }
    }

    navigateToVentas() {
        if (window.location.hash) {
            window.location.hash = '#/ventas';
        } else {
            window.location.href = _URL + '/ventas';
        }
    }

    showNewNotificationToast(notification) {
        // Verificar si SweetAlert está disponible
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Nueva notificación',
                text: notification.mensaje,
                showConfirmButton: false,
                timer: this.config.toastDuration,
                timerProgressBar: true,
                customClass: {
                    popup: 'notification-toast'
                }
            });
        } else {
            // Fallback: mostrar notificación nativa del navegador
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('JVC - Nueva notificación', {
                    body: notification.mensaje,
                    icon: _URL + '/public/login/images/logoJVC.png'
                });
            } else {
                console.log('Nueva notificación:', notification.mensaje);
            }
        }
    }

    isRecentNotification(notification) {
        const notificationTime = new Date(notification.created_at);
        const now = new Date();
        const diffMinutes = (now - notificationTime) / (1000 * 60);

        return diffMinutes < 1; // Menos de 1 minuto
    }

    getIconByType(type) {
        const icons = {
            'orden_trabajo': 'wrench',
            'orden_servicio': 'briefcase',
            'cotizacion': 'file-invoice',
            'venta': 'shopping-cart',
            'compra': 'shopping-bag',
            'cliente': 'user',
            'producto': 'box'
        };
        return icons[type] || 'bell';
    }

    getColorByType(type) {
        const colors = {
            'orden_trabajo': 'text-primary',
            'orden_servicio': 'text-info',
            'cotizacion': 'text-warning',
            'venta': 'text-success',
            'compra': 'text-purple',
            'cliente': 'text-secondary',
            'producto': 'text-dark'
        };
        return colors[type] || 'text-muted';
    }

    timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Ahora';
        if (diffMins < 60) return `${diffMins}m`;
        if (diffHours < 24) return `${diffHours}h`;
        if (diffDays < 30) return `${diffDays}d`;
        return date.toLocaleDateString();
    }

    cleanup() {
        if (this.eventSource) {
            this.eventSource.close();
        }
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
        }
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
        }
    }

    // Método público para refrescar notificaciones
    refresh() {
        this.loadNotifications();
    }

    // Método público para obtener el estado
    getStatus() {
        return {
            initialized: this.isInitialized,
            notificationCount: this.notifications.length,
            sseConnected: this.eventSource && this.eventSource.readyState === EventSource.OPEN,
            pollingActive: !!this.pollingInterval
        };
    }
}

// Instancia global
let notificationManager = null;

// Inicializar cuando el DOM esté listo
function initNotifications() {
    if (notificationManager) {
        return notificationManager;
    }

    // Verificar que _URL esté definido
    if (typeof _URL === 'undefined') {
        console.error('_URL no está definido. Las notificaciones no funcionarán.');
        return null;
    }

    notificationManager = new NotificationManager();

    // Hacer disponible globalmente para debugging
    window.notificationManager = notificationManager;

    return notificationManager;
}

// Auto-inicializar de forma más segura
document.addEventListener('DOMContentLoaded', function() {
    try {
        initNotifications();
    } catch (error) {
        console.error('Error inicializando notificaciones:', error);
    }
});

// También inicializar si se carga después
if (typeof window !== 'undefined') {
    window.initNotifications = initNotifications;
}