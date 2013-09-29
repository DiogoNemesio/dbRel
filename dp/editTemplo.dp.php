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
if (isset($_POST['nome']))			$nome		= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['email']))			$email		= DHCUtil::antiInjection($_POST["email"]);
if (isset($_POST['cidade']))		$cidade		= DHCUtil::antiInjection($_POST["cidade"]);
if (isset($_POST['endereco']))		$endereco	= DHCUtil::antiInjection($_POST["endereco"]);
if (isset($_POST['bairro']))		$bairro		= DHCUtil::antiInjection($_POST["bairro"]);
if (isset($_POST['codTemplo']))		$codTemplo	= DHCUtil::antiInjection($_POST["codTemplo"]);

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

# Nome #
if (!$nome)	{
	$err	= "Campo nome é obrigatório !!";
}

# UF / Cidade #
$array		= explode("/", $cidade);


if (sizeof($array) != 2) {
	$err 	= "UF / Cidade inválida, selecione uma cidade válida !!!";
}else{
	$uf			= trim($array[0]);
	$nomeCidade	= trim($array[1]);

	$infoCidade		= cidade::existeCidade($uf, $nomeCidade); 
	if ($infoCidade == false) {
		$err 	= "UF / Cidade inválida, selecione uma cidade válida !!!";
	}else{
		$codCidade	= $infoCidade->CODIGO;
	}
}

if ($err != null) {
	echo '1'.DHCUtil::encodeUrl('||'.$err);
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
$err	= templo::salva($codTemplo, $nome, $email, $codCidade, $endereco, $bairro);


if (is_numeric($err)) {
	$codTemplo 	= $err;
	$err		= null; 
}



if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codTemplo.'|Templo salvo com sucesso');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codTemplo.'|'.$err);
}
