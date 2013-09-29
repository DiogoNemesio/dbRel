function isNumeric(sText) {
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;

   for (i = 0; i < sText.length && IsNumber == true; i++) { 
      Char = sText.charAt(i); 
      if (ValidChars.indexOf(Char) == -1) {
         IsNumber = false;
      }
   }
   return IsNumber;
}

function lpad (str,len,pad) {
  pad = pad || ' ';
  while(str.length < len) str = pad + str;
  return str;
}

function rpad (str,len,pad) {
  pad = pad || ' ';
  while(str.length < len) str = str + pad;
  return str;
}

function checaRetornoOK (mensagem) {
	if (mensagem.charAt(0) == "0") {
		return true;
	}else{
		return false;
	}
}

function mostraMensagem(msg) {
	var url = 'msg.php?mensagem='+msg;
	$('#zgDivMsgID').load(url,function(){
	    $(this).modal({
	        keyboard:true,
	        backdrop:true
	    });
	}).modal('show');
}

function trocaTemplo(pUrl) {
	$.ajax({
		type: 		"POST", 
		url: 		pUrl,
		data:		$('#codTemploID').serialize(),		
	});
}

function removenull(str) {
    var new_str = str;
    if (str == '') {
        new_str = str.replace('', " - ");
    }
    else if (str == null) {
        new_str = " - ";
    }
    return new_str;
}


function mostraQuestionario(pCodPaciente,pCodConsulta,pCodTipo,pCodQuest) {
	if ((pCodTipo == 'F') && (pCodConsulta == '')) {
		var msg = $.base64.encode("||Salve primeiramente a consulta antes de responder o questionario !!!");
		mostraMensagem('1'+msg);
		return;
	}
	var url = 'modalQuestionario.php?codPaciente='+pCodPaciente+'&codConsulta='+pCodConsulta+'&codTipo='+pCodTipo+'&codQuestionario='+pCodQuest;
	$('#zgDivModalID').load(url,function(){
	    $(this).modal({
	        keyboard:true,
	        backdrop:true
	    });
	}).css({
	    width: '800px',
	    'margin-left': function () {
	        return -($(this).width() / 2);
	    }
	}).modal('show');
}

function showQuest(pCodQuest,pCodPaciente,pCodConsulta){
	var vMaxH, vModal, vBody, vHeader, vFooter;
	vModal		= $('.modal');
	vHeader 	= $(".modal-header"	, vModal);
	vBody 		= $(".modal-body"	, vModal);
	vFooter		= $(".modal-footer"	, vModal);
	vMaxH = Math.round($(document).height() - $("#divMostraQuestID").offset().top - vFooter.height() - 80);
	//alert('DocHeig: '+$(document).height()+' ModalBodyOffset: '+$("#divMostraQuestID").offset().top+ ' FooterHeig:' + vFooter.height());
	vBody.css({
	    'max-height': vMaxH
	})
	var url = 'mostraQuestionario.php?codQuestionario='+pCodQuest+'&codPaciente='+pCodPaciente+'&codConsulta='+pCodConsulta;
	if (pCodQuest != '') {
		$('#divMostraQuestID').load(url);
	}
}

function uploadDoc(pCodPaciente,pCodConsulta,pCodTipo) {
	if ((pCodTipo == 'F') && (pCodConsulta == '')) {
		var msg = $.base64.encode("||Salve primeiramente a consulta antes de carregar o documento !!!");
		mostraMensagem('1'+msg);
		return;
	}
	var url = 'modalDoc.php?codPaciente='+pCodPaciente+'&codConsulta='+pCodConsulta+'&codTipo='+pCodTipo;
	$('#zgDivModalID').load(url,function(){
	    $(this).modal({
	        keyboard:true,
	        backdrop:true
	    });
	}).css({
	    width: '800px',
	    'margin-left': function () {
	        return -($(this).width() / 2);
	    }
	}).modal('show');
}

function showDoc(pTipoDoc,pCodPaciente,pCodConsulta){
	var vMaxH, vModal, vBody, vHeader, vFooter;
	vModal		= $('.modal');
	vHeader 	= $(".modal-header"	, vModal);
	vBody 		= $(".modal-body"	, vModal);
	vFooter		= $(".modal-footer"	, vModal);
	vMaxH = Math.round($(document).height() - $("#divMostraDocID").offset().top - vFooter.height() - 80);
	//alert('DocHeig: '+$(document).height()+' ModalBodyOffset: '+$("#divMostraQuestID").offset().top+ ' FooterHeig:' + vFooter.height());
	vBody.css({
	    'max-height': vMaxH
	})
	var url = 'mostraDoc.php?codTipoDoc='+pTipoDoc+'&codPaciente='+pCodPaciente+'&codConsulta='+pCodConsulta;
	if (pCodConsulta != '') {
		$('#divMostraDocID').load(url);
	}
}

function geraPdfConsulta(pCodConsulta) {
	if (pCodConsulta == '') {
		var msg = $.base64.encode("||Salve primeiramente a consulta antes de imprimir !!!");
		mostraMensagem('1'+msg);
		return;
	}
	var vUrl = 'pdfConsulta.php';
	$.download(vUrl,'codConsulta='+ pCodConsulta);
}

function geraPdfQuest(pCodConsulta,pCodQuest) {
	if (pCodConsulta == '') {
		var msg = $.base64.encode("||Salve primeiramente a consulta antes de imprimir !!!");
		mostraMensagem('1'+msg);
		return;
	}
	var vUrl = 'pdfQuestionario.php';
	$.download(vUrl,'codConsulta='+ pCodConsulta+'&codQuestionario='+pCodQuest);
}

function downloadDoc(pCodConsulta,pCodDoc) {
	if (pCodConsulta == '') {
		var msg = $.base64.encode("||Salve primeiramente a consulta antes de carregar o documento !!!");
		mostraMensagem('1'+msg);
		return;
	}
	var vUrl = 'downloadDoc.php';
	$.download(vUrl,'codConsulta='+pCodConsulta+'&codDoc='+pCodDoc);
}
