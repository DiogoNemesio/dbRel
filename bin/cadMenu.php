<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
#################################################################################
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');
include_once(DOC_ROOT . '/security.php');

#################################################################################
## Resgata os parâmetros passados pelo menu
#################################################################################
if (isset($_GET["id"])) 	{
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST["id"])) 	{
	$id = DHCUtil::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = DHCUtil::antiInjection($id);
}else{
	die('Parâmetro inválido 1');
}

#################################################################################
## Descompacta o ID
#################################################################################
DHCUtil::descompactaId($id);


#################################################################################
##  Recuperar variáveis do form
#################################################################################
if (isset($_POST["codMenu"])) 			$codMenu			= DHCUtil::antiInjection($_POST['codMenu']);
if (isset($_POST["codTipoUsuario"])) 	$codTipoUsuario		= DHCUtil::antiInjection($_POST['codTipoUsuario']);
if (isset($_POST["acao"])) 				$acao				= DHCUtil::antiInjection($_POST['acao']);
if (isset($_POST["codMenuDe"])) 		$codMenuDe			= DHCUtil::antiInjection($_POST['codMenuDe']);
if (isset($_POST["codMenuPara"])) 		$codMenuPara		= DHCUtil::antiInjection($_POST['codMenuPara']);
if (isset($_POST["sMenu"])) 			$sMenu				= DHCUtil::antiInjection($_POST['sMenu']);
if (isset($_POST["sDescricao"])) 		$sDescricao			= DHCUtil::antiInjection($_POST['sDescricao']);
if (isset($_POST["sLink"])) 			$sLink				= DHCUtil::antiInjection($_POST['sLink']);
if (isset($_POST["sIcone"])) 			$sIcone				= DHCUtil::antiInjection($_POST['sIcone']);
if (isset($_POST["aMenu"])) 			$aMenu				= DHCUtil::antiInjection($_POST['aMenu']);
if (isset($_POST["aDescricao"])) 		$aDescricao			= DHCUtil::antiInjection($_POST['aDescricao']);
if (isset($_POST["aCodTipo"])) 			$aCodTipo			= DHCUtil::antiInjection($_POST['aCodTipo']);
if (isset($_POST["aIcone"])) 			$aIcone				= DHCUtil::antiInjection($_POST['aIcone']);
if (isset($_POST["codMenuDel"])) 		$codMenuDel			= DHCUtil::antiInjection($_POST['codMenuDel']);

if (isset($_POST["aLink"])) 			{
	$aLink		= DHCUtil::antiInjection($_POST['aLink']);
}else{
	$aLink		= null;
}

if (!isset($codMenu))					$codMenu		= null;
if (!isset($codTipoUsuario))			$codTipoUsuario	= $system->DBGetTipoUsuario($system->getUsuario());

################################################################################
## Verificar a acao
#################################################################################
if (isset($acao)) {
	
	if ($acao == 'associar') {
		\DBRel\Menu::addMenuTipoUsuario($codMenuDe,$codMenuPara,$codTipoUsuario,$codMenu);
	}elseif ($acao == 'desassociar') {
		\DBRel\Menu::delMenuTipoUsuario($codMenuDe,$codTipoUsuario,$codMenu);
	}elseif ($acao == 'salvar') {
		#################################################################################
		## Resgatar as informações complementares
		#################################################################################
		if ((isset($codMenu)) && ($codMenu != '')) {
			$infoMenu 	= \DBRel\Menu::DBGetInfoMenu($codMenu);
			$return		= \DBRel\Menu::DBSalvaInfoMenu($codMenu,$sMenu,$sDescricao,$infoMenu->CODIGO,$sLink,$infoMenu->NIVEL,$infoMenu->COD_MENU_PAI,$sIcone);
			if ($return) $system->halt($return);
		}
	}elseif ($acao == 'criar') {
		#################################################################################
		## Faz validação dos campos
		#################################################################################
		$valido	= true;
		if (!$aMenu) {
			$aviso	= 'Campo: "Menu" obrigatório !!!';
			$valido	= false;
		}

		if (!$aDescricao) {
			$aviso	= 'Campo: "Descrição" obrigatório !!!';
			$valido	= false;
		}

		if ($valido) {
			$system->log->debug->debug('MenuPai = '.$codMenu);
			$return = \DBRel\Menu::criaMenu($aMenu,$aDescricao,$aCodTipo,$aLink,$codMenu,$aIcone);
			if ($return) $system->halt($return);
		}else{
			$system->halt($aviso,false,false,true);
		}
	}elseif ($acao == 'excluir') {
		if (isset($codMenuDel)) {
			$return = \DBRel\Menu::excluiMenu($codMenuDel);
			if ($return) $system->halt($return);
		}
	}
}

if ((isset($codMenu) && ($codMenu != ''))) {
	$infoMenu	= \DBRel\Menu::DBGetInfoMenu($codMenu);
	if (isset($infoMenu->codTipo)) {
		$sMenu	= $infoMenu->menu;
		$sDesc	= $infoMenu->descricao;
		$sLink	= $infoMenu->link;
		$sIcone	= $infoMenu->icone;

		$hidden		= ' ';
	
		if ($infoMenu->codTipo == 'L') {
			$hidLink	= ' ';
			$hidCad		= 'MCHidden';
		}else{
			$hidLink	= 'MCHidden';
			$hidCad		= ' ';
		}
			
	}else{
		$hidden		= ' ';
		$hidLink	= ' ';
		$hidCad		= ' ';
		$sMenu		= '';
		$sDesc		= '';
		$sLink		= '';
		$sIcone		= '';
	}
	
}else{
	$hidden		= 'MCHidden';
	$hidLink	= 'MCHidden';
	$hidCad		= ' ';
	$sMenu		= '';
	$sDesc		= '';
	$sLink		= '';
	$sIcone		= '';
}


#################################################################################
## Monta os dados do select Tipo de Usuário
#################################################################################
$tiposUsuario	= $system->DBGetListTipoUsuario();
$selTipoUsuario	= '';
foreach ($tiposUsuario as $dados) {
	$selected = ($codTipoUsuario == $dados->CODIGO) ? ' selected ' : ' ';
	$selTipoUsuario	.= "<option $selected value='".$dados->CODIGO."'>".$dados->CODIGO.' - '.$dados->NOME."</option>";
}

#################################################################################
## Monta os dados do select Tipo de de Menu
#################################################################################
$tiposMenu	= \DBRel\Menu::DBGetListTipoMenu();
$selTipoMenu	= '';
foreach ($tiposMenu as $dados) {
	$selTipoMenu	.= "<option value='".$dados->CODIGO."'>".$dados->CODIGO.' - '.$dados->NOME."</option>";
}

$aLocal		= \DBRel\Menu::getArrayArvoreMenu($codMenu);
$local		= "<input type='button' class='MCObject' value='Menu Raiz' onclick='javascript:changeLocal(\"\",\"".$codTipoUsuario."\");'>";
for ($i = 0; $i < sizeof($aLocal); $i++) {
	$info	= \DBRel\Menu::DBGetInfoMenu($aLocal[$i]);
	$local	.= " -> <input type='button' class='MCObject' value='".$info->NOME."' onclick='javascript:changeLocal(\"".$info->CODIGO."\",\"".$codTipoUsuario."\");'>";
}



#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . '/cadMenu.html');

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('FORM_ACTION'		,$_SERVER['REQUEST_URI']);
$template->assign('SEL_TIPO_USUARIO',$selTipoUsuario);
$template->assign('SEL_TIPO_MENU'	,$selTipoMenu);
$template->assign('COD_MENU_PAI'	,$codMenu);
$template->assign('COD_MENU'		,$codMenu);
$template->assign('COD_TIPO_USUARIO',$codTipoUsuario);
$template->assign('LOCALIZACAO'		,$local);
$template->assign('HIDDEN'			,$hidden);
$template->assign('HIDDEN_LINK'		,$hidLink);
$template->assign('HIDDEN_CAD'		,$hidCad);
$template->assign('SMENU'			,$sMenu);
$template->assign('SDESCRICAO'		,$sDesc);
$template->assign('SLINK'			,$sLink);
$template->assign('SICONE'			,$sIcone);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();

?>