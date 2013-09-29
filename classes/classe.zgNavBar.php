<?php

/**
 * NavBar
 * 
 * @package: zgNavBar
 * @created: 29/03/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class zgNavBar {


	/**
	 * Variável que vai armazenar o codigo xml
	 */
	protected static $html;
	
	/**
	 * Array que vai armazenar os menus 
	 */
	protected static $menus;
		
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct($nome) {
		
		global $system;
		
		/** Inicializar os arrays **/
		$this->menus	= array();
		
		/** Inicializar o código HTML **/
		$this->html		= '<div class="navbar">';
		$this->html		.= '<div class="navbar-inner">';
		$this->html		.= '<div class="container">';
		$this->html		.= '<ul class="nav"><li class="active"><a class="brand" href="http://www.drfritzamorsupremo.com.br/"><img src="%IMG_URL%/drFritz-icon.png" /></a></li>';
	}
	

    /**
     * Resgatar o HTML
     *
     * @return string
     */
    public function getHTML () {
    	return $this->html;
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
    	
    	
    	$codTipo	= $system->usuario->getTipo();
    	
    	$url = DHCDHXMenu::montaUrl($link, $codigo, $codTipo);

    	/** Verifica se o nível da arvore **/
    	if ($nivel == 0) {
    		if ($tipo == 'M') {
    			$this->addItem($codigo,$menuPai,$menu,$icone,$nivel);
    		}elseif ($tipo == 'L') {
    			$this->addLink($codigo,$menu,$url,$icone,$descricao,$menuPai);
        	}else{
				$system->halt('Tipo de menu desconhecido ('.$tipo.')',false,false,true);
			}
    	}elseif ($nivel > 0) {
    		$this->addSubMenu($this->menus,$menuPai,$codigo,$menu,$descricao,$tipo,$url,$nivel,$icone);
    	}
    }
    
    /**
     * Montar a URL de um Menu 
     * 
     * @param string $link
     * @param number $codMenu
     * @param string $codTipo
     */
    public static function montaUrl($link,$codMenu,$codTipo) {
    	global $system;
    	
    	/** verifica se a url já tem alguma variável **/
    	if (strpos($link,'?') !== false) {
    		$vars	= '&'.substr(strstr($link, '?'),1);
    		$link	= substr($link,0,strpos($link, '?'));
    	}else{
    		$vars	= '';
    	}
    	
    	$id		= DHCUtil::encodeUrl("_codMenu_=".$codMenu.$vars);
   		$url	= $link."?id=".$id;
    	
    	return ($url);
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
    	$this->menus[$codigo]["OBJ"]	= new DHCDHXMenuItem($codigo,$itemPai);
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
    	$this->menus[$codigo]["OBJ"]	= new DHCDHXMenuLink($codigo,$itemPai);
    	$this->menus[$codigo]["OBJ"]->setNome($nome);
    	$this->menus[$codigo]["OBJ"]->setURL($url);
    	$this->menus[$codigo]["OBJ"]->setIcone($icone);
    	$this->menus[$codigo]["OBJ"]->setDescricao($descricao);
    }
    
    /**
     * Gerar os codigos fontes JS e XML
     */
    public function render () {
    	global $system;
    	    	
    	/** Gerar o código dos itens **/
    	$this->geraXmlMenu($this->menus);
    	$this->html .= '</ul></div></div></div>';
    	
    }

    /**
     * Enter description here...
     *
     * @param array $array
     * @param integer $menuPai
     * @param integer $codigo
     * @param string $menu
     * @param string $descricao
     * @param string $tipo
     * @param string $link
     * @param integer $nivel
     * @param string $icone
     */
    protected function addSubMenu(&$array,$menuPai,$codigo,$menu,$descricao,$tipo,$link,$nivel,$icone) {
    	foreach ($array as $key => $value) {
    		if ((is_object($array[$key]["OBJ"])) && ($array[$key]["OBJ"]->getCodigo() == $menuPai)) {
    			$array[$key]["OBJ"]->addMenu($codigo,$menu,$descricao,$tipo,$link,$nivel,$menuPai,$icone);
    		}elseif ((is_object($array[$key]["OBJ"])) && ($array[$key]["TIPO"] == 'M')) {
    			$this->addSubMenu($array[$key]["OBJ"]->menus,$menuPai,$codigo,$menu,$descricao,$tipo,$link,$nivel,$icone);
    		}
    	}
    }
    
    /**
     * Gera o xml dos menus
     *
     * @param array $array
     */
    protected function geraXmlMenu ($array) {
    	foreach ($array as $key => $value) {
			if ($array[$key]["OBJ"]->getIcone()) {
				$menuIcone	= ' <img src="'.$array[$key]["OBJ"]->getIcone().'" />';
			}else{
				$menuIcone	= '';
			}

    		if ((is_object($array[$key]["OBJ"])) && ($array[$key]["TIPO"] == 'M')) {
				if ($array[$key]["OBJ"]->getIcone()) {
   					$menuIcone	= ' <img src="'.$array[$key]["OBJ"]->getIcone().'" />';
   				}else{
   					$menuIcone	= '';
    			}

    			$this->html .= '<li class="dropdown"><a href="#" class="dropdown-toogle" data-toggle="dropdown">'.$array[$key]["OBJ"]->getNome().'<b class="caret"></b></a><ul class="dropdown-menu">';
    			$this->geraXmlMenu($array[$key]["OBJ"]->menus);
    			$this->html .= '</ul></li>';
    		}elseif ((is_object($array[$key]["OBJ"])) && ($array[$key]["TIPO"] == 'L')) {
	   			if ($array[$key]["OBJ"]->getURL()) {
   					$link 	= ' href="'.$array[$key]["OBJ"]->getURL().'" target="IFCentral"';
   				}else{
	   				$link	= ' href="#"';
   				}

				$this->html	.= '<li><a '.$link.' >'.$array[$key]["OBJ"]->getNome().'</a></li>'; 
    		}
    	}
    }
    
}