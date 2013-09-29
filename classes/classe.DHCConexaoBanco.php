<?php

/**
 * @package: DHCConexaoBanco
 * @created: 24/11/2007
 * @Author: Daniel Henrique Cassela
 * @version: 2.0
 * 
 * Conectar ao Banco de Dados
 */

class DHCConexaoBanco {

	/**
	 * Objeto que irá guardar a instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;

	/**
	 * Objeto que irá guardar a instância do ADODB
	 */
	public $con;
	
	/**
	 * Driver
	 */
	protected $driver;
	
	/**
	 * Construtor privado, usar DHCConexaoBanco::init();
	 *
	 */
	private function __construct() {
		global $system;
		
		$system->log->debug->debug("DHCConexaoBanco: nova instância");
		
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
    	global $system;
    	$system->log->debug->debug("DHCConexaoBanco: tentativa de clonagem");
    }

	/**
	 * Fazer conexão ao banco
	 *
	 * @param string $ip
	 * @param string $usuario
	 * @param string $senha
	 * @param string $banco
	 * @param string $indSenhaCript Indicador de senha criptografada (DHC_SENHA_ESCONDIDA - Criptografada, DHC_SENHA_NAO_ESCONDIDA não criptografada)
	 */
    public function conectar ($ip = '',$usuario = '',$senha = '',$banco = '',$indSenhaCript = '',$driver = '') {
    	global $system;
    	
    	if (!$driver) {
    		$driver	= $system->config->database->driver;
    	}
    	
    	/** Checando se os parâmetros de banco de dados foram configurados **/
		if (!$driver) {
			$system->log->file->err('parâmetros de banco de dados não informado: (database.driver)');
			DHCErro::halt('parâmetros de banco de dados não informado: (database.driver)');
		}
    	
		/**
    	 * Os parâmetros que forem passados em branco serão resgatados do arquivo de configuração
    	 */
    	if (!$ip) 				$ip 			= $system->config->database->ip;
    	if (!$usuario)			$usuario 		= $system->config->database->usuario;
    	if (!$senha)			$senha 			= $system->config->database->senha;
    	if (!$banco)			$banco 			= $system->config->database->banco;
    	if (!$indSenhaCript)	$indSenhaCript 	= $system->config->database->indSenhaCript;
    	
    	/**
    	 * Por padrão a senha passada deve ser criptografada
    	 */
		if (!$indSenhaCript) {
			$indSenhaCript = DHC_SENHA_NAO_ESCONDIDA;
			$system->log->debug->debug("Senha não escondida");
		}elseif ($indSenhaCript == 1) {
			$indSenhaCript = DHC_SENHA_ESCONDIDA;
		}
		
		/** Checando se os parâmetros obrigatórios estão corretos **/
		if ((!$banco) || (!$usuario) ||(!$senha)) {
			$system->log->file->warn('parâmetros de banco de dados não informado');
			DHCErro::halt('parâmetros de banco de dados não informado');
		}

		/** Recuperando a senha do banco de dados caso esteja criptografada **/
		if ($indSenhaCript == DHC_SENHA_ESCONDIDA) {
			$crypt	= new DHCCrypt();
			$pass	= $crypt->decrypt($senha,$usuario);
		}else {
			$pass	= $senha;
		}
		
		//$system->log->file->warn('Senha: '.$pass);
	
		/** Monta o array de parâmetro para conectar ao banco **/
		if ($ip) $dbParams["host"] 	= $ip;
		$dbParams["username"]		= $usuario;
		$dbParams["password"]		= $pass;
		$dbParams["dbname"]			= $banco;
		$dbParams["persistent"]		= false;
		$dbParams["charset"]		= $system->config->database->charset;
				
		/** Salva o parâmetro de display erro do PHP **/
		$dispErroSave	= ini_get('display_errors');
		
		/** Altera o parâmetro para não mostrar os erros **/
		ini_set('display_errors',true);

		try {
			$this->con = Zend_Db::factory($driver,$dbParams);
		    $this->con->getConnection();
		    
		    /** Configura o modo de recuperação de dados **/
		    $this->con->setFetchMode(Zend_Db::FETCH_OBJ);
		    
		} catch (Zend_Db_Adapter_Exception $e) {
			$system->log->file->err('DHCConexaoBanco: '.$e->getTraceAsString());
			unset($system);
			die('Servidor com problemas, tente novamente dentro de instantes');
		} catch (Zend_Exception $e) {
			$system->log->file->err('DHCConexaoBanco: '.$e->getTraceAsString());
			unset($system);
			die('Servidor com problemas, tente novamente dentro de instantes');
		}
		
		/**
		 * Seta a propriedade DRIVER
		 */
		$this->driver	= $system->config->database->driver;
		
		
		/**
		 * Loga que conectou no banco com sucesso
		 */
		$system->log->debug->debug('Conexão com o banco de dados efetuada com sucesso !!!');

		/** retornar o parâmetro de display erro **/
		ini_set('display_errors',$dispErroSave);
		
		/** Setando o charset default **/
		$this->Executa("SET NAMES '".$system->config->database->charset."'");
		$this->Executa('SET character_set_connection='.$system->config->database->charset);
		$this->Executa('SET character_set_client='.$system->config->database->charset);
		$this->Executa('SET character_set_results='.$system->config->database->charset);

    }
    
    public function desconectar () {
    	global $system;
		$system->log->debug->debug('Conexão com o banco fechada com sucesso !!!');
    	$this->con->closeConnection();
    }
    
    /**
     * Executar uma instrução SQL
     *
     * @param string $sql
     * @return RecordSet
     */
    public function Executa ($sql,$parametros = array()) {
		global $system;
    	
    	try {
    		/** Criar o "STATEMENT" **/
    		$stmt	= $this->Statement($sql);
    		
    		/** Executar **/
    		$stmt->execute($parametros);
			//$system->log->debug->debug($sql.' Executado com sucesso !!!');
    		
		} catch (Zend_Exception $e) {
			$system->halt($e->getMessage(),$e->getTraceAsString(),'DHCConexaoBanco');
    	}
    		
    }
    
    /**
     * Recuperar todos os registros de um SQL
     *
     * @param string $sql
     * @return array
     */
    public function extraiTodos ($sql) {
    	global $system;
    	try {
    		/** Criando a sentença **/
    		$stmt	= $this->con->query($sql);
			//$system->log->debug->debug($sql.' FetchAll executado !!!');
    		
    		/** Retornando os dados **/
			//print_r($stmt);
			$return = $stmt->fetchAll();
			$stmt->close();
    		return ($return);
    		

    	} catch (Zend_Exception $e) {
    		$system->halt($e->getMessage(),$e->getTraceAsString(),'DHCConexaoBanco');
    	}
    }
    
    /**
     * Recuperar o Primeiro registros de um SQL
     *
     * @param string $sql
     * @return array
     */
    public function extraiPrimeiro ($sql) {
    	global $system;
    	try {
			/** Criando a sentença **/
    		$stmt	= $this->con->query($sql);    		
			//$system->log->debug->debug($sql.' Fetch executado !!!');
    		
    		/** Retornando os dados **/
    		$return  = $stmt->fetch();
    		$stmt->close();
    		return ($return);

    	} catch (Zend_Exception $e) {
    		$system->halt($e->getMessage(),$e->getTraceAsString(),'DHCConexaoBanco');
    	}
    }

    /**
     * Alterar a forma de extrair os dados
     *
     * @param string $modo
     */
    public function setFetchMode($modo) {
    	$this->con->setFetchMode($modo);
    }


    /**
     * Cria um statement
     *
     * @param string $sql
     * @return Zend_DB_Statement
     */
    private function Statement ($sql) {
    	global $system;
    	
    	try {
    		switch ($this->driver) {
    			case 'oci8':
    			case 'oracle':
    				$stmt	= new Zend_Db_Statement_Oracle($this->con,$sql);
    				break;
    			case 'Mysqli':
    			case 'Mysql':
    			case 'Pdo_Mysql':
    				$stmt	= new Zend_Db_Statement_Mysqli($this->con,$sql);
    				break;
    		}
    		
    		return ($stmt);
    		
		} catch (Zend_Exception $e) {
			$system->halt($e->getMessage(),$e->getTraceAsString(),'DHCConexaoBanco');
    	}
    }

}
