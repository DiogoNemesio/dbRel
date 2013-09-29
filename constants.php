<?php

/**
 * Constantes do Sistema
 */

/** Checa se a constante DOC_ROOT está definida **/
if (!defined('DOC_ROOT')) {
	die('Constante DOC_ROOT não definida !!! (constants)');
}

/**
 * URL Raiz
 */
define('ROOT_URL','http://dbrel');

/**
 * Caminho onde ficam os módulos compartilhados
 */
define('SHARED_PATH',DOC_ROOT);


/**
 * Caminho onde ficam as classes
 */
define('SHARED_CLASS_PATH',SHARED_PATH . '/classes/');
define('CLASS_PATH',DOC_ROOT . '/classes/');

/**
 * Caminho onde ficam as packages
 */
define('PKG_PATH',SHARED_PATH . '/packages/');
define('PKG_URL',ROOT_URL . '/packages/');

/**
 * Caminho onde ficam os arquivos PHP executáveis 
 */
define('BIN_PATH',DOC_ROOT . '/bin/');
define('BIN_URL', ROOT_URL . '/bin/');

/**
 * Caminho onde ficam os arquivos de configuração
 */
define('CONFIG_PATH',DOC_ROOT . '/etc/');
define('CONFIG_URL',ROOT_URL . '/etc/');

/**
 * Caminho onde ficam os arquivos html
 */
define('HTML_PATH',DOC_ROOT . '/html/');
define('HTML_URL',ROOT_URL . '/html/');

/**
 * Caminho onde ficam os arquivos de log
 */
define('LOG_PATH',DOC_ROOT . '/log/');
define('LOG_URL',ROOT_URL . '/log/');

/**
 * Caminho onde ficam as rotinas de terceiros e fontes
 */
define('SRC_PATH',DOC_ROOT . '/src/');
define('SRC_URL',ROOT_URL . '/src/');

/**
 * Caminho onde ficam as imagens
 */
define('IMG_PATH',DOC_ROOT . '/imgs/');
define('IMG_URL',ROOT_URL . '/imgs/');

/**
 * Caminho onde ficam os CSS
 */
define('CSS_PATH',DOC_ROOT . '/css/');
define('CSS_URL',ROOT_URL . '/css/');

/**
 * Caminho onde ficam os Javascripts
 */
define('JS_PATH',DOC_ROOT . '/js/');
define('JS_URL',ROOT_URL . '/js/');

/**
 * Caminho onde ficam os XMLs
 */
define('XML_PATH',DOC_ROOT . '/xml/');
define('XML_URL',ROOT_URL . '/xml/');

/**
 * Caminho do dataProcessor
 */
define('DP_PATH',DOC_ROOT . '/dp/');
define('DP_URL',ROOT_URL . '/dp/');

/**
 * Diretório raiz do PEAR
 */
define('PEAR_ROOT','/usr/share/php5/PEAR/');

/**
 * Prioridade de LOG (Zend Framework)
 */
define ('DHC_USER',8);

/**
 * Indicadores de senha criptografadas
 */
define('DHC_SENHA_NAO_ESCONDIDA','N');
define('DHC_SENHA_ESCONDIDA','C');


/**
 * indicadores do tipo do caminho (caminho absoluto ou url)
 */
define('ZG_PATH',1);
define('ZG_URL' ,2);

/**
 * URL do Menu inicial
 */
define('MENU_URL',ROOT_URL . '/index.php');