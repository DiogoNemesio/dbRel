<?php

namespace Zage\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Botão
 *
 * @package Botao
 *          @created 19/06/2013
 * @author Daniel Henrique Cassela
 * @version GIT: $Id$ 2.0.1
 *         
 */
class Botao extends \Zage\Grid\Coluna {
	
	/**
	 * Modelos existentes
	 */
	const MOD_ADD = 1;
	const MOD_EDIT = 2;
	const MOD_REMOVE = 3;
	
	/**
	 * url
	 *
	 * @var string
	 */
	private $url;
	
	/**
	 * Modelo
	 *
	 * @var string
	 */
	private $modelo;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\Grid::TP_BOTAO );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		$url = (empty ( $valor ) ? "#" : $valor);
		switch ($this->getModelo ()) {
			case self::MOD_ADD :
				$nome 	= "Adicionar";
				$classe = "btn-info";
				$icone	= "<i class='icon-plus icon-white'></i>";
				break;
			case self::MOD_EDIT :
				$nome 	= "Editar";
				$classe = "btn-info";
				$icone	= "<i class='icon-edit icon-white'></i>";
				break;
			case self::MOD_REMOVE :
				$nome 	= "Excluir";
				$classe = "btn-danger";
				$icone	= "<i class='icon-remove icon-white'></i>";
				break;
		}
		//$html = "<a href='" . $url . "'><button class='btn btn-small " . $classe . "' type='button'>" . $nome . "</button></a>";
		$html = "<a href='" . $url . "'><button class='btn btn-mini " . $classe . "' type='button'>" . $icone . "</button></a>";
		return ($html);
	}
	
	/**
	 *
	 * @return the $url
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 *
	 * @param string $url        	
	 */
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 *
	 * @return the $modelo
	 */
	public function getModelo() {
		return $this->modelo;
	}
	
	/**
	 *
	 * @param string $modelo        	
	 */
	public function setModelo($modelo) {
		$this->modelo = $modelo;
	}
}
