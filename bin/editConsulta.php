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
if (!isset($codPaciente) || empty($codPaciente)) {
	DHCErro::halt('Falta de Parâmetros (COD_PACIENTE)');
}

#################################################################################
## Verificar os dados postados
#################################################################################
if (paciente::existe($codPaciente)) {
	$info			= paciente::getInfo($codPaciente);
	$nome			= $info->NOME;
	$idade			= $info->IDADE;
	$fone			= $info->TELEFONE;
	$celular		= $info->CELULAR;
	$cidade			= $info->CIDADE;
	$dataNasc		= $info->NASCIMENTO;
	
	$infoPaciente	= consulta::addInfoPacienteHeader("Nome", $nome, 'input-large');
	
	if (!empty($idade)) 	$infoPaciente .= consulta::addInfoPacienteHeader("Nasc:", $dataNasc. ' ('.$idade.') anos', 'input-medium');
	if (!empty($cidade)) 	$infoPaciente .= consulta::addInfoPacienteHeader("Cidade", $cidade, 'input-medium');
	if (!empty($fone)) 		$infoPaciente .= consulta::addInfoPacienteHeader("Fone", $fone, 'input-medium');
	if (!empty($celular)) 	$infoPaciente .= consulta::addInfoPacienteHeader("Cel", $celular, 'input-medium');

}else{
	DHCErro::halt('Paciente não encontrado');
}

#################################################################################
## Monta o menu com o histórico das consultas
#################################################################################
$hist		= consulta::lista($codPaciente);
$histHtml	= "";
$oldData	= "";
$j			= 1;
for ($i = 0; $i < sizeof($hist); $i++) {
	if ($oldData == $hist[$i]->DATA_FORMATADA) {
		$j++;
	}else{
		$j	= 1;
	}
	$histHtml 	.= "<li id='conLi".$i."' codPaciente='".$codPaciente."' sysid='".$id."' codConsulta='".$hist[$i]->CODIGO."'><a href='#'>".$hist[$i]->DATA_FORMATADA." (".$j.")</a></li>";
	$oldData	= $hist[$i]->DATA_FORMATADA;
}

$consultaAtual		= "Nova consulta";
//$infoPaciente		= '<div class="input-prepend" style="height:10px; padding: 0px 0px 0px 0px;"><span class="add-on" style="height:20px; padding: 0px 0px 0px 0px;">Nome</span><input style="height:20px; padding: 0px 0px 0px 0px;" type="text" value="'.$nome.'"></div>';


#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('COD_PACIENTE'		,$codPaciente);
$template->assign('INFO'				,$infoPaciente);
$template->assign('CONSULTA_ATUAL'		,$consultaAtual);
$template->assign('HISTORICO'			,$histHtml);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('URLVOLTAR'			,BIN_URL.'/cadQuestionario.php?id='.$id);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
