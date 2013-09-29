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
if (isset($_POST['codQuestionario']))	$codQuestionario	= DHCUtil::antiInjection($_POST["codQuestionario"]);
if (isset($_POST['nome']))				$nome				= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['codStatus']))			$codStatus			= DHCUtil::antiInjection($_POST["codStatus"]);
if (isset($_POST['codTipo']))			$codTipo			= DHCUtil::antiInjection($_POST["codTipo"]);

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

# Nome #
if (!$nome)	{
	$err	= "Campo \"nome\" é obrigatório !!";
}
if ($err != null) {
	echo '1'.DHCUtil::encodeUrl('||'.$err);
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
$err	= questionario::salva($codQuestionario,$nome,$codStatus,$codTipo);

if (is_numeric($err)) {
	$codQuestionario 	= $err;
	$err				= null; 
}


if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codQuestionario.'|Questionário salvo com sucesso !!');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codQuestionario.'|'.$err);
}
