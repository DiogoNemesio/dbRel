<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

include_once(BIN_PATH . 'auth.php');

#################################################################################
## Verifica se o usuário é administrador
#################################################################################
if ($system->ehAdmin($system->getUsuario()) == false) {
	$system->halt('SECURITY: Tentativa de acesso administrador do usuário: '.$system->getUsuario().' IP: '.$_SERVER['REMOTE_ADDR'].' !!!',false,false,false);
}
