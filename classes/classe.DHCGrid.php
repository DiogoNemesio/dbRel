<?php

/**
 * @package: DHCGrid
 * @created: 23/09/2009
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os grids
 */

class DHCGrid {

	/**
	 * Variável para guardar as linhas
	 *
	 * @var array
	 */
	private $linhas;
	
	/**
	 * Variável para guardar as linhas
	 *
	 * @var array
	 */
	private $colunas;

	/**
	 * Número de Linhas
	 *
	 * @var integer
	 */
	private $numLinhas;

	/**
	 * Número de Linhas
	 *
	 * @var integer
	 */
	private $numColunas;

	/**
	 * Nome do Grid
	 *
	 * @var string
	 */
	private $nome;

	/**
	 * Skin
	 *
	 * @var string
	 */
	private $skin;

	/**
	 * XML
	 *
	 * @var string
	 */
	private $xml;
	
	/**
	 * JSArray
	 *
	 * @var string
	 */
	private $jsArray;
	
	/**
	 * charset
	 *
	 * @var string
	 */
	private $charset;

	/**
	 * Caracter de nova linha
	 *
	 * @var string
	 */
	private $nl;

	/**
	 * Caracter TAB
	 *
	 * @var string
	 */
	private $tab;
	
	/**
	 * Tipo de registro SUB_ROW
	 *
	 * @var string
	 */
	private $subrow;

	/**
	 * Cores não padrão das linhas
	 *
	 * @var string
	 */
	private $cores;

	/**
	 * Valores não padrão das linhas
	 *
	 * @var string
	 */
	private $valores;

	/**
	 * Indicador de Ajuste de Altura automático
	 *
	 * @var string
	 */
	private $autoHeight;

	/**
	 * Indicador de Ajuste de Largura automático
	 *
	 * @var string
	 */
	private $autoWidth;

	/**
	 * Indicar se o grid vai fazer paginação
	 *
	 * @var string
	 */
	private $paging;

	/**
	 * Fazer o uso de filtros
	 *
	 * @var string
	 */
	private $filtro;

	/**
	 * Variável para guardar os valores de uma combo
	 *
	 * @var array
	 */
	private $coValues;

	/**
	 * Construtor
	 */
	public function __construct($nome) {
		
		/** Definindo o nome do grid **/
		$this->setNome($nome);
		
		
		/** Definindo os valores padrões das variáveis **/
		$this->setNumLinhas(0);
		$this->setNumColunas(0);
		
		/** Inicializando os arrays **/
		$this->colunas	= array();
		$this->linhas	= array();
		$this->cores	= array();
		$this->valores	= array();
		
		/** Definindo o caracter de nova linha **/
		$this->nl		= null;
//		$this->nl		= chr(10);
		
		/** Definindo o caracter tab **/
		$this->tab		= null;
//		$this->tab		= chr(9);

		/** Definindo a string de subrow **/
		$this->subrow	= 'sub_row';

		/** Por padrão não fará paginação **/
		$this->paging	= array(
			"ENABLE" 		=> false,
			"NUMLINHAS"		=> 0,
			"NUMPAGINAS"	=> 5,
			"DIVLINHAS"		=> '',
			"DIVPAGINAS"	=> ''
		);
		
		/** Por padrão não faz filtro **/
		$this->filtro	= false;
	}

	/**
	 * Definir o nome do grid
	 *
	 * @param string $valor
	 */
	private function setNome ($valor) {
		$this->nome	= $valor;
	}

	/**
	 * Definir o skin do grid
	 *
	 * @param string $valor
	 */
	public function setSkin ($valor) {
		$this->skin	= $valor;
	}

	/**
	 * Definir o número de linhas que o grid tem
	 *
	 * @param string $valor
	 */
	private function setNumLinhas ($valor) {
		$this->numLinhas	= $valor;
	}

	/**
	 * Definir o número de colunas que o grid tem
	 *
	 * @param string $valor
	 */
	private function setNumColunas ($valor) {
		$this->numColunas	= $valor;
	}

	/**
	 * Definir o caracter set
	 *
	 * @param string $valor
	 */
	public function setCharset ($valor) {
		$this->charset	= $valor;
	}

	/**
	 * Resgatar o nome do Grid
	 */
	private function getNome () {
		return($this->nome);
	}

	/**
	 * Resgatar o skin do Grid
	 */
	private function getSkin () {
		return($this->skin);
	}

	/**
	 * Resgatar o número de linhas que o grid tem
	 */
	private function getNumLinhas () {
		return($this->numLinhas);
	}

	/**
	 * Resgatar o número de colunas que o grid tem
	 */
	private function getNumColunas () {
		return($this->numColunas);
	}
	
	/**
	 * Resgatar o caracterset
	 */
	private function getCharset () {
		return($this->charset);
	}

	/**
	 * Resgatar o caracter de nova linha
	 */
	private function getNL () {
		return($this->nl);
	}

	/**
	 * Resgatar o caracter TAB
	 */
	private function getTAB () {
		return($this->tab);
	}

	/**
	 * Resgatar a string de sub_row
	 */
	private function getSubRow () {
		return($this->subrow);
	}

	/**
	 * Definir se terá ajuste de altura automático
	 *
	 * @param string $valor
	 */
	public function setAutoHeight($valor) {
		$this->autoHeight	= $valor;
	}

	/**
	 * Resgatar a string autoHeight
	 */
	private function getAutoHeight () {
		return($this->autoHeight);
	}

	/**
	 * Definir se terá ajuste de largura automático
	 *
	 * @param string $valor
	 */
	public function setAutoWidth($valor) {
		$this->autoWidth	= $valor;
	}

	/**
	 * Resgatar a string autoHeight
	 */
	private function getAutoWidth () {
		return($this->autoWidth);
	}
	
	
	/**
	 * Adicionar uma Coluna
	 *
	 * @param string $valor
	 * @param integer $tamanho
	 * @param string $alinhamento
	 * @param string $tipo
	 */
	public function adicionaColuna ($nome,$tamanho,$alinhamento,$tipo,$nomeCampo = null) {
		
		/** Validar os parâmetros **/
		if (($tamanho) && (!is_numeric($tamanho))) {
			DHCErro::halt('Parâmetro Tamanho não numérico !!!');
		}
		
		if (($alinhamento) && (strtolower($alinhamento) != 'left') && (strtolower($alinhamento) != 'center') && (strtolower($alinhamento) != 'right')) {
			DHCErro::halt('Parâmetro Alinhamento deve ser (left,center,right) ');
		}
		
		/** Por enquanto não validar o tipo **/
		
		/** Define o próximo índice **/
		$i = sizeof($this->colunas) + 1;
		
		/** Definindo os valores **/
		$this->colunas[$i]["NOME"]		= $nome ? $nome : ' ';
		$this->colunas[$i]["TAM"]		= $tamanho;
		$this->colunas[$i]["ALIN"]		= $alinhamento;
		$this->colunas[$i]["TIPO"]		= $tipo ? $tipo : 'ro';
		$this->colunas[$i]["NOMECAMPO"]	= $nomeCampo ? $nomeCampo : $nome;
		
		/** Altera o valor da variável numColunas **/
		$this->setNumColunas($i);
		
	}

	/**
	 * Carrega os dados a partir um array
	 *
	 * @param array $dados
	 */
	public function loadObjectArray ($dados) {
		
		/** 
		 * Array esperado é do tipo Zend_Db::FETCH_OBJ
		 * 
		 * As propriedades do objeto devem ser iguais aos nomes das colunas
		 *  
		 **/
		
		/** Zera o array de linhas **/
		$this->linhas	= array();
		$this->setNumLinhas(0);
		
		/** Faz o Loop para gerar o array de linhas **/
		for ($i = 0; $i < sizeof($dados); $i++) {
			
			/** Inicializa o array **/
			$this->linhas[$i] = array();
			
			for ($j = 1; $j <= $this->getNumColunas(); $j++) {
				$nome	= $this->colunas[$j]["NOME"];
				$campo	= $this->colunas[$j]["NOMECAMPO"];
				if ($this->colunas[$j]["TIPO"] == $this->getSubRow()) {
					$this->linhas[$i][$j]	= '...'.$nome;
				}else{
					if (property_exists($dados[$i],$campo)) {
						$this->linhas[$i][$j]	= $dados[$i]->$campo;
					}
				}
			}
			$this->numLinhas++;
		}
	}
	
	/**
	 * Gera a string do Header
	 */
	private function getColHeader() {
		$header	= '';
		
		/** Faz o loop nas colunas para pegar os nomes delas **/
		for ($i = 1; $i <= $this->numColunas; $i++) {
			if ($this->colunas[$i]["TIPO"] == $this->getSubRow()) {
				$header	.= '&nbsp;,';
			}else{
				$header	.= $this->colunas[$i]["NOME"] . ',';
			}
		}
		
		/** Retira o último caracter , que deve ser uma vírgula e retorna **/
		return (substr($header,0,-1));
	}
	
	/**
	 * Gera a string do Columns ID
	 */
	private function getColIds() {
		$header	= '';
		
		/** Faz o loop nas colunas para pegar os nomes delas **/
		for ($i = 1; $i <= $this->numColunas; $i++) {
			if ($this->colunas[$i]["TIPO"] == $this->getSubRow()) {
				$header	.= '&nbsp;,';
			}else{
				$header	.= $this->colunas[$i]["NOMECAMPO"] . ',';
			}
		}
		
		/** Retira o último caracter , que deve ser uma vírgula e retorna **/
		return (substr($header,0,-1));
	}

	/**
	 * Gera a string dos tamanhos
	 */
	private function getColWidth() {
		$width	= '';
		
		/** Faz o loop nas colunas para pegar os tamanhos delas **/
		for ($i = 1; $i <= $this->numColunas; $i++) {
			$width	.= $this->colunas[$i]["TAM"] . ',';
		}
		
		/** Retira o último caracter , que deve ser uma vírgula e retorna **/
		return (substr($width,0,-1));
	}

	/**
	 * Gera a string dos tipos
	 */
	private function getColTypes() {
		$types	= '';
		
		/** Faz o loop nas colunas para pegar os tamanhos delas **/
		for ($i = 1; $i <= $this->numColunas; $i++) {
			$types	.= $this->colunas[$i]["TIPO"] . ',';
		}
		
		/** Retira o último caracter , que deve ser uma vírgula e retorna **/
		return (substr($types,0,-1));
	}

	/**
	 * Gera a string do alinhamento
	 */
	private function getColAlign() {
		$align	= '';
		
		/** Faz o loop nas colunas para pegar os alinhamentos delas **/
		for ($i = 1; $i <= $this->numColunas; $i++) {
			$align	.= $this->colunas[$i]["ALIN"] . ',';
		}
		
		/** Retira o último caracter , que deve ser uma vírgula e retorna **/
		return (substr($align,0,-1));
	}
	
	/**
	 * Gera o XML
	 */
	private function geraXML () {
		/** Inicializa o arquivo XML **/
		$this->xml	= $this->getNL() . "<?xml version=\"1.0\" encoding=\"".$this->getCharset()."\"?>" . $this->getNL();
		$this->xml	.= "<rows>" . $this->getNL();
		
		/** Faz o loop nas linhas **/
		for ($i = 0; $i < $this->getNumLinhas(); $i++) {
			/** Adiciona a Tag de inicialização de registro **/
			$this->xml	.= $this->getTAB() . "<row id=\"".$i."\">" . $this->getNL();
			
			/** Faz o loop nas celulas da linha **/
			for ($j = 1; $j <= sizeof($this->linhas[$i]); $j++) {
				if ($this->colunas[$j]["TIPO"] == $this->getSubRow()) {
					$cellType	= "type=\"sub_row_grid\"";
				}else{
					$cellType	= null;
				}
				$this->xml	.= $this->getTAB() . $this->getTAB() . "<cell $cellType>" . $this->linhas[$i][$j] . "</cell>" . $this->getNL();
			}
			
			/** Adiciona a Tag de finalização de registro **/
			$this->xml	.= $this->getTAB() . "</row>" . $this->getNL();
		}
		$this->xml	.= "</rows>" . $this->getNL();
	}
	
	/**
	 * Gera o JSArray
	 */
	private function geraJSArray () {
		
		/** Inicializa o JSArray **/
		$this->jsArray	= '[';
		
		/** Faz o loop nas linhas **/
		for ($i = 0; $i < $this->getNumLinhas(); $i++) {
			/** Adiciona a Tag de inicialização de registro **/
			$this->jsArray	.= "[";
			
			/** Faz o loop nas celulas da linha **/
			for ($j = 1; $j <= sizeof($this->linhas[$i]); $j++) {
				$this->jsArray	.= "\"".$this->linhas[$i][$j]."\",";
			}
			
			/** Retira a última vírgula **/
			$this->jsArray = substr($this->jsArray,0,-1);
			
			/** Adiciona a Tag de finalização de registro **/
			$this->jsArray	.= "],";
		}
		/** Retira a última vírgula **/
		if (substr($this->jsArray,0,-1) == ',') {
			$this->jsArray = substr($this->jsArray,0,-1);
		}
		$this->jsArray	.= ']';
	}
	
	/**
	 * Define uma cor para um registro
	 */
	public function setCorLinha($linha,$cor) {
		/** Verifica se a linha já existe **/
		if (isset($this->linhas[$linha])) {
			$this->cores[$linha] = $cor;
		}
	}
	
	/**
	 * Altera o valor de uma determinada célula
	 */
	public function setValorColuna($linha,$coluna,$valor) {
		
		//print_r($this->linhas[$linha]);
		
		/** Verifica se a linha/coluna já existe **/
		if (isset($this->linhas[$linha])) {
			$this->valores[$linha][$coluna]	= $valor;
		}
	}
	
	/**
	 * Adiciona um registro no Grid
	 */
	public function adicionaRegistro($registro) {
		
		/**
		 * O Registro deve ser uma string separada por PIPE "|", com o número certo de colunas
		 */

		/** Cria um array com os valores a serem adicionados **/
		$aReg	= explode('|',$registro);
		
		if (sizeof($aReg) != $this->getNumColunas()) {
			DHCErro::halt('AdicionaRegistro: Numero de campos difere do número de colunas');
		}
		
		$i	= $this->getNumLinhas();
			
		/** Inicializa o array **/
		$this->linhas[$i] = array();
			
		for ($j = 0; $j < sizeof($aReg); $j++) {
			$this->linhas[$i][$j+1]	= $aReg[$j];
		}
		
		$this->numLinhas++;

	}

	/**
	 * Carrega os dados a partir um array
	 */
	public function getHtmlCode() {
		global $system;

		/** Verifica se foi setado algum caracter set, senão utilizar UTF-8 **/
		if (!$this->getCharset()) {
			$this->setCharset('UTF-8');
		}
		
		$this->geraXML();
		//$this->geraJSArray();
		
		$script	= "<script>
	var ".$this->getNome().";
	".$this->getNome()." = new dhtmlXGridObject('".$this->getNome()."');
	".$this->getNome().".setHeader(\"".$this->getColHeader()."\");
	".$this->getNome().".setColumnIds(\"".$this->getColIds()."\");
	".$this->getNome().".setImagePath(\"".PKG_URL."/dhtmlx/dhtmlxGrid/codebase/imgs/\");
	".$this->getNome().".setInitWidths(\"".$this->getColWidth()."\");
	".$this->getNome().".setColAlign(\"".$this->getColAlign()."\");
	".$this->getNome().".setColTypes(\"".$this->getColTypes()."\");
	".$this->getNome().".setSkin(\"".$this->getSkin()."\");
	";

	/** Gerar os valores das combos **/
	if (is_array($this->coValues)) {
		foreach ($this->coValues as $key => $value) {
			$script .= "var combo".$key." = ".$this->getNome().".getCombo(".$key.");
		";
			foreach ($this->coValues[$key] as $valor) {
				$script	.= "combo".$key.".put('".$valor[0]."','".$valor[1]."');
		";
			}
		}
	}

	
	/** Verifica as opções **/
	if ($this->getAutoHeight()) {
		$script .= $this->getNome().".enableAutoHeight(true);
	";
	}
	if ($this->getAutoWidth()) {
		$script .= $this->getNome().".enableAutoWidth(true);
	";
	}

	if ($this->filtro) {
		$script .= $this->getNome().".attachHeader(\"".$this->filtro."\");
	";
		//$script .= $this->getNome().".enableSmartRendering(true);	";
	}
	
	if ($this->paging["ENABLE"]) {
		$script .= $this->getNome().'.i18n.paging={results:"Resultados",records:"De ",to:" Até ",page:"Página ",perpage:"Registros por Página",first:"Para a Primeira página", previous:"Página Anterior",found:"Registros encontrados",next:"Próxima página",last:"Para a última página",of:" de ", notfound:"Nenhum registro encontrado" };
	';
//		$script .= $this->getNome().".enablePaging(true,".$this->paging["NUMLINHAS"].",".$this->paging["NUMPAGINAS"].",'".$this->paging["DIVLINHAS"]."',true,'".$this->paging["DIVPAGINAS"]."');
//	";
		$script .= $this->getNome().".enablePaging(true,".$this->paging["NUMLINHAS"].",".$this->paging["NUMPAGINAS"].",'".$this->paging["DIVLINHAS"]."',true);
	";
		$script .= $this->getNome().".setPagingSkin('toolbar','".$this->getSkin()."');
	";
	}
	
	$script .= $this->getNome().".init();
	var xml = '".$this->xml."'
	".$this->getNome().".parse(xml,\"xml\");";

		
	/** Verifica se tem alguma linha com cor diferente **/
	foreach ($this->cores as $linha => $cor) {
		$script .= "
".$this->getNome().".setRowColor(".$linha.", '".$cor."');
		";
	}
		
	/** Verifica se tem alguma linha com valor diferente **/
	foreach ($this->valores as $linha => $aLinha) {
    	foreach ($aLinha as $coluna => $valor) {
			$script .= "
".$this->getNome().".cells(".$linha.",".$coluna.").setValue('".$valor."');
		";
		}
	}

		$script .= '</script>';
		
		/** retorna o código javascript **/
		return ($script);
	}
	
	
	/**
	 * Configurar o grid para fazer paginação
	 */
	public function setPaging($numLinhas,$divLinhas,$divPaginas) {
		$this->paging["ENABLE"] 	= true;
		$this->paging["NUMLINHAS"]	= $numLinhas;
		$this->paging["DIVLINHAS"]	= $divLinhas;
		$this->paging["DIVPAGINAS"]	= $divPaginas;
	}


	/**
	 * Configurar o grid para fazer filtro
	 */
	public function setFilter($filterHeader) {
		$this->filtro	= $filterHeader;
	}
	
	/**
	 * Adicionar um valor a uma combo
	 */
	public function addComboValue($index,$value,$label) {
		$this->coValues[$index][]	= array($value,$label);
	}
	
}
