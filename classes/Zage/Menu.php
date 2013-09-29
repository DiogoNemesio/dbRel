<?php

namespace Zage;

/**
 * Gerenciar os menus
 *
 * @package \Zage\Menu
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */ 
class Menu {
	
	/**
	 * Tipos de menu
	 */
	const TIPO1 = 1;
	

	/**
	 * Tipo do Menu
	 * @var string
	 */
	private $tipo;
	
	/**
	 * Array de itens do Menu
	 * @var array
	 */
	protected $itens;
	
	/**
	 * Código html do menu
	 * @var string
	 */
	private $html;
	
	/**
	 * Href Padrão onde os menus serão abertos
	 * @var string
	 */
	private $target;
	
	/**
	 * Array com a ordem correta dos itens do menu
	 * @var array
	 */
	private $_array;
	
	
	/**
	 * Construtor
	 * 
	 * @param string $tipo        	
	 * @return void
	 */
	public function __construct($tipo) {

		/**
		 * Define o tipo do Menu
		 */
		switch ($tipo) {
			case self::TIPO1:
				$this->setTipo($tipo);
				break;
			default:
				\Zage\Erro::halt('Tipo de Menu desconhecido !!!');
		}
		
		/**
		 * Inicializa o array de itens
		 */
		$this->itens	= array();
		
	}
	
	/**
	 * Inicializa o código html, de acordo com o tipo do menu
	 */
	private function iniciaHtml() {
		switch ($this->getTipo()) {
			case self::TIPO1:
				$this->html	= '<div class="navbar">';
				$this->html	.= '<div class="navbar-inner">';
				$this->html	.= '<ul class="nav navbar-nav" role="navigation">';
				break;
			default:
				\Zage\Erro::halt('Tipo de Menu desconhecido !!!');
		}
	}
	
	/**
	 * Inicializa o código html, de acordo com o tipo do menu
	 */
	private function finalizaHtml() {
		switch ($this->getTipo()) {
			case self::TIPO1:
				$this->html	.= '</ul></div></div>';
				break;
			default:
				\Zage\Erro::halt('Tipo de Menu desconhecido !!!');
		}
	}
	
	/**
	 * Adiciona uma pasta ao menu
	 * @param integer $codigo
	 * @param string $nome
	 * @param string $icone
	 * @param integer $itemPai
	 */
	public function adicionaPasta($codigo,$nome,$icone,$itemPai = null) {
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($codigo) == true) {
			die('Código já existente ('.$codigo.')');
		}
		
		/**
		 * Verifica se o item Pai existe
		 */
		if (($itemPai !== null) && ($this->existeItem($itemPai) == false) ) {
			die('Item Pai inexistente ('.$itemPai.')');
		}
		
		/**
		 * Cria a pasta
		 */
		$this->itens[$codigo]	= new \Zage\Menu\Pasta();
		$this->itens[$codigo]->setCodigo($codigo);
		$this->itens[$codigo]->setNome($nome);
		$this->itens[$codigo]->setIcone($icone);
		$this->itens[$codigo]->setitemPai($itemPai);
	}
	
	/**
	 * Adiciona um Link ao menu
	 * @param integer $codigo
	 * @param string $nome
	 * @param string $icone
	 * @param string $url
	 * @param string $descricao
	 * @param integer $itemPai
	 */
	public function adicionaLink($codigo,$nome,$icone,$url,$descricao,$itemPai = null) {
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($codigo) == true) {
			die('Código já existente ('.$codigo.')');
		}
	
		/**
		 * Verifica se o item Pai existe
		 */
		if (($itemPai !== null) && ($this->existeItem($itemPai) == false) ) {
			die('Item Pai inexistente ('.$itemPai.')');
		}
	
		/**
		 * Cria o link
		 */
		$this->itens[$codigo]	= new \Zage\Menu\Link();
		$this->itens[$codigo]->setCodigo($codigo);
		$this->itens[$codigo]->setNome($nome);
		$this->itens[$codigo]->setIcone($icone);
		$this->itens[$codigo]->setUrl($this->montaUrl($url, $codigo));
		$this->itens[$codigo]->setDescricao($descricao);
		$this->itens[$codigo]->setItemPai($itemPai);
		$this->itens[$codigo]->setTarget($this->getTarget());
	
	}
	
	
	/**
	 * Adiciona um Link ao menu
	 * @param integer $codigo
	 * @param integer $itemPai
	 */
	public function adicionaSeparador($codigo,$itemPai = null) {
		/**
		 * Verifica se o código já foi utilizado
		 */
		if ($this->existeItem($codigo) == true) {
			die('Código já existente ('.$codigo.')');
		}
	
		/**
		 * Verifica se o item Pai existe
		*/
		if (($itemPai !== null) && ($this->existeItem($itemPai) == false) ) {
			die('Item Pai inexistente ('.$itemPai.')');
		}
	
		/**
		 * Cria o Separador
		 */
		$this->itens[$codigo]	= new \Zage\Menu\Separador();
		$this->itens[$codigo]->setCodigo($codigo);
		$this->itens[$codigo]->setItemPai($itemPai);
	
	}
	
	/**
	 * Verifica se existe o item informado
	 * @param integer $codigo
	 * @return boolean
	 */
	private function existeItem($codigo) {
		if (array_key_exists($codigo, $this->itens)) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Montar a URL de um Menu
	 *
	 * @param String $link
	 * @param String $codItem
	 */
	public static function montaUrl($link,$codItem) {
		/** 
		 * verifica se a url já tem alguma variável
		 **/
		if (strpos($link,'?') !== false) {
			$vars	= '&'.substr(strstr($link, '?'),1);
			$link	= substr($link,0,strpos($link, '?'));
		}else{
			$vars	= '';
		}
		 
		$id		= \Zage\Util::encodeUrl("_codMenu_=".$codItem.$vars);
		$url	= $link."?id=".$id;
		 
		return ($url);
	}
	
	
	/** 
	 * Gera o array na ordem correta de nível de nível e ordem
	 */
	private function geraArray() {
		global $nivel,$nivelMax;

		/**
		 * Define os contadores para não deixar acontecer uma recursividade
		 */
		$nivel			= 0;
		$nivelMax		= 500;
		
		/** 
		 * Primeiro percorre o nível 0, os itens que não tem pai 
		 **/
		foreach ($this->itens as $codigo => $obj) {
			if ($obj->getItemPai() == null) {
				$this->_array[$codigo]	= array();
			}
		}
		
		/** 
		 * Encontrar os filhos 
		 **/
		foreach ($this->_array as $codigo => $array) {
			$this->descobreMenuFilhos($this->_array[$codigo], $codigo);			
		}
		
		/** 
		 * Definir os níveis dos menus 
		 **/
		foreach ($this->_array as $codigo => $array) {
			$this->itens[$codigo]->setNivel(0);
			$this->defineNivel($this->_array[$codigo],1);
		}
		
	} 
	
	/**
	 * Descobre os filhos do $item no $this->_array e coloca em $array
	 * @param array $array
	 * @param string $item
	 */
	private function descobreMenuFilhos(&$array,$item) {
		global $nivel,$nivelMax;
		$nivel++;
		foreach ($this->itens as $codigo => $obj) {
			if ($obj->getItemPai() == $item) {
				$array[$codigo] = array();
				$this->descobreMenuFilhos($array[$codigo], $codigo);
			}
			if ($nivel > $nivelMax) die('Recursividade encontrada em :'.__FUNCTION__);
		}
		
	}
	
	/**
	 * Definir o nível dos menus
	 * @param array $array
	 * @param Integer $nivel
	 */
	private function defineNivel(&$array,$nivel) {
		foreach ($array as $cod => $arr) {
			$this->itens[$cod]->setNivel($nivel);
			if (!empty($arr)) {
				$this->defineNivel($array[$cod], $nivel+1);
			}
		}
	}
	
	/**
	 * Gerar o código html do menu
	 * @return void
	 */
	private function geraHtmlItem($codigo,$array) {
		if ($this->itens[$codigo] instanceof \Zage\Menu\Pasta) {
			$this->html .= $this->itens[$codigo]->abrirTag();
			$this->html .= $this->itens[$codigo]->geraHtml();
			if (!empty($array)) {
				foreach ($array as $cod => $arr) {
					$this->geraHtmlItem($cod, $arr);
				}
			}
			$this->html .= $this->itens[$codigo]->fecharTag();
		}elseif ($this->itens[$codigo] instanceof \Zage\Menu\Link) {
			$this->html .= $this->itens[$codigo]->geraHtml();
		}elseif ($this->itens[$codigo] instanceof \Zage\Menu\Separador) {
			$this->html .= $this->itens[$codigo]->geraHtml();
		}
		
	}
	
	
	/**
	 * Gerar o código html do menu
	 * @return void
	 */
	private function geraHtml() {

		/**
		 * Inicializa o código HTML
		 */
		$this->iniciaHtml();
		
		foreach ($this->_array as $codigo => $array) {
			$this->geraHtmlItem($codigo,$array);
		}
	
		/**
		 * Finaliza o código HTML
		 */
		$this->finalizaHtml();
	}
	
	
	/**
	 * @return the $tipo
	 */
	private function getTipo() {
		return $this->tipo;
	}

	/**
	 * @param string $tipo
	 */
	private function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	/**
	 * @return the $html
	 */
	public function getHtml() {
		$this->geraArray();
		$this->geraHtml();
		return $this->html;
	}
	
	/**
	 * @return the $target
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * @param string $target
	 */
	public function setTarget($target) {
		$this->target = $target;
	}


}
