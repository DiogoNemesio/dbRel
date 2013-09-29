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
if (isset($_POST['BS_TA_TIMEOUT']))			$BS_TA_TIMEOUT		= DHCUtil::antiInjection($_POST["BS_TA_TIMEOUT"]);
if (isset($_POST['BS_TA_ITENS']))			$BS_TA_ITENS		= DHCUtil::antiInjection($_POST["BS_TA_ITENS"]);
if (isset($_POST['BS_TA_MINLENGTH']))		$BS_TA_MINLENGTH	= DHCUtil::antiInjection($_POST["BS_TA_MINLENGTH"]);

#################################################################################
## Validação dos campos
#################################################################################
$err 	= null;

# BS_TA_TIMEOUT #
if (!$BS_TA_TIMEOUT)	{
	$err	= "Campo BS_TA_TIMEOUT é obrigatório !!";
}

if (!is_numeric($BS_TA_TIMEOUT)) {
	$err	= "Campo BS_TA_TIMEOUT deve ser numérico !!";
}

if (($BS_TA_TIMEOUT < 300) || ($BS_TA_TIMEOUT > 9999)) {
	$err	= "Campo BS_TA_TIMEOUT deve ser entre 300 e 9999 !!";
}


# $BS_TA_ITENS #
if (!$BS_TA_ITENS)	{
	$err	= "Campo $BS_TA_ITENS é obrigatório !!";
}

if (!is_numeric($BS_TA_ITENS)) {
	$err	= "Campo $BS_TA_ITENS deve ser numérico !!";
}

if (($BS_TA_ITENS < 1) || ($BS_TA_ITENS > 999)) {
	$err	= "Campo BS_TA_ITENS deve ser entre 1 e 999 !!";
}

# $BS_TA_MINLENGTH #
if (!$BS_TA_MINLENGTH)	{
	$err	= "Campo $BS_TA_MINLENGTH é obrigatório !!";
}

if (!is_numeric($BS_TA_MINLENGTH)) {
	$err	= "Campo $BS_TA_MINLENGTH deve ser numérico !!";
}

if (($BS_TA_MINLENGTH < 1) || ($BS_TA_MINLENGTH > 10)) {
	$err	= "Campo BS_TA_ITENS deve ser entre 1 e 10 !!";
}

if ($err != null) {
	echo '1'.DHCUtil::encodeUrl('||'.$err);
	exit;
}

#################################################################################
## Salvar no banco
#################################################################################
$err	= parametro::salva('BS_TA_TIMEOUT',$BS_TA_TIMEOUT);
if ($err) {echo ('1'.DHCUtil::encodeUrl('||'.$err)); exit;} 
$err	= parametro::salva('BS_TA_ITENS',$BS_TA_ITENS);
if ($err) {echo ('1'.DHCUtil::encodeUrl('||'.$err)); exit;} 
$err	= parametro::salva('BS_TA_MINLENGTH',$BS_TA_MINLENGTH);
if ($err) {echo ('1'.DHCUtil::encodeUrl('||'.$err)); exit;} 

#################################################################################
## Atualizar o sistema com os novos parâmetros
#################################################################################
$system->getParamFromDB();


echo '0'.DHCUtil::encodeUrl('||Parâmetros salvos com sucesso !!!');
