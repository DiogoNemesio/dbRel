<?php

namespace Zage;

/**
 * Gerenciar os grids em bootstrap
 *
 * @package \Zage\Grid
 * @created 20/03/2013
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 *         
 */
class Grid {
	
	/**
	 * Tipos de Coluna
	 */
	const TP_TEXTO = 1;
	const TP_BOTAO = 2;
	const TP_ICONE = 3;
	const TP_IMAGEM = 4;
	
	/**
	 * Alinhamentos
	 */
	const LEFT = 1;
	const CENTER = 2;
	const RIGHT = 3;
	
	/**
	 * Estilos de paginação
	 */
	const PG_NONE = 0;
	const PG_BOOTSTRAP = 1;
	
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
	 * Variável para guardar as celulas
	 *
	 * @var array
	 */
	private $celulas;
	
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
	 * Tipo de paginação
	 *
	 * @var string
	 */
	private $pagingType;
	
	/**
	 * Variável para guardar os valores de uma combo
	 *
	 * @var array
	 */
	private $coValues;
	
	/**
	 * Endereço do script para carregar dados do grid (ServerSide Processing)
	 *
	 * @var string
	 */
	private $serverSideUrl;
	
	/**
	 * Nome
	 *
	 * @var string
	 */
	private $nome;
	
	/**
	 * Id
	 *
	 * @var string
	 */
	private $id;
	
	/**
	 * Endereço do arquivo de Linguagem
	 *
	 * @var string
	 */
	private $langUrl;
	
	/**
	 * Construtor
	 */
	public function __construct($nome) {
		
		/**
		 * Define o Nome do grid *
		 */
		$this->setNome ( $nome );
		
		/**
		 * Define o ID do grid *
		 */
		$this->setId ( $nome . 'ID' );
		
		/**
		 * Definindo os valores padrões das variáveis *
		 */
		$this->setNumLinhas ( 0 );
		$this->setNumColunas ( 0 );
		
		/**
		 * Inicializando os arrays *
		 */
		$this->colunas = array ();
		$this->linhas = array ();
		$this->celulas = array ();
		
		/**
		 * Definindo o caracter de nova linha *
		 */
		// $this->nl = null;
		$this->nl = chr ( 10 );
		
		/**
		 * Definindo o caracter tab *
		 */
		// $this->tab = null;
		$this->tab = chr ( 9 );
		
		/**
		 * Por padrão não fará paginação *
		 */
		$this->setPagingType ( self::PG_BOOTSTRAP );
		
		/**
		 * Charset Padrão *
		 */
		$this->setCharset ( "UTF-8" );
		
		/**
		 * Linguagem padrão *
		 */
		$this->setLangUrl ( PKG_URL . "/bootstrap/lang/pt_BR.txt" );
	}
	
	/**
	 * Resgatar o caracter de nova linha
	 */
	private function getNL() {
		return ($this->nl);
	}
	
	/**
	 * Resgatar o caracter TAB
	 */
	private function getTAB() {
		return ($this->tab);
	}
	
	/**
	 * Adicionar uma Coluna
	 *
	 * @param string $nome        	
	 * @param integer $tamanho        	
	 * @param string $alinhamento        	
	 * @param string $tipo        	
	 */
	private function adicionaColuna($nome, $tamanho, $alinhamento, $tipo) {
		
		/**
		 * Validar os parâmetros *
		 */
		if (($tamanho) && (! is_numeric ( $tamanho ))) {
			Erro::halt ( 'Parâmetro Tamanho não numérico !!!' );
		}
		
		if (($alinhamento) && ($alinhamento != self::LEFT) && ($alinhamento != self::CENTER) && ($alinhamento != self::RIGHT)) {
			Erro::halt ( 'Parâmetro Alinhamento deve ser (LEFT,CENTER,RIGHT) ' );
		}
		
		/**
		 * Define o próximo índice *
		 */
		$i = sizeof ( $this->colunas );
		
		/**
		 * Verifica o tipo para instanciar o objeto correto *
		 */
		switch ($tipo) {
			case self::TP_TEXTO :
				$this->colunas [$i] = new \Zage\Grid\Coluna\Texto ();
				break;
			case self::TP_BOTAO :
				$this->colunas [$i] = new \Zage\Grid\Coluna\Botao ();
				break;
			case self::TP_ICONE :
				$this->colunas [$i] = new \Zage\Grid\Coluna\Icone ();
				break;
			case self::TP_IMAGEM :
				$this->colunas [$i] = new \Zage\Grid\Coluna\Imagem ();
				break;
			default :
				Erro::halt ( 'Tipo de coluna desconhecido' );
				break;
		}
		
		/**
		 * Definindo os valores *
		 */
		$this->colunas [$i]->setNome ( $nome );
		$this->colunas [$i]->setTamanho ( $tamanho );
		$this->colunas [$i]->setAlinhamento ( $alinhamento );
		$this->colunas [$i]->setTipo ( $tipo );
		
		/**
		 * Altera o valor da variável numColunas *
		 */
		$this->setNumColunas ( $i + 1 );
		
		/**
		 * Retorna o índice adicionado *
		 */
		return ($i);
	}
	
	/**
	 * Adicionar uma Coluna do tipo botão
	 *
	 * @param string $tipo        	
	 */
	public function adicionaBotao($modelo) {
		
		/**
		 * Valida alguns tipos *
		 */
		if (($modelo != \Zage\Grid\Coluna\Botao::MOD_ADD) && ($modelo != \Zage\Grid\Coluna\Botao::MOD_EDIT) && ($modelo != \Zage\Grid\Coluna\Botao::MOD_REMOVE)) {
			Erro::halt ( 'Tipo desconhecido !!!' );
		}
		
		/**
		 * Adiciona a coluna *
		 */
		$i = $this->adicionaColuna ( "&nbsp;", 4, self::CENTER, self::TP_BOTAO );
		
		/**
		 * Define o modelo do botão *
		 */
		$this->colunas [$i]->setModelo ( $modelo );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Imagem
	 *
	 * @param string $imagem        	
	 */
	public function adicionaImagem($url, $endereco) {
		
		/**
		 * Adiciona a coluna *
		 */
		$i = $this->adicionaColuna ( null, 4, self::CENTER, self::TP_IMAGEM );
		
		/**
		 * Define as informações da Imagem *
		 */
		$this->colunas [$i]->setUrl ( $url );
		$this->colunas [$i]->setEnderecoImagem ( $endereco );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Ícone
	 *
	 * @param string $Icone        	
	 */
	public function adicionaIcone($url, $icone, $descricao) {
		
		/**
		 * Adiciona a coluna *
		 */
		$i = $this->adicionaColuna ( null, 4, self::CENTER, self::TP_ICONE );
		
		/**
		 * Define as informações do Ícone *
		 */
		$this->colunas [$i]->setUrl ( $url );
		$this->colunas [$i]->setIcone ( $icone );
		$this->colunas [$i]->setDescricao ( $descricao );
	}
	
	/**
	 * Adicionar uma Coluna do tipo Texto
	 *
	 * @param string $Icone        	
	 */
	public function adicionaTexto($nome, $tamanho, $alinhamento, $nomeCampo) {
		
		/**
		 * Adiciona a coluna *
		 */
		$i = $this->adicionaColuna ( $nome, $tamanho, $alinhamento, self::TP_TEXTO );
		
		/**
		 * Define as informações do Texto *
		 */
		$this->colunas [$i]->setNomeCampo ( $nomeCampo );
	}
	
	/**
	 * Carrega os dados a partir um array
	 *
	 * @param array $dados        	
	 */
	public function importaDadosArray($dados) {
		
		/**
		 * Array esperado é do tipo Zend_Db::FETCH_OBJ
		 *
		 * As propriedades do objeto devem ser iguais aos nomes das colunas
		 */
		
		/**
		 * Zera o array de linhas *
		 */
		$this->linhas = array ();
		$this->setNumLinhas ( 0 );
		
		/**
		 * Zera o array de celulas *
		 */
		$this->celulas = array ();
		
		if ($dados instanceof \Zend\Db\ResultSet\ResultSet) {
			$i = 0;
			foreach ($dados as $d) {
				/** Inicializa os objetos **/
				$this->linhas [$i] = new \Zage\Grid\Linha ();
				$this->linhas [$i]->setIndice ( $i );
					
				for($j = 0; $j <= $this->getNumColunas () - 1; $j ++) {
					$nome = $this->colunas [$j]->getNome ();
					$campo = $this->colunas [$j]->getNomeCampo ();
						
					$this->celulas [$i] [$j] = new \Zage\Grid\Celula ();
					$this->celulas [$i] [$j]->setLinha ( $i );
					$this->celulas [$i] [$j]->setColuna ( $j );
						
					if (! empty ( $campo )) {
						if (property_exists ( $d, $campo )) {
							$this->celulas [$i] [$j]->setValor ( $d->$campo );
						}
					}
				}
				$this->numLinhas ++;
				$i++;
			}
		}elseif (is_array($dados) ) {
			for($i = 0; $i < sizeof ( $dados ); $i ++) {
				/** Inicializa os objetos **/
				$this->linhas [$i] = new \Zage\Grid\Linha ();
				$this->linhas [$i]->setIndice ( $i );
					
				for($j = 0; $j <= $this->getNumColunas () - 1; $j ++) {
					$nome = $this->colunas [$j]->getNome ();
					$campo = $this->colunas [$j]->getNomeCampo ();
			
					$this->celulas [$i] [$j] = new \Zage\Grid\Celula ();
					$this->celulas [$i] [$j]->setLinha ( $i );
					$this->celulas [$i] [$j]->setColuna ( $j );
			
					if (! empty ( $campo )) {
						if (property_exists ( $dados [$i], $campo )) {
							$this->celulas [$i] [$j]->setValor ( $dados [$i]->$campo );
						}
					}
				}
				$this->numLinhas ++;
			}
		}
		
	}
	
	/**
	 * Gera o HTML
	 */
	private function geraHTML() {
		
		/**
		 * Inicializa o arquivo html *
		 */
		$this->html = $this->getNL () . '<table id="' . $this->getId () . '" class="table table-condensed table-hover table-striped table-bordered bootstrap-datatable datatable display ">' . $this->getNL ();
		$this->html .= '<thead><tr>' . $this->getNL ();
		
		/**
		 * Monta o cabeçalho *
		 */
		for($i = 0; $i < $this->getNumColunas (); $i ++) {
			/**
			 * Verifica o alinhamento *
			 */
			$alinhamento = "text-align:center;";
			/**
			 * Verifica se a coluna está ativa *
			 */
			if ($this->colunas [$i]->getAtiva () == true) {
				$this->html .= $this->getNL () . '<th style="width:' . $this->colunas [$i]->getTamanho () . '%; ' . $alinhamento . '">' . $this->colunas [$i]->getNome () . '</th>' . $this->getNL ();
			}
		}
		
		$this->html .= '</tr></thead>' . $this->getNL ();
		$this->html .= '<tbody>' . $this->getNL ();
		
		/**
		 * Faz o loop nas linhas *
		 */
		for($i = 0; $i < $this->getNumLinhas (); $i ++) {
			
			/**
			 * Verifica se a linha está ativa *
			 */
			if ($this->linhas [$i]->getAtiva () == true) {
				
				/**
				 * Adiciona a Tag de inicialização de registro *
				 */
				$this->html .= $this->getTAB () . "<tr>" . $this->getNL ();
				
				/**
				 * Faz o loop nas celulas da linha *
				 */
				for($j = 0; $j < sizeof ( $this->celulas [$i] ); $j ++) {
					
					/**
					 * Verifica se a coluna está ativa *
					 */
					if ($this->colunas [$j]->getAtiva () == true) {
						
						/**
						 * Alinhamento *
						 */
						switch ($this->colunas [$j]->getAlinhamento ()) {
							case self::LEFT :
								$alinhamento = "text-align: left;";
								break;
							case self::CENTER :
								$alinhamento = "text-align: center;";
								break;
							case self::RIGHT :
								$alinhamento = "text-align: right;";
								break;
							default :
								$alinhamento = "text-align: center;";
								break;
						}
						
						/**
						 * Verifica se a célula está ativa *
						 */
						if ($this->celulas [$i] [$j]->getAtiva () == true) {
							if ($this->colunas [$j]->getTipo () == self::TP_IMAGEM) {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (), $this->celulas [$i] [$j]->getEnderecoImagem () ) . "</td>" . $this->getNL ();
							} else {
								$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">" . $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor () ) . "</td>" . $this->getNL ();
							}
						} else {
							$this->html .= $this->getTAB () . $this->getTAB () . "<td style=\"" . $alinhamento . "\">&nbsp;</td>" . $this->getNL ();
						}
					} else {
						// $this->html .= $this->getTAB() . $this->getTAB() . "<td>&nbsp;</td>" . $this->getNL();
					}
				}
				
				/**
				 * Adiciona a Tag de finalização de registro *
				 */
				$this->html .= $this->getTAB () . "</tr>" . $this->getNL ();
			}
		}
		
		/**
		 * Verifica se foi passado o parâmetro para fazer processamento no servidor *
		 */
		if ($this->getServerSideUrl () == '') {
			$ssCode = '';
		} else {
			$ssCode = '"bProcessing": true,
        			"bServerSide": true,
        			"sAjaxSource": "' . $this->getServerSideUrl () . '",';
		}
		
		/**
		 * Verifica o tipo de paginação *
		 */
		switch ($this->getPagingType ()) {
			case self::PG_NONE :
				$sPaging = "";
				break;
			case self::PG_BOOTSTRAP :
				$sPaging = '"sPaginationType"	: "full_numbers",';
				break;
			default :
				Erro::halt ( 'Tipo de paginação desconhecida !!!' );
				break;
		}
		
		/**
		 * Verifica o arquivo de linguagem *
		 */
		if ($this->getLangUrl () != '') {
			$lang = '"oLanguage"			: {
						"sUrl": "' . $this->getLangUrl () . '"
					}, ';
		} else {
			$lang = "";
		}
		
		$this->html .= "</tbody></table>" . $this->getNL ();
		$this->html .= "<script>" . $this->getNL ();
		$this->html .= '$(document).ready(function() {
				$(\'#' . $this->getId () . '\').dataTable( {
					"sDom": \'<"toolbar">frtlip\',
					"bJQueryUI": true,
					aaSorting: [],
					' . $sPaging . '
					' . $ssCode . '
					' . $lang . '
					"fnInitComplete": function () {
            			$("div.toolbar").html(\'<div class="btn-group"><button type="button" class="btn btn-inverse btn-small dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i></button><ul class="dropdown-menu"><li><a href="%URLADD%"><i class="icon-plus"></i>&nbsp;%NOME%</a></li></ul></div>\');
        			},
				} );
				
			} );
		$(\'[data-toggle="tooltip"]\').tooltip();
		
		';
		$this->html .= "</script>" . $this->getNL ();
	}
	
	/**
	 * Carrega os dados a partir um array
	 */
	public function getHtmlCode() {
		/**
		 * Verifica se foi setado algum caracter set, senão utilizar UTF-8 *
		 */
		if (! $this->getCharset ()) {
			$this->setCharset ( 'UTF-8' );
		}
		
		$this->geraHTML ();
		
		return ($this->html);
	}
	
	/**
	 * Retorna o código em Json dos registros
	 *
	 * @param unknown $echo        	
	 * @param unknown $numRegTotal        	
	 * @param unknown $inicio        	
	 * @param unknown $tamanho        	
	 * @return string
	 */
	public function getJsonData($echo, $numRegTotal, $inicio, $tamanho) {
		$output = array (
				"sEcho" => intval ( $echo ),
				"iTotalRecords" => $numRegTotal,
				"iTotalDisplayRecords" => $this->getNumLinhas (),
				"aaData" => array () 
		);
		
		if ($inicio == null)
			$inicio = 0;
		if ($tamanho == null)
			$tamanho = 10;
		
		$t = ($inicio + $tamanho);
		
		if ($t > $this->getNumLinhas ()) {
			$t = $this->getNumLinhas ();
		}
		
		for($i = $inicio; $i < $t; $i ++) {
			$row = array ();
			/**
			 * Faz o loop nas celulas da linha *
			 */
			for($j = 0; $j < sizeof ( $this->celulas [$i] ); $j ++) {
				if ($this->colunas [$j]->getTipo () == self::TP_IMAGEM) {
					$row [] = $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor (), $this->celulas [$i] [$j]->getEnderecoImagem () );
				} else {
					$row [] = $this->colunas [$j]->geraHtmlValor ( $this->celulas [$i] [$j]->getValor () );
				}
			}
			$output ['aaData'] [] = $row;
		}
		
		return json_encode ( $output );
	}
	
	/**
	 * Definir o valor de uma célula
	 */
	public function setValorCelula($linha, $coluna, $valor) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			$this->celulas [$linha] [$coluna]->setValor ( $valor );
		}
	}
	
	/**
	 * Definir o valor do endereço da imagem de uma célula do tipo imagem
	 */
	public function setEnderecoImagemCelula($linha, $coluna, $enderecoImagem) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			if ($this->colunas [$coluna]->getTipo () !== self::TP_IMAGEM) {
				Erro::halt ( 'Endereço de imagem só pode ser definido para uma coluna do tipo Imagem !!!' );
			} else {
				$this->celulas [$linha] [$coluna]->setEnderecoImagem ( $enderecoImagem );
			}
		}
	}
	
	/**
	 * Desabilitar uma Linha
	 */
	public function desabilitaLinha($indice) {
		if (isset ( $this->linhas [$indice] )) {
			$this->linhas [$indice]->desativar ();
		}
	}
	
	/**
	 * Habilitar uma Linha
	 */
	public function habilitaLinha($indice) {
		if (isset ( $this->linhas [$indice] )) {
			$this->linhas [$indice]->ativar ();
		}
	}
	
	/**
	 * Desabilitar uma Coluna
	 */
	public function desabilitaColuna($indice) {
		if (isset ( $this->colunas [$indice] )) {
			$this->colunas [$indice]->desativar ();
		}
	}
	
	/**
	 * Habilitar uma Linha
	 */
	public function habilitaColuna($indice) {
		if (isset ( $this->colunas [$indice] )) {
			$this->colunas [$indice]->ativar ();
		}
	}
	
	/**
	 * Desabilitar uma Célula
	 */
	public function desabilitaCelula($linha, $coluna) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			$this->celulas [$linha] [$coluna]->desativar ();
		}
	}
	
	/**
	 * Habilitar uma Célula
	 */
	public function habilitaCelula($linha, $coluna) {
		if (isset ( $this->celulas [$linha] [$coluna] )) {
			$this->celulas [$linha] [$coluna]->ativar ();
		}
	}
	
	/**
	 *
	 * @return the $numLinhas
	 */
	public function getNumLinhas() {
		return $this->numLinhas;
	}
	
	/**
	 *
	 * @param number $numLinhas        	
	 */
	public function setNumLinhas($numLinhas) {
		$this->numLinhas = $numLinhas;
	}
	
	/**
	 *
	 * @return the $numColunas
	 */
	public function getNumColunas() {
		return $this->numColunas;
	}
	
	/**
	 *
	 * @param number $numColunas        	
	 */
	public function setNumColunas($numColunas) {
		$this->numColunas = $numColunas;
	}
	
	/**
	 *
	 * @return the $html
	 */
	public function getHtml() {
		return $this->html;
	}
	
	/**
	 *
	 * @param string $html        	
	 */
	public function setHtml($html) {
		$this->html = $html;
	}
	
	/**
	 *
	 * @return the $charset
	 */
	public function getCharset() {
		return $this->charset;
	}
	
	/**
	 *
	 * @param string $charset        	
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
	}
	
	/**
	 *
	 * @return the $pagingType
	 */
	public function getPagingType() {
		return $this->pagingType;
	}
	
	/**
	 *
	 * @param string $pagingType        	
	 */
	public function setPagingType($pagingType) {
		$this->pagingType = $pagingType;
	}
	
	/**
	 *
	 * @return the $serverSideUrl
	 */
	public function getServerSideUrl() {
		return $this->serverSideUrl;
	}
	
	/**
	 *
	 * @param multitype: $serverSideUrl        	
	 */
	public function setServerSideUrl($serverSideUrl) {
		$this->serverSideUrl = $serverSideUrl;
	}
	
	/**
	 *
	 * @return the $nome
	 */
	private function getNome() {
		return $this->nome;
	}
	
	/**
	 *
	 * @param string $nome        	
	 */
	private function setNome($nome) {
		$this->nome = $nome;
	}
	
	/**
	 *
	 * @return the $id
	 */
	private function getId() {
		return $this->id;
	}
	
	/**
	 *
	 * @param string $id        	
	 */
	private function setId($id) {
		$this->id = $id;
	}
	
	/**
	 *
	 * @return the $langUrl
	 */
	private function getLangUrl() {
		return $this->langUrl;
	}
	
	/**
	 *
	 * @param string $langUrl        	
	 */
	private function setLangUrl($langUrl) {
		$this->langUrl = $langUrl;
	}
}
