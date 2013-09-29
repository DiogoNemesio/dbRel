<?php

/**
 * Templo
 * 
 * @package: templo
 * @created: 23/03/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class templo {

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
	 * Salva um templo
	 * @param unknown $codigo
	 * @param unknown $nome
	 * @param unknown $email
	 * @param unknown $codCidade
	 * @param unknown $endereco
	 * @param unknown $bairro
	 * @return string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$nome,$email,$codCidade,$endereco,$bairro) {
		global $system;
		
		if ((!$codigo) && (templo::existeNome($nome) == true)) {
			$info		= templo::getInfo(null,$nome);
			$codigo		= $info->CODIGO;
		}
		
		/** Checar se o templo já existe **/
		if ((!$codigo) || (templo::existe($codigo) == false) ) {

			/** Inserir **/
			$err = templo::inserir($nome,$email,$codCidade,$endereco,$bairro);
			if (is_numeric($err)) {
				$codigo		= $err;
				return ($codigo);
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(templo::update($codigo,$nome,$email,$codCidade,$endereco,$bairro));
		}
    }
	
    
    /**
     * Inserir o templo 
     * @param unknown $nome
     * @param unknown $email
     * @param unknown $codCidade
     * @param unknown $endereco
     * @param unknown $bairro
     * @return string|unknown
     */
    public static function inserir ($nome,$email,$codCidade,$endereco,$bairro) {
		global $system;
		
		if (!$email)		$email		= null;
		if (!$endereco) 	$endereco	= null;
		if (!$bairro) 		$bairro		= null;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO TEMPLOS (CODIGO,NOME,EMAIL,COD_CIDADE,ENDERECO,BAIRRO) VALUES (null,?,?,?,?,?)",
				array($nome,$email,$codCidade,$endereco,$bairro)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			$system->geraEvento($system->getCodUsuario(),'I',templo::getCodDicionario(),null,templo::concatDadosEventos(0,null, $nome, $email, $codCidade, $endereco, $bairro));
				
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
     * Atualizar o templo
     * @param unknown $codigo
     * @param unknown $nome
     * @param unknown $email
     * @param unknown $codCidade
     * @param unknown $endereco
     * @param unknown $bairro
     * @return NULL|string
     */
    public static function update ($codigo,$nome,$email,$codCidade,$endereco,$bairro) {
		global $system;
		
		if (!$email)		$email		= null;
		if (!$endereco) 	$endereco	= null;
		if (!$bairro) 		$bairro		= null;
				
		try {
			
			$system->db->con->beginTransaction();
			
			$system->geraEvento($system->getCodUsuario(),'U',templo::getCodDicionario(),templo::concatDadosEventos(1,$codigo),templo::concatDadosEventos(0,$codigo, $nome, $email, $codCidade, $endereco, $bairro));
				
			$system->db->Executa("
				UPDATE 	TEMPLOS 
				SET		NOME			= ?,
						EMAIL			= ?,
						COD_CIDADE		= ?,
						ENDERECO		= ?,
						BAIRRO			= ?
				WHERE	CODIGO			= ?",
				array($nome,$email,$codCidade,$endereco,$bairro,$codigo)
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
     * @param string $nome
     * @param string $email
     * @param string $codCidade
     * @param string $endereco
     * @param string $bairro
     * @return string
     */
    public static function concatDadosEventos ($busca,$codigo,$nome = null,$email = null,$codCidade = null,$endereco = null ,$bairro = null) {
    	global $system;
    	$s		= $system->getCaracSepEvento();
    	
    	if ($busca == 1) {
    		$info	= templo::getInfo($codigo);
    		return ($info->CODIGO.$s.$info->NOME.$s.$info->EMAIL.$s.$info->COD_CIDADE.$s.$info->ENDERECO.$s.$info->BAIRRO);
    	}else {
    	   	return ($codigo.$s.$nome.$s.$email.$s.$codCidade.$s.$endereco.$s.$bairro);
    	}
    }
    
	/**
	 * 
	 * Lista os templos
	 */
    public static function lista ($nome = null) {
		global $system;
		
		$and	= null;
		
		if ($nome != null)	$and = "AND 	NOME 	LIKE '%".$nome."%'";    	
		
		return (
    		$system->db->extraiTodos("
				SELECT	T.*,C.NOME CIDADE
				FROM	TEMPLOS T,
    					CIDADES	C
				WHERE	T.COD_CIDADE	= C.CODIGO
    			$and
				ORDER	BY T.NOME
			")
   		);
    }

    
    /**
     * Verifica se o templo existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	TEMPLOS T
				WHERE 	T.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Verifica se o nome do templo já existe
     *
     * @param string nome
     * @return array
     */
    public static function existeNome ($nome) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	TEMPLOS T
				WHERE 	T.NOME		= '".$nome."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Resgata as informações do templo
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo = null,$nome = null) {
		global $system;
			$and		= '';
			if ($codigo 	!= null)	$and.= "AND 	T.CODIGO 	= '".$codigo."'"; 
			if ($nome 		!= null) 	$and.= "AND 	T.NOME 		= '".$nome."'"; 
			
			if (($codigo == null) && ($nome == null)) {
				DHCErro::halt(__CLASS__ .': Erro falta de parâmetros');
			}
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	T.*,C.NOME CIDADE
				FROM	TEMPLOS T,
    					CIDADES	C
				WHERE	T.COD_CIDADE	= C.CODIGO
    			$and
			")
   		);	
    }


    /**
     * Verifica se existem usuários nesse templo
     *
     * @param string codTemplo
     * @return array
     */
    public static function existemUsuarios ($codTemplo) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	USUARIO_TEMPLO UT
				WHERE 	UT.COD_TEMPLO		= '".$codTemplo."'
		");
    	 
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Verifica se existem Pacientes nesse templo
     *
     * @param string codTemplo
     * @return array
     */
    public static function existemPacientes ($codTemplo) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	PACIENTE_TEMPLO PT
				WHERE 	PT.COD_TEMPLO		= '".$codTemplo."'
		");
    
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
	/**
	 * Exclui o templo
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function exclui($codigo) {
		global $system;
 
		/** Verifica se o templo existe **/
		if (templo::existe($codigo) == false) return ('Erro: Templo não existe');
		
		try {
			$system->db->con->beginTransaction ();

			/** Verifica se existem registros filhos **/
			if (templo::existemPacientes($codigo)) {
				return ('Erro: Existem pacientes cadastrados nesse templo, exclua primeiro os pacientes !!!');
			}
			
			$system->geraEvento($system->getCodUsuario(),'D',templo::getCodDicionario(),templo::concatDadosEventos(1,$codigo),null);
			
			/** Desassocia os usuários **/
			$system->db->Executa ("DELETE FROM USUARIO_TEMPLO WHERE COD_TEMPLO = ?", array ($codigo) );
				
			/** Apaga o templo **/ 
			$system->db->Executa ("DELETE FROM TEMPLOS WHERE CODIGO = ?", array ($codigo) );
			$system->db->con->commit ();
			
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
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
				WHERE 	DD.NOME		= 'TEMPLOS'
		");
		
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}
}