<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Resgata variáveis do formulário **/
if (!isset($usuario)) {
	$usuario	= null;
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . 'login.html');

/** Define os valores das variáveis **/
$template->assign('URL_FORM',$_SERVER['REQUEST_URI']);
$template->assign('MENSAGEM',$mensagem);
$template->assign('USUARIO', $usuario);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>