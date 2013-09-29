/*==================================================================
CLASSE clHome{}
==================================================================*/
var lfgportal = lfgportal || {};
lfgportal.app = lfgportal.app || {};

;(function(scope, $) {
	scope.clHome = {
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função constutora da Classe clHome</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnInit: function(){			
			scope.clHome.fnCursosDestaque();
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que constrói o box de Cursos em Destque</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnCursosDestaque: function(){
			var holder = $('#cursos-destaque-tab');			
			holder.tab();
			
			/*Tratamento de quantidade de Itens na Descrição*/
			$('.carousel-cursos-em-destaque').each(function(){
				$(this).children().each(function(){
					var prmEl = $('.curso-desc', $(this)),
					    prmString = prmEl.html(),
						texto = scope.clMaster.fnMinMaxCaracteres(prmString,60);
					prmEl.html(texto);
				});
			});
			
			var carousel = $('.carousel-cursos-em-destaque:first');
			carousel.jcarousel({
				scroll: 1,
				wrap: 'last',
				initCallback : function(){
					var nItens = carousel.children().length;
					
					if( nItens < 2 ){
						carousel.css({
							'width' : '96%'
						}).children().css({
							'width' : '100%'
						});
					}
					if( nItens <= 2 ){
						$('.jcarousel-prev', carousel.parents('.jcarousel-container:first')).hide();
						$('.jcarousel-next', carousel.parents('.jcarousel-container:first')).hide();
					}
				}
			});
			
			
			/* applies click listener on tab links */
			$('a', holder).bind('click', function(e) {
				e.preventDefault();
				$(this).tab('show');
				
				/*Analytics*/
				_gaq.push(['_trackEvent', 'home', 'destaque', $(this).attr('href').replace('#','')]);
				
				var carousel = $($(this).attr('href')).find('.carousel-cursos-em-destaque');
				if( carousel.parent('.jcarousel-clip:first').length <= 0 ){
					if( carousel.length > 0 ){
						carousel.jcarousel({
							scroll: 1,
							wrap: 'last',
							initCallback : function(){
								var nItens = carousel.children().length;
								
								if( nItens < 2 ){
									carousel.css({
										'width' : '96%'
									}).children().css({
										'width' : '100%'
									});									
								}
								if( nItens <= 2 ){
									$('.jcarousel-prev', carousel.parents('.jcarousel-container:first')).hide();
									$('.jcarousel-next', carousel.parents('.jcarousel-container:first')).hide();
								}
							}
						});
					}else{
						var div = $($(this).attr('href'));
						if( $('.alert', div).length <=0  ){
							var info = div.html();						
							$($(this).attr('href')).html('');
							scope.clMaster.fnAlerta('info', div,info);
						}
					}
				}								
			});
		},
        /*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que inicializa a tag Crazy Egg</SUMARY>
		----------------------------------------------------------------------------------------------------*/
        clTagCrazyEgg : {
			fnInit: function() {
				this.fnInitTagCrazyEgg();
			},
			/*----------------------------------------------------------------------------------------------------
			SUMARY>Função que insere o Script do [http://dnn506yrbagrg.cloudfront.net/]</SUMARY>
			--------------------------------------------------------------------------------------------------*/
			fnInitTagCrazyEgg: function() {
				var script = document.createElement("script");
			  	script.type = "text/javascript";
			  	script.src = document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0010/4256.js?"+Math.floor(new Date().getTime()/3600000);
			  	document.body.appendChild(script);
			 }
         },
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que inicializa a API do Face BOOK</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		clBoxFacebook: {
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe BoxFaceBook</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnInit: function() {
				this.fnInitFace();
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que insere iFrame da API do Face book</SUMARY>
			----------------------------------------------------------------------------------------------------*/
			fnInitFace: function() {
				//Carregando
				lfgportal.app.clMaster.fnCarregar(true,$('.carometro-facebook'),'Carregando...');
				//IFrame			
				var carometroDiv = $('.carometro-facebook'),
					iframeFacebook = $('<iframe />')
						.attr({
							'src':'//www.facebook.com/plugins/likebox.php?href='
								+ encodeURIComponent('http://www.facebook.com/RedeLFG')
								+ '&width=298'
								+ '&height=290'
								+ '&connections=5'
								+ '&show_faces=true'
								+ '&colorscheme=light'
								+ '&stream=false'
								+ '&border_color=%23446fa9'
								+ '&header=true'
								+ '&appId=515794031765936',
							'scrolling' : 'no',
							'frameborder' : '0',
							'allowTransparency' : 'false',
							'style' : 'border:none; overflow:hidden; width:298px; height:290px;'
						})
						.addClass('carometroFacebookIframe');						

				carometroDiv.append(iframeFacebook);
				//Remover Carregando
				lfgportal.app.clMaster.fnCarregar(false,$('.carometro-facebook'),false);
			}
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que inicializa a API do Twitter</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		clBoxTwitter : {
			tab : '<div class="media">'
			  +  '<img class="pull-left" src="#thumb_url" alt="" width="48" height="48" />'
			  +  '<div class="media-body">'
			  +  '<h4 class="media-heading">#userNome</h4>'
			  +  '<p><a class="media-user" target="_blank" href="#userLink">#screenName</a>'
			  +  '<span class="pull-right twitter-logo">Twitter</span></p>'
			  +  '</div>'
			  +  '</div>'
			  +  '<div class="scroll-pane">'
			  +  '<ul class="twitters-list">#listaTwitteres</ul>'
			  +  '</div>'
			  +  '</div>',
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe clBoxTwitter</SUMARY>
			<param name="prmTwittes">Objeto Array os Users, exemplo:
			[ ['LFG','@PortalLFG',5],
			  ['Anhanguera','@AnhangueraEducacional',5]
			]
			</param>
			----------------------------------------------------------------------------------------------------*/
			fnInit : function(prmTwittes){
				var box = $('.twitter-tab-box'),
					nav = '',
					tabs = '',
					holder = $('#twitter-tab'),
					json = $('#prmTwitter', box);
			
					if( box.length > 0 ){
						try{
							var prmDados = eval(json.val().replace(/\r\n/g, ""));
							
							if( prmDados.length > 0){
								$.each(prmDados,function(index,value){
									nav += '<li ' + (index==0 ? 'class="active"' : '') + '><a href="#twitter-' + value[1].replace('@','') + '">' + value[0] + '<span class="down-arrow"></span></a></li>';
									tabs += '<div class="tab-pane' + (index==0 ? ' active"' : '') + '" id="twitter-' + value[1].replace('@','') + '"></div>';
								});
								$('.nav-tabs', box).html(nav);
								$('.tab-content', box).html(tabs);
								
								/* applies click listener on tab links */
								$('a', holder).bind('click', function(e) {
									e.preventDefault();
									$(this).tab('show');  					
								});
								
								/*Iniciar a API do Twitter*/
								scope.clHome.clBoxTwitter.fnInitTwitter(prmDados);
							}else
								scope.clMaster.fnAlerta('info', box, 'Não há nenhum registro!');
						}catch(err){
							if( json.val().replace(/\r\n/g, "") == '' )
								scope.clMaster.fnAlerta('info', box, 'Não há nenhum registro!');
							else
								scope.clMaster.fnAlerta('block', box, 'Ocorreu um erro interno, tente novamente!');															
						}
						json.remove();
					}
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função que lê um AJAX dos Twittes</SUMARY>
			<param name="prmTwittes">Objeto Array os Users, exemplo:
			[ ['LFG','@PortalLFG',5],
			  ['Anhanguera','@AnhangueraEducacional',5]
			]
			</param>
			----------------------------------------------------------------------------------------------------*/
			fnInitTwitter: function(prmTwittes){
				$.each(prmTwittes,function(index,value){
					var box = $('.twitter-tab-box'),
						listaTwitteres = '',
						thumb_url = '',
						userNome = '';
					
					//Carregando					
					lfgportal.app.clMaster.fnCarregar(true,box,'Carregando...');
					
					$.getJSON('http://api.twitter.com/1/statuses/user_timeline.json?callback=?',{
						screen_name: value[1],
						count: value[2],
						include_rts: true
					},
					function(data) {
						$.each(data, function(i, conteudo){
							var data = new Date(conteudo.created_at),
								dia = data.getDay(),
								mes = data.getMonth(),
								ano = data.getFullYear(),
								hora = data.getHours(),
								minutos = data.getMinutes();

								listaTwitteres +='<li class="item">'
											 + '<div class="content-holder">'
											 + '<p class="twitter">'
											 + '<a href="http://www.twitter.com/' + value[1] + '" target="_blank">'+ value[1] +'</a>&nbsp;'
											 + conteudo.text
											 + '</p>'
											 + '<p class="info">'+dia+'/'+mes+'/'+ano+' - '+hora+':'+ minutos+'</p>'
											 + '</div>'
											 + '</li>';
											 
								thumb_url = conteudo.user.profile_image_url;
								userNome = conteudo.user.name;
							});
						}
					).complete(function() { 
						//Remover Carregando
						lfgportal.app.clMaster.fnCarregar(false,box,false);
						
						//Inserir Lista de twitteres e dados do Usuário
						var html = scope.clHome.clBoxTwitter.tab
									.replace('#listaTwitteres', listaTwitteres)
									.replace('#userNome', userNome)
									.replace('#screenName', value[1])
									.replace('#thumb_url', thumb_url)
									.replace('#userLink', 'http://www.twitter.com/' + value[1]),
							boxTwitter = $('#twitter-' + value[1].replace('@','') );
						//Insere a Lista												
						boxTwitter.html(html);
						//ScrollPane
						lfgportal.app.clMaster.fnInitScroll( $('.scroll-pane', boxTwitter), null );
					});
				});
			}
		}
	};

	$(function() {
		scope.clHome.fnInit();
	});

	$(window).load(function(){
		scope.clHome.clBoxFacebook.fnInit();
		scope.clHome.clBoxTwitter.fnInit();
		scope.clHome.clTagCrazyEgg.fnInit();
	});

})(lfgportal.app, jQuery);