<?php

/**
 * Pergunta
 * 
 * @package: pergunta
 * @created: 06/04/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class pergunta {

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
	 * Salva uma pergunta
	 * @param unknown $codigo
	 * @param unknown $codQuestionario
	 * @param unknown $descricao
	 * @param unknown $codTipo
	 * @param unknown $codStatus
	 * @param unknown $ordem
	 * @return string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio) {
		global $system;
		
		/** Checar se a pergunta já existe **/
		if ((!$codigo) || (pergunta::existe($codigo) == false) ) {

			/** Inserir **/
			$err = pergunta::inserir($codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio);
			if (is_numeric($err)) {
				$codigo		= $err;
				return ($codigo);
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(pergunta::update($codigo,$codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio));
		}
    }
	
    
    /**
     * Inserir a pergunta
     * @param unknown $codQuestionario
     * @param unknown $descricao
     * @param unknown $codTipo
     * @param unknown $codStatus
     * @param unknown $ordem
     * @return string|unknown
     */
    public static function inserir ($codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO PERGUNTAS (CODIGO,COD_QUESTIONARIO,DESCRICAO,COD_TIPO,COD_STATUS,ORDEM,COD_OBRIGATORIO) VALUES (null,?,?,?,?,?,?)",
				array($codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			if (!$cod) {
				return('Erro:Não foi possível resgatar o código');
			}else{
				$system->geraEvento($system->getCodUsuario(),'I',pergunta::getCodDicionario(),null,pergunta::concatDadosEventos(0,$cod,$codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio),'CADASTRO DE PERGUNTA');
				return($cod);
			}
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Atualizar a pergunta 
     * @param unknown $codigo
     * @param unknown $codQuestionario
     * @param unknown $descricao
     * @param unknown $codTipo
     * @param unknown $codStatus
     * @param unknown $ordem
     * @return NULL|string
     */
    public static function update ($codigo,$codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			
			$system->geraEvento($system->getCodUsuario(),'U',pergunta::getCodDicionario(),pergunta::concatDadosEventos(1,$codigo),pergunta::concatDadosEventos(0,$codigo,$codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio),'ALTERAÇÃO DE PERGUNTA');
			
			$system->db->Executa("
				UPDATE  PERGUNTAS 
				SET		COD_QUESTIONARIO	= ?,
						DESCRICAO			= ?,
						COD_TIPO			= ?,
						COD_STATUS			= ?,
						ORDEM				= ?,
						COD_OBRIGATORIO		= ?
				WHERE	CODIGO				= ?",
				array($codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio,$codigo)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

	/**
	 * Lista as perguntas
	 */
    public static function lista ($codQuestionario,$descricao = null) {
		global $system;
		
		$and	= null;
		
		if ($descricao != null)	$and = "AND 	P.DESCRICAO 	LIKE '%".$descricao."%'";    	
		
		return (
    		$system->db->extraiTodos("
				SELECT	P.*,TS.NOME STATUS,TP.NOME TIPO,T.NOME OBRIGATORIO
				FROM	PERGUNTAS			P, 
    					TIPO_STATUS 		TS,
    					TIPO_PERGUNTA		TP,
    					TIPO_OBRIGATORIO	T
				WHERE	P.COD_STATUS		= TS.CODIGO
    			AND		P.COD_TIPO			= TP.CODIGO
    			AND		P.COD_OBRIGATORIO	= T.CODIGO
    			AND		P.COD_QUESTIONARIO	= '".$codQuestionario."'
    			$and
				ORDER	BY P.ORDEM
			")
   		);
    }

    /**
     * Lista os tipos de perguntas
     */
    public static function listaTipos () {
    	global $system;
    	return (
    		$system->db->extraiTodos("
    			SELECT	*
    			FROM	TIPO_PERGUNTA	TP
    			ORDER	BY TP.NOME
    		")
    	);
    }
    
    /**
     * Lista os valores do tipo Obrigatório
     */
    public static function listaTiposObrigatorio () {
    	global $system;
    	return (
    		$system->db->extraiTodos("
    			SELECT	*
    			FROM	TIPO_OBRIGATORIO	T
    			ORDER	BY T.NOME
    		")
    	);
    }
    
    /**
     * Lista os status de perguntas
     */
    public static function listaStatus () {
    	global $system;
    	return (
    			$system->db->extraiTodos("
    			SELECT	*
    			FROM	TIPO_STATUS	TS
    			ORDER	BY TS.NOME
    		")
    	);
    }
    
    /**
     * Lista os possíveis valores de uma pergunta
     */
    public static function listaValores ($codPergunta) {
    	global $system;
    	return (
    			$system->db->extraiTodos("
    			SELECT	*
    			FROM	VALORES_PERGUNTA	VS
    			WHERE	VS.COD_PERGUNTA		= '".$codPergunta."'
    			ORDER	BY VS.COD_VALOR
    		")
    	);
    }
    
    /**
     * Verifica se o pergunta existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	PERGUNTAS	P
				WHERE 	P.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Verifica se o pergunta existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function getMaiorOrdem ($codQuestionario) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	MAX(ORDEM) ORDEM
				FROM	PERGUNTAS	P
				WHERE 	P.COD_QUESTIONARIO	= '".$codQuestionario."'
		");
    	 
    	if ($info->ORDEM > 0) {
    		return $info->ORDEM;
    	}else{
    		return 0;
    	}
    }
    
    
    /**
     * Resgata as informações do pergunta
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo) {
		global $system;

    	return (
    		$system->db->extraiPrimeiro("
				SELECT	P.*,TS.NOME STATUS,TP.NOME TIPO,T.NOME OBRIGATORIO
				FROM	PERGUNTAS			P, 
    					TIPO_STATUS 		TS,
    					TIPO_PERGUNTA		TP,
    					TIPO_OBRIGATORIO	T
				WHERE	P.COD_STATUS		= TS.CODIGO
    			AND		P.COD_TIPO			= TP.CODIGO
    			AND		P.COD_OBRIGATORIO	= T.CODIGO
				AND		P.CODIGO			= '".$codigo."'
			")
   		);	
    }


    /**
     * Verifica se existem respostas para pergunta
     *
     * @param string codpergunta
     * @return array
     */
    public static function existemRespostas ($codPergunta) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	RESPOSTAS_PERGUNTA RP
				WHERE 	RP.COD_PERGUNTA		= '".$codPergunta."'
		");
    	 
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
	/**
	 * Exclui a pergunta
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function exclui($codigo) {
		global $system;
 
		/** Verifica se a pergunta existe **/
		if (pergunta::existe($codigo) == false) return ('Erro: pergunta não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			if (pergunta::existemRespostas($codigo) == true) {
				return ("Erro: Essa pergunta já foi utilizada. Inative-a.");
			}
			
			$system->geraEvento($system->getCodUsuario(),'U',pergunta::getCodDicionario(),pergunta::concatDadosEventos(1,$codigo),null,'EXCLUSÃO DE PERGUNTA');
			
			/** Exclui os valores das perguntas **/
			$system->db->Executa ("DELETE FROM VALORES_PERGUNTA WHERE COD_PERGUNTA IN (SELECT CODIGO FROM PERGUNTAS WHERE COD_PERGUNTA =  ?)", array ($codigo) );
				
			/** Exclui as perguntas **/
			$system->db->Executa ("DELETE FROM PERGUNTAS WHERE CODIGO = ?", array ($codigo) );
				
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
	
	/**
	 * Verifica se existem respostas para pergunta
	 *
	 * @param string codpergunta
	 * @return array
	 */
	public static function existeValor ($codPergunta,$valor) {
		global $system;
	
		$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	VALORES_PERGUNTA VP
				WHERE 	VP.COD_PERGUNTA		= '".$codPergunta."'
				AND		VP.COD_VALOR		= '".$valor."'
		");
	
		if ($info->NUM > 0) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Adiciona um valor a uma pergunta do tipo lista de valores
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return void|NULL|string
	 */
	public static function adicionaValor ($codPergunta,$valor) {
		global $system;
		
		if (pergunta::existeValor($codPergunta, $valor) == true) {
			return;
		}
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO VALORES_PERGUNTA (COD_PERGUNTA,COD_VALOR) VALUES (?,?)",
				array($codPergunta,$valor)
			);
			$system->db->con->commit();
			
			return (null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}
	
	/**
	 * Exclui um valor de uma pergunta do tipo de lista de valores
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return string|NULL
	 */
	public static function excluiValor($codPergunta,$valor) {
		global $system;
		
		if (pergunta::existeValor($codPergunta, $valor) == false) {
			return;
		}
		try {
			$system->db->con->beginTransaction ();
				
			/** Exclui os valores das perguntas **/
			$system->db->Executa ("DELETE FROM VALORES_PERGUNTA WHERE COD_PERGUNTA = ? AND COD_VALOR = ?)", array ($codPergunta,$valor) );
	
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}

	/**
	 * Concatenar os dados para gerar log de eventos
	 * @param unknown $busca
	 * @param unknown $codigo
	 * @param string $nome
	 * @param string $codStatus
	 * @return string
	 */
	public static function concatDadosEventos ($busca,$codigo,$codQuestionario = null,$descricao = null,$codTipo = null,$codStatus = null,$ordem = null,$codObrigatorio = null) {
		global $system;
		$s		= $system->getCaracSepEvento();
			
		if ($busca == 1) {
			$info	= pergunta::getInfo($codigo);
			return ($info->CODIGO.$s.$info->COD_QUESTIONARIO.$s.$info->DESCRICAO.$s.$info->COD_TIPO.$s.$info->COD_STATUS.$s.$info->ORDEM.$s.$info->COD_OBRIGATORIO);
		}else {
			return ($codigo.$s.$codQuestionario.$s.$descricao.$s.$codTipo.$s.$codStatus.$s.$ordem.$s.$codObrigatorio);
		}
	}
	
	/**
	 * Concatenar os dados para gerar log de eventos
	 * @param unknown $busca
	 * @param unknown $codigo
	 * @param string $nome
	 * @param string $codStatus
	 * @return string
	 */
	public static function concatDadosEventosRespAux ($busca,$codPaciente,$codPergunta,$valor = null) {
		global $system;
		$s		= $system->getCaracSepEvento();
		
			
		if ($busca == 1) {
			$infoP    	= pergunta::getInfo($codPergunta);
			$resp		= questionario::getRespostaPergunta("A", $codPaciente, $codPergunta, $infoP->COD_QUESTIONARIO);
			return ($codPaciente.$s.$codPergunta.$s.$resp);
		}else {
			return ($codPaciente.$s.$codPergunta.$s.$valor);
		}
	}
	
	/**
	 * Concatenar os dados para gerar log de eventos
	 * @param unknown $busca
	 * @param unknown $codigo
	 * @param string $nome
	 * @param string $codStatus
	 * @return string
	 */
	public static function concatDadosEventosRespConsulta ($busca,$codConsulta,$codPergunta,$valor = null) {
		global $system;
		$s		= $system->getCaracSepEvento();
		
			
		if ($busca == 1) {
			$infoP    	= pergunta::getInfo($codPergunta);
			$infoC		= consulta::getInfo($codConsulta);
			$resp		= questionario::getRespostaPergunta("F", $infoC->COD_PACIENTE, $codPergunta, $infoP->COD_QUESTIONARIO,$codConsulta);
			return ($codConsulta.$s.$codPergunta.$s.$resp);
		}else {
			return ($codConsulta.$s.$codPergunta.$s.$valor);
		}
	}
	
	/**
	 * Resgatar o código do dicionário dessa tabela
	 */
	public static function getCodDicionario() {
		global $system;
	
		$info = $system->db->extraiPrimeiro("
				SELECT	CODIGO
				FROM	DICIONARIO_DADOS DD
				WHERE 	DD.NOME		= 'PERGUNTAS'
		");
	
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}

	/**
	 * Resgatar o código do dicionário dessa tabela
	 */
	public static function getCodDicionarioRespAux() {
		global $system;
	
		$info = $system->db->extraiPrimeiro("
				SELECT	CODIGO
				FROM	DICIONARIO_DADOS DD
				WHERE 	DD.NOME		= 'PACIENTE_CADASTRO_AUX'
		");
	
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}
	
	/**
	 * Resgatar o código do dicionário dessa tabela
	 */
	public static function getCodDicionarioRespConsulta() {
		global $system;
	
		$info = $system->db->extraiPrimeiro("
				SELECT	CODIGO
				FROM	DICIONARIO_DADOS DD
				WHERE 	DD.NOME		= 'RESPOSTAS_PERGUNTA'
		");
	
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}
	
	/**
	 * Salva a resposta de uma pergunta de um questionário auxiliar de cadastro
	 * @param unknown $codPaciente
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return Ambigous <string, unknown, unknown>|string|Ambigous <NULL, string>
	 */
	public static function salvaRespostaAux ($codPaciente,$codPergunta,$valor) {
		global $system;
		
		$s		= $system->getCaracSepEvento();
	
		/** Checar se a pergunta ja foi respondida **/
		if (pergunta::existeRespostaAux($codPaciente, $codPergunta) == false) {
			/** Inserir **/
			$err = pergunta::inserirRespostaAux ($codPaciente,$codPergunta,$valor);
			
			if ($err) return('Erro: '.$err);
			
			$system->geraEvento($system->getCodUsuario(),'I',pergunta::getCodDicionarioRespAux(),null,pergunta::concatDadosEventosRespAux(0,$codPaciente,$codPergunta,$valor),'CADASTRO DE RESPOSTA DE CADASTRO AUXILIAR');
			
		}else{
			/** Atualizar **/
			$system->geraEvento($system->getCodUsuario(),'U',pergunta::getCodDicionarioRespAux(),pergunta::concatDadosEventosRespAux(1,$codPaciente,$codPergunta,null),pergunta::concatDadosEventosRespAux(0,$codPaciente,$codPergunta,$valor),'ATUALIZAÇÃO DE RESPOSTA DE CADASTRO AUXILIAR');
			return (pergunta::updateRespostaAux ($codPaciente,$codPergunta,$valor));
		}
	}
	
	/**
	 * Salva a resposta de uma pergunta de um questionário de consulta
	 * @param unknown $codConsulta
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return string
	 */
	public static function salvaRespostaConsulta ($codConsulta,$codPergunta,$valor) {
		global $system;
		
		$s		= $system->getCaracSepEvento();
	
		/** Checar se a pergunta ja foi respondida **/
		if (pergunta::existeRespostaConsulta($codConsulta,$codPergunta) == false) {
			/** Inserir **/
			$err = pergunta::inserirRespostaConsulta ($codConsulta,$codPergunta,$valor);
			
			if ($err) return('Erro: '.$err);
			
			$system->geraEvento($system->getCodUsuario(),'I',pergunta::getCodDicionarioRespConsulta(),null,pergunta::concatDadosEventosRespConsulta(0,$codConsulta,$codPergunta,$valor),'CADASTRO DE RESPOSTA DE CONSULTA');
			
		}else{
			/** Atualizar **/
			$system->geraEvento($system->getCodUsuario(),'U',pergunta::getCodDicionarioRespConsulta(),pergunta::concatDadosEventosRespConsulta(1,$codConsulta,$codPergunta,null),pergunta::concatDadosEventosRespAux(0,$codConsulta,$codPergunta,$valor),'ATUALIZAÇÃO DE RESPOSTA DE CONSULTA');
			return (pergunta::updateRespostaConsulta ($codConsulta,$codPergunta,$valor));
		}
	}
	
	/**
	 * Verifica se o pergunta existe
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function existeRespostaAux ($codPaciente,$codPergunta) {
		global $system;
	
		$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	PACIENTE_CADASTRO_AUX PCA
				WHERE 	PCA.COD_PACIENTE	= '".$codPaciente."'
				AND		PCA.COD_PERGUNTA	= '".$codPergunta."'
		");
		 
		if ($info->NUM > 0) {
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Verifica se o pergunta existe
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function existeRespostaConsulta ($codConsulta,$codPergunta) {
		global $system;
	
		$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	RESPOSTAS_PERGUNTA RP
				WHERE 	RP.COD_CONSULTA		= '".$codConsulta."'
				AND		RP.COD_PERGUNTA		= '".$codPergunta."'
		");
			
		if ($info->NUM > 0) {
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Cadastrar uma resposta para um questionário do tipo auxiliar de cadastro
	 * @param unknown $codPaciente
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return string|unknown
	 */
	public static function inserirRespostaAux ($codPaciente,$codPergunta,$valor) {
		global $system;
	
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO PACIENTE_CADASTRO_AUX (COD_PACIENTE,COD_PERGUNTA,VALOR) VALUES (?,?,?)",
				array($codPaciente,$codPergunta,$valor)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			return null;
				
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}

	/**
	 * Cadastrar uma resposta para um questionário do tipo consulta
	 * @param unknown $codConsulta
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return string|unknown
	 */
	public static function inserirRespostaConsulta ($codConsulta,$codPergunta,$valor) {
		global $system;
	
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO RESPOSTAS_PERGUNTA (COD_CONSULTA,COD_PERGUNTA,VALOR) VALUES (?,?,?)",
				array($codConsulta,$codPergunta,$valor)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			return null;
				
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}
	
	
	/**
	 * Atualizar uma resposta para um questionário do tipo auxiliar de cadastro
	 * @param unknown $codPaciente
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return NULL|string
	 */
	public static function updateRespostaAux ($codPaciente,$codPergunta,$valor) {
		global $system;
	
		try {
			$system->db->con->beginTransaction();
				
			$system->db->Executa("
				UPDATE  PACIENTE_CADASTRO_AUX 
				SET		VALOR				= ?
				WHERE	COD_PACIENTE		= ?
				AND		COD_PERGUNTA		= ?",
					array($valor,$codPaciente,$codPergunta)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}
	
	/**
	 * Atualizar uma resposta para um questionário do tipo Consulta
	 * @param unknown $codConsulta
	 * @param unknown $codPergunta
	 * @param unknown $valor
	 * @return NULL|string
	 */
	public static function updateRespostaConsulta ($codConsulta,$codPergunta,$valor) {
		global $system;
	
		try {
			$system->db->con->beginTransaction();
	
			$system->db->Executa("
				UPDATE  RESPOSTAS_PERGUNTA
				SET		VALOR				= ?
				WHERE	COD_CONSULTA		= ?
				AND		COD_PERGUNTA		= ?",
					array($valor,$codConsulta,$codPergunta)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}
	
}