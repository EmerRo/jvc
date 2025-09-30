<?php

require_once "app/models/ArchivoInterno.php";
require_once "app/models/ArchivoInternoPlantilla.php";
require_once "app/http/controllers/ArchivoInternoPDF.php";
require_once "app/models/TipoArchivoInterno.php";
require_once "app/http/controllers/BaseDocumentoController.php";

class ArchivoInternoController extends BaseDocumentoController
{
    
       protected $documentType = 'archivoInterno';


    public function __construct()
    {
          parent::__construct();
        $this->modelo = new ArchivoInterno();
        $this->plantilla = new ArchivoInternoPlantilla();
        $this->pdfGenerator = new ArchivoInternoPDF();
        $this->tipoModelo = new TipoArchivoInterno();
    }
  public function obtenerTiposArchivosInternos()
    {
        return $this->obtenerTiposDocumentos();
    }

    public function insertarTipoArchivoInterno()
    {
        return $this->insertarTipoDocumento();
    }

    public function editarTipoArchivoInterno()
    {
        return $this->editarTipoDocumento();
    }

    public function eliminarTipoArchivoInterno()
    {
        return $this->eliminarTipoDocumento();
    }
    public function obtenerTipoArchivoInternos()
    {
        return $this->obtenerTiposArchivosInternos();
    }
 
}