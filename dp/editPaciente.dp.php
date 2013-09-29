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
if (isset($_POST['nome']))			$nome			= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['codSexo']))		$codSexo		= DHCUtil::antiInjection($_POST["codSexo"]);
if (isset($_POST['email']))			$email			= DHCUtil::antiInjection($_POST["email"]);
if (isset($_POST['telefone']))		$telefone		= DHCUtil::antiInjection($_POST["telefone"]);
if (isset($_POST['celular']))		$celular		= DHCUtil::antiInjection($_POST["celular"]);
if (isset($_POST['dataNasc']))		$dataNasc		= DHCUtil::antiInjection($_POST["dataNasc"]);
if (isset($_POST['profissao']))		$profissao		= DHCUtil::antiInjection($_POST["profissao"]);
if (isset($_POST['cidade']))		$cidade			= DHCUtil::antiInjection($_POST["cidade"]);
if (isset($_POST['endereco']))		$endereco		= DHCUtil::antiInjection($_POST["endereco"]);
if (isset($_POST['bairro']))		$bairro			= DHCUtil::antiInjection($_POST["bairro"]);
if (isset($_FILES['foto']))			$foto			= $_FILES["foto"];

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

# Nome #
if (!$nome)	{
	$err	= "Campo \"nome\" é obrigatório !!";
}

# Data Nasc #
//if (!preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $dataNasc)) {
//	$err	= "Campo \"Nascimento\" deve ter o format dd/mm/yyyy !!";
//}

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
$err	= paciente::salva($codPaciente,$nome,$codSexo,$email,$telefone,$celular,$dataNasc,null,$profissao,$codCidade,$endereco,$bairro);

if (is_numeric($err)) {
	$codPaciente 	= $err;
	$err			= null; 
}

$system->log->debug->debug('POST'. serialize($_POST));
$system->log->debug->debug('FILES'. serialize($_FILES));
if (isset($foto)) {
	$err = paciente::alterafoto($codPaciente, $foto);
}


if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codPaciente.'|Paciente salvo com sucesso !!');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codPaciente.'|'.$err);
}
