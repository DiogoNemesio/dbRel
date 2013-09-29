<?php

/**
 * Validar senha
 * 
 * @package: DHCValSenha
 * @created: 21/12/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class DHCValSenha extends Zend_Validate_Abstract  {
	
	/**
	 * Constante de erro
	 *
	 */
	const MSG_SENHA	= 'senha';

	
	/**
	 * Mensagens de erro
	 *
	 * @var array
	 */
	protected $_messageTemplates	= array (
		self::MSG_SENHA	=> "Informação inválida !!!"
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
		
		/** Verificar se a string é alpha numérica e tem entre 2 e 15 caracteres **/
		$validatorChain = new Zend_Validate();
		$validatorChain->addValidator(new Zend_Validate_StringLength(2, 15));

		if ($validatorChain->isValid($value)) {
			return true;
    	} else {
    		$this->_error(self::MSG_SENHA);
			return false;
    	}

	}
}