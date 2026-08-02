<!-- Modal Agregar orden de servicio -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white bg-rojo text-white">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i class="fa fa-plus-circle me-2"></i>
                    ORDEN DE SERVICIO
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
                            <div class="col-md-5">
                                <label class="form-label">
                                    <i class="fa fa-laptop me-1"></i> Equipo <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <select v-model="equipoBase.tipo" class="form-control" required
                                        @change="alCambiarEquipoBase">
                                        <option value="">Seleccione equipo...</option>
                                        <option v-for="eq in equiposDisponibles" :value="eq.nombre"
                                            :data-marca-nombre="eq.marca_nombre || ''"
                                            :data-modelo-nombre="eq.modelo_nombre || ''">
                                            {{eq.nombre}}{{ eq.marca_nombre || eq.modelo_nombre ? ' — ' + (eq.marca_nombre || '') + (eq.modelo_nombre ? ' / ' + eq.modelo_nombre : '') : '' }}
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
                            <div class="col-md-7">
                                <label class="form-label text-muted">
                                    <i class="fa fa-tag me-1"></i> Marca / Modelo
                                </label>
                                <input type="text" class="form-control" readonly
                                    :value="marcaModeloDisplay(equipoBase)"
                                    placeholder="Se completa al elegir equipo"
                                    style="background:#f8f9fa;cursor:default;">
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
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="fa fa-laptop me-1"></i> Equipo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select v-model="equipo.tipo" class="form-control" required
                                            @change="alCambiarEquipo(equipo)">
                                            <option value="">Seleccione equipo...</option>
                                            <option v-for="eq in equiposDisponibles" :value="eq.nombre"
                                                :data-marca-nombre="eq.marca_nombre || ''"
                                                :data-modelo-nombre="eq.modelo_nombre || ''">
                                                {{eq.nombre}}{{ eq.marca_nombre || eq.modelo_nombre ? ' — ' + (eq.marca_nombre || '') + (eq.modelo_nombre ? ' / ' + eq.modelo_nombre : '') : '' }}
                                            </option>
                                        </select>
                                        <button class="btn bg-rojo text-white" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalEquipo">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">
                                        <i class="fa fa-tag me-1"></i> Marca / Modelo
                                    </label>
                                    <input type="text" class="form-control" readonly
                                        :value="marcaModeloDisplay(equipo)"
                                        placeholder="Se completa al elegir equipo"
                                        style="background:#f8f9fa;cursor:default;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fa fa-barcode me-1"></i> N° De Serie <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" v-model="equipo.serie" required>
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
