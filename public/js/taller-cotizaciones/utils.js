// public\js\taller-cotizaciones\utils.js
// Funciones utilitarias
function toggleInput(checkbox) {
    const input = checkbox.parentElement.querySelector('.precio-input');
    input.disabled = !checkbox.checked;
}

function formatFechaVisual(fecha) {
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES');
}

function formatoDecimal(num, decimales = 2) {
    return parseFloat(num).toFixed(decimales);
}