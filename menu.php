<?php
include_once('include.php');

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . 'menu.html');

/** Define os valores das variáveis **/
$template->assign('ROOT_URL',ROOT_URL);
$template->assign('CSS_URL'	,CSS_URL);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();


?>