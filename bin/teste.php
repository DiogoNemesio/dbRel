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
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('URLADD'				,null);



#################################################################################
## Por fim exibir a página HTML
#################################################################################
print_r(usuario::listaPermissoesTemplos('3'));
//echo $system->parametros["BS_TA_TIMEOUT"]->getValor();

//echo $template->getHtmlCode();
