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
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->getTipoUsuario());

#################################################################################
## Resgatar os dados do grid
#################################################################################
$gridData		= usuario::lista();

#################################################################################
## Cria o objeto do Grid (bootstrap) 
#################################################################################
$grid   		= new zgBSGrid("Usuario");
$grid->setSkin($system->config->skin);
$grid->adicionaColuna('USUARIO'				,10	,'center'		,'ro'			,'USUARIO');
$grid->adicionaColuna('NOME'				,20	,'center'		,'ro'			,'NOME');
$grid->adicionaColuna('TIPO'				,20	,'center'		,'ro'			,'TIPO_USUARIO');
$grid->adicionaColuna('EMAIL'				,25	,'center'		,'ro'			,'EMAIL');
$grid->adicionaColuna('STATUS'				,10	,'center'		,'ro'			,'STATUS');
$grid->adicionaBotao('but-edit');
$grid->adicionaBotao('but-remove');
$grid->adicionaIcone('icon-key','Alterar senha');
$grid->adicionaIcone('icon-building','Ajustar acesso aos Templos');
$grid->loadObjectArray($gridData);

for ($i = 0; $i < sizeof($gridData); $i++) {
	$id		= DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codUsuario='.$gridData[$i]->CODIGO);
	$grid->setValorColuna($i,5,BIN_URL.'/editUsuario.php?id='.$id);
	$grid->setValorColuna($i,6,BIN_URL.'/excUsuario.php?id='.$id);
	$grid->setValorColuna($i,7,BIN_URL.'/alteraSenha.php?id='.$id);
	$grid->setValorColuna($i,8,BIN_URL.'/usuarioTemplo.php?id='.$id);
}

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . 'cadTemplate.html');

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd			= BIN_URL.'editUsuario.php?id='.DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codUsuario='); 

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('GRID'				,$grid->getHtmlCode());
$template->assign('LOCALIZACAO'			,$local);
$template->assign('NOME'				,'Usuários');
$template->assign('URLADD'				,$urlAdd);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
