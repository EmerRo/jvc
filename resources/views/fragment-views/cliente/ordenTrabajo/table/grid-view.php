<div id="grid-view" class="products-grid">
    <div class="grid-filters">
        <div class="row">
            <div class="col-md-3">
                <label for="grid-almacen-select">Almacen</label>
                <div class="d-flex align-items-center gap-1">
                    <select id="grid-almacen-select" class="form-control">
                        <option v-for="alm in almacenes" :key="alm.id_almacen" :value="alm.id_almacen">
                            {{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}
                        </option>
                    </select>
                    <button type="button" class="btn btn-sm btn-outline-success" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" @click="abrirModalAlmacen()">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-5">
                <label>&nbsp;</label>
                <div class="grid-search">
                    <input type="text" id="grid-search-input" class="form-control" placeholder="Buscar repuestos...">
                </div>
            </div>
            <div class="col-md-4 text-end d-flex align-items-end justify-content-end">
                <button class="btn border-rojo bg-white btnSeleccionarTodosGrid">
                    <i class="fa fa-check-square me-1"></i> Seleccionar Todos
                </button>
            </div>
        </div>
    </div>

    <div class="loading-grid" id="loading-grid">
        <i class="fa fa-spinner"></i>
        <p>Cargando repuestos...</p>
    </div>

    <div class="product-grid-container" id="products-container"></div>
    <div class="grid-pagination" id="grid-pagination"></div>
</div>
