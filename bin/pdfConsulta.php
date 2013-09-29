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
## Gera o PDF
#################################################################################
$pdf	= new consultaPDF();


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

$image = Zend_Pdf_Image::imageWithPath(IMG_PATH . '/Historico.png');
$pdf->pages[$pdf->actPage]->drawImage($image,
		(20 + 3),
		(793 - (20 + 40)),
		(20 + 13),
		(793 - (20 + 30))
);

$linhas	= 22;
$pdf->addText("   Histórico: ",'L','B');
$aHist	= explode(chr(10),$info->OBS);

for ($i = 0; $i < sizeof($aHist); $i++) {
	$pdf->addText($aHist[$i],'L','N');
}

for ($j = $i; $j < $linhas; $j++) $pdf->addText(' ');


$image = Zend_Pdf_Image::imageWithPath(IMG_PATH . '/Estetoscopio.png');
$pdf->pages[$pdf->actPage]->drawImage($image,
		(20 + 3),
		(514 - (20 + 40)),
		(20 + 13),
		(514 - (20 + 30))
);

$pdf->addText("   Orientações: ",'L','B');
$aOri	= explode(chr(10),$info->ORIENTACOES);

for ($i = 0; $i < sizeof($aOri); $i++) {
	$pdf->addText($aOri[$i],'L','N');
}

#################################################################################
## manda a requisição para o browser
#################################################################################
$nomeArquivo	= "CONSULTA_".str_replace(' ','_',$infoPaciente->NOME.".pdf");
//$pdf->Output($nomeArquivo, 'D');
//$system->log->debug->debug('NomeArquivo: '.$nomeArquivo);
$conteudo = $pdf->render();
DHCUtil::sendHeaderDownload($nomeArquivo,'pdf');
echo $conteudo;