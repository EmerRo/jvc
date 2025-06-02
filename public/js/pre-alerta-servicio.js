$(document).ready(() => {
  const app = new Vue({
    el: "#client",
    data: {
      prealerta: {
        num_doc: "",
        cliente_Rsocial: "",
      },
      cantidadEquipos: 1,
      equipos: [
        {
          marca: "",
          modelo: "",
          tipo: "",
          serie: "",
        },
      ],
      marcasDisponibles: [],
      modelosDisponibles: [],
      equiposDisponibles: [],
      ultimoNumeroSerie: 16820,
      // Datos para edición
      editando: {
        id_preAlerta: null,
        cliente_Rsocial: "",
        cliente_ruc: "",
        atencion_Encargado: "",
        fecha_ingreso: "",
        equipos: [],
      },
      maquinasIdenticas: false,
      cantidadMaquinasIdenticas: 1,
      seriesMultiples: "",
      equipoBase: {
        marca: "",
        modelo: "",
        tipo: "",
      },
      validationErrors: {
        num_doc: "",
        cliente_Rsocial: "",
        tecnico: "",
        fecha_ingreso: "",
        cantidadEquipos: "",
        marca: "",
        modelo: "",
        equipo: "",
        cantidad: "",
        series: "",
      },
      seriesCount: 0,
    },
    mounted() {
      // this.generarNumeroSerie()
      this.inicializarEquipos();
      this.cargarCatalogos();
    },
    methods: {
      validateForm() {
        let isValid = true;
        this.validationErrors = {
          num_doc: "",
          cliente_Rsocial: "",
          tecnico: "",
          fecha_ingreso: "",
          cantidadEquipos: "",
          marca: "",
          modelo: "",
          equipo: "",
          cantidad: "",
          series: "",
        };

        // Validar documento
        if (!this.prealerta.num_doc) {
          this.validationErrors.num_doc =
            "Por favor ingrese un número de documento";
          isValid = false;
        }

        // Validar cliente
        if (!this.prealerta.cliente_Rsocial) {
          this.validationErrors.cliente_Rsocial =
            "Por favor ingrese el nombre del cliente";
          isValid = false;
        }

        // Validar técnico
        if (!$("#atencion_Encargado").val()) {
          this.validationErrors.tecnico = "Por favor seleccione un técnico";
          isValid = false;
        }

        // Validar fecha
        if (!$("#fecha_ingreso").val()) {
          this.validationErrors.fecha_ingreso =
            "Por favor seleccione una fecha";
          isValid = false;
        }

        // Validar cantidad de equipos
        if (!this.cantidadEquipos || this.cantidadEquipos < 1) {
          this.validationErrors.cantidadEquipos =
            "La cantidad debe ser mayor a 0";
          isValid = false;
        }

        if (this.maquinasIdenticas) {
          // Validaciones para máquinas idénticas
          if (!this.equipoBase.marca) {
            this.validationErrors.marca = "Por favor seleccione una marca";
            isValid = false;
          }

          if (!this.equipoBase.modelo) {
            this.validationErrors.modelo = "Por favor seleccione un modelo";
            isValid = false;
          }

          if (!this.equipoBase.tipo) {
            this.validationErrors.equipo = "Por favor seleccione un equipo";
            isValid = false;
          }

          if (
            !this.cantidadMaquinasIdenticas ||
            this.cantidadMaquinasIdenticas < 1
          ) {
            this.validationErrors.cantidad = "La cantidad debe ser mayor a 0";
            isValid = false;
          }

          const series = this.seriesMultiples
            .split(/[\n,\s]+/)
            .filter((s) => s.trim());
          if (series.length !== this.cantidadMaquinasIdenticas) {
            this.validationErrors.series = `Debe ingresar ${this.cantidadMaquinasIdenticas} números de serie (actualmente: ${series.length})`;
            isValid = false;
          }
        } else {
          // Validar equipos individuales
          const equiposValidos = this.equipos.every((equipo, index) => {
            let equipoValido = true;

            if (!equipo.marca) {
              this.validationErrors[`equipo_${index}_marca`] =
                "Por favor seleccione una marca";
              equipoValido = false;
            }

            if (!equipo.modelo) {
              this.validationErrors[`equipo_${index}_modelo`] =
                "Por favor seleccione un modelo";
              equipoValido = false;
            }

            if (!equipo.tipo) {
              this.validationErrors[`equipo_${index}_tipo`] =
                "Por favor seleccione un tipo";
              equipoValido = false;
            }

            if (!equipo.serie) {
              this.validationErrors[`equipo_${index}_serie`] =
                "Por favor ingrese un número de serie";
              equipoValido = false;
            }

            return equipoValido;
          });

          if (!equiposValidos) {
            isValid = false;
          }
        }

        if (!isValid) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: "Por favor complete todos los campos requeridos",
          });
        }

        return isValid;
      },

      procesarSeriesMultiples() {
        if (!this.validateForm()) {
          return false;
        }

        if (!this.maquinasIdenticas || !this.seriesMultiples) return false;

        // Dividir las series por comas, saltos de línea o espacios
        const series = this.seriesMultiples
          .split(/[\n,\s]+/)
          .filter((serie) => serie.trim() !== "");

        // Validar que la cantidad coincida
        if (series.length !== parseInt(this.cantidadMaquinasIdenticas)) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: `La cantidad de series (${series.length}) no coincide con la cantidad de máquinas indicada (${this.cantidadMaquinasIdenticas})`,
          });
          return false;
        }

        // Crear los equipos con las series
        this.equipos = [];
        series.forEach((serie) => {
          this.equipos.push({
            marca: this.equipoBase.marca,
            modelo: this.equipoBase.modelo,
            tipo: this.equipoBase.tipo,
            serie: serie.trim(),
          });
        });

        this.cantidadEquipos = series.length;
        return true;
      },

      // Método para contar las series mientras el usuario escribe
      contarSeries() {
        const series = this.seriesMultiples
          .split(/[\n,\s]+/)
          .filter((s) => s.trim());
        this.seriesCount = series.length;

        // Actualizar mensaje de validación si hay una cantidad establecida
        if (this.cantidadMaquinasIdenticas > 0) {
          if (series.length !== this.cantidadMaquinasIdenticas) {
            this.validationErrors.series = `Debe ingresar ${this.cantidadMaquinasIdenticas} números de serie (actualmente: ${series.length})`;
          } else {
            this.validationErrors.series = "";
          }
        }
      },

      buscarDocumentSS() {
        const docLength = this.prealerta.num_doc.length;
        if (docLength === 8 || docLength === 11) {
          $("#loader-menor").show();
          this.prealerta.dir_pos = 1;

          _ajax(
            "/ajs/prealerta/doc/cliente",
            "POST",
            {
              doc: this.prealerta.num_doc,
            },
            (resp) => {
              $("#loader-menor").hide();
              console.log(resp);

              if (docLength === 8) {
                // Para DNI
                if (resp.success) {
                  this.prealerta.cliente_Rsocial =
                    resp.nombres +
                    " " +
                    (resp.apellidoPaterno ? resp.apellidoPaterno : "") +
                    " " +
                    (resp.apellidoMaterno ? resp.apellidoMaterno : "");
                  // Limpiar dirección para DNI
                  $("#direccion").val("");
                } else {
                  alertAdvertencia("Documento no encontrado");
                }
              } else if (docLength === 11) {
                // Para RUC
                if (resp.razonSocial) {
                  this.prealerta.cliente_Rsocial = resp.razonSocial;
                  // Solo mostrar dirección si el RUC empieza con 20
                  if (this.prealerta.num_doc.startsWith("20")) {
                    $("#direccion").val(resp.direccion || "");
                  } else {
                    $("#direccion").val("");
                  }
                } else {
                  alertAdvertencia("RUC no encontrado");
                }
              }
            }
          );
        } else {
          alertAdvertencia("Documento, DNI es 8 dígitos y RUC 11 dígitos");
        }
      },
      inicializarEquipos() {
        this.equipos = [
          {
            marca: "",
            modelo: "",
            tipo: "",
            serie: "",
          },
        ];
        // this.actualizarNumerosSerie()
      },

      cargarCatalogos() {
        $.get(_URL +"/ajs/get/marcas", (data) => {
          this.marcasDisponibles = JSON.parse(data);
        });
        $.get( _URL + "/ajs/get/modelos", (data) => {
          this.modelosDisponibles = JSON.parse(data);
        });
        $.get(_URL +"/ajs/get/equipos", (data) => {
          this.equiposDisponibles = JSON.parse(data);
        });
      },
      actualizarEquipos() {
        const cantidad = Math.max(
          1,
          Number.parseInt(this.cantidadEquipos) || 1
        );
        this.cantidadEquipos = cantidad;

        while (this.equipos.length < cantidad) {
          this.equipos.push({
            marca: "",
            modelo: "",
            tipo: "",
            serie: "",
          });
        }
        if (this.equipos.length > cantidad) {
          this.equipos = this.equipos.slice(0, cantidad);
        }
        this.$nextTick(() => {});
      },
      cargarDatosEdicion(id) {
        // Reiniciar el estado antes de cargar nuevos datos
        this.maquinasIdenticas = false;
        this.seriesMultiples = "";
        this.equipoBase = {
          marca: "",
          modelo: "",
          tipo: ""
        };
        this.cantidadMaquinasIdenticas = 1;
        this.editando = {
          id_preAlerta: null,
          cliente_Rsocial: "",
          cliente_ruc: "",
          atencion_Encargado: "",
          fecha_ingreso: "",
          observaciones: "",
          equipos: []
        };
        
        $("#loader-menor").show();
        $.ajax({
          url: _URL + "/ajs/prealerta/detalles",
          type: "POST",
          data: { id: id },
          success: (response) => {
            $("#loader-menor").hide();
            try {
              // Verificar si la respuesta es HTML (comienza con <!DOCTYPE o <html)
              if (typeof response === "string" && (response.trim().toLowerCase().startsWith("<!doctype") || response.trim().toLowerCase().startsWith("<html"))) {
                console.error("La respuesta del servidor es HTML, no JSON:", response.substring(0, 100) + "...");
                throw new Error("El servidor devolvió HTML en lugar de JSON. Posible sesión expirada.");
              }
              
              const datos = typeof response === "object" ? response : JSON.parse(response);
      
              // Cargar datos principales
              this.editando.id_preAlerta = datos.id_preAlerta;
              this.editando.cliente_Rsocial = datos.cliente_razon_social;
              this.editando.cliente_ruc = datos.cliente_ruc;
              this.editando.atencion_Encargado = datos.atencion_encargado;
              this.editando.fecha_ingreso = datos.fecha_ingreso;
              this.editando.observaciones = datos.observaciones || "";
      
              // Cargar equipos
              this.editando.equipos = datos.equipos || [];
              
              // Detectar si los equipos son idénticos y hay más de uno
              if (this.editando.equipos.length > 1) {
                this.prepararEdicionEquiposIdenticos();
              }
      
              // Actualizar campos del formulario
              $("#edit_id_preAlerta").val(datos.id_preAlerta);
              $("#edit_cliente_Rsocial").val(datos.cliente_razon_social);
              $("#edit_cliente_ruc").val(datos.cliente_ruc);
              $("#edit_atencion_Encargado").val(datos.atencion_encargado);
              $("#edit_fecha_ingreso").val(datos.fecha_ingreso);
              $("#edit_observaciones").val(datos.observaciones || "");
      
              // Mostrar el modal
              $("#modalEditar").modal("show");
            } catch (error) {
              console.error("Error al procesar los datos:", error);
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error al cargar los datos para edición. " + (error.message || ""),
                footer: '<a href="javascript:location.reload()">Recargar la página</a>'
              });
            }
          },
          error: (xhr, status, error) => {
            $("#loader-menor").hide();
            console.error("Error en la petición:", { xhr, status, error });
            
            // Verificar si la respuesta es HTML
            if (xhr.responseText && (xhr.responseText.trim().toLowerCase().startsWith("<!doctype") || xhr.responseText.trim().toLowerCase().startsWith("<html"))) {
              console.error("La respuesta del servidor es HTML, no JSON:", xhr.responseText.substring(0, 100) + "...");
              Swal.fire({
                icon: "error",
                title: "Error de sesión",
                text: "Es posible que tu sesión haya expirado. Intenta recargar la página.",
                footer: '<a href="javascript:location.reload()">Recargar la página</a>'
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "Error al obtener los datos del registro",
                footer: '<a href="javascript:location.reload()">Recargar la página</a>'
              });
            }
          },
          // Agregar timeout para evitar esperas largas
          timeout: 15000 // 15 segundos
        });
      },

      agregarEquipoEdicion() {
        this.editando.equipos.push({
          id: null,
          marca: "",
          equipo: "",
          modelo: "",
          numero_serie: "",
        });
      },
      eliminarEquipoEdicion(index) {
        this.editando.equipos.splice(index, 1);
      },
      guardarEdicion() {
        if (
          !this.editando.cliente_Rsocial ||
          !this.editando.atencion_Encargado ||
          !this.editando.fecha_ingreso
        ) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: "Por favor complete todos los campos requeridos",
          });
          return;
        }

        // Si estamos en modo máquinas idénticas, procesar las series
        if (this.maquinasIdenticas) {
          if (!this.procesarSeriesMultiplesEdicion()) {
            return;
          }
        } else {
          // Validar equipos individuales
          const equiposValidos = this.editando.equipos.every(
            (equipo) =>
              equipo.marca &&
              equipo.equipo &&
              equipo.modelo &&
              equipo.numero_serie
          );

          if (!equiposValidos) {
            Swal.fire({
              icon: "warning",
              title: "Advertencia",
              text: "Por favor complete todos los datos de los equipos",
            });
            return;
          }
        }

        const data = {
          id_preAlerta: this.editando.id_preAlerta,
          cliente_razon_social: this.editando.cliente_Rsocial,
          cliente_ruc: this.editando.cliente_ruc,
          atencion_encargado: this.editando.atencion_Encargado,
          fecha_ingreso: this.editando.fecha_ingreso,
          observaciones: this.editando.observaciones,
          equipos: this.editando.equipos,
        };

        $("#loader-menor").show();

        $.ajax({
          type: "POST",
          url: _URL +"/ajs/prealerta/update",
          data: data,
          success: (resp) => {
            $("#loader-menor").hide();
            try {
              const response =
                typeof resp === "object" ? resp : JSON.parse(resp);
              if (response && response.success) {
                tabla_clientes.ajax.reload(null, false);
                Swal.fire({
                  icon: "success",
                  title: "Éxito",
                  text: "Registro actualizado correctamente",
                });
                $("#modalEditar").modal("hide");
              } else {
                throw new Error(response.error || "Error al actualizar");
              }
            } catch (error) {
              Swal.fire({
                icon: "error",
                title: "Error",
                text:
                  error.message ||
                  "Error al procesar la respuesta del servidor",
              });
            }
          },
          error: (xhr, status, error) => {
            $("#loader-menor").hide();
            console.error("Error en la petición:", error);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error al intentar actualizar el registro",
            });
          },
        });
      },
      procesarSeriesMultiplesEdicion() {
        if (
          !this.equipoBase.marca ||
          !this.equipoBase.modelo ||
          !this.equipoBase.tipo
        ) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: "Por favor complete todos los campos de marca, modelo y equipo",
          });
          return false;
        }

        if (!this.seriesMultiples) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: "Por favor ingrese los números de serie",
          });
          return false;
        }

        // Dividir las series por comas, saltos de línea o espacios
        const series = this.seriesMultiples
          .split(/[\n,\s]+/)
          .filter((serie) => serie.trim() !== "");

        // Validar que la cantidad coincida
        if (series.length !== parseInt(this.cantidadMaquinasIdenticas)) {
          Swal.fire({
            icon: "warning",
            title: "Advertencia",
            text: `La cantidad de series (${series.length}) no coincide con la cantidad de máquinas indicada (${this.cantidadMaquinasIdenticas})`,
          });
          return false;
        }

        // Crear los equipos con las series
        this.editando.equipos = [];
        series.forEach((serie) => {
          this.editando.equipos.push({
            marca: this.equipoBase.marca,
            modelo: this.equipoBase.modelo,
            equipo: this.equipoBase.tipo,
            numero_serie: serie.trim(),
          });
        });

        return true;
      },
      detectarEquiposIdenticos() {
        if (!this.editando.equipos || this.editando.equipos.length <= 1)
          return false;

        // Obtener el primer equipo como referencia
        const primerEquipo = this.editando.equipos[0];

        // Verificar si todos los equipos tienen la misma marca, modelo y tipo
        const sonIdenticos = this.editando.equipos.every(
          (equipo) =>
            equipo.marca === primerEquipo.marca &&
            equipo.modelo === primerEquipo.modelo &&
            equipo.equipo === primerEquipo.equipo
        );

        return sonIdenticos;
      },
      prepararEdicionEquiposIdenticos() {
        if (!this.detectarEquiposIdenticos()) return;

        // Configurar el equipo base con los valores comunes
        this.equipoBase = {
          marca: this.editando.equipos[0].marca,
          modelo: this.editando.equipos[0].modelo,
          tipo: this.editando.equipos[0].equipo,
        };

        // Preparar las series
        this.seriesMultiples = this.editando.equipos
          .map((eq) => eq.numero_serie)
          .join("\n");
        this.cantidadMaquinasIdenticas = this.editando.equipos.length;
        this.maquinasIdenticas = true;
      },
    },
    watch: {
      cantidadEquipos: function (newVal, oldVal) {
        this.actualizarEquipos();
      },
      seriesMultiples: function (newVal) {
        this.contarSeries();
      },
    },
  });

  // DataTables initialization
  tabla_clientes = $("#tabla_clientes").DataTable({
    paging: true,
    bFilter: true,
    ordering: true,
    searching: true,
    destroy: true,
    "responsive": true, // Habilitar responsividad
    "scrollX": false,   // Deshabilitar scroll horizontal
    "autoWidth": false, // Deshabilitar auto-ancho
    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip', 
    ajax: {
      url: _URL + "/ajs/prealerta/render",
      method: "POST",
      dataSrc: "",
    },
    language: {
      url: "ServerSide/Spanish.json",
    },
    columns: [
      {
        data: null,
        class: "text-center",
        render: (data, type, row, meta) => meta.row + 1,
      },
      { data: "cliente_razon_social", class: "text-center" },
      { data: "cliente_ruc", class: "text-center" },

      { data: "atencion_encargado", class: "text-center" },

      { data: "fecha_ingreso", class: "text-center" },
      {
        data: null,
        class: "text-center",
        render: (data, type, row) => `
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-info btn-ver-detalles" data-id="${row.id_preAlerta}">
                        <i class="fa fa-eye"></i>
                    </button>
                    <button data-id="${row.id_preAlerta}" class="btn btn-warning btnEditar">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button data-id="${row.id_preAlerta}" class="btn btn-danger btnBorrar">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `,
      },
      {
        data: null,
        class: "text-center",
        render: (data, type, row) => {
          // Convertir a número para asegurar una comparación correcta
          const tieneCotizacion = Number.parseInt(row.tiene_cotizacion);
          
          // Estilo común para ambos badges
          const badgeStyle = `
            display: inline-block;
            width: 130px;
            text-align: center;
            padding: 5px 10px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 0.70rem;
            text-transform: uppercase;
            white-space: nowrap;
          `;
          
          if (tieneCotizacion === 1) {
            // Badge verde para "COTIZACIÓN LISTA"
            return `
              <span 
                onclick="verCotizacion(${row.id_preAlerta})" 
                class="badge" 
                style="${badgeStyle} background-color: #28a745; color: white; cursor: pointer;"
              >
                <i class="fa fa-check-circle" style="margin-right: 5px;"></i>COTIZACIÓN LISTA
              </span>`;
          } else {
            // Badge naranja para "SIN COTIZACIÓN"
            return `
              <span 
                class="badge" 
                style="${badgeStyle} background-color: #ffa500; color: white;"
              >
                <i class="fa fa-clock" style="margin-right: 5px;"></i>SIN COTIZACIÓN
              </span>`;
          }
        },
      },
    ],
    drawCallback: function () {
      // Reinicializar tooltips después de cada redibujado
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
  });

  window.verCotizacion = () => {
    // Redirigir a la página de cotización
    window.location.href = `/taller/coti/view/`;
  };
  function mostrarDetalles(id) {
    $.ajax({
      url: _URL +"/ajs/prealerta/detalles",
      type: "POST",
      data: { id: id },
      success: (response) => {
        try {
          // Verificar si la respuesta es HTML
          if (typeof response === "string" && (response.trim().toLowerCase().startsWith("<!doctype") || response.trim().toLowerCase().startsWith("<html"))) {
            console.error("La respuesta del servidor es HTML, no JSON:", response.substring(0, 100) + "...");
            throw new Error("El servidor devolvió HTML en lugar de JSON. Posible sesión expirada.");
          }
          
          var detalles = typeof response === "object" ? response : JSON.parse(response);
          
        var contenidoModal = `
                <div class="card border-danger mb-2">
                    <div class="card-header bg-secondary p-2">Información de Pre Alerta</div>
                    <div class="card-body p-2">
                        <div class="row g-0">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Cliente:</strong> ${detalles.cliente_razon_social}</p>
                                <p class="mb-1"><strong>Técnico:</strong> ${detalles.atencion_encargado}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Documento:</strong> ${detalles.cliente_ruc}</p>
                                <p class="mb-1"><strong>Fecha de Ingreso:</strong> ${detalles.fecha_ingreso}</p>
                            </div>
                        </div>
                    </div>
                </div>
  
                <div class="card border-danger mb-2">
                    <div class="card-header bg-secondary p-2">
                        Equipos Registrados: ${detalles.equipos.length}
                    </div>
                    <div class="card-body p-2">
                     <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
    <table class="table table-bordered table-striped mb-0">
        <thead class="table-danger sticky-top bg-danger">
            <tr>
                <th>#</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Equipo</th>
                <th>Número de Serie</th>
            </tr>
        </thead>
                                <tbody>
            `;

        detalles.equipos.forEach((equipo, index) => {
          contenidoModal += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${equipo.marca}</td>
                        <td>${equipo.modelo}</td>
                        <td>${equipo.equipo}</td>
                        <td>${equipo.numero_serie}</td>
                    </tr>
                `;
        });

        contenidoModal += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card border-danger">
                    <div class="card-header bg-secondary p-2">
                        Observaciones
                    </div>
                    <div class="card-body p-2">
                        <p class="mb-0">${
                          detalles.observaciones || "Sin observaciones"
                        }</p>
                    </div>
                </div>
            `;

        $("#modalDetalles .modal-body").html(contenidoModal);
        $("#modalDetalles").modal("show");
      } catch (error) {
        console.error("Error al procesar la respuesta:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al procesar los detalles. " + (error.message || ""),
          footer: '<a href="javascript:location.reload()">Recargar la página</a>'
        });
      }
    },
    error: (xhr, status, error) => {
      // Verificar si la respuesta es HTML
      if (xhr.responseText && (xhr.responseText.trim().toLowerCase().startsWith("<!doctype") || xhr.responseText.trim().toLowerCase().startsWith("<html"))) {
        console.error("La respuesta del servidor es HTML, no JSON:", xhr.responseText.substring(0, 100) + "...");
        Swal.fire({
          icon: "error",
          title: "Error de sesión",
          text: "Es posible que tu sesión haya expirado. Intenta recargar la página.",
          footer: '<a href="javascript:location.reload()">Recargar la página</a>'
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "No se pudieron cargar los detalles. " + error,
          footer: '<a href="javascript:location.reload()">Recargar la página</a>'
        });
      }
      console.error("Error fetching details:", error, xhr, status);
    },
    timeout: 15000 // 15 segundos
  });

  }

  // Agregar prealerta
  $("#submitRegistro").click(() => {
    if (!app) {
      console.error("La aplicación Vue no está inicializada");
      return;
    }

    // Validar el formulario antes de procesar
    if (!app.validateForm()) {
      return;
    }

    //procesar series multiples
    if (app.maquinasIdenticas) {
      if (!app.procesarSeriesMultiples()) {
        return; // Si hay error en el procesamiento, detener
      }
    }

    // Validar campos requeridos
    if (
      !app.prealerta.cliente_Rsocial ||
      !$("#atencion_Encargado").val() ||
      !$("#fecha_ingreso").val()
    ) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Por favor complete todos los campos requeridos",
      });
      return;
    }

    // Validar equipos
    const equiposValidos = app.equipos.every(
      (equipo) => equipo.marca && equipo.modelo && equipo.tipo && equipo.serie
    );

    if (!equiposValidos) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Por favor complete todos los datos de los equipos",
      });
      return;
    }

    // Preparar datos para enviar
    const data = {
      cliente_Rsocial: app.prealerta.cliente_Rsocial,
      num_doc: app.prealerta.num_doc,
      atencion_Encargado: $("#atencion_Encargado").val(),
      fecha_ingreso: $("#fecha_ingreso").val(),
      origen: "Ord Servicio",
      direccion: $("#direccion").val(),
      observaciones: $("#observaciones").val(),
      equipos: app.equipos.map((equipo) => ({
        marca: equipo.marca,
        modelo: equipo.modelo,
        equipo: equipo.tipo,
        numero_serie: equipo.serie,
      })),
    };

    $("#loader-menor").show();

    // Enviar datos
    $.ajax({
      type: "POST",
      url: _URL + "/ajs/prealerta/add",
      data: data,
      success: (resp) => {
        $("#loader-menor").hide();
        try {
          const response = typeof resp === "object" ? resp : JSON.parse(resp);
          if (response && !response.error) {
            tabla_clientes.ajax.reload(null, false);
            Swal.fire({
              icon: "success",
              title: "¡Buen trabajo!",
              text: "Registro Exitoso",
            });
            $("#modalAgregar").modal("hide");
            $("#frmClientesAgregar").trigger("reset");
            app.equipos = [];
            app.cantidadEquipos = 1;
            app.inicializarEquipos();
            app.maquinasIdenticas = false;
            app.seriesMultiples = "";
            app.equipoBase = { marca: "", modelo: "", tipo: "" };
            app.validationErrors = {
              marca: "",
              modelo: "",
              equipo: "",
              cantidad: "",
              series: "",
            };
          } else {
            throw new Error(response.error || "Error al guardar");
          }
        } catch (error) {
          Swal.fire({
            icon: "error",
            title: "Error",
            text:
              error.message || "Error al procesar la respuesta del servidor",
          });
        }
      },
      error: (xhr, status, error) => {
        $("#loader-menor").hide();
        console.error("Error en la petición:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al intentar guardar el registro",
        });
      },
    });
  });

  // Eliminar prealerta
  $("#tabla_clientes").on("click", ".btnBorrar", function () {
    var id = $(this).data("id");
    const idData = {
      name: "idDelete",
      value: id,
    };
    Swal.fire({
      title: "¿Deseas borrar el registro?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: _URL + "/ajs/prealerta/delete",
          type: "post",
          data: idData,
          success: (resp) => {
            tabla_clientes.ajax.reload(null, false);
            Swal.fire(
              "¡Buen trabajo!",
              "Registro Borrado Exitosamente",
              "success"
            );
          },
        });
      }
    });
  });

  // Event listener para el botón de editar
  $(document).on("click", ".btnEditar", function () {
    var id = $(this).data("id");
    app.cargarDatosEdicion(id);
  });

  // Replace the error-prone edit button click handler with this improved version:

  $("#tabla_clientes").on("click", ".btnEditar", function () {
    const id = $(this).data("id");
    $("#loader-menor").show();

    $.ajax({
      url: _URL + "/ajs/prealerta/detalles",
      type: "POST",
      data: { id: id },
      success: (resp) => {
        $("#loader-menor").hide();
        try {
          const data = typeof resp === "string" ? JSON.parse(resp) : resp;

          if (!data || data.error) {
            throw new Error(data.error || "Datos inválidos");
          }

          // Llenar el formulario de edición
          $("#idCliente").val(data.id_preAlerta);
          $("#edit_cliente_razon_social").val(data.cliente_razon_social);
          $("#edit_cliente_ruc").val(data.cliente_ruc);
          $("#edit_atencion_encargado").val(data.atencion_encargado);
          $("#edit_fecha_ingreso").val(data.fecha_ingreso);

          // Limpiar y llenar la lista de equipos
          $("#listaEquipos").empty();
          if (data.equipos && Array.isArray(data.equipos)) {
            data.equipos.forEach((equipo, index) => {
              $("#listaEquipos").append(`
              <div class="equipo-item mb-3 p-2 border rounded">
                <h6>Equipo ${index + 1}</h6>
                <div class="row">
                  <div class="col-md-3">
                    <label>Marca</label>
                    <input type="text" class="form-control equipo-marca" value="${
                      equipo.marca || ""
                    }" required>
                  </div>
                  <div class="col-md-3">
                    <label>Modelo</label>
                    <input type="text" class="form-control equipo-modelo" value="${
                      equipo.modelo || ""
                    }" required>
                  </div>
                  <div class="col-md-3">
                    <label>Tipo</label>
                    <input type="text" class="form-control equipo-tipo" value="${
                      equipo.equipo || ""
                    }" required>
                  </div>
                  <div class="col-md-3">
                    <label>Nº Serie</label>
                    <input type="text" class="form-control equipo-serie" value="${
                      equipo.numero_serie || ""
                    }" required>
                  </div>
                </div>
              </div>
            `);
            });
          }

          $("#modalEditar").modal("show");
        } catch (error) {
          console.error("Error al procesar datos:", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al cargar los datos del registro. Por favor, intente nuevamente.",
          });
        }
      },
      error: (xhr, status, error) => {
        $("#loader-menor").hide();
        console.error("Error en la petición:", { xhr, status, error });
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "No se pudo cargar los datos del registro",
        });
      },
    });
  });

  // Also update the submit handler for editing:
  $("#submitEditar").click(() => {
    if (!$("#frmClientesEditar")[0].checkValidity()) {
      $("#frmClientesEditar")[0].reportValidity();
      return;
    }

    $("#loader-menor").show();

    const datosPreAlerta = {
      id_preAlerta: $("#idCliente").val(),
      cliente_razon_social: $("#edit_cliente_razon_social").val(),
      cliente_ruc: $("#edit_cliente_ruc").val(),
      atencion_encargado: $("#edit_atencion_encargado").val(),
      fecha_ingreso: $("#edit_fecha_ingreso").val(),
    };

    const equipos = [];
    $(".equipo-item").each(function () {
      equipos.push({
        marca: $(this).find(".equipo-marca").val(),
        modelo: $(this).find(".equipo-modelo").val(),
        equipo: $(this).find(".equipo-tipo").val(),
        numero_serie: $(this).find(".equipo-serie").val(),
      });
    });

    $.ajax({
      type: "POST",
      url: _URL + "/ajs/prealerta/update",
      data: {
        ...datosPreAlerta,
        equipos: equipos,
      },
      success: (resp) => {
        $("#loader-menor").hide();
        try {
          const result = typeof resp === "string" ? JSON.parse(resp) : resp;

          if (result && result.success) {
            tabla_clientes.ajax.reload(null, false);
            $("#modalEditar").modal("hide");
            Swal.fire({
              icon: "success",
              title: "¡Éxito!",
              text: "Registro actualizado correctamente",
            });
          } else {
            throw new Error(result.error || "Error al actualizar");
          }
        } catch (error) {
          console.error("Error al procesar respuesta:", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo actualizar el registro",
          });
        }
      },
      error: (xhr, status, error) => {
        $("#loader-menor").hide();
        console.error("Error en la petición:", { xhr, status, error });
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Error al intentar actualizar el registro",
        });
      },
    });
  });

  // Agregar una función para actualizar el estado de la cotización
  function actualizarEstadoCotizacion(id_preAlerta) {
    $.ajax({
      url: _URL + "/ajs/prealerta/actualizar-estado-cotizacion",
      type: "POST",
      data: { id_preAlerta: id_preAlerta },
      success: (response) => {
        if (response.success) {
          tabla_clientes.ajax.reload(null, false);
        }
      },
    });
  }

  // Llamar a esta función periódicamente o cuando se guarde una cotización
  setInterval(() => {
    tabla_clientes.ajax.reload(null, false);
  }, 30000); // Actualizar cada 30 segundos, ajusta según necesites

  // Funciones para cargar las tablas en los modales
  function cargarTablaMarcas() {
    $.get("/ajs/get/marcas", (data) => {
      let html = "";
      JSON.parse(data).forEach((marca) => {
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
    $.get(_URL + "/ajs/get/modelos", (data) => {
      let html = "";
      JSON.parse(data).forEach((modelo) => {
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
    $.get(_URL + "/ajs/get/equipos", (data) => {
      let html = "";
      JSON.parse(data).forEach((equipo) => {
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
    $.get( _URL + "/ajs/get/tecnicos", (data) => {
      let html = "";
      JSON.parse(data).forEach((tecnico) => {
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

  // Función para mostrar alertas con SweetAlert2
  function mostrarAlerta(titulo, mensaje, tipo) {
    Swal.fire({
      title: titulo,
      text: mensaje,
      icon: tipo,
      confirmButtonText: "Aceptar",
      confirmButtonColor: "#3085d6",
      customClass: {
        confirmButton: "btn btn-primary",
        cancelButton: "btn btn-danger",
      },
      buttonsStyling: false,
    });
  }

  // Función para confirmar eliminación
  function confirmarEliminacion(mensaje) {
    return Swal.fire({
      title: "¿Está seguro?",
      text: mensaje,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, eliminar",
      cancelButtonText: "Cancelar",
      customClass: {
        confirmButton: "btn btn-danger me-2",
        cancelButton: "btn btn-secondary",
      },
      buttonsStyling: false,
    });
  }

  // Función genérica para habilitar edición en línea
  function habilitarEdicionEnLinea(td, tipo) {
    const texto = td.text();
    const id = td.parent().data("id");
    td.html(`
    <div class="input-group input-group-sm">
        <input type="text" class="form-control" value="${texto}">
        <button class="btn btn-success btn-guardar-${tipo}" data-id="${id}">
            <i class="fa fa-check"></i>
        </button>
        <button class="btn btn-danger btn-cancelar-edicion">
            <i class="fa fa-times"></i>
        </button>
    </div>
`);
  }

  // Función genérica para cancelar edición
  function cancelarEdicion(td, texto) {
    td.html(texto);
  }

  // Función para manejar la transparencia de los modales
  function handleModalBackdrop(modalId) {
    const mainModal = document.getElementById("modalAgregar");

    // Cuando se abre un modal secundario
    $(`#${modalId}`).on("show.bs.modal", () => {
      $(mainModal).addClass("blur-background");
      $(".modal-backdrop").addClass("modal-backdrop-blur");
    });

    // Cuando se cierra un modal secundario
    $(`#${modalId}`).on("hidden.bs.modal", () => {
      $(mainModal).removeClass("blur-background");
      $(".modal-backdrop").removeClass("modal-backdrop-blur");
    });
  }
  // Aplicar el efecto a todos los modales secundarios
  ["modalMarca", "modalModelo", "modalEquipo", "modalTecnico"].forEach(
    handleModalBackdrop
  );

  // Event listeners para edición en línea
  $(document).on("click", ".editar-marca", function () {
    const td = $(this).closest("tr").find(".nombre-campo");
    habilitarEdicionEnLinea(td, "marca");
  });

  $(document).on("click", ".editar-modelo", function () {
    const td = $(this).closest("tr").find(".nombre-campo");
    habilitarEdicionEnLinea(td, "modelo");
  });

  $(document).on("click", ".editar-equipo", function () {
    const td = $(this).closest("tr").find(".nombre-campo");
    habilitarEdicionEnLinea(td, "equipo");
  });

  $(document).on("click", ".editar-tecnico", function () {
    const td = $(this).closest("tr").find(".nombre-campo");
    habilitarEdicionEnLinea(td, "tecnico");
  });

  // Guardar edición en línea
  $(document).on("click", ".btn-guardar-marca", function () {
    const id = $(this).data("id");
    const td = $(this).closest("td");
    const nuevoNombre = td.find("input").val();

    $.ajax({
      url: "/ajs/update/marcas",
      type: "POST",
      data: { id: id, nombre: nuevoNombre },
      success: (response) => {
        cargarTablaMarcas();
        cargarSelect("marcas", "#marca");
        mostrarAlerta("Éxito", "Marca actualizada correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo actualizar la marca", "error");
      },
    });
  });

  $(document).on("click", ".btn-guardar-modelo", function () {
    const id = $(this).data("id");
    const td = $(this).closest("td");
    const nuevoNombre = td.find("input").val();

    $.ajax({
      url: "/ajs/update/modelos",
      type: "POST",
      data: { id: id, nombre: nuevoNombre },
      success: (response) => {
        cargarTablaModelos();
        cargarSelect("modelos", "#modelo");
        mostrarAlerta("Éxito", "Modelo actualizado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo actualizar el modelo", "error");
      },
    });
  });

  $(document).on("click", ".btn-guardar-equipo", function () {
    const id = $(this).data("id");
    const td = $(this).closest("td");
    const nuevoNombre = td.find("input").val();

    $.ajax({
      url: "/ajs/update/equipos",
      type: "POST",
      data: { id: id, nombre: nuevoNombre },
      success: (response) => {
        cargarTablaEquipos();
        cargarSelect("equipos", "#equipo");
        mostrarAlerta("Éxito", "Equipo actualizado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo actualizar el equipo", "error");
      },
    });
  });

  $(document).on("click", ".btn-guardar-tecnico", function () {
    const id = $(this).data("id");
    const td = $(this).closest("td");
    const nuevoNombre = td.find("input").val();

    $.ajax({
      url: "/ajs/update/tecnicos",
      type: "POST",
      data: { id: id, nombre: nuevoNombre },
      success: (response) => {
        cargarTablaTecnicos();
        cargarSelect("tecnicos", "#atencion_Encargado");
        mostrarAlerta("Éxito", "Técnico actualizado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo actualizar el técnico", "error");
      },
    });
  });

  // Cancelar edición
  $(document).on("click", ".btn-cancelar-edicion", function () {
    const td = $(this).closest("td");
    const textoOriginal = td.parent().find(".nombre-campo").text();
    cancelarEdicion(td, textoOriginal);
  });

  // Agregar nueva marca
  $("#btnAgregarMarca").click(() => {
    const nombre = $("#marca_nombre").val();
    if (!nombre) {
      mostrarAlerta("Error", "Por favor ingrese un nombre de marca", "error");
      return;
    }

    $.ajax({
      url: "/ajs/save/marcas",
      type: "POST",
      data: { nombre: nombre },
      success: (response) => {
        $("#marca_nombre").val("");
        cargarTablaMarcas();
        cargarSelect("marcas", "#marca");
        mostrarAlerta("Éxito", "Marca agregada correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo agregar la marca", "error");
      },
    });
  });

  // Agregar nuevo modelo
  $("#btnAgregarModelo").click(() => {
    const nombre = $("#modelo_nombre").val();
    if (!nombre) {
      mostrarAlerta("Error", "Por favor ingrese un nombre de modelo", "error");
      return;
    }

    $.ajax({
      url: "/ajs/save/modelos",
      type: "POST",
      data: { nombre: nombre },
      success: (response) => {
        $("#modelo_nombre").val("");
        cargarTablaModelos();
        cargarSelect("modelos", "#modelo");
        mostrarAlerta("Éxito", "Modelo agregado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo agregar el modelo", "error");
      },
    });
  });

  // Agregar nuevo equipo
  $("#btnAgregarEquipo").click(() => {
    const nombre = $("#equipo_nombre").val();
    if (!nombre) {
      mostrarAlerta("Error", "Por favor ingrese un nombre de equipo", "error");
      return;
    }

    $.ajax({
      url: "/ajs/save/equipos",
      type: "POST",
      data: { nombre: nombre },
      success: (response) => {
        $("#equipo_nombre").val("");
        cargarTablaEquipos();
        cargarSelect("equipos", "#equipo");
        mostrarAlerta("Éxito", "Equipo agregado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo agregar el equipo", "error");
      },
    });
  });

  // Agregar nuevo técnico
  $("#btnAgregarTecnico").click(() => {
    const nombre = $("#tecnico_nombre").val();
    if (!nombre) {
      mostrarAlerta("Error", "Por favor ingrese un nombre de técnico", "error");
      return;
    }

    $.ajax({
      url: "/ajs/save/tecnicos",
      type: "POST",
      data: { nombre: nombre },
      success: (response) => {
        $("#tecnico_nombre").val("");
        cargarTablaTecnicos();
        cargarSelect("tecnicos", "#atencion_Encargado");
        mostrarAlerta("Éxito", "Técnico agregado correctamente", "success");
      },
      error: () => {
        mostrarAlerta("Error", "No se pudo agregar el técnico", "error");
      },
    });
  });

  // Eliminar registros
  $(document).on("click", ".eliminar-marca", function () {
    const id = $(this).closest("tr").data("id");
    confirmarEliminacion("¿Está seguro de eliminar esta marca?").then(
      (result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/ajs/delete/marcas",
            type: "POST",
            data: { id: id },
            success: () => {
              cargarTablaMarcas();
              cargarSelect("marcas", "#marca");
              mostrarAlerta(
                "Éxito",
                "Marca eliminada correctamente",
                "success"
              );
            },
            error: () => {
              mostrarAlerta("Error", "No se pudo eliminar la marca", "error");
            },
          });
        }
      }
    );
  });

  $(document).on("click", ".eliminar-modelo", function () {
    const id = $(this).closest("tr").data("id");
    confirmarEliminacion("¿Está seguro de eliminar este modelo?").then(
      (result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/ajs/delete/modelos",
            type: "POST",
            data: { id: id },
            success: () => {
              cargarTablaModelos();
              cargarSelect("modelos", "#modelo");
              mostrarAlerta(
                "Éxito",
                "Modelo eliminado correctamente",
                "success"
              );
            },
            error: () => {
              mostrarAlerta("Error", "No se pudo eliminar el modelo", "error");
            },
          });
        }
      }
    );
  });

  $(document).on("click", ".eliminar-equipo", function () {
    const id = $(this).closest("tr").data("id");
    confirmarEliminacion("¿Está seguro de eliminar este equipo?").then(
      (result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/ajs/delete/equipos",
            type: "POST",
            data: { id: id },
            success: () => {
              cargarTablaEquipos();
              cargarSelect("equipos", "#equipo");
              mostrarAlerta(
                "Éxito",
                "Equipo eliminado correctamente",
                "success"
              );
            },
            error: () => {
              mostrarAlerta("Error", "No se pudo eliminar el equipo", "error");
            },
          });
        }
      }
    );
  });

  $(document).on("click", ".eliminar-tecnico", function () {
    const id = $(this).closest("tr").data("id");
    confirmarEliminacion("¿Está seguro de eliminar este técnico?").then(
      (result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/ajs/delete/tecnicos",
            type: "POST",
            data: { id: id },
            success: () => {
              cargarTablaTecnicos();
              cargarSelect("tecnicos", "#atencion_Encargado");
              mostrarAlerta(
                "Éxito",
                "Técnico eliminado correctamente",
                "success"
              );
            },
            error: () => {
              mostrarAlerta("Error", "No se pudo eliminar el técnico", "error");
            },
          });
        }
      }
    );
  });

  // Inicialización
  $("#modalMarca").on("show.bs.modal", cargarTablaMarcas);
  $("#modalModelo").on("show.bs.modal", cargarTablaModelos);
  $("#modalModelo").on("show.bs.modal", cargarTablaModelos);
  $("#modalEquipo").on("show.bs.modal", cargarTablaEquipos);
  $("#modalTecnico").on("show.bs.modal", cargarTablaTecnicos);

  $("#modalAgregar").on("show.bs.modal", () => {
    const today = new Date().toISOString().split("T")[0];
    $("#fecha_ingreso").val(today);
  });
  // Cargar selects iniciales
  function cargarSelect(tipo, selector) {
    return new Promise((resolve, reject) => {
      $.get(`${_URL}/ajs/get/${tipo}`, (data) => {
        try {
          let options = "";
          const resp = JSON.parse(data);
          $.each(resp, (i, v) => {
            options += `<option value="${v.nombre}">${v.nombre}</option>`;
          });
          $(selector).html(options);
          resolve();
        } catch (error) {
          console.error(`Error al cargar ${tipo}:`, error);
          reject(error);
        }
      }).fail(reject);
    });
  }

  Promise.all([
    cargarSelect("modelos", "#modelo"),
    cargarSelect("marcas", "#marca"),
    cargarSelect("equipos", "#equipo"),
    cargarSelect("tecnicos", "#atencion_Encargado"),
    cargarSelect("modelos", "#edit_modelo"),
    cargarSelect("marcas", "#edit_marca"),
    cargarSelect("equipos", "#edit_equipo"),
    cargarSelect("tecnicos", "#edit_atencion_encargado"),
  ]).catch((error) => {
    console.error("Error al cargar los selects:", error);
    mostrarAlerta(
      "Error",
      "No se pudieron cargar algunos datos. Por favor, recarga la página.",
      "error"
    );
  });

  // Event listener para el botón de ver detalles
  $(document).on("click", ".btn-ver-detalles", function () {
    var id = $(this).data("id");
    mostrarDetalles(id);
  });
  // Limpiar estado al cerrar el modal de edición
  $("#modalEditar").on("hidden.bs.modal", function () {
    if (app) {
      app.maquinasIdenticas = false;
      app.seriesMultiples = "";
      app.equipoBase = {
        marca: "",
        modelo: "",
        tipo: "",
      };
      app.cantidadMaquinasIdenticas = 1;
      app.editando = {
        id_preAlerta: null,
        cliente_Rsocial: "",
        cliente_ruc: "",
        atencion_Encargado: "",
        fecha_ingreso: "",
        equipos: [],
      };
    }
  });
});
