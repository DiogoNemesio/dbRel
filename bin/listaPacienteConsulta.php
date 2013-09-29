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
$gridData		= paciente::lista();

#################################################################################
## Cria o objeto do Grid (bootstrap) 
#################################################################################
$grid   		= new zgBSGrid("Pacientes");
$grid->setSkin($system->config->skin);
$grid->adicionaColuna('NOME'				,20	,'center'		,'ro'			,'NOME');
$grid->adicionaColuna('SEXO'				,10	,'center'		,'ro'			,'SEXO');
$grid->adicionaColuna('NASCIMENTO'			,8	,'center'		,'ro'			,'NASCIMENTO');
$grid->adicionaColuna('IDADE'				,6	,'center'		,'ro'			,'IDADE');
$grid->adicionaColuna('CIDADE'				,20	,'center'		,'ro'			,'CIDADE');
$grid->adicionaColuna('BAIRRO'				,12	,'center'		,'ro'			,'BAIRRO');
$grid->adicionaIcone('icon-stethoscope','Visualizar as Consulta');
$grid->loadObjectArray($gridData);

for ($i = 0; $i < sizeof($gridData); $i++) {
	$id		= DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codPaciente='.$gridData[$i]->CODIGO);
	$grid->setValorColuna($i,6,BIN_URL.'/editConsulta.php?id='.$id);
}

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd			= BIN_URL.'editPaciente.php?id='.DHCUtil::encodeUrl('_codMenu_='.$_codMenu_.'&codPaciente='); 

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('GRID'				,$grid->getHtmlCode());
$template->assign('LOCALIZACAO'			,$local);
$template->assign('NOME'				,'Lista de Pacientes');
$template->assign('URLADD'				,$urlAdd);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
