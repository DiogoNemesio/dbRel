<?php

/**
 * Rotinas gerais do Sistema
 * 
 * @package: CLINAR
 * @created: 22/03/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class dbRel extends webSystem {

	/**
	 * Objeto que irá guardar a Instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;

	
	private $dynHtml;
	public 	$parametros;
	
	/**
	 * Caracter separador de eventos
	 * @var unknown
	 */
	private $caracSepEvento;
	
	/**
	 * Empresa selecionada
	 * @var unknown
	 */
	private $codEmpresa;
	
	
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		/** Verificar função inicializaSistema() **/

	}
	
	/**
	 * Construtor para implemetar SINGLETON
	 *
	 * @return object
	 */
	public static function init() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    /**
     * Refazer a função para não permitir a clonagem deste objeto.
     *
     */
    public function __clone() {
        DHCErro::halt('Não é permitido clonar ');
    }
    
    public function inicializaSistema () {

    	/** Chama o construtor da classe mae **/
    	 parent::__construct();
    	
		$this->log->debug->debug(__CLASS__.": nova Instância");

		/** Definindo atributos globais a Instância de e-mail (Podem ser alterados no momento do envio do e-mail) **/
		$this->mail->setSubject('.:: Erro no sistema (drFritz) ::.');
		
		/** Dynamic Html **/
		$this->dynHtml = $this->genDynHtmlLoad();
		
		/** Resgatar os parâmetros do sistema **/
		$this->getParamFromDB();
	
		/** Definir caracter separador de campos da tabela de eventos **/
		//$this->setCaracSepEvento(chr(176));
		$this->setCaracSepEvento("~");
    }
	
    /**
	 * @return the $caracSepEvento
	 */
	public function getCaracSepEvento() {
		return $this->caracSepEvento;
	}

	/**
	 * @param unknown $caracSepEvento
	 */
	public function setCaracSepEvento($caracSepEvento) {
		$this->caracSepEvento = $caracSepEvento;
	}

	/**
	 * @return the $codEmpresa
	 */
	public function getCodEmpresa() {
		return $this->codEmpresa;
	}

	/**
	 * @param field_type $codEmpresa
	 */
	public function setCodEmpresa($codEmpresa) {
		$this->codEmpresa = $codEmpresa;
	}

	public function getSkin() {
    	return ('dhx_skyblue');
    }
    
    /**
     * Gerar o html da Combo
     *
     * @return string
     */
    public function geraHtmlCombo($array,$codigo,$valor,$codigoSel = null,$valorDefault = null) {
    	global $system;
    	
    	$html    = '';
    	if ($valorDefault !== null) {
    		($codigoSel == null) ? $selected = "selected=\"true\"" : $selected = "";
    		$html    .= "<option $selected value=\"\">".$valorDefault."</option>";
    	}
    	for($i=0; $i<sizeof($array);$i++) {
    		if ($codigoSel !== null) {
    			($codigoSel == $array[$i]->$codigo) ? $selected = "selected=\"true\"" : $selected = "";
    		}else{
    			if ($i == 0) {
    				$selected = "selected=\"true\"";
    			}else{
    				$selected = "";
    			}
    		}
    		$html .= "<option value=\"".$array[$i]->$codigo."\" $selected>".$array[$i]->$valor.'</option>';
    	}
    	return ($html);
    }
    
    /**
     * 
     * @return string
     */
    public function genDynHtmlLoad($codLocal = "H") {
		global $system;
    	
		$and	= '';
		
		$html   = $system->db->extraiTodos("
			SELECT  H.URL
			FROM    CONFIG_LOAD_HTML H
			WHERE   ATIVO   	= 1
			AND		COD_LOCAL	= '".$codLocal."'
			ORDER   BY H.ORDEM
		");
    	
		$return = '<!-- Carregado dinamicamente através do dinamicHtmlLoad -->'.PHP_EOL;
		for ($i = 0; $i < sizeof($html); $i++) {
			$return .= $html[$i]->URL.PHP_EOL;
		}
		$return .= '<!-- Fim do carregamento dinâmico (dinamicHtmlLoad) -->'.PHP_EOL;
		return ($return);
	}
	
	/**
	 *
	 * @return string
	 */
	public function getParamFromDB() {
		global $system;
		 
		$info   = parametro::lista();
		$this->parametros	= null;

		for ($i = 0; $i < sizeof($info); $i++) {
			$this->parametros[$info[$i]->CODIGO]	= new parametro();
			$this->parametros[$info[$i]->CODIGO]->setValor($info[$i]->VALOR);
			$this->parametros[$info[$i]->CODIGO]->setDescricao($info[$i]->DESCRICAO);
		}
	}
	
	
	/**
	 * 
	 * @return string
	 */
	public function getDynHtmlLoad() {
		return ($this->dynHtml);
	}
    	
	/**
	 * Resgata as telas / itens para um determinado usuário
	 */
	public function DBGetMenuItens ($codUsuario) {
		return($this->db->extraiTodos("
				SELECT  M.*
				FROM    MENU 				M,
						MENU_TIPO_USUARIO 	MTU,
						USUARIOS 			U
				WHERE   M.CODIGO				= MTU.COD_MENU
				AND		MTU.COD_TIPO_USUARIO	= U.COD_TIPO
				AND		U.CODIGO				= '".$codUsuario."'
				ORDER   BY NIVEL,ORDEM
                "));
	}
	
	
	/**
	 * Gerar o html da localização do Menu
	 *
	 * @return string
	 */
	public function geraLocalizacao($codMenu,$codTipoUsuario) {
		global $system;
	
		$aLocal         = zgBSNavBar::getArrayArvoreMenuUrl($codMenu);
		$html          	= '<div><hr class="zgHr"><ul class="breadcrumb">';
		$html			.= '<li><a href="#">Home</a> <span class="divider">/</span></li>';
		$total			= sizeof($aLocal);
		for ($i = 0; $i < sizeof($aLocal); $i++) {
			if ($aLocal[$i]->LINK != null) {
				$info           = usuario::getInfo($system->getCodUsuario());
				$codTipo        = $info->COD_TIPO;
				$url 			= zgBSNavBar::montaUrl($aLocal[$i]->LINK, $aLocal[$i]->CODIGO, $codTipo);
			} else{
				$url			= "#";
			}
			$html  .= "<li><a href='".$url."'>".$aLocal[$i]->NOME."</a>";
			if ($i < ($total -1) ) {
				$html .= '<span class="divider">/</span>';
			}
			$html .= "</li>";
			
		}
		$html	.= "</ul></div>";
		return ($html);
	}

	/**
	 * Gera evento de Log no sistema
	 * @param unknown $codUsuario
	 * @param unknown $codTipoEvento
	 * @param unknown $tabela
	 * @param unknown $valorAnt
	 * @param unknown $valorPos
	 * @return NULL|string
	 */
	public function geraEvento ($codUsuario,$codTipoEvento,$tabela,$valorAnt,$valorPos,$historico = null) {
		
		if ($valorAnt == $valorPos) return;
		
		try {
			$this->db->con->beginTransaction();
			$this->db->Executa("INSERT INTO LOG_EVENTOS (CODIGO,COD_USUARIO,DATA,COD_TIPO_EVENTO,COD_DICIONARIO,VALOR_ANTERIOR,VALOR_POSTERIOR,HISTORICO) VALUES (null,?,?,?,?,?,?,?)",
				array($codUsuario,date("Y-m-d H:i:s"),$codTipoEvento,$tabela,$valorAnt,$valorPos,$historico)
			);
			$this->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$this->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}
	

	/**
	 * Atualiza o dicionário de dados
	 */
	public function atualizaDicionario() {
		
		try {
			
			$info	= $this->db->extraiPrimeiro("
				SELECT  COUNT(*) NUM
				FROM    LOG_EVENTOS L
            ");
				
			if ($info->NUM > 0 ) return ("Existem registros na tabela LOG_EVENTOS");
			
			$this->db->con->beginTransaction();
			$this->db->Executa("DELETE FROM CAMPOS_DICIONARIO",array());
			$this->db->Executa("DELETE FROM DICIONARIO_DADOS",array());
			
			$tables	= $this->db->extraiTodos("
				SELECT  TABLE_NAME
				FROM    `INFORMATION_SCHEMA`.`TABLES` 
				WHERE   `TABLE_SCHEMA`='drFritz'
				AND		TABLE_NAME 	NOT IN ('CAMPOS_DICIONARIO','DICIONARIO_DADOS','MENU','CIDADES','CONFIG_LOAD_HTML','ESTADOS','LOG_EVENTOS','MENU_TIPO_USUARIO','REGIOES')
				AND		TABLE_NAME  NOT LIKE 'TIPO%'
				ORDER   BY `TABLE_NAME`
            ");
			
			for ($i = 0; $i < sizeof($tables); $i++) {
				$this->db->Executa("INSERT INTO DICIONARIO_DADOS (CODIGO,NOME,DESCRICAO) VALUES (null,?,?)",array($tables[$i]->TABLE_NAME,$tables[$i]->TABLE_NAME));
				$cod	= $this->db->con->lastInsertId();
				
				$colunas = $this->db->extraiTodos("
					SELECT  `COLUMN_NAME` COLUNA
					FROM    `INFORMATION_SCHEMA`.`COLUMNS` 
					WHERE   `TABLE_SCHEMA`='drFritz'
					AND		TABLE_NAME = '".$tables[$i]->TABLE_NAME."'
					ORDER   BY ORDINAL_POSITION
            	");
				
				for ($j=0; $j < sizeof($colunas); $j++) {
					$this->db->Executa("INSERT INTO CAMPOS_DICIONARIO (CODIGO,COD_DICIONARIO,NOME,ORDEM) VALUES (null,?,?,?)",array($cod,$colunas[$j]->COLUNA,($j + 1)));
				}
			}
				
			$this->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$this->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}

}