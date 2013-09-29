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
if (isset($_GET['q']))			$q			= DHCUtil::antiInjection($_GET["q"]);

$cidades	= cidade::busca($q);
$array		= array();

for ($i = 0; $i < sizeof($cidades); $i++) {
	$array[$i] = $cidades[$i]->COD_UF . ' / '.$cidades[$i]->NOME;
	if ($i > $system->parametros["BS_TA_ITENS"]->getValor()) break;
}

echo json_encode($array);