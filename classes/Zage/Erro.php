<?php

namespace Zage;

/**
 * Gerenciar Erros
 *
 * @package \Zage\Erro
 * @created 10/07/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Erro {
	
	/**
	 * Construtor privado
	 */
	private function __construct() {
	}
	
	/**
	 * Essa função irá mostrar uma mensagem de erro padrão e fazer log da mnesagem original
	 * 
	 * @param string $errstr        	
	 */
	public static function halt($mensagem = null) {
		/**
		 * Definindo Variáveis globais *
		 */
		global $system,$log;
		
		$tpl = new Template ( HTML_PATH . "Erro.html" );
		$tpl->MENSAGEM = "Houve um problema em nosso servidor, favor tente novamente dentro de instantes";
		
		if (isset ( $system ) && (is_object ( $system ))) {
			$log->err( $mensagem );
		}
		
		$tpl->show ();
		exit;
	}
}