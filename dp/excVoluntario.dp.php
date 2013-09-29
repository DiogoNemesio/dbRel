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
if (isset($_POST['id']))			$id			= DHCUtil::antiInjection($_POST["id"]);

#################################################################################
## Descompacta o ID
#################################################################################
DHCUtil::descompactaId($id);

#################################################################################
## Verifica se algumas variáveis estão OK
#################################################################################
if (!isset($codUsuario)) {
	echo '1'.DHCUtil::encodeUrl('||'.'Falta de Parâmetros (COD_USUARIO)');
	exit;
}

#################################################################################
## Verifica se o usuário está logado
#################################################################################
if ($codUsuario == $system->getCodUsuario()) {
	echo '1'.DHCUtil::encodeUrl('||'.'Não é permitido excluir o voluntário que está conectado !!!');
	exit;
}

#################################################################################
## Excluir do banco
#################################################################################
$err	= usuario::exclui($codUsuario);


if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codUsuario.'|Voluntário excluído com sucesso !!');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codUsuario.'|'.$err);
}
