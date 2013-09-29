<?php

namespace Zage\Auth;

/**
 * Gerenciar a autenticação
 *
 * @package \Zage\validaSenha
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */
class validaSenha extends \Zend\Validator\AbstractValidator {
	/**
	 * Constante de erro
	 *
	 */
	const MSG_USUARIO	= 'senha';
	
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
	
		$this->setValue($value);
	
		/** Verificar se a string é alpha numérica e tem entre 2 e 14 caracteres **/
		$validatorChain = new \Zend\Validator\ValidatorChain();
		$validatorChain->attach(new \Zend\Validator\StringLength(array('min' => 2,'max' => 20)));
	
		if ($validatorChain->isValid($value)) {
			return true;
		} else {
			$this->error(self::MSG_USUARIO);
			return false;
		}
	
	}
	
}