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
if (isset($_GET['codPaciente']))		$codPaciente		= DHCUtil::antiInjection($_GET["codPaciente"]);
if (isset($_GET['codConsulta']))		$codConsulta		= DHCUtil::antiInjection($_GET["codConsulta"]);
if (isset($_GET['codTipo']))			$codTipo			= DHCUtil::antiInjection($_GET["codTipo"]);
if (isset($_GET['codQuestionario']))	$codQuestionario	= DHCUtil::antiInjection($_GET["codQuestionario"]);

if (!isset($codPaciente) || !isset($codTipo)) exit;
if (!isset($codConsulta)) 		$codConsulta		= null;
if (!isset($codQuestionario)) 	$codQuestionario	= null;

#################################################################################
## Resgata os questionarios já respondidos
#################################################################################
$qResp	= paciente::getQuestionariosAuxRespondidos($codPaciente);
/*if (sizeof($qResp) > 0) {
	$codQuestionario	= $qResp[0]->CODIGO;
}else{
	$codQuestionario	= '';
}*/

#################################################################################
## Resgatar os dados das combos (select)
#################################################################################
$oQuests	= $system->geraHtmlCombo(questionario::lista($codQuestionario,$codTipo,'A'), 'CODIGO', 'NOME', '', '');


#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('QUESTIONARIOS'			,$oQuests);
$template->assign('COD_QUESTIONARIO'		,$codQuestionario);
$template->assign('COD_PACIENTE'			,$codPaciente);
$template->assign('COD_CONSULTA'			,$codConsulta);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
