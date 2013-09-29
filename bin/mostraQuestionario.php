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
if (isset($_GET['codQuestionario']))	$codQuestionario	= DHCUtil::antiInjection($_GET["codQuestionario"]);
if (isset($_GET['codPaciente']))		$codPaciente		= DHCUtil::antiInjection($_GET["codPaciente"]);
if (isset($_GET['codConsulta']))		$codConsulta		= DHCUtil::antiInjection($_GET["codConsulta"]);

if (!isset($codQuestionario) || !isset($codPaciente)) exit;
if (!isset($codConsulta)) $codConsulta	= null;

#################################################################################
## Carrega as informações do Questionário
#################################################################################
$info	= questionario::getInfo($codQuestionario);

if (isset($info->CODIGO) && ($info->CODIGO == $codQuestionario)) {
	#################################################################################
	## Resgata as perguntas
	#################################################################################
	$perguntas			= questionario::listaPerguntasAtivas($codQuestionario);
	$titulo				= $info->NOME;
	
}else{
	$codQuestionario	= null;
	$titulo				= null;
	$perguntas			= null;
}


#################################################################################
## Monta o form das perguntas
#################################################################################
$pHtml	= '<form class="form-horizontal" id="zgBodyQuestFormID" name="testeForm" action="#" method="post">';
$pHtml	.= '<input type="hidden" name="codPaciente" value="'.$codPaciente.'">';
$pHtml	.= '<input type="hidden" name="codQuestionario" value="'.$codQuestionario.'">';
$pHtml	.= '<input type="hidden" name="codTipo" value="'.$info->COD_TIPO.'">';
if ($codConsulta != null) {
	$pHtml	.= '<input type="hidden" name="codConsulta" value="'.$codConsulta.'">';
}

for ($i=0; $i < sizeof($perguntas); $i++) {
	
	# Resgata a resposta da pergunta se houver #
	$resp		= questionario::getRespostaPergunta($info->COD_TIPO,$codPaciente,$perguntas[$i]->CODIGO,$codQuestionario,$codConsulta);
	//$system->log->debug->debug("Resposta (".$perguntas[$i]->CODIGO.") = ".$resp);
	$requerido	= ($perguntas[$i]->COD_OBRIGATORIO == "S") ? "required" : null;
	switch ($perguntas[$i]->COD_TIPO) {
		case "SN":
			$icone		= "icon-th-list";
			$inputType	= "select";
			if ($resp == "Sim") $selSim = "selected"; else $selSim = null;
			if ($resp == "Não") $selNao = "selected"; else $selNao = null;
			$options	= "<option $selSim value='Sim'>Sim</option><option $selNao value='Não'>Não</option>";
			$dateFormat	= null;
			$pClass		= null;
			break;
		case "T":
			$icone		= "icon-file-alt";
			$inputType	= "textarea";
			$options	= null;
			$dateFormat	= null;
			$pClass		= null;
			break;
		case "L":
			$icone		= "icon-th-list";
			$inputType	= "select";
			$valores	= pergunta::listaValores($perguntas[$i]->CODIGO);
			$options	= "";
			for ($j = 0; $j < sizeof($valores);$j++) {
				$sel	= (($valores[$j]->COD_VALOR == $resp) ? "selected" : null);
				$options .= "<option $sel value='".$valores[$j]->COD_VALOR."'>".$valores[$j]->COD_VALOR."</option>";
			}
			$dateFormat	= null;
			$pClass		= null;
			break;
		case "N":
			$icone		= "icon-list-ol";
			$inputType	= "number";
			$options	= null;
			$dateFormat	= null;
			$pClass		= null;
			break;
		case "D":
			$icone		= "icon-calendar";
			$inputType	= "data";
			$options	= null;
			$dateFormat	= "data-date-format='dd/mm/yyyy'";
			$pClass		= "date datepicker";
			break;
	}

	$pHtml	.= '<div class="control-group"><label class="control-label" style="width:240px;" for="form'.$perguntas[$i]->CODIGO.'ID">'.$perguntas[$i]->DESCRICAO.'&nbsp;</label>';
	$pHtml	.= '<div class="controls"><div class="input-prepend '.$pClass.'" '.$dateFormat.'><span class="add-on"><i class="'.$icone.'"></i></span>';
	
	if ($inputType	== "select") {
		$pHtml .= "<select $requerido id='form".$perguntas[$i]->CODIGO."ID' name='perguntas[".$perguntas[$i]->CODIGO."]' data-rel='chosen'>".$options."</select>";
	}elseif ($inputType == "textarea") {
		$pHtml	.= "<textarea $requerido class='input-large' id='form".$perguntas[$i]->CODIGO."ID' name='perguntas[".$perguntas[$i]->CODIGO."]' autocomplete='off' rows='3'>".$resp."</textarea>";
	}elseif ($inputType == "data") {
		$pHtml	.= "<input $requerido class='input-large' id='form".$perguntas[$i]->CODIGO."ID' type='text' name='perguntas[".$perguntas[$i]->CODIGO."]' value='".$resp."' dataBR autocomplete='off'>";
	}else{
		$pHtml	.= "<input $requerido class='input-large' id='form".$perguntas[$i]->CODIGO."ID' type='".$inputType."' name='perguntas[".$perguntas[$i]->CODIGO."]' value='".$resp."' autocomplete='off'>";
	}
	
	$pHtml	.= '</div></div></div>';
	
}
$pHtml	.= "</form>
<script>
	var timer;
	$(function() {
		$('.datepicker').datepicker({
			language: 'pt-BR'
		});
	});
		
	$('input,textarea').jqBootstrapValidation({
		preventSubmit: true
	});

	function mostraAlerta(pMsg) {
		$('#alertModalID').html('<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><p>'+pMsg+'</p>');
		$('#alertModalID').show();	
	}
	$('#zgBodyQuestFormID').submit(function() {
		$.ajax({
			type: 		'post',
			url: 		'".DP_URL."/editRespostaQuestionario.dp.php',
			data:		$('#zgBodyQuestFormID').serialize(),
		}).done(function( data, textStatus, jqXHR) {
			alert(data);
		}).fail(function( jqXHR, textStatus, errorThrown) {
			alert('1||'+errorThrown);
		});
		return false; 
	});
	
	$('#submitModalQuest').click(function() {
		$('#zgBodyQuestFormID').submit();
	});
</script>

		
";

echo $pHtml;
