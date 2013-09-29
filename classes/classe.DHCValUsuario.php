<?php

/**
 * Validar usuário
 * 
 * @package: DHCValUsuario
 * @created: 21/12/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class DHCValUsuario extends Zend_Validate_Abstract  {
	
	/**
	 * Constante de erro
	 *
	 */
	const MSG_USUARIO	= 'usuario';
	
	/**
	 * Mensagens de erro
	 *
	 * @var array
	 */
	protected $_messageTemplates	= array (
		self::MSG_USUARIO	=> "Informação inválida !!!"
	);


	/**
	 * Construtor
	 *
	 */
	public function __construct() {
	}
	
	/**
	 * Verificar se a informação é válida
	 *
	 * @param string $value
	 * @return boolean
	 */
	public function isValid($value) {
		
		$this->_setValue($value);
		
		/** Verificar se a string é alpha numérica e tem entre 4 e 14 caracteres **/
		$validatorChain = new Zend_Validate();
		$validatorChain->addValidator(new Zend_Validate_StringLength(2, 14));
		$validatorChain->addValidator(new Zend_Validate_Alnum());

		if ($validatorChain->isValid($value)) {
			return true;
    	} else {
    		$this->_error(self::MSG_USUARIO);
			return false;
    	}

	}
}