<?php

namespace Zage;

/**
 * Gerenciar a autenticação
 *
 * @package \Zage\Auth
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 12/07/2013
 */
class Auth implements \Zend\Authentication\Adapter\AdapterInterface {

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
		global $system,$log;
	
		$log->debug(__CLASS__.": nova instância");
		$log->debug(__CLASS__.': Definindo usuario: '.$username);
	
		$this->username 	= $username;
		$this->password		= $password;
	
	}
	
	/**
	 * Faz a autenticação
	 *
	 * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {
		 
		global $system;
		 
		if (isset($system->usuario) && method_exists($system->usuario, 'autenticar') ) {

			$result		= $system->usuario->autenticar($this->username,$this->password);
				
			if ($result == true) {
				$result	= \Zend\Authentication\Result::SUCCESS;
				$messages[] = null;
			}else{
				$result		= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
				$messages[] = "Informações incorretas !!!";
			}
			return new \Zend\Authentication\Result($result,$this->username,$messages);

		}else{
			die('Função de autenticação do usuário não encontrada !!!');
		}
		
	}
	
	
}
