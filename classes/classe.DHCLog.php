<?php

/**
 * Manipulação de Logs
 *
 * @package: DHCLog
 * @created: 11/12/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 *
 */

class DHCLog {

        /**
         * Objeto que irá guardar a instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
         */
        private static $instance;

        /**
         * Objeto que irá gerenciar as mensagens de log para arquivo
         *
         * @var object
         */
        public $file;

        /**
         * Objeto que irá gerenciar as mensagens de depura��o
         *
         * @var object
         */
        public $debug;

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
         * Objeto que irá gerenciar as mensagens de log para arquivo dos módulos
         *
         * @var object
         */
        public $modulo;
        
        /** Metodos **/
        /**
         * Construtor privado para implementar SINGLETON ()
         *
         */
        private function __construct() {

                /** Definindo Variáveis globais **/
                global $system,$sessao;

                /** Definindo o formato do log **/
                $this->logFormatText    = '[%timestamp%] [%priority%] [%priorityName%] [%message%]' . PHP_EOL;
                $this->logFormatHtml    = '[%timestamp%] [%priority%] [%priorityName%] [%message%]' . "<BR>";

                /** Criando o objeto (Zend Framework) do formato do log **/
                $this->formatterText    = new Zend_Log_Formatter_Simple($this->logFormatText);
                $this->formatterHtml    = new Zend_Log_Formatter_Simple($this->logFormatHtml);

                /** Criando os objetos de log **/
                $this->file				= new Zend_Log();
                $this->debug			= new Zend_Log();

                /** Criando a prioridade USER **/
                $this->file->addPriority ('USER', DHC_USER);
                $this->debug->addPriority('USER', DHC_USER);

                /** Criando o campo user **/
/*                if ($_SERVER['DOCUMENT_ROOT']) {
                        $this->file->setEventItem('user', $system->getUsuario());
                        $this->debug->setEventItem('user', $system->getUsuario());
                }*/

                /** Criando os writers do log **/
                $wNull                  = new Zend_Log_Writer_Null;
                $wTela                  = new Zend_Log_Writer_Stream('php://output');
                if ($system->config->log->arquivo->habilitado) {
                	if (is_writable($system->config->log->arquivo->caminho)) {
						$wLog           = new Zend_Log_Writer_Stream($system->config->log->arquivo->caminho);
                	}else{
                		$wLog           = &$wNull;
                	}
                }else{
					$wLog           = &$wNull;
                }

                /** Associa o formato do log para o writer **/
                $wTela->setFormatter($this->formatterHtml);
                $wLog->setFormatter($this->formatterText);

                /** Cria o stream de log de acordo com o nível de depuração configurada **/
                switch ($system->config->debug) {
                        case 0:
                                $this->debug->addWriter($wNull);
                                break;
                        case 1:
                                $this->debug->addWriter($wLog);
                                break;
                        case 2:
                                $this->debug->addWriter($wTela);
                                break;
                        case 3:
                                $this->debug->addWriter($wLog);
                                $this->debug->addWriter($wTela);
                }

                /** Cria o stream de log para o log normal **/
                $this->file->addWriter($wLog);
        }

        /**
         * Construtor para implemetar SINGLETON
         *
         * @return object
         */
        public static function init() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    /**
     * Refazer a função para não permitir a clonagem deste objeto.
     *
     */
    public function __clone() {
        DHCErro::halt('Não é permitido clonar ');
    }
    
    /**
     * Adicionar um writer para o módulo
     */
    public function addWriter($nivel,$habilitado,$caminho) {

    	/** Criando os objetos de log **/
		$this->modulo		= new Zend_Log();

		/** Criando a prioridade USER **/
		$this->modulo->addPriority ('USER', DHC_USER);
		
		/** Criando os writers do log **/
		$wTelaMod			= new Zend_Log_Writer_Stream('php://output');
		$wNull				= new Zend_Log_Writer_Null;
        if ($habilitado) {
			$wModulo		= new Zend_Log_Writer_Stream($caminho);
        }else{
			$wModulo		= &$wNull;
        }

		/** Associa o formato do log para o writer **/
		$wTelaMod->setFormatter($this->formatterHtml);
		$wModulo->setFormatter($this->formatterText);


		/** Cria o stream de log de acordo com o nível de depuração configurada **/
        switch ($nivel) {
			case 1:
				$this->debug->addWriter($wModulo);
				break;
			case 2:
				$this->debug->addWriter($wTelaMod);
				break;
			case 3:
				$this->debug->addWriter($wModulo);
				$this->debug->addWriter($wTelaMod);
        }

		/** Cria o stream de log **/
		$this->modulo->addWriter($wModulo);
    }

}