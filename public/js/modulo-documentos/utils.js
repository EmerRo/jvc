
/**
 * Clase principal para gestionar documentos
 */
class DocumentosUtils {
    constructor(config) {
        this.config = {
            tipo: config.tipo, // 'carta', 'constancia', 'informe', 'archivoInterno', 'otro-archivo'
            urls: config.urls,
            elementos: config.elementos,
            ...config
        };

        this.documentos = [];
        this.filtroActual = '';
        this.tipoFiltroActual = 'todos';
        this.filtroTipo = 'todos';
        this.editor = null;
        this.templateEditor = null;
        this.procesandoAccion = false;
        this.plantillaActual = null;
        this.documentoActual = null;
        this.pdfsRenderizados = new Set(); // Cache de PDFs ya renderizados
        this.pdfLoadingTasks = new Map();
        this.pdfRenderTasks = new Map();
        this.timeoutBusqueda = null; // Para debounce de búsqueda
        this.eventNamespace = `.documentos-${this.config.tipo}`;
        this.rootElement = config.rootElement || document.querySelector(this.config.elementos.vistaLista);
        this.swalCargaActiva = false;
        this.destroyed = false;
        this.seleccionados = new Set();

        const selectoresModales = this.config.modalesPropios || [
            this.config.elementos.modalEliminar,
            this.config.elementos.modalPreview,
            this.config.elementos.modalPlantilla,
            this.config.elementos.modalMembretes
        ].filter(Boolean);
        this.modalElements = selectoresModales
            .map((selector) => document.querySelector(selector))
            .filter(Boolean);

        [this.rootElement, ...this.modalElements].forEach((element) => {
            if (element) {
                element.__documentosUtilsOwner = this;
            }
        });

        this.init();
    }

    /**
     * Inicialización del módulo
     */
    init() {
        if (window[`${this.config.tipo}ModuleInitialized`]) {
            console.log(`El módulo de ${this.config.tipo} ya ha sido inicializado. Evitando reinicialización.`);
            return;
        }

        window[`${this.config.tipo}ModuleInitialized`] = true;
        console.log(`Inicializando módulo de ${this.config.tipo}...`);

        this.verificarCompatibilidad();
        this.configurarEventos();
        this.cargarDocumentos();
        this.cargarQuillSiNoExiste();
    }

    /**
     * Verificar compatibilidad del navegador
     */
    verificarCompatibilidad() {
        if (!window.MutationObserver) {
            console.warn('MutationObserver no está disponible en este navegador');
            Swal.fire({
                icon: 'warning',
                title: 'Navegador no compatible',
                text: 'Su navegador no es compatible con todas las funciones. Por favor, actualice su navegador.'
            });
        }
    }

    mostrarCarga(titulo, texto) {
        this.swalCargaActiva = true;
        Swal.fire({
            title: titulo,
            text: texto,
            allowOutsideClick: false,
            didOpen: (popup) => {
                const container = popup.closest('.swal2-container');
                if (container) {
                    container.dataset.documentosOwner = this.config.tipo;
                }
                Swal.showLoading();
            }
        });
    }

    cerrarCarga() {
        if (!this.swalCargaActiva) return;

        this.swalCargaActiva = false;
        const container = document.querySelector(`.swal2-container[data-documentos-owner="${this.config.tipo}"]`);
        if (container) {
            Swal.close();
            container.remove();
        }
    }

    /**
     * Configurar eventos principales
     */
    configurarEventos() {
        const self = this;

        // Botones de navegación
        $(this.config.elementos.btnLista).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.mostrarVistaLista());
        $(this.config.elementos.btnNuevo).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.mostrarFormularioNuevo());
        $(this.config.elementos.btnEditarPlantilla).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.editarPlantilla());
        $(this.config.elementos.btnGestionarMembretes).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.gestionarMembretes());

        // Modal de confirmación para eliminar
        $(this.config.elementos.modalEliminar).off(this.eventNamespace).on(`show.bs.modal${this.eventNamespace}`, function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            $(self.config.elementos.btnConfirmarEliminar).off(self.eventNamespace).on(`click${self.eventNamespace}`, () => self.eliminarDocumento(id));
        });

        // Búsqueda con Debounce
        $(this.config.elementos.inputBuscar).off(this.eventNamespace).on(`keyup${this.eventNamespace}`, () => {
            clearTimeout(this.timeoutBusqueda);
            this.timeoutBusqueda = setTimeout(() => {
                this.buscarDocumentos();
            }, 300); // Espera 300ms después de que el usuario deja de escribir
        });


        // Formulario
        $(this.config.elementos.btnCancelar).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.mostrarVistaLista());
        $(this.config.elementos.btnGuardar).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.guardarDocumento());
        $(this.config.elementos.btnPreview).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.mostrarVistaPrevia());

        // Plantilla
        $(this.config.elementos.btnGuardarPlantilla).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.guardarPlantilla());
        $(this.config.elementos.btnPreviewPlantilla).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.mostrarVistaPreviewPlantilla());

        // Membretes
        $(this.config.elementos.btnGuardarMembretes).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.guardarMembretes());
        $(this.config.elementos.btnPreviewMembretes).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => this.mostrarVistaPreviewMembretes());

        // Imágenes de membretes
        $(this.config.elementos.headerImageInput).off(this.eventNamespace).on(`change${this.eventNamespace}`, (e) => {
            this.manejarCambioImagen(e, 'membrete_header_image_data', 'membrete-header-preview', 'header-placeholder-membrete');
        });

        $(this.config.elementos.footerImageInput).off(this.eventNamespace).on(`change${this.eventNamespace}`, (e) => {
            this.manejarCambioImagen(e, 'membrete_footer_image_data', 'membrete-footer-preview', 'footer-placeholder-membrete');
        });

        $(this.config.elementos.resetHeaderBtn).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => {
            this.restablecerImagen('membrete_header_image_data', 'membrete-header-preview', 'header-placeholder-membrete');
        });

        $(this.config.elementos.resetFooterBtn).off(this.eventNamespace).on(`click${this.eventNamespace}`, () => {
            this.restablecerImagen('membrete_footer_image_data', 'membrete-footer-preview', 'footer-placeholder-membrete');
        });

        // Eventos de modales
        this.configurarEventosModales();
    }

    /**
     * Configurar eventos específicos de modales
     */
    configurarEventosModales() {
        const self = this;

        // Modal de plantilla
        $(this.config.elementos.modalPlantilla).off(this.eventNamespace).on(`hidden.bs.modal${this.eventNamespace}`, function() {
            console.log("Modal de plantilla cerrado, destruyendo editor");
            self.destruirEditorPlantilla();
            $(self.config.elementos.editorPlantilla).empty();
        });

        $(this.config.elementos.modalPlantilla).on(`show.bs.modal${this.eventNamespace}`, function() {
            console.log("Modal de plantilla abriéndose");
            self.destruirEditorPlantilla();
        });

        // Modal de vista previa - regresar a membretes
        $(this.config.elementos.modalPreview).off(this.eventNamespace).on(`hidden.bs.modal${this.eventNamespace}`, function() {
            if (window.regresarAMembretes) {
                window.regresarAMembretes = false;
                setTimeout(() => {
                    const modal = new bootstrap.Modal(document.getElementById(self.config.elementos.modalMembretes.replace('#', '')));
                    modal.show();
                }, 300);
            }
        });
    }

    /**
     * Mostrar vista de lista
     */
    mostrarVistaLista() {
        $(".vista").removeClass("active");
        $(this.config.elementos.vistaLista).addClass("active");

        // Actualizar estado de botones
        this.actualizarEstadoBotones('lista');
        this.destruirEditor();
        this.cargarDocumentos();
    }

    /**
     * Actualizar estado de los botones
     */
    actualizarEstadoBotones(vista) {
        const botones = {
            lista: this.config.elementos.btnLista,
            nuevo: this.config.elementos.btnNuevo,
            plantilla: this.config.elementos.btnEditarPlantilla
        };

        // Resetear todos los botones
        Object.values(botones).forEach(btn => {
            $(btn).removeClass("bg-rojo text-white btn-rojo").addClass("border-rojo btn-outline-danger");
        });

        // Activar el botón correspondiente
        if (botones[vista]) {
            $(botones[vista]).removeClass("border-rojo btn-outline-danger").addClass("bg-rojo text-white btn-rojo");
        }
    }

    /**
     * Cargar Quill si no existe
     */
    cargarQuillSiNoExiste() {
        if (typeof Quill === 'undefined') {
            console.log("Quill no está cargado, cargando la biblioteca...");

            if (!this.quillCssLoaded) {
                const quillCSS = document.createElement('link');
                quillCSS.rel = 'stylesheet';
                quillCSS.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css';
                document.head.appendChild(quillCSS);
                this.quillCssLoaded = true;
            }

            const quillScript = document.createElement('script');
            quillScript.src = 'https://cdn.quilljs.com/1.3.6/quill.min.js';
            quillScript.onload = () => {
                console.log("Quill cargado correctamente");
                this.quillLoaded = true;
            };
            quillScript.onerror = () => {
                console.error("Error al cargar Quill");
            };
            document.head.appendChild(quillScript);
        } else {
            this.quillLoaded = true;
        }
    }

    /**
     * Cargar documentos
     */
    cargarDocumentos() {
        console.log(`Cargando ${this.config.tipo}s...`);

        $(this.config.elementos.contenedorLista).html(`
            <div class="text-center py-5">
                <div class="spinner-border text-rojo" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando ${this.config.tipo}s...</p>
            </div>
        `);

        let url = this.config.urls.render;
        if (this.filtroActual) {
            url += `?filtro=${encodeURIComponent(this.filtroActual)}&tipo_busqueda=${this.tipoFiltroActual}`;
        } else if (this.filtroTipo && this.filtroTipo !== 'todos') {
            url += `?filtro=${encodeURIComponent(this.filtroTipo)}&tipo_busqueda=tipo`;
        }

        $.ajax({
            url: url,
            method: "GET",
            dataType: 'json',
            success: (data) => {
                const documentosKey = this.config.tipo === 'informe' ? 'informes' : `${this.config.tipo}s`;

                if (!data || !data[documentosKey]) {
                    this.mostrarNoHayDocumentos();
                    return;
                }

                this.documentos = Array.isArray(data[documentosKey]) ? data[documentosKey] : [];
                this.renderizarDocumentos();
            },
            error: (xhr, status, error) => {
                console.error(`Error al cargar ${this.config.tipo}s:`, status, error);
                $(this.config.elementos.contenedorLista).html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error al cargar los ${this.config.tipo}s. Por favor, intente nuevamente.
                    </div>
                    <button class="btn btn-rojo mt-3" onclick="window.recargar${this.config.tipo.charAt(0).toUpperCase() + this.config.tipo.slice(1)}s()">
                        <i class="fas fa-sync me-2"></i>Reintentar
                    </button>
                `);
            }
        });
    }

    /**
     * Mostrar mensaje cuando no hay documentos
     */
    mostrarNoHayDocumentos() {
        const tipoCapitalizado = this.config.tipo.charAt(0).toUpperCase() + this.config.tipo.slice(1);
        $(this.config.elementos.contenedorLista).html(`
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                No se encontraron ${this.config.tipo}s.
            </div>
            <button class="btn btn-rojo mt-3" onclick="window.mostrarFormularioNuevo${tipoCapitalizado}()">
                <i class="fas fa-plus me-2"></i>Crear primer ${this.config.tipo}
            </button>
        `);
    }


    /**
     * Renderizar documentos
     */
    renderizarDocumentos() {
        this.seleccionados.clear();
        $(this.config.elementos.contenedorLista).empty();

        if (!this.documentos || this.documentos.length === 0) {
            this.mostrarNoHayDocumentos();
            return;
        }

        const tipo = this.config.tipo;
        const toolbarId = `toolbar-sel-${tipo}`;
        const checkAllId  = `check-all-${tipo}`;
        const countId     = `count-sel-${tipo}`;
        const btnElimId   = `btn-elim-sel-${tipo}`;

        let html = `
            <div class="d-flex align-items-center gap-2 flex-wrap mb-3 px-2 py-2 rounded border bg-light d-none doc-sel-toolbar" id="${toolbarId}">
                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" id="${checkAllId}" style="width:1.1rem;height:1.1rem;cursor:pointer;">
                    <label class="form-check-label fw-semibold" for="${checkAllId}">Seleccionar todos</label>
                </div>
                <span class="badge bg-secondary ms-1" id="${countId}">0 seleccionados</span>
                <button class="btn btn-danger btn-sm ms-auto" id="${btnElimId}">
                    <i class="fas fa-trash-alt me-1"></i> Eliminar seleccionados
                </button>
            </div>
            <div class="row row-cols-1 row-cols-md-3 g-4">
        `;

        this.documentos.forEach((documento) => {
            const fecha = new Date(documento.fecha_creacion).toLocaleDateString();
            const cliente = documento.cliente_nombre || 'Sin cliente';
            const canvasId = `pdf-preview-${tipo}-${documento.id}`;
            const idField = tipo === 'informe' ? 'id_informe' : 'id';
            const docId = documento[idField];

            html += `
                <div class="col">
                    <div class="card ${tipo}-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" class="form-check-input doc-sel-check" data-id="${docId}"
                                    style="width:1.1rem;height:1.1rem;cursor:pointer;flex-shrink:0;">
                                <span class="badge bg-rojo">${documento.tipo || 'Sin tipo'}</span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="${this.config.urls.generarPDF}?id=${docId}" target="_blank">
                                        <i class="fas fa-file-pdf me-2"></i> Ver PDF
                                    </a></li>
                                    <li><a class="dropdown-item ${tipo}-editar" data-id="${docId}">
                                        <i class="fas fa-edit me-2"></i> Editar
                                    </a></li>
                                    <li><a class="dropdown-item ${tipo}-whatsapp" data-id="${docId}">
                                        <i class="fab fa-whatsapp me-2 text-success"></i> Compartir por WhatsApp
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                           data-bs-target="${this.config.elementos.modalEliminar}" data-id="${docId}">
                                        <i class="fas fa-trash-alt me-2"></i> Eliminar
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="document-preview">
                                <canvas id="${canvasId}" class="pdf-preview-canvas"></canvas>
                            </div>
                        </div>
                        <div class="card-footer">
                            <h5 class="card-title">${documento.titulo}</h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i> ${cliente}<br>
                                    <i class="fas fa-calendar-alt me-1"></i> ${fecha}
                                </small>
                            </p>
                            <div class="d-flex justify-content-between mt-2">
                                <a href="${this.config.urls.generarPDF}?id=${docId}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                </a>
                                <div>
                                    <button class="btn btn-sm btn-outline-success ${tipo}-whatsapp" data-id="${docId}">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="btn btn-sm text-rojo ${tipo}-editar" data-id="${docId}">
                                        <i class="fas fa-edit me-1"></i> Editar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        $(this.config.elementos.contenedorLista).html(html);

        const container = $(this.config.elementos.contenedorLista);

        // Checkboxes individuales
        container.find('.doc-sel-check').on('change', (e) => {
            const id = parseInt($(e.currentTarget).data('id'));
            if ($(e.currentTarget).is(':checked')) {
                this.seleccionados.add(id);
            } else {
                this.seleccionados.delete(id);
                container.find(`#${checkAllId}`).prop('checked', false);
            }
            this.actualizarToolbarSeleccion();
        });

        // Seleccionar / deseleccionar todos
        container.find(`#${checkAllId}`).on('change', (e) => {
            const checked = $(e.currentTarget).is(':checked');
            container.find('.doc-sel-check').each((_, el) => {
                $(el).prop('checked', checked);
                const id = parseInt($(el).data('id'));
                if (checked) this.seleccionados.add(id);
                else this.seleccionados.delete(id);
            });
            this.actualizarToolbarSeleccion();
        });

        // Botón eliminar seleccionados
        container.find(`#${btnElimId}`).on('click', () => this.eliminarSeleccionados());

        // Agregar eventos a los botones de editar
        $(`.${tipo}-editar`).off(this.eventNamespace).on(`click${this.eventNamespace}`, (e) => {
            const id = $(e.currentTarget).data('id');
            this.editarDocumento(id);
        });

        // Agregar eventos a los botones de WhatsApp
        $(`.${tipo}-whatsapp`).off(this.eventNamespace).on(`click${this.eventNamespace}`, (e) => {
            const id = $(e.currentTarget).data('id');
            const funcionWhatsApp = `compartirWhatsApp${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`;
            if (typeof window[funcionWhatsApp] === 'function') {
                window[funcionWhatsApp](id);
            } else {
                console.error(`Función ${funcionWhatsApp} no está definida`);
            }
        });

        // Inicializar la carga de PDFs
        this.documentos.forEach((documento) => {
            const idField = tipo === 'informe' ? 'id_informe' : 'id';
            const canvasId = `pdf-preview-${tipo}-${documento.id}`;
            setTimeout(() => {
                this.renderPdfPreview(`${this.config.urls.generarPDF}?id=${documento[idField]}`, canvasId);
            }, 100);
        });
    }

    actualizarToolbarSeleccion() {
        const count = this.seleccionados.size;
        const tipo  = this.config.tipo;
        const container = $(this.config.elementos.contenedorLista);
        container.find('.doc-sel-toolbar').toggleClass('d-none', count === 0);
        container.find(`#count-sel-${tipo}`).text(`${count} seleccionado${count !== 1 ? 's' : ''}`);
    }

    eliminarSeleccionados() {
        const count = this.seleccionados.size;
        if (count === 0) return;

        const tipo = this.config.tipo;

        Swal.fire({
            title: `¿Eliminar ${count} ${tipo}${count !== 1 ? 's' : ''}?`,
            text: 'Se eliminarán los documentos seleccionados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar'
        }).then((r1) => {
            if (!r1.isConfirmed) return;

            Swal.fire({
                title: 'Confirmación final',
                html: `Está a punto de eliminar <strong>${count} documento${count !== 1 ? 's' : ''}</strong> de forma permanente.<br><br>Esta acción <strong>no se puede deshacer</strong>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar definitivamente',
                cancelButtonText: 'Cancelar'
            }).then((r2) => {
                if (!r2.isConfirmed) return;
                this._ejecutarBorradoMasivo(Array.from(this.seleccionados));
            });
        });
    }

    _ejecutarBorradoMasivo(ids) {
        const urlMasivo = this.config.urls.borrar.replace('/borrar', '/borrar-masivo');

        this.mostrarCarga('Eliminando...', 'Por favor espere');

        $.ajax({
            url: urlMasivo,
            method: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: (data) => {
                this.cerrarCarga();
                if (data.res) {
                    Swal.fire({ icon: 'success', title: 'Eliminados', text: data.msg, timer: 2000, showConfirmButton: false });
                    this.seleccionados.clear();
                    this.cargarDocumentos();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.msg });
                }
            },
            error: () => {
                this.cerrarCarga();
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar con el servidor' });
            }
        });
    }

    /**
     * Buscar documentos
     */
    buscarDocumentos() {
        const busqueda = $(this.config.elementos.inputBuscar).val().trim().toLowerCase();

        if (busqueda === "") {
            this.filtroActual = "";
            this.tipoFiltroActual = "titulo";
        } else {
            this.filtroActual = busqueda;
            this.tipoFiltroActual = "titulo";
        }

        this.cargarDocumentos();
    }

    /**
     * Destruir editor principal
     */
    destruirEditor() {
        if (this.editor) {
            try {
                if (this.editor.off) {
                    this.editor.off();
                }

                const container = this.editor.container;
                if (container && container.parentNode) {
                    const toolbars = container.parentNode.querySelectorAll('.ql-toolbar');
                    toolbars.forEach(toolbar => {
                        if (toolbar && toolbar.parentNode) {
                            try {
                                toolbar.parentNode.removeChild(toolbar);
                            } catch (e) {
                                console.warn('Error removiendo toolbar:', e);
                            }
                        }
                    });

                    try {
                        container.innerHTML = '';
                    } catch (e) {
                        console.warn('Error limpiando contenedor:', e);
                    }
                }

                $(this.config.elementos.editorPrincipal).empty();
                this.editor = null;
            } catch (error) {
                console.error("Error al destruir el editor:", error);
                $(this.config.elementos.editorPrincipal).empty();
                this.editor = null;
            }
        }

        const autocompleteResults = document.getElementById('autocomplete-results');
        if (autocompleteResults) {
            autocompleteResults.style.display = 'none';
        }
    }

    /**
     * Destruir editor de plantilla
     */
    destruirEditorPlantilla() {
        if (this.templateEditor) {
            try {
                if (this.templateEditor.off) {
                    this.templateEditor.off();
                }

                const container = this.templateEditor.container;
                if (container && container.parentNode) {
                    const toolbars = container.parentNode.querySelectorAll('.ql-toolbar');
                    toolbars.forEach(toolbar => {
                        if (toolbar && toolbar.parentNode) {
                            toolbar.parentNode.removeChild(toolbar);
                        }
                    });

                    if (container) {
                        while (container.firstChild) {
                            container.removeChild(container.firstChild);
                        }
                    }
                }

                $(this.config.elementos.editorPlantilla).empty();
                this.templateEditor = null;
            } catch (error) {
                console.error("Error al destruir el editor de plantilla:", error);
                $(this.config.elementos.editorPlantilla).empty();
                this.templateEditor = null;
            }
        }

        if ($("#autocomplete-results").length) {
            $("#autocomplete-results").hide();
        }
    }

    /**
     * Mostrar formulario nuevo documento
     */
    mostrarFormularioNuevo() {
        if (this.procesandoAccion) return;
        this.procesandoAccion = true;

        console.log(`Mostrando formulario de nuevo ${this.config.tipo}...`);

        this.actualizarEstadoBotones('nuevo');
        $(".vista").removeClass("active");
        $(this.config.elementos.vistaFormulario).addClass("active");

        this.limpiarFormulario();
        $(this.config.elementos.tituloPagina).text(`Nueva ${this.config.tipo.charAt(0).toUpperCase() + this.config.tipo.slice(1)}`);

        this.inicializarAutocompletarClientes();

        this.esperarPorQuill(() => {
            this.inicializarEditor();
            this.cargarPlantilla();
        });

        this.cargarTiposSelect();

        setTimeout(() => {
            this.procesandoAccion = false;
        }, 500);
    }

    /**
     * Limpiar formulario
     */
    limpiarFormulario() {
        const campos = [
            this.config.elementos.idDocumento,
            this.config.elementos.tipoDocumento,
            this.config.elementos.clienteId,
            'header_image_data',
            'footer_image_data'
        ];

        campos.forEach(campo => {
            if (campo && $(campo).length) {
                $(campo).val("");
            }
        });

        // Establecer título por defecto según el tipo de documento
        let tituloDefecto = '';
        if (this.config.documentType === 'carta') {
            tituloDefecto = 'CARTA';
        } else if (this.config.documentType === 'constancia') {
            tituloDefecto = 'CONSTANCIA';
        }
        $(this.config.elementos.tituloDocumento).val(tituloDefecto);

        // Ocultar vistas previas de imágenes
        $("#header-preview, #footer-preview").hide();
        $("#header-placeholder, #footer-placeholder").show();
        $("#cliente_info").hide();
    }

    /**
     * Esperar a que Quill esté cargado
     */
    esperarPorQuill(callback) {
        if (typeof Quill !== 'undefined') {
            this.quillLoaded = true;
            callback();
            return;
        }

        console.log("Esperando a que Quill se cargue...");
        this.cargarQuillSiNoExiste();

        const checkQuill = () => {
            return new Promise((resolve, reject) => {
                if (typeof Quill !== 'undefined') {
                    resolve();
                    return;
                }

                const timeout = setTimeout(() => {
                    reject(new Error('Tiempo de espera agotado para cargar Quill'));
                }, 5000);

                const interval = setInterval(() => {
                    if (typeof Quill !== 'undefined') {
                        clearInterval(interval);
                        clearTimeout(timeout);
                        resolve();
                    }
                }, 100);
            });
        };

        checkQuill()
            .then(() => {
                this.quillLoaded = true;
                console.log("Quill ya está disponible, continuando...");
                callback();
            })
            .catch((error) => {
                console.error(error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar el editor. Por favor, recargue la página e intente nuevamente.'
                });
            });
    }

    /**
     * Inicializar editor principal
     */
    inicializarEditor() {
        console.log("Inicializando editor Quill...");

        if ($(this.config.elementos.editorPrincipal).length === 0) {
            console.error(`Error: No se encontró el contenedor del editor ${this.config.elementos.editorPrincipal}`);
            return;
        }

        this.destruirEditor();

        try {
            $(this.config.elementos.editorPrincipal).html('');

            this.editor = new Quill(this.config.elementos.editorPrincipal, {
                modules: {
                    toolbar: [
                        [{ 'font': [] }, { 'size': [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'script': 'sub' }, { 'script': 'super' }],
                        [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }, { 'align': [] }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                },
                placeholder: `Escriba el contenido del ${this.config.tipo}...`,
                theme: 'snow'
            });

            console.log("Editor Quill inicializado correctamente");

            if (this.editor && this.editor.on) {
                this.editor.on('text-change', () => {
                    const contenidoInput = document.getElementById(this.config.elementos.contenidoDocumento.replace('#', ''));
                    if (contenidoInput) {
                        contenidoInput.value = this.editor.root.innerHTML;
                    }
                });
            } else {
                console.error("Error: El editor Quill no se inicializó correctamente");
            }
        } catch (error) {
            console.error("Error al inicializar Quill:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al inicializar el editor: ' + error.message
            });
        }
    }

    /**
     * Inicializar editor de plantilla
     */
    inicializarEditorPlantilla() {
        console.log("Inicializando editor de plantilla Quill...");

        if ($(this.config.elementos.editorPlantilla).length === 0) {
            console.error(`Error: No se encontró el contenedor del editor ${this.config.elementos.editorPlantilla}`);
            return;
        }

        this.destruirEditorPlantilla();

        try {
            $(this.config.elementos.editorPlantilla).empty();

            this.templateEditor = new Quill(this.config.elementos.editorPlantilla, {
                modules: {
                    toolbar: [
                        [{ 'font': [] }, { 'size': [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'script': 'sub' }, { 'script': 'super' }],
                        [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }, { 'align': [] }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                },
                placeholder: 'Escriba el contenido de la plantilla...',
                theme: 'snow'
            });

            console.log("Editor de plantilla Quill inicializado correctamente");

            if (this.templateEditor && this.templateEditor.on) {
                this.templateEditor.on('text-change', () => {
                    const contenidoInput = this.elementoSeguro('contenido_plantilla');
                    if (contenidoInput) {
                        contenidoInput.value = this.templateEditor.root.innerHTML;
                    }
                });

                this.templateEditor.on('editor-change', () => {
                    console.log("Editor de plantilla listo para recibir contenido");
                });
            } else {
                console.error("Error: El editor de plantilla Quill no se inicializó correctamente");
            }
        } catch (error) {
            console.error("Error al inicializar Quill para plantilla:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al inicializar el editor de plantilla: ' + error.message
            });
        }
    }

    /**
     * Inicializar autocomplete de clientes
     */
    inicializarAutocompletarClientes() {
        let timeoutId;
        let currentRequest;

        $("#cliente_search").off('input keyup');
        $("#cliente_search").removeData('autocomplete-initialized');

        if (!$("#autocomplete-results").length) {
            $("body").append('<div id="autocomplete-results" class="autocomplete-dropdown" style="display: none; position: absolute; z-index: 9999; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto;"></div>');
        }

        const $input = $("#cliente_search");
        const $results = $("#autocomplete-results");

        const buscarClientes = (query) => {
            if (currentRequest) {
                currentRequest.abort();
            }

            if (query.length < 2) {
                $results.hide();
                return;
            }

            currentRequest = $.ajax({
                url: _URL + "/ajs/buscar/cliente/datos",
                method: "GET",
                data: { term: query },
                dataType: 'json',
                success: (data) => {
                    this.mostrarResultadosClientes(data, $results, $input);
                },
                error: (xhr) => {
                    if (xhr.statusText !== 'abort') {
                        console.error("Error en búsqueda de clientes:", xhr);
                    }
                },
                complete: () => {
                    currentRequest = null;
                }
            });
        };

        $input.on('input', function() {
            const query = $(this).val().trim();
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => buscarClientes(query), 300);
        });

        $input.on('keydown', function(e) {
            if (e.key === 'Escape') {
                $results.hide();
            }
        });

        $input.on('blur', function() {
            setTimeout(() => $results.hide(), 200);
        });

        $("#btn-search-cliente").off('click').on("click", function() {
            const query = $input.val().trim();
            if (query === "") {
                $("#cliente_id").val("");
                $("#cliente_info").hide();
                $results.hide();
            } else {
                buscarClientes(query);
            }
        });

        $input.data('autocomplete-initialized', true);
    }

    /**
     * Mostrar resultados de búsqueda de clientes
     */
    mostrarResultadosClientes(items, $results, $input) {
        $results.empty();

        if (!items || items.length === 0) {
            $results.hide();
            return;
        }

        items.forEach((item) => {
            const $item = $('<div class="autocomplete-item" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">')
                .html('<strong>' + item.documento + '</strong> | ' + item.datos)
                .on('click', () => this.seleccionarCliente(item, $input, $results))
                .on('mouseenter', function() {
                    $(this).css('background-color', '#f5f5f5');
                })
                .on('mouseleave', function() {
                    $(this).css('background-color', 'white');
                });

            $results.append($item);
        });

        const inputOffset = $input.offset();
        $results.css({
            top: inputOffset.top + $input.outerHeight(),
            left: inputOffset.left,
            width: $input.outerWidth(),
            display: 'block'
        });
    }

    /**
     * Seleccionar cliente
     */
    seleccionarCliente(item, $input, $results) {
        $("#cliente_id").val(item.codigo);
        $("#cliente_nombre").text(item.datos);
        $("#cliente_documento").text("Documento: " + item.documento);
        $("#cliente_direccion").text("Dirección: " + (item.direccion || "No especificada"));
        $("#cliente_info").show();
        $input.val(item.datos);
        $results.hide();
    }

    /**
     * Elemento seguro (verificar existencia)
     */
    elementoSeguro(id) {
        const elemento = document.getElementById(id);
        if (!elemento) {
            console.warn(`Elemento con ID '${id}' no encontrado en el DOM`);
            return null;
        }
        return elemento;
    }

    /**
     * Cargar plantilla
     */
    cargarPlantilla() {
        $.ajax({
            url: this.config.urls.obtenerTemplate,
            method: "GET",
            dataType: 'json',
            success: (data) => {
                if (data.success && data.data) {
                    this.plantillaActual = data.data;

                    if (this.editor) {
                        this.editor.root.innerHTML = this.plantillaActual.contenido;
                        const contenidoInput = this.elementoSeguro(this.config.elementos.contenidoDocumento.replace('#', ''));
                        if (contenidoInput) {
                            contenidoInput.value = this.plantillaActual.contenido;
                        }
                    }

                    this.mostrarImagenesPlantilla();
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al cargar la plantilla:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Error al cargar la plantilla de ${this.config.tipo}`
                });
            }
        });
    }

    /**
     * Mostrar imágenes de la plantilla
     */
    mostrarImagenesPlantilla() {
        if (!this.plantillaActual) return;

        const headerPreview = this.elementoSeguro('header-preview');
        const footerPreview = this.elementoSeguro('footer-preview');
        const headerPlaceholder = this.elementoSeguro('header-placeholder');
        const footerPlaceholder = this.elementoSeguro('footer-placeholder');

        if (this.plantillaActual.header_image_url && headerPreview && headerPlaceholder) {
            headerPreview.src = this.plantillaActual.header_image_url;
            headerPreview.style.display = 'block';
            headerPlaceholder.style.display = 'none';
        }

        if (this.plantillaActual.footer_image_url && footerPreview && footerPlaceholder) {
            footerPreview.src = this.plantillaActual.footer_image_url;
            footerPreview.style.display = 'block';
            footerPlaceholder.style.display = 'none';
        }
    }

    /**
     * Cargar tipos en select
     */
    cargarTiposSelect(tipoSeleccionado = '') {
        $.ajax({
            url: this.config.urls.obtenerTipos,
            method: "GET",
            dataType: 'json',
            success: (data) => {
                if (data.success && data.tipos) {
                    let options = '<option value="">Seleccione un tipo</option>';
                    data.tipos.forEach((tipo) => {
                        const selected = tipo.nombre === tipoSeleccionado ? 'selected' : '';
                        options += `<option value="${tipo.nombre}" ${selected}>${tipo.nombre}</option>`;
                    });
                    $(this.config.elementos.tipoDocumento).html(options);
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al cargar tipos:", error);
            }
        });
    }

    /**
     * Editar documento
     */
    editarDocumento(id) {
        console.log(`Editando ${this.config.tipo} ID:`, id);

        this.actualizarEstadoBotones('editar');
        $(".vista").removeClass("active");
        $(this.config.elementos.vistaFormulario).addClass("active");

        $(this.config.elementos.tituloPagina).text(`Editar ${this.config.tipo.charAt(0).toUpperCase() + this.config.tipo.slice(1)}`);

        this.inicializarAutocompletarClientes();

        this.esperarPorQuill(() => {
            this.inicializarEditor();
            this.cargarDatosDocumento(id);
        });
    }

    /**
     * Cargar datos del documento
     */
    cargarDatosDocumento(id) {
        const formData = new FormData();
        const idField = this.config.tipo === 'informe' ? 'id_informe' : 'id';
        formData.append(idField, id);

        $.ajax({
            url: this.config.urls.getOne,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: (data) => {
                if (data.success && data.data) {
                    this.documentoActual = data.data;
                    this.llenarFormularioDocumento();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || `Error al cargar el ${this.config.tipo}`
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error("Error en la solicitud:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Error al cargar el ${this.config.tipo}`
                });
            }
        });
    }

    /**
     * Llenar formulario con datos del documento
     */
    llenarFormularioDocumento() {
        if (!this.documentoActual) return;

        const idField = this.config.tipo === 'informe' ? 'id_informe' : 'id';

        $(this.config.elementos.idDocumento).val(this.documentoActual[idField]);
        $(this.config.elementos.clienteId).val(this.documentoActual.id_cliente || '');

        if (this.documentoActual.id_cliente && this.documentoActual.cliente_nombre) {
            $("#cliente_search").val(this.documentoActual.cliente_nombre);
            $("#cliente_nombre").text(this.documentoActual.cliente_nombre);
            $("#cliente_documento").text("Documento: " + (this.documentoActual.cliente_documento || ""));
            $("#cliente_direccion").text("Dirección: " + (this.documentoActual.cliente_direccion || "No especificada"));
            $("#cliente_info").show();
        }

        this.cargarTiposSelect(this.documentoActual.tipo || '');
        $(this.config.elementos.tituloDocumento).val(this.documentoActual.titulo);

        // Campos específicos por tipo
        if (this.config.elementos.motivoDocumento && this.documentoActual.motivo) {
            $(this.config.elementos.motivoDocumento).val(this.documentoActual.motivo);
        }

        // Imágenes
        document.getElementById('header_image_data').value = this.documentoActual.header_image || '';
        document.getElementById('footer_image_data').value = this.documentoActual.footer_image || '';

        this.mostrarImagenesDocumento();

        if (this.editor) {
            this.editor.root.innerHTML = this.documentoActual.contenido;
            const contenidoInput = this.elementoSeguro(this.config.elementos.contenidoDocumento.replace('#', ''));
            if (contenidoInput) {
                contenidoInput.value = this.documentoActual.contenido;
            }
        }
    }

    /**
     * Mostrar imágenes del documento
     */
    mostrarImagenesDocumento() {
        if (!this.documentoActual) return;

        if (this.documentoActual.header_image_url) {
            const headerPreview = document.getElementById('header-preview');
            const headerPlaceholder = document.getElementById('header-placeholder');
            if (headerPreview && headerPlaceholder) {
                headerPreview.src = this.documentoActual.header_image_url;
                headerPreview.style.display = 'block';
                headerPlaceholder.style.display = 'none';
            }
        }

        if (this.documentoActual.footer_image_url) {
            const footerPreview = document.getElementById('footer-preview');
            const footerPlaceholder = document.getElementById('footer-placeholder');
            if (footerPreview && footerPlaceholder) {
                footerPreview.src = this.documentoActual.footer_image_url;
                footerPreview.style.display = 'block';
                footerPlaceholder.style.display = 'none';
            }
        }
    }

    /**
     * Guardar documento
     */
    async guardarDocumento() {
        const titulo = $(this.config.elementos.tituloDocumento).val().trim();
        const contenido = $(this.config.elementos.contenidoDocumento).val().trim();
        const tipo = $(this.config.elementos.tipoDocumento).val().trim();

        if (!titulo) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El título es obligatorio'
            });
            return;
        }

        if (!contenido) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El contenido es obligatorio'
            });
            return;
        }

        const formData = new FormData(document.getElementById(this.config.elementos.formulario.replace('#', '')));
        formData.set('contenido', contenido);

        const documentoId = $(this.config.elementos.idDocumento).val();
        const url = documentoId ? this.config.urls.editar : this.config.urls.insertar;

        let data = null;
        let errorSolicitud = null;
        this.mostrarCarga('Guardando', `Guardando ${this.config.tipo}...`);

        try {
            data = await $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });
        } catch (error) {
            errorSolicitud = error;
            console.error("Error en la solicitud para guardar:", error);
        } finally {
            this.cerrarCarga();
        }

        if (this.destroyed) return;

        if (errorSolicitud) {
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Error al guardar el ${this.config.tipo}`
            });
            return;
        }

        if (data && data.res) {
            await Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.msg
            });
            this.mostrarVistaLista();
            return;
        }

        await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data?.msg || `Error al guardar el ${this.config.tipo}`
        });
    }

    /**
     * Mostrar vista previa
     */
    async mostrarVistaPrevia() {
        const contenido = $(this.config.elementos.contenidoDocumento).val().trim();

        if (!contenido) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe ingresar contenido para la vista previa'
            });
            return;
        }

        const formData = new FormData();
        formData.append('titulo', $(this.config.elementos.tituloDocumento).val().trim());
        formData.append('contenido', contenido);
        formData.append('header_image', document.getElementById('header_image_data').value);
        formData.append('footer_image', document.getElementById('footer_image_data').value);

        let data = null;
        let errorSolicitud = null;
        this.mostrarCarga('Generando vista previa', 'Por favor espere...');

        try {
            data = await $.ajax({
                url: this.config.urls.vistaPrevia,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            if (data.success && data.pdfBase64) {
                document.getElementById(this.config.elementos.previewFrame.replace('#', '')).src = "data:application/pdf;base64," + data.pdfBase64;

                const modal = new bootstrap.Modal(document.getElementById(this.config.elementos.modalPreview.replace('#', '')));
                modal.show();

                document.getElementById(this.config.elementos.btnDownloadPdf.replace('#', '')).onclick = () => {
                    const blob = this.b64toBlob(data.pdfBase64, 'application/pdf');
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${this.config.tipo}_${new Date().getTime()}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                };
            }
        } catch (error) {
            errorSolicitud = error;
            console.error("Error al generar la vista previa:", error);
        } finally {
            this.cerrarCarga();
        }

        if (this.destroyed) return;

        if (errorSolicitud || !data || !data.success || !data.pdfBase64) {
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data?.msg || 'Error al generar la vista previa'
            });
        }
    }

    /**
     * Editar plantilla
     */
    editarPlantilla() {
        console.log(`Editando plantilla de ${this.config.tipo}...`);

        this.destruirEditorPlantilla();
        this.actualizarEstadoBotones('plantilla');

        const modal = new bootstrap.Modal(document.getElementById(this.config.elementos.modalPlantilla.replace('#', '')));
        modal.show();

        $(this.config.elementos.modalPlantilla).on('shown.bs.modal', () => {
            $(this.config.elementos.modalPlantilla).off('shown.bs.modal');

            this.esperarPorQuill(() => {
                this.inicializarEditorPlantilla();
                setTimeout(() => {
                    this.cargarDatosPlantilla();
                }, 200);
            });
        });
    }

    /**
     * Cargar datos de plantilla
     */
    cargarDatosPlantilla() {
        $.ajax({
            url: this.config.urls.obtenerTemplate,
            method: "GET",
            dataType: 'json',
            success: (data) => {
                if (data.success && data.data) {
                    this.plantillaActual = data.data;

                    const idPlantilla = this.elementoSeguro('id_plantilla');
                    const tituloPlantilla = this.elementoSeguro('titulo_plantilla');
                    const headerImageData = this.elementoSeguro('plantilla_header_image_data');
                    const footerImageData = this.elementoSeguro('plantilla_footer_image_data');

                    if (idPlantilla) idPlantilla.value = this.plantillaActual.id;
                    if (tituloPlantilla) tituloPlantilla.value = this.plantillaActual.titulo;
                    if (headerImageData) headerImageData.value = this.plantillaActual.header_image || '';
                    if (footerImageData) footerImageData.value = this.plantillaActual.footer_image || '';

                    this.mostrarImagenesPlantillaModal();

                    if (this.templateEditor && this.templateEditor.root) {
                        const checkEditorReady = () => {
                            if (this.templateEditor.root && this.templateEditor.root.innerHTML !== undefined) {
                                this.templateEditor.root.innerHTML = this.plantillaActual.contenido;
                                const contenidoInput = this.elementoSeguro('contenido_plantilla');
                                if (contenidoInput) {
                                    contenidoInput.value = this.plantillaActual.contenido;
                                }
                                console.log("Contenido establecido en el editor:", this.plantillaActual.contenido.substring(0, 100) + "...");
                            } else {
                                setTimeout(checkEditorReady, 100);
                            }
                        };

                        checkEditorReady();
                    } else {
                        console.warn("Editor de plantilla no está inicializado al cargar datos");
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Error al cargar la plantilla'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error("Error en la solicitud:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar la plantilla'
                });
            }
        });
    }

    /**
     * Mostrar imágenes de plantilla en modal
     */
    mostrarImagenesPlantillaModal() {
        if (!this.plantillaActual) return;

        const headerPreview = this.elementoSeguro('plantilla-header-preview');
        const footerPreview = this.elementoSeguro('plantilla-footer-preview');
        const headerPlaceholder = this.elementoSeguro('header-placeholder-plantilla');
        const footerPlaceholder = this.elementoSeguro('footer-placeholder-plantilla');

        if (this.plantillaActual.header_image_url && headerPreview && headerPlaceholder) {
            headerPreview.src = this.plantillaActual.header_image_url;
            headerPreview.style.display = 'block';
            headerPlaceholder.style.display = 'none';
        }

        if (this.plantillaActual.footer_image_url && footerPreview && footerPlaceholder) {
            footerPreview.src = this.plantillaActual.footer_image_url;
            footerPreview.style.display = 'block';
            footerPlaceholder.style.display = 'none';
        }
    }

    /**
     * Guardar plantilla
     */
    guardarPlantilla() {
        const titulo = document.getElementById('titulo_plantilla').value.trim();
        const contenido = document.getElementById('contenido_plantilla').value.trim();

        if (!titulo) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El título es obligatorio'
            });
            return;
        }

        if (!contenido) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El contenido es obligatorio'
            });
            return;
        }

        const formData = new FormData(document.getElementById(this.config.elementos.formularioPlantilla.replace('#', '')));
        formData.set('contenido', contenido);

        Swal.fire({
            title: 'Guardando',
            text: 'Guardando plantilla...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: this.config.urls.guardarTemplate,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: (data) => {
                if (data.success) {
                    this.plantillaActual = null;
                    const modalEl = document.getElementById(this.config.elementos.modalPlantilla.replace('#', ''));
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    const afterHide = () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.mensaje || 'Plantilla guardada correctamente'
                        }).then(() => this.mostrarVistaLista());
                    };
                    if (modal) {
                        $(modalEl).one('hidden.bs.modal', afterHide);
                        modal.hide();
                    } else {
                        afterHide();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.msg || 'Error al guardar la plantilla'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error("Error en la solicitud:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la plantilla'
                });
            }
        });
    }

    /**
     * Mostrar vista previa de plantilla
     */
    mostrarVistaPreviewPlantilla() {
        const modalPlantilla = bootstrap.Modal.getInstance(document.getElementById(this.config.elementos.modalPlantilla.replace('#', '')));
        if (modalPlantilla) {
            modalPlantilla.hide();
        }

        $(this.config.elementos.modalPlantilla).on('hidden.bs.modal', () => {
            $(this.config.elementos.modalPlantilla).off('hidden.bs.modal');

            let contenidoActual = '';
            let tituloActual = 'Vista Previa de Plantilla';

            if (this.templateEditor && this.templateEditor.root) {
                contenidoActual = this.templateEditor.root.innerHTML;
            }

            const tituloInput = this.elementoSeguro('titulo_plantilla');
            if (tituloInput && tituloInput.value.trim()) {
                tituloActual = tituloInput.value.trim();
            }

            Swal.fire({
                title: 'Generando vista previa',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: this.config.urls.vistaPrevia,
                method: "POST",
                data: {
                    titulo: tituloActual,
                    contenido: contenidoActual
                },
                dataType: 'json',
                success: (data) => {
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        document.getElementById(this.config.elementos.previewFrame.replace('#', '')).src = "data:application/pdf;base64," + data.pdfBase64;
                        const modal = new bootstrap.Modal(document.getElementById(this.config.elementos.modalPreview.replace('#', '')));
                        modal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al generar la vista previa'
                        });
                    }
                },
                error: (xhr, status, error) => {
                    Swal.close();
                    console.error("Error en vista previa:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al generar la vista previa'
                    });
                }
            });
        });
    }

    /**
     * Gestionar membretes
     */
    gestionarMembretes() {
        console.log("Gestionando membretes...");

        this.cargarDatosMembretes();

        const modal = new bootstrap.Modal(document.getElementById(this.config.elementos.modalMembretes.replace('#', '')));
        modal.show();
    }

    /**
     * Cargar datos de membretes
     */
    cargarDatosMembretes() {
        $.ajax({
            url: this.config.urls.obtenerMembretes,
            method: "GET",
            dataType: 'json',
            success: (data) => {
                if (data.success && data.data) {
                    const membretes = data.data;

                    document.getElementById('membrete_header_image_data').value = membretes.header_image || '';
                    document.getElementById('membrete_footer_image_data').value = membretes.footer_image || '';

                    if (membretes.header_image_url) {
                        // Construir URL absoluta si es una ruta relativa
                        let headerUrl = membretes.header_image_url;
                        if (headerUrl && !headerUrl.startsWith('http') && !headerUrl.startsWith('data:')) {
                            // Extraer solo el dominio de _URL: http://indus.jvc/documentos/cartas -> http://indus.jvc
                            const urlParts = _URL.split('/');
                            const baseUrl = urlParts[0] + '//' + urlParts[2]; // http://indus.jvc
                            headerUrl = baseUrl + '/' + headerUrl.replace(/^\/+/, '');
                        }
                        document.getElementById('membrete-header-preview').src = headerUrl;
                        document.getElementById('membrete-header-preview').style.display = 'block';
                        document.getElementById('header-placeholder-membrete').style.display = 'none';
                    } else {
                        document.getElementById('membrete-header-preview').style.display = 'none';
                        document.getElementById('header-placeholder-membrete').style.display = 'block';
                    }

                    if (membretes.footer_image_url) {
                        // Construir URL absoluta si es una ruta relativa
                        let footerUrl = membretes.footer_image_url;
                        if (footerUrl && !footerUrl.startsWith('http') && !footerUrl.startsWith('data:')) {
                            // Extraer solo el dominio de _URL: http://indus.jvc/documentos/cartas -> http://indus.jvc
                            const urlParts = _URL.split('/');
                            const baseUrl = urlParts[0] + '//' + urlParts[2]; // http://indus.jvc
                            footerUrl = baseUrl + '/' + footerUrl.replace(/^\/+/, '');
                        }
                        document.getElementById('membrete-footer-preview').src = footerUrl;
                        document.getElementById('membrete-footer-preview').style.display = 'block';
                        document.getElementById('footer-placeholder-membrete').style.display = 'none';
                    } else {
                        document.getElementById('membrete-footer-preview').style.display = 'none';
                        document.getElementById('footer-placeholder-membrete').style.display = 'block';
                    }
                }
            },
            error: (xhr, status, error) => {
                console.error("Error al cargar membretes:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cargar los membretes'
                });
            }
        });
    }

    /**
     * Guardar membretes
     */
    guardarMembretes() {
        const formData = new FormData(document.getElementById(this.config.elementos.formularioMembretes.replace('#', '')));

        const headerFile = document.getElementById(this.config.elementos.headerImageInput.replace('#', '')).files[0];
        const footerFile = document.getElementById(this.config.elementos.footerImageInput.replace('#', '')).files[0];

        if (headerFile) {
            formData.append('header_image_file', headerFile);
        }

        if (footerFile) {
            formData.append('footer_image_file', footerFile);
        }

        const headerData = document.getElementById('membrete_header_image_data').value;
        const footerData = document.getElementById('membrete_footer_image_data').value;

        if (headerData && headerData.trim() !== '') {
            formData.append('header_image', headerData);
        }

        if (footerData && footerData.trim() !== '') {
            formData.append('footer_image', footerData);
        }

        Swal.fire({
            title: 'Guardando',
            text: 'Guardando membretes...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: this.config.urls.guardarMembretes,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: (data) => {
                if (data.success) {
                    this.plantillaActual = null;
                    const modalEl = document.getElementById(this.config.elementos.modalMembretes.replace('#', ''));
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    const afterHide = () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.mensaje || 'Membretes guardados correctamente'
                        }).then(() => this.mostrarVistaLista());
                    };
                    if (modal) {
                        $(modalEl).one('hidden.bs.modal', afterHide);
                        modal.hide();
                    } else {
                        afterHide();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.msg || 'Error al guardar los membretes'
                    });
                }
            },
            error: (xhr, status, error) => {
                console.error("Error en la solicitud:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión al guardar los membretes'
                });
            }
        });
    }

    /**
     * Mostrar vista previa de membretes
     */
    mostrarVistaPreviewMembretes() {
        window.regresarAMembretes = true;

        const modalMembretes = bootstrap.Modal.getInstance(document.getElementById(this.config.elementos.modalMembretes.replace('#', '')));
        if (modalMembretes) {
            modalMembretes.hide();
        }

        $(this.config.elementos.modalMembretes).on('hidden.bs.modal', () => {
            $(this.config.elementos.modalMembretes).off('hidden.bs.modal');

            const headerImageData = document.getElementById('membrete_header_image_data').value;
            const footerImageData = document.getElementById('membrete_footer_image_data').value;

            Swal.fire({
                title: 'Generando vista previa',
                text: 'Por favor espere...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('titulo', 'Vista Previa de Membretes');
            formData.append('contenido', 'Contenido de ejemplo para mostrar los membretes configurados.');

            if (headerImageData && headerImageData.trim() !== '') {
                formData.append('header_image', headerImageData);
            }
            if (footerImageData && footerImageData.trim() !== '') {
                formData.append('footer_image', footerImageData);
            }

            $.ajax({
                url: this.config.urls.vistaPrevia,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: (data) => {
                    Swal.close();

                    if (data.success && data.pdfBase64) {
                        document.getElementById(this.config.elementos.previewFrame.replace('#', '')).src = "data:application/pdf;base64," + data.pdfBase64;
                        const modal = new bootstrap.Modal(document.getElementById(this.config.elementos.modalPreview.replace('#', '')));
                        modal.show();
                    } else {
                        window.regresarAMembretes = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'Error al generar la vista previa'
                        });
                    }
                },
                error: (xhr, status, error) => {
                    Swal.close();
                    window.regresarAMembretes = false;
                    console.error("Error en vista previa:", status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al generar la vista previa'
                    });
                }
            });
        });
    }

    /**
     * Eliminar documento
     */
    eliminarDocumento(id) {
        $(this.config.elementos.btnConfirmarEliminar).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...').prop('disabled', true);

        const formData = new FormData();
        const idField = this.config.tipo === 'informe' ? 'id_informe' : 'id';
        formData.append(idField, id);

        $.ajax({
            url: this.config.urls.borrar,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: (data) => {
                $(this.config.elementos.modalEliminar).modal('hide');

                if (data.res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.msg
                    }).then(() => {
                        this.cargarDocumentos();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.msg || `Error al eliminar el ${this.config.tipo}`
                    });
                }

                $(this.config.elementos.btnConfirmarEliminar).html('Eliminar').prop('disabled', false);
            },
            error: (xhr, status, error) => {
                console.error("Error en la solicitud:", status, error);
                $(this.config.elementos.modalEliminar).modal('hide');

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `Error al eliminar el ${this.config.tipo}`
                });

                $(this.config.elementos.btnConfirmarEliminar).html('Eliminar').prop('disabled', false);
            }
        });
    }

    /**
     * Manejar cambio de imagen
     */
    manejarCambioImagen(event, inputId, previewId, placeholderId) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                const hiddenInput = document.getElementById(inputId);
                if (hiddenInput) {
                    hiddenInput.value = e.target.result;
                }

                const previewImg = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);

                if (previewImg) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                }

                if (placeholder) {
                    placeholder.style.display = 'none';
                }

                console.log('Imagen cargada correctamente:', inputId);
            };

            reader.readAsDataURL(file);
        }
    }

    /**
     * Restablecer imagen
     */
    restablecerImagen(inputId, previewId, placeholderId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).style.display = 'none';

        if (placeholderId) {
            document.getElementById(placeholderId).style.display = 'block';
        }
    }

    /**
     * Renderizar vista previa de PDF
     */
    async renderPdfPreview(pdfUrl, canvasId) {
        if (this.pdfsRenderizados.has(canvasId)) {
            console.log(`Renderizado de ${canvasId} ya en progreso. Omitiendo.`);
            return;
        }

        console.log('Renderizando PDF:', pdfUrl, 'en canvas:', canvasId);
        this.pdfsRenderizados.add(canvasId);
        let loadingTask = null;
        let renderTask = null;
        let pdf = null;
        let page = null;

        try {
            const pdfjsLib = window.pdfJsReady
                ? await window.pdfJsReady
                : window.pdfjsLib;

            if (!pdfjsLib || typeof pdfjsLib.getDocument !== 'function') {
                throw new Error('PDF.js no está disponible');
            }

            loadingTask = pdfjsLib.getDocument({ url: pdfUrl });
            this.pdfLoadingTasks.set(canvasId, loadingTask);
            pdf = await loadingTask.promise;
            page = await pdf.getPage(1);

            const canvas = document.getElementById(canvasId);
            if (!canvas || this.destroyed || (this.rootElement && !this.rootElement.contains(canvas))) {
                throw new Error(`Canvas no disponible: ${canvasId}`);
            }

            const context = canvas.getContext('2d');
            const container = canvas.parentElement;
            canvas.width = container.clientWidth * 2;
            canvas.height = container.clientHeight * 2;

            const viewport = page.getViewport({ scale: 1.0 });
            const scaledViewport = page.getViewport({ scale: canvas.width / viewport.width });
            const renderContext = {
                canvasContext: context,
                viewport: scaledViewport,
                transform: [1, 0, 0, 1, (canvas.width - scaledViewport.width) / 2, 0],
                intent: 'display'
            };

            context.fillStyle = 'white';
            context.fillRect(0, 0, canvas.width, canvas.height);

            renderTask = page.render(renderContext);
            this.pdfRenderTasks.set(canvasId, renderTask);
            await renderTask.promise;
            console.log(`PDF de ${this.config.tipo} renderizado correctamente en`, canvasId);
        } catch (error) {
            if (!this.destroyed) {
                console.error(`No se pudo renderizar el PDF de ${this.config.tipo}:`, error);
                this.mostrarErrorPdf(canvasId, pdfUrl);
            }
        } finally {
            this.pdfsRenderizados.delete(canvasId);
            this.pdfLoadingTasks.delete(canvasId);
            this.pdfRenderTasks.delete(canvasId);

            try {
                page?.cleanup();
                await pdf?.cleanup();
                await loadingTask?.destroy();
            } catch (cleanupError) {
                if (!this.destroyed) {
                    console.warn(`No se pudo liberar completamente ${canvasId}:`, cleanupError);
                }
            }
        }
    }

    mostrarErrorPdf(canvasId, pdfUrl) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !canvas.parentNode || (this.rootElement && !this.rootElement.contains(canvas))) return;

        const fallback = document.createElement('div');
        fallback.className = 'text-center p-4';

        const icon = document.createElement('i');
        icon.className = 'fas fa-file-pdf fa-4x text-danger';

        const message = document.createElement('p');
        message.className = 'mt-2 mb-2';
        message.textContent = 'No se pudo cargar la vista previa.';

        const link = document.createElement('a');
        link.className = 'btn btn-sm btn-outline-primary';
        link.href = pdfUrl;
        link.target = '_blank';
        link.rel = 'noopener';
        link.textContent = 'Abrir PDF';

        fallback.append(icon, message, link);
        canvas.parentNode.replaceChildren(fallback);
    }


    /**
     * Convertir base64 a Blob
     */
    b64toBlob(b64Data, contentType = '', sliceSize = 512) {
        const byteCharacters = atob(b64Data);
        const byteArrays = [];

        for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
            const slice = byteCharacters.slice(offset, offset + sliceSize);
            const byteNumbers = new Array(slice.length);

            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            const byteArray = new Uint8Array(byteNumbers);
            byteArrays.push(byteArray);
        }

        const blob = new Blob(byteArrays, { type: contentType });
        return blob;
    }

    /**
     * Limpia el módulo y resetea el estado
     */
    cleanup() {
        console.log(`Limpiando módulo ${this.config.tipo}...`);
        if (this.destroyed) return;

        const rootElement = this.rootElement;
        const modalElements = [...this.modalElements];
        const backdrops = Array.from(document.querySelectorAll('.modal-backdrop'));
        const perteneceAInstancia = (element) => element?.__documentosUtilsOwner === this;

        this.destroyed = true;
        clearTimeout(this.timeoutBusqueda);

        this.pdfRenderTasks.forEach((task) => task.cancel());
        this.pdfLoadingTasks.forEach((task) => {
            task.destroy().catch((error) => console.warn('Error cancelando carga PDF:', error));
        });

        Object.values(this.config.elementos).forEach((selector) => {
            if (typeof selector === 'string') {
                $(selector).off(this.eventNamespace);
            }
        });

        // Destruir editores
        this.destruirEditor();
        this.destruirEditorPlantilla();
        
        // Limpiar formularios
        this.limpiarFormulario();
        
        // Resetear arrays
        this.documentos = [];
        this.filtroActual = '';
        
        // Limpiar objetos actuales
        this.documentoActual = null;
        this.plantillaActual = null;
        
        // Limpiar PDFs renderizados
        if (this.pdfsRenderizados) {
            this.pdfsRenderizados.clear();
        }
        this.pdfLoadingTasks.clear();
        this.pdfRenderTasks.clear();

        // Ocultar solo los nodos capturados y todavía pertenecientes a esta instancia.
        modalElements.forEach((element) => {
            if (!perteneceAInstancia(element)) return;

            const modal = window.bootstrap?.Modal?.getInstance(element);
            if (modal) {
                modal.hide();
            } else {
                $(element).modal('hide');
            }

            element.classList.remove('show');
            element.style.display = 'none';
            element.setAttribute('aria-hidden', 'true');
            element.removeAttribute('aria-modal');
        });

        this.cerrarCarga();
        $(`.swal2-container[data-documentos-owner="${this.config.tipo}"]`).remove();
        $('#loader-menor').hide();

        setTimeout(() => {
            const rootSigueSiendoPropio = perteneceAInstancia(rootElement);

            modalElements.forEach((element) => {
                if (!rootSigueSiendoPropio || !element.isConnected || !perteneceAInstancia(element)) return;

                element.classList.remove('show');
                element.style.display = 'none';
                element.setAttribute('aria-hidden', 'true');
                element.removeAttribute('aria-modal');
            });

            if (!document.querySelector('.modal.show')) {
                backdrops.forEach((backdrop) => {
                    if (backdrop.isConnected) backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            }
        }, 350);
        
        // Limpiar previews de imágenes
        $('.imagen-preview').attr('src', '').hide();
        $('.imagen-placeholder').show();
        
        // Resetear inputs de archivos
        $('input[type="file"]').val('');

        window[`${this.config.tipo}ModuleInitialized`] = false;
        if (window[`${this.config.tipo}ModuleInstance`] === this) {
            window[`${this.config.tipo}ModuleInstance`] = null;
        }
        if (this.config.tipo === 'carta' && window.cartasInitializationRoot === this.rootElement) {
            window.cartasInitializationPromise = null;
            window.cartasInitializationRoot = null;
        }

        console.log(`Módulo ${this.config.tipo} limpiado`);
    }

    /**
     * Reinicia completamente el módulo
     */
    reiniciar() {
        console.log(`Reiniciando módulo ${this.config.tipo}...`);
        if (this.destroyed) return null;

        const reinicializar = this.config.reinicializar;
        this.cleanup();

        if (typeof reinicializar === 'function') {
            return reinicializar();
        }

        const nuevaInstancia = new DocumentosUtils(this.config);
        window[`${this.config.tipo}ModuleInstance`] = nuevaInstancia;
        return nuevaInstancia;
    }
}

// Exportar la clase para uso global
window.DocumentosUtils = DocumentosUtils;
''
