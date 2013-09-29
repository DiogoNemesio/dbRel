<?php

/**
 * @package: DHCHtmlTemplate
 * @created: 27/12/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar templates html
 */

class DHCHtmlTemplate  {

	/**
	 * html do Template
	 *
	 * @var string
	 */
	private $html;
	
	
	/**
	 * Construtor
	 *
	 */
	public function __construct() {
		//global $system;
		
		//$system->log->debug->debug("DHCHtmlTemplate: nova instância");

	}

	/**
	 * Carregar o arquivo de template
	 *
	 * @param string $template
	 */
	public function loadTemplate ($template) {
		global $system;
		
		if ($this->html) {
			DHCErro::halt('Template já carregado !!!');
		}
		
		/** Lê o conteudo do arquivo **/
		$this->html	= DHCUtil::getConteudoArquivo($template);
		
		if (!$this->html) {
			$system->log->file->warn('Template: '.$template. ' não carregado !!!');
		}
		
		/** Substituir variáveis padrões **/
		$this->assignDefaultVariables();
	}

	
	private function assignDefaultVariables() {
		global $system;
		
		/** Substituir variáveis padrões **/
		$this->assign('LOAD_HTML'				,$system->getDynHtmlLoad('H'));
		$this->assign('LOAD_BS_SCRIPT'			,$system->getDynHtmlLoad('B'));
		$this->assign('CHARSET'					,$system->config->charset);
		$this->assign('ROOT_URL'				,ROOT_URL);
		$this->assign('CSS_URL'					,CSS_URL);
		$this->assign('PKG_URL'					,PKG_URL);
		$this->assign('BIN_URL'					,BIN_URL);
		$this->assign('HTML_URL'				,HTML_URL);
		$this->assign('IMG_URL'					,IMG_URL);
		$this->assign('JS_URL'					,JS_URL);
		$this->assign('DP_URL'					,DP_URL);
		$this->assign('XML_URL'					,XML_URL);
		$this->assign('MENU_URL'				,MENU_URL);
		$this->assign('PKG_PATH'				,PKG_PATH);
		$this->assign('XML_PATH'				,XML_PATH);
		$this->assign('HTML_PATH'				,HTML_PATH);
		$this->assign('BIN_PATH'				,BIN_PATH);
		$this->assign('SKIN'					,$system->getSkin());
		$this->assign('PARAM_BS_TA_MINLENGTH'	,$system->parametros["BS_TA_MINLENGTH"]->getValor());
		$this->assign('PARAM_BS_TA_ITENS'		,$system->parametros["BS_TA_ITENS"]->getValor());
		$this->assign('PARAM_BS_TA_TIMEOUT'		,$system->parametros["BS_TA_TIMEOUT"]->getValor());
	}
	/**
	 * Retornar o código html do template
	 *
	 */
	public function getHtmlCode() {
		/** Substituir variáveis padrões **/
		$this->assignDefaultVariables();
		$this->html = stripslashes($this->html);
		return $this->html;
	}
	
	/**
	 * Definir o valor de uma variável
	 *
	 * @param string $variable
	 * @param string $value
	 */
	public function assign($variable, $value) {
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

}
