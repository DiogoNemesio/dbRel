<?php
namespace Zage;

/**
 * Gerenciar as criptografias
 *
 * @package \Zage\Crypt
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */

class Crypt {

	/**
	 * Chave de criptografia
	 *
	 * @var string
	 */
	private $cryptKey;
	
	/**
	 * Instância do objeto \Zend\Crypt
	 *
	 * @var string
	 */
	private $object;
	
	/**
	 * Caracter de junção de complemento
	 *
	 * @var string
	 */
	private $char;
	
	/**
	 * Construtor
	 *
	 */
	public function __construct() {
		global $system;
		
		/** Chave de criptografia **/
		$this->setCryptKey('!@*%ZWSKLA)(&MHDw31J6...');
		
		/**
		 * Define o Caracter de junção de complemento
		 */
		$this->setChar(chr(131));
		
		$this->object 		=  \Zend\Crypt\BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		$this->object->setKey($this->getCryptKey());

	}

	/**
	 * Criptgrafar uma string
	 *
	 * @param string $texto Texto a ser criptografado
	 * @param string $complementoChave Complemento da chave
	 * @return string
	 */
	public function encrypt ($texto,$complementoChave) {
		global $system;

		/** 
		 * Criptografando 
		 **/
		$encrypted	= $this->object->encrypt($texto.$this->getChar().$complementoChave);
		return $encrypted;
	}

	/**
	 * Decriptar uma string
	 *
	 * @param string $encrypted string criptografada
	 * @param string $complementoChave complemento da chave
	 * @return string
	 */
	public function decrypt ($encrypted) {
		global $system;
		
		/**
		 * Decriptando
		 **/
		$decrypted	= $this->object->decrypt($encrypted);

		/**
		 * Retornando o texto original sem o complemento
		 */
		$texto		= substr($decrypted,0,strpos($decrypted,$this->getChar()));
		return ($texto);
	}
	
	/**
	 * @return the $cryptKey
	 */
	private function getCryptKey() {
		return $this->cryptKey;
	}

	/**
	 * @param string $cryptKey
	 */
	private function setCryptKey($cryptKey) {
		$this->cryptKey = $cryptKey;
	}
	
	/**
	 * @return the $char
	 */
	private function getChar() {
		return $this->char;
	}


	/**
	 * @param string $char
	 */
	private function setChar($char) {
		$this->char = $char;
	}


}
