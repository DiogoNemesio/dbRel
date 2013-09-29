<?php

/**
 * Gerenciar a autenticação 
 * 
 * @package: DHCAuth
 * @created: 11/12/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class DHCAuth implements Zend_Auth_Adapter_Interface {

	/**
	 * Usuário
	 */
	private $username;

	/**
	 * Senha
	 */
	private $password;
	
    /**
     * Sets username and password for authentication
     *
	 * @return void
	 */
	public function __construct($username,$password) {
		
		/** Definindo Variáveis globais **/
		global $system;
		
		$system->log->debug->debug("DHCAuth: nova instância");
		$system->log->debug->debug('DHCAuth: Definindo usuario: '.$username);
		
		$this->username 	= $username;
		$this->password		= DHCAuth::encrypt($username,$password);
		
	}
	
    /**
     * Faz a autenticação
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() {
    	
    	global $system;
    	
    	$result		= $system->usuario->autenticar($this->username,$this->password);
    	
    	if ($result == 0) {
    		$result	= Zend_Auth_Result::SUCCESS;
    		$messages[] = null;
    	}elseif ($result == 2){
    		$result		= Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$messages[] = "Usuário desativado !!!";
    	}elseif ($result == 3){
    		$result		= Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$messages[] = "Sem acesso ao templo !!!";
    	}else{
    		$result		= Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$messages[] = "Informações incorretas !!!";
    	}
    	return new Zend_Auth_Result($result,$this->username,$messages);
    }
    
    
    /**
     * Criptografar a senha
     * @param string $usuario
     * @param string $senha
     * @return string
     */
    public static function encrypt ($usuario,$senha) {
    	return md5($usuario."|drf|".$senha);
    }
    
}
