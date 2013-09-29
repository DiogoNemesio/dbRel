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
/*if (!isset($codTemplo)) {
	echo "<script>alert('Erro variável codTemplo perdida !!!!');</script>";
	DHCErro::halt('Falta de Parâmetros (COD_TEMPLO)');
}*/


#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());

#################################################################################
## Resgatar os dados do grid
#################################################################################
$gridData		= templo::lista();


#################################################################################
## Cria o objeto do Grid (bootstrap) 
#################################################################################
$grid   		= new zgBSGrid("Templo");
$grid->setSkin($system->config->skin);
$grid->adicionaColuna('NOME'				,20	,'center'		,'ro'			,'NOME');
$grid->adicionaColuna('EMAIL'				,30	,'center'		,'ro'			,'EMAIL');
$grid->adicionaColuna('CIDADE'				,20	,'center'		,'ro'			,'CIDADE');
$grid->adicionaBotao('but-edit');
$grid->adicionaBotao('but-remove');
$grid->loadObjectArray($gridData);

for ($i = 0; $i < sizeof($gridData); $i++) {
	$id		= DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codTemplo='.$gridData[$i]->CODIGO);
	$grid->setValorColuna($i,3,BIN_URL.'/editTemplo.php?id='.$id);
	$grid->setValorColuna($i,4,BIN_URL.'/excTemplo.php?id='.$id);
}

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . '/cadTemplate.html');

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd			= BIN_URL.'editTemplo.php?id='.DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codTemplo='); 

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('GRID'				,$grid->getHtmlCode());
$template->assign('LOCALIZACAO'			,$local);
$template->assign('NOME'				,'Templos');
$template->assign('URLADD'				,$urlAdd);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
