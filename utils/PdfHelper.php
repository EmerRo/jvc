<?php

class PdfHelper
{
  /**
   * Escribe el encabezado estándar de la empresa en el PDF
   * Incluye: logo, nombre, dirección, teléfono, email, web y cuadro de título del documento
   */
  public static function escribirEncabezadoEmpresa($mpdf, $datoEmpresa, $htmlCuadroHead)
  {
    // Logo de la empresa
    $mpdf->WriteFixedPosHTML("<img style='max-width: 300px;max-height: 85px' src='" . URL::to('files/logos/' . $datoEmpresa['logo']) . "'>", 35, 8, 150, 120);

    // Cuadro del encabezado (título del documento)
    $mpdf->WriteFixedPosHTML($htmlCuadroHead, 0, 5, 196, 130);

    // Nombre de la empresa
    $mpdf->WriteFixedPosHTML("<span style='font-family: Calibri, Helvetica Neue, sans-serif; font-size: 14px;margin: 1pt 2pt 3pt;'><strong>COMERCIAL & INDUSTRIAL J. V. C. S.A.C.
 </strong></span>", 25, 30, 210, 130);

    // Dirección de la empresa
    $mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'><span style='font-size: 10px'>{$datoEmpresa['direccion']}</span></span>", 35, 36, 210, 130);

    // Teléfono y email
    $mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'>Telf: {$datoEmpresa['telefono']} -Email:{$datoEmpresa['email']} </span>", 25, 39, 210, 130);

    // Sitio web
    $mpdf->WriteFixedPosHTML("<span style='font-size: 12px;margin: 1pt 2pt 3pt;'> Web: https://industriajvcsac.com</span>", 40, 43, 210, 130);
  }

  /**
   * Busca la ruta de una imagen de producto probando varias extensiones
   * Retorna la URL completa o null si no existe
   */
  public static function getImagePath($imageName)
  {
    if (empty($imageName)) {
      return null;
    }
    $extensions = ['jpg', 'jpeg', 'png'];
    $basePath = 'public/img/productos/';

    if (pathinfo($imageName, PATHINFO_EXTENSION)) {
      $fullPath = $basePath . $imageName;
      if (file_exists($fullPath)) {
        return URL::to($fullPath);
      }
    } else {
      foreach ($extensions as $ext) {
        $fullPath = $basePath . $imageName . '.' . $ext;
        if (file_exists($fullPath)) {
          return URL::to($fullPath);
        }
      }
    }

    return null;
  }
}
