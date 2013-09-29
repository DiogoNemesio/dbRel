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

if (isset($_POST['codQuestionario']))	$codQuestionario	= DHCUtil::antiInjection($_POST["codQuestionario"]);
if (isset($_POST['codPaciente']))		$codPaciente		= DHCUtil::antiInjection($_POST["codPaciente"]);
if (isset($_POST['codConsulta']))		$codConsulta		= DHCUtil::antiInjection($_POST["codConsulta"]);
if (isset($_POST['perguntas'])	)		$perguntas			= $_POST["perguntas"];

$info	= questionario::getInfo($codQuestionario);

if ($info->COD_TIPO == "A") {
	foreach ($perguntas as $codPergunta => $valor) {
		$err = pergunta::salvaRespostaAux ($codPaciente,$codPergunta,$valor);
		if ($err) {
			echo ($err);
			return;
		}
	}
}else{
	foreach ($perguntas as $codPergunta => $valor) {
		$err = pergunta::salvaRespostaConsulta ($codConsulta,$codPergunta,$valor);
		if ($err) {
			echo ($err);
			return;
		}
	}
}

echo ('Respostas salvas com sucesso !!');
