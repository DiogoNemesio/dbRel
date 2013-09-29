<?php

namespace Zage\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo ícone
 * 
 * @package Icone
 *          @created 19/06/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Icone extends \Zage\Grid\Coluna {
	
	/**
	 * url
	 *
	 * @var string
	 */
	private $url;
	
	/**
	 * Icone
	 *
	 * @var string
	 */
	private $icone;
	
	/**
	 * Descrição
	 *
	 * @var string
	 */
	private $descricao;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->setTipo ( \Zage\Grid::TP_ICONE );
	}
	
	/**
	 * Gerar o código Html da célula
	 */
	public function geraHtmlValor($valor) {
		$url = (empty ( $valor ) ? "#" : $valor);
		$html = "<a href='" . $url . "' data-toggle='tooltip' data-trigger='click hover' data-animation='true' data-title='" . $this->getDescricao () . "'><button class='btn btn-small' type='button'><i class='" . $this->getIcone () . "'></i></button></a>";
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
	 * @return the $icone
	 */
	public function getIcone() {
		return $this->icone;
	}
	
	/**
	 *
	 * @param string $icone        	
	 */
	public function setIcone($icone) {
		$this->icone = $icone;
	}
	
	/**
	 *
	 * @return the $descricao
	 */
	public function getDescricao() {
		return $this->descricao;
	}
	
	/**
	 *
	 * @param string $descricao        	
	 */
	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}
}
