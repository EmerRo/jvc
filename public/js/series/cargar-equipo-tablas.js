 /* public\js\series\cargar-equipo-tablas.js
  Funciones para cargar datos */
 function cargarTablaMarcas() {
    $.ajax({
        url: _URL + "/ajs/get/marcas",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let html = "";
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function (marca) {
                html += `
                    <tr data-id="${marca.id}">
                        <td class="nombre-campo">${marca.nombre}</td>
                        <td>
                            <button class="btn btn-sm editar-marca" style="color: #0d6efd;">
                                <i class="fa fa-edit"></i>
                            </button>
                        <button class="btn btn-sm eliminar-marca" style="color: #dc3545;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            });
            $("#tablaMarcas tbody").html(html);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar marcas:", error);
            $("#tablaMarcas tbody").html('<tr><td colspan="2" class="text-center">Error al cargar datos</td></tr>');
        }
    });
}

function cargarTablaModelos() {
    $.ajax({
        url: _URL + "/ajs/get/modelos",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let html = "";
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function (modelo) {
                html += `
                <tr data-id="${modelo.id}">
                    <td class="nombre-campo">${modelo.nombre}</td>
                    <td>
                        <button class="btn btn-sm editar-modelo" style="color: #0d6efd;">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm eliminar-modelo" style="color: #dc3545;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            });
            $("#tablaModelos tbody").html(html);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar modelos:", error);
            $("#tablaModelos tbody").html('<tr><td colspan="2" class="text-center">Error al cargar datos</td></tr>');
        }
    });
}

function cargarTablaEquipos() {
    $.ajax({
        url: _URL + "/ajs/get/equipos",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let html = "";
            if (typeof data === "string") {
                data = JSON.parse(data);
            }
            data.forEach(function (equipo) {
                html += `
                <tr data-id="${equipo.id}">
                    <td class="nombre-campo">${equipo.nombre}</td>
                    <td>
                        <button class="btn btn-sm editar-equipo" style="color: #0d6efd;">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm eliminar-equipo" style="color: #dc3545;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            });
            $("#tablaEquipos tbody").html(html);
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar equipos:", error);
            $("#tablaEquipos tbody").html('<tr><td colspan="2" class="text-center">Error al cargar datos</td></tr>');
        }
    });
}