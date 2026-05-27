<div class="page-title-box" style="padding:12px 0;">
    <div class="row align-items-center">
        <div class="col-12">
            <h6 class="page-title text-center">GESTIÓN DE MARCAS, MODELOS Y EQUIPOS</h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <!-- NAV TABS -->
        <ul class="nav nav-tabs nav-bordered mb-3" id="tabEquipos" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-marcas" role="tab">
                    <i class="fa fa-tag me-1"></i> Marcas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-modelos" role="tab">
                    <i class="fa fa-cube me-1"></i> Modelos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-equipos" role="tab">
                    <i class="fa fa-laptop me-1"></i> Equipos
                </a>
            </li>
        </ul>

        <div class="tab-content">

            <!-- ═══════════ TAB MARCAS ═══════════ -->
            <div class="tab-pane fade show active" id="tab-marcas" role="tabpanel">
                <div class="card" style="border-radius:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,.08)">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-tag me-2 text-danger"></i> Marcas</h5>
                        <button class="btn bg-rojo text-white btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarMarca">
                            <i class="fa fa-plus me-1"></i> Nueva Marca
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dtMarcas" class="table table-bordered table-sm table-striped text-center nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════ TAB MODELOS ═══════════ -->
            <div class="tab-pane fade" id="tab-modelos" role="tabpanel">
                <div class="card" style="border-radius:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,.08)">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-cube me-2 text-danger"></i> Modelos</h5>
                        <button class="btn bg-rojo text-white btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarModelo">
                            <i class="fa fa-plus me-1"></i> Nuevo Modelo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dtModelos" class="table table-bordered table-sm table-striped text-center nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Marca</th>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════ TAB EQUIPOS ═══════════ -->
            <div class="tab-pane fade" id="tab-equipos" role="tabpanel">
                <div class="card" style="border-radius:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,.08)">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-laptop me-2 text-danger"></i> Equipos</h5>
                        <button class="btn bg-rojo text-white btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarEquipo">
                            <i class="fa fa-plus me-1"></i> Nuevo Equipo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dtEquipos" class="table table-bordered table-sm table-striped text-center nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /tab-content -->
    </div>
</div>

<!-- ═══════════════════════ MODALES ═══════════════════════ -->

<!-- Modal Agregar Marca -->
<div class="modal fade" id="modalAgregarMarca" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title"><i class="fa fa-tag me-1"></i> Nueva Marca</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" id="pg_marca_nombre" placeholder="Ej: MASTER GOLDS">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn bg-rojo text-white btn-sm" id="pg_btnGuardarMarca"><i class="fa fa-save me-1"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Marca -->
<div class="modal fade" id="modalEditarMarca" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title"><i class="fa fa-edit me-1"></i> Editar Marca</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pg_marca_id_edit">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" id="pg_marca_nombre_edit">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn bg-rojo text-white btn-sm" id="pg_btnActualizarMarca"><i class="fa fa-save me-1"></i> Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Modelo -->
<div class="modal fade" id="modalAgregarModelo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title"><i class="fa fa-cube me-1"></i> Nuevo Modelo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Marca <span class="text-danger">*</span></label>
                    <select class="form-select" id="pg_modelo_marca_id">
                        <option value="">-- Seleccionar marca --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre del modelo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pg_modelo_nombre" placeholder="Ej: AG-06">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn bg-rojo text-white btn-sm" id="pg_btnGuardarModelo"><i class="fa fa-save me-1"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Modelo -->
<div class="modal fade" id="modalEditarModelo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title"><i class="fa fa-edit me-1"></i> Editar Modelo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pg_modelo_id_edit">
                <div class="mb-3">
                    <label class="form-label">Marca</label>
                    <select class="form-select" id="pg_modelo_marca_id_edit">
                        <option value="">-- Seleccionar marca --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="pg_modelo_nombre_edit">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn bg-rojo text-white btn-sm" id="pg_btnActualizarModelo"><i class="fa fa-save me-1"></i> Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Equipo -->
<div class="modal fade" id="modalAgregarEquipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title"><i class="fa fa-laptop me-1"></i> Nuevo Equipo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Marca <span class="text-danger">*</span></label>
                    <select class="form-select" id="pg_equipo_marca_id">
                        <option value="">-- Seleccionar marca --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Modelo <span class="text-danger">*</span></label>
                    <select class="form-select" id="pg_equipo_modelo_id">
                        <option value="">-- Seleccionar modelo --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre del equipo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pg_equipo_nombre" placeholder="Ej: LUSTRADORA">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn bg-rojo text-white btn-sm" id="pg_btnGuardarEquipo"><i class="fa fa-save me-1"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Equipo -->
<div class="modal fade" id="modalEditarEquipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title"><i class="fa fa-edit me-1"></i> Editar Equipo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="pg_equipo_id_edit">
                <div class="mb-3">
                    <label class="form-label">Marca</label>
                    <select class="form-select" id="pg_equipo_marca_id_edit">
                        <option value="">-- Seleccionar marca --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Modelo</label>
                    <select class="form-select" id="pg_equipo_modelo_id_edit">
                        <option value="">-- Seleccionar modelo --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="pg_equipo_nombre_edit">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn bg-rojo text-white btn-sm" id="pg_btnActualizarEquipo"><i class="fa fa-save me-1"></i> Actualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════ SCRIPTS ═══════════════════════ -->
<script>
var _pgMarcas  = [];
var _pgModelos = [];
var _pgEquipos = [];

var dtMarcas, dtModelos, dtEquipos;

var dtLang = {
    sProcessing:"Procesando...", sLengthMenu:"Mostrar _MENU_ registros",
    sZeroRecords:"Sin resultados", sEmptyTable:"Sin datos",
    sInfo:"Mostrando _START_ a _END_ de _TOTAL_", sInfoEmpty:"0 registros",
    sInfoFiltered:"(filtrado de _MAX_)", sSearch:"Buscar:",
    sLoadingRecords:"Cargando...",
    oPaginate:{sFirst:"«",sLast:"»",sNext:"›",sPrevious:"‹"}
};

// ─── Carga de datos ───────────────────────────────────────────────────────────
function pgCargarTodo(cb) {
    var p = 3;
    function done() { if (--p === 0 && cb) cb(); }

    $.get(_URL + "/ajs/get/marcas",  function(d) { _pgMarcas  = typeof d==='string'?JSON.parse(d):d; done(); });
    $.get(_URL + "/ajs/get/modelos", function(d) { _pgModelos = typeof d==='string'?JSON.parse(d):d; done(); });
    $.get(_URL + "/ajs/get/equipos", function(d) { _pgEquipos = typeof d==='string'?JSON.parse(d):d; done(); });
}

// ─── Opciones para selects ────────────────────────────────────────────────────
function pgOpsMarcas(selVal) {
    return '<option value="">-- Marca --</option>' +
        _pgMarcas.map(m=>`<option value="${m.id}"${String(m.id)===String(selVal)?' selected':''}>${m.nombre}</option>`).join('');
}
function pgOpsModelos(marcaId, selVal) {
    var lista = marcaId ? _pgModelos.filter(m=>String(m.marca_id)===String(marcaId)) : _pgModelos;
    return '<option value="">-- Modelo --</option>' +
        lista.map(m=>`<option value="${m.id}"${String(m.id)===String(selVal)?' selected':''}>${m.nombre}</option>`).join('');
}

// ─── Renderizar DataTables ────────────────────────────────────────────────────
function pgRenderMarcas() {
    if ($.fn.DataTable.isDataTable('#dtMarcas')) dtMarcas.destroy();
    dtMarcas = $('#dtMarcas').DataTable({
        data: _pgMarcas,
        language: dtLang,
        columns: [
            { data: null, render: (d,t,r,m) => m.row+1, width:'40px' },
            { data: 'nombre' },
            { data: null, width:'90px', render: (d,t,r) => `
                <button class="btn btn-sm btn-warning pg-edit-marca" data-id="${r.id}" title="Editar"><i class="fa fa-edit"></i></button>
                <button class="btn btn-sm btn-danger pg-del-marca"  data-id="${r.id}" title="Eliminar"><i class="fa fa-trash"></i></button>
            `}
        ]
    });
}

function pgRenderModelos() {
    if ($.fn.DataTable.isDataTable('#dtModelos')) dtModelos.destroy();
    dtModelos = $('#dtModelos').DataTable({
        data: _pgModelos,
        language: dtLang,
        columns: [
            { data: null, render: (d,t,r,m) => m.row+1, width:'40px' },
            { data: 'marca_nombre', defaultContent:'<span class="text-muted">—</span>' },
            { data: 'nombre' },
            { data: null, width:'90px', render: (d,t,r) => `
                <button class="btn btn-sm btn-warning pg-edit-modelo" data-id="${r.id}" title="Editar"><i class="fa fa-edit"></i></button>
                <button class="btn btn-sm btn-danger pg-del-modelo"  data-id="${r.id}" title="Eliminar"><i class="fa fa-trash"></i></button>
            `}
        ]
    });
}

function pgRenderEquipos() {
    if ($.fn.DataTable.isDataTable('#dtEquipos')) dtEquipos.destroy();
    dtEquipos = $('#dtEquipos').DataTable({
        data: _pgEquipos,
        language: dtLang,
        columns: [
            { data: null, render: (d,t,r,m) => m.row+1, width:'40px' },
            { data: 'marca_nombre',  defaultContent:'<span class="text-muted">—</span>' },
            { data: 'modelo_nombre', defaultContent:'<span class="text-muted">—</span>' },
            { data: 'nombre' },
            { data: null, width:'90px', render: (d,t,r) => `
                <button class="btn btn-sm btn-warning pg-edit-equipo" data-id="${r.id}" title="Editar"><i class="fa fa-edit"></i></button>
                <button class="btn btn-sm btn-danger pg-del-equipo"  data-id="${r.id}" title="Eliminar"><i class="fa fa-trash"></i></button>
            `}
        ]
    });
}

function pgRecargar(cb) {
    pgCargarTodo(function() {
        pgRenderMarcas();
        pgRenderModelos();
        pgRenderEquipos();
        if (cb) cb();
    });
}

// ─── Helpers UI ──────────────────────────────────────────────────────────────
function pgOk(msg)  { Swal.fire({ icon:'success', title:'Éxito',  text:msg, confirmButtonColor:'#ca3438' }); }
function pgErr(msg) { Swal.fire({ icon:'error',   title:'Error',  text:msg, confirmButtonColor:'#ca3438' }); }
function pgConfirm(msg) {
    return Swal.fire({ icon:'warning', title:'¿Eliminar?', text:msg,
        showCancelButton:true, confirmButtonColor:'#ca3438', cancelButtonColor:'#6c757d',
        confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar' });
}

$(document).ready(function () {

    // Carga inicial
    pgRecargar();

    // ── Cascade selects en modales ──────────────────────────────────────────

    // Agregar modelo: marca cambia → recargar modelos (solo informativo, modelo no tiene sub-select)
    $('#modalAgregarModelo').on('show.bs.modal', function() {
        $('#pg_modelo_marca_id').html(pgOpsMarcas(''));
        $('#pg_modelo_nombre').val('');
    });
    $('#modalAgregarEquipo').on('show.bs.modal', function() {
        $('#pg_equipo_marca_id').html(pgOpsMarcas(''));
        $('#pg_equipo_modelo_id').html(pgOpsModelos('',''));
        $('#pg_equipo_nombre').val('');
    });

    $(document).on('change', '#pg_equipo_marca_id', function() {
        $('#pg_equipo_modelo_id').html(pgOpsModelos($(this).val(),''));
    });
    $(document).on('change', '#pg_equipo_marca_id_edit', function() {
        $('#pg_equipo_modelo_id_edit').html(pgOpsModelos($(this).val(),''));
    });

    // ── MARCAS CRUD ─────────────────────────────────────────────────────────

    $('#pg_btnGuardarMarca').click(function() {
        var nombre = $('#pg_marca_nombre').val().trim();
        if (!nombre) { pgErr('Ingresá un nombre'); return; }
        $.post(_URL + "/ajs/save/marcas", { nombre })
            .done(function() { $('#modalAgregarMarca').modal('hide'); pgRecargar(); pgOk('Marca guardada'); })
            .fail(function() { pgErr('No se pudo guardar'); });
    });

    $(document).on('click', '.pg-edit-marca', function() {
        var id = $(this).data('id');
        var m  = _pgMarcas.find(x=>String(x.id)===String(id));
        if (!m) return;
        $('#pg_marca_id_edit').val(m.id);
        $('#pg_marca_nombre_edit').val(m.nombre);
        $('#modalEditarMarca').modal('show');
    });

    $('#pg_btnActualizarMarca').click(function() {
        var id     = $('#pg_marca_id_edit').val();
        var nombre = $('#pg_marca_nombre_edit').val().trim();
        if (!nombre) { pgErr('Ingresá un nombre'); return; }
        $.post(_URL + "/ajs/update/marcas", { id, nombre })
            .done(function() { $('#modalEditarMarca').modal('hide'); pgRecargar(); pgOk('Marca actualizada'); })
            .fail(function() { pgErr('No se pudo actualizar'); });
    });

    $(document).on('click', '.pg-del-marca', function() {
        var id = $(this).data('id');
        pgConfirm('Se eliminará la marca y puede afectar modelos/equipos asociados').then(r => {
            if (!r.isConfirmed) return;
            $.post(_URL + "/ajs/delete/marcas", { id })
                .done(function() { pgRecargar(); pgOk('Marca eliminada'); })
                .fail(function() { pgErr('No se pudo eliminar'); });
        });
    });

    // ── MODELOS CRUD ────────────────────────────────────────────────────────

    $('#pg_btnGuardarModelo').click(function() {
        var marca_id = $('#pg_modelo_marca_id').val();
        var nombre   = $('#pg_modelo_nombre').val().trim();
        if (!nombre) { pgErr('Ingresá un nombre'); return; }
        $.post(_URL + "/ajs/save/modelos", { nombre, marca_id })
            .done(function() { $('#modalAgregarModelo').modal('hide'); pgRecargar(); pgOk('Modelo guardado'); })
            .fail(function() { pgErr('No se pudo guardar'); });
    });

    $(document).on('click', '.pg-edit-modelo', function() {
        var id = $(this).data('id');
        var m  = _pgModelos.find(x=>String(x.id)===String(id));
        if (!m) return;
        $('#pg_modelo_id_edit').val(m.id);
        $('#pg_modelo_marca_id_edit').html(pgOpsMarcas(m.marca_id));
        $('#pg_modelo_nombre_edit').val(m.nombre);
        $('#modalEditarModelo').modal('show');
    });

    $('#pg_btnActualizarModelo').click(function() {
        var id       = $('#pg_modelo_id_edit').val();
        var marca_id = $('#pg_modelo_marca_id_edit').val();
        var nombre   = $('#pg_modelo_nombre_edit').val().trim();
        if (!nombre) { pgErr('Ingresá un nombre'); return; }
        $.post(_URL + "/ajs/update/modelos", { id, nombre, marca_id })
            .done(function() { $('#modalEditarModelo').modal('hide'); pgRecargar(); pgOk('Modelo actualizado'); })
            .fail(function() { pgErr('No se pudo actualizar'); });
    });

    $(document).on('click', '.pg-del-modelo', function() {
        var id = $(this).data('id');
        pgConfirm('Se eliminará el modelo').then(r => {
            if (!r.isConfirmed) return;
            $.post(_URL + "/ajs/delete/modelos", { id })
                .done(function() { pgRecargar(); pgOk('Modelo eliminado'); })
                .fail(function() { pgErr('No se pudo eliminar'); });
        });
    });

    // ── EQUIPOS CRUD ────────────────────────────────────────────────────────

    $('#pg_btnGuardarEquipo').click(function() {
        var marca_id  = $('#pg_equipo_marca_id').val();
        var modelo_id = $('#pg_equipo_modelo_id').val();
        var nombre    = $('#pg_equipo_nombre').val().trim();
        if (!nombre) { pgErr('Ingresá un nombre'); return; }
        $.post(_URL + "/ajs/save/equipos", { nombre, marca_id, modelo_id })
            .done(function() { $('#modalAgregarEquipo').modal('hide'); pgRecargar(); pgOk('Equipo guardado'); })
            .fail(function() { pgErr('No se pudo guardar'); });
    });

    $(document).on('click', '.pg-edit-equipo', function() {
        var id = $(this).data('id');
        var e  = _pgEquipos.find(x=>String(x.id)===String(id));
        if (!e) return;
        $('#pg_equipo_id_edit').val(e.id);
        $('#pg_equipo_marca_id_edit').html(pgOpsMarcas(e.marca_id));
        $('#pg_equipo_modelo_id_edit').html(pgOpsModelos(e.marca_id, e.modelo_id));
        $('#pg_equipo_nombre_edit').val(e.nombre);
        $('#modalEditarEquipo').modal('show');
    });

    $('#pg_btnActualizarEquipo').click(function() {
        var id        = $('#pg_equipo_id_edit').val();
        var marca_id  = $('#pg_equipo_marca_id_edit').val();
        var modelo_id = $('#pg_equipo_modelo_id_edit').val();
        var nombre    = $('#pg_equipo_nombre_edit').val().trim();
        if (!nombre) { pgErr('Ingresá un nombre'); return; }
        $.post(_URL + "/ajs/update/equipos", { id, nombre, marca_id, modelo_id })
            .done(function() { $('#modalEditarEquipo').modal('hide'); pgRecargar(); pgOk('Equipo actualizado'); })
            .fail(function() { pgErr('No se pudo actualizar'); });
    });

    $(document).on('click', '.pg-del-equipo', function() {
        var id = $(this).data('id');
        pgConfirm('Se eliminará el equipo').then(r => {
            if (!r.isConfirmed) return;
            $.post(_URL + "/ajs/delete/equipos", { id })
                .done(function() { pgRecargar(); pgOk('Equipo eliminado'); })
                .fail(function() { pgErr('No se pudo eliminar'); });
        });
    });

});
</script>
