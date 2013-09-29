<?php

#################################################################################
## Definir o buffer de saída para poder fazer o Header da imagem
#################################################################################


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
if (!isset($codPaciente)) {
	DHCErro::halt('Falta de Parâmetros (COD_PACIENTE)');
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());


#################################################################################
## Verificar os dados postados
#################################################################################
$info			= paciente::getInfo($codPaciente);
if ((isset($info->CODIGO)) && ($codPaciente == $info->CODIGO)) {
	$foto			= $info->FOTO;

	$finfo = new finfo(FILEINFO_MIME);
	
	$system->log->debug->debug('Type: '.$finfo->buffer($foto));
	$mime	= $finfo->buffer($foto);
	
	if (empty($foto)) {
		$file	= IMG_PATH . "semFoto.jpg";
		$mime	= $finfo->file($file);
		$foto	= readfile($file);
	}
	header('Content-type: '.$mime);
	echo $foto;
	
}else{
	DHCErro::halt('Paciente não encontrado !!!');
}
