<?php

/**
 * Classe que implementa as funções do Sistema
 * 
 * @package: webSystem
 * @created: 02/09/2009
 * @Author: Daniel Henrique Cassela
 * @version: 1.1
 * 
 */

class webSystem {

	/**
	 * Instância da classe DHCUsuario
	 *
	 * @var object
	 */
	public $usuario;
	
	/**
	 * Instância da classe Zend_Mail
	 *
	 * @var object
	 */
	public $mail;

	/**
	 * Instância da classe DHCLog
	 *
	 * @var object
	 */
	public $log;

	/**
	 * Instância da classe DHCConfig
	 *
	 * @var object
	 */
	public $config;
	
	/**
	 * Instância da classe DHCConexaoBanco
	 *
	 * @var object
	 */
	public $db;

	/**
	 * Indica se o usuário ja está autenticado
	 *
	 * @var boolean
	 */
	public $autenticado;
	
	/**
	 * Indica se ja foi feita a reconexão com o novo usuário de banco
	 *
	 * @var boolean
	 */
	private $reconectado;
	

	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {

    	/** Instânciando os objetos **/
		$this->config		= new Zend_Config_Ini(CONFIG_FILE, CONFIG_SESSION);
		$this->mail			= new Zend_Mail('utf-8');
		$this->initLog();

		$this->log->debug->debug(__CLASS__.": nova Instância !!!");

		/** Definindo o timezone padrão **/
		date_default_timezone_set($this->config->data->timezone);
		
		/** Não mostrar os erros **/
		ini_set("display_errors", "1");
		
		/** Definindo atributos globais a Instância de e-mail (Podem ser alterados no momento do envio do e-mail) **/
		$this->mail->setFrom($this->config->mail->from,$this->config->mail->fromname);
		$this->mail->addTo($this->config->admin->email,$this->config->admin->nome);

		/** Fazendo a conexão ao banco de dados **/
		$this->db		= DHCConexaoBanco::init();
		$this->db->conectar(null,null,null,null,DHC_SENHA_ESCONDIDA);

		/** Instânciando os objetos por ordem de precedência **/
		$this->usuario		= new DHCUsuario();
		
		/** Define que o usuário não está autenticado **/
		$this->desautentica();
		
		/** Define que o usuário ainda não reconectou com o banco de dados **/
		//$this->reconectado = false;
		
    }
	
    /**
     * Resgatar o Usuário
     *
     * @return string
     */
    public function getUsuario () {
    	if (is_object($this->usuario)) {
    		return $this->usuario->getUsuario();
    	}else{
    		return null;
    	}
    }
    
    /**
     * Resgatar o Usuário
     *
     * @return string
     */
    public function getCodUsuario () {
    	if (is_object($this->usuario)) {
    		return $this->usuario->getCodUsuario();
    	}else{
    		return null;
    	}
    }

    /**
     * Resgatar o Tipo do Usuário
     *
     * @return string
     */
    public function getTipoUsuario () {
    	if (is_object($this->usuario)) {
    		return $this->usuario->getTipo();
    	}else{
    		return null;
    	}
    }
    
    /**
     * Indicar que o usuário está autenticado
     */
    public function autenticado() {
    	$this->autenticado = true;
    }

    /**
     * Desautenticar
     *
     */
    public function desautentica() {
    	$this->autenticado = false;
    }

    /**
     * Verifica se o usuario ja está autenticado
     *
     * @return boolean
     */
    public function estaAutenticado() {
    	return $this->autenticado;
    }

    /**
     * Definir o código do Sistema
     */
    public function setCodSistema($valor) {
    	$this->codSistema	= $valor;
    }

    /**
     * Resgatar o código do Sistema
     */
    public function getCodSistema() {
    	return($this->codSistema);
    }

    /**
     * Definir o código do Sistema
     */
    public function setCodModulo($valor) {
    	$this->codModulo	= $valor;
    }

    /**
     * Resgatar o código do Sistema
     */
    public function getCodModulo() {
    	return($this->codModulo);
    }
    
    /**
     * Verifica se o usuario ja está autenticado
     *
     * @return boolean
     */
    public function jaReconectou() {
    	return $this->reconectado;
    }
    
    /**
     * Inicia o streamer de log 
     *
     * @return boolean
     */
    public function initLog() {
    	return $this->log = DHCLog::init();
    }
    
    /**
     * Terminar a execução do script por conta de um erro, se for o caso tb mandar um e-mail
     *
     * @param string $mensagem
     * @param string $trace
     */
    public function halt ($mensagem, $trace, $classe, $mostrar = false) {

    	/** Gerar Log de Erro **/
		$this->log->file->err("$classe: ".$mensagem);
		
		$htmlMessage	= "
		<html>
		<head>
		<style type=text/css>
		.Texto {
			font-family: Trebuchet MS,Verdana,Arial;
			font-size: 14px;
		}
		.Mensagem {
			font-family: Trebuchet MS,Verdana,Arial;
			font-size: 16px;
			color: Red;
		}

		.Titulo {
			font-family: Trebuchet MS,Verdana,Arial;
			font-weight: bold;
			font-size: 16px;
		}
		</style>
		<body align='center'>
		<table align='center'>
		";
		if ($classe) {
			$htmlMessage .= "<tr><td class='Titulo'>Classe:</td><td class='Texto'>".$classe."</td></tr>";
		}
		if ($trace) {
			$htmlMessage .= "<tr><td class='Titulo'>Trace:</td><td class='Texto'>".$trace."</td></tr>";
		}
		if ($this->getUsuario()) {
			$htmlMessage .= "<tr><td class='Titulo'>Usuário:</td><td class='Texto'>".$this->getUsuario()."</td></tr>";
		}

		$htmlMessage .= "<tr><td class='Titulo'>IP:</td><td class='Texto'>".$_SERVER['REMOTE_ADDR']."</td></tr>";
		$htmlMessage .= "<tr><td class='Titulo'>Erro:</td><td class='Mensagem'>".$mensagem."</td></tr>
		</table>
		</body>
		</html>";
		
		/** Enviar e-mail se o parâmetro estiver configurado para isso **/
		if ($this->config->admin->email) {
			$this->mail->setBodyHtml($htmlMessage);
			$this->mail->send();
		}

		/** Terminar o script **/
		if ($mostrar) {
			DHCErro::halt($htmlMessage);
		}else{
			DHCErro::halt();
		}
    }
        
    /**
     * Desconecta do banco
     */
    public function DBDesconecta () {
    	$this->db->desconectar();
    	$this->log->debug->debug(__CLASS__.": Conexão fechada !!!");
    }
    
    /**
     * Reconecta no banco
     */
    public function DBReconecta ($usuario,$senha,$banco) {
    	$this->db->conectar('',$usuario,$senha,$banco,DHC_SENHA_NAO_ESCONDIDA);
    	$this->reconectado = true;
    	$this->log->debug->debug(__CLASS__.": Reconectou com usuario novo (".$usuario.") !!!");
    }
    
    /**
     * Verifica se o usuário tem permissão no menu
     */
    public function temPermissaoNoMenu($codMenu) {
    	$arr = $this->db->extraiPrimeiro("
                SELECT  COUNT(*) NUM
                FROM    MENU				M,
    					MENU_TIPO_USUARIO	MTU,
    					USUARIOS			U
				WHERE   U.COD_TIPO			= MTU.COD_TIPO_USUARIO
    			AND 	MTU.COD_MENU		= M.CODIGO
    			AND		M.CODIGO			= '".$codMenu."'
    			AND		U.CODIGO          	= '".$this->getCodUsuario()."'
    	");
    	if ($arr->NUM == 0) {
    		return false;
    	}else{
    		return true;
    	}
    }
    
    /**
     * Verifica se o usuário tem permissão no sistema
     */
    public function ehAdmin($usuario) {
    	$arr = $this->db->extraiPrimeiro("
			SELECT  COUNT(*) NUM
			FROM    USUARIOS U
			WHERE   U.USUARIO               = '".$usuario."'
			AND     U.COD_TIPO              = 'A'
                ");
    	if ($arr->NUM == 0) {
    		return false;
    	}else{
    		return true;
    	}
    }
    
    
    /**
     * Verifica se o usuário tem permissão no menu
     */
    public function checaPermissao($codMenu) {
    	if ($this->temPermissaoNoMenu($codMenu) == false) DHCErro::halt('Sem Permissão no Menu !!!');
    }
    
    
    /**
     * Resgata o tipo de Usuário
     *
     * @param string $skin
     * @return string
     */
    public function DBGetTipoUsuario($usuario) {
    	$return = $this->db->extraiPrimeiro("
     		SELECT COD_TIPO
            FROM   USUARIOS U
            WHERE  U.USUARIO   = '".$usuario."'
                ");
    	if (isset($return->COD_TIPO)) {
    		return ($return->COD_TIPO);
    	}else{
    		return null;
    	}
    }
    

    /**
     * Resgatar a lista de Tipos de Usuários
     */
    public function DBGetListTipoUsuario() {
    	return (
    		$this->db->extraiTodos("
				SELECT  *
				FROM    TIPO_USUARIO
				ORDER BY NOME
		")
    	);
    }
    
    
}