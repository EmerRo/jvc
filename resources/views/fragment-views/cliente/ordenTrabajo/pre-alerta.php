<!-- resources\views\fragment-views\cliente\ordenTrabajo\pre-alerta.php -->

<!-- orden de trabajo -->
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">
                REGISTRO DE MAQUINA A PRODUCIR
            </h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="clearfix">
            <ol class="breadcrumb m-0 float-start" style="background: transparent;">
                <li class="breadcrumb-item"><a href="javascript: void(0);"
                        style="color: #718096; text-decoration: none;">Orden Trabajo</a></li>
                <li class="breadcrumb-item active " aria-current="page" style="font-weight: 500; color: #CA3438;">Pre
                    Alerta</li>
            </ol>
        </div>
    </div>
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header bg-white bordes">
                <div class="row align-items-center">
                    <div class="col-md-12 text-end">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar"
                            class="btn bg-rojo text-white bordes">
                            <i class="fa fa-plus"></i> Añadir
                        </button>
                    </div>
                </div>

            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes"
                                class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                                <thead class="table-light">
                                    <tr>
                                        <th>Numero</th>
                                        <th>Cliente/Razon Social</th>
                                        <th>N° Documento</th>
                                        <th>Tecnico</th>
                                        <th>Fecha De Ingreso</th>
                                        <th>Fecha De Salida</th>
                                        <th>Estado De Producción</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para mostrar detalles -->
            <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo p-4">
                            <h5 class="modal-title text-light" id="modalDetallesLabel">
                                <i class="fa fa-info-circle me-2"></i>
                                Detalles de <span class="text-white"> ORDEN DE TRABAJO</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-2">
                            <!-- Aquí se cargarán los detalles dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>

            <div id="client">
                <!-- Modal Agregar orden de trabajo -->
                <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    <i class="fa fa-plus-circle me-2"></i>
                                    ORDEN DE TRABAJO
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="frmClientesAgregar">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="col-lg-12">
                                                    <label for="input_datos_cliente" class="form-label">
                                                        <i class="fa fa-id-card me-1"></i> DNI o RUC
                                                        <span class="text-danger">(*)</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input id="input_datos_cliente" v-model="prealerta.num_doc"
                                                            name="num_doc" type="text" placeholder="Ingrese Documento"
                                                            class="form-control" maxlength="11"
                                                            :class="{ 'is-invalid': validationErrors.num_doc }">
                                                        <div class="input-group-prepend">
                                                            <button @click="buscarDocumentSS"
                                                                class="btn bg-rojo text-white" type="button">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <p class="validation-message" v-if="validationErrors.num_doc">
                                                        {{ validationErrors.num_doc }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <div class="col-lg-12">
                                                    <label for="cliente_Rsocial" class="form-label">
                                                        <i class="fa fa-user me-1"></i> Cliente
                                                    </label>
                                                    <input v-model="prealerta.cliente_Rsocial" type="text"
                                                        placeholder="Nombre del cliente" class="form-control"
                                                        autocomplete="off" id="cliente_Rsocial" name="cliente_Rsocial"
                                                        :class="{ 'is-invalid': validationErrors.cliente_Rsocial }">
                                                    <p class="validation-message"
                                                        v-if="validationErrors.cliente_Rsocial">
                                                        {{ validationErrors.cliente_Rsocial }}
                                                    </p>
                                                    <input type="hidden" id="direccion" name="direccion" value="">
                                                    <input type="hidden" id="cliente_documento" name="cliente_documento"
                                                        value="">
                                                </div>
                                            </div>

                                            <!-- SECCIÓN DE BÚSQUEDA DE SERIES CON DROPDOWN -->
                                            <div class="form-group mb-3">
                                                <div class="col-lg-12">
                                                    <label class="form-label">
                                                        <i class="fa fa-search me-1"></i> Búsqueda de Series
                                                    </label>
                                                    <div class="row">
                                                        <div class="col-8">
                                                            <!-- Campo de búsqueda por serie (visible por defecto) -->
                                                            <div id="grupo_buscar_serie_prealerta"
                                                                class="form-group mb-0">
                                                                <input id="input_buscar_serie" type="text"
                                                                    placeholder="Ingrese número de serie"
                                                                    class="form-control">
                                                            </div>

                                                            <!-- Campo de búsqueda por cliente (oculto inicialmente) -->
                                                            <div id="grupo_buscar_cliente_prealerta"
                                                                class="form-group mb-0" style="display: none;">
                                                                <input id="input_buscar_cliente_prealerta" type="text"
                                                                    placeholder="Ingrese nombre del cliente"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <select id="metodo_busqueda_select" class="form-select">
                                                                <option value="serie" selected>N° Serie</option>
                                                                <option value="cliente">Cliente</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- <div class="mb-3">
                                                <label for="cliente_nombre_right" class="form-label">
                                                    <i class="fa fa-user me-1"></i> Cliente
                                                </label>
                                                <input type="text" class="form-control" id="cliente_nombre_right"
                                                    placeholder="Nombre del cliente" readonly>
                                            </div> -->

                                            <div class="mb-3">
                                                <label for="atencion_Encargado" class="form-label">
                                                    <i class="fa fa-user-cog me-1"></i> Técnico
                                                </label>
                                                <div class="input-group">
                                                    <select type="text" class="form-control" id="atencion_Encargado"
                                                        name="atencion_Encargado" required
                                                        :class="{ 'is-invalid': validationErrors.tecnico }"></select>
                                                    <button class="btn bg-rojo text-white" type="button"
                                                        data-bs-toggle="modal" data-bs-target="#modalTecnico">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p class="validation-message" v-if="validationErrors.tecnico">
                                                    {{ validationErrors.tecnico }}
                                                </p>
                                            </div>

                                            <div class="mb-3">
                                                <label for="fecha_ingreso" class="form-label">
                                                    <i class="fa fa-calendar me-1"></i> Fecha De Ingreso
                                                </label>
                                                <input type="date" class="form-control" id="fecha_ingreso"
                                                    name="fecha_ingreso" required
                                                    :class="{ 'is-invalid': validationErrors.fecha_ingreso }">
                                                <p class="validation-message" v-if="validationErrors.fecha_ingreso">
                                                    {{ validationErrors.fecha_ingreso }}
                                                </p>
                                            </div>
                                            <div class="mb-3">
    <label for="fecha_salida" class="form-label">
        <i class="fa fa-calendar-check me-1"></i> Fecha De Salida (Plazo)
    </label>
    <input type="date" class="form-control" id="fecha_salida"
        name="fecha_salida" required
        :class="{ 'is-invalid': validationErrors.fecha_salida }">
    <p class="validation-message" v-if="validationErrors.fecha_salida">
        {{ validationErrors.fecha_salida }}
    </p>
</div>

                                        </div>
                                    </div>

                                    <!-- Lista de equipos -->
                                    <div v-if="cantidadEquipos > 0 && !maquinasIdenticas" class="equipos-lista">
                                        <div class="bg-gris text-white p-3 mb-3" style="border-radius: 8px;">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="fa fa-laptop me-2"></i>Equipos Seleccionados ({{
                                                cantidadEquipos }})
                                            </h6>
                                        </div>
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            <div v-for="(equipo, index) in equipos" :key="index"
                                                class="equipo-item p-3 border rounded mb-3">
                                                <h6 class="mb-3 text-rojo">
                                                    <i class="fa fa-laptop me-1"></i> Equipo {{index + 1}}
                                                </h6>
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label">
                                                            <i class="fa fa-tag me-1"></i> Marca
                                                        </label>
                                                        <input v-model="equipo.marca" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">
                                                            <i class="fa fa-cube me-1"></i> Modelo
                                                        </label>
                                                        <input v-model="equipo.modelo" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">
                                                            <i class="fa fa-laptop me-1"></i> Equipo
                                                        </label>
                                                        <input v-model="equipo.equipo" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">
                                                            <i class="fa fa-barcode me-1"></i> N° De Serie
                                                        </label>
                                                        <input type="text" class="form-control" v-model="equipo.serie">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="observaciones" class="form-label fw-bold">
                                            <i class="fa fa-comment me-2 text-rojo"></i>Observaciones
                                        </label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                            placeholder="Ingrese observaciones adicionales"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                                    <i class="fa fa-times me-1"></i> Cerrar
                                </button>
                                <button type="button" id="submitRegistro" class="btn bg-rojo text-white">
                                    <i class="fa fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Editar orden de trabajo -->
                <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header text-white bg-rojo">
                                <h5 class="modal-title" id="modalEditarLabel">
                                    <i class="fa fa-edit me-2"></i> Editar Orden de Trabajo
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="frmClientesEditar">
                                    <input type="hidden" id="edit_id_orden_trabajo" name="id_orden_trabajo"
                                        v-model="editando.id_orden_trabajo">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="edit_cliente_Rsocial" class="form-label">
                                                    <i class="fa fa-user me-1"></i> Cliente
                                                </label>
                                                <input type="text" class="form-control" id="edit_cliente_Rsocial"
                                                    name="cliente_razon_social" v-model="editando.cliente_Rsocial"
                                                    :class="{ 'is-invalid': validationErrors.edit_cliente }" required>
                                                <p class="validation-message" v-if="validationErrors.edit_cliente">
                                                    {{ validationErrors.edit_cliente }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="edit_atencion_Encargado" class="form-label">
                                                    <i class="fa fa-user-cog me-1"></i> Técnico
                                                </label>
                                                <input type="text" class="form-control" id="edit_atencion_Encargado"
                                                    name="atencion_encargado" v-model="editando.atencion_Encargado"
                                                    :class="{ 'is-invalid': validationErrors.edit_tecnico }" required>
                                                <p class="validation-message" v-if="validationErrors.edit_tecnico">
                                                    {{ validationErrors.edit_tecnico }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección para equipos individuales en modo edición -->
                                    <div class="equipos-lista mt-4" style="max-height: 400px; overflow-y: auto;">
                                        <div v-for="(equipo, index) in editando.equipos" :key="index"
                                            class="equipo-item mb-3 p-3 border rounded bg-light">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">
                                                    <i class="fa fa-laptop me-1"></i> Equipo {{index + 1}}
                                                </h6>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    @click="eliminarEquipoEdicion(index)"
                                                    v-if="editando.equipos.length > 1">
                                                    <i class="fa fa-trash me-1"></i> Eliminar
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">
                                                        <i class="fa fa-tag me-1"></i> Marca
                                                    </label>
                                                    <input class="form-control" v-model="equipo.marca"
                                                        :class="{ 'is-invalid': validationErrors['equipo_'+index+'_marca'] }">
                                                    <p class="validation-message"
                                                        v-if="validationErrors['equipo_'+index+'_marca']">
                                                        {{ validationErrors['equipo_'+index+'_marca'] }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">
                                                        <i class="fa fa-cube me-1"></i> Modelo
                                                    </label>
                                                    <input class="form-control" v-model="equipo.modelo"
                                                        :class="{ 'is-invalid': validationErrors['equipo_'+index+'_modelo'] }">
                                                    <p class="validation-message"
                                                        v-if="validationErrors['equipo_'+index+'_modelo']">
                                                        {{ validationErrors['equipo_'+index+'_modelo'] }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">
                                                        <i class="fa fa-laptop me-1"></i> Equipo
                                                    </label>
                                                    <input class="form-control" v-model="equipo.equipo"
                                                        :class="{ 'is-invalid': validationErrors['equipo_'+index+'_equipo'] }">
                                                    <p class="validation-message"
                                                        v-if="validationErrors['equipo_'+index+'_equipo']">
                                                        {{ validationErrors['equipo_'+index+'_equipo'] }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">
                                                        <i class="fa fa-barcode me-1"></i> Número de Serie
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        v-model="equipo.numero_serie"
                                                        :class="{ 'is-invalid': validationErrors['equipo_'+index+'_serie'] }">
                                                    <p class="validation-message"
                                                        v-if="validationErrors['equipo_'+index+'_serie']">
                                                        {{ validationErrors['equipo_'+index+'_serie'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" class="btn bg-rojo btn-sm" @click="agregarEquipoEdicion">
                                            <i class="fa fa-plus me-1"></i> Agregar Equipo
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <label for="edit_fecha_ingreso" class="form-label">
                                            <i class="fa fa-calendar me-1"></i> Fecha De Ingreso
                                        </label>
                                        <input type="date" class="form-control" id="edit_fecha_ingreso"
                                            name="fecha_ingreso" v-model="editando.fecha_ingreso"
                                            :class="{ 'is-invalid': validationErrors.edit_fecha }" required>
                                        <p class="validation-message" v-if="validationErrors.edit_fecha">
                                            {{ validationErrors.edit_fecha }}
                                        </p>
                                    </div>
                                    <div class="mb-3">
    <label for="edit_fecha_salida" class="form-label">
        <i class="fa fa-calendar-check me-1"></i> Fecha De Salida (Plazo)
    </label>
    <input type="date" class="form-control" id="edit_fecha_salida"
        name="fecha_salida" v-model="editando.fecha_salida"
        :class="{ 'is-invalid': validationErrors.edit_fecha_salida }" required>
    <p class="validation-message" v-if="validationErrors.edit_fecha_salida">
        {{ validationErrors.edit_fecha_salida }}
    </p>
</div>

                                    <div class="form-group mb-3">
                                        <label for="edit_observaciones">Observaciones</label>
                                        <textarea class="form-control" id="edit_observaciones" name="edit_observaciones"
                                            rows="3" placeholder="Ingrese observaciones adicionales"
                                            v-model="editando.observaciones"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fa fa-times me-1"></i> Cerrar
                                </button>
                                <button type="button" id="submitEditar" class="btn bg-rojo text-white"
                                    @click="guardarEdicion">
                                    <i class="fa fa-save me-1"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- New Technicians Modal -->
<div class="modal fade" id="modalTecnico" tabindex="-1" aria-labelledby="modalTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="modalTecnicoLabel">
                    <i class="fa fa-user-cog me-1"></i> Técnicos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="tecnico_nombre" placeholder="Nombre del técnico">
                        <button type="button" class="btn bg-rojo text-white" id="btnAgregarTecnico">
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table table-striped" id="tablaTecnicos">
                        <thead>
                            <tr>
                                <th><i class="fa fa-user-cog me-1"></i> Nombre</th>
                                <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL::to('public/js/orden-trabajo.js') ?>?v=<?= time() ?>"></script>