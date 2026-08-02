// public/js/orden/orden-repuestos-grid.js
// Grid view for repuestos (IIFE)
var arrayIdsOkUsar = [];

(function () {
    var gridProducts = [];
    var totalProducts = 0;
    var currentPage = 1;
    var productsPerPage = 12;
    var searchTerm = '';
    var currentView = 'table';

    function getStockClass(cantidad) {
        var stock = parseInt(cantidad);
        if (stock <= 5) return 'low-stock';
        if (stock <= 20) return 'medium-stock';
        return '';
    }

    function loadGridProducts(page, search) {
        page = page || 1;
        search = search || '';
        $('#loading-grid').addClass('active');
        $('#products-container').empty();

        var gridAlmacen = $('#grid-almacen-select').val() || almacenCod;

        _ajax("/ajs/data/repuestos/grid", "POST", {
            almacenId: gridAlmacen,
            page: page,
            limit: productsPerPage,
            search: search
        }, function (resp) {
            $('#loading-grid').removeClass('active');
            if (resp.res) {
                gridProducts = resp.data;
                totalProducts = resp.total;
                currentPage = page;
                renderGridProducts();
                renderGridPagination();
            } else {
                alertAdvertencia("Error al cargar repuestos");
            }
        });
    }

    function renderGridProducts() {
        var container = $('#products-container');
        container.empty();
        if (gridProducts.length === 0) {
            container.html('<div class="col-12 text-center" style="grid-column: 1 / -1;"><div class="alert alert-info"><i class="fa fa-info-circle me-2"></i>No se encontraron repuestos.</div></div>');
            return;
        }
        gridProducts.forEach(function (product) {
            var stockClass = getStockClass(product.cantidad);
            var imageUrl = product.imagen ? _URL + '/img/repuestos/' + product.imagen : null;
            var isChecked = arrayIdsOkUsar.some(function (item) { return item.id === product.id_repuesto + ''; });
            var cardHtml =
                '<div class="product-card" data-product-id="' + product.id_repuesto + '">' +
                '  <div class="product-checkbox">' +
                '    <input type="checkbox" class="btnCheckEliminar" data-id="' + product.id_repuesto + '"' + (isChecked ? ' checked' : '') + '>' +
                '  </div>' +
                '  <div class="product-image-container">' +
                (imageUrl ? '<img src="' + imageUrl + '" alt="' + product.nombre + '" class="product-image">' : '<div class="no-image-placeholder"><i class="fa fa-image"></i><span>Sin imagen</span></div>') +
                '  </div>' +
                '  <div class="product-info">' +
                '    <span class="product-code">' + (product.codigo || '') + '</span>' +
                '    <h6 class="product-name" title="' + product.nombre + '">' + product.nombre + '</h6>' +
                '    <div class="product-details">' +
                '      <span class="product-unit">' + (product.unidad_nombre || 'N/A') + '</span>' +
                '      <span class="product-stock ' + stockClass + '"><i class="fa fa-cubes me-1"></i>' + product.cantidad + '</span>' +
                '    </div>' +
                '    <div class="product-price">S/ ' + parseFloat(product.precio || 0).toFixed(2) + '</div>' +
                '    <div class="product-actions">' +
                '      <button class="btn btn-edit-product bg-rojo btn-edt-grid" data-item="' + product.id_repuesto + '">' +
                '        <i class="fa fa-edit me-1"></i> Editar' +
                '      </button>' +
                '    </div>' +
                '  </div>' +
                '</div>';
            container.append(cardHtml);
        });
    }

    function renderGridPagination() {
        var totalPages = Math.ceil(totalProducts / productsPerPage);
        var pagination = $('#grid-pagination');
        pagination.empty();
        if (totalPages <= 1) return;
        if (currentPage > 1) {
            pagination.append('<button class="btn border-rojo bg-white btn-grid-page" data-page="' + (currentPage - 1) + '"><i class="fa fa-chevron-left"></i> Anterior</button>');
        }
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);
        if (startPage > 1) {
            pagination.append('<button class="btn border-rojo bg-white btn-grid-page" data-page="1">1</button>');
            if (startPage > 2) pagination.append('<span class="mx-2">...</span>');
        }
        for (var i = startPage; i <= endPage; i++) {
            var activeClass = i === currentPage ? 'bg-rojo text-white' : 'border-rojo bg-white';
            pagination.append('<button class="btn ' + activeClass + ' btn-grid-page" data-page="' + i + '">' + i + '</button>');
        }
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) pagination.append('<span class="mx-2">...</span>');
            pagination.append('<button class="btn border-rojo bg-white btn-grid-page" data-page="' + totalPages + '">' + totalPages + '</button>');
        }
        if (currentPage < totalPages) {
            pagination.append('<button class="btn border-rojo bg-white btn-grid-page" data-page="' + (currentPage + 1) + '">Siguiente <i class="fa fa-chevron-right"></i></button>');
        }
        var startItem = (currentPage - 1) * productsPerPage + 1;
        var endItem = Math.min(currentPage * productsPerPage, totalProducts);
        pagination.append('<div class="ms-3 text-muted">Mostrando ' + startItem + ' - ' + endItem + ' de ' + totalProducts + ' repuestos</div>');
    }

    // Event listeners
    $(document).on('click', '.btn-grid-page', function () {
        loadGridProducts(parseInt($(this).data('page')), searchTerm);
    });

    $(document).on('click', '#btn-table-view', function () {
        if (currentView !== 'table') {
            currentView = 'table';
            $('#btn-table-view').addClass('active bg-rojo text-white').removeClass('bg-white text-rojo');
            $('#btn-grid-view').removeClass('active bg-rojo text-white').addClass('bg-white text-rojo');
            $('#table-view').removeClass('hidden');
            $('#grid-view').removeClass('active');
            $('.grid-filters').removeClass('active');
            if ($.fn.DataTable.isDataTable('#datatable')) {
                datatable.ajax.reload();
            }
        }
    });

    $(document).on('click', '#btn-grid-view', function () {
        if (currentView !== 'grid') {
            currentView = 'grid';
            $('#btn-grid-view').addClass('active bg-rojo text-white').removeClass('bg-white text-rojo');
            $('#btn-table-view').removeClass('active bg-rojo text-white').addClass('bg-white text-rojo');
            $('#table-view').addClass('hidden');
            $('#grid-view').addClass('active');
            $('.grid-filters').addClass('active');
            $('#grid-almacen-select').val(almacenCod);
            loadGridProducts(1, searchTerm);
        }
    });

    var searchTimeout;
    $(document).on('input', '#grid-search-input', function () {
        clearTimeout(searchTimeout);
        searchTerm = $(this).val();
        searchTimeout = setTimeout(function () {
            currentPage = 1;
            loadGridProducts(1, searchTerm);
        }, 500);
    });

    $(document).on('change', '#grid-almacen-select', function () {
        almacenCod = $(this).val();
        if (app) app.almacen = almacenCod;
        $('#almacenSelect').val(almacenCod);
        loadGridProducts(1, searchTerm);
    });

    $(document).on('click', '.btn-edt-grid', function () {
        var cod = $(this).attr('data-item');
        _ajax("/ajs/data/repuesto/info", "POST", { cod: cod }, function (resp) {
            if (resp.res) {
                app.setInfo(resp.data);
            } else {
                alertAdvertencia("Información no encontrada");
            }
        });
    });

    $(document).on('click', '.btnSeleccionarTodosGrid', function () {
        var $button = $(this);
        var isSelectingAll = $button.text().indexOf('Seleccionar Todos') >= 0;
        if (isSelectingAll) {
            $('.product-card .btnCheckEliminar').each(function () {
                if (!$(this).prop('checked')) {
                    $(this).prop('checked', true);
                    var id = $(this).attr('data-id');
                    if (!arrayIdsOkUsar.some(function (item) { return item.id === id; })) {
                        arrayIdsOkUsar.push({ id: id });
                    }
                }
            });
            $button.html('<i class="fa fa-times me-1"></i> Deseleccionar Todos').removeClass('border-rojo bg-white').addClass('btn-warning');
        } else {
            $('.product-card .btnCheckEliminar').prop('checked', false);
            $('.product-card .btnCheckEliminar').each(function () {
                var id = $(this).attr('data-id');
                arrayIdsOkUsar = arrayIdsOkUsar.filter(function (item) { return item.id !== id; });
            });
            $button.html('<i class="fa fa-check-square me-1"></i> Seleccionar Todos').removeClass('btn-warning').addClass('border-rojo bg-white');
        }
        if (arrayIdsOkUsar.length > 0) {
            localStorage.setItem("idChecks", JSON.stringify(arrayIdsOkUsar));
        } else {
            localStorage.removeItem("idChecks");
        }
    });

    $(document).on('repuestoActualizado repuestoAgregado', function () {
        if (currentView === 'grid') {
            loadGridProducts(currentPage, searchTerm);
        }
    });
})();
