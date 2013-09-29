<?php

/** Checando se a constante que define a localização do arquivo de configuração foi definida **/
if (!defined('CONFIG_FILE')) {
	DHCErro::halt('Constante CONFIG_FILE não definida !!!');
}

/** Checando se a constante que define a sessão do arquivo de configuração foi definida **/
if (!defined('CONFIG_SESSION')) {
	DHCErro::halt('Constante CONFIG_SESSION não definida !!!');
}


if (!is_object($system)) {
	
	/** Instancia o sistema **/
	$system		= dbRel::init();

	/** Inicializa o sistema **/
	$system->inicializaSistema();

}

$system->initLog();

/**
 * Checar se as configurações do arquivo INI (configuração) está OK
 */

/** Validar o valor da variável debug **/
if ((!Zend_Validate::is($system->config->debug,'Int')) || (!Zend_Validate::is($system->config->debug,'Between',array(0,3)))) {
	DHCErro::halt('Erro de configuração, variável <b>"debug"</b> com valor incorreto !!!');
}

/** Validar as variáveis de log **/
if ((!Zend_Validate::is($system->config->log->arquivo->habilitado,'Int')) || (!Zend_Validate::is($system->config->log->arquivo->habilitado,'Between',array(0,1)))) {
	DHCErro::halt('Erro de configuração, variável log.arquivo.habilitado com valor incorreto !!!');
}

if ($system->config->log->arquivo->habilitado == 1) {
	/** Checar se a variavel do caminho do arquivo está configurada **/
	if (!$system->config->log->arquivo->caminho) {
		DHCErro::halt('Erro de configuração, variável log.arquivo.caminho com valor incorreto !!!');
	}
}

if (!$system->config->skin) {
	DHCErro::halt('Erro de configuração, variável skin não definida !!!');
}


if ($_SERVER['DOCUMENT_ROOT']) {

	/** descarregar o buffer de saída **/
	//ob_end_flush();

	/** Checa se a autenticação foi feita **/
	//include_once(BIN_PATH . 'auth.php');
}
