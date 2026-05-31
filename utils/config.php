<?php

// ─── Detección de entorno ──────────────────────────────────────────────────
// Local (Laragon): el host termina en .test  →  $esLocal = true
// Producción: cualquier otro host            →  $esLocal = false
$_host   = $_SERVER['HTTP_HOST'] ?? php_uname('n');
$esLocal = (substr($_host, -5) === '.test') || $_host === 'localhost' || $_host === '127.0.0.1';

if ($esLocal) {

    // ── LOCAL (Laragon) ────────────────────────────────────────────────────
    define("DOMINIO",       "http://jvc.test");
    define("HOST_SS",       "localhost");
    define("DATABASE_SS",   "magusqao_jvc_factura");
    define("USER_SS",       "root");
    define("PASSWORD_SS",   "");

    define("HOST_SMTP",     "smtp.gmail.com");
    define("USER_SMTP",     "umbrellasrl@gmail.com");
    define("CLAVE_SMTP",    "mncpbsfnutdarmxv");
    define("PUERTO_SMTP",   "465");

    define("ENDPOINT",      "beta");
    define("URL_API_SUNAT", "http://api-sunat-laravel.test");

} else {

    // ── PRODUCCIÓN ─────────────────────────────────────────────────────────
    define("DOMINIO",       "https://industriajvc.com/");
    define("HOST_SS",       "localhost");
    define("DATABASE_SS",   "magusqao_jvc_factura");
    define("USER_SS",       "magusqao_factura_jvc");
    define("PASSWORD_SS",   "v494OxMp12I3TM");

    define("HOST_SMTP",     "mail.apperpan.com");
    define("USER_SMTP",     "envios@apperpan.com");
    define("CLAVE_SMTP",    "C4p1cu4$$");
    define("PUERTO_SMTP",   "465");

    define("ENDPOINT",      "production");
    define("URL_API_SUNAT", "https://magus-qa.com/api-sunat-laravel");

}

// ─── Constantes comunes (iguales en ambos entornos) ────────────────────────
define("KEY_ENCRYPT", "matrixsistem_key");
define("URL_GEN_XML_SUNAT", "http://genxml.des");
define("DEFAULT_USER_AVATAR", "public/assets/images/users/user-4.jpg");
