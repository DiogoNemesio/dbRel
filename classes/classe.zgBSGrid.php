<?php

/**
 * @package: zgBSGrid
 * @created: 20/03/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciar os grids em bootstrap
 */

class zgBSGrid {

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
	 * HTML
	 *
	 * @var string
	 */
	private $html;
	
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
	 * @param string $nome
	 * @param integer $tamanho
	 * @param string $alinhamento
	 * @param string $tipo
	 * @param string $nomeCampo
	 * @param string $imagem
	 */
	public function adicionaColuna ($nome,$tamanho,$alinhamento,$tipo,$nomeCampo = null,$imagem = null) {
		
		/** Validar os parâmetros **/
		if (($tamanho) && (!is_numeric($tamanho))) {
			DHCErro::halt('Parâmetro Tamanho não numérico !!!');
		}
		
		if (($alinhamento) && (mb_strtolower($alinhamento) != 'left') && (mb_strtolower($alinhamento) != 'center') && (mb_strtolower($alinhamento) != 'right')) {
			DHCErro::halt('Parâmetro Alinhamento deve ser (left,center,right) ');
		}
		
		/** Valida alguns tipos **/
		if (($tipo == 'img') && ($imagem == null)) {
			DHCErro::halt('Tipos imagem devem ter o parâmetro imagem definido !!!');
		}
		
		
		/** Define o próximo índice **/
		$i = sizeof($this->colunas) + 1;
		
		/** Definindo os valores **/
		$this->colunas[$i]["NOME"]		= $nome ? $nome : ' ';
		$this->colunas[$i]["TAM"]		= $tamanho;
		$this->colunas[$i]["ALIN"]		= $alinhamento;
		$this->colunas[$i]["TIPO"]		= $tipo ? $tipo : 'ro';
		$this->colunas[$i]["NOMECAMPO"]	= $nomeCampo ? $nomeCampo : $nome;
		$this->colunas[$i]["IMG"]		= $imagem;
		
		/** Altera o valor da variável numColunas **/
		$this->setNumColunas($i);
		
	}

	/**
	 * Adicionar uma Coluna do tipo botão
	 *
	 * @param string $tipo
	 */
	public function adicionaBotao ($tipo) {
	
	
		/** Valida alguns tipos **/
		if (($tipo != 'but-edit') && ($tipo != 'but-remove')) {
			DHCErro::halt('Tipo desconhecido !!!');
		}
	
		/** Adiciona a coluna **/	
		$this->adicionaColuna(null, 6, 'center', $tipo);
	
	}
	
	
	/**
	 * Adicionar uma Coluna do tipo Imagem
	 *
	 * @param string $imagem
	 */
	public function adicionaImagem ($imagem) {
	
		/** Adiciona a coluna **/	
		$this->adicionaColuna(null, 6, 'center', 'img',null,$imagem);
	
	}
	
	/**
	 * Adicionar uma Coluna do tipo Ícone
	 *
	 * @param string $Icone
	 */
	public function adicionaIcone ($icone,$descricao) {
	
		/** Adiciona a coluna **/
		$this->adicionaColuna(null, 4, 'center', 'icone', $descricao, $icone);
	
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
				if (($this->colunas[$j]["TIPO"] == 'img') || ($this->colunas[$j]["TIPO"] == 'icone')) {
					$this->linhas[$i][$j]	= null;
				}else{
					if (property_exists($dados[$i],$campo)) {
						$this->linhas[$i][$j]	= $dados[$i]->$campo;
					}else{
						$this->linhas[$i][$j]	= null;
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
	 * Gera o HTML
	 */
	private function geraHTML () {

		/** Verificar se foi inserido valores para campos fora do loadObjectArray **/
		foreach ($this->valores as $linha => $aLinha) {
			foreach ($aLinha as $coluna => $valor) {
				$this->linhas[$linha][$coluna+1]	= $valor;
			}
		}
				
		/** Inicializa o arquivo html **/
		$this->html	= $this->getNL() . '<table id="'.$this->getNome().'ID" class="table table-condensed table-hover table-striped table-bordered bootstrap-datatable datatable">' . $this->getNL();
		$this->html	.= '<thead><tr>' . $this->getNL();

		/** Faz o loop nas colunas **/
		for ($i = 1; $i <= $this->getNumColunas(); $i++) {
			/** Verifica o alinhamento **/
			$alinhamento 	= "text-align:center;";
			$this->html	.= $this->getNL() . '<th style="width:'.$this->colunas[$i]["TAM"].'%; '.$alinhamento.'">'.$this->colunas[$i]["NOME"].'</th>' . $this->getNL();
		}
		
		$this->html	.= '</tr></thead>' . $this->getNL();
		$this->html	.= '<tbody>' . $this->getNL();
		
		/** Faz o loop nas linhas **/
		for ($i = 0; $i < $this->getNumLinhas(); $i++) {
			/** Adiciona a Tag de inicialização de registro **/
			$this->html	.= $this->getTAB() . "<tr>" . $this->getNL();
			
			/** Faz o loop nas celulas da linha **/
			for ($j = 1; $j <= sizeof($this->linhas[$i]); $j++) {
				/** Alinhamento **/
				$alinhamento 	= (empty($this->colunas[$j]["ALIN"]) ? null : "text-align: ".$this->colunas[$j]["ALIN"].";");
				
				if ($this->colunas[$j]["TIPO"] == 'img') {
					$url = (empty($this->linhas[$i][$j]) ? "#" : $this->linhas[$i][$j]);
					$this->html	.= $this->getTAB() . $this->getTAB() . "<td style=\"".$alinhamento."\"><a href='".$url."'><img src='%IMG_URL%/".$this->colunas[$j]["IMG"]."'/></a></td>" . $this->getNL();
				}elseif ($this->colunas[$j]["TIPO"] == 'but-edit') {
					$url = (empty($this->linhas[$i][$j]) ? "#" : $this->linhas[$i][$j]);
					$this->html	.= $this->getTAB() . $this->getTAB() . "<td style=\"".$alinhamento."\"><a href='".$url."'><button class='btn btn-small btn-info' type='button'>Editar</button></a></td>" . $this->getNL();
				}elseif ($this->colunas[$j]["TIPO"] == 'but-remove') {
					$url = (empty($this->linhas[$i][$j]) ? "#" : $this->linhas[$i][$j]);
					$this->html	.= $this->getTAB() . $this->getTAB() . "<td style=\"".$alinhamento."\"><a href='".$url."'><button class='btn btn-small btn-danger' type='button'>Excluir</button></a></td>" . $this->getNL();
				}elseif ($this->colunas[$j]["TIPO"] == 'icone') {
					$url = (empty($this->linhas[$i][$j]) ? "#" : $this->linhas[$i][$j]);
					$this->html	.= $this->getTAB() . $this->getTAB() . "<td style=\"".$alinhamento."\"><a href='".$url."' data-toggle='tooltip' data-trigger='click hover' data-animation='true' data-title='".$this->colunas[$j]["NOMECAMPO"]."'><button class='btn btn-small' type='button'><i class='".$this->colunas[$j]["IMG"]."'></i></button></a></td>" . $this->getNL();
				}else{
					$this->html	.= $this->getTAB() . $this->getTAB() . "<td style=\"".$alinhamento."\">" . $this->linhas[$i][$j] . "</td>" . $this->getNL();
				}
			}
			
			/** Adiciona a Tag de finalização de registro **/
			$this->html	.= $this->getTAB() . "</tr>" . $this->getNL();
		}
		$this->html	.= "</tbody></table>" . $this->getNL();
		$this->html .= "<script>". $this->getNL();
		$this->html .= '$(document).ready(function() {
				$(\'#'.$this->getNome().'ID\').dataTable( {
					"sPaginationType"	: "bootstrap",
					"oLanguage"			: {
						"sUrl": "%PKG_URL%/bootstrap/lang/pt_BR.txt"
					}
				} );
			} );
		$(\'[data-toggle="tooltip"]\').tooltip();
		';
		$this->html .= "</script>". $this->getNL();
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
		
		$this->geraHTML();
		

		return ($this->html);
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
