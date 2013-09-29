<?php
/**
 * Script de include dos scripts
 */

/**
 * incluir o arquivo de configuração
 */
include_once('root.php');

/**
 * Definições de constantes
 */
include_once(DOC_ROOT		. '/constants.php');

/**
 * Zend FrameWork
 */
/* Zend */
set_include_path(get_include_path() . PATH_SEPARATOR . PKG_PATH . '/Zend/library/');
include_once('Zend/Loader/Autoloader.php');
include_once (CLASS_PATH . '/Zage/Loader.php');
include_once ('autoLoad.php');
//include_once(SHARED_CLASS_PATH . 'classe.DHCLoader.php');
//include_once('autoLoad.php');

/**
 * TCPdf
 */
include_once(PKG_PATH . '/tcpdf/config/lang/bra.php');
include_once(PKG_PATH . '/tcpdf/tcpdf.php');

/**
 * Carregar configuração do sistema
 */
include_once(CONFIG_PATH . '/config.php');


/**
 * Checar se a configuração do Web Server está OK
 */
if ($_SERVER['DOCUMENT_ROOT']) {
	include_once(DOC_ROOT		.'/check.php');
}

/**
 * Gerenciamento de sessão
 */
if ($_SERVER['DOCUMENT_ROOT']) {
	include_once('session.php');
}

/**
 * Alterar o parâmetro do php para fazer buffer
 */
ini_set('output_buffer',65535);

/**
 * Inicializar o sistema
 */
if ($_SERVER['DOCUMENT_ROOT']) {
	include_once(DOC_ROOT	. '/system.php');
}
