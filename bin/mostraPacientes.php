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
if (isset($_GET['nome']))		$nome		= DHCUtil::antiInjection($_GET["nome"]);
if (isset($_GET['cidade']))		$cidade		= DHCUtil::antiInjection($_GET["cidade"]);

if (!isset($nome))		$nome 	= null;
if (!isset($cidade))	$cidade	= null;

#################################################################################
## Valida a cidade
#################################################################################
if ($cidade != null) {
	$cidade 	= DHCUtil::decodeUrl($cidade);
	$array		= explode("/", $cidade);
	if (sizeof($array) != 2) {
		$codCidade = null;
	}else{
		$uf			= trim($array[0]);
		$nomeCidade	= trim($array[1]);
	
		$infoCidade		= cidade::existeCidade($uf, $nomeCidade);
		if ($infoCidade == false) {
			$codCidade = null;
		}else{
			$codCidade	= $infoCidade->CODIGO;
		}
	}
}else{
	$codCidade	= null;
}

#################################################################################
## Monta o html do DIV
#################################################################################
$pacientes		= paciente::busca($nome,$codCidade);
$pacienteHtml	= '<div class="well">Total encontrado: '.sizeof($pacientes).'</div>';
$pacienteHtml	.= '<table class="table">';
$pacienteHtml	.= '<thead><tr><th>Nome</th><th>Cidade</th></tr></thead>';
for ($i=0 ; $i < sizeof($pacientes); $i++) {
	$pacienteHtml	.= '<tr><td>'.$pacientes[$i]->NOME.'</td><td>'.$pacientes[$i]->CIDADE.'</td></tr>';
}
$pacienteHtml	.= '</table>';

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('NOME'					,$nome);
$template->assign('COD_CIDADE'				,$codCidade);
$template->assign('PACIENTES'				,$pacienteHtml);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
