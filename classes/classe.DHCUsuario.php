<?php

/**
 * Gerenciamento de usuários
 * 
 * @package: DHCUsuario
 * @created: 29/10/2008
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class DHCUsuario {

	/**
	 * código do usuário
	 */
	private $codUsuario;
	
	/**
	 * usuário
	 */
	private $usuario;
	
	/**
	 * Nome
	 */
	private $nome;
	
	/**
	 * Tipo
	 */
	private $tipo;
	
    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $system;
		
		$system->log->debug->debug("DHCUsuario: nova instância");
		
	}
	
	/**
	 * Autenticar o usuário no banco
	 *
	 * @param string $usuario
	 * @param string $senha
	 * @return boolean
	 */
    public function autenticar ($usuario,$senha) {
    	
    	global $system;
    	    	
    	/** Faz a autenticação no banco **/
    	$arr = $system->db->extraiPrimeiro("
    		SELECT 	*
    		FROM 	USUARIOS U
    		WHERE 	U.USUARIO 	= '".$usuario."'
    		AND 	U.SENHA 	= '".$senha."'
		");
    	
    	
    	if (isset($arr->CODIGO)) {
    		
    		/** Verifica se o usuário está ativo **/
    		if ($arr->COD_STATUS <> 'A') {
    			$system->log->debug->debug('Usuário '.$usuario. ' desativado !!! ');
    			return 2;
    		}

    		/** Atualiza os atributos **/
    		$this->setCodUsuario($arr->CODIGO);
    		$this->setNome($arr->NOME);
    		$this->setUsuario($arr->USUARIO);
			$this->setTipo($arr->COD_TIPO);
    		return 0;
		}else{
			return 1;
		}
    }
    
    /**
     * Resgatar o código do usuário
     *
     * @return string
     */
    public function getCodUsuario () {
    	return $this->codUsuario;
    }
    
    /**
     * Definir o código do usuário
     *
     * @param string $codUsuario
     */
    public function setCodUsuario ($codUsuario) {
    	$this->codUsuario = $codUsuario;
    }
    
    /**
     * Resgatar a identificação do usuário
     *
     * @return string
     */
    public function getUsuario () {
    	return $this->usuario;
    }
    
    /**
     * Definir a identificação do usuário
     *
     * @param string $usuario
     */
    public function setUsuario ($usuario) {
    	$this->usuario = $usuario;
    }
    
    /**
     * Resgatar o nome do usuário
     *
     * @return string
     */
    public function getNome () {
    	return $this->nome;
    }
    
    /**
     * Definir o nome do usuário
     *
     * @param string $nome
     */
    public function setNome ($nome) {
    	$this->nome = $nome;
    }

    /**
     * Resgatar o tipo do usuário
     *
     * @return string
     */
    public function getTipo () {
    	return $this->tipo;
    }
    
    /**
     * Definir o tipo do usuário
     *
     * @param string $tipo
     */
    public function setTipo ($tipo) {
    	$this->tipo = $tipo;
    }
    
    /**
     * Resgata as informações do usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function getInfo ($codUsuario) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	U.*, TU.*
				FROM	USUARIOS U, TIPO_USUARIO TU
				WHERE   TU.CODIGO 		= U.COD_TIPO
				AND 	U.CODIGO 		= '".$codUsuario."'

			")
   		);	
    }
    
    /**
     * Resgata as informações do usuário
     *
     * @param integer $usuario
     * @param integer $templo
     * @return array
     */
    public static function temAcessoAoTemplo ($codUsuario,$codTemplo) {
    	global $system;
    		
    	$ret	= $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	USUARIOS U, USUARIO_TEMPLO UT
				WHERE   UT.COD_USUARIO 		= U.CODIGO
				AND		UT.COD_TEMPLO		= '".$codTemplo."'
				AND 	U.CODIGO 			= '".$codUsuario."'
    
			");
    	if ($ret->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
}
