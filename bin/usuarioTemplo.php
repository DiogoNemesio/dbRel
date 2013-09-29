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
	DHCErro::halt('Falta de parâmetros !!!');
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());

#################################################################################
## Verificar os dados postados
#################################################################################
$info			= usuario::getInfo($codUsuario);
if (empty($info->CODIGO)) DHCErro::halt('Usuário não encontrado !!!');

#################################################################################
## Gerar as checkboxs dos templos
#################################################################################
$templos	= usuario::listaPermissoesTemplos($codUsuario);
$cbs		= '';
for ($i = 0; $i < sizeof($templos); $i++) {
	$checked 	= ($templos[$i]->TEM_PERMISSAO == 1) ? "checked" : "";
	$disabled	= ($info->COD_TIPO	== "A") ? "disabled" : "";
	$cbs	.= '<label class="checkbox"><input type="checkbox" '.$checked.' '.$disabled.' id="'.$templos[$i]->NOME.'ID" name="TP_'.$templos[$i]->CODIGO.'" value="'.$templos[$i]->CODIGO.'">'.$templos[$i]->NOME.'</label>';
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
$template->assign('ID'					,$id);
$template->assign('USUARIO'				,$info->USUARIO);
$template->assign('COD_USUARIO'			,$codUsuario);
$template->assign('TEMPLOS_CB'			,$cbs);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/cadUsuario.php?id='.$id);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
