<?php

namespace Zage\Menu;

/**
 * Gerenciar as pastas do Menu (Sub-Menu) 
 *
 * @package Pasta
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Pasta {
	
	/**
	 * Codigo da Pasta
	 * @var string
	 */
	private $codigo;
	
	/**
	 * Nome da pasta
	 * @var string
	 */
	private $nome;
	
	/**
	 * Icone da pasta
	 */
	private $icone;
	
	/**
	 * Codigo do Item Pai (superior)
	 * @var Integer
	 */
	private $itemPai;
		
	/**
	 * Nivel do pasta
	 * @var Integer
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
		if ($this->getIcone() != null) {
			$menuIcone	= '<i class="'.$this->getIcone().'"></i>';
		}else{
			$menuIcone	= '';
		}
		
		if ($this->getNivel() == 0) {
			return '<a id="pasta_a_'.$this->getCodigo().'" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">'.$menuIcone.$this->getNome().'<b class="caret"></b></a><ul class="dropdown-menu" role="menu" aria-labelledby="pasta_a_'.$this->getCodigo().'">'.PHP_EOL;
		}else{
			return '<a id="pasta_a_'.$this->getCodigo().'" href="#">'.$menuIcone.$this->getNome().'</a><ul class="dropdown-menu" role="menu" aria-labelledby="pasta_a_'.$this->getCodigo().'">'.PHP_EOL;			
		}
	}
	
	/**
	 * Abrir/Inicia a tag html do item
	 * @param string $codigo
	 * @return string
	 */
	public function abrirTag() {
		if ($this->getNivel() == 0) {
			return '<li class="dropdown">';
		}else{
			return '<li class="dropdown-submenu">';
		}
	}
	
	/**
	 * Fechar a tag do código html do item
	 * @param String $codigo
	 * @return string
	 */
	public function fecharTag() {
		return '</ul></li>';
	}
	
	/**
	 * @return the $codigo
	 */
	public function getCodigo() {
		return $this->codigo;
	}

	/**
	 * @param field_type $codigo
	 */
	public function setCodigo($codigo) {
		$this->codigo = $codigo;
	}

	/**
	 * @return the $nome
	 */
	public function getNome() {
		return $this->nome;
	}

	/**
	 * @param field_type $nome
	 */
	public function setNome($nome) {
		$this->nome = $nome;
	}

	/**
	 * @return the $icone
	 */
	public function getIcone() {
		return $this->icone;
	}

	/**
	 * @param field_type $icone
	 */
	public function setIcone($icone) {
		$this->icone = $icone;
	}

	/**
	 * @return the $itemPai
	 */
	public function getItemPai() {
		return $this->itemPai;
	}

	/**
	 * @param field_type $itemPai
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
	 * @param field_type $nivel
	 */
	public function setNivel($nivel) {
		$this->nivel = $nivel;
	}
	
}
