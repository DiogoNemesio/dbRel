<?php

/**
 * Consulta
 * 
 * @package: consulta
 * @created: 23/03/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class consulta {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $system;

		$system->log->debug->debug(__CLASS__.": nova Instância");
	}
	
	/**
	 * Salva um consulta
	 * @param unknown $codigo
	 * @param unknown $codPaciente
	 * @param unknown $data
	 * @param unknown $obs
	 * @param unknown $orientacoes
	 * @return Ambigous <string, unknown, unknown>|string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$codPaciente,$data,$obs,$orientacoes) {
		global $system;
		
		/** Checar se o consulta já existe **/
		if ((!$codigo) || (consulta::existe($codigo) == false) ) {

			/** Inserir **/
			$err = consulta::inserir($codPaciente,$data,$obs,$orientacoes);
			if (is_numeric($err)) {
				$codigo		= $err;
				return ($codigo);
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(consulta::update($codigo,$codPaciente,$data,$obs,$orientacoes));
		}
    }
	
    /**
     * Inserir uma consulta
     * @param unknown $codigo
     * @param unknown $codPaciente
     * @param unknown $data
     * @param unknown $obs
     * @param unknown $orientacoes
     * @return string|unknown
     */
    public static function inserir ($codPaciente,$data,$obs,$orientacoes) {
		global $system;
		
		if (!$obs)			$obs		= null;
		if (!$orientacoes) 	$orientacoes	= null;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO CONSULTAS (CODIGO,COD_PACIENTE,DATA,OBS,ORIENTACOES) VALUES (null,?,CURDATE(),?,?)",
				array($codPaciente,$obs,$orientacoes)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			$system->geraEvento($system->getCodUsuario(),'I',consulta::getCodDicionario(),null,consulta::concatDadosEventos(0,null, $codPaciente,$data,$obs,$orientacoes),'CADASTRO DE CONSULTA');
				
			if (!$cod) {
				return('Erro:Não foi possível resgatar o código');
			}else{
				return($cod);
			}
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Atualizar a consulta
     * @param unknown $codigo
     * @param unknown $codPaciente
     * @param unknown $data
     * @param unknown $obs
     * @param unknown $orientacoes
     * @return NULL|string
     */
    public static function update ($codigo,$codPaciente,$data,$obs,$orientacoes) {
		global $system;
		
		if (!$obs)			$obs			= null;
		if (!$orientacoes) 	$orientacoes	= null;
				
		try {
			
			$system->db->con->beginTransaction();
			
			$system->geraEvento($system->getCodUsuario(),'U',consulta::getCodDicionario(),consulta::concatDadosEventos(1,$codigo),consulta::concatDadosEventos(0,$codigo,$codPaciente,$data,$obs,$orientacoes),'ALTERAÇÃO DE CONSULTA');
				
			$system->db->Executa("
				UPDATE 	CONSULTAS 
				SET		OBS				= ?,
						ORIENTACOES		= ?
				WHERE	CODIGO			= ?",
				array($obs,$orientacoes,$codigo)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Concatenar os dados para gerar log de eventos
     * @param unknown $busca
     * @param unknown $codigo
     * @param string $codPaciente
     * @param string $data
     * @param string $obs
     * @param string $orientacoes
     * @return string
     */
    public static function concatDadosEventos ($busca,$codigo,$codPaciente = null,$data = null,$obs = null,$orientacoes = null) {
    	global $system;
    	$s		= $system->getCaracSepEvento();
    	
    	if ($busca == 1) {
    		$info	= consulta::getInfo($codigo);
    		return ($info->CODIGO.$s.$info->COD_PACIENTE.$s.$info->DATA.$s.$info->OBS.$s.$info->ORIENTACOES);
    	}else {
    	   	return ($codigo.$s.$codPaciente.$s.$data.$s.$obs.$s.$orientacoes);
    	}
    }
    
    /**
     * Concatenar os dados para gerar log de eventos
     * @param unknown $busca
     * @param unknown $codigo
     * @param string $codPaciente
     * @param string $data
     * @param string $obs
     * @param string $orientacoes
     * @return string
     */
    public static function concatDadosEventosDoc ($codigo,$codConsulta,$nome,$codTipo) {
    	global $system;
    	$s		= $system->getCaracSepEvento();
   	   	return ($codigo.$s.$codConsulta.$s.$nome.$s.$codTipo);
    }
    
    /**
	 * 
	 * Lista as consultas
	 */
    public static function lista ($codPaciente) {
		global $system;
		return (
    		$system->db->extraiTodos("
				SELECT	C.*,DATE_FORMAT(C.DATA,'%d/%m/%Y') DATA_FORMATADA
				FROM	CONSULTAS C
    			WHERE	C.COD_PACIENTE	= '".$codPaciente."'
				ORDER	BY C.DATA,C.CODIGO
			")
   		);
    }

    /**
     * Verifica se o consulta existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	CONSULTAS C
				WHERE 	C.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    
    /**
     * Resgata as informações da consulta
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo) {
		global $system;
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	C.*,DATE_FORMAT(C.DATA,'%d/%m/%Y') DATA_FORMATADA
				FROM	CONSULTAS C
    			WHERE	C.CODIGO	= '".$codigo."'
			")
   		);	
    }

    
	/**
	 * Resgatar o código do dicionário dessa tabela
	 */
	public static function getCodDicionario() {
		global $system;
		
		$info = $system->db->extraiPrimeiro("
				SELECT	CODIGO
				FROM	DICIONARIO_DADOS DD
				WHERE 	DD.NOME		= 'CONSULTAS'
		");
		
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}

	/**
	 * Resgatar o código do dicionário da tabela de documentos
	 */
	public static function getCodDicionarioDoc() {
		global $system;
	
		$info = $system->db->extraiPrimeiro("
				SELECT	CODIGO
				FROM	DICIONARIO_DADOS DD
				WHERE 	DD.NOME		= 'CONSULTA_ARQUIVOS'
		");
	
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}
	
	/**
	 * Resgatas os documentos anexados
	 *
	 * @param integer $codConsulta
	 * @return array
	 */
	public static function getDocs($codConsulta,$codTipo = null) {
		global $system;
		
		$and	= null;
		
		if ($codTipo	!= null)	$and .= "AND 	TA.CODIGO 	= '".$codTipo."'";
		
		return($system->db->extraiTodos("
    			SELECT	A.*,TA.NOME TIPO
    			FROM	CONSULTA_ARQUIVOS 	A,
						TIPO_ARQUIVO		TA
				WHERE	A.COD_TIPO_ARQUIVO	= TA.CODIGO
				AND		A.COD_CONSULTA		= '".$codConsulta."'
				$and
		"));
	}
	
	/**
	 * Resgata um documento
	 * @param number $codDoc
	 * @return NULL
	 */
	public static function getDoc($codDoc) {
		global $system;
		
		$doc	= $system->db->extraiPrimeiro("
    			SELECT	A.*
    			FROM	CONSULTA_ARQUIVOS 	A
				WHERE	A.CODIGO		= '".$codDoc."'
		");
		
		if (isset ($doc->ARQUIVO)) {
			return($doc);
		}else{
			return null;
		}
	}
	
	/**
	 * Resgatas os tipos de documentos
	 *
	 * @param integer $codConsulta
	 * @return array
	 */
	public static function getTiposDoc() {
		global $system;
	
		return($system->db->extraiTodos("
    			SELECT	TA.*
    			FROM	TIPO_ARQUIVO		TA
		"));
	}
	
	/**
	 * Gerar o Código Html para as informações do paciente 
	 * @param unknown $campo
	 * @param unknown $valor
	 * @param unknown $tamanho
	 * @return string
	 */
	public static function addInfoPacienteHeader ($campo,$valor,$tamanho) {
		return ('<div class="input-prepend" style="padding: 0px 0px 0px 3px; margin: 0px;"><span class="add-on">'.$campo.'</span><input class="'.$tamanho.' " type="text" value="'.$valor.'" /></div>');
	}
	
	public static function uploadDoc($codConsulta, $arquivo, $nome, $codTipo) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO CONSULTA_ARQUIVOS (CODIGO,COD_CONSULTA,NOME,COD_TIPO_ARQUIVO,ARQUIVO) VALUES (null,?,?,?,?)",
					array($codConsulta,$nome,$codTipo,DHCUtil::getConteudoArquivo($arquivo))
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			$system->geraEvento($system->getCodUsuario(),'I',consulta::getCodDicionarioDoc(),null,consulta::concatDadosEventos($cod,$codConsulta,$nome,$codTipo),'INSERÇÃO DE DOCUMENTO');

			return($cod);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
		
	}
	
}