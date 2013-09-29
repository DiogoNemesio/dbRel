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
if (!isset($codUsuario)) {
	DHCErro::halt('Falta de Parâmetros (COD_USUARIO)');
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());


#################################################################################
## Verificar os dados postados
#################################################################################
if (empty($codUsuario)) {
	$usuario		= null;
	$nome			= null;
	$codTipo		= null;
	$email			= null;
	$telefone		= null;
	$celular		= null;
	$codStatus		= null;
}else{
	$info			= usuario::getInfo($codUsuario);
	$usuario		= $info->USUARIO;
	$nome			= $info->NOME;
	$codTipo		= $info->COD_TIPO;
	$email			= $info->EMAIL;
	$telefone		= $info->TELEFONE;
	$celular		= $info->CELULAR;
	$codStatus		= $info->COD_STATUS;
}


#################################################################################
## Resgatar os dados das combos (select)
#################################################################################
$oTipos		= $system->geraHtmlCombo(usuario::listaTipos("V"), 'CODIGO', 'NOME', $codTipo, null);
$oStatus	= $system->geraHtmlCombo(usuario::listaStatus(), 'CODIGO', 'NOME', $codStatus, null);


#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('LOCALIZACAO'			,$local);
$template->assign('COD_USUARIO'			,$codUsuario);
$template->assign('USUARIO'				,$usuario);
$template->assign('NOME'				,$nome);
$template->assign('TIPOS'				,$oTipos);
$template->assign('EMAIL'				,$email);
$template->assign('TELEFONE'			,$telefone);
$template->assign('CELULAR'				,$celular);
$template->assign('STATUS'				,$oStatus);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/cadVoluntario.php?id='.$id);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
