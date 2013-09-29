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

if (!isset($codPaciente) || !isset($codTipo)) exit;
if (!isset($codConsulta)) 		$codConsulta		= null;

#################################################################################
## Resgata os documentos já transferidos
#################################################################################
$qDocs	= consulta::getDocs($codConsulta);

#################################################################################
## Resgatar os dados das combos (select)
#################################################################################
$oTiposDoc	= $system->geraHtmlCombo(consulta::getTiposDoc(), 'CODIGO', 'NOME', '', '');

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('TIPOS_DOC'				,$oTiposDoc);
$template->assign('COD_PACIENTE'			,$codPaciente);
$template->assign('COD_CONSULTA'			,$codConsulta);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
