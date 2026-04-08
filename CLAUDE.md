# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Descripción del Proyecto

JVC es un **sistema de facturación electrónica peruano** con integración SUNAT. Maneja ventas, compras, cotizaciones, garantías, inventario, guías de remisión y generación de reportes PDF.

**Lenguaje**: PHP 7+ con JS/jQuery en el frontend
**Base de datos**: MySQL 8.0.30 (`magusqao_jvc_factura`, ~120 tablas)
**Servidor**: Apache con mod_rewrite (desarrollo en Laragon)
**URL local**: `http://jvc.test` (virtual host de Laragon)
**Zona horaria**: America/Lima

## Entorno de Desarrollo

- **Servidor local**: Laragon (Apache + MySQL + PHP)
- **Config de BD**: `utils/config.php` — constantes HOST_SS, DATABASE_SS, USER_SS, PASSWORD_SS
- **Sin herramientas de build**: No hay Webpack, npm ni preprocesadores CSS. Assets estáticos servidos directamente
- **Sin framework de testing**: No hay PHPUnit ni tests automatizados
- **Dependencia Composer**: Solo `cboden/ratchet` para WebSocket (raíz)
- **Librerías vendorizadas** en `utils/lib/`: mPDF (PDFs), PHPMailer, PHPSpreadsheet (Excel), QR code (endroid), Greenter GRE (SUNAT guías electrónicas)

## Comando MySQL directo

```bash
C:/laragon/bin/mysql/mysql-8.0.30-winx64/bin/mysql.exe -u root magusqao_jvc_factura -e "QUERY"
```

Ejemplos útiles:
- `... -e "SHOW tables;"` — listar tablas
- `... -e "DESCRIBE nombre_tabla;"` — ver estructura de una tabla

## Arquitectura

### Flujo de arranque (`index.php` → `src/launcher.php`)

1. Inicia sesión, zona horaria `America/Lima`
2. Carga config (`utils/config.php`), constantes de rutas (`src/Roots.php`), utilidades (`utils/Tools.php`)
3. Carga clases base Controller y Middleware
4. Registra autoloader personalizado (`src/autoloader/Autoloader.php`)
5. Carga conexión BD (`config/Conexion.php`)
6. Auto-escanea y requiere todos los archivos en `routes/`
7. Despacha la solicitud con `Route::submit()`

### Sistema de Rutas (`src/router/Route.php`)

Rutas definidas en `routes/web.php`, `routes/ajax2.php`, `routes/ajaxs.php`, `routes/admin.php`. Se cargan automáticamente.

```php
Route::post("/ruta", "NombreController@metodo")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/venta/comprobante/pdf/:venta", "Controller@metodo");
Route::postBase("/pagina", "Controller@metodo", [Middleware::class]);
```

- `Route::postBase()` maneja GET (renderizar página) y POST (procesar acción)
- Reescritura de URL vía `.htaccess`: todas las solicitudes van a `index.php?uri=$1`
- Rutas AJAX con prefijo `/ajs/` y respuesta JSON, concentradas en `ajax2.php` (~100+ rutas) y `ajaxs.php`

### Constantes de Rutas (`src/Roots.php`)

```
PATH_APP         → ./app/
PATH_CONFIG      → ./config/
PATH_SRC         → ./src/
PATH_ROUTES      → ./routes/
PATH_CONTROLLERS → ./app/http/controllers/
PATH_VIEWS       → ./resources/views/
```

### Controladores (`app/http/controllers/`)

~78 controladores que extienden `Controller`. Renderizan vistas con `$this->view("ruta", $datos)`. La lógica de negocio está directamente en los métodos del controlador.

Principales: `VentasController`, `ComprasController`, `ConsultasController`, `CotizacionesController`, `GarantiaController`, `ReportesVentaController`, `InformePDF`, `BaseDocumentoController`.

### Modelos (`app/models/`)

~60 modelos con MySQLi directo vía clase `Conexion`. Sin ORM — queries SQL escritos a mano. `Consultas.php` es un helper de consultas usado en todo el proyecto.

### Vistas (`resources/views/`)

Plantillas PHP puras (sin Blade/Twig). Layout principal en `resources/views/index.php`. Vistas de funcionalidades en `resources/views/fragment-views/cliente/`. Fragmentos reutilizables en `resources/views/fragment/` (head, header, navbar).

### Middleware (`app/http/middleware/`)

- `ValidarTokenMiddleware` — Auth por token con timeout de 12 horas, lee header `token-app`
- `LoginMiddleware` / `NoLoginMiddleware` — Verificación de sesión
- `AdminMiddleware` — Verificación de rol admin
- `VerificarPermisosMiddleware` — Verificación de permisos

### Base de Datos (`config/Conexion.php`)

Conexión MySQLi directa con constantes de `utils/config.php`. Sin migraciones formales — los cambios de esquema se hacen con archivos `.sql` sueltos en `database/` o en la raíz del proyecto.

### DataTables Server-Side (`app/clases/serverside.php`)

Clase `TableData` con conexión PDO separada (no usa `Conexion`). Procesa peticiones DataTables server-side. Config de módulos en `ServerSide/modulos.json`.

### Clases de Servicio (`app/clases/`)

- `SunatApi.php` / `SunatApi2.php` — Integración con API de SUNAT
- `EnvioEmail.php` — Envío de correos vía PHPMailer
- `SendURL.php` — Peticiones HTTP a servicios externos
- `serverside.php` — Procesamiento DataTables (ver arriba)

### Integración SUNAT (`sunat/`)

Usa librería Greenter para generación de XML. Templates Twig para XML de facturas/guías. Certificados digitales en `facturacion/certificados/`. Respuestas CDR en `facturacion/cdr/`.

- Constante `ENDPOINT` en `utils/config.php`: `"beta"` para pruebas, `"production"` para producción
- `app/clases/SunatApi.php` maneja las llamadas a la API de SUNAT

### Frontend

- **jQuery** + **Bootstrap 4/5** para UI
- **DataTables** para tablas (locale español)
- **Highcharts** para gráficos, **Flatpickr** para fechas, **SweetAlert/Toastr** para notificaciones
- **Select2** para dropdowns con búsqueda, **Quill** para texto enriquecido
- JS personalizado en `public/js/` (tools.js, orden-trabajo.js, series/, modulo-documentos/, etc.)
- CSS personalizado en `public/css/`

### Encriptación

`utils/Tools.php` provee `Encriptar()` y `Desencriptar()` usando constante `KEY_ENCRYPT` del config. Se usa para autenticación por token.

## Convenciones Clave

- Todo el código, variables y comentarios en **español**
- Controladores usan notación `@metodo` en rutas: `"NombreController@nombreMetodo"`
- Rutas AJAX con prefijo `/ajs/` retornan JSON
- Nombres de tablas en español: `ventas`, `compras`, `clientes`, `productos`, `cotizaciones`, `garantia`, `guias_remision`
- Uploads van a `public/uploads/`
- Archivos generados (PDFs, XMLs) van a `files/` y `reportes/`
- Generación de PDFs usa mPDF (`utils/lib/mpdf/`) — controladores como `ReportesVentaController` e `InformePDF` escriben HTML inline y lo pasan a `$this->mpdf->WriteHTML()`
- Parámetros de ruta con prefijo `:` (ej: `/pdf/:venta`) se pasan como argumentos al método del controlador
- `Route::baseStatic()` + `Route::postBase()` patrón para páginas con vista GET y acción POST en la misma URL
- Constante `DOMINIO` en `utils/config.php` define la URL base del sistema
