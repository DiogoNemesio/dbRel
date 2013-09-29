<?php

namespace Zage\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Texto
 *
 * @package Texto
 *          @created 20/06/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Texto extends \Zage\Grid\Coluna {
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\Grid::TP_TEXTO );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		$html = $valor;
		return ($html);
	}
}
