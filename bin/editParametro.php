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
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

#################################################################################
## Descompacta o ID
#################################################################################
DHCUtil::descompactaId($id);

#################################################################################
## Verifica se o usuário tem permissão no menu
#################################################################################
$system->checaPermissao($_codMenu_);


#################################################################################
## Verifica se algumas variáveis estão OK
#################################################################################

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());

#################################################################################
## Definição de variáveis
#################################################################################
$param1			= "BS_TA_TIMEOUT (".$system->parametros["BS_TA_TIMEOUT"]->getDescricao().")";
$param2			= "BS_TA_ITENS (".$system->parametros["BS_TA_ITENS"]->getDescricao().")";
$param3			= "BS_TA_MINLENGTH (".$system->parametros["BS_TA_MINLENGTH"]->getDescricao().")";

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('LOCALIZACAO'			,$local);
$template->assign('PARAM1'				,$param1);
$template->assign('PARAM2'				,$param2);
$template->assign('PARAM3'				,$param3);
$template->assign('BS_TA_TIMEOUT'		,$system->parametros["BS_TA_TIMEOUT"]->getValor());
$template->assign('BS_TA_ITENS'			,$system->parametros["BS_TA_ITENS"]->getValor());
$template->assign('BS_TA_MINLENGTH'		,$system->parametros["BS_TA_MINLENGTH"]->getValor());
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
