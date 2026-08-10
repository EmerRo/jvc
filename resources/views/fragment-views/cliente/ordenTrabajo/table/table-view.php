<div id="table-view" class="table-view">
    <div class="row">
        <div class="form-group col-md-3">
            <label for="">Almacén</label>
            <div class="d-flex align-items-center gap-1">
                <select name="almacenSelect" id="almacenSelect" class="form-control" v-model="almacen" @change="changeAlmacen($event)">
                    <option value="" disabled>Seleccionar</option>
                    <option v-for="alm in almacenes" :key="alm.id_almacen" :value="alm.id_almacen">
                        {{ alm.nombre }}{{ alm.principal == 1 ? ' ★' : '' }}
                    </option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-success" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" @click="abrirModalAlmacen()">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table id="datatable" class="table table-bordered dt-responsive nowrap text-center table-sm"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead class="table-light">
                <tr>
                    <th>Codigo</th>
                    <th>Nombre Del Repuesto</th>
                    <th>Unidades</th>
                    <th>Precios</th>
                    <th>stock</th>
                    <th>Editar</th>
                    <th>Eliminar <input type="checkbox" class="btnSeleccionarTodos"></th>
                </tr>
            </thead>
            <tbody id="tbodyRepuestos"></tbody>
        </table>
    </div>
</div>
