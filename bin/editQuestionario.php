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
if (!isset($codQuestionario)) {
	DHCErro::halt('Falta de Parâmetros (COD_Questionario)');
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());


#################################################################################
## Verificar os dados postados
#################################################################################
if (empty($codQuestionario)) {
	$nome			= null;
	$codStatus		= null;
	$codTipo		= null;
}else{
	$info			= questionario::getInfo($codQuestionario);
	$nome			= $info->NOME;
	$codStatus		= $info->COD_STATUS;
	$codTipo		= $info->COD_TIPO;
}


#################################################################################
## Resgatar os dados das combos (select)
#################################################################################
$oStatus	= $system->geraHtmlCombo(usuario::listaStatus(), 'CODIGO', 'NOME', $codStatus, null);
$oTipos		= $system->geraHtmlCombo(questionario::listaTipos(), 'CODIGO', 'NOME', $codTipo, null);


#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('LOCALIZACAO'			,$local);
$template->assign('NOME'				,$nome);
$template->assign('STATUS'				,$oStatus);
$template->assign('TIPOS'				,$oTipos);
$template->assign('COD_QUESTIONARIO'	,$codQuestionario);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/cadQuestionario.php?id='.$id);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
