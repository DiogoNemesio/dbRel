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
if (!isset($codPaciente)) {
	DHCErro::halt('Falta de Parâmetros (COD_PACIENTE)');
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_FILES['foto']))			$foto			= $_FILES["foto"];
$err = null;

if (isset($foto)) {


	## Pega as dimensões da imagem ##
	$dimensoes = getimagesize($foto["tmp_name"]);

	//$system->log->debug->debug('Dimensoes'. serialize($dimensoes));
	## Verifica se a largura da imagem é maior que a largura permitida ##
/*	if ($dimensoes[0] > 200) {
		$err = "A largura da imagem não deve ultrapassar 200 pixels";
	}

	## Verifica se a altura da imagem é maior que a altura permitida ##
	if($dimensoes["height"] > 300) {
		$err = "Altura da imagem não deve ultrapassar 300 pixels";
	}

	## Verifica se o tamanho da imagem é maior que o tamanho permitido ##
	if($foto["size"] > 1024000) {
		$err = "A imagem deve ter no máximo 1 mb";
	}
	
	*/

	if ($err == null) {
		$err = paciente::alterafoto($codPaciente, $foto["tmp_name"]);
	}
	
}


if ($err) {
	DHCErro::halt('Erro: '.$err);

}


################################################################################
## Verificar os dados postados
#################################################################################
$info			= paciente::getInfo($codPaciente);
if ((isset($info->CODIGO)) && ($codPaciente == $info->CODIGO)) {
	$foto			= $info->FOTO;
}else{
	DHCErro::halt('Paciente não encontrado !!!');
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
$template->assign('COD_PACIENTE'		,$codPaciente);
$template->assign('ID'					,$id);
$template->assign('DP'					,DHCUtil::getCaminhoCorrespondente(__FILE__, 'dp',ZG_URL));
$template->assign('FORM_URL'			,$_SERVER["PHP_SELF"]);
$template->assign('URLVOLTAR'			,BIN_URL.'/cadPaciente.php?id='.$id);


#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
