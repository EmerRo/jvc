function abreviaturaMes(mes) {
    const meses = ["ENR", "FEBR", "MZO", "ABR", "MYO", "JUN", "JUL", "AGTO", "SPT", "OCT", "NOV", "DIC"];
    return meses[mes];
}

function abreviaturaMesIngles(mes) {
    const meses = ["JAN", "FEB", "MAR", "APR", "MAY", "JUNE", "JULY", "AUG", "SEPT", "OCT", "NOV", "DEC"];
    return meses[mes];
}

function getdiasSemanas(idioma) {
    if (idioma == 'es') {
        return ['Dom', 'Lun', 'Mar', 'Mir', 'Jue', 'Vie', 'Sab'];
    } else {
        return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fir', 'Sat'];
    }
}

function formatFechaVisual(fecha) {
    let d = new Date(fecha);
    return abreviaturaMes(d.getMonth()) + " " + (d.getDate() + 1) + " del," + d.getFullYear();
}

function getMesAbreLinst(idioma) {
    if (idioma == 'es') {
        return ["En", "Febr", "Mzo", "Abr", "My", "Jun", "Jul", "Agto", "Spt", "Oct", "Nov", "Dic"];
    } else {
        return ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    }
}

function renombrarURL(url, titulo = '', data = null) {
    const newUrl = _URL + url;
    window.history.pushState(data, titulo, newUrl);
}

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function getPathURL() {
    return location.pathname + location.search;
}

function _ajaxDOM(url, contenedor_id, callback) {
    $.ajax({
        headers: {
            'token-app': localStorage.getItem("_token"),
        },
        type: 'POST',
        url: url,
        success: function (resp) {
            $("#loader-menor").hide();
            $("#" + contenedor_id).html(resp);

            // Ejecuta el callback si se pasa
            if (typeof callback === 'function') {
                callback();
            }

            // CALLBACK PARA DESPUÉS DE CARGAR CONTENIDO
            if (typeof window.onContentLoaded === 'function') {
                window.onContentLoaded();
            }

            // INICIALIZAR MÓDULOS ESPECÍFICOS DESPUÉS DE CARGAR CONTENIDO
            setTimeout(function() {
                inicializarModuloSegunURL(url);
            }, 100);
        },
        error: function (xhr, status, error) {
            $("#loader-menor").hide();
            console.error('Error al cargar contenido:', error);
            alertError('Error', 'No se pudo cargar el contenido. Intente nuevamente.');
        }
    });
}

// FUNCIÓN PARA INICIALIZAR MÓDULOS SEGÚN LA URL
function inicializarModuloSegunURL(url) {
    console.log('Inicializando módulo para URL:', url);

    // Limpiar módulos de documentos antes de inicializar uno nuevo
    limpiarModulosDocumentos();

    // Detectar si es el módulo de informes
    if (url.includes('/documentos/informe') || url.includes('informe')) {
        console.log('Detectado módulo de informes, inicializando...');
        if (window.InformesModule && typeof window.InformesModule.init === 'function') {
            // Forzar reinicialización completa
            window.InformesModule.reiniciar();
        }
    }

    // Detectar si es el módulo de cartas
    else if (url.includes('/documentos/cartas') || url.includes('cartas')) {
        console.log('Detectado módulo de cartas, inicializando...');
        // Limpiar configuración anterior para evitar redeclaración
        if (window.cartasModuleConfig) {
            delete window.cartasModuleConfig;
        }

        // Esperar a que el DOM esté completamente cargado
        setTimeout(() => {
            if (typeof window.inicializarModuloCartas === 'function') {
                window.inicializarModuloCartas();
            }
        }, 300);
    }

    // Detectar si es el módulo de constancias
    else if (url.includes('/documentos/constancias') || url.includes('constancias')) {
        console.log('Detectado módulo de constancias, inicializando...');
        // Limpiar configuración anterior para evitar redeclaración
        if (window.constanciasModuleConfig) {
            delete window.constanciasModuleConfig;
        }

        // Esperar a que el DOM esté completamente cargado
        setTimeout(() => {
            if (typeof window.inicializarModuloConstancias === 'function') {
                window.inicializarModuloConstancias();
            }
        }, 300);
    }
}

// FUNCIÓN PARA LIMPIAR MÓDULOS DE DOCUMENTOS
function limpiarModulosDocumentos() {
    console.log('Limpiando módulos de documentos...');

    // Limpiar módulo de cartas
    if (window.cartaModuleInstance) {
        if (typeof window.cartaModuleInstance.cleanup === 'function') {
            window.cartaModuleInstance.cleanup();
        }
        window.cartaModuleInstance = null;
    }

    // Limpiar módulo de constancias
    if (window.constanciaModuleInstance) {
        if (typeof window.constanciaModuleInstance.cleanup === 'function') {
            window.constanciaModuleInstance.cleanup();
        }
        window.constanciaModuleInstance = null;
    }

    // Limpiar módulo de informes
    if (window.InformesModule && typeof window.InformesModule.cleanup === 'function') {
        window.InformesModule.cleanup();
    }

    // CORRECCIÓN: Limpiar filtro de taller cuando se cambia de módulo
    if (typeof window.limpiarFiltroTaller === 'function') {
        window.limpiarFiltroTaller();
    }
}

function alertError(title, msg) {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: msg,
    });
}

function alertExito(title, msg) {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: msg,
    });
}

function alertAdvertencia(title, msg) {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: msg,
    });
}

function alertInfo(title, msg) {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: msg,
    });
}

function getTime() {
    var d = new Date();
    d.setHours(d.getHours()); // offset from local time
    var h = (d.getHours() % 12) || 12; // show midnight & noon as 12
    return (
        (h < 10 ? '0' : '') + h +
        (d.getMinutes() < 10 ? ':0' : ':') + d.getMinutes() +
        // optional seconds display
        // ( d.getSeconds() < 10 ? ':0' : ':') + d.getSeconds() +
        (d.getHours() < 12 ? ' AM' : ' PM')
    );
}

function _ajax(url, method, data = {}, func) {
    $.ajax({
        type: method,
        url: _URL + url,
        headers: {
            'token-app': localStorage.getItem("_token"),
        },
        data: data,
        success: function (resp) {
            $("#loader-menor").hide();
            if (isJson(resp)) {
                func(JSON.parse(resp));
            } else {
                console.log(resp);
                alertError('ERR', 'Error en el servidor');
            }
        }
    });
}

function downloadFile(filePath) {
    var link = document.createElement('a');
    link.href = filePath;
    link.download = filePath.substr(filePath.lastIndexOf('/') + 1);
    link.click();
}

function _post(url, data = {}, func) {
    _ajax(url, "POST", data, func);
}

function _get(url, func) {
    _ajax(url, "GET", {}, func);
}

function reloadPagDon() {
    // Función placeholder
}

// FUNCIÓN GLOBAL PARA LIMPIAR TODOS LOS MÓDULOS
function limpiarTodosLosModulos() {
    // Limpiar módulo de informes
    if (window.InformesModule && typeof window.InformesModule.cleanup === 'function') {
        try {
            window.InformesModule.cleanup();
        } catch (e) {
            console.error('Error al limpiar módulo de informes:', e);
        }
    }

    // Limpiar módulos de documentos
    limpiarModulosDocumentos();

    // Limpiar eventos jQuery
    $(document).off('.informes');
    $(document).off('.documentos');
    $(document).off('.cartas');
    $(document).off('.constancias');

    console.log('Todos los módulos limpiados');
}