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
if (isset($_POST['senha']))			$senha		= DHCUtil::antiInjection($_POST["senha"]);
if (isset($_POST['confSenha']))		$confSenha	= DHCUtil::antiInjection($_POST["confSenha"]);

#################################################################################
## Descompacta o ID
#################################################################################
DHCUtil::descompactaId($id);

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;


# CodUsuario #
if (empty($codUsuario))		$err = "Campo codUsuario não informado !!";

# Senha #
if (!$senha)				$err = "Campo Senha é obrigatório !!";

if (strlen($senha) < 4) 	$err = "Campo Senha menor que 4, use uma senha maior !!!"; 


# Confirmação de senha #
if ($senha != $confSenha) 	$err	= "Senhas não conferem !!!";

# Verifica se o usuário existe
$info	= usuario::getInfo($codUsuario);
if (!isset($info->NOME)) 	$err	= "Usuário não localizado !!";

if ($err != null) {
	echo '1'.DHCUtil::encodeUrl('||'.$err);
	exit;
}



#################################################################################
## Salvar no banco
#################################################################################
$err	= usuario::AlteraSenha($codUsuario, DHCAuth::encrypt($info->USUARIO, $senha));
if ($err) {echo ('1'.DHCUtil::encodeUrl('||'.$err)); exit;} 



echo '0'.DHCUtil::encodeUrl('||Senha Alterada com sucesso !!!');
