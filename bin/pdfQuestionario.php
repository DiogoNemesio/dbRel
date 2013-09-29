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
if (isset($_POST['codConsulta'])) {
	$codConsulta 	= DHCUtil::antiInjection($_POST['codConsulta']);
}elseif (isset($_GET['codConsulta'])) {
	$codConsulta 	= DHCUtil::antiInjection($_GET['codConsulta']);
}else{
	DHCErro::halt('Parâmetro não informado !!!');
}

if (isset($_POST['codQuestionario'])) {
	$codQuestionario 	= DHCUtil::antiInjection($_POST['codQuestionario']);
}elseif (isset($_GET['codConsulta'])) {
	$codQuestionario 	= DHCUtil::antiInjection($_GET['codQuestionario']);
}else{
	DHCErro::halt('Parâmetro não informado (2) !!!');
}


#################################################################################
## Resgata as informações da consulta
#################################################################################
$info	= consulta::getInfo($codConsulta);
if (!$info) {
	DHCErro::halt('Consulta não encontrada !!! ('.$codConsulta.')');
}

#################################################################################
## Resgata as informações do Paciente
#################################################################################
$infoPaciente	= paciente::getInfo($info->COD_PACIENTE);

#################################################################################
## Resgata as informações do questionario
#################################################################################
$infoQuest		= questionario::getInfo($codQuestionario);

#################################################################################
## Resgata as perguntas
#################################################################################
$perguntas	= questionario::listaPerguntasAtivas($codQuestionario);

#################################################################################
## Gera o PDF
#################################################################################
$pdf	= new questionarioPDF();

$pdf->addRow(
	array(
		array('Nome: ',57,'L','B'),
		array($infoPaciente->NOME, 200,'L','N'),
		array('Idade: ',45,'L','B'),
		array($infoPaciente->IDADE . " anos",85,'L','N'),
		array('Nasc.: ',45,'L','B'),
		array($infoPaciente->NASCIMENTO,180,'L','N'),
	),'N','L',0,null
);

$pdf->addRow(
	array(
		array('Endereço: ',57,'L','B'),
		array($infoPaciente->ENDERECO,200,'L','N'),
		array('Bairro: ',45,'L','B'),
		array($infoPaciente->BAIRRO ,85,'L','N'),
		array('Cidade: ',45,'L','B'),
		array($infoPaciente->CIDADE,180,'L','N'),
	),'N','L',0,null
);

$pdf->addRow(
	array(
		array('E-Mail: ',57,'L','B'),
		array($infoPaciente->EMAIL,200,'L','N'),
		array('Fone: ',45,'L','B'),
		array($infoPaciente->TELEFONE ,85,'L','N'),
		array('Cel.: ',45,'L','B'),
		array($infoPaciente->CELULAR,180,'L','N'),
	),'N','L',0,null
);

$pdf->addLine();
$pdf->addText(" ");
$pdf->addText("Questionário: ".$infoQuest->NOME,'C','B');
$pdf->addText(" ");

#################################################################################
## Montar as perguntas com as respostas
#################################################################################
for ($i = 0; $i < sizeof($perguntas); $i++) {
	$pdf->addText(($i+1) . ") ".$perguntas[$i]->DESCRICAO.": ",'L','B');
	$resposta = questionario::getRespostaPergunta($infoQuest->COD_TIPO, $info->COD_PACIENTE, $perguntas[$i]->CODIGO, $codQuestionario,$codConsulta);
	$aResp	= explode(chr(10),$resposta);
	
	for ($j = 0; $j < sizeof($aResp); $j++) {
		if ($j == 0) 	{
			$pdf->addText("R -  ".$aResp[$j],'L','N');
		}else{
			$pdf->addText("     ".$aResp[$j],'L','N');
		}
	}
	
	
	$pdf->addText(" ");
}


#################################################################################
## manda a requisição para o browser
#################################################################################
$nomeArquivo	= "QUESTIONARIO_".str_replace(' ','_',$infoPaciente->NOME.".pdf");
$conteudo = $pdf->render();
DHCUtil::sendHeaderDownload($nomeArquivo,'pdf');
echo $conteudo;