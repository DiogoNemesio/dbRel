<?php

namespace Zage;

/**
 * Gerenciar o arquivo de configuração do Sistema
 *
 * @package \Zage\Config
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *          @created 12/07/2013
 */
class Config {
	
	/**
	 * Instância da classe Zend\Config
	 *
	 * @var object
	 */
	private $object;
	
	/**
	 * Arquivo de configuração
	 *
	 * @var string
	 */
	private $arquivo;
	
	/**
	 * Construtor
	 * 
	 * @param string $arquivo        	
	 * @return void
	 */
	public function __construct($arquivo) {
		
		/**
		 * Define o arquivo de configuração
		 */
		$this->arquivo = $arquivo;
	}
	

	/**
	 * Retorna um array com as configurações do sistema
	 * @return array
	 */
	public function load() {

		/**
		 * Verifica se o arquivo existe
		 */
		if (file_exists ( $this->arquivo )) {
			/**
			 * Instância o objeto do Zend Reader
			 */
			$reader 		= new \Zend\Config\Reader\Xml ();
			$this->object 	= $reader->fromFile ($this->arquivo);
			return ($this->object);
		}
	}
}
