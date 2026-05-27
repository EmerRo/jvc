/* public/js/series/cargar-equipo-tablas.js
   Funciones para cargar tablas de gestión de marcas, modelos y equipos */

function cargarTablaMarcas() {
    $.get(_URL + "/ajs/get/marcas", function (data) {
        if (typeof data === "string") data = JSON.parse(data);
        _allMarcas = data;

        let html = "";
        data.forEach(function (marca) {
            html += `
                <tr data-id="${marca.id}">
                    <td class="nombre-campo">${marca.nombre}</td>
                    <td>
                        <button class="btn btn-sm editar-marca" style="color:#0d6efd;" title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm eliminar-marca" style="color:#dc3545;" title="Eliminar">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $("#tablaMarcas tbody").html(html || '<tr><td colspan="2" class="text-center text-muted">Sin registros</td></tr>');
    }).fail(function () {
        $("#tablaMarcas tbody").html('<tr><td colspan="2" class="text-center text-danger">Error al cargar</td></tr>');
    });
}

function cargarTablaModelos() {
    $.get(_URL + "/ajs/get/modelos", function (data) {
        if (typeof data === "string") data = JSON.parse(data);
        _allModelos = data;

        let html = "";
        data.forEach(function (modelo) {
            html += `
                <tr data-id="${modelo.id}" data-marca-id="${modelo.marca_id || ''}">
                    <td class="marca-campo">${modelo.marca_nombre || '<span class="text-muted">—</span>'}</td>
                    <td class="nombre-campo">${modelo.nombre}</td>
                    <td>
                        <button class="btn btn-sm editar-modelo" style="color:#0d6efd;" title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm eliminar-modelo" style="color:#dc3545;" title="Eliminar">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $("#tablaModelos tbody").html(html || '<tr><td colspan="3" class="text-center text-muted">Sin registros</td></tr>');
    }).fail(function () {
        $("#tablaModelos tbody").html('<tr><td colspan="3" class="text-center text-danger">Error al cargar</td></tr>');
    });
}

function cargarTablaEquipos() {
    $.get(_URL + "/ajs/get/equipos", function (data) {
        if (typeof data === "string") data = JSON.parse(data);
        _allEquipos = data;

        let html = "";
        data.forEach(function (equipo) {
            html += `
                <tr data-id="${equipo.id}" data-marca-id="${equipo.marca_id || ''}" data-modelo-id="${equipo.modelo_id || ''}">
                    <td class="marca-campo">${equipo.marca_nombre || '<span class="text-muted">—</span>'}</td>
                    <td class="modelo-campo">${equipo.modelo_nombre || '<span class="text-muted">—</span>'}</td>
                    <td class="nombre-campo">${equipo.nombre}</td>
                    <td>
                        <button class="btn btn-sm editar-equipo" style="color:#0d6efd;" title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm eliminar-equipo" style="color:#dc3545;" title="Eliminar">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $("#tablaEquipos tbody").html(html || '<tr><td colspan="4" class="text-center text-muted">Sin registros</td></tr>');
    }).fail(function () {
        $("#tablaEquipos tbody").html('<tr><td colspan="4" class="text-center text-danger">Error al cargar</td></tr>');
    });
}
