/*==================================================================
CLASSE clAprovometro{}
==================================================================*/
var lfgportal = lfgportal || {};
lfgportal.app = lfgportal.app || {};

;(function(scope, $) {
	scope.clAprovometro = {
		obj : {},
		chart : {},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função construtora da Classe clAprovometro</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		fnInit: function() {
			var holder =  $('#aprovacoes-carousel');
			var json = $('.aprovacoes-box').find('textarea');
			
			if( holder.length > 0 ){
				try{
					var prmDados = eval('[' + json.val().replace(/\r\n/g, "") + ']');						
					
					if( prmDados.length > 0){
						$.each(prmDados,function(i,v){
							/*Cache do Objeto*/
							scope.clAprovometro.obj[i] = prmDados[i];
							$('.carousel-inner', holder).append('<li class="item' + (i==0 ? ' active' : '') + '"><div id="item-grafico' + (i+1) + '" class="item-grafico"></div></li>');
							//Carregando
							lfgportal.app.clMaster.fnCarregar(true,$('.carousel-inner', holder).find('li:last'),'Carregando...');
				
							/*NAVEGACAO*/					
							if( i == 0 )
								$('.ctm-nav', holder).append('<li class="nav-prev"><a href="#aprovacoes-carousel" data-slide="prev"><span class="left-nav-icon icon"></span></a></li>');
								
							$('.ctm-nav', holder).append('<li class="nav-index' + (i==0 ? ' active' : '') + '"><a href="#aprovacoes-carousel" data-slide-index="' + i + '">' + (i+1) + '</a></li>');
							
							if( (i+1) == prmDados.length ){
								$('.ctm-nav', holder).append('<li class="nav-next"><a href="#aprovacoes-carousel" data-slide="next"><span class="right-nav-icon icon"></span></a></li>');
								/*Inicia Carousel*/
								scope.clAprovometro.fnInitCarousel(i);
							}
								
							/*Construtora do HighChart*/
							if( i == 0 )
								scope.clAprovometro.fnInitHighChart( $('#item-grafico' + (i+1)), i, prmDados[i] );
						});
					}else
						scope.clMaster.fnAlerta('info', holder, 'Não há nenhum registro!');
				}catch(err){
					if( json.val().replace(/\r\n/g, "") == '' )
						scope.clMaster.fnAlerta('info', holder, 'Não há nenhum registro!');
					else
						scope.clMaster.fnAlerta('block', holder, 'Ocorreu um erro interno, tente novamente!');															
				}
				json.remove();
			}
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que filtra o Ano do Aprovometro</SUMARY>
		<param name="prmAno"></param>
		----------------------------------------------------------------------------------------------------*/
		fnDropAno: function(prmAno){			
			var val = prmAno,
				prmObj= $('.aprova-graph');
			
			$.each(scope.clAprovometro.obj.series, function(i,v){
				if( v.name == val )
					scope.clAprovometro.obj.series[i].color = '#31abda'
				else
					scope.clAprovometro.obj.series[i].color = '#6c6c6c'
			});
			scope.clAprovometro.fnInitHighChart($('#aprovacoes-interna'),1,scope.clAprovometro.obj);
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que inicializa o Carousel</SUMARY>
		<param name="prmI">Número de Itens, index do JSON</param>
		----------------------------------------------------------------------------------------------------*/
		fnInitCarousel: function(prmI){			
			var holder =  $('#aprovacoes-carousel');
			holder.carousel({
				interval: 10000,
				pause : 'hover'
			});
			ctm_nav = $('.ctm-nav', holder);

			/* applies click listener on nav links */
			$('.nav-index a', ctm_nav).die('click').live('click', function(e) {
				var idx = parseInt( $(this).attr('data-slide-index') );
				holder.carousel(idx);
				
				e.preventDefault();
			});

			/* event fired when slide animations is over */
			holder.die('slid').live('slid', function(evt) {
				var idx = $('.item.active:first', holder).index();

				$('li.active', ctm_nav).removeClass('active');
				$('li a[data-slide-index="' + idx + '"]', ctm_nav).parent().addClass('active');
								
				setTimeout(function(){
					scope.clAprovometro.fnInitHighChart( $('#item-grafico' + (idx+1)), idx, scope.clAprovometro.obj[idx] );
				},500);
			});
			
			/*Oculta Navegação caso tenha apenas 1 item*/
			if( prmI == 0 ) ctm_nav.hide();
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Função que inicializa os HIGHCHART</SUMARY>
		<param name="prmObj">Objeto que renderizará o Gráfico</param>
		<param name="prmI">index da array que receberá o new highchart()</param>
		<param name="prmDados">Objeto com os dados</param>
		----------------------------------------------------------------------------------------------------*/
		fnInitHighChart: function(prmObj,prmI,prmDados){
			/*Remove Carregando*/
			$('.carregando-ajax', $(prmObj).parent()).remove();
			
			/*Destrutora*/
			if( typeof(scope.clAprovometro.chart[prmI]) == 'object' ) {
				scope.clAprovometro.chart[prmI].destroy();
			}			
			switch(prmDados.tipo){
				case 'barra' :
					$(prmObj[0]).parents('.default-box:first').find('.back-bt').remove();								
					scope.clAprovometro.chart[prmI] = new Highcharts.Chart({
						chart: {
							renderTo: prmObj[0],
							type: 'bar'
						},
						title: {
							text: prmDados.titulo
						},
						subtitle: {
							text: prmDados.subtitulo
						},
						xAxis: {
							categories: prmDados.categorias,
							labels : {
								style: {
									fontSize : "14px"
								}
							},
							title: {
								text: null
							}
						},
						yAxis: {
							gridLineColor : "#fff",
							min: 0,
							endOnTick : false,
							maxPadding : 0.2,
							showFirstLabel: false,
							showLastLabel: false,
							labels: {
								enabled: false
							},
							title: {
								text: null
							}
						},
						tooltip: {
							formatter: function() {
								var point = this.point,
									s = this.x +':<b>'+ this.y +'</b> ' + prmDados.seriesLabel;
								if ( typeof(point.drilldown) != 'undefined' )
									s += typeof(point.drilldown.categories) == 'undefined' ? '' : '<br />' + prmDados.seriesLabelClick;
								else
									s += '<br /	>Voltar';

								return s;
							}
						},
						plotOptions: {
							bar: {
								dataLabels: {
									enabled: true,
									style: {
										fontWeight: 'bold'
									},
									formatter: function() {
										return this.y +' ' + prmDados.seriesLabel;
									},
									align : "left"
								},
								point: {
									events: {
										click: function() {
											var drilldown = this.drilldown;											
											if ( typeof(drilldown) != 'undefined' && typeof(drilldown.categories) != 'undefined'){ //Detalha
												var ano = $('select[name="aprovacoes-ano"]').find('option:selected').val();												
												$(prmObj[0]).parents('.default-box:first').append('<a class="default-button btn back-bt" name="filter-buscar" href="javascript:lfgportal.app.clAprovometro.fnDropAno(' + ano +')"><span class="icon-arrow-left icon-white"></span> voltar</a>')
												scope.clAprovometro.fnDrillDropHighChar(prmI, drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
											}else if ( typeof(drilldown) == 'undefined' ) // restore																								
												scope.clAprovometro.fnDrillDropHighChar(prmI, scope.clAprovometro.obj.seriesName, scope.clAprovometro.obj.categorias, scope.clAprovometro.obj.series);
										}
									}
								},
								borderWidth: 0,
								shadow : false
							}
						},
						legend: {
							enabled : false
						},
						credits: {
							enabled: false
						},
						series: [{
							name: prmDados.seriesName,
							data: prmDados.series,
							color: 'white'
						}]
					});
					break;
				case 'coluna' :
					var total = 0;
					$.each(prmDados.series, function(i,v){
						total += v.y; 
					});
					scope.clAprovometro.chart[prmI] = new Highcharts.Chart({
						chart: {
							renderTo: prmObj[0],
							type: 'column',
							marginTop : 40,
							marginRight : 10,
							marginBottom : 45,
							marginLeft : 10
						},
						credits : { enabled : false },
						title: {
							text: prmDados.titulo,
							y : 12,
							align : 'center',
							style : {
								width: '190px',
								color : '#6c6c6c',
								fontSize : '14px'
							}
						},
						subtitle: {
							text: prmDados.subtitulo,
							y : 140,
							floating : true,
							style : {
								width: '190px',
								color : '#32acdd',
								fontSize : '11px'
							}
						},
						xAxis: {
							min: 0,
							categories: prmDados.categorias,
							lineColor : '#f46e25',
							gridLineColor : '#fff',
							labels: {
								style: {
									fontSize : '9px',
									lineHeight : '10px'
								}
							}
						},		
						yAxis: {
							min: null,
							max : null,
							lineColor: '#f46e25',
							gridLineColor : '#fff',
							endOnTick : false,
							maxPadding : 0.2,
							lineWidth: 1,
							title: {
								enabled : false,
								text: ''
							},
							labels: {
								enabled : false
							}
						},
						legend: { 
							enabled : false
						},
						tooltip: {
							formatter: function() {
								return this.x + ': <br />'+ this.y + ' - ' + lfgportal.app.clMaster.fnArredondarNum( (this.y / total)*100 ,2) + '%';
							},
							style: {
								fontSize : '10px',
								lineHeight : '11px'
							}
						},
						plotOptions: {
							column: {
								borderWidth: 0,
								shadow : false
							}
						},
						series: [{
							 data: prmDados.series
						}]
					});					
					break;
				case 'pizza' :
					scope.clAprovometro.chart[prmI] = new Highcharts.Chart({
						chart: {				
							renderTo: prmObj[0],
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							marginTop : 40,
							marginRight : 130,
							marginBottom : 30,
							marginLeft : 10
						},
						credits : { enabled : false },
						title: {
							text: prmDados.titulo,
							y : 12,
							floating : true,
							style : {
								width: '190px',
								color : '#6c6c6c',
								fontSize : '14px'
							}
						},
						subtitle: {
							text: prmDados.subtitulo,
							y : 140,
							floating : true,
							style : {
								width: '190px',
								color : '#32acdd',
								fontSize : '11px',
								lineHeight : '12px'
							}
						},
						tooltip: {
							formatter: function() {
								return this.key +': <br />'+ this.y + ' - ' + lfgportal.app.clMaster.fnArredondarNum(this.percentage,2) + '%';
							},
							style : {
								fontSize : '10px',
								lineHeight : '11px'
							}
						},
						legend: {
							layout: 'vertical',
							backgroundColor: '#FFFFFF',
							floating: true,
							align: 'right',
							verticalAlign: 'top',
							x: 0,
							y: 30,
							labelFormatter: function() {
								return this.name;
							},
							itemStyle: {
								fontSize : '9px',
								lineHeight : '10px'
							}
						},
						plotOptions: {
							pie: {
								size: '100%',
								allowPointSelect: true,
								dataLabels: {
									enabled: false
								},
								showInLegend: true,
								borderWidth: 0,
								shadow : false
							}
						},
						series: [{
							type: 'pie',
							name: prmDados.seriesNome,
							data: prmDados.series
						}]
					});
					break
				case  'linha' :
					scope.clAprovometro.chart[prmI] = new Highcharts.Chart({
						chart: {				
							renderTo: prmObj[0],
							type: 'line',
							marginTop : 40,
							marginRight : 10,
							marginBottom : 30,
							marginLeft : 10
						},
						credits : { enabled : false },
						title: {
							text: prmDados.titulo,
							style : {
								width: '100%',
								color : '#6c6c6c',
								fontSize : '14px'
							}
						},
						subtitle: {
							text: prmDados.subtitulo,
							x : 0,
							y : 50,
							floating : true,
							style : {
								width: '100%',
								color : '#32acdd',
								fontSize : '11px',
								lineHeight : '12px'
							}
						},
						xAxis: {
							categories: prmDados.categorias,
							labels: {
								style: {
									fontSize : '9px',
									lineHeight : '10px'
								}
							}
						},
						yAxis: {
							title: {
								enabled : false,
								text: ''
							},
							labels: {
								enabled : false
							},
							endOnTick : false,
							maxPadding : 0.2
						},
						tooltip: {
							formatter: function() {
								return this.series.name +': <br />'+ this.x + ' - ' + lfgportal.app.clMaster.fnArredondarNum(this.percentage,2) + '%';
							},
							style : {
								fontSize : '10px',
								lineHeight : '11px'
							}
						},
						legend: { 
							enabled : false
						},
						plotOptions: {
							line: {
								color: '#31abda'
							}
						},
						series: [{
							name: prmDados.seriesNome,
							data: prmDados.series
						}]
					});
					break;
				case 'indicador' :
					$('.indicador-wrap', prmObj[0]).html('')
					$.each(prmDados.series,function(i,v){
						scope.clAprovometro.clIndicador.fnInit( prmObj[0],{titulo: prmDados.titulo , series : v });
					})					
					break;
			}
		},
		fnDrillDropHighChar: function(prmI, name, categories, data, color) {
			scope.clAprovometro.chart[prmI].xAxis[0].setCategories(categories, false);
			scope.clAprovometro.chart[prmI].series[0].remove(false);
			scope.clAprovometro.chart[prmI].addSeries({
				name: name,
				data: data,
				color: color || 'white'
			}, false);
			scope.clAprovometro.chart[prmI].redraw();
		},
		/*----------------------------------------------------------------------------------------------------
		<SUMARY>Classe que constrói o gráfico do tipo clIndicador{}</SUMARY>
		----------------------------------------------------------------------------------------------------*/
		clIndicador : {
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função construtora da Classe clIndicador</SUMARY>
			<param name="prmDiv">DIV ITEM</param>
			<param name="param">Objeto com os parâmetros</param>
			----------------------------------------------------------------------------------------------------*/
			fnInit : function(prmDiv, param) {
				var prmDiv = $(prmDiv),
					series = param.series,
					titulo = param.titulo,				
					total = series.y,
					prmCssClass = $('.indicador-val:last', prmDiv).length <= 0 ? 0 : $('.indicador-val:last', prmDiv).index() + 1,
					eNumero = lfgportal.app.clMaster.fnIsNumeric( series.y.replace('.','') );
				
				if( $('.indicador-tit', prmDiv).length <= 0 )
					prmDiv.html('<div class="indicador-tit">' + titulo + '</div><div class="indicador-wrap"><div class="indicador-val indicador' + prmCssClass + '" ' + ( eNumero == false ? ' style="margin-top:-500px"' : '' ) + '><span></span>' + series.name + '</div></div>');
				else
					$('.indicador-wrap', prmDiv).append('<div class="indicador-val indicador' + prmCssClass + '" ' + ( eNumero == false ? ' style="margin-top:-500px"' : '' ) + '><span></span>' + series.name + '</div>');
							
				if( eNumero )				
					scope.clAprovometro.clIndicador.fnContadorDec(prmDiv, prmCssClass, series.y, 0);
				else
					scope.clAprovometro.clIndicador.fnEfeitoTexto(prmDiv, prmCssClass);
			},
			fnEfeitoTexto: function(prmDiv, prmCssClass){
				$('.indicador' + prmCssClass, prmDiv).animate({ marginTop : '0px' });
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função incremental do clIndicador</SUMARY>
			<param name="prmDiv">DIV ITEM</param>
			<param name="prmCssClass">Classe que indica qual item a ser altedo</param>
			<param name="prmValor">Valor do clIndicador</param>
			<param name="prmCont">Contador incremental</param>
			----------------------------------------------------------------------------------------------------*/
			fnContadorDec : function(prmDiv, prmCssClass, prmValor, prmCont){
				var i = 0;
				var t = setInterval(function(){
					if( i <= 999 ){
						$('.indicador' + prmCssClass, prmDiv).find('span').html( 
							prmCont + '.' + 
							(i.toString().length == 1 ? '00' + i : 
								( i.toString().length == 2 ) ? 
									'0' + i : 
									i) 
							);
						i=i+100;
					}else{
						scope.clAprovometro.clIndicador.fnContadorInt(prmDiv, prmCssClass, prmValor, (prmCont+50) );
						clearInterval(t);
					}
				},1);
			},
			/*----------------------------------------------------------------------------------------------------
			<SUMARY>Função incremental do clIndicador</SUMARY>
			<param name="prmDiv">DIV ITEM</param>
			<param name="prmCssClass">Classe que indica qual item a ser altedo</param>
			<param name="prmValor">Valor do clIndicador</param>
			<param name="prmCont">Contador incremental</param>
			----------------------------------------------------------------------------------------------------*/
			fnContadorInt : function(prmDiv, prmCssClass, prmValor, prmCont){
				var total = parseInt(prmValor);
				if( prmCont <= total ){
					$('.indicador' + prmCssClass, prmDiv).find('span').html( prmCont + '.000');
					scope.clAprovometro.clIndicador.fnContadorDec(prmDiv, prmCssClass, prmValor, prmCont);
				}else
					$('.indicador' + prmCssClass, prmDiv).find('span').html(prmValor);						
			}
		}
	};

	$(function() {
		scope.clAprovometro.fnInit();
	});

})(lfgportal.app, jQuery);