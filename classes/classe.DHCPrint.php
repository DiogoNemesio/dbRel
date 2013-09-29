<?php

/**
 * @package: DHCGrid
 * @created: 11/04/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar páginas para impressão
 */

class DHCPrint {
	
	/**
	 * Variável para guardar as linhas
	 *
	 * @var array
	 */
	private $lines;
	
	/**
	 * Variável para guardar o cabeçalho
	 *
	 * @var array
	 */
	private $header;

	/**
	 * Variável para guardar o cabeçalho
	 *
	 * @var string
	 */
	private $posHeader;

	/**
	 * Número máximo de linhas por página
	 * 
	 * @var number
	 */
	private $maxLinesPerPage;

	/**
	 * Número máximo de linhas do Cabeçalho
	 * 
	 * @var number
	 */
	private $maxHeaderLines;

	/**
	 * Número de páginas do documento
	 * 
	 * @var number
	 */
	private $numPages;
	
	/**
	 * Número de real de linhas
	 * 
	 * @var number
	 */
	private $__lines;
	
	/**
	 * Código HTML
	 * 
	 * @var string
	 */
	private $html;

	/**
	 * Código HTML do Header
	 * 
	 * @var string
	 */
	private $headerHtml;

	/**
	 * Caracteres TAB e EOL(Fim de Linha)
	 * 
	 * @var string
	 */
	private $tab,$eol;

	/**
	 * Construtor
	 */
	public function __construct() {
		
		/** Número máximo de Linhas por página **/
		$this->maxLinesPerPage	= 43;

		/** Número máximo de Linhas por cabecalho **/
		$this->maxHeaderLines	= 4;

		/** Inicializa os arrays **/
		$this->linhas		= array();
		$this->header		= array();
		$this->posHeader	= null;
		$this->numPages		= 0;
		$this->__lines		= 0;
		
		/** Atribui os valores dos caracteres **/
		$this->tab			= chr(9);
		$this->eol			= PHP_EOL;
		
		/** Inicializa o código html **/
		$this->html	=  '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'.$this->eol;
		$this->html	.= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="br" lang="br">'.$this->eol;
		$this->html	.= $this->tab.'<head>'.$this->eol;
		$this->html	.= $this->tab.'<meta http-equiv="content-type" content="text/html; charset=UTF-8" />'.$this->eol;
		$this->html .= $this->tab.'<style type="text/css" media="all">@import "'.CSS_URL.'/clinar.css";</style>'.$this->eol;
		$this->html .= '</head>'.$this->eol;
		$this->html .= '<body class="uniBody" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">'.$this->eol;
		
	}
	
	/**
	 * Função para adicionar conteúdo ao cabeçalho
	 * 
	 */
	public function addHeader($posicao,$conteudo) {
		if (($posicao > $this->maxHeaderLines) || ($posicao < 1)) return;
		$this->header[$posicao]	= $conteudo;
	}

	/**
	 * Função para adicionar o Pos Cabeçalho
	 * 
	 */
	public function addPosHeader($conteudo) {
		$this->posHeader	= $conteudo;
	}
	
	/**
	 * Resgata o número de linhas
	 * 
	 */
	private function numLinhas() {
		return sizeof($this->lines);
	}


	/**
	 * Função para adicionar as linhas
	 *
	 */
	public function addLine($data,$count = 1,$valign = 'middle') {
		
		/** Valida o parâmetro valign **/
		if (($valign !== 'middle') && ($valign !== 'top') && ($valign !== 'bottom')) DHCErro::halt('Parâmetro valign incorreto !!!');
		
		$index							= $this->numLinhas() + 1;
		$this->lines[$index]["DATA"]	= $data;
		$this->lines[$index]["COUNT"]	= $count;
		$this->lines[$index]["VALIGN"]	= $valign;
		
		/** Contabiliza o número real de linhas **/
		$this->__lines	+=	$count;
		
	}
	
	/**
	 * Gera o código HTML do Header
	 */
	private function makeHtmlHeader () {

		/** Carregando o template html do cabeçalho **/
		$header	= new DHCHtmlTemplate();
		$header->loadTemplate(HTML_PATH . 'DHCPrintHeaderTemplate.html');
		$header->assign('IMG_URL',IMG_URL);
		
		/** Adicionando os campos do Header **/
		for ($i = 1; $i <= 5; $i++) {
			if (isset($this->header[$i])) {
				$header->assign('HEADER'.$i,$this->header[$i]);
			}else{
				$header->assign('HEADER'.$i,null);
			}
		}
		
		/** Adicionando o posheader **/
		if (isset($this->posHeader)) {
			$header->assign('POSHEADER',$this->posHeader);
		}else{
			$header->assign('POSHEADER',null);
		}
		$this->headerHtml	= $header->getHtmlCode();
	}
	
	/**
	 * Resgatar o código html da página
	 */
	public function getHtml () {
		
		/** Gera o html do Header **/
		$this->makeHtmlHeader();		

		/** Cria a primeira página **/
		$this->addPage();

		if ($this->numLinhas() == 0) {
			return $this->html;
		}

		/** Cria uma variável com o número de linhas da página atual **/
		$actNumLines	= 0;
		
		/** Faz o loop nas linhas para adiciona-las **/
		for ($i = 1; $i <= $this->numLinhas(); $i++) {
			
			/** Verifica se a próxima linha vai caber na página **/
			if (($actNumLines + $this->lines[$i]["COUNT"]) >= $this->maxLinesPerPage) {
				/** Pula para a pŕoxima página **/
				$this->addPage();
				$actNumLines	= 0;
			}

			$this->html .= '<tr style="vertical-align: '.$this->lines[$i]["VALIGN"].';"><td class="uniRel tabRel" rowspan="'.$this->lines[$i]["COUNT"].'"><div class="divPrintRowData">'.$this->lines[$i]["DATA"].'</div></td></tr>'.$this->eol;
			$this->__lines++;
				
			for ($j = 1; $j < $this->lines[$i]["COUNT"]; $j++) {
				$this->html .= '<tr class="trPrintData"><td class="uniFonte uniCabRel tabRel">&nbsp;</td></tr>'.$this->eol;
				$this->__lines++;
			}
				
			$actNumLines += $this->lines[$i]["COUNT"];
			
		}
		
		/** Fecha a última página **/
		$this->closePage();
		
		return ($this->html);
		
	}

	/**
	 * Adicionar uma página
	 */
	private function addPage() {
		
		if ($this->numPages > 0) {
			$this->closePage();
		}
		
		$this->html	.= $this->headerHtml;
		$this->html .= '<div class="divPrintData">'.$this->eol;
		$this->html .= '<table class="tabRel">'.$this->eol;
		$this->numPages++;

	}
	
	/**
	 * Fechar uma página
	 */
	private function closePage() {
		$this->html .= '</table>'.$this->eol;
		$this->html .= '</div>'.$this->eol;
	}

}

