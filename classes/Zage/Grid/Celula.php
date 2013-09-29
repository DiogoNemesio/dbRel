<?php

namespace Zage\Grid;

/**
 * Gerenciar as Celulas do grid em bootstrap
 *
 * @package Celula
 *          @created 20/06/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 */
class Celula {
	
	/**
	 * Linha
	 *
	 * @var number
	 */
	private $linha;
	
	/**
	 * Coluna
	 *
	 * @var number
	 */
	private $coluna;
	
	/**
	 * Indicador se a celula esta ativa
	 *
	 * @var boolean
	 */
	private $ativa;
	
	/**
	 * Valor
	 *
	 * @var string
	 */
	private $valor;
	
	/**
	 * Endereço da Imagem (usado para as colunas do tipo imagem)
	 *
	 * @var string
	 */
	private $enderecoImagem;
	
	/**
	 * Construtor
	 */
	public function __construct() {
		/**
		 * Por padrão toda celula está ativa *
		 */
		$this->ativar ();
	}
	
	/**
	 *
	 * @return the $linha
	 */
	public function getLinha() {
		return $this->linha;
	}
	
	/**
	 *
	 * @param number $linha        	
	 */
	public function setLinha($linha) {
		$this->linha = $linha;
	}
	
	/**
	 *
	 * @return the $coluna
	 */
	public function getColuna() {
		return $this->coluna;
	}
	
	/**
	 *
	 * @param number $coluna        	
	 */
	public function setColuna($coluna) {
		$this->coluna = $coluna;
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
	
	/**
	 *
	 * @return the $valor
	 */
	public function getValor() {
		return $this->valor;
	}
	
	/**
	 *
	 * @param string $valor        	
	 */
	public function setValor($valor) {
		$this->valor = $valor;
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
