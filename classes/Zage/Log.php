<?php

namespace Zage;

/**
 * Gerenciar os logs do sistema
 *
 * @package \Zage\Log
 * @created 10/07/2013
 * @author Daniel Henrique Cassela
 * @version GIT: $Id$ 2.0.1
 *         
 */
class Log {
	
	/**
	 * Objeto que irá guardar a instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;
	
	/**
	 * Objeto que irá gerenciar as mensagens de log para arquivo
	 *
	 * @var object
	 */
	private $file;
	private $debug;
	
	/**
	 * Formato no qual o log será armazendado em texto
	 *
	 * @var string
	 */
	private $logFormatText;
	
	/**
	 * Formato no qual o log será armazendado em html
	 *
	 * @var string
	 */
	private $logFormatHtml;
	
	/**
	 * Objeto que irá definir o formato do log em texto
	 *
	 * @var object
	 */
	private $formatterText;
	
	/**
	 * Objeto que irá definir o formato do log em html
	 *
	 * @var object
	 */
	private $formatterHtml;
	
	/**
	 * Construtor privado para implementar SINGLETON ()
	 */
	private function __construct() {
		
		/**
		 * Definindo Variáveis globais *
		 */
		global $system;
		
		/**
		 * Definindo o formato do log *
		 */
		$this->logFormatText = '[%timestamp%] [%priority%] [%priorityName%] [%message%]' . PHP_EOL;
		$this->logFormatHtml = '[%timestamp%] [%priority%] [%priorityName%] [%message%]' . "<BR>";
		
		/**
		 * Criando o objeto (Zend Framework) do formato do log *
		 */
		$this->formatterText = new \Zend\Log\Formatter\Simple ( $this->logFormatText );
		$this->formatterHtml = new \Zend\Log\Formatter\Simple ( $this->logFormatHtml );
		
		/**
		 * Criando os objetos de log *
		 */
		$this->file 	= new \Zend\Log\Logger ();
		$this->debug 	= new \Zend\Log\Logger ();
		
		/**
		 * Criando os writers do log *
		 */
		$wNull = new \Zend\Log\Writer\Null ();
		$wTela = new \Zend\Log\Writer\Stream ( 'php://output' );
		
		if (isset($system) && isset($system->config) && isset($system->config["log"]["habilitado"]) && ($system->config["log"]["habilitado"] == 1) ) {
			/**
			 * Verifica se o arquivo está com permissão para leitura e gravação
			 */
			if (is_writable(DOC_ROOT . $system->config["log"]["caminho"])) {
				$wLog = new \Zend\Log\Writer\Stream ( DOC_ROOT . $system->config["log"]["caminho"] );
			}else{
				die("Arquivo: ". DOC_ROOT . $system->config["log"]["caminho"] . " não pode ser aberto para gravação !!!");
			}
			
		} else {
			$wLog = &$wNull;
		}
		
		/**
		 * Associa o formato do log para o writer *
		 */
		$wTela->setFormatter 	( $this->formatterHtml );
		$wLog->setFormatter 	( $this->formatterText );
		
		/**
		 * Cria o stream de log de acordo com o nível de depuração configurada *
		 */

		switch ($system->config["debug"]) {
			case 0 :
				$this->debug->addWriter ( $wNull );
				break;
			case 1 :
				$this->debug->addWriter ( $wLog );
				break;
			case 2 :
				$this->debug->addWriter ( $wTela );
				break;
			case 3 :
				$this->debug->addWriter ( $wLog );
				$this->debug->addWriter ( $wTela );
		}
		
		$this->file->addWriter( $wLog );
	}
	
	/**
	 * Construtor para implemetar SINGLETON
	 *
	 * @return object
	 */
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			$c = __CLASS__;
			self::$instance = new $c ();
		}
		
		return self::$instance;
	}
	
	/**
	 * Refazer a função para não permitir a clonagem deste objeto.
	 */
	public function __clone() {
		Erro::halt ( __CLASS__ . ': Não é permitido clonar !! ' );
	}
	
	/**
	 * Fazer log do tipo DEBUG
	 * @param string $mensagem
	 */
	public function debug($mensagem) {
		$this->debug->log(\Zend\Log\Logger::DEBUG, $mensagem);
	} 

	/**
	 * Fazer log do tipo INFO
	 * @param string $mensagem
	 */
	public function info($mensagem) {
		$this->file->log(\Zend\Log\Logger::INFO, $mensagem);
	}
	
	/**
	 * Fazer log do tipo NOTICE
	 * @param string $mensagem
	 */
	public function notice($mensagem) {
		$this->file->log(\Zend\Log\Logger::NOTICE, $mensagem);
	}

	/**
	 * Fazer log do tipo WARN
	 * @param string $mensagem
	 */
	public function warn($mensagem) {
		$this->file->log(\Zend\Log\Logger::WARN, $mensagem);
	}
	
	/**
	 * Fazer log do tipo ERR
	 * @param string $mensagem
	 */
	public function err($mensagem) {
		$this->file->log(\Zend\Log\Logger::ERR, $mensagem);
	}

	/**
	 * Fazer log do tipo CRIT
	 * @param string $mensagem
	 */
	public function crit($mensagem) {
		$this->file->log(\Zend\Log\Logger::CRIT, $mensagem);
	}

	/**
	 * Fazer log do tipo ALERT
	 * @param string $mensagem
	 */
	public function alert($mensagem) {
		$this->file->log(\Zend\Log\Logger::ALERT, $mensagem);
	}

	/**
	 * Fazer log do tipo EMERG
	 * @param string $mensagem
	 */
	public function emerg($mensagem) {
		$this->file->log(\Zend\Log\Logger::EMERG, $mensagem);
	}
}
