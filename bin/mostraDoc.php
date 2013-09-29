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
if (isset($_GET['codPaciente']))		$codPaciente		= DHCUtil::antiInjection($_GET["codPaciente"]);
if (isset($_GET['codConsulta']))		$codConsulta		= DHCUtil::antiInjection($_GET["codConsulta"]);
if (isset($_GET['codTipoDoc']))			$codTipoDoc			= DHCUtil::antiInjection($_GET["codTipoDoc"]);

if (!isset($codTipoDoc) || !isset($codConsulta) || !isset($codPaciente)) exit;

#################################################################################
## Monta o html do DIV
#################################################################################
$divHtml	= '';

#################################################################################
## Verifica o tipo do Arquivo
#################################################################################
if ($codTipoDoc == 'E') { # Exame
	$divHtml	.= '<div class="fileupload fileupload-new" data-provides="fileupload">
		<span class="btn btn-file"><span class="fileupload-new">Escolher Arquivo do Exame</span><span class="fileupload-exists">Trocar Arquivo</span><input type="file" name="arquivo" id="arquivoID"></span>
		<span class="fileupload-preview"></span>
		<a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
		</div>
	';
}elseif ($codTipoDoc == 'F') { # Foto
	$divHtml	.= '<div class="fileupload fileupload-new" data-provides="fileupload">
		<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>
			<div>
				<span class="btn btn-file"><span class="fileupload-new">Escolher a Foto</span><span class="fileupload-exists">Alterar Foto</span><input type="file" name="arquivo" id="arquivoID"></span>
				<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remover</a>
			</div>
		</div>
	';
}

#################################################################################
## Carregando o template html
#################################################################################
$template       = new DHCHtmlTemplate();
$template->loadTemplate(DHCUtil::getCaminhoCorrespondente(__FILE__, 'html'));

#################################################################################
## Define os valores das variáveis
#################################################################################
$template->assign('COD_PACIENTE'			,$codPaciente);
$template->assign('COD_CONSULTA'			,$codConsulta);
$template->assign('COD_TIPO_DOC'			,$codTipoDoc);
$template->assign('DIV_HTML'				,$divHtml);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
echo $template->getHtmlCode();
