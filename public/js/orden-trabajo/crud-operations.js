// Operaciones CRUD

    // Funciones para cargar tablas
    function cargarTablaMarcas() {
        $.get("/jvc/ajs/get/marcas", function (data) {
            let html = '';
            JSON.parse(data).forEach(marca => {
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
        });
    }

    function cargarTablaModelos() {
        $.get("/jvc/ajs/get/modelos", function (data) {
            let html = '';
            JSON.parse(data).forEach(modelo => {
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
        });
    }

    function cargarTablaEquipos() {
        $.get("/jvc/ajs/get/equipos", function (data) {
            let html = '';
            JSON.parse(data).forEach(equipo => {
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
        });
    }

    function cargarTablaTecnicos() {
        $.get("/jvc/ajs/get/tecnicos", function (data) {
            let html = '';
            JSON.parse(data).forEach(tecnico => {
                html += `
                    <tr data-id="${tecnico.id}">
                        <td class="nombre-campo">${tecnico.nombre}</td>
                        <td>
                            <button class="btn btn-sm editar-tecnico" style="color: #0d6efd;">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm eliminar-tecnico" style="color: #dc3545;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $("#tablaTecnicos tbody").html(html);
        });
    }

    // Event handlers para CRUD
    $("#btnAgregarMarca, #btnAgregarModelo, #btnAgregarEquipo, #btnAgregarTecnico").click(function() {
        const tipo = this.id.replace('btnAgregar', '').toLowerCase();
        const nombre = $(`#${tipo}_nombre`).val();
        
        if (!nombre) {
            mostrarAlerta('Error', `Por favor ingrese un nombre de ${tipo}`, 'error');
            return;
        }

        $.ajax({
            url: `/jvc/ajs/save/${tipo}s`,
            type: "POST",
            data: { nombre: nombre },
            success: function(response) {
                $(`#${tipo}_nombre`).val('');
                window[`cargarTabla${tipo.charAt(0).toUpperCase() + tipo.slice(1)}s`]();
                cargarSelect(`${tipo}s`, `#${tipo}`);
                mostrarAlerta('Éxito', `${tipo.charAt(0).toUpperCase() + tipo.slice(1)} agregado correctamente`, 'success');
            },
            error: function() {
                mostrarAlerta('Error', `No se pudo agregar el ${tipo}`, 'error');
            }
        });
    });

    // Guardar registro
    $("#submitRegistro").click(function() {
        if (!app) {
            console.error("La aplicación Vue no está inicializada");
            return;
        }

        // Obtener los datos básicos del formulario
        let formData = new FormData($("#frmClientesAgregar")[0]);
        let equiposData = [];

        // Validar campos requeridos
        if (!formData.get('cliente_Rsocial') || !formData.get('atencion_Encargado') || !formData.get('fecha_ingreso')) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Por favor complete todos los campos requeridos",
            });
            return;
        }

        // Recopilar datos de los equipos desde Vue
        app.equipos.forEach((equipo, index) => {
            if (equipo.marca && equipo.modelo && equipo.tipo && equipo.serie) {
                equiposData.push({
                    marca: equipo.marca,
                    modelo: equipo.modelo,
                    equipo: equipo.tipo,
                    numero_serie: equipo.serie
                });
            }
        });

        // Validar que haya al menos un equipo con datos completos
        if (equiposData.length === 0) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Debe completar los datos de al menos un equipo",
            });
            return;
        }

        // Crear objeto con todos los datos
        let data = {
            cliente_Rsocial: formData.get('cliente_Rsocial'),
            num_doc: formData.get('num_doc'),
            atencion_Encargado: formData.get('atencion_Encargado'),
            fecha_ingreso: formData.get('fecha_ingreso'),
            origen: formData.get('origen'),
            equipos: equiposData
        };

        $("#loader-menor").show();

        $.ajax({
            type: "POST",
            url: _URL + "/ajs/prealerta/add",
            data: data,
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    let response = typeof resp === 'object' ? resp : JSON.parse(resp);
                    if (typeof response === "object" && !response.error) {
                        tabla_clientes.ajax.reload(null, false);
                        Swal.fire("¡Buen trabajo!", "Registro Exitoso", "success");
                        $("#modalAgregar").modal("hide");
                        $("body").removeClass("modal-open");
                        $("#frmClientesAgregar").trigger("reset");
                        app.equipos = [];
                        app.cantidadEquipos = 1;
                        app.inicializarEquipos();
                    } else {
                        throw new Error(response.error || 'Error al guardar');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: error.message || "Error al procesar la respuesta del servidor",
                    });
                }
            },
            error: function(xhr, status, error) {
                $("#loader-menor").hide();
                console.error("Error en la petición:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Error al intentar guardar el registro",
                });
            }
        });
    });

    // Eliminar registro
    $("#tabla_clientes").on("click", ".btnBorrar", function() {
        const id = $(this).data("id");
        confirmarEliminacion("¿Deseas borrar el registro?").then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: _URL + "/ajs/prealerta/delete",
                    type: "post",
                    data: { name: "idDelete", value: id },
                    success: function(resp) {
                        tabla_clientes.ajax.reload(null, false);
                        mostrarAlerta("¡Buen trabajo!", "Registro Borrado Exitosamente", "success");
                    }
                });
            }
        });
    });

    // Editar registro
    $("#tabla_clientes").on("click", ".btnEditar", function() {
        const id = $(this).data("id");
        $("#loader-menor").show();

        $.ajax({
            url: _URL + "/ajs/prealerta/get/" + id,
            type: "POST",
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    let data = typeof resp === 'object' ? resp : JSON.parse(resp);
                    if (Array.isArray(data)) {
                        data = data[0];
                    }
                    if (!data || !data.id_preAlerta) {
                        throw new Error('Datos inválidos');
                    }

                    $("#edit_id_preAlerta").val(data.id_preAlerta);
                    $("#edit_cliente_Rsocial").val(data.cliente_razon_social);
                    $("#edit_modelo").val(data.modelo);
                    $("#edit_marca").val(data.marca);
                    $("#edit_equipo").val(data.equipo);
                    $("#edit_atencion_Encargado").val(data.atencion_encargado);
                    $("#edit_numero_Serie").val(data.numero_serie);
                    $("#edit_fecha_ingreso").val(data.fecha_ingreso);

                    $("#modalEditar").modal('show');
                } catch (error) {
                    console.error("Error al procesar datos:", error, "Respuesta:", resp);
                    mostrarAlerta("Error", "Error al cargar los datos del registro. Por favor, intente nuevamente.", "error");
                }
            },
            error: function(xhr, status, error) {
                $("#loader-menor").hide();
                console.error("Error en la petición:", { xhr, status, error });
                mostrarAlerta("Error", "No se pudo cargar los datos del registro", "error");
            }
        });
    });

    // Actualizar registro
    $("#submitEditar").click(function() {
        if (!$("#frmClientesEditar")[0].checkValidity()) {
            $("#frmClientesEditar")[0].reportValidity();
            return;
        }

        $("#loader-menor").show();
        const formData = new FormData($("#frmClientesEditar")[0]);
        const jsonData = {};

        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        $.ajax({
            type: "POST",
            url: _URL + "/ajs/prealerta/update",
            data: jsonData,
            success: function(resp) {
                $("#loader-menor").hide();
                try {
                    const result = typeof resp === 'object' ? resp : JSON.parse(resp);
                    if (result && !result.error) {
                        tabla_clientes.ajax.reload(null, false);
                        $("#modalEditar").modal('hide');
                        mostrarAlerta("¡Éxito!", "Registro actualizado correctamente", "success");
                    } else {
                        throw new Error(result.error || 'Error al actualizar');
                    }
                } catch (error) {
                    console.error("Error al procesar respuesta:", error, "Respuesta:", resp);
                    mostrarAlerta("Error", "No se pudo actualizar el registro", "error");
                }
            },
            error: function(xhr, status, error) {
                $("#loader-menor").hide();
                console.error("Error en la petición:", { xhr, status, error });
                mostrarAlerta("Error", "Error al intentar actualizar el registro", "error");
            }
        });
    });


