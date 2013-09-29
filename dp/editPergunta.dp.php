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
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	echo '1'.DHCUtil::encodeUrl('||'.'Falta de Parâmetros 1');
	exit;
}

#################################################################################
## Descompacta o ID
#################################################################################
DHCUtil::descompactaId($id);

#################################################################################
## Resgata as variáveis postadas
#################################################################################
if (isset($_POST['codQuestionario']))	$codQuestionario	= DHCUtil::antiInjection($_POST["codQuestionario"]);
if (isset($_POST['codPergunta']))		$codPergunta		= DHCUtil::antiInjection($_POST["codPergunta"]);
if (isset($_POST['descricao']))			$descricao			= DHCUtil::antiInjection($_POST["descricao"]);
if (isset($_POST['codStatus']))			$codStatus			= DHCUtil::antiInjection($_POST["codStatus"]);
if (isset($_POST['codTipo']))			$codTipo			= DHCUtil::antiInjection($_POST["codTipo"]);
if (isset($_POST['ordem']))				$ordem				= DHCUtil::antiInjection($_POST["ordem"]);
if (isset($_POST['codObrigatorio']))	$codObrigatorio		= DHCUtil::antiInjection($_POST["codObrigatorio"]);
if (isset($_POST['campos']))			$campos				= $_POST["campos"];

#################################################################################
## Verifica se algumas variáveis estão OK
#################################################################################

if (!isset($codPergunta)) {
	echo '1'.DHCUtil::encodeUrl('||'.'Falta de Parâmetros 2');
	exit;
}

if (!isset($codQuestionario)) {
	echo '1'.DHCUtil::encodeUrl('||'.'Falta de Parâmetros 3');
	exit;
}

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

# codQuestionario #
$info	= questionario::getInfo($codQuestionario);
if (!isset($info->CODIGO)) {
	$err 	= "Questionario não encontrado !!! (COD_QUESTIONARIO)";
}

# Ordem #
if (empty($ordem))	{
	$err	= "Campo \"Ordem\" é obrigatório !!";
}

if (!is_numeric($ordem)) {
	$err	= "Campo \"Ordem\" deve ser numérico !!";
}

# Descrição #
if (empty($descricao))	{
	$err	= "Campo \"Descrição\" é obrigatório !!";
}

if ($err != null) {
	echo '1'.DHCUtil::encodeUrl('||'.$err);
	exit;
}


#################################################################################
## Salvar no banco
#################################################################################
$err	= pergunta::salva($codPergunta,$codQuestionario,$descricao,$codTipo,$codStatus,$ordem,$codObrigatorio);

if (is_numeric($err)) {
	$codPergunta 		= $err;
	$err				= null; 
}

#################################################################################
## Salvar os valores
#################################################################################
if (isset($campos)) {
	for ($i = 0; $i < sizeof($campos); $i++) {
		pergunta::adicionaValor($codPergunta, $campos[$i]);
	}
}

if ($err == null) {
	echo '0'.DHCUtil::encodeUrl('|'.$codPergunta.'|Pergunta salva com sucesso !!');
}else{
	echo '1'.DHCUtil::encodeUrl('|'.$codPergunta.'|'.$err);
}
