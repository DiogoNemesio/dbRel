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

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());

#################################################################################
## Resgatar os dados do grid
#################################################################################
$gridData		= questionario::lista();


#################################################################################
## Cria o objeto do Grid (bootstrap) 
#################################################################################
$grid   		= new zgBSGrid("Questionario");
$grid->setSkin($system->config->skin);
$grid->adicionaColuna('NOME'				,40	,'center'		,'ro'			,'NOME');
$grid->adicionaColuna('STATUS'				,20	,'center'		,'ro'			,'STATUS');
$grid->adicionaColuna('TIPO'				,20	,'center'		,'ro'			,'TIPO');
$grid->adicionaBotao('but-edit');
$grid->adicionaBotao('but-remove');
$grid->adicionaIcone('icon-question-sign','Gerenciar Perguntas');
$grid->loadObjectArray($gridData);

for ($i = 0; $i < sizeof($gridData); $i++) {
	$id		= DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codQuestionario='.$gridData[$i]->CODIGO);
	$grid->setValorColuna($i,3,BIN_URL.'/editQuestionario.php?id='.$id);
	$grid->setValorColuna($i,4,BIN_URL.'/excQuestionario.php?id='.$id);
	$grid->setValorColuna($i,5,BIN_URL.'/cadPergunta.php?id='.$id);
}

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . '/cadTemplate.html');

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd			= BIN_URL.'editQuestionario.php?id='.DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codQuestionario='); 

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('GRID'				,$grid->getHtmlCode());
$template->assign('LOCALIZACAO'			,$local);
$template->assign('NOME'				,'Questionarios');
$template->assign('URLADD'				,$urlAdd);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
