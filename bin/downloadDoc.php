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
if (isset($_POST['codConsulta'])) {
	$codConsulta 	= DHCUtil::antiInjection($_POST['codConsulta']);
}elseif (isset($_GET['codConsulta'])) {
	$codConsulta 	= DHCUtil::antiInjection($_GET['codConsulta']);
}else{
	DHCErro::halt('Parâmetro não informado !!!');
}

if (isset($_POST['codDoc'])) {
	$codDoc 	= DHCUtil::antiInjection($_POST['codDoc']);
}elseif (isset($_GET['codConsulta'])) {
	$codDoc 	= DHCUtil::antiInjection($_GET['codDoc']);
}else{
	DHCErro::halt('Parâmetro não informado (2) !!!');
}


#################################################################################
## Resgata as informações da consulta
#################################################################################
$info	= consulta::getInfo($codConsulta);
if (!$info) {
	DHCErro::halt('Consulta não encontrada !!! ('.$codConsulta.')');
}

#################################################################################
## Resgata as informações do Paciente
#################################################################################
$infoPaciente	= paciente::getInfo($info->COD_PACIENTE);

#################################################################################
## Resgata o documento
#################################################################################
$documento		= consulta::getDoc($codDoc);
if ($documento == NULL) {
	DHCErro::halt("Documento não encontrado !!!");
}else{
	$finfo = new finfo(FILEINFO_MIME);
	$tipo =  $finfo->buffer($documento->ARQUIVO);
	$regex1 = "#([^\;]+)#";
	$regex2 = "#([^\/]+)#";
	preg_match_all($regex1, $tipo, $match1);
	preg_match_all($regex2, $match1[0][0], $match2);
	$ext			= $match2[0][1];
	$nomeArquivo	= $documento->NOME. "." . $ext;
}

#################################################################################
## manda a requisição para o browser
#################################################################################
DHCUtil::sendHeaderDownload($nomeArquivo,$tipo);
echo $documento->ARQUIVO;


