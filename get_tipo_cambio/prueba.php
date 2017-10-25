<?php
// EJEMPLO DE UTILIZACION
require_once("Indicador.php");

// Constructor recibe como parametro true si se va a usar SOAP, de lo contrario por defecto es false
$i = new Indicador(false);

// Metodo recibe el tipo de cambio Indicador::VENTA o Indicador::COMPRA
$venta = $i->obtenerIndicadorEconomico(Indicador::VENTA);
$compra = $i->obtenerIndicadorEconomico(Indicador::COMPRA);
$cambio = array("venta" => $venta,"compra" => $compra,);

 
echo json_encode($cambio, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);



?>