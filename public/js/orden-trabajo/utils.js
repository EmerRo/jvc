// Funciones de utilidad
function mostrarAlerta(titulo, mensaje, tipo) {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo,
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#3085d6',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });
}

function confirmarEliminacion(mensaje) {
    return Swal.fire({
        title: '¿Está seguro?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    });
}

function habilitarEdicionEnLinea(td, tipo) {
    const texto = td.text();
    const id = td.parent().data('id');
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

function cancelarEdicion(td, texto) {
    td.html(texto);
}

function handleModalBackdrop(modalId) {
    const mainModal = document.getElementById('modalAgregar');
    $(`#${modalId}`).on('show.bs.modal', function () {
        $(mainModal).addClass('blur-background');
        $('.modal-backdrop').addClass('modal-backdrop-blur');
    });
    $(`#${modalId}`).on('hidden.bs.modal', function () {
        $(mainModal).removeClass('blur-background');
        $('.modal-backdrop').removeClass('modal-backdrop-blur');
    });
}

function cargarSelect(tipo, selector) {
    return new Promise((resolve, reject) => {
        $.get(`/jvc/ajs/get/${tipo}`, function (data) {
            try {
                let options = '';
                let resp = JSON.parse(data);
                $.each(resp, function (i, v) {
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

