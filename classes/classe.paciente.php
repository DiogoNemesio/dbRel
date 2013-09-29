<?php

/**
 * Paciente
 * 
 * @package: paciente
 * @created: 06/04/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class paciente {

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
	 * Salvar um paciente
	 * @param unknown $codigo
	 * @param unknown $nome
	 * @param unknown $sexo
	 * @param unknown $fone
	 * @param unknown $celular
	 * @param unknown $dataNasc
	 * @param unknown $dataCad
	 * @param unknown $profissao
	 * @param unknown $codCidade
	 * @param unknown $endereco
	 * @param unknown $bairro
	 * @return Ambigous <string, unknown, unknown>|string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$nome,$sexo,$email,$fone,$celular,$dataNasc,$dataCad,$profissao,$codCidade,$endereco,$bairro) {
		global $system;
		
		/** Checar se o paciente já existe **/
		if ((!$codigo) || (paciente::existe($codigo) == false) ) {

			/** Inserir **/
			$err = paciente::inserir($nome,$sexo,$email,$fone,$celular,$dataNasc,$dataCad,$profissao,$codCidade,$endereco,$bairro);
			if (is_numeric($err)) {
				$codigo		= $err;
				return ($codigo);
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(paciente::update($codigo,$nome,$sexo,$email,$fone,$celular,$dataNasc,$dataCad,$profissao,$codCidade,$endereco,$bairro));
		}
    }
	
    
    /**
     * Inserir o paciente
     * @param unknown $nome
     * @param unknown $sexo
     * @param unknown $fone
     * @param unknown $celular
     * @param unknown $dataNasc
     * @param unknown $dataCad
     * @param unknown $profissao
     * @param unknown $codCidade
     * @param unknown $endereco
     * @param unknown $bairro
     * @return string|unknown
     */
    public static function inserir ($nome,$sexo,$email,$fone,$celular,$dataNasc,$dataCad,$profissao,$codCidade,$endereco,$bairro) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			
			$system->db->Executa("INSERT INTO PACIENTES (CODIGO,NOME,COD_SEXO,EMAIL,TELEFONE,CELULAR,DATA_NASC,DATA_CAD,PROFISSAO,COD_CIDADE,ENDERECO,BAIRRO) VALUES (null,?,?,?,?,?,STR_TO_DATE(?,'%d/%m/%Y'),STR_TO_DATE(?,'%d/%m/%Y'),?,?,?,?)",
				array($nome,$sexo,$email,$fone,$celular,$dataNasc,date('d/m/Y'),$profissao,$codCidade,$endereco,$bairro)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
				
			if (!$cod) {
				return('Erro:Não foi possível resgatar o código');
			}else{
				$system->geraEvento($system->getCodUsuario(),'I',paciente::getCodDicionario(),null,paciente::concatDadosEventos(0,$cod,$nome,$sexo,$email,$fone,$celular,$dataNasc,$dataCad,$profissao,$codCidade,$endereco,$bairro),'CADASTRO DE PACIENTE');
				
				/** Associa o Paciente ao templo **/
				$system->db->Executa("INSERT INTO PACIENTE_TEMPLO (COD_PACIENTE,COD_TEMPLO) VALUES (?,?)",
					array($cod,$system->getCodTemplo())
				);
				return($cod);
			}
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Atualiza o paciente
     * @param unknown $codigo
     * @param unknown $nome
     * @param unknown $sexo
     * @param unknown $fone
     * @param unknown $celular
     * @param unknown $dataNasc
     * @param unknown $dataCad
     * @param unknown $profissao
     * @param unknown $codCidade
     * @param unknown $endereco
     * @param unknown $bairro
     * @return NULL|string
     */
    public static function update ($codigo,$nome,$sexo,$email,$fone,$celular,$dataNasc,$dataCad,$profissao,$codCidade,$endereco,$bairro) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			
			$system->geraEvento($system->getCodUsuario(),'U',paciente::getCodDicionario(),paciente::concatDadosEventos(1,$codigo),paciente::concatDadosEventos(0,$codigo,$nome,$sexo,$email,$fone,$celular,$dataNasc,$dataCad,$profissao,$codCidade,$endereco,$bairro),'ATUALIZAÇÃO DE PACIENTE');
			
			$system->db->Executa("
				UPDATE  PACIENTES  
				SET		NOME			= ?,
						COD_SEXO		= ?,
						EMAIL			= ?,
						TELEFONE		= ?,
						CELULAR			= ?,
						DATA_NASC		= STR_TO_DATE(?,'%d/%m/%Y'),
						PROFISSAO		= ?,
						COD_CIDADE		= ?,
						ENDERECO		= ?,
						BAIRRO			= ?
				WHERE	CODIGO			= ?",
				array($nome,$sexo,$email,$fone,$celular,$dataNasc,$profissao,$codCidade,$endereco,$bairro,$codigo)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

	/**
	 * Lista os pacientes
	 */
    public static function lista ($nome = null) {
		global $system;
		
		$and	= null;
		
		if ($nome != null)	$and = "AND 	P.NOME	LIKE '%".$nome."%'";    	
		
		return (
    		$system->db->extraiTodos("
				SELECT	P.*,TS.NOME SEXO,DATE_FORMAT(P.DATA_NASC,'%d/%m/%Y') NASCIMENTO,C.NOME CIDADE,YEAR(FROM_DAYS(DATEDIFF(CURDATE(),P.DATA_NASC))) AS IDADE 
				FROM	PACIENTES		P,
    					TIPO_SEXO		TS,
    					CIDADES			C,
    					PACIENTE_TEMPLO	PT
				WHERE	P.COD_SEXO		= TS.CODIGO
    			AND		P.COD_CIDADE	= C.CODIGO
    			AND		P.CODIGO		= PT.COD_PACIENTE
    			AND		PT.COD_TEMPLO	= '".$system->getCodTemplo()."'
    			$and
				ORDER	BY P.NOME
			")
   		);
    }

	/**
	 * Buscar
	 */
    public static function busca ($nome = null,$codCidade = null) {
		global $system;
		
		$and	= null;
		
		if ($nome 		!= null)	$and = "AND 	P.NOME			LIKE '%".$nome."%'";    	
		if ($codCidade 	!= null)	$and = "AND 	P.COD_CIDADE	= '".$codCidade."'";
		
		return (
    		$system->db->extraiTodos("
				SELECT	P.*,TS.NOME SEXO,DATE_FORMAT(P.DATA_NASC,'%d/%m/%Y') NASCIMENTO,C.NOME CIDADE,YEAR(FROM_DAYS(DATEDIFF(CURDATE(),P.DATA_NASC))) AS IDADE 
				FROM	PACIENTES		P,
    					TIPO_SEXO		TS,
    					CIDADES			C,
    					PACIENTE_TEMPLO	PT
				WHERE	P.COD_SEXO		= TS.CODIGO
    			AND		P.COD_CIDADE	= C.CODIGO
    			AND		P.CODIGO		= PT.COD_PACIENTE
    			AND		PT.COD_TEMPLO	= '".$system->getCodTemplo()."'
    			$and
				ORDER	BY P.NOME
			")
   		);
    }
    
    /**
     * Lista os sexos
     */
    public static function listaSexos() {
    	global $system;
    	return (
    		$system->db->extraiTodos("
    			SELECT	*
    			FROM	TIPO_SEXO	TS
    			ORDER	BY TS.NOME
    		")
    	);
    }
    
    /**
     * Verifica se o paciente existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	PACIENTES	P
				WHERE 	P.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Resgatas os Questionarios Auxiliares respondidos
     *
     * @param integer $codPaciente
     * @return array
     */
    public static function getQuestionariosAuxRespondidos($codPaciente) {
    	global $system;
    
    	return($system->db->extraiTodos("
    			SELECT Q.*
    			FROM	(
					SELECT	DISTINCT P.COD_QUESTIONARIO
					FROM	PACIENTE_CADASTRO_AUX 	PCA,
	    					PERGUNTAS				P
					WHERE	PCA.COD_PERGUNTA		= P.CODIGO
	    			AND		PCA.COD_PACIENTE		= '".$codPaciente."'
    			) 	VQ,
    				QUESTIONARIOS Q
    			WHERE	VQ.COD_QUESTIONARIO		= Q.CODIGO
		"));
    }
    
    /**
     * Resgatas os Questionarios de Consulta respondidos
     *
     * @param integer $codPaciente
     * @return array
     */
    public static function getQuestionariosConsultaRespondidos($codConsulta) {
    	global $system;
    
    	return($system->db->extraiTodos("
    			SELECT Q.*
    			FROM	(
					SELECT	DISTINCT P.COD_QUESTIONARIO
					FROM	RESPOSTAS_PERGUNTA 		RP,
	    					PERGUNTAS				P
					WHERE	RP.COD_PERGUNTA			= P.CODIGO
	    			AND		RP.COD_CONSULTA			= '".$codConsulta."'
    			) 	VQ,
    				QUESTIONARIOS Q
    			WHERE	VQ.COD_QUESTIONARIO		= Q.CODIGO
		"));
    }
    
    /**
     * Resgata as informações do paciente
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo) {
		global $system;

    	return (
    		$system->db->extraiPrimeiro("
				SELECT	P.*,TS.NOME SEXO,DATE_FORMAT(P.DATA_NASC,'%d/%m/%Y') NASCIMENTO,C.NOME CIDADE,YEAR(FROM_DAYS(DATEDIFF(CURDATE(),P.DATA_NASC))) AS IDADE
				FROM	PACIENTES		P,
    					TIPO_SEXO		TS,
    					CIDADES			C
				WHERE	P.COD_SEXO		= TS.CODIGO
    			AND		P.COD_CIDADE	= C.CODIGO
    			AND		P.CODIGO		= '".$codigo."'
			")
   		);	
    }


    /**
     * Verifica se existem Consultas para esse paciente
     *
     * @param string codPaciente
     * @return array
     */
    public static function existemConsultas($codPaciente) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	CONSULTAS C
			WHERE 	C.COD_PACIENTE		= '".$codPaciente."'
		");
    	 
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
	/**
	 * Exclui a paciente
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function exclui($codigo) {
		global $system;
 
		/** Verifica se o paciente existe **/
		if (paciente::existe($codigo) == false) return ('Erro: paciente não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			$system->geraEvento($system->getCodUsuario(),'U',paciente::getCodDicionario(),paciente::concatDadosEventos(1,$codigo),null,'EXCLUSÃO DE PACIENTE');
			
			if (paciente::existemConsultas($codigo) == true) {
				return ("Erro: Essa paciente já teve consultas, portanto não pode ser excluído !!");
			}
			
			/** Exclui os o cadastro auxiliar  **/
			$system->db->Executa ("DELETE FROM PACIENTE_CADASTRO_AUX WHERE COD_PACIENTE = ?", array ($codigo) );
				
			/** Desassocia do templo **/
			$system->db->Executa ("DELETE FROM PACIENTE_TEMPLO WHERE COD_PACIENTE = ?", array ($codigo) );
			
			/** Exclui as pacientes **/
			$system->db->Executa ("DELETE FROM PACIENTES WHERE CODIGO = ?", array ($codigo) );
				
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
	 * @param string $email
	 * @param string $codCidade
	 * @param string $endereco
	 * @param string $bairro
	 * @return string
	 */
	public static function concatDadosEventos ($busca,$codigo,$nome = null,$sexo = null,$email = null,$fone = null,$celular = null,$dataNasc = null,$dataCad = null,$profissao = null,$codCidade = null,$endereco = null,$bairro = null) {
		global $system;
		$s		= $system->getCaracSepEvento();
		 
		if ($busca == 1) {
			$info	= paciente::getInfo($codigo);
			return ($info->CODIGO.$s.$info->NOME.$s.$info->COD_SEXO.$s.$info->EMAIL.$s.$info->TELEFONE.$s.$info->CELULAR.$s.$info->DATA_NASC.$info->DATA_CAD.$s.$info->PROFISSAO.$s.$info->COD_CIDADE.$s.$info->ENDERECO.$s.$info->BAIRRO);
		}else {
			return ($codigo.$s.$nome.$s.$sexo.$s.$email.$s.$fone.$s.$celular.$s.$dataNasc.$s.$dataCad.$s.$profissao.$s.$codCidade.$s.$endereco.$s.$bairro);
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
				WHERE 	DD.NOME		= 'PACIENTES'
		");
	
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}
	
	/**
	 * Atualiza o paciente
	 * @param unknown $codigo
	 * @param unknown $nome
	 * @param unknown $sexo
	 * @param unknown $fone
	 * @param unknown $celular
	 * @param unknown $dataNasc
	 * @param unknown $dataCad
	 * @param unknown $profissao
	 * @param unknown $codCidade
	 * @param unknown $endereco
	 * @param unknown $bairro
	 * @return NULL|string
	 */
	public static function alteraFoto($codigo,$foto) {
		global $system;
	
		try {
			$system->db->con->beginTransaction();
				
			$system->geraEvento($system->getCodUsuario(),'U',paciente::getCodDicionario(),paciente::concatDadosEventos(1,$codigo),paciente::concatDadosEventos(0,$codigo,null,null,null,null,null,null,null,null,null,null,null),'ALTERAÇÃO DE FOTO');
				
			$system->db->Executa("
				UPDATE  PACIENTES
				SET		FOTO			= ?
				WHERE	CODIGO			= ?",
					array(DHCUtil::getConteudoArquivo($foto),$codigo)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
	}
}