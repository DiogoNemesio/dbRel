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
if (!isset($codTemplo)) {
	DHCErro::halt('Falta de Parâmetros (COD_TEMPLO)');
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());


#################################################################################
## Verificar os dados postados
#################################################################################
if (empty($codTemplo)) {
	$nome			= null;
	$email			= null;
	$codCidade		= null;
	$endereco		= null;
	$bairro			= null; 
	$cidade			= null;
}else{
	$info			= templo::getInfo($codTemplo);
	$nome			= $info->NOME;
	$email			= $info->EMAIL;
	$codCidade		= $info->COD_CIDADE;
	$endereco		= $info->ENDERECO;
	$bairro			= $info->BAIRRO;
	$infoCidade		= cidade::getInfo($codCidade);
	$cidade			= $infoCidade->UF_CIDADE;
}

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
$template->assign('EMAIL'				,$email);
$template->assign('CIDADE'				,$cidade);
$template->assign('ENDERECO'			,$endereco);
$template->assign('BAIRRO'				,$bairro);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/cadTemplo.php?id='.$id);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
