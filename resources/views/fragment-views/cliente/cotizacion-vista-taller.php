<!-- resources\views\fragment-views\cliente\cotizacion-vista-taller.php -->
<style>
.page-title {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 15px;
    font-weight: 600;
}

.badge-tipo {
    font-size: 11px !important;
    padding: 6px 12px !important;
    font-weight: 500 !important;
    min-width: 100px !important;
    display: inline-block !important;
    text-align: center !important;
}
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title">COTIZACIONES DEL TALLER</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Taller cotizaciones</a></li>
               
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card"
            style="border-radius:20px;box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06)">
            <div class="card-body">
                <h4 class="card-title"></h4>
                
                <!-- Filtro simple -->
               <div class="mb-3 d-flex align-items-center" style="width: fit-content;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Filtrar</span>
                                <i class="fas fa-filter text-muted"></i>
                                <select id="filtroTipo" class="form-select form-select-sm"
                                    style="width: auto; min-width: 150px;">
                                    <option value="">Todos</option>
                                    <option value="ORD TRABAJO">Orden de Trabajo</option>
                                    <option value="ORD SERVICIO">Orden de Servicio</option>
                                </select>
                            </div>
                        </div>
                
                <div class="table-responsive">
                    <table id="datatable-c" class="table table-bordered dt-responsive nowrap text-center table-sm" 
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    function tes() {
   
    }
    var tabla;
    $(document).ready(function () {
        tabla = $("#datatable-c").DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "scrollX": false,
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "sAjaxSource": _URL + "/data/taller/cotizaciones/lista/ss",
            "language": {
                "url": "ServerSide/Spanish.json"
            },
            order: [
                [1, "desc"] // Ordenar por fecha (columna 1) en lugar de por # (columna 0)
            ],
            
            columnDefs: [
                // Columna # correlativa
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        // Calcular el número correlativo basado en la página actual
                        var pageInfo = tabla.page.info();
                        return pageInfo.start + meta.row + 1;
                    }
                },
                // Columna para mostrar el tipo con badges mejorados
                {
                    targets: 4,
                    render: function (data, type, row, meta) {
                        // Verificar diferentes posibles valores para orden de trabajo
                        if (data && (data.toString().includes('TRABAJO') || data.toString().includes('trabajo'))) {
                            return `<span class="badge bg-warning badge-tipo">ORD TRABAJO</span>`;
                        } 
                        // Verificar diferentes posibles valores para servicio
                        else if (data && (data.toString().includes('SERVICIO') || data.toString().includes('servicio') || data.toString().includes('Servicio'))) {
                            return `<span class="badge bg-success badge-tipo">ORD SERVICIO</span>`;
                        } 
                        // Si no coincide con ninguno, mostrar el valor original
                        else {
                            return `<span class="badge bg-secondary badge-tipo">${data || 'N/A'}</span>`;
                        }
                    }
                },
                // Columna de acciones
                {
                    targets: 5,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="btn-group" role="group">
                                <a href="/edt/coti/taller?id=${data}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="${_URL + '/r/taller/reporte/' + data}" target="_blank" class="btn btn-sm btn-info" title="Ver reporte">
                                    <i class="fa fa-file"></i>
                                </a>
                                <button onclick="eliminarCotizacion(${data})" type="button" class="btn btn-danger btn-sm" title="Eliminar">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Evento para el filtro de tipo
        $('#filtroTipo').on('change', function() {
            var valorFiltro = this.value;
            
            if (valorFiltro === '') {
                // Mostrar todos los registros
                tabla.column(4).search('').draw();
            } else {
                // Filtrar por el tipo seleccionado
                tabla.column(4).search(valorFiltro).draw();
            }
        });

        tes();
    });

function eliminarCotizacion(cod) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Usar jQuery AJAX en lugar de _ajax para mejor control
            $.ajax({
                url: _URL + "/ajs/taller/cotizaciones/del",
                type: "POST",
                data: { cod: cod },
                dataType: 'json',
                success: function(resp) {
                    console.log("Respuesta del servidor:", resp);
                    
                    if (resp.success === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'La cotización ha sido eliminada correctamente.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Recargar la tabla
                        if (tabla && typeof tabla.ajax !== 'undefined') {
                            tabla.ajax.reload(null, false); // false para mantener la paginación
                        } else {
                            // Si hay problemas con la recarga, recargar la página
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: resp.message || 'No se pudo eliminar la cotización'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en la petición:", {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor. Inténtelo nuevamente.'
                    });
                }
            });
        }
    });
}

</script>

<script src="<?= URL::to('public/js/dataTables.spanish.js') ?>?v=<?= time() ?>"></script>