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
if (isset($_POST["codConsulta"]))	$codConsulta	= DHCUtil::antiInjection($_POST["codConsulta"]);
if (isset($_POST["codTipoDoc"]))	$codTipoDoc		= DHCUtil::antiInjection($_POST["codTipoDoc"]);
if (isset($_POST["nome"]))			$nome			= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_FILES["arquivo"]))		$arquivo		= $_FILES["arquivo"];

//$system->log->debug->debug("Nome: ".$nome." Arquivo: ".$arquivo["tmp_name"]." Consulta: ".$codConsulta);
$system->log->debug->debug("Post: ".serialize($_POST)." Files: ".serialize($_FILES)."Arquivo: ".$arquivo["tmp_name"]);

#################################################################################
## Salvar no banco
#################################################################################
if (isset($arquivo)) {
	$err = consulta::uploadDoc($codConsulta, $arquivo["tmp_name"], $nome,$codTipoDoc);
}

if (is_numeric($err)) {
	$codArquivo 		= $err;
	$err				= null;
}


if ($err == null) {
	echo 'Arquivo anexado com sucesso !!';
}else{
	echo $err;
}
