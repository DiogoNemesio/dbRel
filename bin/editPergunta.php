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
	DHCErro::halt('Falta de Parâmetros (COD_QUESTIONARIO)');
}

if (!isset($codPergunta)) {
	DHCErro::halt('Falta de Parâmetros (COD_PERGUNTA)');
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());

#################################################################################
## Verificar os dados postados
#################################################################################
if (empty($codPergunta)) {
	$descricao		= null;
	$codTipo		= null;
	$codStatus		= null;
	$ordem			= (pergunta::getMaiorOrdem($codQuestionario) + 1);
	$codObrigatorio	= null;
	$hidden			= "hide";
}else{
	$info			= pergunta::getInfo($codPergunta);
	$descricao		= $info->DESCRICAO;
	$codTipo		= $info->COD_TIPO;
	$codStatus		= $info->COD_STATUS;
	$ordem			= $info->ORDEM;
	$codObrigatorio	= $info->COD_OBRIGATORIO;
	if ($codTipo == "L") {
		$hidden		= null;
	}else{
		$hidden		= "hide";
	}
}


#################################################################################
## Resgatar os dados das combos (select)
#################################################################################
$oTipos		= $system->geraHtmlCombo(pergunta::listaTipos(), 'CODIGO', 'NOME', $codTipo, null);
$oStatus	= $system->geraHtmlCombo(pergunta::listaStatus(), 'CODIGO', 'NOME', $codStatus, null);
$oObrig		= $system->geraHtmlCombo(pergunta::listaTiposObrigatorio(), 'CODIGO', 'NOME', $codObrigatorio, null);



#################################################################################
## Resgatar os valores para a pergunta
#################################################################################
if ($codTipo == "L") {
	$array		= pergunta::listaValores($codPergunta);
	$valores	= '';
	for ($i=0; $i < sizeof($array); $i++) {
		$valores .= '<div id="DivValor999999'.$i.'" class="box-content"><div class="input-append"><input class="input-large" name="campos[]" type="text" disabled value="'.$array[$i]->COD_VALOR.'"><button class="btn" onclick="return false;" id="addValID999999'.$i.'"><i class="icon-info-sign icon-white"></i></button></a></div></div>';
	}
}else{
	$valores	= null;
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
$template->assign('COD_QUESTIONARIO'	,$codQuestionario);
$template->assign('COD_PERGUNTA'		,$codPergunta);
$template->assign('DESCRICAO'			,$descricao);
$template->assign('TIPOS'				,$oTipos);
$template->assign('STATUS'				,$oStatus);
$template->assign('OBRIGATORIO'			,$oObrig);
$template->assign('ORDEM'				,$ordem);
$template->assign('HIDDEN'				,$hidden);
$template->assign('VALORES'				,$valores);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/cadPergunta.php?id='.$id);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
