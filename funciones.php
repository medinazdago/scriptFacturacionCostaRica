<?php 
date_default_timezone_set('America/Costa_Rica');

function get_row($table,$row, $id, $equal){
	global $con;
	$query=mysqli_query($con,"select $row from $table where $id='$equal'");
	$rw=mysqli_fetch_array($query);
	$value=$rw[$row];
	return $value;
}

function getVentaDolarColones(){
	require_once("get_tipo_cambio/Indicador.php");
// Constructor recibe como parametro true si se va a usar SOAP, de lo contrario por defecto es false
	$i = new Indicador(false);
// Metodo recibe el tipo de cambio Indicador::VENTA o Indicador::COMPRA
	$cambioDolar = $i->obtenerIndicadorEconomico(Indicador::VENTA);
	if($cambioDolar == 0){
		$cambioDolar = 550.00;	
	}
	return $cambioDolar;
}
?>