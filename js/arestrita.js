/*==================================================================
CLASSE clAreaRestrita{}
==================================================================*/
var lfgportal = lfgportal || {};
lfgportal.app = lfgportal.app || {};

;(function(scope, $) {
	scope.clAreaRestrita = {
		form : $('#frmLoginAreaRestrita'),
		area : $('select[name="codTemplo"]', $('#form-ar')),
		fnInit: function(){
			this.clMenuAr.fnInit();
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que controla a area Restrita</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        clMenuAr: {
			form : $('#form-ar'),
			listaTemplo : '',
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe clMenuAr</SUMARY>	
			----------------------------------------------------------------------------------------------------*/
            fnInit: function(){
                this.fnInitMenuAr();
                //this.fnValidar();
            },
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que: 1. Cliques para Mostra/Esconde Área Restrira; 2. Submit Formulário</SUMARY>	
			----------------------------------------------------------------------------------------------------*/
            fnInitMenuAr: function(){
				scope.clAreaRestrita.clMenuAr.listaTemplo = $('select[name="codTemplo"]', scope.clAreaRestrita.clMenuAr.form).html();
                $('#call-ar').click(function() {
                    $(this).parent().animate({ 'top' : '-100px'},
						function(){ $('#sub-ar').slideToggle(); }
					);
                });
                $('#close-ar').click(function() {
					 $('#sub-ar').slideToggle(300,
					 	function(){ $('#call-ar').parent().animate({ 'top' : '0'});	}
					 );
                });
				$('#form-ar-entrar').click(function() {

 	 	 	 	        var validator = scope.clAreaRestrita.clMenuAr.form;
			                validator.validate({
			                    debug: true,
			                    rules: {
                        			"usuario": {
			                            required: true
			                        },
			                        "senha": {
			                            required: true
			                        }
			                    },
			                    errorPlacement: function(error,element) {
			                        return false;
			                    },
			                    submitHandler: function(form) {
			                    	var usuario	= document.getElementById('user').value;
			                    	var senha	= document.getElementById('pwd').value;
			                    	var templo	= document.getElementById('templo');
			                    	document.getElementById('userID').value	= usuario;
			                    	document.getElementById('passID').value	= senha;
			                    	document.getElementById('temploID').value	= templo.options[templo.selectedIndex].value;
			                    	scope.clAreaRestrita.form.submit();
			                    	return true;
			                    }
			                });
			                validator.submit();
				});
            },
        },
	};

	$(function(){
		scope.clAreaRestrita.fnInit();
	});
})(lfgportal.app, jQuery);
