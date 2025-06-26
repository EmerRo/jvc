<style>
.page-title {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 15px;
    font-weight: 600;
}
</style>

<div class="page-title-box">
    <div class="row align-items-center">
        <div class="clearfix">
            <h6 class="page-title">COTIZACIONES DEL TALLER</h6>
            <ol class="breadcrumb m-0 float-start">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Cotizaciones</a></li>
                <li class="breadcrumb-item active" aria-current="page">Productos</li>
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
                <div class="table-responsive">
                    <table id="datatable-c"  class="table table-bordered dt-responsive nowrap text-center table-sm" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
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
            "responsive": true, // Habilitar responsividad
            "scrollX": false,   // Deshabilitar scroll horizontal
            "autoWidth": false, // Deshabilitar auto-ancho
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            "sAjaxSource": _URL + "/data/taller/cotizaciones/lista/ss",

            order: [
                [0, "desc"]
            ],
            
            columnDefs: [
             // Columna para mostrar el tipo (Servicio u Orden de Trabajo)
             {
                targets: 4,
                render: function (data, type, row, meta) {
                    const tipo = data === 'ORD TRABAJO' ? 'Orden de Trabajo' : 'Servicio';
                    const badgeClass = data === 'ORD TRABAJO' ? 'bg-primary' : 'bg-success';

                    return `<span class="badge ${badgeClass}" style="font-size: 12px;">${tipo}</span>`;
                    // Ajusta el valor de "14px" según el tamaño que desees
                }

             },
             // Columna de acciones (ahora es la 5 en lugar de la 4)
             {
                targets: 5,
                render: function (data, type, row, meta) {
                    return `
                        <a href="/edt/coti/taller?id=${data}" class="button-link btn btn-sm btn-primary ">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="${_URL + '/r/taller/reporte/' + data}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fa fa-file"></i>
                        </a>
                        <button onclick="eliminarCotizacion(${data})" type="button" class="btn-del btn btn-danger btn-sm">
                            <i class="fa fa-times"></i>
                        </button>
                    `;
                }
            },           
            ]
        })

        tes()
      
    })
    function eliminarCotizacion(cod) {
        console.log(cod)
        _ajax("/ajs/taller/cotizaciones/del", "POST", { cod }, function (resp) {
            tabla.ajax.reload();
        })
    }
</script>

<script src="<?= URL::to('public/js/dataTables.spanish.js') ?>?v=<?= time() ?>"></script>
