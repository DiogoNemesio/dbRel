/*==================================================================
CLASSE clVideo{}
==================================================================*/
var lfgportal = lfgportal || {};
lfgportal.app = lfgportal.app || {};

;(function(scope, $) {
	scope.clVideo = {
		idCanal : '8a4fe5b8cff81031c87fa14a70070106', /*ID do Canal Sambatech*/
		apiKey : '593f31176fbbb461ac8561971c07d058', /*API Key da Sambatech*/
		videoNome : '',
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função construtora da Classe clVideo</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnInit: function() {
			var holder =  $('.video-box');
			var json = $(holder).parent().find('textarea');
			
			if( holder.length > 0 ){
				try{
					var prmDados = eval('[' + json.val() + ']');						
					
					var titulo = prmDados[0].titulo;
					var tipoVideo = prmDados[0].tipo_video;
					var descricao = prmDados[0].descricao;
					var embed = prmDados[0].video;
					
					//Carregando
					lfgportal.app.clMaster.fnCarregar(true,holder,'Carregando...');						
					
					if( titulo != '' || tipoVideo != '' || descricao != '' || embed != '' )
						scope.clVideo.fnInitPlayer(holder, titulo, tipoVideo, descricao, embed, 258, 175, null);
					else
						holder.html('<p>Ocorreu um erro, tente novamente.</p>');
				}catch(err){
					scope.clMaster.fnAlerta('block', holder, 'Ocorreu um erro interno, tente novamente!');
				}
				json.remove();
			}
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que inicializa o Player de Vídeo</SUMARY>
		<param name="prmHolder">DIV</param>
		<param name="prmTit">Título do Vídeo</param>
		<param name="prmTipoVideo">youtube/sambatech</param>
		<param name="prmDesc">Descrição do Vídeo</param>
		<param name="prmEmbed">URL para youtube e ID para Sambatech</param>
		<param name="prmLarg">Largura do Vídeo</param>
		<param name="prmAlt">Altura do Vídeo</param>
		<param name="prmCallBack">Function CallBack, atribuir null caso não tenha</param>
		----------------------------------------------------------------------------------------------------*/
		fnInitPlayer: function(prmHolder, prmTit, prmTipoVideo, prmDesc, prmEmbed, prmLarg, prmAlt, prmCallBack){
			var holder =  $(prmHolder);
			var lar = prmLarg;
			var alt = prmAlt;			
			
			var content = '<h3 class="box-header"><span class="icon video-icon"></span>' + prmTit + '</h3>'
						+ '<div class="box-content">'									
						+ '<div class="video-preview" id="player-' + prmTipoVideo + '">'
						+ '</div>'									
						+ '<p>' + prmDesc + '</p>'
						+ '</div>';
			holder.html(content);
			scope.clVideo.videoNome = prmDesc;
			
			switch(prmTipoVideo){
				case 'youtube' :
					var div = $('#player-' + prmTipoVideo );
					var player = '<iframe id="player" type="text/html" width="' + lar + '" height="' + alt + '" src="' + prmEmbed + '" frameborder="0"></iframe>';
					div.html(player);
					_gaq.push(['_trackEvent', 'home', 'video', lfgportal.app.clVideo.videoNome, 'youtube']);
					break;
				case 'sambatech' :
					var filePath = 'http://player.sambatech.com.br/current/samba-player.js?playerWidth=' + lar + '&playerHeight=' + alt + '&ph=' + scope.clVideo.idCanal + '&m=' + prmEmbed + '&cb=playerFn';
					var div = document.getElementById('player-' + prmTipoVideo );
					var sambaPlayerScript = document.createElement('script');
					sambaPlayerScript.type = 'text/javascript';
					sambaPlayerScript.src = filePath;					
					div.innerHTML = '';
					div.appendChild(sambaPlayerScript);
					break;
			};
			//Remover Carregando
			lfgportal.app.clMaster.fnCarregar(false,holder,false);
			
			if( prmCallBack != null)
				prmCallBack();
		}		
	};

	$(window).load(function(){
		scope.clVideo.fnInit();
	});
})(lfgportal.app, jQuery);

/*----------------------------------------------------------------------------------------------------
<SUMARY>Função de CallBack para GoogleAnalytics</SUMARY>
<param name="id"></param>
<param name="name"></param>
<param name="params"></param>
----------------------------------------------------------------------------------------------------*/
function playerFn(id, name, params){
	_gaq.push(['_trackEvent', 'home', 'video', lfgportal.app.clVideo.videoNome, name]);
}