<?php
#################################################################################
## Includes
#################################################################################
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

#################################################################################
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['mensagem']))	$mensagem	= DHCUtil::antiInjection($_GET["mensagem"]);
if (!isset($mensagem))			$mensagem 	= null;

#################################################################################
## Checa o tipo da mensagem
#################################################################################
if ((substr($mensagem,0,1) == "0") || (!$mensagem) || ($mensagem == null)) {
	$tipo	= "success";
	$icon	= "icon-ok";
	$classe	= "";
	$titulo = "Mensagem";
}elseif ((substr($mensagem,0,1) == "2")){
	$tipo	= "warning";
	$icon	= "icon-info-sign";
	$classe	= "btn-warning";
	$titulo = "Aviso";
}else{
	$tipo	= "error";
	$icon	= "icon-exclamation-sign icon-white";
	$titulo = "Mensagem de erro";
	$classe	= "btn-danger";
}

$decodeMsg	= DHCUtil::decodeUrl(substr($mensagem,1));

$array		= explode("|",$decodeMsg);
if (sizeof($array) == 1) {
	$msg	= $array[0];
}else{
	$msg		= $array[sizeof($array)-1];
}


#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('TITULO'				,$titulo);
$template->assign('MENSAGEM'			,$msg);
$template->assign('ICON'				,$icon);
$template->assign('TIPO'				,$tipo);
$template->assign('CLASSE'				,$classe);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
