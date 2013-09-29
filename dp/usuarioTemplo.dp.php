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
if (isset($_POST['codUsuario']))			$codUsuario		= DHCUtil::antiInjection($_POST["codUsuario"]);


#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

# Usuario #
if (empty($codUsuario)) {
	$err = "Falta de parâmetros (COD_USUARIO)";
}

# Verifica se o usuário existe
$info	= usuario::getInfo($codUsuario);
if (!isset($info->NOME)) 	$err	= "Usuário não localizado !!";

# Verifica o tipo do usuário
if ($info->COD_TIPO == "A") {
	echo '2'.DHCUtil::encodeUrl('||Usuário administrador tem acesso a todos os templos');
	exit;
	
}

if ($err != null) {
	echo '1'.DHCUtil::encodeUrl('||'.$err);
	exit;
}

$system->log->debug->debug(serialize($_POST));

#################################################################################
## Salvar no banco
#################################################################################
$templos	= usuario::listaPermissoesTemplos($codUsuario);
for ($i = 0; $i < sizeof($templos); $i++) {
	if (isset($_POST["TP_".$templos[$i]->CODIGO])) {
		$err = usuario::associaTemplo($codUsuario, $templos[$i]->CODIGO);
	}else{
		$err = usuario::desassociaTemplo($codUsuario, $templos[$i]->CODIGO);
	}
}


echo '0'.DHCUtil::encodeUrl('||Associação efetuada com sucesso !!!');
