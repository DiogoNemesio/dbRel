<?php

namespace Zage;

/**
 * Gerenciar conexões com o banco de dados
 *
 * @package \Zage\DB
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 12/07/2013
 */
class DB {
	
	const DB_SENHA_TEXTO = 0;
	const DB_SENHA_CRYPT = 1;
	
	/**
	 * Objeto que irá guardar a instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;
	
	/**
	 * Objeto que irá guardar a instância do \Zend\DB
	 */
	public $con;
	
	/**
	 * Driver que será utilizado
	 * @var string
	 */
	private $driver;
	
	/**
	 * Construtor privado, usar DB::getInstance();
	 *
	 */
	private function __construct() {
		global $system,$log;
	
		$log->debug(__CLASS__.": nova instância");
	}
	
	/**
	 * Construtor para implemetar SINGLETON
	 *
	 * @return object
	 */
	public static function getInstance() {
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
		global $system,$log;
		$log->debug(__CLASS__.": tentativa de clonagem");
		die(__CLASS__.': não pode ser clonado !!!');
	}
	
	/**
	 * Fazer conexão ao banco
	 *
	 * @param string $ip
	 * @param string $usuario
	 * @param string $senha
	 * @param string $banco
	 * @param string $indSenhaCript Indicador de senha criptografada (1 Criptografada, 0 não criptografada)
	 */
	public function conectar ($ip = '',$usuario = '',$senha = '',$banco = '',$indSenhaCript = '',$driver = '') {
		global $system,$log;
		 
		if (!$driver) {
			$driver	= $system->config["database"]["driver"];
		}
		 
		/** Checando se os parâmetros de banco de dados foram configurados **/
		if (!$driver) {
			Erro::halt('parâmetros de banco de dados não informado: (database.driver)');
		}else{
			$this->setDriver($driver);
		}
		 
		/**
		 * Os parâmetros que forem passados em branco serão resgatados do arquivo de configuração
		 */
		if (!$ip) 				$ip 			= $system->config["database"]["ip"];
		if (!$usuario)			$usuario 		= $system->config["database"]["usuario"];
		if (!$senha)			$senha 			= $system->config["database"]["senha"];
		if (!$banco)			$banco 			= $system->config["database"]["banco"];
		if (!$indSenhaCript)	$indSenhaCript 	= $system->config["database"]["indSenhaCript"];
		 
		/**
		 * Por padrão a senha passada deve ser criptografada
		 */
		if (!$indSenhaCript) {
			$indSenhaCript = self::DB_SENHA_TEXTO;
			$log->debug("Senha não escondida");
		}elseif ($indSenhaCript == self::DB_SENHA_CRYPT) {
			$indSenhaCript = self::DB_SENHA_CRYPT;
		}
	
		/** 
		 * Checando se os parâmetros obrigatórios estão corretos
		 **/
		if ((!$banco) || (!$usuario) ||(!$senha)) {
			Erro::halt('parâmetros de banco de dados não informado');
		}
	
		/** 
		 * Recuperando a senha do banco de dados caso esteja criptografada
		 **/
		if ($indSenhaCript == self::DB_SENHA_CRYPT) {
			$crypt	= new Crypt();
			$pass	= $crypt->decrypt($senha,$usuario);
		}else {
			$pass	= $senha;
		}
	
		/** 
		 * Monta o array de parâmetro para conectar ao banco
		 **/
		if ($ip) $dbParams["host"] 	= $ip;
		$dbParams["driver"]			= $driver;
		$dbParams["username"]		= $usuario;
		$dbParams["password"]		= $pass;
		$dbParams["database"]		= $banco;
		$dbParams["charset"]		= $system->config["database"]["charset"];
		$dbParams["options"] 		= array('buffer_results' => true);
	
		/** 
		 * Salva o parâmetro de display erro do PHP
		 **/
		$dispErroSave	= ini_get('display_errors');
	
		/** 
		 * Altera o parâmetro para não mostrar os erros 
		 **/
		ini_set('display_errors',true);
	
		try {
			
			/**
			 * Cria a adaptador da conexão
			 */
			$this->con = new \Zend\Db\Adapter\Adapter($dbParams);

			/**
			 * Testa se a conexao foi bem sucedida
			 */
			$this->testaConexao();
			
			
			/**
			 * Configura o modo de recuperação de dados
			 **/
			//$this->con->setFetchMode(Zend_Db::FETCH_OBJ);
	
			//$this->Executa("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '.,'");
			
			//print_r($this->con);
	
		} catch (\Exception $e) {
			Erro::halt($e->getMessage(),$e->getTraceAsString(),__CLASS__);
		}
	
		/** retornar o parâmetro de display erro **/
		ini_set('display_errors',$dispErroSave);
	}
	
	
	private function testaConexao () {
		global $system;
		switch ($this->getDriver()) {
			case "Mysqli":
				$sql	= "SELECT USER() AS USUARIO";
				break;
			case "Oracle":
				$sql	= "SELECT USER USUARIO FROM DUAL";
				break;
			default:
				Erro::halt("Driver: ".$this->getDriver()." ainda não implementado !!!");
		}
		
		try {
			$res	= @$this->con->query($sql);
		}catch (\Zend\Db\Adapter\Exception\ExceptionInterface $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		} catch (\Exception $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		}
	}
	
	/**
	 * @return the $driver
	 */
	protected function getDriver() {
		return $this->driver;
	}

	/**
	 * @param string $driver
	 */
	protected function setDriver($driver) {
		$this->driver = $driver;
	}

	/**
	 * Extrair todos os dados de uma consulta SQL
	 * @param string $sql
	 * @param array $parametros
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function extraiTodos($sql, $parametros = null) {
		
		try {
			$stmt 		= $this->con->createStatement($sql);
			$stmt->prepare();
			$result 	= $stmt->execute($parametros);
			
		} catch (\Zend\Db\Adapter\Exception\RuntimeException $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		}catch (\Zend\Db\Adapter\Exception\ExceptionInterface $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		} catch (\Exception $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		}
		
		if ($result instanceof \Zend\Db\Adapter\Driver\ResultInterface && $result->isQueryResult()) {
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($result);
		
			return ($resultSet);
		}
	}
	
	/**
	 * Extrair o primeiro registro de uma consulta SQL
	 * @param string $sql
	 * @param array $parametros
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function extraiPrimeiro($sql, $parametros = null) {
		try {
			$stmt 		= $this->con->createStatement($sql);
			$stmt->prepare();
			$result 	= $stmt->execute($parametros);
			
		}catch (\Zend\Db\Adapter\Exception\ExceptionInterface $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		} catch (\Zend\Db\Adapter\Exception\RuntimeException $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		} catch (\Exception $e) {
			$erro	= "[".__CLASS__."] [.".__FUNCTION__."]". $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
			Erro::halt($erro);
		}
		
		if ($result instanceof \Zend\Db\Adapter\Driver\ResultInterface && $result->isQueryResult()) {
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($result);
	
			foreach ($resultSet as $row) {
				return $row;
			}
		}
	
	}
	
	
}
