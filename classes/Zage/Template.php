<?php

namespace Zage;

/**
 * Gerenciamento de templates HTML
 *
 * @package \Zage\Template
 * @created 10/07/2013
 * @author Daniel Henrique Cassela
 * @version GIT: $Id$ 1.0.1
 *
 */
class Template {

	/**
	 * html do Template
	 *
	 * @var string
	 */
	private $html;
	
	/**
	 * Array com variáveis e valores definidos pelo usuário
	 *
	 * @var array
	 */
	private $values = array ();
	
	/**
	 * Lista de blocos
	 *
	 * @var array
	 */
	private $blocks = array ();
	
	/**
	 * Lista de blocos que tem pelo menos um bloco filho
	 *
	 * @var array
	 */
	private $parents = array ();
	
	/**
	 * Expressão regular usada para encontrar variáveis e blocos
	 * Apenas alfanuméricos e _ são permitidos
	 *
	 * @var string
	 */
	private static $REG_NAME = "([[:alnum:]]|_)+";
	
	/**
	 * Construtor
	 *
	 */
	public function __construct() {
	}

	/**
	 * Carregar o arquivo de template
	 *
	 * @param string $template
	 */
	public function load ($template) {
		global $system,$log;
		
		if ($this->html) {
			die('Template já carregado !!!');
		}
		
		/** Lê o conteudo do arquivo **/
		$this->html	= \Zage\Util::getConteudoArquivo($template);
		
		if (!$this->html) {
			$log->warn('Template: '.$template. ' não carregado !!!');
		}
		
		/** Substituir variáveis padrões **/
		$this->__assignDefaultVariables();
		
		
		/** Criando os blocos **/
		$blocks = $this->recognize ( $this->html, "." );
		$this->createBlocks ( $blocks );
	}

	
	/**
	 * Substitui valores pré-definidos
	 */
	private function __assignDefaultVariables() {
		global $system;
		
		if (isset($system) && is_object($system) && method_exists($system, 'loadHtml')) {
			$this->set('LOAD_HTML'			,$system->loadHtml());
		}
		
		/**
		 * Definindo as constantes
	 	 */
		$this->set('ROOT_URL'	,ROOT_URL);
		$this->set('CSS_URL'	,CSS_URL);
		$this->set('PKG_URL'	,PKG_URL);
		$this->set('BIN_URL'	,BIN_URL);
		$this->set('HTML_URL'	,HTML_URL);
		$this->set('IMG_URL'	,IMG_URL);
		$this->set('JS_URL'		,JS_URL);
		$this->set('DP_URL'		,DP_URL);
		$this->set('XML_URL'	,XML_URL);
		
		if (defined(HTMLX_IMG_URL)) $this->set('HTMLX_IMG_URL'	,HTMLX_IMG_URL);
		
		/**
		 * CharacterSet Default
		 */
		if (isset($system) && is_object($system) && (property_exists($system, "config")) && isset($system->config["charset"])) {
			$this->set('CHARSET'	,$system->config["charset"]);
		}
		
		/**
		 * Skin padrão
		 */
		if (isset($system) && is_object($system) && (method_exists($system, "getSkin")) ) {
			$this->set('SKIN'	,$system->getSkin());
		}
		
	}

	/**
	 * Retornar o código html do template
	 *
	 */
	public function getHtml() {
		/** Substituir variáveis padrões **/
		$this->__assignDefaultVariables();
		$this->html = stripslashes($this->html);
		return $this->html;
	}
	
	/**
	 * Exibir o código html
	 *
	 */
	public function show() {
		echo $this->getHtml();
	}
	
	/**
	 * Definir o valor de uma variável
	 *
	 * @param string $variable
	 * @param string $value
	 */
	public function set($variable, $value) {
		$this->html	= str_replace('%'.$variable.'%'	,$value	,$this->html);
	}
	
	/**
	 * Retira as quebras de linhas e adiciona a barra invertida
	 *
	 */
	public function compile() {
		$this->html	= str_replace(PHP_EOL	,' ',$this->html);
		$this->html	= str_replace('\''	,'\\\'',$this->html);
	}
	
	/**
	 * Identifica todos os blocos automaticamente e retorna.
	 *
	 * All variables and blocks are already identified at the moment when
	 * user calls Template::setFile(). This method calls Template::identifyVars()
	 * and Template::identifyBlocks() methods to do the job.
	 *
	 * @param string $content
	 * @param string $varname
	 *        	variable name of the file
	 *
	 * @return array array where the key is the block name and the value is an
	 *         array with the children block names.
	 */
	private function recognize(&$content, $varname) {
		$blocks = array ();
		$queued_blocks = array ();
		foreach ( explode ( "\n", $content ) as $line ) {
			if (strpos ( $line, "<!--" ) !== false)
				$this->identifyBlocks ( $line, $varname, $queued_blocks, $blocks );
		}
		return $blocks;
	}
	
	/**
	 * Identifica todos os blocos definidos no template
	 *
	 * @param string $line
	 *        	one line of the content file
	 * @param string $varname
	 *        	the filename variable identifier
	 * @param string $queued_blocks
	 *        	a list of the current queued blocks
	 * @param string $blocks
	 *        	a list of all identified blocks in the current file
	 *
	 * @return void
	 */
	private function identifyBlocks(&$line, $varname, &$queued_blocks, &$blocks) {
		$reg = "/<!--\s*BEGIN\s+(" . self::$REG_NAME . ")\s*-->/sm";
		preg_match ( $reg, $line, $m );
		if (1 == preg_match ( $reg, $line, $m )) {
			if (0 == sizeof ( $queued_blocks ))
				$parent = $varname;
			else
				$parent = end ( $queued_blocks );
			if (! isset ( $blocks [$parent] )) {
				$blocks [$parent] = array ();
			}
			$blocks [$parent] [] = $m [1];
			$queued_blocks [] = $m [1];
		}
		$reg = "/<!--\s*END\s+(" . self::$REG_NAME . ")\s*-->/sm";
		if (1 == preg_match ( $reg, $line ))
			array_pop ( $queued_blocks );
	}
	
	/**
	 * Create all identified blocks given by Template::identifyBlocks().
	 *
	 * @param array $blocks
	 *        	all identified block names
	 * @return void
	 */
	private function createBlocks(&$blocks) {
		$this->parents = array_merge ( $this->parents, $blocks );
		foreach ( $blocks as $parent => $block ) {
			foreach ( $block as $chield ) {
				if (in_array ( $chield, $this->blocks ))
					throw new \UnexpectedValueException ( "bloco duplicado: $chield" );
				$this->blocks [] = $chield;
				$this->setBlock ( $parent, $chield );
			}
		}
	}
	
	/**
	 * A variable $parent may contain a variable block defined by:
	 * &lt;!-- BEGIN $varname --&gt; content &lt;!-- END $varname --&gt;.
	 *
	 *
	 * This method removes that block from $parent and replaces it with a variable
	 * reference named $block. The block is inserted into the varKeys and varValues
	 * hashes.
	 * Blocks may be nested.
	 *
	 * @param string $parent
	 *        	the name of the parent variable
	 * @param string $block
	 *        	the name of the block to be replaced
	 * @return void
	 */
	private function setBlock($parent, $block) {
		$name = "B_" . $block;
		$str = $this->getVar ( $parent );
		if ($this->accurate) {
			$str = str_replace ( "\r\n", "\n", $str );
			$reg = "/\t*<!--\s*BEGIN\s+$block\s+-->\n*(\s*.*?\n?)\t*<!--\s+END\s+$block\s*-->\n?/sm";
		} else
			$reg = "/<!--\s*BEGIN\s+$block\s+-->\s*(\s*.*?\s*)<!--\s+END\s+$block\s*-->\s*/sm";
		if (1 !== preg_match ( $reg, $str, $m ))
			throw new \UnexpectedValueException ( "bloco $block está mal formado" );
		$this->setValue ( $name, '' );
		$this->setValue ( $block, $m [1] );
		$this->setValue ( $parent, preg_replace ( $reg, "{" . $name . "}", $str ) );
	}
	
	/**
	 * Internal setValue() method.
	 *
	 * The main difference between this and Template::__set() method is this
	 * method cannot be called by the user, and can be called using variables or
	 * blocks as parameters.
	 *
	 * @param string $varname
	 *        	a varname
	 * @param string $value
	 *        	constains the new value for the variable
	 * @return void
	 */
	private function setValue($varname, $value) {
		$this->values ["{" . $varname . "}"] = $value;
	}
	
}
