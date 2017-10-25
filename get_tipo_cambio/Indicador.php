<?php

/* 
 * Descripcion: Clase para obtener el tipo de cambio actual en Dolares del Banco Central de Costa Rica.
 * Autor: Ariel Orozco <bassplayer85@gmail.com>
 * Web: http://arielorozco.com/
 * Fecha: 29/12/2010
 */

class Indicador {
	
	// Constantes de tipo de cambio
	const COMPRA = 317;
	const VENTA = 318; 
	
	// URL del WebService
	private $ind_econom_ws = "http://indicadoreseconomicos.bccr.fi.cr/indicadoreseconomicos/WebServices/wsIndicadoresEconomicos.asmx";
	
	// Funcion que se va a utilizar del WebService
	private $ind_econom_func = "ObtenerIndicadoresEconomicosXML";
	
	// Bandera que indica si se va a utilizar SOAP para obtener los datos (falso por defecto)
	private $soap = false;
	
	// Tipo de cambio que se quiere obtener (COMPRA por defecto)
	private $tipo = COMPRA;
	
	// Fecha actual
	private $fecha = "";
	
	function __construct($soap = false) {		
		$this->soap = $soap;
		$this->fecha = date("d/m/Y");
	}
	
	public function obtenerIndicadorEconomico($tipo) {		
		$this->tipo = $tipo;		
		$valor = ($this->soap) ? $this->obtenerPorSoap() : $this->obtenerPorGet();		
		return $valor;
	}
	
	private function obtenerPorGet() {		
		$urlWS = $this->ind_econom_ws."/".$this->ind_econom_func."?tcIndicador=".$this->tipo."&tcFechaInicio=".$this->fecha."&tcFechaFinal=".$this->fecha."&tcNombre=tq&tnSubNiveles=N";
		$tipoCambio = "";
		
		if (file_get_contents($urlWS)!=false) {
			$indWS = file_get_contents($urlWS);
			$xml = simplexml_load_string($indWS);
			$tipo_cambio = trim(strip_tags(substr($xml,strpos($xml,"<NUM_VALOR>"),strripos($xml,"</NUM_VALOR>"))));
			$tipoCambio = number_format($tipo_cambio,2);
		}		
		
		return $tipoCambio;				
	}
	
	private function obtenerPorSoap() {
		require_once("soap/nusoap.php");
		$tipoCambio = "";
		$parametros = array(
						"tcIndicador"=>$this->tipo,
						"tcFechaInicio"=>$this->fecha,
						"tcFechaFinal"=>$this->fecha,
						"tcNombre"=>"TQ",
						"tnSubNiveles"=>"N");
		$oSoapClient = new nusoap_client("http://indicadoreseconomicos.bccr.fi.cr/indicadoreseconomicos/WebServices/wsIndicadoresEconomicos.asmx?WSDL",true);
		$aRespuesta = $oSoapClient->call($this->ind_econom_func, $parametros);
		$xml = simplexml_load_string($aRespuesta['ObtenerIndicadoresEconomicosXMLResult']);
		$tipoCambio = $xml->INGC011_CAT_INDICADORECONOMIC[0]->NUM_VALOR;
		return $tipoCambio;		
	}	
	
}


?>