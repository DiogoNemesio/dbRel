<?php

/**
 * Usuario
 * 
 * @package: usuario
 * @created: 30/03/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class usuario {

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
	 * Salva um usuário
	 * @param unknown $codigo
	 * @param unknown $usuario
	 * @param unknown $nome
	 * @param unknown $senha
	 * @param unknown $codTipo
	 * @param unknown $email
	 * @param unknown $telefone
	 * @param unknown $celular
	 * @param unknown $codStatus
	 * @return string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$usuario,$nome,$senha,$codTipo,$email,$telefone,$celular,$codStatus) {
		global $system;	
		
		if ((!$codigo) && (usuario::existeUsuario($usuario) == true)) {
			return('Erro: Usuário "'.$usuario.'" já existe, escolha outro nome !!');
			//$info		= usuario::getInfo(null,$nome);
			//$codigo		= $info->codigo;
		}
		
		/** Checar se o usuario já existe **/
		if ((!$codigo) || (usuario::existe($codigo) == false) ) {

			/** Inserir **/
			$err = usuario::inserir($usuario,$nome,$senha,$codTipo,$email,$telefone,$celular,$codStatus);
			if (is_numeric($err)) {
				$codigo		= $err;
				return ($codigo);
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(usuario::update($codigo,$usuario,$nome,$codTipo,$email,$telefone,$celular,$codStatus));
		}
    }
	
    
    /**
     * Inserir o usuário 
     * @param unknown $codigo
     * @param unknown $usuario
     * @param unknown $nome
     * @param unknown $senha
     * @param unknown $codTipo
     * @param unknown $email
     * @param unknown $telefone
     * @param unknown $celular
     * @param unknown $codStatus
     * @return string|unknown
     */
    public static function inserir ($usuario,$nome,$senha,$codTipo,$email,$telefone,$celular,$codStatus) {
		global $system;
		
		if (!$email)		$email		= null;
		if (!$telefone) 	$telefone	= null;
		if (!$celular) 		$celular	= null;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO USUARIOS (CODIGO,USUARIO,NOME,SENHA,COD_TIPO,EMAIL,TELEFONE,CELULAR,COD_STATUS) VALUES (null,?,?,?,?,?,?,?,?)",
				array($usuario,$nome,$senha,$codTipo,$email,$telefone,$celular,$codStatus)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			if (!$cod) {
				return('Erro:Não foi possível resgatar o código');
			}else{
				$system->geraEvento($system->getCodUsuario(),'I',usuario::getCodDicionario(),null,usuario::concatDadosEventos(0,$cod,$usuario,$nome,$senha,$codTipo,$email,$telefone,$celular,$codStatus),'CADASTRO DE USUÁRIO');
				return($cod);
			}
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Atualizar o usuario
     * @param unknown $codigo
     * @param unknown $nome
     * @param unknown $email
     * @param unknown $codCidade
     * @param unknown $endereco
     * @param unknown $bairro
     * @return NULL|string
     */
    public static function update ($codigo,$usuario,$nome,$codTipo,$email,$telefone,$celular,$codStatus) {
		global $system;
		
		if (!$email)		$email		= null;
		if (!$telefone) 	$telefone	= null;
		if (!$celular) 		$celular	= null;
						
		try {
			$system->db->con->beginTransaction();
			
			$system->geraEvento($system->getCodUsuario(),'U',usuario::getCodDicionario(),usuario::concatDadosEventos(1,$codigo),usuario::concatDadosEventos(0,$codigo,$usuario,$nome,null,$codTipo,$email,$telefone,$celular,$codStatus),'ATUALIZAÇÃO DE USUÁRIO');
			
			$system->db->Executa("
				UPDATE 	USUARIOS 
				SET		USUARIO			= ?,
						NOME			= ?,
						COD_TIPO		= ?,
						EMAIL			= ?,
						TELEFONE		= ?,
						CELULAR			= ?,
						COD_STATUS		= ?
				WHERE	CODIGO			= ?",
				array($usuario,$nome,$codTipo,$email,$telefone,$celular,$codStatus,$codigo)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Alterar a senha do usuário
     * @param unknown $codigo
     * @param unknown $senha
     * @return NULL|string
     */
    public static function AlteraSenha ($codigo,$senha) {
    	global $system;
    
    	try {
    		$system->db->con->beginTransaction();
    		
    		$system->geraEvento($system->getCodUsuario(),'U',usuario::getCodDicionario(),usuario::concatDadosEventos(1,$codigo),usuario::concatDadosEventos(0,$codigo,null,null,$senha,null,null,null,null,null),'ALTERAÇÃO DE SENHA');
    		
    		$system->db->Executa("
				UPDATE 	USUARIOS
				SET		SENHA			= ?
				WHERE	CODIGO			= ?",
    				array($senha,$codigo)
    		);
    		$system->db->con->commit();
    		return(null);
    	} catch (Exception $e) {
    		$system->db->con->rollback();
    		return('Erro: '.$e->getMessage());
    	}
    }
    
    /**
     * Desassociar um usuário de um templo
     * @param unknown $codUsuario
     * @param unknown $codTemplo
     * @return NULL|string
     */
    public static function desassociaTemplo ($codUsuario,$codTemplo) {
    	global $system;
    
    	if (usuario::temPermissaoTemplo($codUsuario, $codTemplo) == false) return;
    	
    	try {
    		$system->db->con->beginTransaction();
    		$system->geraEvento($system->getCodUsuario(),'D',usuario::getCodDicionario(),null,usuario::concatDadosEventos(0,$codUsuario,null,null,null,null,null,null,null,null),'DESASSOCIAÇÃO DE TEMPLO ('.$codTemplo.')');
    		$system->db->Executa("DELETE FROM USUARIO_TEMPLO WHERE COD_USUARIO = ? AND COD_TEMPLO = ?"    		
				,array($codUsuario,$codTemplo)
    		);
    		$system->db->con->commit();
    		return(null);
    	} catch (Exception $e) {
    		$system->db->con->rollback();
    		return('Erro: '.$e->getMessage());
    	}
    }
    
    /**
     * Desassociar um usuário de um templo
     * @param unknown $codUsuario
     * @param unknown $codTemplo
     * @return NULL|string
     */
    public static function associaTemplo ($codUsuario,$codTemplo) {
    	global $system;
    	
    	if (usuario::temPermissaoTemplo($codUsuario, $codTemplo) == true) return;
    
    	try {
    		$system->db->con->beginTransaction();
    		$system->geraEvento($system->getCodUsuario(),'I',usuario::getCodDicionario(),usuario::concatDadosEventos(0,$codUsuario,null,null,null,null,null,null,null,null),null,'ASSOCIAÇÃO DE TEMPLO ('.$codTemplo.')');
			$system->db->Executa("INSERT INTO USUARIO_TEMPLO (COD_USUARIO,COD_TEMPLO) VALUES (?,?)",
				array($codUsuario,$codTemplo)
			);
    		$system->db->con->commit();
    		return(null);
    	} catch (Exception $e) {
    		$system->db->con->rollback();
    		return('Erro: '.$e->getMessage());
    	}
    }
    
    
    /**
	 * 
	 * Lista os usuarios
	 */
    public static function lista ($nome = null,$codTipo = null) {
		global $system;
		
		$and	= null;
		
		if ($nome 	!= null)	$and .= "AND 	U.NOME 		LIKE '%".$nome."%'";
		if ($codTipo!= null)	$and .= "AND 	U.COD_TIPO 		= '".$codTipo."'";
		
		return (
    		$system->db->extraiTodos("
				SELECT	U.*,TU.NOME TIPO_USUARIO,TS.NOME STATUS
				FROM	USUARIOS 		U,
    					TIPO_USUARIO 	TU,
    					TIPO_STATUS		TS
				WHERE	U.COD_TIPO		= TU.CODIGO
    			AND		U.COD_STATUS	= TS.CODIGO
    			AND		U.CODIGO		= CASE TU.CODIGO
    				WHEN 'A' THEN U.CODIGO
    				ELSE (SELECT 	COD_USUARIO
    					  FROM		USUARIO_TEMPLO UT
    					  WHERE		UT.COD_TEMPLO	= '".$system->getCodTemplo()."'
    					  AND		UT.COD_USUARIO	= U.CODIGO
    				) END
    			$and
				ORDER	BY U.NOME
			")
   		);
    }

    /**
     *
     * Lista os usuarios
     */
    public static function listaVoluntarios () {
    	global $system;
    
    	return (
    			$system->db->extraiTodos("
				SELECT	U.*,TU.NOME TIPO_USUARIO,TS.NOME STATUS
				FROM	USUARIOS 		U,
    					TIPO_USUARIO 	TU,
    					TIPO_STATUS		TS
				WHERE	U.COD_TIPO		= TU.CODIGO
    			AND		U.COD_STATUS	= TS.CODIGO
    			AND		U.CODIGO		= CASE TU.CODIGO
    				WHEN 'A' THEN U.CODIGO
    				ELSE (SELECT 	COD_USUARIO
    					  FROM		USUARIO_TEMPLO UT
    					  WHERE		UT.COD_TEMPLO	= '".$system->getCodTemplo()."'
    					AND		UT.COD_USUARIO	= U.CODIGO
    			) END
				AND 	U.COD_TIPO 		IN  ('V','R','P')
    					ORDER	BY U.NOME
			")
    	);
    }
    
    
    /**
     *
     * Lista os tipos de usuário
     */
    public static function listaTipos ($nome = null,$codTipo = null) {
    	global $system;
    
    	$and	= null;
    
    	if ($nome 		!= null)	$and .= "AND 	TU.NOME 	LIKE '%".$nome."%'";
    	if ($codTipo 	!= null)	$and .= "AND 	TU.CODIGO	= '".$codTipo."'";
    	 
    	return (
    			$system->db->extraiTodos("
    					SELECT	TU.*
    					FROM	TIPO_USUARIO 	TU
    					WHERE	1 = 1
    					$and
    					ORDER	BY TU.NOME
    					")
    	);
    }
    
    /**
     *
     * Lista os Status de usuário
     */
    public static function listaStatus ($nome = null,$codStatus = null) {
    	global $system;
    
    	$and	= null;
    
    	if ($nome 		!= null)	$and .= "AND 	TS.NOME 	LIKE '%".$nome."%'";
    	if ($codStatus 	!= null)	$and .= "AND 	TS.CODIGO 	= '".$nome."'";
    	 
    	return (
    			$system->db->extraiTodos("
    					SELECT	TS.*
    					FROM	TIPO_STATUS 	TS
    					WHERE	1 = 1
    					$and
    					ORDER	BY TS.NOME
    					")
    	);
    }
    
    /**
     * Verifica se o usuario existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	USUARIOS U
				WHERE 	U.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Verifica se o login do usuario já existe
     *
     * @param string nome
     * @return array
     */
    public static function existeUsuario ($usuario) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	USUARIOS U
				WHERE 	U.USUARIO		= '".$usuario."'
		");
    	 
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    
    /**
     * Verifica se o nome do usuario já existe
     *
     * @param string nome
     * @return array
     */
    public static function existeNome ($nome) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	USUARIOS U
				WHERE 	U.NOME		= '".$nome."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Resgata as informações do usuario
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo = null,$nome = null) {
		global $system;
			$and		= '';
			if ($codigo 	!= null)	$and.= "AND 	U.CODIGO 	= '".$codigo."'"; 
			if ($nome 		!= null) 	$and.= "AND 	U.NOME 		= '".$nome."'"; 
			
			if (($codigo == null) && ($nome == null)) {
				DHCErro::halt(__CLASS__ .': Erro falta de parâmetros');
			}
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	U.*,TU.NOME TIPO_USUARIO,TS.NOME STATUS
				FROM	USUARIOS 		U,
    					TIPO_USUARIO 	TU,
    					TIPO_STATUS		TS
				WHERE	U.COD_TIPO		= TU.CODIGO
    			AND		U.COD_STATUS	= TS.CODIGO
    			$and
			")
   		);	
    }

    /**
     * Lista os templos que o usuário tem permissão
     */
    public static function listaEmpresasAcesso ($codUsuario) {
    	global $system;
    
    	return (
    			$system->db->extraiTodos("
    			SELECT VV.*
    			FROM 	(
	    			SELECT V.CODIGO,V.NOME,IF(U.COD_TIPO='A',1,V.TEM_PERMISSAO) TEM_PERMISSAO
	    			FROM	(
	    				SELECT E.CODIGO,E.FANTASIA NOME,(SELECT COUNT(*) FROM USUARIO_EMPRESA UE WHERE UE.COD_EMPRESA = E.CODIGO AND UE.COD_USUARIO = '".$codUsuario."') TEM_PERMISSAO
						FROM   EMPRESAS E
	    			) V,
	    					USUARIOS U
	    			WHERE	U.CODIGO	= '".$codUsuario."'
    			) VV
    			WHERE	TEM_PERMISSAO > 0
    			ORDER	BY VV.NOME
    		")
    	);
    }
    
    /**
     * Lista permissões nos templos
     */
    public static function listaPermissoesTemplos ($codUsuario) {
    	global $system;

    	return (
    		$system->db->extraiTodos("
    			SELECT V.CODIGO,V.NOME,IF(U.COD_TIPO='A',1,V.TEM_PERMISSAO) TEM_PERMISSAO
    			FROM	(
    				SELECT T.CODIGO,T.NOME,(SELECT COUNT(*) FROM USUARIO_TEMPLO UT WHERE UT.COD_TEMPLO = T.CODIGO AND UT.COD_USUARIO = '".$codUsuario."') TEM_PERMISSAO
					FROM   TEMPLOS T
    			) V,
    					USUARIOS U
    			WHERE	U.CODIGO	= '".$codUsuario."'
    			ORDER	BY V.NOME
    		")
    	);
    }
    
    /**
     * Verifica se o usuário tem permissões nos templos
     */
    public static function temPermissaoTemplo ($codUsuario,$codTemplo) {
    	global $system;
    
    	$return	= $system->db->extraiPrimeiro("
    			SELECT V.CODIGO,V.NOME,IF(U.COD_TIPO='A',1,V.TEM_PERMISSAO) TEM_PERMISSAO
    			FROM	(
    				SELECT	T.CODIGO,T.NOME,(SELECT COUNT(*) FROM USUARIO_TEMPLO UT WHERE UT.COD_TEMPLO = T.CODIGO AND UT.COD_USUARIO = '".$codUsuario."') TEM_PERMISSAO
					FROM	TEMPLOS T
    				WHERE	T.CODIGO	= '".$codTemplo."'
    			) V,
    					USUARIOS U
    			WHERE	U.CODIGO	= '".$codUsuario."'
    			ORDER	BY V.NOME
		");
    	
    	if ($return->TEM_PERMISSAO == 0) {
    		return false;
    	}else{
    		return true;
    	}
    	
    }
    
    /**
     * Verifica se existem templos nesse usuario
     *
     * @param string codusuario
     * @return array
     */
    public static function existemTemplos ($codUsuario) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	USUARIO_TEMPLO UT
				WHERE 	UT.COD_USUARIO		= '".$codUsuario."'
		");
    	 
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    

    
    /**
     * Verifica se existem eventos para esse usuário
     *
     * @param string codusuario
     * @return array
     */
    public static function existemEventos ($codUsuario) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	LOG_EVENTOS L
				WHERE 	L.COD_USUARIO		= '".$codUsuario."'
		");
    
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
	 * Exclui o usuario
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function exclui($codigo) {
		global $system;
 
		/** Verifica se o usuario existe **/
		if (usuario::existe($codigo) == false) return ('Erro: usuario não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Verifica se existe algum evento **/
			if (usuario::existemEventos($codigo) == true) {
				return ("Erro: Usuário já realizou movimentações no sistema. Inative-o.");
			}
			
			$system->geraEvento($system->getCodUsuario(),'D',usuario::getCodDicionario(),usuario::concatDadosEventos(1,$codigo),null,'EXCLUSÃO DE USUÁRIO');
			
			/** Desassocia o usuários dos templos **/
			$system->db->Executa ("DELETE FROM USUARIO_TEMPLO WHERE COD_USUARIO = ?", array ($codigo) );
			
			/** Apaga o usuario **/ 
			$system->db->Executa ("DELETE FROM USUARIOS WHERE CODIGO = ?", array ($codigo) );
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
	 * @param string $usuario
	 * @param string $nome
	 * @param string $senha
	 * @param string $codTipo
	 * @param string $email
	 * @param string $telefone
	 * @param string $celular
	 * @param string $codStatus
	 * @return string
	 */
	public static function concatDadosEventos ($busca,$codigo,$usuario = null,$nome = null,$senha = null,$codTipo = null,$email = null,$telefone = null,$celular = null,$codStatus = null) {
		global $system;
		$s		= $system->getCaracSepEvento();
			
		if ($busca == 1) {
			$info	= usuario::getInfo($codigo);
			return ($info->CODIGO.$s.$info->USUARIO.$s.$info->NOME.$s.$info->SENHA.$s.$info->COD_TIPO.$s.$info->EMAIL.$s.$info->TELEFONE.$info->CELULAR.$s.$info->COD_STATUS);
		}else {
			return ($codigo.$s.$usuario.$s.$nome.$s.$senha.$s.$codTipo.$s.$email.$s.$telefone.$s.$celular.$s.$codStatus);
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
				WHERE 	DD.NOME		= 'USUARIOS'
		");
	
		if (isset($info->CODIGO)) {
			return $info->CODIGO;
		}else{
			DHCErro::halt('Código do dicionário não encontrado !!!');
		}
	}
	
	
}