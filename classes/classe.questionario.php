<?php

/**
 * Questionario
 * 
 * @package: questionario
 * @created: 05/04/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class questionario {

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
	 * Salva um questionario
	 * @param unknown $codigo
	 * @param unknown $nome
	 * @param unknown $codStatus
	 * @return string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$nome,$codStatus,$codTipo) {
		global $system;
		
		if ((!$codigo) && (questionario::existeNome($nome) == true)) {
			$info		= questionario::getInfo(null,$nome);
			$codigo		= $info->CODIGO;
		}
		
		/** Checar se o questionario já existe **/
		if ((!$codigo) || (questionario::existe($codigo) == false) ) {

			/** Inserir **/
			$err = questionario::inserir($nome,$codStatus,$codTipo);
			if (is_numeric($err)) {
				$codigo		= $err;
				return ($codigo);
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(questionario::update($codigo,$nome,$codStatus,$codTipo));
		}
    }
	
    
    /**
     * Inserir o questionário
     * @param unknown $codigo
     * @param unknown $nome
     * @param unknown $codStatus
     * @return string|unknown
     */
    public static function inserir ($nome,$codStatus,$codTipo) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO QUESTIONARIOS (CODIGO,NOME,COD_STATUS,COD_TIPO) VALUES (null,?,?,?)",
				array($nome,$codStatus,$codTipo)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			if (!$cod) {
				return('Erro:Não foi possível resgatar o código');
			}else{
				$system->geraEvento($system->getCodUsuario(),'I',questionario::getCodDicionario(),null,questionario::concatDadosEventos(0,$cod,$nome,$codStatus,$codTipo),'CADASTRO DE QUESTIONÁRIO');
				return($cod);
			}
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Atualizar o Questionário 
     * @param unknown $codigo
     * @param unknown $nome
     * @param unknown $codStatus
     * @return NULL|string
     */
    public static function update ($codigo,$nome,$codStatus,$codTipo) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			
			$system->geraEvento($system->getCodUsuario(),'U',questionario::getCodDicionario(),questionario::concatDadosEventos(1,$codigo),questionario::concatDadosEventos(0,$codigo,$nome,$codStatus,$codTipo),'ALTERAÇÃO DE QUESTIONÁRIO');
			
			$system->db->Executa("
				UPDATE  QUESTIONARIOS 
				SET		NOME			= ?,
						COD_STATUS		= ?,
						COD_TIPO		= ?
				WHERE	CODIGO			= ?",
				array($nome,$codStatus,$codTipo,$codigo)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Lista os questionarios
     * @param string $nome
     * @param string $codTipo
     * @param string $codStatus
     */
    public static function lista ($nome = null,$codTipo =  null,$codStatus = null) {
		global $system;
		
		$and	= "";
		
		if ($nome 		!= null)	$and .= "AND 	Q.NOME 			LIKE '%".$nome."%'";
		if ($codTipo 	!= null)	$and .= "AND 	Q.COD_TIPO 		= '".$codTipo."'";
		if ($codStatus	!= null)	$and .= "AND 	Q.COD_STATUS	= '".$codStatus."'";
		
		return (
    		$system->db->extraiTodos("
				SELECT	Q.*,TS.NOME STATUS,TQ.NOME TIPO
				FROM	QUESTIONARIOS 		Q,
    					TIPO_STATUS 		TS,
    					TIPO_QUESTIONARIO	TQ
				WHERE	Q.COD_STATUS	= TS.CODIGO
    			AND		Q.COD_TIPO		= TQ.CODIGO
    			$and
				ORDER	BY Q.NOME
			")
   		);
    }

   
    /**
     * Lista as perguntas Ativas do Questionario
     */
    public static function listaPerguntasAtivas ($codQuestionario) {
    	global $system;
    	return (
    		$system->db->extraiTodos("
    			SELECT	P.*,TP.NOME TIPO,TS.NOME STATUS
    			FROM	PERGUNTAS 		P,
    					TIPO_PERGUNTA	TP,
    					TIPO_STATUS		TS
    			WHERE	P.COD_TIPO			= TP.CODIGO
    			AND		P.COD_STATUS		= TS.CODIGO
    			AND		P.COD_QUESTIONARIO	= '".$codQuestionario."'
    			AND		P.COD_STATUS		= 'A'
    			ORDER	BY P.ORDEM
    		")
    	);
    }
    
    /**
     * Lista os tipos de questionários
     */
    public static function listaTipos () {
    	global $system;
    	return (
    			$system->db->extraiTodos("
    			SELECT	*
    			FROM	TIPO_QUESTIONARIO	TQ
    			ORDER	BY TQ.NOME
    		")
    	);
    }
    
    /**
     * Verifica se o questionario existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	QUESTIONARIOS Q
				WHERE 	Q.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Verifica se o nome do questionario já existe
     *
     * @param string nome
     * @return array
     */
    public static function existeNome ($nome) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	QUESTIONARIOS Q
				WHERE 	Q.NOME		= '".$nome."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Resgata as informações do questionario
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo = null,$nome = null) {
		global $system;
			$and		= '';
			if ($codigo 	!= null)	$and.= "AND 	Q.CODIGO 	= '".$codigo."'"; 
			if ($nome 		!= null) 	$and.= "AND 	Q.NOME 		= '".$nome."'"; 
			
			if (($codigo == null) && ($nome == null)) {
				DHCErro::halt(__CLASS__ .': Erro falta de parâmetros');
			}
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	Q.*,TS.NOME STATUS,TQ.NOME TIPO
				FROM	QUESTIONARIOS 		Q,
    					TIPO_STATUS 		TS,
    					TIPO_QUESTIONARIO	TQ
				WHERE	Q.COD_STATUS	= TS.CODIGO
    			AND		Q.COD_TIPO		= TQ.CODIGO
				$and
			")
   		);	
    }


    /**
     * Verifica se existem perguntas para questionario
     *
     * @param string codquestionario
     * @return array
     */
    public static function existemPerguntas ($codQuestionario) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	PERGUNTAS P
				WHERE 	P.COD_QUESTIONARIO		= '".$codQuestionario."'
		");
    	 
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }


    /**
     * 
     * @param unknown $codTipoQuest
     * @param unknown $codPaciente
     * @param unknown $codPergunta
     * @param string $codQuestionario
     * @param string $codConsulta
     * @return boolean
     */
    public static function getRespostaPergunta ($codTipoQuest,$codPaciente,$codPergunta,$codQuestionario, $codConsulta = null) {
    	global $system;
    
    	if ($codTipoQuest == "A") {
    		$resp = $system->db->extraiPrimeiro("
				SELECT	PCA.VALOR
				FROM	PACIENTE_CADASTRO_AUX 	PCA,
    					PERGUNTAS				P
				WHERE 	PCA.COD_PERGUNTA		= P.CODIGO
    			AND		P.COD_QUESTIONARIO		= '".$codQuestionario."'
    			AND		PCA.COD_PACIENTE		= '".$codPaciente."'
    			AND		PCA.COD_PERGUNTA		= '".$codPergunta."'			
			");
    		
    	}else{
    		if ($codConsulta == null) DHCErro::halt('Falta de Parâmetros (COD_CONSULTA)');
    		$resp = $system->db->extraiPrimeiro("
				SELECT	RP.VALOR
				FROM	RESPOSTAS_PERGUNTA		RP,
    					PERGUNTAS				P,
    					CONSULTAS				C
				WHERE 	RP.COD_PERGUNTA			= P.CODIGO
    			AND		RP.COD_CONSULTA			= C.CODIGO
    			AND		P.COD_QUESTIONARIO		= '".$codQuestionario."'
    			AND		RP.COD_CONSULTA			= '".$codConsulta."'
    			AND		C.COD_PACIENTE			= '".$codPaciente."'
    			AND		RP.COD_PERGUNTA			= '".$codPergunta."'				
			");
    	}
    
    	if (isset($resp->VALOR)) {
    		return $resp->VALOR;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Verifica se existem resposta para o questionario
     *
     * @param string codquestionario
     * @return array
     */
    public static function existemRespostas ($codQuestionario) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	RESPOSTAS_PERGUNTA RP
				WHERE 	RP.COD_PERGUNTA IN (
					SELECT 	COD_PERGUNTA
    				FROM	PERGUNTAS
    				WHERE	COD_QUESTIONARIO		= '".$codQuestionario."'
    			)
		");
    
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
	/**
	 * Exclui o questionario
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function exclui($codigo) {
		global $system;
 
		/** Verifica se o questionario existe **/
		if (questionario::existe($codigo) == false) return ('Erro: questionario não existe');
		
		try {
			$system->db->con->beginTransaction ();
			if (questionario::existemRespostas($codigo) == true) {
				return ('Erro: Esse questionário já foi respondido, inative o questionário.');
			}
			
			$system->geraEvento($system->getCodUsuario(),'D',questionario::getCodDicionario(),questionario::concatDadosEventos(1,$codigo),null,'EXCLUSÃO DE QUESTIONÁRIO');
			/** Exclui os valores das perguntas **/
			$system->db->Executa ("DELETE FROM VALORES_PERGUNTA WHERE COD_PERGUNTA IN (SELECT COD_PERGUNTA FROM PERGUNTAS WHERE COD_QUESTIONARIO = ?)", array ($codigo) );
			/** Exclui as perguntas **/
			$system->db->Executa ("DELETE FROM PERGUNTAS WHERE COD_QUESTIONARIO = ?", array ($codigo) );
			/** Apaga o questionario **/ 
			$system->db->Executa ("DELETE FROM QUESTIONARIOS WHERE CODIGO = ?", array ($codigo) );
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
	public static function concatDadosEventos ($busca,$codigo,$nome = null,$codStatus = null,$codTipo = null) {
		global $system;
		$s		= $system->getCaracSepEvento();
			
		if ($busca == 1) {
			$info	= questionario::getInfo($codigo);
			return ($info->CODIGO.$s.$info->NOME.$s.$info->COD_STATUS.$s.$info->COD_TIPO);
		}else {
			return ($codigo.$s.$nome.$s.$codStatus.$s.$codTipo);
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
				WHERE 	DD.NOME		= 'QUESTIONARIOS'
		");
	
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}
	
	
	
}