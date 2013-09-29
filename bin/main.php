<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
include_once(BIN_PATH . 'auth.php');


/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . 'main.html');

/** Cria o objeto do Menu DHTMLX **/
$menu   = new zgBSNavBar("Main");

/** Carrega os menus do banco **/
$menus	= $system->DBGetMenuItens($system->getCodUsuario());



/** Adiciona os menus no objeto **/
for ($i = 0; $i < sizeof($menus); $i++) {
	//echo "$i = ".$menus[$i]->NOME."<BR>";
	$menu->addMenu($menus[$i]->CODIGO,$menus[$i]->NOME,$menus[$i]->DESCRICAO,$menus[$i]->COD_TIPO,$menus[$i]->LINK,$menus[$i]->NIVEL,$menus[$i]->COD_MENU_PAI,$menus[$i]->ICONE,null);
}

/** Gera o código HTML e Javascript do Menu **/
$menu->render();

/** Define os valores das variáveis **/
$template->assign('NAVBAR',$menu->getHTML());
$template->assign('URL_FORM',$_SERVER['REQUEST_URI']);



/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>