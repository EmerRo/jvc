// Variables globales para el control de vistas
    let currentView = 'table'; // 'table' o 'grid'
    let gridProducts = [];
    let currentPage = 1;
    let productsPerPage = 12;
    let totalProducts = 0;
    let searchTerm = '';
    let arrayIdsOkUsar = [];

    function descarFunccc() {
        window.open(_URL +
            `/reporte/producto/excel?texto=${$("#datatable_filter input").val()}`)
    }

    var codProdT = ''
    /*
    async function printBarcode() {
        try {
            const printer = await EscPosPrinter.requestPrinter();
 
            // Conectar a la impresora
            await printer.connect();
 
            // Configurar el tama├▒o del ticket (50 mm x 25 mm)
            await printer.setPageFormat(50, 25);
 
            // Imprimir el t├¡tulo
            await printer.printText('Barcode Title\n');
 
            // Generar el c├│digo de barras utilizando JsBarcode
            const svgData = JsBarcode.generateSvg('123456789', {
                format: 'CODE128',
                displayValue: true,
            });
 
            // Imprimir el c├│digo de barras
            await printer.printImage(svgData);
 
            // Cortar el ticket
            await printer.cut();
 
            // Desconectar la impresora
            await printer.disconnect();
        } catch (error) {
            console.error(error);
        }
    }
    */

    function imprimir2() {
        window.open(_URL + "/ge/bar/code2?code=" + codeBarraTemps + "&nombre=" + nombreBarraTemps + "&scal=" + $("#scalimg").val(), "_blank");
    }

    function imprimir() {
        window.open(_URL + "/ge/bar/code?code=" + codeBarraTemps + "&nombre=" + nombreBarraTemps + "&scal=" + $("#scalimg").val(), "_blank");
    }

    function abrirModalBarras(e, n = '') {
        e = e.trim();
        console.log(e);
        nombreBarraTemps = n;
        codeBarraTemps = e; // Usar el c├│digo del producto

        // Primero mostrar el modal
        $('#modalCodigoBarras').modal('show');

        // Esperar a que el modal est├® completamente visible antes de generar el c├│digo de barras
        $('#modalCodigoBarras').on('shown.bs.modal', function () {
            setTimeout(function () {
                try {
                    JsBarcode("#idCodigoBarras", e); // Usar el c├│digo del producto
                } catch (error) {
                    console.error("Error al generar c├│digo de barras:", error);
                }
            }, 100);
        });
    }

    function clearSelection() {
        // Limpiar array de IDs seleccionados
        arrayIdsOkUsar = [];

        // Desmarcar todos los checkboxes
        $('.btnCheckEliminar').prop('checked', false);
        $('.btnSeleccionarTodos').prop('checked', false);
        $('.btnSeleccionarTodosGrid').text('Seleccionar Todos').removeClass('btn-warning').addClass('border-rojo bg-white');

        // Eliminar datos guardados en localStorage
        localStorage.removeItem('idChecks');
    }

    // Funci├│n para cargar productos en vista grid
    function loadGridProducts(page = 1, search = '') {
        $('#loading-grid').addClass('active');
        $('#products-container').empty();

        const filter = $('#filtroProductos').val() || $('#filtroProductos-mobile').val() || ''



        _ajax("/ajs/data/productos/grid", "POST", {
            almacenId: almacenCod,
            page: page,
            limit: productsPerPage,
            search: search,
            filter: filter
        }, function (resp) {
            $('#loading-grid').removeClass('active');

            if (resp.res) {
                gridProducts = resp.data;
                totalProducts = resp.total;
                currentPage = page;

                renderGridProducts();
                renderGridPagination();
            } else {
                alertAdvertencia("Error al cargar productos");
            }
        });
    }

    // Funci├│n para renderizar productos en grid
    function renderGridProducts() {
        const container = $('#products-container');
        container.empty();

        if (gridProducts.length === 0) {
            container.html(`
                <div class="col-12 text-center" style="grid-column: 1 / -1;">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        No se encontraron productos.
                    </div>
                </div>
            `);
            return;
        }

        gridProducts.forEach(product => {
            const stockClass = getStockClass(product.cantidad);
            const imageUrl = product.imagen ?
                `${_URL}/img/productos/${product.imagen}` :
                null;

            const isChecked = arrayIdsOkUsar.some(item => item.id === product.id_producto);

            const productCard = `
                <div class="product-card" data-product-id="${product.id_producto}">
                    <div class="product-checkbox">
                        <input type="checkbox" class="btnCheckEliminar" data-id="${product.id_producto}" ${isChecked ? 'checked' : ''}>
                    </div>
                    <div class="product-image-container">
                        ${imageUrl ?
                    `<img src="${imageUrl}" alt="${product.nombre}" class="product-image">` :
                    `<div class="no-image-placeholder">
                                <i class="fa fa-image"></i>
                                <span>Sin imagen</span>
                            </div>`
                }
                    </div>
                    <div class="product-info">
                        <a href="javascript:abrirModalBarras('${product.codigo}','${product.nombre}')" class="product-code">
                            ${product.codigo}
                        </a>
                        <h6 class="product-name" title="${product.nombre}">
                            ${product.nombre}
                        </h6>
                        <div class="product-details">
                            <span class="product-unit">${product.unidad_nombre || 'N/A'}</span>
                            <span class="product-stock ${stockClass}">
                                <i class="fa fa-cubes me-1"></i>${product.cantidad}
                            </span>
                        </div>
                        <div class="product-price">
                            S/ ${parseFloat(product.precio || 0).toFixed(2)}
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-edit-product bg-rojo btn-edt" data-item="${product.id_producto}">
                                <i class="fa fa-edit me-1"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            `;

            container.append(productCard);
        });
    }

    // Funci├│n para obtener la clase de color seg├║n el stock
    function getStockClass(cantidad) {
        const stock = parseInt(cantidad);
        if (stock <= 5) return 'low-stock';
        if (stock <= 20) return 'medium-stock';
        return '';
    }

    // Funci├│n para renderizar paginaci├│n
    function renderGridPagination() {
        const totalPages = Math.ceil(totalProducts / productsPerPage);
        const pagination = $('#grid-pagination');
        pagination.empty();

        if (totalPages <= 1) return;

        // Bot├│n anterior
        if (currentPage > 1) {
            pagination.append(`
                <button class="btn border-rojo bg-white" onclick="loadGridProducts(${currentPage - 1}, '${searchTerm}')">
                    <i class="fa fa-chevron-left"></i> Anterior
                </button>
            `);
        }

        // P├íginas
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            pagination.append(`<button class="btn border-rojo bg-white" onclick="loadGridProducts(1, '${searchTerm}')">1</button>`);
            if (startPage > 2) {
                pagination.append(`<span class="mx-2">...</span>`);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === currentPage ? 'bg-rojo text-white' : 'border-rojo bg-white';
            pagination.append(`
                <button class="btn ${activeClass}" onclick="loadGridProducts(${i}, '${searchTerm}')">${i}</button>
            `);
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pagination.append(`<span class="mx-2">...</span>`);
            }
            pagination.append(`<button class="btn border-rojo bg-white" onclick="loadGridProducts(${totalPages}, '${searchTerm}')">${totalPages}</button>`);
        }

        // Bot├│n siguiente
        if (currentPage < totalPages) {
            pagination.append(`
                <button class="btn border-rojo bg-white" onclick="loadGridProducts(${currentPage + 1}, '${searchTerm}')">
                    Siguiente <i class="fa fa-chevron-right"></i>
                </button>
            `);
        }

        // Info de resultados
        const startItem = (currentPage - 1) * productsPerPage + 1;
        const endItem = Math.min(currentPage * productsPerPage, totalProducts);
        pagination.append(`
            <div class="ms-3 text-muted">
                Mostrando ${startItem} - ${endItem} de ${totalProducts} productos
            </div>
        `);
    }

    var nombreBarraTemps = ''
    var codeBarraTemps = ''
    var datatable
    var almacenCod = window._almacenPrincipal || '1';

    $(document).ready(function () {
        const app = new Vue({
            el: "#conte-vue-modals",
            data: {
                almacen: window._almacenPrincipal || 1,
                t: 0,
                listaProd: [],
                almacenImportacion: 1, // Almac├®n por defecto para importaci├│n
                buscarProductoImport: '', // Buscador en modal de importaci├│n
                modoEdicion: false, // Controla si la tabla est├í en modo edici├│n
                restock: {
                    cod: '',
                    cantidad: '',
                },
                precios: [],
                preciosNuevos: [],
                reg: {
                    nombre: '',
                    precio: '0',
                    costo: '0',
                    cantidad: '0',
                    codSunat: '',
                    afecto: '0',
                    ruc: '',
                    razon: '',
                    precio1: 0,
                    precio2: 0,
                    precio3: 0,
                    precio4: 0,
                    codigo: 0,
                    imagen: '',
                    detalle: '',
                    categoria: '',
                    almacen: 1,
                    unidad: '',
                    moneda: 'PEN',
                    usar_multiprecio: false,
                },
                edt: {
                    nombre: '',
                    cod_prod: '',
                    cod: '',
                    precio: '0',
                    costo: '0',
                    codSunat: '',
                    afecto: '0',
                    usar_barra: '0',
                    ruc: '',
                    razon: '',
                    precioMayor: '',
                    precioMenor: '',
                    precio2: '',
                    precio3: '',
                    precio4: '',
                    precio_unidad: '',
                    codigo: '',
                    cantidad: '',
                    detalle: '',
                    categoria: '',
                    almacen: '',
                    unidad: '',
                    moneda: 'PEN',
                    usar_multiprecio: false,
                },
                listaIdsss: [],
                units: [], // Will store available units
                imagePreview: null,
                stockData: {
                    producto_id: '',
                    stock_actual: '',
                    cantidad_ingresar: '',
                    producto_nombre: '',
                    costo_compra: '',
                    moneda: 'PEN',
                    observaciones: ''
                },
                disminuirData: {
                    producto_id: '',
                    stock_actual: '',
                    cantidad_disminuir: '',
                    producto_nombre: '',
                    unidad: '',
                    observaciones: ''
                },
                trasladoData: {
                    almacen_origen: '',
                    almacen_destino: '',
                    producto_id: '',
                    productos: [],
                    nota: ''
                },
                historialStock: [],
                almacenes: [],
                nuevoAlmacen: '',
            },
            mounted() {
                this.cargarAlmacenes();
            },
            computed: {
                // S├¡mbolos de moneda para el formulario de agregar
                simboloMonedaReg() {
                    return this.reg.moneda === 'USD' ? '$' : 'S/';
                },
                // S├¡mbolos de moneda para el formulario de editar
                simboloMonedaEdt() {
                    return this.edt.moneda === 'USD' ? '$' : 'S/';
                },
                // Filtrar productos para importaci├│n
                productosFiltrados() {
                    if (!this.buscarProductoImport) {
                        return this.listaProd;
                    }
                    const busqueda = this.buscarProductoImport.toLowerCase();
                    return this.listaProd.filter(item => {
                        return (item.producto && item.producto.toLowerCase().includes(busqueda)) ||
                            (item.descripcicon && item.descripcicon.toLowerCase().includes(busqueda)) ||
                            (item.codigoProd && item.codigoProd.toLowerCase().includes(busqueda));
                    });
                }
            },
            methods: {
                cargarAlmacenes() {
                    var self = this;
                    _get('/ajs/almacenes/listar', function(res) {
                        if (res.estado) {
                            self.almacenes = res.almacenes;
                        }
                    });
                },
                guardarAlmacen() {
                    if (!this.nuevoAlmacen.trim()) {
                        alertAdvertencia('Ingrese el nombre del almac├®n');
                        return;
                    }
                    var self = this;
                    _post('/ajs/almacenes/agregar', { nombre: this.nuevoAlmacen.trim() }, function(res) {
                        if (res.estado) {
                            alertExito(res.mensaje);
                            self.nuevoAlmacen = '';
                            $('#modal-agregar-almacen').modal('hide');
                            self.cargarAlmacenes();
                        } else {
                            alertAdvertencia(res.mensaje);
                        }
                    });
                },
                toggleModoEdicion() {
                    // Mostrar loader genial con color rojo
                    Swal.fire({
                        title: this.modoEdicion ? 'Cambiando a modo vista...' : 'Activando modo edici├│n...',
                        html: `
                            <div style="display: flex; justify-content: center; align-items: center; padding: 20px;">
                                <div style="
                                    width: 60px;
                                    height: 60px;
                                    border: 6px solid #f3f3f3;
                                    border-top: 6px solid #CA3438;
                                    border-radius: 50%;
                                    animation: spin 1s linear infinite;
                                "></div>
                            </div>
                            <style>
                                @keyframes spin {
                                    0% { transform: rotate(0deg); }
                                    100% { transform: rotate(360deg); }
                                }
                            </style>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: 'swal-loader-custom'
                        }
                    });

                    // Usar setTimeout para permitir que el DOM se actualice
                    setTimeout(() => {
                        this.modoEdicion = !this.modoEdicion;

                        // Cerrar el loader despu├®s de un breve momento
                        setTimeout(() => {
                            Swal.close();
                        }, 300);
                    }, 100);
                },
                agregarPrecio() {
                    this.precios.push({
                        nombre: '',
                        precio: '0.00'
                    });
                },

                eliminarPrecio(index) {
                    this.precios.splice(index, 1);
                },

                // M├®todos para el modal de agregar
                agregarPrecioNuevo() {
                    this.preciosNuevos.push({
                        nombre: '',
                        precio: '0.00'
                    });
                },

                eliminarPrecioNuevo(index) {
                    this.preciosNuevos.splice(index, 1);
                },
                agregarIds() {
                    this.t = 5
                    console.log(this.listaIdsss);
                    this.listaIdsss.push({
                        id: 20
                    })
                    console.log(this.listaIdsss);
                },
                agregarPrecios() {
                    const data = {
                        ...this.edt
                    }
                    _ajax("/ajs/data/producto/edt/precios", "POST", data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                alertExito("Actualizado")
                                    .then(function () {
                                        location.reload()
                                    })
                            } else {
                                alertAdvertencia("No se pudo actualizar")
                            }
                        }
                    )
                },
                changeAlmacen(event) {
                    // Limpiar checkboxes y localStorage
                    clearSelection();
                    $('.filter-option').prop('checked', false);

                    // Actualizar el almac├®n seleccionado
                    almacenCod = event.target.value;

                    if (currentView === 'table') {
                        // Destruir la tabla actual
                        if ($.fn.DataTable.isDataTable('#datatable')) {
                            datatable.destroy();
                        }

                        // Reinicializar DataTable con la nueva configuraci├│n
                        initializeDataTable();
                    } else {
                        // Recargar vista grid
                        loadGridProducts(1, searchTerm);
                    }
                },
                edtGenerarCodeBarra() {
                    // Usar $nextTick para asegurar que el DOM se ha actualizado
                    this.$nextTick(() => {
                        setTimeout(() => {
                            if (document.getElementById("barcode")) {
                                try {
                                    // CAMBIO AQU├ì: Usar el c├│digo del producto
                                    JsBarcode("#barcode", this.edt.codigo);
                                } catch (error) {
                                    console.error("Error al generar c├│digo de barras:", error);
                                }
                            } else {
                                console.warn("Elemento #barcode no encontrado en el DOM");
                            }
                        }, 100);
                    });
                },
                agregarListaImport() {
                    if (this.listaProd.length > 0) {
                        _ajax("/ajs/data/producto/add/lista", "POST", {
                            lista: JSON.stringify(this.listaProd),
                            almacen: this.almacenImportacion
                        },
                            function (resp) {
                                console.log(resp);
                                if (resp.res) {
                                    alertExito("Productos agregados exitosamente")
                                        .then(function () {
                                            $("#modal-lista-productos").modal("hide");
                                            if (currentView === 'table') {
                                                datatable.ajax.reload(null, false);
                                            } else {
                                                loadGridProducts(currentPage, searchTerm);
                                            }
                                        });
                                } else {
                                    let errorMsg = resp.error ? resp.error : "No se pudo agregar la lista de productos";
                                    alertAdvertencia(errorMsg);
                                    console.error("Error al agregar productos:", resp.error);
                                }
                            })
                    } else {
                        alertAdvertencia("La lista est├í vac├¡a");
                    }
                },
                ChangeconsultarDocRUC() {
                    if (this.reg.ruc.length == 11) {
                        this.getInfoDoc2();
                    } else {
                        this.reg.ruc = ''
                    }
                },
                consultarDocRUC() {
                    if (this.reg.ruc.length == 11) {
                        this.getInfoDoc2();
                    } else if (this.edt.ruc.length == 11) {
                        this.getInfoDoc3();
                    } else {
                        alertAdvertencia("El RUC es de 11 d├¡gitos")
                    }
                },
                getInfoDoc2() {
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/doc/cliente", "POST", {
                        doc: this.reg.ruc
                    },
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                app._data.reg.razon = resp.data.razon_social;
                                app._data.reg.direccion = resp.data.direccion;
                                app._data.reg.distrito = resp.data.distrito;
                                app._data.reg.provincia = resp.data.provincia;
                                app._data.reg.departamento = resp.data.departamento;
                                app._data.reg.ubigeo = resp.data.ubigeo;
                            } else {
                                alertAdvertencia("Documento no encontrado")
                            }
                        }
                    )
                },
                getInfoDoc3() {
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/doc/cliente", "POST", {
                        doc: this.edt.ruc
                    },
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                app._data.edt.razon = resp.data.razon_social;
                                app._data.edt.direccion = resp.data.direccion;
                                app._data.edt.distrito = resp.data.distrito;
                                app._data.edt.provincia = resp.data.provincia;
                                app._data.edt.departamento = resp.data.departamento;
                                app._data.edt.ubigeo = resp.data.ubigeo;
                            } else {
                                alertAdvertencia("Documento no encontrado")
                            }
                        }
                    )
                },
                eliminarItemTablaPro(index) {
                    this.listaProd.splice(index, 1)
                },
                agregarStock() {
                    const data = {
                        ...this.restock
                    }
                    _ajax("/ajs/data/producto/restock", "POST", data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                alertExito("Actualizado")
                                    .then(function () {
                                        location.reload()
                                    })
                            } else {
                                alertAdvertencia("No se pudo actualizar")
                            }
                        }
                    )
                },
                cargarHistorialStock() {
                    // Destruir DataTable si ya existe
                    if ($.fn.DataTable.isDataTable('#tabla-historial-stock')) {
                        const table = $('#tabla-historial-stock').DataTable();
                        table.destroy();
                        // Limpiar completamente
                        $('#tabla-historial-stock').empty();
                    }

                    // Reconstruir la estructura de la tabla
                    $('#tabla-historial-stock').html(`
                        <thead class="table-light">
                            <tr>
                                <th>C├│digo</th>
                                <th>Producto</th>
                                <th>Movimiento</th>
                                <th>Cantidad</th>
                                <th>Costo</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    `);

                    // Inicializar DataTable
                    const table = $('#tabla-historial-stock').DataTable({
                        ajax: {
                            url: _URL + "/ajs/data/producto/historial/stock",
                            type: "POST",
                            dataSrc: "data"
                        },
                        columns: [
                            { data: "codigo" },
                            { data: "producto_nombre" },
                            { 
                                data: "tipo_movimiento",
                                render: function(data) {
                                    const clase = data === 'INGRESO' ? 'bg-success' : 'bg-danger';
                                    return `<span class="badge ${clase}">${data}</span>`;
                                }
                            },
                            { data: "cantidad" },
                            { 
                                data: "costo_compra",
                                render: function(data) {
                                    return data ? 'S/ ' + parseFloat(data).toFixed(2) : '<span class="text-muted">-</span>';
                                }
                            },
                            { 
                                data: "fecha_movimiento",
                                render: function(data) {
                                    const fecha = new Date(data);
                                    const dia = String(fecha.getDate()).padStart(2, '0');
                                    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                                    const anio = fecha.getFullYear();
                                    const hora = String(fecha.getHours()).padStart(2, '0');
                                    const min = String(fecha.getMinutes()).padStart(2, '0');
                                    return `${dia}/${mes}/${anio} ${hora}:${min}`;
                                }
                            },
                            { data: "usuario" },
                            { 
                                data: "observaciones",
                                render: function(data) {
                                    if (!data) return '<span class="text-muted">-</span>';
                                    if (data.length > 50) {
                                        return `<span title="${data}">${data.substring(0, 50)}...</span>`;
                                    }
                                    return data;
                                }
                            }
                        ],
                        language: {
                            url: "ServerSide/Spanish.json"
                        },
                        order: [[5, 'desc']], // Ordenar por fecha descendente
                        pageLength: 15,
                        lengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "Todos"]],
                        responsive: false,
                        dom: '<"row mb-2"<"col-12 col-sm-6 mb-2 mb-sm-0"B><"col-12 col-sm-6"f>>' +
                             '<"row"<"col-12"tr>>' +
                             '<"row mt-2"<"col-12 col-sm-5 mb-2 mb-sm-0"l><"col-12 col-sm-2 text-center mb-2 mb-sm-0"i><"col-12 col-sm-5"p>>',
                        buttons: [
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel"></i> Excel',
                                className: 'btn btn-success btn-sm me-1',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fa fa-file-pdf"></i> PDF',
                                className: 'btn btn-danger btn-sm',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            }
                        ],
                        initComplete: function() {
                            // Ajustar columnas despu├®s de cargar los datos
                            table.columns.adjust().draw();
                        },
                        drawCallback: function() {
                            // Ajustar columnas cada vez que se redibuja la tabla
                            table.columns.adjust();
                        }
                    });
                },

                formatearFecha(fecha) {
                    const date = new Date(fecha);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                },

                formatearFecha(fecha) {
                    const date = new Date(fecha);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                },

                actualizarProd() {
                    // Validar campos requeridos
                    if (!this.edt.categoria || this.edt.categoria === '') {
                        alertAdvertencia("Debe seleccionar una categor├¡a");
                        return;
                    }

                    if (!this.edt.unidad || this.edt.unidad === '') {
                        alertAdvertencia("Debe seleccionar una unidad");
                        return;
                    }

                    let formData = new FormData();

                    // A├▒adir los datos al formData
                    formData.append('id_producto', this.edt.cod);
                    formData.append('cod', this.edt.cod);
                    formData.append('nombre', this.edt.nombre);
                    formData.append('codigo', this.edt.codigo);
                    formData.append('detalle', this.edt.detalle);
                    formData.append('categoria', this.edt.categoria);
                    formData.append('unidad', this.edt.unidad);
                    formData.append('precio', this.edt.precio);
                    formData.append('costo', this.edt.costo);
                    formData.append('almacen', this.edt.almacen);
                    formData.append('codSunat', this.edt.codSunat);
                    formData.append('afecto', this.edt.afecto);
                    formData.append('usar_barra', this.edt.usar_barra);
                    formData.append('precioMayor', this.edt.precioMayor);
                    formData.append('precioMenor', this.edt.precioMenor);
                    formData.append('precio3', this.edt.precio3);
                    formData.append('precio4', this.edt.precio4);
                    formData.append('cantidad', this.edt.cantidad);
                    formData.append('razon', 1);
                    formData.append('ruc', 1);
                    formData.append('moneda', this.edt.moneda);
                    formData.append('usar_multiprecio', this.edt.usar_multiprecio ? '1' : '0');

                    // Agregar la imagen solo si fue seleccionada Y el input no est├í vac├¡o
                    let inputImagen = document.querySelector('#upload-input');
                    let imagen = inputImagen && inputImagen.files && inputImagen.files[0];
                    if (imagen && inputImagen.value) {
                        formData.append('imagen', imagen);
                    }

                    // Verificar si se debe eliminar la imagen
                    let eliminarImagen = document.querySelector('#eliminar-imagen-flag');
                    if (eliminarImagen && eliminarImagen.value === '1') {
                        formData.append('eliminar_imagen', '1');
                    }


                    // Hacer la solicitud AJAX
                    $.ajax({
                        url: _URL + '/ajs/data/producto/edt',
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            // Si usa multiprecio, guardar los precios
                            if (app._data.edt.usar_multiprecio) {
                                _ajax("/ajs/data/producto/guardar/precios", "POST", {
                                    id_producto: app._data.edt.cod,
                                    precios: app._data.precios
                                }, function (resp) {
                                    if (resp.res) {
                                        alertExito("Producto actualizado exitosamente").then(() => {
                                            $("#modal-edt-prod").modal("hide");
                                            if (currentView === 'table') {
                                                datatable.ajax.reload(null, false);
                                            } else {
                                                loadGridProducts(currentPage, searchTerm);
                                            }
                                        });
                                    } else {
                                        alertAdvertencia("Error al guardar los precios");
                                    }
                                });
                            } else {
                                alertExito("Producto actualizado exitosamente").then(() => {
                                    $("#modal-edt-prod").modal("hide");
                                    if (currentView === 'table') {
                                        datatable.ajax.reload(null, false);
                                    } else {
                                        loadGridProducts(currentPage, searchTerm);
                                    }
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error en la solicitud:", error);
                            alertAdvertencia("Hubo un error al intentar actualizar el producto.");
                        }
                    });

                },
                onImageChange(event) {
                    const file = event.target.files[0];
                    this.reg.imagen = file;
                    // Crear vista previa
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.imagePreview = null;
                    }
                },
                agregarProd() {
                    // Validar campos requeridos
                    if (!this.reg.categoria || this.reg.categoria === '') {
                        alertAdvertencia("Debe seleccionar una categor├¡a");
                        return;
                    }

                    if (!this.reg.unidad || this.reg.unidad === '') {
                        alertAdvertencia("Debe seleccionar una unidad");
                        return;
                    }

                    const formData = new FormData();
                    formData.append('nombre', this.reg.nombre);
                    formData.append('precio', this.reg.precio);
                    formData.append('costo', this.reg.costo);
                    formData.append('cantidad', this.reg.cantidad);
                    formData.append('codSunat', this.reg.codSunat);
                    formData.append('afecto', this.reg.afecto);
                    formData.append('ruc', this.reg.ruc);
                    formData.append('razon', this.reg.razon);
                    formData.append('precio1', this.reg.precio1);
                    formData.append('precio2', this.reg.precio2);
                    formData.append('precio3', this.reg.precio3);
                    formData.append('precio4', this.reg.precio4);
                    formData.append('codigo', this.reg.codigo);
                    formData.append('imagen', this.reg.imagen);
                    formData.append('detalle', this.reg.detalle);
                    formData.append('categoria', this.reg.categoria);
                    formData.append('almacen', this.reg.almacen);
                    formData.append('unidad', this.reg.unidad);
                    formData.append('moneda', this.reg.moneda);
                    formData.append('usar_multiprecio', this.reg.usar_multiprecio ? '1' : '0');
                    formData.append('precios', JSON.stringify(this.preciosNuevos));

                    fetch(_URL + '/ajs/data/producto/add', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(resp => {
                            if (resp.res) {
                                alertExito("Agregado").then(() => {
                                    $("#modal-add-prod").modal("hide");
                                    if (currentView === 'table') {
                                        datatable.ajax.reload(null, false);
                                    } else {
                                        loadGridProducts(currentPage, searchTerm);
                                    }
                                    this.preciosNuevos = [];
                                });
                            } else {
                                alertAdvertencia("No se pudo agregar");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alertAdvertencia("Error en la solicitud");
                        });
                },
                // Funci├│n setInfo corregida
                setInfo(data) {
                    $("#modal-edt-prod").modal("show");

                    console.log("Datos recibidos:", data);
                    console.log("Valor de usar_multiprecio en la base de datos:", data.usar_multiprecio);
                    console.log("Tipo de dato:", typeof data.usar_multiprecio);

                    // Convertir el valor de usar_multiprecio correctamente
                    // IMPORTANTE: Esto debe hacerse ANTES de la promesa
                    this.edt = {
                        usar_multiprecio: data.usar_multiprecio === '1' || data.usar_multiprecio === 1
                    };

                    console.log("Valor convertido para Vue:", this.edt.usar_multiprecio);

                    // Primero cargar las categor├¡as y unidades
                    Promise.all([
                        $.get(_URL + "/ajs/get/categorias"),
                        $.get(_URL + "/ajs/get/unidades")
                    ]).then(([categorias, unidades]) => {
                        const cats = JSON.parse(categorias);
                        const units = JSON.parse(unidades);

                        // Poblar los selectores con opci├│n por defecto
                        let catOptions = '<option value="">Seleccione categor├¡a</option>';
                        cats.forEach(cat => {
                            catOptions += `<option value="${cat.id}">${cat.nombre}</option>`;
                        });
                        $('#categoria-edt').html(catOptions);

                        let unitOptions = '<option value="">Seleccione unidad</option>';
                        units.forEach(unit => {
                            unitOptions += `<option value="${unit.id}">${unit.nombre}</option>`;
                        });
                        $('#unidades-edt').html(unitOptions);

                        // Luego setear los valores MANTENIENDO el valor de usar_multiprecio
                        this.edt = {
                            ...this.edt, // Mantener el valor de usar_multiprecio que ya establecimos
                            cod_prod: data.codigo || '', // Asegurarse de que cod_barra se asigne
                            usar_barra: data.usar_barra,
                            cod: data.id_producto,
                            nombre: data.nombre,
                            codigo: data.codigo,
                            detalle: data.detalle,
                            categoria: data.categoria || '',
                            unidad: data.unidad_id || data.unidad || '',
                            precio: data.precio,
                            costo: parseFloat(data.costo).toFixed(2),
                            almacen: data.almacen,
                            codSunat: data.codsunat,
                            afecto: data.iscbp,
                            precioMayor: data.precio_mayor,
                            precioMenor: data.precio_menor,
                            precio2: data.precio2,
                            precio3: data.precio3,
                            precio4: data.precio4,
                            cantidad: data.cantidad,
                            razon_social: data.razon_social,
                            ruc: data.ruc,
                            moneda: data.moneda || 'PEN'
                        };
                        // Si hay c├│digo de barras y usar_barra es '1', generar el c├│digo de barras
                        if (this.edt.usar_barra === '1') {
                            $("#barcode").show();
                            // Esperar a que el modal est├® completamente visible
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    try {
                                        if (document.getElementById("barcode")) {
                                            // Usar el c├│digo del producto para el c├│digo de barras
                                            JsBarcode("#barcode", this.edt.codigo);
                                        }
                                    } catch (error) {
                                        console.error("Error al generar c├│digo de barras:", error);
                                    }
                                }, 300);
                            });
                        } else {
                            $("#barcode").hide();
                        }

                        // Setear los valores de los selectores
                        this.$nextTick(() => {
                            $('#categoria-edt').val(this.edt.categoria);
                            $('#unidades-edt').val(this.edt.unidad);
                        });

                        // Manejo de la imagen
                        if (data.imagen) {
                            $('#img-preview').attr('src', _URL + '/img/productos/' + data.imagen);
                            $('.image-container').show();
                            $('#no-image-message').hide();
                        } else {
                            $('.image-container').hide();
                            $('#no-image-message').show();
                        }

                        // Limpiar flag de eliminar imagen al abrir modal
                        $('#eliminar-imagen-flag').remove();

                        // CR├ìTICO: Limpiar el input file para evitar que se env├¡e imagen de otro producto
                        $('#upload-input').val('');


                    }).catch(error => {
                        console.error('Error cargando datos:', error);
                        alert('Error al cargar las categor├¡as y unidades');
                    });

                    // Cargar los precios si usa multiprecio
                    if (this.edt.usar_multiprecio) {
                        _ajax("/ajs/data/producto/obtener/precios", "POST", {
                            id_producto: data.id_producto
                        }, function (resp) {
                            if (resp.res) {
                                app._data.precios = resp.precios;
                            } else {
                                app._data.precios = [];
                            }
                        });
                    } else {
                        this.precios = [];
                    }
                },
                onlyNumber($event) {
                    //console.log($event.keyCode); //keyCodes value
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                        $event.preventDefault();
                    }
                },
                aumentarStockProducto() {
                    if (!this.stockData.producto_id) {
                        alertAdvertencia("Debe seleccionar un producto");
                        return;
                    }

                    if (!this.stockData.cantidad_ingresar || this.stockData.cantidad_ingresar <= 0) {
                        alertAdvertencia("Debe ingresar una cantidad v├ílida");
                        return;
                    }

                    if (!this.stockData.costo_compra || this.stockData.costo_compra <= 0) {
                        alertAdvertencia("Debe ingresar el precio de compra");
                        return;
                    }

                    const data = {
                        producto_id: this.stockData.producto_id,
                        cantidad: this.stockData.cantidad_ingresar,
                        costo_compra: this.stockData.costo_compra,
                        moneda: this.stockData.moneda,
                        observaciones: this.stockData.observaciones
                    };

                    _ajax("/ajs/data/producto/aumentar/stock", "POST", data, function (resp) {
                        if (resp.res) {
                            alertExito("Stock aumentado exitosamente").then(() => {
                                $("#modal-aumentar-stock").modal("hide");
                                if (currentView === 'table') {
                                    datatable.ajax.reload(null, false);
                                } else {
                                    loadGridProducts(currentPage, searchTerm);
                                }
                                // Limpiar formulario
                                app._data.stockData = {
                                    producto_id: '',
                                    stock_actual: '',
                                    cantidad_ingresar: '',
                                    producto_nombre: '',
                                    costo_compra: '',
                                    moneda: 'PEN',
                                    observaciones: ''
                                };
                                $('#buscar-producto-stock').val('');
                            });
                        } else {
                            alertAdvertencia("Error al aumentar el stock");
                        }
                    });
                },
                disminuirStockProducto() {
                    if (!this.disminuirData.producto_id) {
                        alertAdvertencia("Debe seleccionar un producto");
                        return;
                    }

                    if (!this.disminuirData.cantidad_disminuir || this.disminuirData.cantidad_disminuir <= 0) {
                        alertAdvertencia("Debe ingresar una cantidad v├ílida");
                        return;
                    }

                    if (this.disminuirData.cantidad_disminuir > this.disminuirData.stock_actual) {
                        alertAdvertencia("La cantidad a disminuir no puede ser mayor al stock actual");
                        return;
                    }

                    const data = {
                        producto_id: this.disminuirData.producto_id,
                        cantidad: this.disminuirData.cantidad_disminuir,
                        observaciones: this.disminuirData.observaciones
                    };

                    _ajax("/ajs/data/producto/disminuir/stock", "POST", data, function (resp) {
                        if (resp.res) {
                            alertExito("Stock disminuido exitosamente").then(() => {
                                $("#modal-disminuir-stock").modal("hide");
                                if (currentView === 'table') {
                                    datatable.ajax.reload(null, false);
                                } else {
                                    loadGridProducts(currentPage, searchTerm);
                                }
                                // Limpiar formulario
                                app._data.disminuirData = {
                                    producto_id: '',
                                    stock_actual: '',
                                    cantidad_disminuir: '',
                                    producto_nombre: '',
                                    unidad: '',
                                    observaciones: ''
                                };
                                $('#buscar-producto-disminuir').val('');
                            });
                        } else {
                            alertAdvertencia("Error al disminuir el stock");
                        }
                    });
                },
                limpiarProductoTraslado() {
                    $('#buscar-producto-traslado').val('');
                    this.trasladoData.producto_id = '';
                },
                eliminarProductoTraslado(index) {
                    this.trasladoData.productos.splice(index, 1);
                },
                realizarTraslado() {
                    if (!this.trasladoData.almacen_origen) {
                        alertAdvertencia("Debe seleccionar el almac├®n de origen");
                        return;
                    }

                    if (!this.trasladoData.almacen_destino) {
                        alertAdvertencia("Debe seleccionar el almac├®n de destino");
                        return;
                    }

                    if (this.trasladoData.almacen_origen === this.trasladoData.almacen_destino) {
                        alertAdvertencia("El almac├®n de origen y destino no pueden ser el mismo");
                        return;
                    }

                    if (this.trasladoData.productos.length === 0) {
                        alertAdvertencia("Debe agregar al menos un producto");
                        return;
                    }

                    const data = {
                        almacen_origen: this.trasladoData.almacen_origen,
                        almacen_destino: this.trasladoData.almacen_destino,
                        productos: this.trasladoData.productos,
                        nota: this.trasladoData.nota
                    };

                    _ajax("/ajs/data/producto/traslado/almacenes", "POST", data, function (resp) {
                        if (resp.res) {
                            alertExito("Traslado realizado exitosamente").then(() => {
                                $("#modal-traslado-almacenes").modal("hide");
                                if (currentView === 'table') {
                                    datatable.ajax.reload(null, false);
                                } else {
                                    loadGridProducts(currentPage, searchTerm);
                                }
                                // Limpiar formulario
                                app._data.trasladoData = {
                                    almacen_origen: '',
                                    almacen_destino: '',
                                    producto_id: '',
                                    productos: [],
                                    nota: ''
                                };
                                $('#buscar-producto-traslado').val('');
                            });
                        } else {
                            alertAdvertencia(resp.error || "Error al realizar el traslado");
                        }
                    });
                }
            },

        });

        // Funci├│n para inicializar DataTable
        function initializeDataTable() {
            datatable = $("#datatable").DataTable({
                order: [[0, 'ASC']],
                "processing": true,
                "serverSide": true,
                "sAjaxSource": _URL + "/ajs/server/sider/productos",
                "language": {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ning├║n dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "├Ültimo",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    },
                    "buttons": {
                        "copy": "Copiar",
                        "colvis": "Visibilidad"
                    }
                },
                "fnServerParams": function (aoData) {
                    aoData.push(
                        { "name": "almacenId", "value": almacenCod },
                        {
                            "name": "filter", "value": $('#filtroProductos').val() || $('#filtroProductos-mobile').val() || ''

                        }
                    );
                },
                "drawCallback": function (settings) {
                    // Callback despu├®s de que la tabla se ha redibujado
                    $("#datatable_processing").hide();

                    // Esperar un momento antes de restaurar estados
                    setTimeout(() => {
                        if (localStorage.getItem('idChecks')) {
                            restoreCheckboxStates();
                        }
                    }, 100);
                },
                columnDefs: [
                    {
                        "targets": [0],
                        "className": "text-center",
                        "width": "auto",
                        "render": function (data, type, row, meta) {
                            return '<a href="javascript:abrirModalBarras(\'' + row[0] + '\',\'' + row[0] + '\')">' + row[0] + '</a>';
                        }
                    },
                    {
                        "targets": [1],
                        "className": "dt-body-left",
                        "width": "auto",
                        "render": function (data, type, row, meta) {
                            return '<div style="white-space: normal;">' + data + '</div>';
                        }
                    },
                    {
                        "targets": [3],
                        "className": "text-center",
                        "render": function (data, type, row, meta) {
                            // La moneda est├í en row[7] (├¡ndice 7 del array de columnas)
                            const moneda = row[7] || 'PEN';
                            const simbolo = moneda === 'USD' ? '$' : 'S/';
                            return `${simbolo}${parseFloat(data || 0).toFixed(2)}`;
                        }
                    },
                    {
                        "targets": 5,
                        "render": function (data, type, row, meta) {
                            return `<button data-item="${row[6]}" class="btn-edt btn btn-sm btn-info"><i class="fa fa-edit"></i></button>`;
                        }
                    },
                    {
                        "targets": 6,
                        "render": function (data, type, row, meta) {
                            return `<input type="checkbox" class="btnCheckEliminar" data-id="${row[6]}" data-row-index="${meta.row}">`;
                        }
                    }
                ],
            });
        }

        // Inicializar DataTable
        initializeDataTable();



        function toggleViewButtons() {
            const isTablet = window.innerWidth < 992;
            const isDesktop = window.innerWidth >= 1200;

            if (isTablet) {
                // En tablet/m├│vil: mostrar dropdown, ocultar botones individuales
                $('.grid-view-buttons').addClass('d-none');
                $('.table-view-dropdown').removeClass('d-none');
            } else if (isDesktop) {
                // En desktop grande: mostrar botones individuales, ocultar dropdown
                $('.grid-view-buttons').removeClass('d-none');
                $('.table-view-dropdown').addClass('d-none');
            } else {
                // En pantallas medianas: mostrar solo los toggle buttons
                $('.grid-view-buttons').addClass('d-none');
                $('.table-view-dropdown').addClass('d-none');
            }
        }

        // Funci├│n 'debounce' para optimizar el evento de resize.
        const debounce = (func, delay) => {
            let timeoutId;
            return (...args) => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        };

        // Asignamos el evento de resize usando el debounce.
        $(window).on('resize', debounce(toggleViewButtons, 200));

        // Event listeners para cambio de vista (c├│digo original del usuario)
        $('#btn-table-view').click(function () {
            if (currentView !== 'table') {
                currentView = 'table';
                $('#btn-table-view').addClass('active');
                $('#btn-grid-view').removeClass('active');
                $('#table-view').removeClass('hidden');
                $('#grid-view').removeClass('active');
                $('.grid-filters').removeClass('active');
                toggleViewButtons();
                if ($.fn.DataTable.isDataTable('#datatable')) {
                    datatable.ajax.reload();
                }
            }
        });

        $('#btn-grid-view').click(function () {
            if (currentView !== 'grid') {
                currentView = 'grid';
                $('#btn-grid-view').addClass('active');
                $('#btn-table-view').removeClass('active');
                $('#table-view').addClass('hidden');
                $('#grid-view').addClass('active');
                $('.grid-filters').addClass('active');
                toggleViewButtons();
                loadGridProducts(1, searchTerm);
            }
        });
        // Event listeners para botones desktop
        $('#btn-table-view-desktop').click(function () {
            $('#btn-table-view').click(); // Reutilizar funcionalidad existente
        });

        $('#btn-grid-view-desktop').click(function () {
            $('#btn-grid-view').click(); // Reutilizar funcionalidad existente
        });

        // Sincronizar estados entre versiones m├│vil y desktop
        $(document).on('click', '#btn-table-view, #btn-table-view-desktop', function () {
            $('#btn-table-view, #btn-table-view-desktop').addClass('active');
            $('#btn-grid-view, #btn-grid-view-desktop').removeClass('active');
        });

        $(document).on('click', '#btn-grid-view, #btn-grid-view-desktop', function () {
            $('#btn-grid-view, #btn-grid-view-desktop').addClass('active');
            $('#btn-table-view, #btn-table-view-desktop').removeClass('active');
        });


        // Llamada inicial para establecer el estado correcto.
        toggleViewButtons();

        // Event listeners para el dropdown
        $(document).on('click', '#add-prod-dropdown', function () {
            $('#add-prod').click(); // Reutilizar la funcionalidad existente
        });

        $(document).on('click', '.btnBorrar-dropdown', function () {
            $('.btnBorrar').click(); // Reutilizar la funcionalidad existente
        });

        // Inicializar la vista correcta al cargar la p├ígina
        currentView = 'table';
        toggleViewButtons(); // Usar la funci├│n para inicializar correctamente

        // Search en vista grid
        let searchTimeout;
        $('#grid-search-input').on('input', function () {
            clearTimeout(searchTimeout);
            searchTerm = $(this).val();

            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadGridProducts(currentPage, searchTerm);
            }, 500);
        });

        // Seleccionar todos en vista grid
        $('.btnSeleccionarTodosGrid').click(function () {
            const $button = $(this);
            const isSelectingAll = $button.text().includes('Seleccionar Todos');

            if (isSelectingAll) {
                // Seleccionar todos los checkboxes visibles en el grid
                $('.product-card .btnCheckEliminar').each(function () {
                    if (!$(this).prop('checked')) {
                        $(this).prop('checked', true);
                        const id = $(this).attr('data-id');
                        if (!arrayIdsOkUsar.some(item => item.id === id)) {
                            arrayIdsOkUsar.push({ id: id });
                        }
                    }
                });

                $button.html('<i class="fa fa-times me-1"></i> Deseleccionar Todos')
                    .removeClass('border-rojo bg-white')
                    .addClass('btn-warning');
            } else {
                // Deseleccionar todos
                $('.product-card .btnCheckEliminar').prop('checked', false);
                arrayIdsOkUsar = [];

                $button.html('<i class="fa fa-check-square me-1"></i> Seleccionar Todos')
                    .removeClass('btn-warning')
                    .addClass('border-rojo bg-white');
            }

            // Actualizar localStorage
            if (arrayIdsOkUsar.length > 0) {
                localStorage.setItem("idChecks", JSON.stringify(arrayIdsOkUsar));
            } else {
                localStorage.removeItem("idChecks");
            }
        });

        // Reemplazar el autocomplete existente con esta versi├│n mejorada
        $("#buscar-producto-stock").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: _URL + "/ajs/cargar/productos/" + almacenCod,
                    data: { term: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    }
                });
            },
            minLength: 2,
            appendTo: "#modal-aumentar-stock .modal-body",
            select: function (event, ui) {
                // Actualizar el valor del input con el nombre del producto
                $(this).val(ui.item.label || ui.item.nombre);

                // Actualizar los datos de Vue
                app._data.stockData.producto_id = ui.item.codigo;
                app._data.stockData.stock_actual = ui.item.cnt;
                app._data.stockData.producto_nombre = ui.item.nombre;
                $('#producto-seleccionado-id').val(ui.item.codigo);

                // Cerrar el autocomplete inmediatamente despu├®s de la selecci├│n
                $(this).autocomplete("close");

                return false; // Prevenir comportamiento por defecto
            }
            ,
            open: function () {
                $('.ui-autocomplete').css({
                    'z-index': 9999,
                    'max-width': $('#buscar-producto-stock').outerWidth() + 'px',
                    'font-size': '13px'
                });

                var input = $('#buscar-producto-stock');
                var inputOffset = input.position();

                $('.ui-autocomplete').css({
                    'left': inputOffset.left + 'px',
                    'top': (inputOffset.top + input.outerHeight() + 2) + 'px'
                });
            },
            close: function () {
                // Asegurar que el dropdown se oculte completamente
                $('.ui-autocomplete').hide();
            }
        });


        // Autocomplete para modal de DISMINUIR stock
        $("#buscar-producto-disminuir").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: _URL + "/ajs/cargar/productos/" + almacenCod,
                    data: { term: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    }
                });
            },
            minLength: 2,
            appendTo: "#modal-disminuir-stock .modal-body",
            select: function (event, ui) {
                $(this).val(ui.item.label || ui.item.nombre);
                app._data.disminuirData.producto_id = ui.item.codigo;
                app._data.disminuirData.stock_actual = ui.item.cnt;
                app._data.disminuirData.producto_nombre = ui.item.nombre;
                app._data.disminuirData.unidad = ui.item.unidad || '';
                $('#producto-disminuir-id').val(ui.item.codigo);
                $(this).autocomplete("close");
                return false;
            },
            open: function () {
                $('.ui-autocomplete').css({
                    'z-index': 9999,
                    'max-width': $('#buscar-producto-disminuir').outerWidth() + 'px',
                    'font-size': '13px'
                });
                var input = $('#buscar-producto-disminuir');
                var inputOffset = input.position();
                $('.ui-autocomplete').css({
                    'left': inputOffset.left + 'px',
                    'top': (inputOffset.top + input.outerHeight() + 2) + 'px'
                });
            },
            close: function () {
                $('.ui-autocomplete').hide();
            }
        });

        // Limpiar modal de disminuir al cerrar
        $('#modal-disminuir-stock').on('hidden.bs.modal', function () {
            $('#buscar-producto-disminuir').val('');
            $('.ui-autocomplete').hide();
            app._data.disminuirData = {
                producto_id: '',
                stock_actual: '',
                cantidad_disminuir: '',
                producto_nombre: '',
                unidad: '',
                observaciones: ''
            };
        });

        // Autocomplete para modal de TRASLADO entre almacenes
        $("#buscar-producto-traslado").autocomplete({
            source: function (request, response) {
                const almacenOrigen = app._data.trasladoData.almacen_origen;
                if (!almacenOrigen) {
                    response([]);
                    return;
                }
                $.ajax({
                    url: _URL + "/ajs/cargar/productos/" + almacenOrigen,
                    data: { term: request.term },
                    success: function (data) {
                        response(JSON.parse(data));
                    }
                });
            },
            minLength: 2,
            appendTo: "#modal-traslado-almacenes .modal-body",
            select: function (event, ui) {
                // Verificar si el producto ya est├í en la lista
                const existe = app._data.trasladoData.productos.find(p => p.id_producto === ui.item.codigo);
                if (existe) {
                    alertAdvertencia("Este producto ya est├í en la lista");
                    $(this).val('');
                    return false;
                }

                // Agregar producto a la lista
                app._data.trasladoData.productos.push({
                    id_producto: ui.item.codigo,
                    nombre: ui.item.nombre,
                    unidad: ui.item.unidad || 'Unidad',
                    cantidad: 1,
                    stock_disponible: ui.item.cnt
                });

                $(this).val('');
                $(this).autocomplete("close");
                return false;
            },
            open: function () {
                $('.ui-autocomplete').css({
                    'z-index': 9999,
                    'max-width': $('#buscar-producto-traslado').outerWidth() + 'px',
                    'font-size': '13px'
                });
            },
            close: function () {
                $('.ui-autocomplete').hide();
            }
        });

        // Limpiar modal de traslado al cerrar
        $('#modal-traslado-almacenes').on('hidden.bs.modal', function () {
            $('#buscar-producto-traslado').val('');
            $('.ui-autocomplete').hide();
            app._data.trasladoData = {
                almacen_origen: '',
                almacen_destino: '',
                producto_id: '',
                productos: [],
                nota: ''
            };
        });

        // Agregar despu├®s de la configuraci├│n del autocomplete
        $('#modal-aumentar-stock').on('hidden.bs.modal', function () {
            // Limpiar el autocomplete cuando se cierre el modal
            $('#buscar-producto-stock').val('');
            $('.ui-autocomplete').hide();

            // Limpiar los datos de Vue
            app._data.stockData = {
                producto_id: '',
                stock_actual: '',
                cantidad_ingresar: '',
                producto_nombre: '',
                costo_compra: '',
                moneda: 'PEN',
                observaciones: ''
            };
        });

        // Evento para cargar historial cuando se abre el modal
        $('#modal-historial-stock').on('shown.bs.modal', function () {
            // Peque├▒o delay para asegurar que el modal est├® completamente visible
            setTimeout(function() {
                app.cargarHistorialStock();
            }, 100);
        });

        // Evento para limpiar cuando se cierra el modal
        $('#modal-historial-stock').on('hidden.bs.modal', function () {
            // Destruir DataTable completamente
            if ($.fn.DataTable.isDataTable('#tabla-historial-stock')) {
                $('#tabla-historial-stock').DataTable().clear().destroy();
            }
            // Limpiar el HTML de la tabla
            $('#tabla-historial-stock tbody').empty();
        });

        // Update filter handling
        $('.filter-option-select').on('change', function () {
            // Sincronizar ambos selects (desktop y mobile)
            const selectedValue = $(this).val();
            $('.filter-option-select').val(selectedValue);

            if (currentView === 'table') {
                datatable.ajax.reload();
            } else {
                currentPage = 1;
                loadGridProducts(currentPage, searchTerm);
            }
        });


        $("#file-import-exel").change(function () {
            if ($("#file-import-exel").val().length > 0) {
                var fd = new FormData();
                var file = $("#file-import-exel")[0].files[0];

                // Validar tipo de archivo
                var validExtensions = ['xlsx', 'xls', 'csv'];
                var fileExt = file.name.split('.').pop().toLowerCase();
                if (!validExtensions.includes(fileExt)) {
                    alertAdvertencia("Formato de archivo no v├ílido. Use Excel (.xlsx, .xls) o CSV");
                    $("#file-import-exel").val("");
                    return;
                }

                fd.append('file', file);

                $.ajax({
                    type: 'POST',
                    url: _URL + '/ajs/data/producto/add/exel',
                    data: fd,
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        $("#loader-menor").show();
                    },
                    error: function (err) {
                        $("#loader-menor").hide();
                        console.error("Error al procesar archivo:", err);
                        alertAdvertencia("Error al procesar el archivo: " + (err.responseText || "Error desconocido"));
                        $("#file-import-exel").val("");
                    },
                    success: function (resp) {
                        $("#loader-menor").hide();
                        try {
                            resp = JSON.parse(resp);
                            if (resp.res) {
                                if (!resp.data || resp.data.length <= 1) {
                                    alertAdvertencia("El archivo est├í vac├¡o o no contiene datos v├ílidos");
                                    return;
                                }

                                var listaTemp = [];
                                var hasErrors = false;

                                // Procesar datos saltando la primera fila (encabezados)
                                for (var i = 1; i < resp.data.length; i++) {
                                    var el = resp.data[i];
                                    if (!el[0]) { // Validar producto
                                        hasErrors = true;
                                        console.error("Fila " + (i + 1) + ": Nombre de producto vac├¡o");
                                        continue;
                                    }

                                    listaTemp.push({
                                        producto: el[0],
                                        descripcicon: el[1] || '',
                                        cantidad: el[2] || 0,
                                        costo: el[3] || 0,
                                        precio_unidad: el[4] || 0,
                                        precio_mayor: el[5] || 0,
                                        precio_menor: el[6] || 0,
                                        almacen: el[7] || 1,
                                        afecto: false,
                                        codigoProd: el[8] || '',
                                        unidad: el[9] || '',
                                        categoria: el[10] || '',
                                        moneda: el[11] || 'PEN',
                                        afecto: false
                                    });
                                }

                                if (hasErrors) {
                                    alertAdvertencia("Algunos productos no se pudieron procesar. Revise la consola para m├ís detalles.");
                                }

                                if (listaTemp.length > 0) {
                                    app._data.listaProd = listaTemp;
                                    $("#importarModal").modal("hide");
                                    $("#modal-lista-productos").modal("show");
                                } else {
                                    alertAdvertencia("No se encontraron productos v├ílidos para importar");
                                }
                            } else {
                                alertAdvertencia(resp.error || "No se pudo procesar el archivo");
                            }
                        } catch (e) {
                            console.error("Error procesando respuesta:", e);
                            alertAdvertencia("Error procesando la respuesta del servidor");
                        }
                        $("#file-import-exel").val("");
                    }
                });
            }
        });



        $("#datatable").on("click", ".btn-re-stock", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            app._data.restock.cod = cod
            app._data.restock.cantidad = ''
            $("#modal-restock").modal("show");

        })
        $("#generarreporteProd").click(() => {
            console.log("---------------------------------------")
            const anioREd = $("#anioreporEFG").val()
            const messREd = parseInt($("#mesreprEFG").val())
            const diaRed = $("#diareporEfghg").val().length > 0 ? parseInt($("#diareporEfghg").val()) : 'nn'
            window.open(_URL +
                `/reporte/productos/pdf/${codProdT}?fecha=${anioREd}${messREd}-${diaRed}`)
            $("#modal-prodEreport").modal("hide");
        })

        $("#datatable").on("click", ".btn-reporte", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");

            codProdT = cod
            $("#modal-prodEreport").modal("show");
        })

        $("#datatable").on("click", ".btn-ver-precios", function (evt) {
            const cod = $(evt.currentTarget).attr("data-item");
            console.log(cod);
            $("#modal-precios").modal("show");
            _ajax("/ajs/cargar/productos/precios", "POST", {
                cod
            },
                function (resp) {
                    console.log(resp);
                    $("#modal-precios").modal("show");

                    app._data.edt.precio = resp.precio == null ? parseFloat(0 + "").toFixed(2) : resp.precio
                    app._data.edt.precio2 = resp.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio2 + "").toFixed(2)
                    app._data.edt.precio3 = resp.precio3 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio3 + "").toFixed(2)
                    app._data.edt.precio4 = resp.precio4 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio4 + "").toFixed(2)
                    app._data.edt.precio_unidad = resp.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio_unidad + "").toFixed(2)

                    $('#precio1').val(resp.precio == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio + "").toFixed(2))
                    $('#precio2').val(resp.precio2 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio2 + "").toFixed(2))
                    $('#precio3').val(resp.precio3 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio3 + "").toFixed(2))
                    $('#precio3').val(resp.precio4 == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio4 + "").toFixed(2))
                    $('#precio_unidad').val(resp.precio_unidad == null ? parseFloat(0 + "").toFixed(2) : parseFloat(resp.precio_unidad + "").toFixed(2))
                    app._data.edt.cod_prod = cod
                }
            )

        })

        // Event delegation para botones de editar (funciona tanto en tabla como en grid)
        $(document).on("click", ".btn-edt", function (evt) {
            // Limpiar input file antes de abrir modal
            $('#upload-input').val('');

            const cod = $(evt.currentTarget).attr("data-item");
            _ajax("/ajs/data/producto/info", "POST", {
                cod
            },
                function (resp) {
                    console.log(resp);
                    if (resp.res) {
                        app.setInfo(resp.data)
                    } else {
                        alertAdvertencia("Informaci├│n no encontrada")
                    }
                }
            )
        });
        // Funci├│n para restaurar estados de checkboxes
        function restoreCheckboxStates() {
            const savedIds = JSON.parse(localStorage.getItem('idChecks') || '[]');

            savedIds.forEach(item => {
                $(`.btnCheckEliminar[data-id="${item.id}"]`).prop('checked', true);
            });

            // Actualizar el array global
            arrayIdsOkUsar = savedIds;
        }

        // Manejador para los checkboxes individuales con delegaci├│n de eventos
        $(document).on("click", ".btnCheckEliminar", function (e) {
            e.stopPropagation();

            const id = $(this).attr("data-id");
            const isChecked = $(this).prop("checked");

            if (isChecked) {
                if (!arrayIdsOkUsar.some(item => item.id === id)) {
                    arrayIdsOkUsar.push({ id: id });
                }
            } else {
                arrayIdsOkUsar = arrayIdsOkUsar.filter(item => item.id !== id);
                $('.btnSeleccionarTodos').prop('checked', false);
                $('.btnSeleccionarTodosGrid').html('<i class="fa fa-check-square me-1"></i> Seleccionar Todos')
                    .removeClass('btn-warning')
                    .addClass('border-rojo bg-white');
            }

            // Verificar si todos los checkboxes visibles est├ín marcados
            if (currentView === 'table') {
                const allChecked = $(".btnCheckEliminar:visible").length === $(".btnCheckEliminar:visible:checked").length;
                if (allChecked && $(".btnCheckEliminar:visible").length > 0) {
                    $(".btnSeleccionarTodos").prop("checked", true);
                }
            } else {
                // Vista grid
                const allChecked = $(".product-card .btnCheckEliminar:visible").length === $(".product-card .btnCheckEliminar:visible:checked").length;
                if (allChecked && $(".product-card .btnCheckEliminar:visible").length > 0) {
                    $('.btnSeleccionarTodosGrid').html('<i class="fa fa-times me-1"></i> Deseleccionar Todos')
                        .removeClass('border-rojo bg-white')
                        .addClass('btn-warning');
                }
            }

            // Guardar en localStorage
            if (arrayIdsOkUsar.length > 0) {
                localStorage.setItem("idChecks", JSON.stringify(arrayIdsOkUsar));
            } else {
                localStorage.removeItem("idChecks");
            }
        });

        // Manejador para el checkbox "Seleccionar Todos" con prevenci├│n de propagaci├│n
        $(".btnSeleccionarTodos").on("click", function (e) {
            e.stopPropagation();

            const isChecked = $(this).prop("checked");
            arrayIdsOkUsar = [];

            if (isChecked) {
                $(".btnCheckEliminar:visible").each(function () {
                    $(this).prop("checked", true);
                    const id = $(this).attr("data-id");
                    if (id && !arrayIdsOkUsar.some(item => item.id === id)) {
                        arrayIdsOkUsar.push({ id: id });
                    }
                });
            } else {
                $(".btnCheckEliminar:visible").prop("checked", false);
            }

            if (arrayIdsOkUsar.length > 0) {
                localStorage.setItem("idChecks", JSON.stringify(arrayIdsOkUsar));
            } else {
                localStorage.removeItem("idChecks");
            }
        });

        $('.btnBorrar').click(function () {
            console.log(localStorage.getItem('idChecks'));
            let ids = localStorage.getItem('idChecks')
            if (!ids) {
                alertAdvertencia("Seleccione productos");
                return;
            }

            let arrayId = JSON.parse(ids)
            Swal.fire({
                title: 'Desea borrar estos productos?',
                showDenyButton: true,
                confirmButtonText: 'Si',
                denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                    if (localStorage.getItem("idChecks") !== null) {
                        _ajax("/ajs/data/producto/delete", "POST", {
                            arrayId
                        },
                            function (resp) {
                                console.log(resp);
                                if (resp.res) {
                                    localStorage.removeItem('idChecks');
                                    Swal.fire('Buen trabajo',
                                        'Productos borrados exitosamente',
                                        'success', {}).then((result) => {
                                            // Limpiar checkboxes
                                            $('.btnCheckEliminar').prop('checked', false);
                                            $('.btnSeleccionarTodos').prop('checked', false);
                                            $('.btnSeleccionarTodosGrid').html('<i class="fa fa-check-square me-1"></i> Seleccionar Todos')
                                                .removeClass('btn-warning')
                                                .addClass('border-rojo bg-white');
                                            arrayIdsOkUsar = [];

                                            // Actualizar vista actual
                                            if (currentView === 'table') {
                                                datatable.ajax.reload(function () {
                                                    console.log("Tabla actualizada despu├®s de borrar");
                                                }, false);
                                            } else {
                                                loadGridProducts(currentPage, searchTerm);
                                            }
                                        });
                                } else {
                                    alertAdvertencia("Ocurri├│ un error")
                                }
                            })
                    } else {
                        alertAdvertencia("Seleccione productos")
                    }
                }
            })
        });
    });

    $(document).ready(function () {
        $('#add-prod').click(function () {
            $.get(_URL + "/ajs/get/categorias", function (data, textStatus, jqXHR) {
                let option = '<option value="">Seleccione una categor├¡a</option>';
                let resp = JSON.parse(data);
                $.each(resp, function (i, v) {
                    option += `<option value="${v.id}">${v.nombre}</option>`;
                });

                $('#categoria').html(option);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las categor├¡as: " + textStatus, errorThrown);
                alertError("No se pudo cargar las categor├¡as. Por favor, intenta nuevamente.");
            });

            $.get(_URL + "/ajs/get/unidades", function (data, textStatus, jqXHR) {
                let option = '<option value="">Seleccione una unidad</option>';
                let resp = JSON.parse(data);
                $.each(resp, function (i, v) {
                    option += `<option value="${v.id}">${v.nombre}</option>`;
                });

                $('#unidades').html(option);
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar las unidades: " + textStatus, errorThrown);
                alertError("No se pudo cargar las unidades. Por favor, intenta nuevamente.");
            });

            $('#modal-add-prod').modal('show');
        });

    });

    function truncateText(text, maxLength) {
        if (text == null) {
            return null;
        }
        if (text.length > maxLength) {
            return text.substring(0, maxLength) + '...';
        }
        return text;
    }

    // Encapsulamiento de la funcionalidad de imagen usando IIFE
    (function () {
        let imageMenuOpen = false;

        window.previewImage = function (input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Asignar la imagen a la vista previa
                    $('#img-preview').attr('src', e.target.result);

                    // Mostrar el contenedor de la imagen y ocultar el mensaje
                    $('.image-container').show();
                    $('#no-image-message').hide();

                    // Ocultar el men├║ desplegable
                    $('#image-menu').hide();
                    imageMenuOpen = false;

                    // IMPORTANTE: Si se sube una nueva imagen, se anula la eliminaci├│n
                    $('#eliminar-imagen-flag').remove();
                }
                reader.readAsDataURL(input.files[0]);
            }
        };

        window.toggleImageMenu = function () {
            imageMenuOpen = !imageMenuOpen;
            $('#image-menu').fadeToggle(200);
        };

        window.changeImage = function () {
            $('#upload-input').click();
            $('#image-menu').hide();
            imageMenuOpen = false;
        };

        window.removeImage = function () {
            // Limpiar el input y la vista previa
            $('#upload-input').val('');
            $('#img-preview').attr('src', '');

            // Ocultar el contenedor de la imagen y mostrar el mensaje
            $('.image-container').hide();
            $('#no-image-message').show();

            // Ocultar el men├║ desplegable
            $('#image-menu').hide();
            imageMenuOpen = false;

            // A├▒adir un campo oculto para indicar al backend que se debe eliminar la imagen
            $('#eliminar-imagen-flag').remove(); // Limpiar flag anterior
            $('<input>').attr({
                type: 'hidden',
                id: 'eliminar-imagen-flag',
                name: 'eliminar_imagen',
                value: '1'
            }).appendTo('#modal-edt-prod form');
        };

        // Cerrar el men├║ desplegable si se hace clic fuera de ├®l
        $(document).click(function (e) {
            if (!$(e.target).closest('#image-edit-button').length) {
                if ($('#image-menu').is(':visible')) {
                    $('#image-menu').fadeOut(200);
                    imageMenuOpen = false;
                }
            }
        });
    })();