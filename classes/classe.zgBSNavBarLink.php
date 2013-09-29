<?php

/**
 * Rotinas para gerenciar os links do menu
 * 
 * @package: zgBsNavBarLink
 * @created: 02/04/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class zgBsNavBarLink {
	
	/**
	 * Codigo do link Pai (superior)
	 */
	private $ItemPai;

	/**
	 * Codigo do Link
	 */
	private $codigo;

	/**
	 * Nome do Item
	 */
	private $nome;

	/**
	 * URL do Item
	 */
	private $url;

	/**
	 * Icone do Item
	 */
	private $icone;

	/**
	 * Descricao do Item
	 */
	private $descricao;

	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct($codigo,$itemPai) {
		
		/** Salvar o codigo **/
		$this->setCodigo($codigo);
		
		/** Salvar o codigo do ItemPai **/
		$this->setItemPai($itemPai);

	}

    /**
     * Resgatar o codigo do itemPai
     *
     * @return string
     */
    public function getItemPai () {
    	return $this->ItemPai;
    }
    
    /**
     * Definir o codigo do ItemPai
     *
     * @param string $valor
     */
    public function setItemPai ($valor) {
    	$this->ItemPai	= $valor;
    }

    /**
     * Resgatar o codigo
     *
     * @return string
     */
    public function getCodigo () {
    	return $this->codigo;
    }
    
    /**
     * Definir o codigo
     *
     * @param string $valor
     */
    public function setCodigo ($valor) {
    	$this->codigo	= $valor;
    }
	
    /**
     * Resgatar o nome
     *
     * @return string
     */
    public function getNome () {
    	return $this->nome;
    }
    
    /**
     * Definir o nome
     *
     * @param string $valor
     */
    public function setNome ($valor) {
    	$this->nome	= $valor;
    }

    /**
     * Resgatar a URL
     *
     * @return string
     */
    public function getURL () {
    	return $this->url;
    }
    
    /**
     * Definir a URL
     *
     * @param string $valor
     */
    public function setURL ($valor) {
    	$this->url	= $valor;
    }

    /**
     * Resgatar o icone
     *
     * @return string
     */
    public function getIcone () {
    	return $this->icone;
    }
    
    /**
     * Definir o icone
     *
     * @param string $valor
     */
    public function setIcone ($valor) {
    	$this->icone	= $valor;
    }

    /**
     * Resgatar a descricao
     *
     * @return string
     */
    public function getDescricao () {
    	return $this->descricao;
    }
    
    /**
     * Definir a descricao
     *
     * @param string $valor
     */
    public function setDescricao ($valor) {
    	$this->descricao	= $valor;
    }

}
