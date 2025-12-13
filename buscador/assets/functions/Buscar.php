<?php
include "BD.php";
$opc = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';



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

        // Verificar si es GUÍA DE REMISIÓN (id_tido = 11)
        if ($consoleInner == '11') {
            // BUSCAR EN TABLA guia_remision
            $sqlcod = "SELECT gr.id_guia_remision, d.cod_sunat
                       FROM guia_remision gr, documentos_sunat d
                       WHERE gr.serie='$serie' AND gr.numero='$correlativo'
                       AND d.id_tido = '11'";

            $rescod = mysqli_query($con, $sqlcod);

            if ($rescod && mysqli_num_rows($rescod) > 0) {
                $arrcod = mysqli_fetch_array($rescod, MYSQLI_ASSOC);
                $id_guia = $arrcod['id_guia_remision'];

                if ($id_guia > 0) {
                    $buscar = 1;
                    $codsut = $arrcod['cod_sunat'];
                    // Ruta para GUÍAS DE REMISIÓN
                    $ruta = "/guia/remision/pdf/".$id_guia."/".$rucemisor."-".$codsut."-".$serie."-".$correlativo;
                } else {
                    $buscar = 0;
                    $ruta = "";
                }
            } else {
                $buscar = 0;
                $ruta = "";
            }
        }
        // Verificar si es NOTA DE CRÉDITO (id_tido = 3) o NOTA DE DÉBITO (id_tido = 4)
        elseif ($consoleInner == '3' || $consoleInner == '4') {
            // BUSCAR EN TABLA notas_electronicas
            $sqlcod = "SELECT ne.nota_id, nes.nombre_xml, d.cod_sunat
                       FROM notas_electronicas ne
                       INNER JOIN notas_electronicas_sunat nes ON ne.nota_id = nes.id_notas_electronicas
                       INNER JOIN documentos_sunat d ON ne.tido = d.id_tido
                       WHERE ne.serie='$serie' AND ne.numero='$correlativo' AND ne.tido='$consoleInner'";

            $rescod = mysqli_query($con, $sqlcod);

            if ($rescod && mysqli_num_rows($rescod) > 0) {
                $arrcod = mysqli_fetch_array($rescod, MYSQLI_ASSOC);
                $nota_id = $arrcod['nota_id'];
                $nombre_xml = $arrcod['nombre_xml'];

                if ($nota_id > 0) {
                    $buscar = 1;
                    $codsut = $arrcod['cod_sunat'];
                    // Ruta para NOTAS ELECTRÓNICAS
                    $ruta = "/nota/electronica/pdf/".$nota_id."/".$nombre_xml;
                } else {
                    $buscar = 0;
                    $ruta = "";
                }
            } else {
                $buscar = 0;
                $ruta = "";
            }
        }
        else {
            // BUSCAR EN TABLA ventas (FACTURAS, BOLETAS)
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
                    // Ruta para VENTAS (facturas, boletas)
                    $ruta = "/venta/comprobante/pdf/".$codshb."/".$rucemisor."-".$codsut."-".$serie."-".$correlativo;
                } else {
                    $buscar = 0;
                    $ruta = "";
                }
            } else {
                $buscar = 0;
                $ruta = "";
            }
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
