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
	DHCErro::halt('Falta de Parâmetros (COD_USUARIO) 2'); 
}else{
	$info			= usuario::getInfo($codUsuario);
	$nome			= $info->NOME;
	$mensagem		= "Tem certeza que deseja excluir o usuário ";
}


#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . "excTemplate.html");

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('LOCALIZACAO'			,$local);
$template->assign('NOME'				,$nome);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/cadUsuario.php?id='.$id);
$template->assign('EXCTITLE'			,"Exclusão de Usuário");
$template->assign('MENSAGEM'			,$mensagem);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
