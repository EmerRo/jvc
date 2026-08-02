<div class="card-header" style="background: #fff; border-bottom: none; padding-bottom: 0;">
    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
        <!-- Móvil: Dropdown Opciones -->
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <div class="table-view-dropdown ms-2">
                <div class="dropdown">
                    <button class="btn bg-rojo text-white dropdown-toggle" type="button" id="dropdownOpcionesRepuestos" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-cog me-1"></i> Opciones
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownOpcionesRepuestos" style="min-width: 250px;">
                        <li><h6 class="dropdown-header">Reportes</h6></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="descarFunccc()"><i class="fa fa-file-excel me-2"></i> Descargar Excel</a></li>
                        <li><a class="dropdown-item" href="<?= URL::to('/reporte/historial-stock-repuestos/excel') ?>" target="_blank"><i class="fa fa-chart-line me-2"></i> Reporte Movimientos</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#importarModal"><i class="fa fa-file-excel me-2"></i> Importar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Gestión de Stock</h6></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-aumentar-stock"><i class="fa fa-plus me-2"></i> Aumentar Stock</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-disminuir-stock-repuesto"><i class="fa fa-minus me-2"></i> Disminuir Stock</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-traslado-almacenes-repuesto"><i class="fa fa-exchange-alt me-2"></i> Traslado Almacenes</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modal-historial-stock-repuesto"><i class="fa fa-history me-2"></i> Historial Stock</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Configuración</h6></li>
                        <li><a class="dropdown-item" href="/unidades/repuestos"><i class="fa fa-ruler me-2"></i> Unidades</a></li>
                        <li><a class="dropdown-item" href="/categorias/repuestos"><i class="fa fa-folder me-2"></i> Categorías</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" id="add-rep-dropdown"><i class="fa fa-plus me-2"></i> Agregar Repuesto</a></li>
                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" class="btnBorrar-dropdown"><i class="fa fa-trash me-2"></i> Borrar</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Desktop: Botones individuales -->
        <div class="grid-view-buttons d-none d-lg-flex flex-wrap justify-content-end gap-2">
            <button onclick="descarFunccc()" class="btn bg-white text-rojo border-rojo btn-sm"><i class="fa fa-file-excel"></i> <span class="d-none d-lg-inline">Descargar Excel</span></button>
            <a href="<?= URL::to('/reporte/historial-stock-repuestos/excel') ?>" target="_blank" class="btn bg-white text-rojo border-rojo btn-sm"><i class="fa fa-chart-line"></i> <span class="d-none d-lg-inline">Reporte Movimientos</span></a>
            <button data-bs-toggle="modal" data-bs-target="#importarModal" class="btn bg-white text-rojo border-rojo btn-sm"><i class="fa fa-file-excel"></i> <span class="d-none d-lg-inline">Importar</span></button>
            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal" data-bs-target="#modal-aumentar-stock"><i class="fa fa-plus"></i> <span class="d-none d-lg-inline">Aumentar Stock</span></button>
            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal" data-bs-target="#modal-disminuir-stock-repuesto"><i class="fa fa-minus"></i> <span class="d-none d-lg-inline">Disminuir Stock</span></button>
            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal" data-bs-target="#modal-traslado-almacenes-repuesto"><i class="fa fa-exchange-alt"></i> <span class="d-none d-lg-inline">Traslado Almacenes</span></button>
            <button class="btn border-rojo bg-white btn-sm" data-bs-toggle="modal" data-bs-target="#modal-historial-stock-repuesto"><i class="fa fa-history"></i> <span class="d-none d-lg-inline">Historial Stock</span></button>
            <a href="/unidades/repuestos" class="btn bg-white text-rojo border-rojo button-link btn-sm"><i class="fa fa-ruler"></i> <span class="d-none d-lg-inline">Unidades</span></a>
            <a href="/categorias/repuestos" class="btn bg-white text-rojo border-rojo button-link btn-sm"><i class="fa fa-folder"></i> <span class="d-none d-lg-inline">Categorías</span></a>
            <button class="btn bg-rojo text-white bordes btn-sm" id="add-rep"><i class="fa fa-plus"></i> <span class="d-none d-lg-inline">Agregar Repuesto</span></button>
            <button class="btn bg-rojo btnBorrar btn-sm"><i class="fa fa-trash"></i> <span class="d-none d-lg-inline">Borrar</span></button>
            <button id="btn-table-view" class="btn border-rojo bg-rojo text-white btn-sm active"><i class="fa fa-table me-1"></i> <span class="d-none d-lg-inline">Vista Tabla</span></button>
            <button id="btn-grid-view" class="btn border-rojo bg-white text-rojo btn-sm"><i class="fa fa-th-large me-1"></i> <span class="d-none d-lg-inline">Vista Grid</span></button>
        </div>
    </div>
</div>
