<?php

// ─── Detección de entorno ──────────────────────────────────────────────────
// Local (Laragon): el host termina en .test  →  $esLocal = true
// Producción: cualquier otro host            →  $esLocal = false
$_host   = $_SERVER['HTTP_HOST'] ?? php_uname('n');
$esLocal = str_ends_with($_host, '.test') || $_host === 'localhost' || $_host === '127.0.0.1';

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
    define("DOMINIO",       "http://185.111.156.125/");
    define("HOST_SS",       "localhost");
    define("DATABASE_SS",   "magusqao_jvc_factura");
    define("USER_SS",       "root");
    define("PASSWORD_SS",   "");

    define("HOST_SMTP",     "mail.apperpan.com");
    define("USER_SMTP",     "envios@apperpan.com");
    define("CLAVE_SMTP",    "C4p1cu4$$");
    define("PUERTO_SMTP",   "465");

    define("ENDPOINT",      "production");
    define("URL_API_SUNAT", "http://84.247.162.204/api-sunat-laravel");

}

// ─── Constantes comunes (iguales en ambos entornos) ────────────────────────
define("KEY_ENCRYPT", "matrixsistem_key");
define("URL_GEN_XML_SUNAT", "http://genxml.des");
