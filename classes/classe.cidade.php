<?php

/**
 * Cidade
 * 
 * @package: cidade
 * @created: 01/04/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class cidade {

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
	 * Salvar a cidade
	 * @param unknown $codigo
	 * @param unknown $uf
	 * @param unknown $nome
	 * @return string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$uf,$nome) {
		global $system;
		
		if ((!$codigo) && (cidade::existeNome($nome) == true)) {
			$info		= cidade::getInfo(null,$nome);
			$codigo		= $info->CODIGO;
		}
		
		/** Checar se a cidade já existe **/
		if ((!$codigo) || (cidade::existe($codigo) == false) ) {

			/** Inserir **/
			$err = cidade::inserir ($uf,$nome);
			if (is_numeric($err)) {
				$codigo		= $err;
				return($codigo);
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(cidade::update($codigo,$uf,$nome));
		}
    }
	
    
    /**
     * Inserir a cidade
     * @param unknown $uf
     * @param unknown $nome
     * @return string|unknown
     */
    public static function inserir ($uf,$nome) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO CIDADES (CODIGO,COD_UF,NOME) VALUES (null,?,?)",
				array($uf,$nome)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
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
     * Atualizar a cidade 
     * @param unknown $codigo
     * @param unknown $uf
     * @param unknown $nome
     * @return NULL|string
     */
    public static function update ($codigo,$uf,$nome) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE 	CIDADES 
				SET		COD_UF			= ?,
						NOME			= ?
				WHERE	CODIGO			= ?",
				array($uf,$nome,$codigo)
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
	 * Lista as cidades
	 */
    public static function lista ($nome = null) {
		global $system;
		
		$and	= null;
		
		if ($nome != null)	$and = "AND 	C.NOME 	LIKE '%".$nome."%'";    	
		
		return (
    		$system->db->extraiTodos("
				SELECT	C.*,E.NOME UF,CONCAT(C.COD_UF,' / ',C.NOME) UF_CIDADE
				FROM	CIDADES	C,
    					ESTADOS E
				WHERE	C.COD_UF	= E.COD_UF
    			$and
				ORDER	BY C.COD_UF,C.NOME
			")
   		);
    }

    /**
     *
     * Busca cidades
     */
    public static function busca ($string = null) {
    	global $system;
    
    	$and	= null;
    
    	if ($string != null)	$and = "AND 	CONCAT(UPPER(C.COD_UF),' / ',UPPER(C.NOME)) LIKE UPPER('%".$string."%')";
    
    	return (
    			$system->db->extraiTodos("
    					SELECT	C.*,E.NOME UF,CONCAT(C.COD_UF,' / ',C.NOME) UF_CIDADE
    					FROM	CIDADES	C,
    					ESTADOS E
    					WHERE	C.COD_UF	= E.COD_UF
    					$and
    					ORDER	BY C.COD_UF,C.NOME
    			")
    	);
    }
    
    /**
     * Verifica se a cidade existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	CIDADES C
				WHERE 	C.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Verifica se o nome da cidade já existe
     *
     * @param string nome
     * @return array
     */
    public static function existeNome ($nome) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	CIDADES C
				WHERE 	C.NOME		= '".$nome."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Verifica se a cidade existe
     *
     * @param string nome
     * @return array
     */
    public static function existeCidade ($uf,$nome) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	C.*,E.NOME UF,CONCAT(C.COD_UF,' / ',C.NOME) UF_CIDADE
				FROM	CIDADES	C,
    					ESTADOS E
				WHERE	C.COD_UF	= E.COD_UF
				AND		C.COD_UF	= '".$uf."'
    			AND		C.NOME		= '".$nome."'
		");
    	
    	if (isset($info->NOME)) {
    		return $info;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Resgata as informações do cidade
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo = null,$nome = null,$uf = null) {
		global $system;
		$and			= '';
		if ($codigo 	!= null)	$and	.= "AND 	C.CODIGO 	= '".$codigo."'"; 
		if ($nome 		!= null) 	$and	.= "AND 	C.NOME 		= '".$nome."'"; 
		if ($uf 		!= null) 	$and	.= "AND 	C.COD_UF	= '".$uf."'";
		
		if (($codigo == null) && ($nome == null)) {
			DHCErro::halt(__CLASS__ .': Erro falta de parâmetros');
		}
		$system->log->debug->debug("5.1: Codigo:".$codigo." Nome: ".$nome." UF: ".$uf);			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	C.*,E.NOME UF,CONCAT(C.COD_UF,' / ',C.NOME) UF_CIDADE
				FROM	CIDADES	C,
    					ESTADOS E
				WHERE	C.COD_UF	= E.COD_UF
    			$and
				ORDER	BY C.COD_UF,C.NOME
			")
   		);	
    }


    /**
     * Verifica se existem TEMPLOS nesse cidade
     *
     * @param string codcidade
     * @return array
     */
    public static function existemTemplos ($codCidade) {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	TEMPLOS T
				WHERE 	T.COD_CIDADE		= '".$codCidade."'
		");
    	 
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    
	/**
	 * Exclui a cidade
	 *
	 * @param integer $codigo
	 * @return array
	 */
	public static function exclui($codigo) {
		global $system;
 
		/** Verifica se o cidade existe **/
		if (cidade::existe($codigo) == false) return ('Erro: cidade não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga a cidade **/ 
			$system->db->Executa ("DELETE FROM CIDADES WHERE CODIGO = ?", array ($codigo) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
}