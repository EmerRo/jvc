/* public/js/series/cargar-selects.js
 * Nuevo flujo: solo se elige Equipo → Marca y Modelo se autocompletan solos.
 */

var _allMarcas  = [];
var _allModelos = [];
var _allEquipos = [];
var _datosListos = false;

// ─── Carga inicial ────────────────────────────────────────────────────────────
function cargarTodosLosDatos(callback) {
    var p = 3;
    function done() { if (--p === 0) { _datosListos = true; if (callback) callback(); } }
    $.get(_URL + "/ajs/get/marcas",  function(d) { _allMarcas  = typeof d==='string'?JSON.parse(d):d; done(); });
    $.get(_URL + "/ajs/get/modelos", function(d) { _allModelos = typeof d==='string'?JSON.parse(d):d; done(); });
    $.get(_URL + "/ajs/get/equipos", function(d) { _allEquipos = typeof d==='string'?JSON.parse(d):d; done(); });
}

// ─── Opciones para select de equipos ─────────────────────────────────────────
function opcionesEquipos() {
    return '<option value="">Seleccionar equipo...</option>' +
        _allEquipos.map(function(e) {
            var info = [];
            if (e.marca_nombre)  info.push(e.marca_nombre);
            if (e.modelo_nombre) info.push(e.modelo_nombre);
            var label = info.length ? e.nombre + ' — ' + info.join(' / ') : e.nombre;
            return `<option value="${e.id}" data-marca="${e.marca_id||''}" data-modelo="${e.modelo_id||''}" data-marca-nombre="${e.marca_nombre||''}" data-modelo-nombre="${e.modelo_nombre||''}">${label}</option>`;
        }).join('');
}

// ─── Inicializar select de equipo en un equipo-item ───────────────────────────
function inicializarSelectsItem($item, equipoVal) {
    var $sel = $item.find('.select-equipo');
    $sel.html(opcionesEquipos());
    if (equipoVal) {
        $sel.val(equipoVal).trigger('change');
    }
}

// ─── Inicializar select común (máquinas idénticas) ───────────────────────────
function inicializarSelectsComunes() {
    $('#equipo_comun').html(opcionesEquipos());
}

// ─── Inicializar select común edición (máquinas idénticas) ───────────────────
function inicializarSelectsComunesU() {
    $('#equipo_comun_u').html(opcionesEquipos());
}

// ─── Inicializar selects en modales de gestión (sin cambios) ─────────────────
function inicializarSelectsModales() {
    var opsMarcas = '<option value="">-- Marca --</option>' +
        _allMarcas.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
    var opsModelos = '<option value="">-- Modelo --</option>' +
        _allModelos.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');

    $("#modelo_marca_id").html(opsMarcas);
    $("#equipo_marca_id").html(opsMarcas);
    $("#equipo_modelo_id").html(opsModelos);
}

// ─── Funciones legacy (por si algo externo las llama) ────────────────────────
function cargarSelectMarcas()  {}
function cargarSelectModelos() {}
function cargarSelectEquipos() {}

// ─── Autocompletado al elegir equipo en equipo-items ─────────────────────────
$(document).on('change', '.select-equipo', function() {
    var $item    = $(this).closest('.equipo-item');
    var $opt     = $(this).find(':selected');
    var marcaNom = $opt.data('marca-nombre')  || '';
    var modelNom = $opt.data('modelo-nombre') || '';
    var marcaId  = $opt.data('marca')  || '';
    var modelId  = $opt.data('modelo') || '';

    // Campo de display
    var display = '';
    if (marcaNom && modelNom)  display = marcaNom + ' / ' + modelNom;
    else if (marcaNom)         display = marcaNom;
    else if (modelNom)         display = modelNom;

    $item.find('.input-marca-modelo').val(display);
    $item.find('.hidden-marca').val(marcaId);
    $item.find('.hidden-modelo').val(modelId);
});

// ─── Autocompletado al elegir equipo común ────────────────────────────────────
$(document).on('change', '#equipo_comun', function() {
    var $opt     = $(this).find(':selected');
    var marcaNom = $opt.data('marca-nombre')  || '';
    var modelNom = $opt.data('modelo-nombre') || '';
    var marcaId  = $opt.data('marca')  || '';
    var modelId  = $opt.data('modelo') || '';

    var display = '';
    if (marcaNom && modelNom)  display = marcaNom + ' / ' + modelNom;
    else if (marcaNom)         display = marcaNom;
    else if (modelNom)         display = modelNom;

    $('#marca_modelo_comun_display').val(display);
    $('#marca_comun').val(marcaId);
    $('#modelo_comun').val(modelId);
});

// ─── Autocompletado al elegir equipo común (edición) ─────────────────────────
$(document).on('change', '#equipo_comun_u', function() {
    var $opt     = $(this).find(':selected');
    var marcaNom = $opt.data('marca-nombre')  || '';
    var modelNom = $opt.data('modelo-nombre') || '';
    var marcaId  = $opt.data('marca')  || '';
    var modelId  = $opt.data('modelo') || '';

    var display = '';
    if (marcaNom && modelNom)  display = marcaNom + ' / ' + modelNom;
    else if (marcaNom)         display = marcaNom;
    else if (modelNom)         display = modelNom;

    $('#marca_modelo_comun_display_u').val(display);
    $('#marca_comun_u').val(marcaId);
    $('#modelo_comun_u').val(modelId);
});

// ─── Bug 6: regenerar series masivas si cambia la cantidad ───────────────────
$(document).on('change', '#cantidad_equipos', function() {
    var ultimo = parseInt($('#ultimo_numero_serie').val());
    if (!isNaN(ultimo) && typeof generarSeriesMasivas === 'function') {
        generarSeriesMasivas(ultimo);
    }
});

// ─── Cascade en modal Equipo (gestión) ───────────────────────────────────────
$(document).on('change', '#equipo_marca_id', function() {
    var marcaId = $(this).val();
    var lista = marcaId ? _allModelos.filter(m => String(m.marca_id) === String(marcaId)) : _allModelos;
    var ops = '<option value="">-- Modelo --</option>' +
        lista.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
    $('#equipo_modelo_id').html(ops);
});

// ─── Inicialización ───────────────────────────────────────────────────────────
$(document).ready(function() {
    cargarTodosLosDatos(function() {
        // Inicializar items existentes
        $('#equipos_container .equipo-item').each(function() {
            inicializarSelectsItem($(this));
        });
        inicializarSelectsComunes();
        inicializarSelectsComunesU();
        inicializarSelectsModales();
    });
});

// ─── Agregar nuevo equipo-item ────────────────────────────────────────────────
$('#agregar_equipo_diferente').click(function() {
    var idx = $('#equipos_container .equipo-item').length;

    var ultimoNumero;
    if (idx > 0) {
        var lastVal = parseInt($('input[name^="equipos"][name$="[numero_serie]"]').last().val());
        if (!isNaN(lastVal)) {
            ultimoNumero = lastVal;
        } else {
            // AJAX de último número aún pendiente: calcular por posición
            var base = parseInt($('#ultimo_numero_serie').val()) || 0;
            ultimoNumero = base + idx;
        }
    } else {
        ultimoNumero = parseInt($('#ultimo_numero_serie').val()) || 0;
    }
    var siguiente = ultimoNumero + 1;

    $('#equipos_container').append(`
        <div class="equipo-item card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0">Equipo ${idx + 1}</h5>
                    <button type="button" class="btn btn-sm btn-danger btn-eliminar-equipo">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-box me-1 text-rojo"></i>
                            Producto del almacén <small class="text-muted">(opcional)</small>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control input-buscar-producto"
                                name="equipos[${idx}][producto_busqueda]"
                                placeholder="Buscar por código o nombre..." autocomplete="off">
                            <input type="hidden" class="input-id-producto"
                                name="equipos[${idx}][id_producto]" value="">
                        </div>
                        <small class="text-muted producto-seleccionado-info"></small>
                    </div>
                </div>
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Equipo <span class="text-danger">*</span></label>
                        <select class="form-select select-equipo" name="equipos[${idx}][equipo]" required>
                            <option value="">Seleccionar equipo...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted"><i class="fa fa-tag me-1"></i>Marca / Modelo</label>
                        <input type="text" class="form-control input-marca-modelo" readonly
                            placeholder="Se completa al elegir equipo"
                            style="background:#f8f9fa;cursor:default;">
                        <input type="hidden" name="equipos[${idx}][marca]" class="hidden-marca">
                        <input type="hidden" name="equipos[${idx}][modelo]" class="hidden-modelo">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Número de Serie</label>
                        <div class="input-group">
                            <input type="text" class="form-control"
                                name="equipos[${idx}][numero_serie]"
                                placeholder="Número de Serie" value="${siguiente}" required>
                            <button type="button" class="btn btn-generar-serie" title="Generar número de serie">
                                <i class="fa fa-magic"></i>
                            </button>
                        </div>
                        <div class="feedback-container"></div>
                    </div>
                </div>
            </div>
        </div>
    `);

    var $nuevo = $('#equipos_container .equipo-item').last();
    inicializarSelectsItem($nuevo);
    $('#contador_equipos').text(idx + 1);
});
