<!-- resources\views\fragment-views\cliente\ordenTrabajo\pre-alerta.php -->

<!-- orden de servicio -->
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">
              REGISTRO DE MAQUINAS A REPARAR 
            </h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar" class="btn bg-rojo text-white">
                            <i class="fa fa-plus"></i> Añadir
                        </button>
                    </div>
                </div>
            </div>
            <div id="conte-vue-modals">
                <div class="card-body">
                    <div class="card-title-desc">
                        <div class="table-responsive">
                            <table id="tabla_clientes" class="table table-bordered dt-responsive nowrap text-center table-sm dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-hashtag me-1"></i> Item</th>
                                        <th><i class="fa fa-user me-1"></i> Cliente/Razon Social</th>
                                        <th><i class="fa fa-id-card me-1"></i> N° Documento</th>
                                        <th><i class="fa fa-user-cog me-1"></i> Tecnico</th>
                                        <th><i class="fa fa-calendar me-1"></i> Fecha De Ingreso</th>
                                        <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para mostrar detalles -->
            <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
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
                <!-- Modal Agregar prealerta -->
                <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-rojo text-white">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    <i class="fa fa-plus-circle me-2"></i>
                                    ORDEN DE TRABAJO Y SERVICIO
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="frmClientesAgregar">
                                    <input type="hidden" name="origen" value="Ord Trabajo">

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
                                                            <button @click="buscarDocumentSS" class="btn bg-rojo text-white" type="button">
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
                                                    <p class="validation-message" v-if="validationErrors.cliente_Rsocial">
                                                        {{ validationErrors.cliente_Rsocial }}
                                                    </p>
                                                    <input type="hidden" id="direccion" name="direccion" value="">
                                                    <input type="hidden" id="cliente_documento" name="cliente_documento" value="">
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
                <div id="grupo_buscar_serie_prealerta" class="form-group mb-0">
                    <input id="input_buscar_serie" type="text" placeholder="Ingrese número de serie" class="form-control">
                </div>

                <!-- Campo de búsqueda por cliente (oculto inicialmente) -->
                <div id="grupo_buscar_cliente_prealerta" class="form-group mb-0" style="display: none;">
                    <input id="input_buscar_cliente_prealerta" type="text" placeholder="Ingrese nombre del cliente" class="form-control">
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
                                            <div class="mb-3">
                                                <label for="cliente_nombre_right" class="form-label">
                                                    <i class="fa fa-user me-1"></i> Cliente
                                                </label>
                                                <input type="text" class="form-control" id="cliente_nombre_right" placeholder="Nombre del cliente" readonly>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="atencion_Encargado" class="form-label">
                                                    <i class="fa fa-user-cog me-1"></i> Técnico
                                                </label>
                                                <div class="input-group">
                                                    <select type="text" class="form-control" id="atencion_Encargado"
                                                        name="atencion_Encargado" required
                                                        :class="{ 'is-invalid': validationErrors.tecnico }"></select>
                                                    <button class="btn bg-rojo text-white" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modalTecnico">
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
                                        </div>
                                    </div>

                                    <!-- Lista de equipos -->
                                    <div v-if="cantidadEquipos > 0 && !maquinasIdenticas" class="equipos-lista">
                                        <div class="bg-gris text-white p-3 mb-3" style="border-radius: 8px;">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="fa fa-laptop me-2"></i>Equipos Seleccionados ({{ cantidadEquipos }})
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