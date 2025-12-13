   <div class="modal fade" id="importarModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content"
                        style="border-radius: 15px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                        <div class="modal-header bg-rojo text-white"
                            style="border-radius: 15px 15px 0 0; border-bottom: none;">
                            <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 600;">
                                <i class="fas fa-file-excel me-2"></i>Importar Productos con EXCEL
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form enctype='multipart/form-data'>
                                <div class="mb-4">
                                    <div class="p-3 bg-light rounded-3" style="border: 1px dashed #dee2e6;">
                                        <p class="mb-2">Descargue el modelo en <span class="fw-bold">EXCEL</span> para
                                            importar,
                                            no
                                            modifique los campos en el archivo.</p>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-2">Click para descargar:</span>
                                            <a href="<?= URL::to('/reporte/producto/guia') ?>"
                                                class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                                                <i class="fas fa-download me-1"></i>plantilla.xlsx
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold mb-2">Importar Excel:</label>
                                    <div class="file-upload-wrapper">
                                        <div class="file-upload-area"
                                            style="position: relative; border: 2px dashed #CA3438; border-radius: 10px; padding: 20px; text-align: center; background-color: #fff5f5; transition: all 0.3s ease;">
                                            <input id="file-import-exel"
                                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                                type="file"
                                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                                            <div class="file-info">
                                                <i class="fas fa-cloud-upload-alt"
                                                    style="font-size: 2rem; color: #CA3438; margin-bottom: 10px;"></i>
                                                <p class="mb-0" id="file-name-display">Arrastre su archivo aquí o haga
                                                    click
                                                    para seleccionar</p>
                                                <p class="text-muted small mt-1">Formatos aceptados: Excel, CSV</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer" style="border-top: none;">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                                style="border-radius: 8px; padding: 8px 20px; font-weight: 500;">Cancelar</button>
                            <button type="button" class="btn bg-rojo text-white" id="btn-importar"
                                style="border-radius: 8px; padding: 8px 20px; font-weight: 500;">
                                <i class="fas fa-file-import me-1"></i>Importar
                            </button>
                        </div>
                    </div>
                </div>
            </div>