<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white bg-rojo">
                <div class="d-flex align-items-center">
                    <h5 class="modal-title me-3" id="modalEditarLabel">
                        <i class="fa fa-edit me-2"></i> Editar Registro
                    </h5>
                    <div v-if="detectarEquiposIdenticos() || maquinasIdenticas">
                        <button type="button" class="btn btn-sm bg-rojo ms-3"
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
                    <input type="hidden" id="edit_id_orden_servicio" name="id_orden_servicio"
                        v-model="editando.id_orden_servicio">
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
                                    <button class="btn bg-rojo" type="button" data-bs-toggle="modal"
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
                                    <span class="badge bg-rojo ms-2" v-if="seriesCount > 0">
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
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="fa fa-laptop me-1"></i> Equipo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <select v-model="equipo.equipo" class="form-control" required
                                            @change="alCambiarEquipoEdicion(equipo)">
                                            <option value="">Seleccione equipo...</option>
                                            <option v-for="eq in equiposDisponibles" :value="eq.nombre"
                                                :data-marca-nombre="eq.marca_nombre || ''"
                                                :data-modelo-nombre="eq.modelo_nombre || ''">
                                                {{eq.nombre}}{{ eq.marca_nombre || eq.modelo_nombre ? ' — ' + (eq.marca_nombre || '') + (eq.modelo_nombre ? ' / ' + eq.modelo_nombre : '') : '' }}
                                            </option>
                                        </select>
                                        <button class="btn bg-rojo" type="button"
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
                                        <i class="fa fa-barcode me-1"></i> Número de Serie <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                        v-model="equipo.numero_serie" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn bg-rojo btn-sm"
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
                <button type="button" id="submitEditar" class="btn bg-rojo" @click="guardarEdicion">
                    <i class="fa fa-save me-1"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>
