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
if (isset($_POST['codUsuario']))	$codUsuario		= DHCUtil::antiInjection($_POST["codUsuario"]);
if (isset($_POST['usuario']))		$usuario		= DHCUtil::antiInjection($_POST["usuario"]);
if (isset($_POST['nome']))			$nome			= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['senha']))			$senha			= DHCUtil::antiInjection($_POST["senha"]);
if (isset($_POST['email']))			$email			= DHCUtil::antiInjection($_POST["email"]);
if (isset($_POST['codTipo']))		$codTipo		= DHCUtil::antiInjection($_POST["codTipo"]);
if (isset($_POST['telefone']))		$telefone		= DHCUtil::antiInjection($_POST["telefone"]);
if (isset($_POST['celular']))		$celular		= DHCUtil::antiInjection($_POST["celular"]);
if (isset($_POST['codStatus']))		$codStatus		= DHCUtil::antiInjection($_POST["codStatus"]);

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

# Usuario #
if (!$usuario)	{
	$err	= "Campo \"Voluntário\" é obrigatório !!";
}

# Nome #
if (!$nome)	{
	$err	= "Campo \"nome\" é obrigatório !!";
}

# Telefone #
if (($telefone != null) && (!is_numeric($telefone))) {
	$err	= "Campo \"Telefone\" deve ser numérico (".$telefone.") !!";
}

# Celular #
if (($celular != null) && (!is_numeric($celular))) {
	$err	= "Campo \"Celular\" deve ser numérico (".$celular.") !!";
}

# Senha #
if ($senha) {
	$senha	= DHCAuth::encrypt($usuario,$senha);
}

if ($err != null) {
	echo '1'.DHCUtil::encodeUrl('||'.$err);
	exit;
}




#################################################################################
## Salvar no banco
#################################################################################
$err	= usuario::salva($codUsuario,$usuario,$nome,$senha,$codTipo,$email,$telefone,$celular,$codStatus);

if (is_numeric($err)) {
	$codUsuario 	= $err;
	$err			= null; 
}

#################################################################################
## Troca senha
#################################################################################
if ($err == null) {
	if (!empty($senha)) {
		$err = usuario::AlteraSenha($codUsuario, $senha);
	}
}

#################################################################################
## Associa o usuário ao templo
#################################################################################
if ($err == null) {
	$err = usuario::associaTemplo($codUsuario, $system->getCodTemplo());
}

if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codUsuario.'|Voluntário salvo com sucesso !!');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codUsuario.'|'.$err);
}
