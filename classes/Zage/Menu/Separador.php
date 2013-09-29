<?php

namespace Zage\Menu;

/**
 * Gerenciar os separadores de menu 
 *
 * @package Separador
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Separador {
	
	/**
	 * Código do Separador
	 * @var String
	 */
	private $codigo;
	
	/**
	 * Codigo do Item Pai (superior)
	 * @var integer
	 */
	private $itemPai;
	
	/**
	 * Nivel do separador
	 * @var integer
	 */
	private $nivel;
	
	
	/**
	 * Construtor
	 * 
	 * @return void
	 */
	public function __construct() {
		
	}
	
	/**
	 * Gera o código html
	 * @return void
	 */
	public function geraHtml() {
		return '<li id="sep_li_'.$this->getCodigo().'" class="divider"></li>'.PHP_EOL;
	
	}
	
	
	/**
	 * @return the $codigo
	 */
	public function getCodigo() {
		return $this->codigo;
	}

	/**
	 * @param number $codigo
	 */
	public function setCodigo($codigo) {
		$this->codigo = $codigo;
	}
	
	/**
	 * @return the $itemPai
	 */
	public function getItemPai() {
		return $this->itemPai;
	}

	/**
	 * @param number $itemPai
	 */
	public function setItemPai($itemPai) {
		$this->itemPai = $itemPai;
	}

	/**
	 * @return the $nivel
	 */
	public function getNivel() {
		return $this->nivel;
	}

	/**
	 * @param number $nivel
	 */
	public function setNivel($nivel) {
		$this->nivel = $nivel;
	}


	
}
