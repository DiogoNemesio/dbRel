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
if (!isset($codQuestionario)) DHCErro::halt('Falta de parâmetros (COD_QUESTIONARIO)');

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = '<br />';

#################################################################################
## Resgatar os dados do grid
#################################################################################
$gridData		= pergunta::lista($codQuestionario);

#################################################################################
## Cria o objeto do Grid (bootstrap) 
#################################################################################
$grid   		= new zgBSGrid("Perguntas");
$grid->setSkin($system->config->skin);
$grid->adicionaColuna('ORDEM'				,5	,'center'		,'ro'			,'ORDEM');
$grid->adicionaColuna('DESCRICAO'			,50	,'center'		,'ro'			,'DESCRICAO');
$grid->adicionaColuna('TIPO'				,12	,'center'		,'ro'			,'TIPO');
$grid->adicionaColuna('STATUS'				,10	,'center'		,'ro'			,'STATUS');
$grid->adicionaColuna('OBRIGATORIO'			,10	,'center'		,'ro'			,'OBRIGATORIO');
$grid->adicionaBotao('but-edit');
$grid->adicionaBotao('but-remove');
$grid->loadObjectArray($gridData);

for ($i = 0; $i < sizeof($gridData); $i++) {
	$id		= DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codQuestionario='.$codQuestionario.'&codPergunta='.$gridData[$i]->CODIGO);
	$grid->setValorColuna($i,5,BIN_URL.'/editPergunta.php?id='.$id);
	$grid->setValorColuna($i,6,BIN_URL.'/excPergunta.php?id='.$id);
	
}

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . '/cadTemplate.html');

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd			= BIN_URL.'editPergunta.php?id='.DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codQuestionario='.$codQuestionario.'&codPergunta='); 

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('GRID'				,$grid->getHtmlCode());
$template->assign('LOCALIZACAO'			,$local);
$template->assign('NOME'				,'Perguntas &nbsp; <a class="btn btn-warn"  href="'.BIN_URL.'/cadQuestionario.php?id='.$id.'"><i class="icon-repeat icon-white"></i></a>');
$template->assign('URLADD'				,$urlAdd);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();