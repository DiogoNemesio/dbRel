<?php

/**
 * @package: DHCCrypt
 * @created: 24/11/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 * Gerenciamento de criptografia 
 */

class DHCCrypt {

	/**
	 * Chave de criptografia
	 *
	 * @var string
	 */
	private $cryptKey;
	
	/**
	 * Vetor de inicialização
	 *
	 * @var string
	 */
	private $iv;
	
	/**
	 * Tamanho que a string a ser codificado deve ser
	 *
	 * @var int
	 */
	private $tamanhoString;
	
	/**
	 * Tamanho que a string a ser codificado deve ser
	 *
	 * @var int
	 */
	private $tamanhoChave;
	
	/**
	 * Caracter que ser� adicionado a string a ser criptografada caso o tamanho dela seja menor que o esperado
	 *
	 * @var string
	 */
	private $caracterAdicional;
	
	/**
	 * Construtor privado, usar DHCConexaoBanco::init();
	 *
	 */
	public function __construct() {
//		global $system;
		
//		$system->log->debug->debug("DHCCrypt: nova instância");
		
		/** Chave de criptografia **/
		$this->cryptKey	= '!@*%DHCKLA)(&MHDw31J6';
		
		/** Criando o vetor de inicialização **/
		$this->iv		= mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);

		/** Definindo o tamanha da chave **/
		$this->tamanhoChave			= 32;
		
		/** Definindo o caracter adicional **/
		$this->caracterAdicional	= chr(131);
		
		/** Definindo o tamanho que a string a ser criptografado deve ficar **/
		$this->tamanhoString		= 32;

	}

	/**
	 * Adiciona o complemento a chave (Para que a mesma string tenha v�rias criptografias)
	 *
	 * @param string $complementoChave
	 * @return string
	 */
	private function geraChave ($complementoChave) {
//		global $system;
		
		$complementoChave	= strtoupper($complementoChave);
		$chave	= $complementoChave . "#" . $this->cryptKey;
		$tam	= strlen($chave);
		if ($tam == $this->tamanhoChave) {
			null;
		}elseif ($tam > $this->tamanhoChave) {
			/** Retornar os primeiros caracteres da chave **/
			$chave	= substr($chave,0,$this->tamanhoChave);
			//$system->log->debug->debug("DHCCrypt: Tamanho de chave maior, reduzindo em: ".($tam - $this->tamanhoChave));
		}else {
			
			/** adiciona o complemento da chave no final (somente a quantidade de caracteres que restam) **/
			$diferenca	= $this->tamanhoChave - $tam;
			//$system->log->debug->debug("DHCCrypt: Tamanho de chave menor, aumentando em: ".$diferenca);
			
			if ((!$complementoChave) || (strlen($complementoChave) < $diferenca)){
				$chave	= str_pad($chave,$this->tamanhoChave,$this->caracterAdicional);
			}else{
				$chave	= $chave . '#' . substr($complementoChave,0,$diferenca-1);
			}
		}
		return ($chave);
		
	}
	
	/**
	 * Criptgrafar uma string
	 *
	 * @param string $texto Texto a ser criptografado
	 * @param string $complementoChave Complemento da chave
	 * @return string
	 */
	public function encrypt ($texto,$complementoChave) {
//		global $system;

		/** Gerando chave **/
		$chave		= $this->geraChave($complementoChave);
		
		//$system->log->debug->debug('DHCCrypt: Tamanho da chave = '.strlen($chave));
		//$system->log->debug->debug('DHCCrypt: Chave = '.$chave);
		//$system->log->debug->debug('DHCCrypt: TEXTO = '.$texto);

		/** Criptografando **/
		$encrypted	= mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $chave, $this->completaString($texto), MCRYPT_MODE_ECB, $this->iv);
		//$system->log->debug->debug('DHCCrypt: MCRYPT = ' . $encrypted);

		$hexa		= bin2hex($encrypted);
		//$system->log->debug->debug('DHCCrypt: HEXA = '.$hexa);
		return $hexa;
	}

	/**
	 * Decriptar uma string
	 *
	 * @param string $encrypted string criptografada
	 * @param string $complementoChave complemento da chave
	 * @return string
	 */
	public function decrypt ($encrypted,$complementoChave) {
//		global $system;
		
		/** Gerando a chave **/
		$chave		= $this->geraChave($complementoChave);
		//$system->log->debug->debug('DHCCrypt: Tamanho da chave = '.strlen($chave));
		//$system->log->debug->debug('DHCCrypt: Chave = '.$chave);
		//$system->log->debug->debug('DHCCrypt: ENCRYPTED: '.$encrypted);
		
		/** Convertendo a string em bin�rio **/		
		$encoded	= pack("H*", $encrypted);
		//$system->log->debug->debug('DHCCrypt: ENCODED = '.$encoded);
		
		/** Decriptando **/
		$decrypted	= mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $chave, $encoded, MCRYPT_MODE_ECB, $this->iv);
		//$system->log->debug->debug('DHCCrypt: DECRYPTED = '.$decrypted);
		
		$texto		= $this->voltaString($decrypted);
		return 		$texto;
	}
	
	 /**
	  * Adiciona o caracter adicional em uma string at� a string ficar com o tamanho correto
	  *
	  * @param string $string
	  * @return string
	  */
	 private function completaString($string) {
		if (strlen($string) >= $this->tamanhoString) {
			return $string;
		}else{
			return str_pad($string,$this->tamanhoString,$this->caracterAdicional);
		}
	}
	
	 /**
	  * retira os caracteres adicional de uma string
	  *
	  * @param string $string
	  * @return string
	  */
	private function voltaString($string) {
		return str_ireplace($this->caracterAdicional,'',$string);
	}

}
