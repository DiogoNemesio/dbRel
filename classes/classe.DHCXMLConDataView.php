<?php

/**
 * Gerenciar arquivos XML para o conector DHXConnector para a tela de menus
 * 
 * @package: DHCXMLConDataView
 * @created: 06/10/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.1
 * 
 */

class DHCXMLConDataView {

	/**
	 * Variável que vai armazenar o codigo xml
	 */
	protected static $xml;
	
	/**
	 * Variável que vai armazenar o array
	 */
	protected static $array;
			
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		
		global $system;
		
		
		/** Inicializar os arrays **/
		$this->array	= array();
		
		/** Inicializar o código XML **/
		$this->xml		= '<?xml version="1.0" encoding="'.$system->config->charset.'" ?>';
		$this->xml		= '<data>';
	}
	
    /**
     * Resgatar o divId
     *
     * @return string
     */
    public function loadArray ($array) {
    	for ($i = 0; $i < sizeof($array); $i++) {
    		$this->xml	.= "<item id='".$i."'>";
    		
    		foreach ($array[$i] as $key => $value) {
    			$this->xml	.= "<".$key.">".$value."</".$key.">";
    		}
    		//$this->xml	.= "<codMenu>".$array[$i]->codMenu."</codMenu>";
    		//$this->xml	.= "<menu>".$array[$i]->menu."</menu>";
    		//$this->xml	.= "<codMenuPai>".$array[$i]->codMenuPai."</codMenuPai>";
    		$this->xml 	.= "</item>";
    	}
    	$this->xml 	.= "</data>";
    }
    
    /**
     * Resgatar o XML
     *
     * @return xml
     */
    public function getXML() {
    	return ($this->xml);
    }

}


