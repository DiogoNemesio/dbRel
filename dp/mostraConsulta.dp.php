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
if (isset($_POST['codConsulta']))	$codConsulta	= DHCUtil::antiInjection($_POST["codConsulta"]);
if (isset($_POST['codPaciente']))	$codPaciente	= DHCUtil::antiInjection($_POST["codPaciente"]);
if (isset($_POST['data']))			$data			= DHCUtil::antiInjection($_POST["data"]);
if (isset($_POST['obs']))			$obs			= DHCUtil::antiInjection($_POST["obs"]);
if (isset($_POST['orientacoes']))	$orientacoes	= DHCUtil::antiInjection($_POST["orientacoes"]);

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

if (!isset($data))	$data = date('d/m/Y');

#################################################################################
## Salvar no banco
#################################################################################
$err	= consulta::salva($codConsulta,$codPaciente,$data,$obs,$orientacoes);

if (is_numeric($err)) {
	$codConsulta 	= $err;
	$err			= null; 
}

if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codConsulta.'|Consulta salva com sucesso');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codConsulta.'|'.$err);
}
