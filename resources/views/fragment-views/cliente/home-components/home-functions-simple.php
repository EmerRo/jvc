<?php
/**
 * Funciones simplificadas para el Dashboard Home
 * Versión de respaldo con datos simulados
 */

// Función para generar datos simulados según el período
function generarDatosGrafico($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion = null) {
    $datos = [];
    $count = count($categorias);
    
    for ($i = 0; $i < $count; $i++) {
        $datos[] = rand(1000, 8000); // Datos simulados entre 1000 y 8000
    }
    
    return $datos;
}

function generarDatosUtilidadBruta($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion = null) {
    $datos = [];
    $count = count($categorias);
    
    for ($i = 0; $i < $count; $i++) {
        $datos[] = rand(300, 2500); // Utilidad simulada
    }
    
    return $datos;
}

function generarDatosIngresos($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $sucursal, $conexion = null) {
    $datos = [];
    $count = count($categorias);
    
    for ($i = 0; $i < $count; $i++) {
        $datos[] = rand(2000, 10000); // Ingresos simulados
    }
    
    return $datos;
}

function generarDatosEgresos($periodo, $categorias, $fecha_inicio, $fecha_fin, $empresa, $conexion = null) {
    $datos = [];
    $count = count($categorias);
    
    for ($i = 0; $i < $count; $i++) {
        $datos[] = rand(1000, 6000); // Egresos simulados
    }
    
    return $datos;
}

echo "<!-- Archivo home-functions-simple.php cargado correctamente -->\n";
?>