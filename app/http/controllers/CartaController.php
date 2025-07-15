<?php
// app/controllers/CartaController.php

require_once "app/models/Carta.php";
require_once "app/models/CartaTemplate.php";
require_once "app/http/controllers/CartaPDF.php";
require_once "app/models/TipoCarta.php";
require_once "app/http/controllers/BaseDocumentoController.php";

class CartaController extends BaseDocumentoController
{
    protected $documentType = 'carta';

    public function __construct()
    {
        parent::__construct();
        $this->modelo = new Carta();
        $this->plantilla = new CartaTemplate();
        $this->pdfGenerator = new CartaPDF();
        $this->tipoModelo = new TipoCarta();
    }

    // Métodos específicos para cartas que usan nombres diferentes
    public function obtenerTiposCartas()
    {
        return $this->obtenerTiposDocumentos();
    }

    public function insertarTipoCarta()
    {
        return $this->insertarTipoDocumento();
    }

    public function editarTipoCarta()
    {
        return $this->editarTipoDocumento();
    }

    public function eliminarTipoCarta()
    {
        return $this->eliminarTipoDocumento();
    }
    public function obtenerTipoCartas()
    {
        return $this->obtenerTiposCartas();
    }
}
// error
// <br />
// <b>Fatal error</b>:  Cannot declare class CartaTemplate, because the name is already in use in <b>C:\xampp\htdocs\jvc\app\http\controllers\CartaController.php</b> on line <b>11</b><br />
