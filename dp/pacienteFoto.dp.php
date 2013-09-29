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
if (isset($_POST['codPaciente']))	$codPaciente	= DHCUtil::antiInjection($_POST["codPaciente"]);
if (isset($_FILES['foto']))			$foto			= $_FILES["foto"];


#################################################################################
## Salvar no banco
#################################################################################
if (isset($foto)) {
	$err = paciente::alterafoto($codPaciente, $foto["tmp_name"]);
}

if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codPaciente.'|Foto Alterada com sucesso !!');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codPaciente.'|'.$err);
}
