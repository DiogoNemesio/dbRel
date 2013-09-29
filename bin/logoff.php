<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . 'logoff.html');

/** Define os valores das variáveis **/
$template->assign('MENSAGEM','Para entrar novamente no sistema click no botão abaixo !!!');
$template->assign('TITULO','Sistema encerrado');
$template->assign('URL_FORM',ROOT_URL);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

$system->db->desconectar();
session_unset();
session_destroy();
unset($system);