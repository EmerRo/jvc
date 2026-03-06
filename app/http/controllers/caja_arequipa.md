Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 1 de 12
Documento de Integración
CAJA AREQUIPA y EMPRESA
Versión: V.1
Caja Municipal Arequipa
Junio 2018
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 2 de 12
Índice
1. Introducción ........................................................................................ 4
2. Especificación Funcional ................................................................... 4
3. Comunicaciones.................................................................................. 4
4. Especificación Técnica ....................................................................... 5
5. Servicios Web ...................................................................................... 5
5.1. WS#1 Consulta ................................................................................................. 5
5.2. WS#2 Pago ........................................................................................................ 8
5.3. WS#3 Extorno .................................................................................................. 9
6. CONCILIACION .................................................................................. 10
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 3 de 12
Historial de versiones
La siguiente tabla describe la historia de modificación de la Guía Operativa, de la más
reciente a la más antigua, para propósitos de seguimiento.
Versión
(V.X)
Fecha
dd/mm/aaaa Modificaciones Modificado por
V.1 15/01/2018 Documento Inicial Hernan Laqui
V.2 14/06/2018 Modificación Hernan Laqui
V.3 22/03/2019 Actualización Hernan Laqui
V.4 26/10/2023 Actualización Frank Pinto
Charming Pilares
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 4 de 12
1. Introducción
La Caja Arequipa proporciona un canal de recaudo para realizar la integración con cualquier EMPRESA
interesada y se establecerá la comunicación para las diferentes transacciones que se requiera.
El objetivo de este documento es describir el flujo funcional de pago entre la Caja Arequipa y EMPRESA,
la especificación técnica de los servicios Web expuestos por la EMPRESA, así como la estructura del
archivo de conciliación que se envía a la entidad recaudadora.
2. Especificación Funcional
El proceso de pago entre la CAJA y EMPRESA comprende el siguiente flujo:
 La EMPRESA genera un Código de Contrato y se la da a sus clientes
 El Cliente se acerca a la CAJA y solicita efectuar el pago de la EMPRESA proporcionando el
Código de Contrato
 La CAJA consulta a la EMPRESA la información del Código de Contrato para obtener la
información del monto y cliente
 La CAJA procesa el pago de la EMPRESA.
El medio de comunicación entre la CAJA y EMPRESA serán WEB SERVICES bajo la arquitectura REST
3. Comunicaciones
La CAJA accederá a los Web Services que expondrá la EMPRESA bajo las siguientes modalidades siempre
y cuando cumplan las premisas indicadas:
 Interconexión vía Bancared
La EMPRESA debe de pertenecer a la red de Bancos BANCARED, el proceso de configuración se
efectuaría entre el encargado de la EMPRESA, Caja y Asbanc-Bancared.
La EMPRESA debe remitir la IP NAT asignada a su servicio.
 Interconexión vía enlace privado
o VPN Site to Site
La EMPRESA debe contar con un equipo (firewall, servidor) que soporte configuración
de VPN site to site, nosotros remitiríamos un formato con los campos requeridos, como
algoritmos de encriptación y claves de conexión para establecer este tema. La
configuración en el lado de la EMPRESA deben efectuarla ellos, como Caja Arequipa no
intervenimos en configuración de equipos que no sean de nuestra propiedad
o Enlace privado dedicado a través de un proveedor de servicio u operador
 Virtual (extranet o similar)
Si la EMPRESA cuenta con una cabecera de comunicaciones con alguno de los
operadores con los cuales la Caja actualmente tiene contratos vigentes, esto
se validaría en una sesión técnica se podría establecer un enlace virtual, según
su factibilidad, lo cual significaría un tiempo de configuración entre 10 a 15
días, pero representaría un gasto mensual. Para tener un valor
específicamente, se debe consultar a la EMPRESA con que operador tiene sus
servicios principales.
 Físico (circuito digital)
Se gestionaría la instalación de un enlace privado virtual (IPVPN o RPV) a través
de un proveedor de comunicaciones, esto tomaría, según factibilidad técnica,
entre 30 a 45 días (este tiempo depende del tipo de medio físico a emplearse
para interconectar ambas empresas). También incurriría en un costo mensual,
el cual deberá de ser asumido por la EMPRESA.
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 5 de 12
Interconexión vía InternetLa EMPRESA debe proporcionar un servicio expuesto (publicado) el
cual Caja pueda consumir a través de internet, de preferencia la EMPRESA debe de contar con
un whitelist que asegure que la conexión es solo para empresas; esta conexión debe ser cifrada
por la información sensible, mínimo debe contar con un certificado de seguridad firmado por una
entidad certificadora y mínimamente cumplir con TLSv1.2
Las IP publicas de CAJA son las siguientes:
38.187.15.194
191.98.140.194
191.98.148.162
Se deberán realizar pruebas con el equipo técnico de CAJA para validar la conexión realizada.
Cabe indicar que se efectuará un análisis de seguridad y vulnerabilidad del servicio expuesto
para confirmar la validez del servicio.
4. Especificación Técnica
Los Servicios Web de la EMPRESA expondrá los siguientes métodos principales:
 Consulta: Consulta los datos a partir de la Código de Contrato.
 Pago: Notifica a EMPRESA el pago hecho por el cliente final. Caja Arequipa enviará la orden de
pago a la EMPRESA y esperará la respuesta por un lapso de 10 segundos, de no tener respuesta
en ese tiempo se enviará de forma automática una operación de Extorno.
 Extorno: Este método aplica la reversión del Pago. Esto se da por problemas técnicos en la
comunicación, alguna excepción interna en la CAJA o si el cliente desea cancelar la operación.
Esta función debe estar disponible de forma inmediata después de realizar el pago y estar
abierta por un lapso de 15 minutos.
5. Servicios Web
Los Servicios Web que se exponen deben estar desarrollados bajo la arquitectura REST y tener la
siguiente estructura:
5.1. WS#1 Consulta
URL: https://IpPublicaEMPRESA/WsEMPRESA/api/Consulta
Datos de Entrada
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 CodigoContrato N M 1-20 Código del Contrato
2 Moneda AN M 3 Formato ISO (PEN=soles,
USD=dólares)
3 Canal N M 1 1 Ventanilla
2 Cajeros
3 Home banking
4 Corresponsal
6 Banca movil
5 Debito Autom.
Datos de Entrada - Ejemplo:
Body:
{
"CodigoContrato": "20180474",
"Moneda": "PEN",
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 6 de 12
"Canal": "1"
}
Datos de Salida
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 Mensaje AN M 200 Mensaje de respuesta
2 Codigo N M 5 Código de respuesta
00000: Sin Error
00099: Con Error
3 CodigoContrato N M 1-20 Código del Contrato
4 Cliente AN M 1-30 Nombre del Cliente
5 Datos AN M - Documentos del contrato
Datos: Documentos del contrato
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 FechaCreacion DATETIME M 10 Fecha de Creación del
Documento
Formato: yyyy-mm-dd
2 FechaVencimiento DATETIME M 10 Fecha de Vencimiento
Formato: yyyy-mm-dd
3 Moneda AN M 3 Formato ISO (PEN=soles,
USD=dólares)
4 Monto N M 18 Monto del documento pendiente
a Pagar. El separador es un "."
15 dígitos numéricos
1 digito carácter (.)
2 dígitos decimales
5 NumeroDocumento AN M 1-20 Número del documento –
Identificador de la deuda
6 Periodo AN M 1-8 Periodo del documento
Datos de Salida – Ejemplo:
Body:
{
"Mensaje": "Proceso correcto",
"Codigo": "00000",
"CodigoContrato": "100001",
"Cliente": "Cliente Prueba",
"Datos": [{
"FechaCreacion": "2018-01-31",
"FechaVencimiento": "2018-02-10",
"Moneda": "PEN",
"Monto": 35.50,
"NumeroDocumento": "123456789",
"Periodo": "201801"
},
{
"FechaCreacion": "2018-02-28",
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 7 de 12
"FechaVencimiento": "2018-03-10",
"Moneda": "PEN",
"Monto": 25.50,
"NumeroDocumento": "789456789",
"Periodo": "201802"
}]
}
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 8 de 12
5.2. WS#2 Pago
URL: https://IpPublicaEMPRESA/WsEMPRESA/api/Pago
Datos de Entrada
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 CodigoContrato N M 1-20 Código del Contrato
2 NumeroTrace AN M 1-6 Numero Operación de Caja
Arequipa
3 Fecha DATETIME M 10 Fecha que se realiza la Operación
de Pago.
El formato: yyyy-mm-dd
4 Canal
N M 1 1 Ventanilla
2 Cajeros
3 Home banking
4 Corresponsal
6 Banca movil
5 Debito Autom.
5 Datos AN M - Documentos del contrato
Datos: Documentos a Pagar
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 Moneda AN M 3 Formato ISO (PEN=soles,
USD=dólares)
2 Monto N M 18 Monto del documento pendiente a
Pagar. El separador es un "."
15 dígitos numéricos
1 digito carácter (.)
2 dígitos decimales
3 NumeroDocumento AN M 1-20 Número del documento
4 Periodo AN M 1-8 Periodo del documento
Datos de Entrada - Ejemplo:
Body:
{
"CodigoContrato": "100001",
"NumeroTrace": "000005",
"Fecha": "2018-01-31",
"Canal": "1",
"Datos": [{
"Moneda": "PEN",
"Monto": 35.50,
"NumeroDocumento": "123456789",
"Periodo": "201801"
}, {
"Moneda": "PEN",
"Monto": 25.50,
"NumeroDocumento": "789456789",
"Periodo": "201802"
}
]
}
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 9 de 12
Datos de Salida
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 Mensaje AN M 200 Mensaje de respuesta
2 Codigo N M 5 Código de respuesta
00000: Sin Error
00099: Con Error
3 CodigoContrato N M 1-20 Código del Contrato
4 NumeroOperacion AN M 1-20 Numero Operación Empresa
(Pago)
Datos de Salida – Ejemplo:
Body:
{
"Mensaje": "Proceso correcto",
"Codigo": "00000",
"CodigoContrato": "100001",
"NumeroOperacion": "123456789"
}
5.3. WS#3 Extorno
URL: https://IpPublicaEMPRESA/WsEMPRESA/api/Extorno
Datos de Entrada
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 CodigoContrato N M 1-20 Código del Contrato
2 NumeroTrace AN M 1-6 Numero Operación de
Caja Arequipa
(Original del Pago)
3 Fecha DATETIME M 10 Fecha que se realizó la
Operación de Pago.
El formato: yyyy-mm-dd
(Original del Pago)
Datos de Entrada - Ejemplo:
Body:
{
"CodigoContrato ": "100001",
"NumeroTrace": "000005"
"Fecha": "2018-01-31",
}
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 10 de 12
Datos de Salida
Nro Campo Atributo Opcional/
Mandatorio
Longitud Descripción
1 Mensaje AN M 200 Mensaje de respuesta
2 Codigo N M 5 Código de respuesta
00000: Sin Error
00099: Con Error
3 CodigoContrato N M 1-20 Código del Contrato
4 NumeroOperacion AN M 1-20 Numero Operación de Empresa
(Extorno)
Datos de Salida – Ejemplo:
Body:
{
"Mensaje": "Proceso correcto",
"Codigo": "00000",
"CodigoContrato": "100001",
"NumeroOperacion": "123456788"
}
6. CONCILIACION
La Caja Arequipa enviará un archivo de texto después del cierre diario,
aproximadamente a las 3:00 am, el mismo contendrá las operaciones de todos los pagos
realizados a través de nuestros canales de atención.
El medio de envío será vía correo electrónico, la EMPRESA debe enviar el listado de
correos destinatarios a la CAJA.
El archivo tendrá la siguiente estructura:
Cabecera
Campo Nombre Longitud Tipo Observaciones
1 tipo 1 AN T=Titulo (siempre va T)
2 IdEmpresa 20 AN Identificación de la empresa en
la CMAC Arequipa
3 FechaPago 10 D Fecha de pago AAAA/MM/DD
4 Registros 12 N Cantidad de líneas sin incluir el
titulo
5 Monto Total 12.2 N Total pagado en la fecha.
Detalle
Campo Nombre Longitud Tipo Observaciones
1 tipo 1 AN D=Detalle (siempre D)
2 CódigoContrato 1-20 N Código del Contrato
3 NumeroDocumento 1-20 AN Número del documento pagado
4 Monto 12.2 N Monto
5 Hora 8 AN Hora de pago HH:MM:SS
6 Comprobante 20 N Numero de comprobante de pago
de la CAJA
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 11 de 12
Gerencia de Soluciones del Negocio
Documento de Integración – Caja Arequipa
Página 12 de 12
Ejemplo:
Archivo UACV-22-03-2019
T,66,2019/03/22,23,14485.90,
D,2018824092,182210,1590.00,10:33:04,1903220130509800001,
D,2018350184,168554,150.10,11:35:17,1903220010509800002,
D,2017243891,178360,642.00,11:45:48,1903220560509800002,
D,2019900297,187521,250.00,12:12:54,1903220020509800003,
D,2018822022,182197,801.00,12:14:47,1903220130509800005,