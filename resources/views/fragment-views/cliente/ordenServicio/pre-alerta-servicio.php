<?php
require_once 'app/models/PreAlerta.php';

$c_prealerta = new PreAlerta();
?>
<link rel="stylesheet" href="<?= URL::to('public/css/taller/prealerta.css') ?>?v=<?= time() ?> ?>">


<!-- orden de servicio -->
<div class="page-title-box" style="padding: 12px 0;">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h6 class="page-title text-center">
                <!-- <i class="fa fa-tools me-2"></i> -->
                REGISTRO DE MAQUINAS A REPARAR 
            </h6>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalAgregar"
                            class="btn bg-rojo text-white ">
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
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-hashtag me-1"></i> Item</th>
                                        <th><i class="fa fa-user me-1"></i> Cliente/Razon Social</th>
                                        <th><i class="fa fa-id-card me-1"></i> N° Documento</th>
                                        <th><i class="fa fa-user-cog me-1"></i> Tecnico</th>
                                        <th><i class="fa fa-calendar me-1"></i> Fecha De Ingreso</th>
                                        <th><i class="fa fa-cogs me-1"></i> Acciones</th>
                                        <th><i class="fa fa-cogs me-1"></i> Estado De Cotizacion</th>
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
                        <div class="modal-header bg-danger p-4">
                            <h5 class="modal-title text-light" id="modalDetallesLabel">
                                <i class="fa fa-info-circle me-2"></i>
                                Detalles de <span class="text-white"> ORDEN DE SERVICIO</span>

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
                <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header text-white bg-rojo text-white">
                                <h5 class="modal-title" id="exampleModalLabel">
                                    <i class="fa fa-plus-circle me-2"></i>
                                    ORDEN DE TRABAJO Y SERVICIO
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="frmClientesAgregar">
                                <input type="hidden" name="origen" value="Ord Servicio">

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
                                                            <button @click="buscarDocumentSS" class="btn bg-rojo text-white"
                                                                type="button">
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
                                                </div>
                                            </div>
                                            <div class="mb-3" v-if="!maquinasIdenticas">
                                                <label for="cantidad_equipos" class="form-label">
                                                    <i class="fa fa-list-ol me-1"></i> Cantidad de Equipos
                                                </label>
                                                <input type="number" class="form-control"
                                                    v-model.number="cantidadEquipos" min="1" max="10"
                                                    @input="actualizarEquipos"
                                                    :class="{ 'is-invalid': validationErrors.cantidadEquipos }">
                                                <p class="validation-message" v-if="validationErrors.cantidadEquipos">
                                                    {{ validationErrors.cantidadEquipos }}
                                                </p>
                                            </div>

                                            <!-- Checkbox de máquinas idénticas mejorado -->
                                            <div class="maquinas-identicas-check d-flex align-items-center mb-3">
                                                <input type="checkbox" class="form-check-input me-2"
                                                    id="maquinasIdenticas" v-model="maquinasIdenticas">
                                                <label class="form-check-label" for="maquinasIdenticas">
                                                    <i class="fa fa-copy me-1"></i>
                                                    Máquinas idénticas (solo cambia el número de serie)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
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
                                    <div v-if="maquinasIdenticas" class="maquinas-identicas-section col-12 mt-3">
                                        <h6 class="mb-3">
                                            <i class="fa fa-cogs me-1"></i>
                                            Configuración de máquinas idénticas
                                        </h6>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-tag me-1"></i> Marca
                                                </label>
                                                <div class="input-group">
                                                    <select v-model="equipoBase.marca" class="form-control"
                                                        :class="{ 'is-invalid': validationErrors.marca }" required>
                                                        <option value="">Seleccione marca</option>
                                                        <option v-for="marca in marcasDisponibles"
                                                            :value="marca.nombre">
                                                            {{marca.nombre}}
                                                        </option>
                                                    </select>
                                                    <button class="btn bg-rojo text-white" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modalMarca">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p class="text-danger small mt-1" v-if="validationErrors.marca">
                                                    {{ validationErrors.marca }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-cube me-1"></i> Modelo
                                                </label>
                                                <div class="input-group">
                                                    <select v-model="equipoBase.modelo" class="form-control"
                                                        :class="{ 'is-invalid': validationErrors.modelo }" required>
                                                        <option value="">Seleccione modelo</option>
                                                        <option v-for="modelo in modelosDisponibles"
                                                            :value="modelo.nombre">
                                                            {{modelo.nombre}}
                                                        </option>
                                                    </select>
                                                    <button class="btn bg-rojo text-white" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modalModelo">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p class="validation-message" v-if="validationErrors.modelo">
                                                    {{ validationErrors.modelo }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-laptop me-1"></i> Equipo
                                                </label>
                                                <div class="input-group">
                                                    <select v-model="equipoBase.tipo" class="form-control"
                                                        :class="{ 'is-invalid': validationErrors.equipo }" required>
                                                        <option value="">Seleccione equipo</option>
                                                        <option v-for="eq in equiposDisponibles" :value="eq.nombre">
                                                            {{eq.nombre}}
                                                        </option>
                                                    </select>
                                                    <button class="btn bg-rojo text-white" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modalEquipo">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p class="validation-message" v-if="validationErrors.equipo">
                                                    {{ validationErrors.equipo }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-calculator me-1"></i> Cantidad de máquinas
                                                </label>
                                                <input type="number" class="form-control"
                                                    v-model.number="cantidadMaquinasIdenticas" min="1"
                                                    :class="{ 'is-invalid': validationErrors.cantidad }">
                                                <p class="validation-message" v-if="validationErrors.cantidad">
                                                    {{ validationErrors.cantidad }}
                                                </p>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">
                                                    <i class="fa fa-barcode me-1"></i> Números de serie
                                                    <span class="badge bg-rojo text-white ms-2" v-if="seriesCount > 0">
                                                        {{ seriesCount }} / {{ cantidadMaquinasIdenticas }}
                                                    </span>
                                                </label>
                                                <textarea class="form-control" v-model="seriesMultiples" rows="3"
                                                    :class="{ 'is-invalid': validationErrors.series }"
                                                    @input="contarSeries"
                                                    placeholder="Ingrese todos los números de serie, uno por línea o separados por comas"></textarea>
                                                <p class="validation-message" v-if="validationErrors.series">
                                                    {{ validationErrors.series }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fa fa-info-circle me-1"></i>
                                                    Ejemplo: 001, 002, 003 o un número por línea
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Lista de equipos -->
                                    <div v-if="cantidadEquipos > 0 && !maquinasIdenticas" class="equipos-lista mt-4"
                                        style="max-height: 300px; overflow-y: auto; margin-bottom: 15px;">
                                        <div v-for="(equipo, index) in equipos" :key="index"
                                            class="equipo-item mb-3 p-3 border rounded bg-light">
                                            <h6 class="mb-3">
                                                <i class="fa fa-laptop me-1"></i> Equipo {{index + 1}}
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">
                                                        <i class="fa fa-tag me-1"></i> Marca
                                                    </label>
                                                    <div class="input-group">
                                                        <select v-model="equipo.marca" class="form-control" required>
                                                            <option value="">Seleccione marca</option>
                                                            <option v-for="marca in marcasDisponibles"
                                                                :value="marca.nombre">
                                                                {{marca.nombre}}
                                                            </option>
                                                        </select>
                                                        <button class="btn bg-rojo text-white" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#modalMarca">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">
                                                        <i class="fa fa-cube me-1"></i> Modelo
                                                    </label>
                                                    <div class="input-group">
                                                        <select v-model="equipo.modelo" class="form-control" required>
                                                            <option value="">Seleccione modelo</option>
                                                            <option v-for="modelo in modelosDisponibles"
                                                                :value="modelo.nombre">
                                                                {{modelo.nombre}}
                                                            </option>
                                                        </select>
                                                        <button class="btn bg-rojo text-white" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#modalModelo">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">
                                                        <i class="fa fa-laptop me-1"></i> Equipo
                                                    </label>
                                                    <div class="input-group">
                                                        <select v-model="equipo.tipo" class="form-control" required>
                                                            <option value="">Seleccione equipo</option>
                                                            <option v-for="eq in equiposDisponibles" :value="eq.nombre">
                                                                {{eq.nombre}}
                                                            </option>
                                                        </select>
                                                        <button class="btn bg-rojo text-white" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#modalEquipo">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">
                                                        <i class="fa fa-barcode me-1"></i> N° De Serie
                                                    </label>
                                                    <input type="text" class="form-control" v-model="equipo.serie">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                            placeholder="Ingrese observaciones adicionales"></textarea>
                                    </div>
                                </form>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fa fa-times me-1"></i> Cerrar
                                </button>
                                <button type="button" id="submitRegistro" class="btn bg-rojo text-white">
                                    <i class="fa fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Editar -->
                <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header text-white bg-danger">
                                <div class="d-flex align-items-center">
                                    <h5 class="modal-title me-3" id="modalEditarLabel">
                                        <i class="fa fa-edit me-2"></i> Editar Registro
                                    </h5>
                                    <div v-if="detectarEquiposIdenticos() || maquinasIdenticas">
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-3"
                                            @click="maquinasIdenticas = !maquinasIdenticas">
                                            <i class="fa" :class="maquinasIdenticas ? 'fa-list' : 'fa-copy'"></i>
                                            {{ maquinasIdenticas ? 'Modo individual' : 'Máquinas idénticas' }}
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="frmClientesEditar">
                                    <input type="hidden" id="edit_id_preAlerta" name="id_preAlerta"
                                        v-model="editando.id_preAlerta">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="edit_cliente_Rsocial" class="form-label">
                                                    <i class="fa fa-user me-1"></i> Cliente (RUC o DNI)
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

                                    <!-- Sección para máquinas idénticas en modo edición -->
                                    <div v-if="maquinasIdenticas" class="maquinas-identicas-section col-12 mt-3">
                                        <h6 class="mb-3">
                                            <i class="fa fa-cogs me-1"></i>
                                            Configuración de máquinas idénticas
                                        </h6>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-tag me-1"></i> Marca
                                                </label>
                                                <div class="input-group">
                                                    <select v-model="equipoBase.marca" class="form-control"
                                                        :class="{ 'is-invalid': validationErrors.marca }" required>
                                                        <option value="">Seleccione marca</option>
                                                        <option v-for="marca in marcasDisponibles"
                                                            :value="marca.nombre">
                                                            {{marca.nombre}}
                                                        </option>
                                                    </select>
                                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modalMarca">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p class="text-danger small mt-1" v-if="validationErrors.marca">
                                                    {{ validationErrors.marca }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-cube me-1"></i> Modelo
                                                </label>
                                                <div class="input-group">
                                                    <select v-model="equipoBase.modelo" class="form-control"
                                                        :class="{ 'is-invalid': validationErrors.modelo }" required>
                                                        <option value="">Seleccione modelo</option>
                                                        <option v-for="modelo in modelosDisponibles"
                                                            :value="modelo.nombre">
                                                            {{modelo.nombre}}
                                                        </option>
                                                    </select>
                                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modalModelo">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p class="validation-message" v-if="validationErrors.modelo">
                                                    {{ validationErrors.modelo }}
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-laptop me-1"></i> Equipo
                                                </label>
                                                <div class="input-group">
                                                    <select v-model="equipoBase.tipo" class="form-control"
                                                        :class="{ 'is-invalid': validationErrors.equipo }" required>
                                                        <option value="">Seleccione equipo</option>
                                                        <option v-for="eq in equiposDisponibles" :value="eq.nombre">
                                                            {{eq.nombre}}
                                                        </option>
                                                    </select>
                                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modalEquipo">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <p class="validation-message" v-if="validationErrors.equipo">
                                                    {{ validationErrors.equipo }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fa fa-calculator me-1"></i> Cantidad de máquinas
                                                </label>
                                                <input type="number" class="form-control"
                                                    v-model.number="cantidadMaquinasIdenticas" min="1"
                                                    :class="{ 'is-invalid': validationErrors.cantidad }">
                                                <p class="validation-message" v-if="validationErrors.cantidad">
                                                    {{ validationErrors.cantidad }}
                                                </p>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">
                                                    <i class="fa fa-barcode me-1"></i> Números de serie
                                                    <span class="badge bg-primary ms-2" v-if="seriesCount > 0">
                                                        {{ seriesCount }} / {{ cantidadMaquinasIdenticas }}
                                                    </span>
                                                </label>
                                                <textarea class="form-control" v-model="seriesMultiples" rows="5"
                                                    :class="{ 'is-invalid': validationErrors.series }"
                                                    @input="contarSeries"
                                                    placeholder="Ingrese todos los números de serie, uno por línea o separados por comas"></textarea>
                                                <p class="validation-message" v-if="validationErrors.series">
                                                    {{ validationErrors.series }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fa fa-info-circle me-1"></i>
                                                    Ejemplo: 001, 002, 003 o un número por línea
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección para equipos individuales en modo edición -->
                                    <div v-if="!maquinasIdenticas" class="equipos-lista mt-4"
                                        style="max-height: 400px; overflow-y: auto;">
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
                                                    <div class="input-group">
                                                        <select class="form-control" v-model="equipo.marca"
                                                            :class="{ 'is-invalid': validationErrors['equipo_'+index+'_marca'] }">
                                                            <option value="">Seleccione marca</option>
                                                            <option v-for="marca in marcasDisponibles"
                                                                :value="marca.nombre">
                                                                {{marca.nombre}}
                                                            </option>
                                                        </select>
                                                        <button class="btn btn-primary" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#modalMarca">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <p class="validation-message"
                                                        v-if="validationErrors['equipo_'+index+'_marca']">
                                                        {{ validationErrors['equipo_'+index+'_marca'] }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">
                                                        <i class="fa fa-cube me-1"></i> Modelo
                                                    </label>
                                                    <div class="input-group">
                                                        <select class="form-control" v-model="equipo.modelo"
                                                            :class="{ 'is-invalid': validationErrors['equipo_'+index+'_modelo'] }">
                                                            <option value="">Seleccione modelo</option>
                                                            <option v-for="modelo in modelosDisponibles"
                                                                :value="modelo.nombre">
                                                                {{modelo.nombre}}
                                                            </option>
                                                        </select>
                                                        <button class="btn btn-primary" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#modalModelo">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
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
                                                    <div class="input-group">
                                                        <select class="form-control" v-model="equipo.equipo"
                                                            :class="{ 'is-invalid': validationErrors['equipo_'+index+'_equipo'] }">
                                                            <option value="">Seleccione equipo</option>
                                                            <option v-for="eq in equiposDisponibles" :value="eq.nombre">
                                                                {{eq.nombre}}
                                                            </option>
                                                        </select>
                                                        <button class="btn btn-primary" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#modalEquipo">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
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
                                        <button type="button" class="btn btn-primary btn-sm"
                                            @click="agregarEquipoEdicion">
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
                                    <div class="form-group mb-3">
                                        <label for="edit_observaciones">Observaciones</label>
                                        <textarea class="form-control" id="edit_observaciones" name="edit_observaciones"
                                            rows="3" placeholder="Ingrese observaciones adicionales"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fa fa-times me-1"></i> Cerrar
                                </button>
                                <button type="button" id="submitEditar" class="btn btn-primary" @click="guardarEdicion">
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

<!-- Modals for managing marcas, modelos, equipos and tecnicos -->
<div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMarcaLabel">
                    <i class="fa fa-tag me-1"></i> Marcas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="marca_nombre" placeholder="Nombre de la marca">
                        <button type="button" class="btn btn-primary" id="btnAgregarMarca">
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table table-striped" id="tablaMarcas">
                        <thead>
                            <tr>
                                <th><i class="fa fa-tag me-1"></i> Nombre</th>
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

<div class="modal fade" id="modalModelo" tabindex="-1" aria-labelledby="modalModeloLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModeloLabel">
                    <i class="fa fa-cube me-1"></i> Modelos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="modelo_nombre" placeholder="Nombre del modelo">
                        <button type="button" class="btn btn-primary" id="btnAgregarModelo">
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table table-striped" id="tablaModelos">
                        <thead>
                            <tr>
                                <th><i class="fa fa-cube me-1"></i> Nombre</th>
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

<div class="modal fade" id="modalEquipo" tabindex="-1" aria-labelledby="modalEquipoLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEquipoLabel">
                    <i class="fa fa-laptop me-1"></i> Equipos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="equipo_nombre" placeholder="Nombre del equipo">
                        <button type="button" class="btn btn-primary" id="btnAgregarEquipo">
                            <i class="fa fa-save"></i>
                        </button>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="table table-striped" id="tablaEquipos">
                        <thead>
                            <tr>
                                <th><i class="fa fa-laptop me-1"></i> Nombre</th>
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

<!-- New Technicians Modal -->
<div class="modal fade" id="modalTecnico" tabindex="-1" aria-labelledby="modalTecnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-stacked">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTecnicoLabel">
                    <i class="fa fa-user-cog me-1"></i> Técnicos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="tecnico_nombre" placeholder="Nombre del técnico">
                        <button type="button" class="btn btn-primary" id="btnAgregarTecnico">
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




<script src="<?= URL::to('public/js/pre-alerta-servicio.js') ?>?v=<?= time() ?>"></script>