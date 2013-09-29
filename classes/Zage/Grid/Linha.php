<?php

namespace Zage\Grid;

/**
 * Gerenciar as linhas do grid em bootstrap
 *
 * @package Linha
 *          @created 20/06/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Linha {
	
	/**
	 * Indice
	 *
	 * @var number
	 */
	private $indice;
	
	/**
	 * Indicador se a linha ativa
	 *
	 * @var boolean
	 */
	private $ativa;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		
		/**
		 * Por padrão toda linha está ativa *
		 */
		$this->ativar ();
	}
	
	/**
	 *
	 * @return the $indice
	 */
	public function getIndice() {
		return $this->indice;
	}
	
	/**
	 *
	 * @param number $indice        	
	 */
	public function setIndice($indice) {
		$this->indice = $indice;
	}
	
	/**
	 * Ativar / Habilitar a linha
	 */
	public function ativar() {
		$this->ativa = true;
	}
	
	/**
	 * Desativar / Desabilitar a linha
	 */
	public function desativar() {
		$this->ativa = false;
	}
	
	/**
	 *
	 * @return the $ativa
	 */
	public function getAtiva() {
		return $this->ativa;
	}
}
