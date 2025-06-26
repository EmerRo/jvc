<?php
include "BD.php";
$opc = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$sistema = "/jvc";

switch ($opc) {
    case '1': // BUSCAR COMPROBANTE
        $rucemisor = (isset($_POST['rucemisor'])) ? $_POST['rucemisor'] : '';
        $serie = (isset($_POST['serie'])) ? $_POST['serie'] : '';
        $correlativo = (isset($_POST['correlativo'])) ? $_POST['correlativo'] : '';
        $consoleInner = (isset($_POST['consoleInner'])) ? $_POST['consoleInner'] : '';
        
        if (empty($rucemisor) || empty($serie) || empty($correlativo) || empty($consoleInner)) {
            $listar = array("buscar" => 0, "ruta" => "", "error" => "Todos los campos son requeridos");
            $data[] = $listar;
            break;
        }
        
        $sqlcod = "SELECT v.id_venta, d.cod_sunat FROM ventas v, documentos_sunat d 
                   WHERE v.serie='$serie' AND v.numero='$correlativo' AND v.id_tido ='$consoleInner'
                   AND d.id_tido = v.id_tido";
        
        $rescod = mysqli_query($con, $sqlcod);
        
        if ($rescod && mysqli_num_rows($rescod) > 0) {
            $arrcod = mysqli_fetch_array($rescod, MYSQLI_ASSOC);
            $codshb = $arrcod['id_venta'];
            
            if ($codshb > 0) { 
                $buscar = 1;	
                $codsut = $arrcod['cod_sunat'];
                // CORREGIDO: URL relativa sin "localhost"
                $ruta = $sistema."/venta/comprobante/pdf/".$codshb."/".$rucemisor."-".$codsut."-".$serie."-".$correlativo;
            } else { 
                $buscar = 0;
                $ruta = "";
            }
        } else {
            $buscar = 0;
            $ruta = "";
        }
        
        $listar = array("buscar" => $buscar, "ruta" => $ruta);
        $data[] = $listar;
        break;
        
    default:
        $listar = array("buscar" => 0, "ruta" => "", "error" => "Opción no válida");
        $data[] = $listar;
        break;
}         

print json_encode($data);
?>
