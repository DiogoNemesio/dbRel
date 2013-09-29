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
## Resgata a lista de Pacientes
#################################################################################
$pacientes	= paciente::lista();

#################################################################################
## Resgata as informacoes do templo
#################################################################################
$templo		= templo::getInfo($system->getCodTemplo());

#################################################################################
## Gera o PDF
#################################################################################
$pdf	= new pacientesPDF();



for ($i = 0; $i < sizeof($pacientes); $i++) {
	$pdf->addRow(
		array(
			array($pacientes[$i]->NOME,190,'L','N'),
			array($pacientes[$i]->EMAIL,190,'L','N'),
			array($pacientes[$i]->TELEFONE,54,'L','N'),
			array($pacientes[$i]->BAIRRO,140,'L','N'),
		),'N','L',0,null
	);
}

#################################################################################
## manda a requisição para o browser
#################################################################################
$nomeArquivo	= "LISTA_PACIENTES_".str_replace(' ','_',$templo->NOME.".pdf");
$conteudo = $pdf->render();
DHCUtil::sendHeaderDownload($nomeArquivo,'pdf');
echo $conteudo;