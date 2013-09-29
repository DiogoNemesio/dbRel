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
if (isset($_POST['codCidade']))		$codCidade	= DHCUtil::antiInjection($_POST["codCidade"]);
if (isset($_POST['email']))			$email		= $_POST["email"];
if (isset($_POST['titulo']))		$titulo		= DHCUtil::antiInjection($_POST["titulo"]);

$system->log->debug->debug("POST: ".serialize($_POST));

if (!isset($nome))		$nome 		= null;
if (!isset($codCidade))	$codCidade	= null;

if ((!$email) || (empty($email)))	{
	exit;
}

if ((!$titulo) || (empty($titulo)))	{
	exit;
}

#################################################################################
## Resgata as variáveis postadas
#################################################################################
$info	= templo::getInfo($system->getCodTemplo());

$pacientes		= paciente::busca($nome,$codCidade);
$mail			= new Zend_Mail('utf-8');
$mail->setSubject($titulo);
$mail->setBodyHtml($email);
if (!empty($info->EMAIL)) {
	$mail->setFrom($info->EMAIL,$info->NOME);
	$mail->addTo($info->EMAIL,$info->NOME);
}else{
	DHCErro::halt('Erro: Falta configurar o email do Templo !!!');
}

for ($i=0; $i < sizeof($pacientes); $i++) {
	if (!empty($pacientes[$i]->EMAIL)) {
		$mail->addBcc($pacientes[$i]->EMAIL,$pacientes[$i]->NOME);
		$system->log->debug->debug('Enviando email para: '.$pacientes[$i]->EMAIL." Email: ".$email);
	}
}
$mail->send();
