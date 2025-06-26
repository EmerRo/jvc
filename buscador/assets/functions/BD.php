<?php
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','factura_jvc1');

$con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if(!$con){
    die("imposible conectarse: ".mysqli_error($con));
}
if (@mysqli_connect_errno()) {
    die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
}

mysqli_query($con,"SET CHARACTER SET 'utf8'");
mysqli_query($con,"SET SESSION collation_connection ='utf8_unicode_ci'");
?>
