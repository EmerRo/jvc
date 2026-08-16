<!-- Modal de Enviar Comprobante (WhatsApp + Email) -->
<!-- Componente reutilizable para ventas, cotizaciones, etc. -->
<div class="modal fade" id="modalEnviarComprobante" tabindex="-1" aria-labelledby="modalEnviarComprobanteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-rojo text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="modalEnviarComprobanteLabel">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Comprobante
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Información del documento -->
                <div class="mb-4 pb-3 border-bottom">
                    <p class="mb-0 fw-bold">
                        <span>Documento</span>
                        <span>-</span>
                        <span id="nombreDocumentoEnviar">-</span>
                    </p>
                </div>

                <!-- Formulario de Email -->
                <form id="formEnviarEmail" class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-envelope me-1 text-primary"></i> Enviar por Email
                    </label>
                    <div class="input-group">
                        <input type="email" class="form-control" id="emailEnviarComprobante"
                               name="email" placeholder="ejemplo@correo.com" required>
                        <button type="submit" class="btn bg-rojo">
                            <i class="fas fa-paper-plane"></i> Enviar
                        </button>
                    </div>
                    <input type="hidden" id="linkDocumentoEmail" name="link" value="">
                    <input type="hidden" id="nombreArchivoEmail" name="nombrefile" value="">
                </form>

                <hr>

                <!-- Formulario de WhatsApp -->
                <form id="formEnviarWhatsApp">
                    <label class="form-label fw-bold">
                        <i class="fab fa-whatsapp me-1 text-success"></i> Enviar por WhatsApp
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">+51</span>
                        <input type="tel" class="form-control" id="whatsappEnviarComprobante"
                               name="numero" placeholder="987654321" maxlength="9" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <button type="submit" class="btn bg-rojo">
                            <i class="fab fa-whatsapp"></i> Enviar
                        </button>
                    </div>
                    <input type="hidden" id="linkDocumentoWhatsApp" name="link" value="">
                    <input type="hidden" id="mensajeWhatsApp" name="mensaje" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="border-rojo bg-white" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales para el modal de envío
var datosEnvioComprobante = {
    id: null,
    tipo: null, // 'venta', 'cotizacion', etc.
    numero: null,
    link: null,
    linkDescarga: null,
    nombreArchivo: null,
    email: null,
    telefono: null
};

/**
 * Abre el modal de enviar comprobante
 * @param {Object} opciones - Opciones de configuración
 * @param {string} opciones.tipo - Tipo de documento ('venta', 'cotizacion')
 * @param {number} opciones.id - ID del documento
 * @param {string} opciones.numero - Número del documento para mostrar
 * @param {string} opciones.link - Link del documento (para WhatsApp)
 * @param {string} opciones.linkDescarga - Link de descarga (para Email)
 * @param {string} opciones.nombreArchivo - Nombre del archivo
 * @param {string} opciones.email - Email del cliente (opcional)
 * @param {string} opciones.telefono - Teléfono del cliente (opcional)
 */
function abrirModalEnviarComprobante(opciones) {
    datosEnvioComprobante = {
        id: opciones.id || null,
        tipo: opciones.tipo || 'documento',
        numero: opciones.numero || '',
        link: opciones.link || '',
        linkDescarga: opciones.linkDescarga || opciones.link || '',
        nombreArchivo: opciones.nombreArchivo || '',
        email: opciones.email || '',
        telefono: opciones.telefono || ''
    };

    // Actualizar información visual
    $('#nombreDocumentoEnviar').text(datosEnvioComprobante.numero || 'Sin número');

    // Pre-llenar campos si hay datos
    $('#emailEnviarComprobante').val(datosEnvioComprobante.email);
    $('#whatsappEnviarComprobante').val(datosEnvioComprobante.telefono);

    // Configurar links ocultos
    $('#linkDocumentoEmail').val(datosEnvioComprobante.linkDescarga);
    $('#nombreArchivoEmail').val(datosEnvioComprobante.nombreArchivo);
    $('#linkDocumentoWhatsApp').val(datosEnvioComprobante.link);

    // Construir mensaje de WhatsApp según el tipo
    let mensaje = '';
    if (datosEnvioComprobante.tipo === 'cotizacion') {
        mensaje = `Hola, te envío la cotización ${datosEnvioComprobante.numero} para que puedas revisarla.\n\nPuedes verla aquí: ${datosEnvioComprobante.link}\n\nSi tienes alguna consulta, no dudes en escribirme.`;
    } else if (datosEnvioComprobante.tipo === 'venta') {
        mensaje = `Hola, te envío el comprobante de venta ${datosEnvioComprobante.numero}.\n\nPuedes verlo aquí: ${datosEnvioComprobante.link}\n\nGracias por tu preferencia.`;
    } else {
        mensaje = `Te comparto el documento ${datosEnvioComprobante.numero}.\n\nLink: ${datosEnvioComprobante.link}`;
    }
    $('#mensajeWhatsApp').val(mensaje);

    // Mostrar modal
    $('#modalEnviarComprobante').modal('show');
}

// Manejar envío por Email
$(document).on('submit', '#formEnviarEmail', function(e) {
    e.preventDefault();

    const email = $('#emailEnviarComprobante').val().trim();
    if (!email) {
        alertAdvertencia('Ingrese un correo electrónico válido');
        return;
    }

    $("#loader-menor").show();

    $.ajax({
        url: _URL + "/ajs/send/comprobante/email",
        type: "POST",
        dataType: "json", // Especificar que esperamos JSON
        data: {
            email: email,
            link: $('#linkDocumentoEmail').val(),
            nombrefile: $('#nombreArchivoEmail').val()
        },
        success: function(resp) {
            $("#loader-menor").hide();
            
            // Validar que la respuesta sea un objeto
            if (typeof resp === 'string') {
                try {
                    resp = JSON.parse(resp);
                } catch (e) {
                    console.error('Error parseando respuesta:', e);
                    console.log('Respuesta recibida:', resp);
                    alertAdvertencia("Error en la respuesta del servidor");
                    return;
                }
            }
            
            if (resp.res) {
                alertExito("Correo enviado correctamente");
                $('#modalEnviarComprobante').modal('hide');
            } else {
                alertAdvertencia(resp.msg || "No se pudo enviar el correo");
            }
        },
        error: function(xhr, status, error) {
            $("#loader-menor").hide();
            console.error('Error AJAX:', error);
            console.log('Respuesta del servidor:', xhr.responseText);
            alertError("Error al enviar el correo");
        }
    });
});

// Manejar envío por WhatsApp
$(document).on('submit', '#formEnviarWhatsApp', function(e) {
    e.preventDefault();

    const numero = $('#whatsappEnviarComprobante').val().trim();
    if (!numero || numero.length !== 9) {
        alertAdvertencia('Ingrese un número de 9 dígitos');
        return;
    }

    const mensaje = $('#mensajeWhatsApp').val();
    const whatsappUrl = `https://api.whatsapp.com/send?phone=51${numero}&text=${encodeURIComponent(mensaje)}`;

    $('#modalEnviarComprobante').modal('hide');
    window.open(whatsappUrl, '_blank');
});
</script>
