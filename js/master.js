/*==================================================================
CLASSE PRINCIPAL DO OBJETO lfgportal.app
--------------------------------------------------------------------
Projeto: Portal LFG
==================================================================*/
var lfgportal = lfgportal || {};
lfgportal.app = lfgportal.app || {};

(function(scope, $){
	/*----------------------------------------------------------------------------------------------------
	<SUMARY>Classe clMaster</SUMARY>	
	----------------------------------------------------------------------------------------------------*/
    scope.clMaster = {
        nItensPag : ( $.cookie('nPags') != null ) ? $.cookie('nPags') :4, /*Número de Itens por Página*/
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função contrutora da Classe clMaster</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        fnInit: function(){
            this.clMainMenu.fnInit();
			this.clFixSideBar.fnInit();
            this.clSubMenu.fnInit();            
            this.fnSlideShow();
            this.clDropSelect.fnInit();
            this.fnInitToolTip();
			this.fnInitPopOver();
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que fixa as SidesBar no Scroll</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        clFixSideBar: {
			side : '',
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe clFixSideBar</SUMARY>	
			----------------------------------------------------------------------------------------------------*/
			fnInit : function() {
				var container = $('.body-holder .content-row'),
					sidebar = container.find('.sidebar'),
					altSide = 0;
				
				if( sidebar.length > 0 ){
					sidebar.each(function(index, element) {
						$(element).css({'position' : 'relative', 'top' : '0px', 'left' : '0px'});
						if( $(element).height() >  altSide ){
							altSide = $(element).height();
							scope.clMaster.clFixSideBar.side = $(element);
						}
						
					});	
				scope.clMaster.clFixSideBar.fnFixar();
				}
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que aplica o SCROLL sob demanda nas sidebars</SUMARY>	
			----------------------------------------------------------------------------------------------------*/
			fnFixar : function(){
				var container = $('.body-holder .content-row'),
					sidebar = container.find('.sidebar');
				$(window).scroll(function(){
					var topWin = $(this).scrollTop(),
						altWin = $(this).height(),						
						topContent = container.offset().top,
						altContent = container.height(),
						altSide = scope.clMaster.clFixSideBar.side.height();
						topSide = scope.clMaster.clFixSideBar.side.position().top;
						
					if( topWin >= topContent && altSide < altContent ){
						/*Calcula o TOP baseado na diferença entre altura da Janela e altura do Container da Side*/
						calcTop = ((topWin-topContent)+40); 
						
						/*Se a soma do TOP da Side e Altura da Side for maior que a Altura do Container da Side
						Calcula o TOP baseado na diferença entre a Altura do Container e Altura da Side, 
						que é o limite que ajusta a side no bottom do Container*/
						if( (topSide+altSide) > altContent ){
							calcTop = altContent-altSide;
						}
						
						/*Se a Altura da Janela for menor que o TOP da Side
						Calcula o TOP baseado na diferença entre altura da Janela e altura do Container da Side*/
						if( topWin <= (topSide+80) ){
							calcTop = ((topWin-topContent)+40);
						}
						
						sidebar.stop().animate({ 'top' : calcTop+'px', 'left' : '0px'},'fast');
					}else
						sidebar.stop().animate({ 'top' : '0px', 'left' : '0px'},'fast');
				});
			}
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função construtora dos Collaps</SUMARY>
		<PARAM NAME="prmAcc">DIV container</PARAM>
		<PARAM NAME="prmCallback"></PARAM>
		----------------------------------------------------------------------------------------------------*/
        fnInitAccordion: function(prmAcc, prmCallback){	
			if( $(prmAcc).length > 0 ){
				$(prmAcc).on('show', function (e) {
         			$(e.target).parents('.accordion-group:first').addClass('active-accordion');
    			});
				$(prmAcc).on('hide', function (e) {
         			$(e.target).parents('.accordion-group:first').removeClass('active-accordion');
    			});
				
				if( prmCallback != null )
					prmCallback();
			}				
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função construtora dos ScrollPanes</SUMARY>
		<PARAM NAME="prmPane">DIV container</PARAM>
		<PARAM NAME="prmCallback"></PARAM>
		----------------------------------------------------------------------------------------------------*/
        fnInitScroll: function(prmPane, prmCallback){	
			if( $(prmPane).length > 0 ){
				$(prmPane).jScrollPane({
					autoReinitialise: true,
					animateScroll: true,
					horizontalGutter: 30
				});
				
				if( prmCallback != null )
					prmCallback();
			}				
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função construtora dos PopOver, inicializa os objetos PopOver com o rel=popover</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnInitPopOver: function(){
			$('a[rel="popover"]').each(function(index, element) {
				/*Remove a tooltip do elemento*/
				if( $(element).attr('data-content') == '' )
					$(element).parent().html( $(this).html() );
				else{				
					var options = {
						html : true,
						trigger : 'hover'
					};
					$(element).popover(options);
				}
			});
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função construtora dos ToolTips, inicializa os objetos tooltios com o rel=tooltip</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        fnInitToolTip: function(){			
            $('a[rel="tooltip"]').each(function(index, element) {
				/*Tratar os espaços duplicados*/
				var title = $(element).attr('title')
								.replace( /^\s+|\s+$/gi, "" )
								.replace( /\s{2,}/gi, " " )
								.replace(/\r\n/g, "")
								.replace( /<br>/g, " " );
								
				$(element).attr('title', title);
				
				/*Remove a tooltip do elemento*/
				if( $(element).attr('title') == '' )
					$(element).parent().html( $(this).html() );
				else{
					$(element).tooltip({
						html : true,
						placement: function(a, element) {
							//title is by tooltip moved to data-original-title
							var title= $(element).attr('data-original-title');
							//create dummy, a div with the same features as the tooltïp
							var dummy=$('<div class="tooltip">'+title+'</div>').appendTo('body');
							
							dummy.remove();
					
							var position = $(element).position();
							if (position.left > 515) {
								return "left";
							}
							if (position.left < 515) {
								return "right";
							}
							if (position.top < 110){
								return "bottom";
							}
							return "top";
						}
					});
				}
			});
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que inicializa e atauliza os clDrops Selects do Plugin jquery.stylish-select</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        clDropSelect: {
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe clDropSelect</SUMARY>	
			----------------------------------------------------------------------------------------------------*/
			fnInit : function(){
				scope.clMaster.clDropSelect.fnConstructor( $('.default-dropdown') );
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Construtora de Select2</SUMARY>	
			----------------------------------------------------------------------------------------------------*/
            fnConstructor : function(prmDrop){
				$(prmDrop).select2({
					formatNoMatches : function(term){
						return 'Nenhum item encontrado';
					}
				});
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que atualiza um Drop Select</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnUpdateDrop: function(prmDrop,prmLista,prmCallback){
				$(prmDrop).prev().find('.select2-choice span:first').addClass('selectedTxt-carregando').html('Carregando...');
				setTimeout(function(){
					$(prmDrop).prev().remove();
					$(prmDrop).html(prmLista)
					scope.clMaster.clDropSelect.fnConstructor( $(prmDrop) );
					
					$('.select2-drop-mask').remove();
					if( prmCallback != false) prmCallback();
				},1000);
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que seta um valor ao Drop Select</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnSetValor: function(prmDrop,prmValue,prmCallback){
				$(prmDrop).select2("data", {
					id: prmValue.id, 
					text: prmValue.text,
					formatNoMatches : function(term){
						return 'Nenhum item encontrado';
					}
				}); 
				$(prmDrop).find('option[value="' + prmValue.id + '"]').change();
				if( prmCallback != false) prmCallback();
			}
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que inicializa o Menu Principal</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        clMainMenu: {
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe Main Menu</SUMARY>
			----------------------------------------------------------------------------------------------------*/
            fnInit: function() {
                this.fnFixMenuContentHeight();
				this.fnMenuFixo();
            },
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que fixa a altura dos Sub-menus do Menu</SUMARY>
			----------------------------------------------------------------------------------------------------*/
            fnFixMenuContentHeight: function() {
                $('#main-menu').find('.submenu-holder').each(function(){
                    var height = $(this).height();
                    $(this).find('.info-wrapper:first').css('height', height + 'px');
                });
            },
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que clona e posiciona o Menu Principal no Topo no evento scroll</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnMenuFixo: function(){
				/*Função para posicionar o Menu principal no TOPO*/
				$(window).scroll(function(){
					var top = $(window).scrollTop();
					
					if( top > 130 && $('#main-menu-fixo').length <= 0 )
						$('.window-wrapper').prepend('<div id="main-menu-fixo" style="position:fixed; top:0; left:0; width:100%; z-index:999;"><div class="header-holder container"><ul id="main-menu" class="main-menu">' + $('#main-menu').html() +'</ul></div></div>');
					else if ( top < 130 )
						$('#main-menu-fixo').remove();
				});
			}
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que controla o SubMenu Lateral Esquerquo</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        clSubMenu: {
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe SubMenu</SUMARY>	
			----------------------------------------------------------------------------------------------------*/
            fnInit: function(){
                this.fnInitSubMenu();
            },
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que: 1. Aplica clique Mostra/Esconde; 2. Expande o Sub-Menu ativo</SUMARY>
			----------------------------------------------------------------------------------------------------*/
            fnInitSubMenu: function(){
                var divSubmenu = $('#submenu');
                var quantItens = $(divSubmenu).children().length;
                if( divSubmenu.length > 0 ){
                    $('a:first', $(divSubmenu).children()).bind('click', function(){
                        var div = $('.tabbable', $(this).parent());
                        var index =  $(this).parent().index();
                        
                        if( div.length > 0 ){						
                            if( div.is(':visible') ){
                                $('.tabbable', $(this).parent()).slideUp();
                                if( (index+1) == quantItens )
                                    $(this).parent().removeClass('submenu-bottom');								 
                            }else{
                                $('.tabbable', $(this).parent()).slideDown();
                                if( (index+1) == quantItens )
                                    $(this).parent().addClass('submenu-bottom');								 
                            }
                            return false;
                        }
                    });
                    
                    /*Abre o #item ATIVO*/
                    $('.tabbable', $(divSubmenu).find('.active')).slideDown();					
                }
            }
        },			
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que inicializa o Banner SlideShow</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        fnSlideShow: function(){
            var holder = $('.main-carousel');			
            
            if( ! holder.is('*') )
                return;
            
            holder.each(function(index, element) {
				/*Verificar se tem Item*/
				if( $('.carousel-inner', element).children().length > 0 ){				
					ctm_nav = $('.ctm-nav', $(element));
					
					$(element).carousel({
						pause : 'hover'
					});			
					
					if( ctm_nav.children().length <= 1 )
						ctm_nav.hide();
					
					/* applies click listener on nav links */
					$('.nav-banner-rotation a', ctm_nav).die('click').live('click', function(e) {
						var idx = parseInt( $(this).attr('data-slide-index') );
						$(element).carousel(idx);
						
						e.preventDefault();
					});
					
					/* event fired when slide animations is over */
					$(element).die('slid').live('slid', function(evt) {
						var idx = $('.item.active:first', $(element)).index();
						
						$('li.active', $(element)).removeClass('active');
						$('li', $(element)).eq(idx).addClass('active');
					});
				}else
					$(element).remove();
			});
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que controla as Redes Sociais do Rodapé</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        clSocialButtonsFooter:{
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função Construtora do Facebook[Compartilhar], Twitter e Google Plus</SUMARY>
			----------------------------------------------------------------------------------------------------*/
            fnRunOnload : function(prmLocalFace){
                /**
				* Criando dinamicamente e inserindo Iframe do facebook
				*/
                var socialButtonsDiv = $(''+prmLocalFace+''),
                iframeFacebook = $('<iframe />')
                .attr({
                    'src':'//www.facebook.com/plugins/like.php?href='+ encodeURIComponent('http://www.lfg.com.br/')+'&send=false&layout=button_count&width=105&show_faces=false&font&colorscheme=light&action=recommend&height=21',
                    'scrolling':'no',
                    'frameborder':'0',
                    'allowTransparency':'true'
                })
                .addClass('iframeFacebook');
                
                socialButtonsDiv.find('.text').after(iframeFacebook);
                
                /**
				* Criando dinamicamente e inserindo Tag scrypt do Twitter
				*/				
                !function(prmObj,prmScript,prmId){
                    var js,
                    fjs = prmObj.getElementsByTagName(prmScript)[0];
                    if(!prmObj.getElementById(prmId)){
                        js = prmObj.createElement(prmScript);
                        js.id = prmId;
                        js.src="//platform.twitter.com/widgets.js";
                        fjs.parentNode.insertBefore(js,fjs);
                    }
                }(document,"script","twitter-wjs");
                
                /**
				* Criando dinamicamente e inserindo Tag scrypt do Google Plus
				*/				
                (function() {
                    var po = document.createElement('script'); 
                    po.type = 'text/javascript'; 
                    po.async = true;
                    
                    po.src = 'https://apis.google.com/js/plusone.js';
                    
                    var s = document.getElementsByTagName('script')[0]; 
                    s.parentNode.insertBefore(po, s);
                })();				
            
            }
        },
        /*----------------------------------------------------------------------------------------------------
		<SUMARY>Função de Mensagem, imprime um box de Alerta, Erro, Sucesso ou Informação</SUMARY>
		<param name="prmTipo">block, error, success, info</param>
		<param name="prmObj">Objeto que receberá o bos da MSG</param>
		<param name="prmMsg">Mensagem</param>
		----------------------------------------------------------------------------------------------------*/
        fnAlerta: function(prmTipo, prmObj, prmMsg){
            switch(prmTipo){
                case 'block' :
                    var h = 'Atenção!';
                    break;
                case 'error' :
                    var h = 'Erro!';
                    break;
                case 'success' :
                    var h = 'Sucesso!';
                    break;
                case 'info' :
                    var h = 'Informação!';
                    break;
            }
            $(prmObj).append('<div class="alert alert-' + prmTipo + '">'
                + '<h4>' + h + '</h4>'							 
                + prmMsg
                + '</div>');
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que Pagina uma lista JSON</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		clPaginacaoAjax: {
			ul : '',
			li : '',
			div : '',
			subarea : '',
			controller : '',
			total : '',
			pag : 1,
			de : 0,
			ate : 0,
			nPags : 0,
			filtros : '',
			conteudo : '',
			msgNoRegistro : '',
			callback : '',
                        areaPaginacao : '',
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função Construtora da Classe clPaginacaoAjax</SUMARY>
			<param name="prmUl">Objeto UL que receberá a lista de itens</param>
			<param name="prmLi">Marcação HTML composta de TAGS, por exemplo: 
			<li>#titulo</li>, sendo que #titulo correponde ao nó titulo do JSON.
			Exemplo e JSON:
			[{"conteudo" : [ {"titulo" : "Lorem"} ],"total" : "0"}]				
			</param>
			<param name="prmSubarea">Um parametro chave que indica uma área, categoria, subárea, etc</param>
			<param name="prmController">URL do controller PHP que irá processar o AJAX</param>
			<param name="prmCall">Função Callback, case negativo:false</param>
			<param name="prmMsgNoRegistro"></param>
			<param name="prmAreaPag"></param>
			----------------------------------------------------------------------------------------------------*/
			fnInit : function(prmUl,prmLi,prmSubarea,prmController,prmCall,prmMsgNoRegistro,prmAreaPag){
				/*Armazenado variaveis globais de controle*/
				scope.clMaster.clPaginacaoAjax.ul = $(prmUl);
				scope.clMaster.clPaginacaoAjax.li = prmLi;
				scope.clMaster.clPaginacaoAjax.div = $(prmUl).parents('.default-box:first');
				scope.clMaster.clPaginacaoAjax.form = $('#type-filter-form');
				scope.clMaster.clPaginacaoAjax.subarea = prmSubarea;
				scope.clMaster.clPaginacaoAjax.controller = prmController;
				scope.clMaster.clPaginacaoAjax.de = 1;
				scope.clMaster.clPaginacaoAjax.ate = scope.clMaster.nItensPag;
				scope.clMaster.clPaginacaoAjax.msgNoRegistro =  prmMsgNoRegistro == undefined ? 'Não foram encontrados resultados utilizando as opções selecionadas. Por favor, reveja os filtros e tente novamente' : prmMsgNoRegistro;
				scope.clMaster.clPaginacaoAjax.callback = prmCall;
				scope.clMaster.clPaginacaoAjax.areaPaginacao = prmAreaPag == undefined ? '' : prmAreaPag;
				scope.clMaster.clPaginacaoAjax.fnAjax();				
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que lista os Filtros, pode ser do tipo: checkbox, select e hidden(No caso de MultiDrop)</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnRetornarFiltros: function(){
				var listaFiltros = '';               
				
				/*Filtros do tipo Checkebox*/
				var fChecks = $(':checkbox:checked', scope.clMaster.clPaginacaoAjax.form);
				if( fChecks.length > 0 ){					
					/*Montando Lista de Filtros*/
					$.each(fChecks, function(i,v){
						listaFiltros += $(v).val() + ',';
					});
					listaFiltros = listaFiltros.replace(/,$/g,'');
				}
								
				/*Filtros do tipo Selects*/
				var fSelects = $('select', scope.clMaster.clPaginacaoAjax.form);
				if( fSelects.length > 0 ){	
					/*Montando Lista de Filtros*/
					listaFiltros = listaFiltros != '' ? listaFiltros + ',' : listaFiltros;
					$.each(fSelects, function(i,v){
						valor = $(v).find('option:selected').val();
						if( valor != '0' )
							listaFiltros += valor + ',';
					});
					listaFiltros = listaFiltros.replace(/,$/g,'');
				}
				
				/*Filtros do tipo TAGS*/
				var fTags = $('input[type="hidden"]', scope.clMaster.clPaginacaoAjax.form);
				if( fTags.length > 0 ){	
					/*Montando Lista de Filtros*/
					listaFiltros = listaFiltros != '' ? listaFiltros + ',' : listaFiltros;
					$.each(fTags, function(i,v){
						valor = $(v).val();
						if( valor != '0' )
							listaFiltros += valor + ',';
					});
					listaFiltros = listaFiltros.replace(/,$/g,'');
				}
				
				/*Filtros do tipo SLIDER*/
				var fSliders = $('input[type="slider"]', scope.clMaster.clPaginacaoAjax.form);
				if( fSliders.length > 0 ){	
					/*Montando Lista de Filtros*/
					listaFiltros = listaFiltros != '' ? listaFiltros + ',' : listaFiltros;
					$.each(fSliders, function(i,v){
						valor = $(v).val();
						if( valor != '0' )
							listaFiltros += valor + ',';
					});
					listaFiltros = listaFiltros.replace(/,$/g,'');
				}
				
				scope.clMaster.clPaginacaoAjax.filtros =  listaFiltros;
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que dispara AJAX para a controller e lista os itens</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnAjax : function(){				
				/*Ler os Filtros*/
				scope.clMaster.clPaginacaoAjax.fnRetornarFiltros();
				
				/*Inicia o AJAX para o Conteudo*/
				$.ajax({				
					url: scope.clMaster.clPaginacaoAjax.controller,
					data : { 
						Ate : scope.clMaster.clPaginacaoAjax.ate, 
						De : scope.clMaster.clPaginacaoAjax.de, 						
						Filtros : scope.clMaster.clPaginacaoAjax.filtros, 
						Subarea : scope.clMaster.clPaginacaoAjax.subarea
					},
					type: 'GET',
					dataType: "JSON",	
					error: function(err){
						/*Limpar Lista*/
						scope.clMaster.clPaginacaoAjax.ul.html('');
						$('.pagination-container', scope.clMaster.clPaginacaoAjax.div).html('');						
						
						/*Limpa Mensagens de Erros*/
						$('.alert', scope.clMaster.clPaginacaoAjax.div).remove();
						/*Info*/
						scope.clMaster.fnAlerta('error',scope.clMaster.clPaginacaoAjax.div,'Ocorreu um erro, tente novamente mais tarde!');
						
						/*Seta variavel conteudo=false para flegar a mensagem de erro*/
						scope.clMaster.clPaginacaoAjax.conteudo = null;
					},
					beforeSend: function(){ 
						/*Limpa Mensagens de Erros*/
						$('.alert', scope.clMaster.clPaginacaoAjax.div).remove();						
						/*Resetando valores*/
						scope.clMaster.clPaginacaoAjax.conteudo = "";
						/*Carregando*/
                    	scope.clMaster.fnCarregar(true,scope.clMaster.clPaginacaoAjax.div,'Aguarde...');						
					},
					success: function(prmDados){
						try{
							var dados = eval(prmDados);
							/*Total de Registros*/
							scope.clMaster.clPaginacaoAjax.total = parseInt(dados[0].total);
							
							//*Montando Lista de Conteúdo*//
							$.each(dados[0].conteudo,function(i,v1){
								var aux = typeof(scope.clMaster.clPaginacaoAjax.li) == 'string' ? scope.clMaster.clPaginacaoAjax.li : scope.clMaster.clPaginacaoAjax.li[0];
								
								/*Laço nos atributos*/
								$.each(v1,function(j,v2){
									var valor = v2;
									var style= '';
									var regex = new RegExp('#'+j,'g');
									
									/*Tratar Particularidades*/									
									switch(j){
										case 'link_evento' :	
											if( valor == null || valor == "" || valor == "null" )
												valor = '';
											else{
												var lnk = valor.indexOf('http://') > -1 ? valor : 'http://' + valor;
												valor = lnk;
											}
											break;
										case 'periodo' :
											valor = v2.join();
											style = valor != '' ? 'style="display:block;"' :  'style="display:none;"';
											aux = aux.replace('#' + j + 'Style', style);
											break;
										case 'descricao' :
											valor = scope.clMaster.fnMinMaxCaracteres(v2,210);
											break;
										case 'itens' : case 'cursos' :
											valor = '';																						
											li = '';
											$.each(v2,function(k,v3){
												aux2 = scope.clMaster.clPaginacaoAjax.li[1];
												$.each(v3,function(l,v4){
													regex2 = new RegExp('#'+l,'g');
													aux2 = aux2.replace(regex2, v4);
												});
												li += aux2;											
											});	
											valor = li;									
											break;
										case 'edital' :	
											if( valor == null || valor == "" || valor == "null" || valor == "Não informado" )
												valor = 'Não informado';
											else{
												var edital = valor.indexOf('http://') > -1 ? valor : 'http://' + valor;
												valor = '<a href="' + edital +'" target="_blank" title="Clique aqui para abrir o Edital">Clique aqui</a>'
											}
											break;
										case 'imagem' :
											valor = valor != "" ? '<img src="' + valor + '" width="162px" class="img-rounded pull-left"  />' : "";
											break;
									}
									aux = aux.replace(regex, valor);
								});
								scope.clMaster.clPaginacaoAjax.conteudo += aux;
							});														
						}catch(err){
							/*Limpar Lista*/
							scope.clMaster.clPaginacaoAjax.ul.html('');
							$('.pagination-container', scope.clMaster.clPaginacaoAjax.div).html('');
							
							/*Limpa Mensagens de Erros*/
							$('.alert', scope.clMaster.clPaginacaoAjax.div).remove();
							/*Info*/
							scope.clMaster.fnAlerta('error',scope.clMaster.clPaginacaoAjax.div,'Ocorreu um erro, tente novamente mais tarde!');
						}
					},
					complete: function(){
						/*Ocorreu um Erro*/
						if( scope.clMaster.clPaginacaoAjax.conteudo == null ){
							scope.clMaster.clPaginacaoAjax.ul.hide();
							$('.pagination-container', scope.clMaster.clPaginacaoAjax.div).hide();
						}else{							
							if( scope.clMaster.clPaginacaoAjax.conteudo != '' ){
								/*Deu tudo CERTO*/
								
								/*Inserindo Conteúdo*/
								scope.clMaster.clPaginacaoAjax.ul.show().html(scope.clMaster.clPaginacaoAjax.conteudo);
								
								/*Montar a Navegação*/
								scope.clMaster.clPaginacaoAjax.fnNavegacao();															
							}else{								
								/*CONTEUDO VAZIO*/
								
								scope.clMaster.clPaginacaoAjax.ul.hide();
								$('.pagination-container', scope.clMaster.clPaginacaoAjax.div).hide();
								
								/*Limpa Mensagens de Erros*/
								$('.alert', scope.clMaster.clPaginacaoAjax.div).remove();
								/*Info*/
								scope.clMaster.fnAlerta('info',scope.clMaster.clPaginacaoAjax.div,scope.clMaster.clPaginacaoAjax.msgNoRegistro);
							}
						}
						/*Carregando*/
						scope.clMaster.fnCarregar(false,scope.clMaster.clPaginacaoAjax.div,false);
						
						/*Inicia os Eventos de Navegação*/
						scope.clMaster.clPaginacaoAjax.fnEventos();
						
						/*CallBack*/
						if( typeof(scope.clMaster.clPaginacaoAjax.callback) == 'function' ) 
							scope.clMaster.clPaginacaoAjax.callback();
					}
				});	
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que Monta a Navegação</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnNavegacao: function(){
				var t = scope.clMaster.fnIsInteger(scope.clMaster.clPaginacaoAjax.total/scope.clMaster.nItensPag);
                scope.clMaster.clPaginacaoAjax.nPags =  !t ? parseInt(scope.clMaster.clPaginacaoAjax.total/scope.clMaster.nItensPag)+1 : (scope.clMaster.clPaginacaoAjax.total/scope.clMaster.nItensPag);
				
				var dropPag ='<div class="btn-group" id="pag-nItens">'
							+ '<button class="btn dropdown-toggle" data-toggle="dropdown">'
							+ '<i class="icon-list"></i> ' + scope.clMaster.nItensPag + ' Itens/Página &nbsp;'
							+ '<span class="caret"></span>'
							+ '</button>'							
							+ '<ul class="dropdown-menu pag-nItens-drop">'
							+ '<li><a href="javascript:void(0);" rel="4">4 Itens</a></li>'
							+ '<li><a href="javascript:void(0);" rel="10">10 Itens</a></li>'
							+ '<li><a href="javascript:void(0);" rel="20">20 Itens</a></li>'
							+ '<li><a href="javascript:void(0);" rel="30">30 Itens</a></li>'
							+ '<li><a href="javascript:void(0);" rel="40">40 Itens</a></li>'
							+ '<li><a href="javascript:void(0);" rel="50">50 Itens</a></li>'
							+ '<li><a href="javascript:void(0);" rel="100">100 Itens</a></li>'
							+ '</ul>'
							+ '</div>';
				
				if( scope.clMaster.clPaginacaoAjax.nPags > 1 ){					
                    var pag = '<li class="' + ( scope.clMaster.clPaginacaoAjax.pag == 1 ? 'disabled ' : '' ) + 'pag-primeiro"><a class="pag" href="javascript:void(0);">«</a></li>';
					var totalAte = ( scope.clMaster.clPaginacaoAjax.ate - scope.clMaster.nItensPag ) + scope.clMaster.clPaginacaoAjax.ul.children().length;
                    for(k=1;k<=scope.clMaster.clPaginacaoAjax.nPags;k++){
                    	pag += '<li ' 
                            + ( k== scope.clMaster.clPaginacaoAjax.pag ? ' class="active"' : '' )
                            + ( k >= (scope.clMaster.clPaginacaoAjax.pag-3) &&
							    k <= ( k<=7 ? 7 : (scope.clMaster.clPaginacaoAjax.pag+3) )
							? ' style="display: inline;"' : 'style="display: none;"') 
                            + '><a href="javascript:void(0);" class="pag" rel="' + k + '">' + k + '</a></li>';
                     }
                     pag = '<div class="pagination">'
					 	 + '<ul>' 
					 	 +  pag + '<li class="' + ( scope.clMaster.clPaginacaoAjax.pag == scope.clMaster.clPaginacaoAjax.nPags ? 'disabled ' : '' ) + 'pag-ultima"><a class="pag" href="javascript:void(0);">»</a></li>'
						 + '</ul>'
						 + '<ul>'
						 + '<li class="disabled"><a href="javascript:void(0);">Exibindo ' + scope.clMaster.clPaginacaoAjax.de + ' ao ' + totalAte + ' de ' + scope.clMaster.clPaginacaoAjax.total +' '+scope.clMaster.clPaginacaoAjax.areaPaginacao+  '</a></li>'
						 + '</ul>'
						 + '</div>'						 
						 + dropPag;
				}else{
					pag = '<div class="pagination">'
					 	 + '<ul>' 
					 	 + '<li class="disabled" style="display: inline;" class="active"><a class="pag" href="javascript:void(0);" rel="1">1</a></li>'
						 + '</ul>'
						 + '</div>'						 
						 + dropPag;
				}
				$('.pagination-container', scope.clMaster.clPaginacaoAjax.div).show().html(pag);
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que dispara os Eventos da Navegação, Filtros, etc</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnEventos: function(){
				/*Evento nos botoes de Pag*/				
                $('a.pag:visible', $('.pagination', scope.clMaster.clPaginacaoAjax.div)).die().bind('click', function(){				
                    if( !$(this).parent().hasClass('disabled') ){
						if( !$(this).parent().hasClass('pag-primeiro') && !$(this).parent().hasClass('pag-ultima') ){							
							scope.clMaster.clPaginacaoAjax.pag = parseInt($(this).text());
							scope.clMaster.clPaginacaoAjax.ate = scope.clMaster.clPaginacaoAjax.pag*scope.clMaster.nItensPag;
							scope.clMaster.clPaginacaoAjax.de = (scope.clMaster.clPaginacaoAjax.ate-scope.clMaster.nItensPag)+1;
							scope.clMaster.clPaginacaoAjax.fnAjax();
						}else{
							var aux = parseInt($('.active', $('.pagination', scope.clMaster.clPaginacaoAjax.div)).find('a').text());
							var index = $(this).parent().hasClass('pag-primeiro') ? aux-1 : aux+1;
							if( index >= 1 	&& index <= scope.clMaster.clPaginacaoAjax.nPags )
								$('.pagination ul', scope.clMaster.clPaginacaoAjax.div).children('li:eq(' + index + ')').find('a').click();
						}
					}
					
					$('html, body').animate({
                    	scrollTop: scope.clMaster.clPaginacaoAjax.div.offset().top
					}, 'slow');
				});
				/*Drop com Número de Itens por Página*/
				$('.pag-nItens-drop a').die().bind('click', function(){
					$.cookie('nPags', $(this).attr('rel') );
					scope.clMaster.nItensPag = parseInt( $(this).attr('rel') );
					
					$('html, body').animate({
                    	scrollTop: scope.clMaster.clPaginacaoAjax.div.offset().top
					}, 'slow');
					
					scope.clMaster.clPaginacaoAjax.pag = 1;
					scope.clMaster.clPaginacaoAjax.ate = scope.clMaster.clPaginacaoAjax.pag*scope.clMaster.nItensPag;
                   	scope.clMaster.clPaginacaoAjax.de = (scope.clMaster.clPaginacaoAjax.ate-scope.clMaster.nItensPag)+1;
					scope.clMaster.clPaginacaoAjax.fnAjax();
				});	
				/*Drop Multi-Select com Tags*/			
				$('.dropdown-tags-list a').die().live('click', function(){
					var li = $(this).parent(),
						valor = $(this).attr('rel'),
						texto = $(this).html(),
						lista = $('.tags-list'),
						tag = '',
						drop = $('.dropdown-tags-list')
					
					li.remove();
					tag = ('<a href="javascript:void(0);" class="label remove-tag" style="display:none;">'
						 + '<span class="icon-remove"></span>'
						 + '<input type="hidden" value="' + valor + '" data-value="' + texto + '">'
						 + texto
						+ '</a>');
						
					switch( valor ){
						case 'todos' :
							var flag = lista.children().length;
								
							if( $('a', lista).length > 0 ){
								$('a', lista).each(function(index, element) {
									var el = $(this),
										val = $('input', el).val(),
										tex = $('input', el).attr('data-value');	
									
									el.fadeOut('fast',function(){
										drop.append('<li><a href="javascript:void(0);" rel="' + val + '">' + tex +'</a></li>');
										el.remove();
									});								
									
									if( (index+1) == flag ){
										lista.append(tag);
										$('a:last', lista).fadeIn();
									}
								});
							}else{
								lista.append(tag);
								$('a:last', lista).fadeIn();
							}
							break;
						default :
							/*Verifica se tem o todos e o remove*/
							var tagTodos = $('input[value="todos"]', lista);
							if( tagTodos.length > 0 ){
								tagTodos.parents('.label:first').fadeOut('fast',function(){
									drop.append('<li><a href="javascript:void(0);" rel="todos">Todos</a></li>');
									tagTodos.parents('.label:first').remove();
								});	
							}
							
							lista.append(tag);						
							$('a:last', lista).fadeIn();
							break;
					}

					/*Verifica se a lista está vazia*/
					if( $('.dropdown-tags-list').children().length < 1 )
						$('.dropdown-tags-list').append('<li class="dropdown-lista-vazia"><span>Nenhum item!</span></li>');
					if( $('.dropdown-tags-list').children().length > 0 )
						$('.dropdown-lista-vazia').remove();
				});
				/*Remover Tag*/
				$('.remove-tag').die().live('click', function(){
					var tag = $(this),
						valor = $('input', tag).val(),
						texto = $('input', tag).attr('data-value'),
						drop = $('.dropdown-tags-list');
						
					tag.fadeOut('fast',function(){
						drop.append('<li><a href="javascript:void(0);" rel="' + valor + '">' + texto +'</a></li>');
						tag.remove();
					});
					
					/*Verifica se a lista está vazia*/
					if( $('.dropdown-tags-list').children().length > 0 )
						$('.dropdown-lista-vazia').remove();
				});				
				/*Filtros*/
                $('button[name="filter-buscar"]', scope.clMaster.clPaginacaoAjax.form).unbind('click').bind('click', function(){
                    
					$('html, body').animate({
                    	scrollTop: scope.clMaster.clPaginacaoAjax.div.offset().top
					}, 'slow');
					
					scope.clMaster.clPaginacaoAjax.pag = 1;
					scope.clMaster.clPaginacaoAjax.ate = scope.clMaster.clPaginacaoAjax.pag*scope.clMaster.nItensPag;
                   	scope.clMaster.clPaginacaoAjax.de = (scope.clMaster.clPaginacaoAjax.ate-scope.clMaster.nItensPag)+1;
					scope.clMaster.clPaginacaoAjax.fnAjax();					
                    return false;
                });				
			}
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função testa se número é inteiro</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        fnIsInteger: function(prmVal) {
            if ((undefined === prmVal) || (null === prmVal))
                return false;
            return prmVal % 1 == 0;
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função testa se número ou string, retorno true/false</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnIsNumeric: function(str){
		  var er = /^[0-9]+$/;
		  return (er.test(str));
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que arredonda um valor decimal</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnArredondarNum: function(prmVal,prmCasas){
		   var novo = Math.round( prmVal * Math.pow( 10 , prmCasas ) ) / Math.pow( 10 , prmCasas );
		   return( novo );
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que mostra um Carregando</SUMARY>
		<param name="flag">True - Cria o Carregando/False - Remove o Carregando</param>
		<param name="box">Objeto de Destino</param>
		<param name="msg">Mensagem do Carregando</param>
		----------------------------------------------------------------------------------------------------*/
        fnCarregar: function(prmFlag,prmBox,prmMsg){
            var bloque = $(prmBox);
            
			if(prmFlag)                
                bloque.append('<div class="carregando-ajax"><div><span>&nbsp;</span>' + prmMsg +'</div></div>');					
            else
                setTimeout( function(){
                    bloque.find('.carregando-ajax').remove()
                }, 1000);
        },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que trata o tamanho de uma string</SUMARY>
		<param name="prmString">Texto</param>
		<param name="prmMax">Tamanho máximo em inteiro</param>
		----------------------------------------------------------------------------------------------------*/
		fnMinMaxCaracteres: function(prmString,prmMax){
			if( prmString == false || prmString == true || prmString == null )
				return "";
				
			expString = prmString.split(/\s+/);
			texto = '';
			flag = 0;
			
			for(i=0;i<expString.length;i++){				
				if( (texto.length + expString[i].length) <= prmMax && flag == i){
					texto += expString[i] + ' ';
					flag++;
				}
			}
			return  texto.length < prmString.length ?  texto + '...' : texto;
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que Retira os acentos</SUMARY>
		<param name="prmPalavra>Texto</param>
		----------------------------------------------------------------------------------------------------*/
		fnRetiraAcentos : function(prmPalavra) {
			if ( prmPalavra == "" || prmPalavra == null || typeof(prmPalavra) == 'undefined' )
				return "";
			
			com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ';
			sem_acento = 'aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC';
			especiais = '()[]{}!@#$%&*-+=_|?<>';
			nova ='';			
			for(i=0;i<prmPalavra.length;i++) {
				flag = true;
				letra = prmPalavra.substr(i,1);
				/*Pesquisa por caracteres especiais*/
				for( j=0; j<especiais.length;j++){
					caracter = especiais.substr(j,1);
					if( letra == caracter ){
						flag = false;
						break;
					}
				}				
				/*Faz a troca do acento*/
				nova += (flag) ?
							( com_acento.search(letra)>=0) ? 
									sem_acento.substr(com_acento.search(letra),1) :
									letra
							: letra;
			}
			return nova;
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que monta uma URL Amigável</SUMARY>
		<param name="prmUrl">Texto</param>
		----------------------------------------------------------------------------------------------------*/
		fnUrlAmigavel : function(prmUrl){
			prmUrl = scope.clMaster.fnRetiraAcentos( prmUrl.toLowerCase() );
			
			/*Remove caracteres especiais*/
			var specialChars = "!@#$^&%*()+=-[]{}|:<>?,.ªº";
    		for (var i = 0; i < specialChars.length; i++) {
    			prmUrl = prmUrl.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
			}
			/*Troca caracteres especiais por -*/
			var specialChars = "\/";
    		for (var i = 0; i < specialChars.length; i++) {
    			prmUrl = prmUrl.replace(new RegExp("\\" + specialChars[i], 'gi'), '-');
			}
			/*Remove espaços duplicados*/
			prmUrl = prmUrl.replace(/\s{2,}/g, ' ');
			
			/*Retorno URL Amigável*/
			return prmUrl.replace(/ /g, '-');
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que retorna uma valor de parãmetro de uma URL</SUMARY>
		<param name="prmParam">Nome do parâmetro</param>
		----------------------------------------------------------------------------------------------------*/
		fnGetParamURL : function(prmParam){
			var url   = window.location.search.replace("?", "");
		  	var itens = url.split("&");
		
		  	for(n in itens){
				if( itens[n].match(prmParam) )
			  		return decodeURIComponent(itens[n].replace(prmParam+"=", ""));			
		  	}
		  	return null;
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que quebra uma URL e retorna um valor</SUMARY>
		<param name="prmParam">Nome do parâmetro, no caso usa-se uma 
		string anterior ao parâmetro como referência</param>
		----------------------------------------------------------------------------------------------------*/
		fnGetPathURL : function(prmParam){
			var url   = window.location.pathname;
		  	var itens = url.split("/");
		
		  	for(n in itens){
				var x = parseInt(n)+1;
			  	if( itens[n].match(prmParam) )
			  		return typeof( itens[x] ) != 'undefined' ? decodeURIComponent(itens[x]) : "";			
		  	}
		  	return null;
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função para Capitular, Capitula todas as primeiras letras em maiusculas</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnCapitular: function(prmPalavra){
			var palavras = prmPalavra.split(' ');
			var nova = '';
			
			$.each(palavras, function(i,v){
				nova += v.substring(0,1) + (v.substring(1,v.length)).toLowerCase() + ' ';
			});
			
			return nova.replace(/ $/g,'');
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que monta um Modal</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		clModalAlerta: {
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe clModalAlerta</SUMARY>
			<param name="prmTit">Título do Modal</param>
			<param name="prmConteudo">Conteúdo HTML do Modal</param>
			----------------------------------------------------------------------------------------------------*/
			fnInit: function(prmTit,prmConteudo){
				var html = '<div id="modal-alerta" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'
						 + '<div class="modal-header">'
						 + '<button type="button" class="close modal-alerta-fechar" data-dismiss="modal" aria-hidden="true">×</button>'
						 + '<h3 id="modal-alertaLabel">' + prmTit + '</h3>'
						 + '</div>'
						 + '<div class="modal-body">'
						 + prmConteudo
						 + '</div>'
						 + '<div class="modal-footer">'
						 + '<button class="btn modal-alerta-fechar" data-dismiss="modal" aria-hidden="true">Fechar</button>'
						 + '</div>'
						 + '</div>';
				if( $('#modal-alerta').length > 0 ){
					$('#modal-alerta').remove().modal('hide');
				}
				$('body').append(html);
				$('#modal-alerta').modal({
					backdrop : false,
					keyboard : true
				});
				scope.clMaster.clModalAlerta.fnFechar();
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função no botão FECHAR</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnFechar: function(){
				$('.modal-alerta-fechar').off('click').on('click', function(){
					$('#modal-alerta').modal('hide');
				});
			}			
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe Genérica para Validar tipos específicos</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		clValidacao: {
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função para validar CPF</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnCPF : function (prmCPF) {
				cpf = prmCPF.replace(/[^\d]+/g,'');
				if(cpf == '') return false;

				// Elimina CPFs invalidos conhecidos
				if (cpf.length != 11 || 
					cpf == "00000000000" || 
					cpf == "11111111111" || 
					cpf == "22222222222" || 
					cpf == "33333333333" || 
					cpf == "44444444444" || 
					cpf == "55555555555" || 
					cpf == "66666666666" || 
					cpf == "77777777777" || 
					cpf == "88888888888" || 
					cpf == "99999999999")
					return false;
				 
				// Valida 1o digito
				add = 0;
				for (i=0; i < 9; i ++)
					add += parseInt(cpf.charAt(i)) * (10 - i);
				rev = 11 - (add % 11);
				if (rev == 10 || rev == 11)
					rev = 0;
				if (rev != parseInt(cpf.charAt(9)))
					return false;
				 
				// Valida 2o digito
				add = 0;
				for (i = 0; i < 10; i ++)
					add += parseInt(cpf.charAt(i)) * (11 - i);
				rev = 11 - (add % 11);
				if (rev == 10 || rev == 11)
					rev = 0;
				if (rev != parseInt(cpf.charAt(10)))
					return false;
					 
				return true;
			}				
		}
    };
    
    $(function(){
        scope.clMaster.fnInit();
    });
    
    $(window).load(function(){
        scope.clMaster.clSocialButtonsFooter.fnRunOnload('.footer-wrapper .container .content .social-buttons');		
    });

})(lfgportal.app, jQuery);