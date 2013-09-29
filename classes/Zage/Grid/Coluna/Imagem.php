<?php

namespace Zage\Grid\Coluna;

/**
 * Gerenciar as colunas to tipo Imagem
 *
 * @package Imagem
 *          @created 19/06/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Imagem extends \Zage\Grid\Coluna {
	
	/**
	 * url
	 *
	 * @var string
	 */
	private $url;
	
	/**
	 * Endereço da Imagem
	 *
	 * @var string
	 */
	private $enderecoImagem;
	
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
	public function geraHtmlValor($valor, $enderecoImagem) {
		$url = (empty ( $valor ) ? "#" : $valor);
		if (empty ( $enderecoImagem ))
			$enderecoImagem = $this->getEnderecoImagem ();
		$html = "<a href='" . $url . "'><img src='" . $enderecoImagem . "'/></a>";
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
	 * @return the $enderecoImagem
	 */
	public function getEnderecoImagem() {
		return $this->enderecoImagem;
	}
	
	/**
	 *
	 * @param string $enderecoImagem        	
	 */
	public function setEnderecoImagem($enderecoImagem) {
		$this->enderecoImagem = $enderecoImagem;
	}
}
