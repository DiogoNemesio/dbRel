<?php

/**
 * Rotinas para gerenciar os botoes do menu
 * 
 * @package: zgBSNavBarItem
 * @created: 02/04/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class zgBSNavBarItem {
	
	/**
	 * Array que vai armazenar os links "buttons"
	 */
	public $menus;

	/**
	 * Codigo do link Pai (superior)
	 */
	private $itemPai;

	/**
	 * Codigo do Item
	 */
	private $codigo;

	/**
	 * Nome do Item
	 */
	private $nome;

	/**
	 * Icone do Item
	 */
	private $icone;

	/**
	 * Nivel do Item
	 */
	private $nivel;

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

		/** Inicializar os arrays **/
		$this->menus	= array();

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
     * Resgatar o Nível
     *
     * @return string
     */
    public function getNivel () {
    	return $this->nivel;
    }
    
    /**
     * Definir o nivel
     *
     * @param string $valor
     */
    public function setNivel ($valor) {
    	$this->nivel	= $valor;
    }

    /**
     * Adicionar um menu
     *
     * @param string $codigo
     * @param string $menu
     * @param string $descricao
     * @param string $tipo
     * @param string $link
     * @param string $nivel
     * @param string $menuPai
     * @param string $icone
     */
    public function addMenu($codigo,$menu,$descricao,$tipo,$link,$nivel,$menuPai,$icone) {
    	global $system;

    	if ($menuPai !== $this->getCodigo()) {
    		$system->halt('MenuPai ('.$menuPai.' - '.$this->getCodigo().') difere do informado !!!',false,false,true);
    	}
    	
    	if ($tipo == 'M') {
   			$this->addItem($codigo,$menuPai,$menu,$icone,$nivel);
   		}elseif ($tipo == 'L') {
   			$this->addLink($codigo,$menu,$link,$icone,$descricao,$menuPai);
    	}else{
			$system->halt('Tipo de menu desconhecido ('.$tipo.')',false,false,true);
    	}
    	
    }
        
    /**
     * Adicionar um Item
     */
    protected function addItem ($codigo,$itemPai,$nome,$icone,$nivel) {
    	global $system;
    	if (array_key_exists($codigo,$this->menus)) {
    		$system->halt('Menu duplicado: '.$codigo,false,false,true);
    	}
    	$this->menus[$codigo]			= array();
    	$this->menus[$codigo]["TIPO"]	= 'M';
    	$this->menus[$codigo]["OBJ"]	= new zgBSNavBarItem($codigo,$itemPai);
    	$this->menus[$codigo]["OBJ"]->setNome($nome);
    	$this->menus[$codigo]["OBJ"]->setIcone($icone);
    	$this->menus[$codigo]["OBJ"]->setNivel($nivel);
	}

        
    /**
     * Adicionar um Link
     */
	protected function addLink ($codigo,$nome,$url,$icone,$descricao,$itemPai) {
		global $system;
    	if (array_key_exists($codigo,$this->menus)) {
    		$system->halt('Menu duplicado: '.$codigo,false,false,true);
    	}
    	$this->menus[$codigo]			= array();
    	$this->menus[$codigo]["TIPO"]	= 'L';
    	$this->menus[$codigo]["OBJ"]	= new zgBSNavBarLink($codigo,$itemPai);
    	$this->menus[$codigo]["OBJ"]->setNome($nome);
    	$this->menus[$codigo]["OBJ"]->setURL($url);
    	$this->menus[$codigo]["OBJ"]->setIcone($icone);
    	$this->menus[$codigo]["OBJ"]->setDescricao($descricao);
    }

}
