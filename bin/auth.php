<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}


/** Verificar se o usuário e senha estão sendo passados através do form **/
if ((isset($_POST['usuario'])) && (isset($_POST['senha'])) ) {
	$usuario	= DHCUtil::antiInjection($_POST['usuario']);
	$senha		= DHCUtil::antiInjection($_POST['senha']);
}else{
	$usuario	= '';
	$senha		= '';
}

//echo "Usuário:".$usuario."<BR>Senha: ".$senha;

/** Limpando a variável da mensagem **/
$mensagem		= '';

/** Instanciando o objeto de autenticação **/
$auth			= Zend_Auth::getInstance();

/** Verifica se o usuário já está conectado **/
if (!$system->estaAutenticado()) {

	/** Se as variáveis estiverem preenchidas é pq o formulário já foi mostrado, então deve ser feito a autenticação **/
	if (($usuario) && ($senha)) {
		
		/** Verificar as informações **/
		$valUsuario	= new DHCValUsuario();
		$valSenha	= new DHCValSenha();
		
		if (!$valUsuario->isValid($usuario)) {
    		$r			= Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new Zend_Auth_Result($r,$usuario,$valUsuario->getMessages());
		}elseif (!$valSenha->isValid($senha)) {
    		$r			= Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new Zend_Auth_Result($r,$usuario,$valSenha->getMessages());
		}else{
			/** Cria o adaptador para autenticação **/
			$authAdap	= new DHCAuth($usuario,$senha);
			$result		= $authAdap->authenticate();
		}
		

		/** Checa o resultado **/
		if (!$result->isValid()) {
						
			/** Resgata a mensagem retornada (Apenas a primeira) **/
			$m			= $result->getMessages();
		
			if (isset($m["usuario"])) {
				$mensagem	= "Usuario: ".$m["usuario"];
			}elseif (isset($m["senha"])) {
				$mensagem	= "Senha: ".$m["senha"];
			}else{
				$mensagem	= $m[0];	
			}
			
			/** Voltar para a tela de Login **/
			include_once(BIN_PATH . '/login.php');
			exit;
			
		} else {
			/** Tudo OK, salva na sessão **/
			$system->usuario->setUsuario($usuario);
			$system->autenticado();
		}
	}else{

		/** Mostrar tela de login **/
		include_once(BIN_PATH . '/login.php');
		exit;
	}

}