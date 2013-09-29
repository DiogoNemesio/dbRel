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
## Resgata as variáveis postadas
#################################################################################
if (isset($_GET['codPaciente']))	$codPaciente	= DHCUtil::antiInjection($_GET["codPaciente"]);
if (isset($_GET['codConsulta']))	$codConsulta	= DHCUtil::antiInjection($_GET["codConsulta"]);

#################################################################################
## Verifica se algumas variáveis estão OK
#################################################################################
if (!isset($codPaciente) || empty($codPaciente)) {
	DHCErro::halt('Falta de Parâmetros (COD_PACIENTE)');
}
if (!isset($codConsulta)) {
	DHCErro::halt('Falta de Parâmetros (COD_CONSULTA 1)');
}

#################################################################################
## Verificar os dados postados
#################################################################################
if (!empty($codConsulta)) {
	$info			= consulta::getInfo($codConsulta);
	$obs			= $info->OBS;
	$orientacoes	= $info->ORIENTACOES;
	$data			= $info->DATA_FORMATADA;
}else{
	$obs			= null;
	$orientacoes	= null;
	$data			= null;
}

$questHtml			= "";
$examesHtml			= "";
$fotosHtml			= "";

#################################################################################
## Resgata os questionarios já respondidos
#################################################################################
$quests	= paciente::getQuestionariosConsultaRespondidos($codConsulta);
for ($i=0; $i < sizeof($quests); $i++) {
	$questHtml	.= "<li id='questLi".$i."' codPaciente='".$codPaciente."' codConsulta='".$codConsulta."'><a href=\"javascript:geraPdfQuest('".$codConsulta."','".$quests[$i]->CODIGO."');\">".$quests[$i]->NOME."</a></li>"; 
}

#################################################################################
## Resgata os documentos
#################################################################################
$exames	= consulta::getDocs($codConsulta,"E");
for ($i=0; $i < sizeof($exames); $i++) {
	$examesHtml	.= "<li id='docsLi".$i."' codPaciente='".$codPaciente."' codConsulta='".$codConsulta."'><a href=\"javascript:downloadDoc('".$codConsulta."','".$exames[$i]->CODIGO."');\">".$exames[$i]->NOME."</a></li>";
}

$fotos	= consulta::getDocs($codConsulta,"F");
for ($i=0; $i < sizeof($fotos); $i++) {
	$fotosHtml	.= "<li id='docsLi".$i."' codPaciente='".$codPaciente."' codConsulta='".$codConsulta."'><a href=\"javascript:downloadDoc('".$codConsulta."','".$fotos[$i]->CODIGO."');\">".$fotos[$i]->NOME."</a></li>";
}

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('COD_PACIENTE'		,$codPaciente);
$template->assign('COD_CONSULTA'		,$codConsulta);
$template->assign('OBS'					,$obs);
$template->assign('ORIENTACOES'			,$orientacoes);
$template->assign('QUESTIONARIOS'		,$questHtml);
$template->assign('DOC_EXAMES'			,$examesHtml);
$template->assign('DOC_FOTOS'			,$fotosHtml);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/listaPacienteConsulta.php?id='.$id);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
