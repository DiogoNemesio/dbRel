<?php

namespace Zage;

/**
 * Funções diversas
 *
 * @package \Zage\Util
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */

class Util {
	
	/**
	 * Construtor privado, a classe deve ser usada de forma statica
	 */
	private function __construct() {
		
	}
	
	/**
	 * Validação de e-mail
	 * @param unknown $email
	 * @return boolean
	 */
	public static function validarEMail($email) {
		$validator = new \Zend\Validator\EmailAddress();
		return $validator->isValid($email);
	}
	
	/**
	 * Resgatar o conteúdo de um arquivo
	 * @param string $arquivo
	 * @return string 
	 */
	public static function getConteudoArquivo ($arquivo) {
		/** Checar se o arquivo existe **/
		if (file_exists($arquivo)) {
			try {
				/** Abre o arquivo somente para leitura **/
				$handle         = fopen($arquivo, "r");
	
				/** Lê o conteudo do arquivo em uma variavel **/
				$conteudo       = fread ($handle, filesize ($arquivo));
	
				/** Fecha o arquivo **/
				fclose($handle);
	
				return($conteudo);
	
			} catch (\Exception $e) {
				\Zage\Erro::halt('Código do Erro: "getConteudoArquivo": '.$e->getMessage());
			}
		}else{
			return null;
		}
	}
	
	
	/**
	 * Implementação de Anti injeção de SQL
	 * @param string $string
	 * @return string
	 */
	public static function antiInjection($string) {
	
		/** remove palavras que contenham sintaxe sql **/
		$string = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i","",$string);
	
		/** limpa espaços vazio **/
		$string = trim($string);
	
		/** tira tags html e php **/
		$string = strip_tags($string);//
	
		/** Converte caracteres especiais para a realidade HTML **/
		$string = htmlspecialchars($string);
	
		if (!get_magic_quotes_gpc()) {
			$string = addslashes($string);
		}
	
		return ($string);
	}
	
	/**
	 * Retornar o mês por extenso
	 * @param integer $mes
	 * @return string
	 */
	public static function mesPorExtenso($mes) {
		$mes    = (int) $mes;
		switch (fmod($mes,12)) {
			case 1:
			case "01":
				return('Janeiro');
			case 2:
			case "02":
				return('Fevereiro');
			case 3:
			case "03":
				return('Março');
			case 4:
			case "04":
				return('Abril');
			case 5:
			case "05":
				return('Maio');
			case 6:
			case "06":
				return('Junho');
			case 7:
			case "07":
				return('Julho');
			case 8:
			case "08":
				return('Agosto');
			case 9:
			case "09":
				return('Setembro');
			case "10":
				return('Outubro');
			case "11":
				return('Novembro');
			case "0":
			case "12":
				return('Dezembro');
			default:
				return('??????');
		}
	}
	
	/**
	 * Descobrir o mime type de um arquivo
	 * @param unknown $arquivo
	 */
	public static function getMimeType($arquivo) {
		return(MIME_Type::autoDetect($arquivo));
	}
	
	/**
	 * Descompactar um arquivo, retornando o conteúdo descompactado
	 * @param unknown $arquivo
	 * @return boolean|string
	 */
	public static function descompacta ($arquivo) {
		 
		/** Verifica se o arquivo existe e pode ser lido **/
		if ((!file_exists($arquivo)) || (!is_readable($arquivo))) return false;
		 
		/** Verifica o mime type do arquivo **/
		switch (\Zage\Util::getMimeType($arquivo)) {
			case 'application/x-bzip2':
				try {
					$bz = bzopen($arquivo, "r");
					while (!feof($bz)) {
						$arquivo_descomprimido .= bzread($bz, 4096);
					}
					bzclose($bz);
					return ($arquivo_descomprimido);
				} catch (\Exception $e) {
					\Zage\Erro::halt('Erro ao tentar descompactar o arquivo: '.$arquivo. ' Trace: '.$e->getTraceAsString());
				}
		}
		 
	}
	
	/**
	 * Checar se um IP é válido
	 * @param string $ip
	 * @return boolean
	 */
	public static function validaIP ($ip) {
		/** Verificar se o IP está no format global do IPV4 **/
		if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip))  {
			/** Separar cada bloco em uma array **/
			$parts	= explode(".",$ip);
			 
			/** Checar se cada bloco está correto **/
			foreach($parts as $ip_parts) {
				if (intval($ip_parts)>255 || intval($ip_parts)<0) {
					return false;
				} else {
					return true;
				}
			}
		}else{
			return false;
		}
	}
	

	/**
	 * Retornar um número em formato de moeda (BR)
	 *
	 * @param number
	 * @return string
	 */
	public static function to_money($n) {
		if (!$n)	$n = 0;
		$temp = str_replace(",",".",$n);
		return('R$ '.number_format($temp, 2, ',', '.'));
	}

	/**
	 * Retornar um número formatado
	 *
	 * @param number
	 * @return string
	 */
	public static function to_number($n) {
		$temp = str_replace(".","",$n);
		$temp = str_replace(",",".",$temp);
		return(number_format($temp, 0, ',', '.'));
	}
	
	/**
	 * Retornar Primeiro dia do mês
	 *
	 * @param date (formato dd/mm/yyyy)
	 * @return date (formato dd/mm/yyyy)
	 */
	public static function getFirstDayOfMonth($data) {
		list($dia,$mes,$ano)	= split('/',$data);
		$timeStamp				= mktime(0,0,0,$mes,1,$ano); //Create time stamp of the first day
    	$firstDay				= date('d/m/Y',$timeStamp);  //get first day of the given month		
		return($firstDay);
	}

	/**
	 * Retornar último dia do mês
	 *
	 * @param date (formato dd/mm/yyyy)
	 * @return date (formato dd/mm/yyyy)
	 */
	public static function getLastDayOfMonth($data) {
		list($dia,$mes,$ano)	= split('/',$data);
		$timeStamp				= mktime(0,0,0,$mes,1,$ano);    		//Create time stamp of the first day
		list($t,$m,$a)			= split('/',date('t/m/Y',$timeStamp)); 	//Find the last date of the month and separating it
    	$lastDayTimeStamp		= mktime(0,0,0,$m,$t,$a);				//create time stamp of the last date of the give month
		$lastDay				= date('d/m/Y',$lastDayTimeStamp);
		return($lastDay);
	}
	

	/**
	 * Formatar um CGC
	 *
	 * @param number 
	 * @return string 
	 */
	public static function formatCGC($cgc) {
		if (((strlen($cgc)) < 13) || ((strlen($cgc)) > 14)) {
			return $cgc;
		}else{
			if ((strlen($cgc)) == 13) $cgc = "0".$cgc;
			return (substr($cgc,0,2).'.'.substr($cgc,2,3).'.'.substr($cgc,5,3).'/'.substr($cgc,8,4).'-'.substr($cgc,12,2)) ;
		}
	}

	/**
	 * Retirar todos os espaços em branco contínuos de uma string
	 *
	 * @param string
	 * @return string 
	 */
	public static function retiraEspacos($string) {
		
		$str 	= $string;
		
		while (strpos($str, '  ') !== false) {
			$str = str_replace('  ',' ',$str);
		}
		
		return (trim($str));
	}


	/**
	 * Retornar uma quantidade de caracter 
	 *
	 * @param string
	 * @return string 
	 */
	public static function qtdStr($chr,$qtd) {
		$string	= '';
		for ($i = 1; $i <= $qtd; $i++) $string .= $chr;
		return ($string);
	}

	/**
	 * Adicionar caracteres a esquerda de uma string
	 *
	 * @param string
	 * @return string 
	 */
	public static function lpad( $string, $length, $pad = ' ' ) { 
		return str_pad( $string, $length, $pad, STR_PAD_LEFT );
	}
	
	/**
	 * Adicionar caracteres a direita de uma string
	 *
	 * @param string
	 * @return string 
	 */
	public static function rpad( $string, $length, $pad = ' ' ) { 
		return str_pad( $string, $length, $pad, STR_PAD_RIGHT );
	}
	
	/**
	 * Formatar um CEP
	 *
	 * @param number 
	 * @return string 
	 */
	public static function formatCEP($cep) {
		if ((strlen($cep)) !== 8)  {
			return $cep;
		}else{
			return (substr($cep,0,5) . '-'.substr($cep,5,3));
		}
	}

	/**
	 * Formatar uma Data
	 *
	 * @param string
	 * @return date
	 */
	public static function toDate($date) {
		
		$ano		= substr($date,0,4);
		$mes		= substr($date,4,2);
		$dia		= substr($date,6,2);
		
		return ($dia.'/'.$mes.'/'.$ano);
	}
	
	/**
	 * Transformar um ano em 4 dígitos
	 *
	 * @param string
	 * @return number
	 */
	public static function toYear4($ano) {
		if ((strlen($ano)) !== 2)  return $ano;
		
		if ($ano > 60 ) {
			return ($ano + 1900);
		}else{
			return ($ano + 2000);
		}
	}
	
	/**
	 * Enviar os header para o browser fazer download do arquivo
	 *
	 * @param varchar Nome do Arquivo
	 * @param varchar Tipo do Arquivo

	 */
	public static function sendHeaderDownload($nomeArquivo,$tipo) {
		header("Pragma: public");
  		header("Expires: 0");
  		header("Pragma: no-cache");
  		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
  		header("Content-Type: application/octet-stream");
  		header("Content-Type: application/download");
		header('Content-disposition: attachment; filename='.$nomeArquivo);
  		header("Content-Type: application/".$tipo);
  		header("Content-Transfer-Encoding: binary");
	}

	/**
	 * Comparação entre 2 números float
	 * @param unknown $f1
	 * @param unknown $f2
	 * @param number $precision
	 * @return boolean
	 */
	public static function floatcmp($f1,$f2,$precision = 10) {
		$e = pow(10,$precision);
		return (intval($f1 * $e) == intval($f2 * $e));
	}
	
	
	/**
	 * Codifica uma string
	 * @param string $string
	 */
	public static function encodeUrl($string) {
		return(base64_encode($string));
	}
	
	/**
	 * Codifica uma string
	 * @param string $string
	 */
	public static function decodeUrl($string) {
		return(base64_decode($string));
	}
	
	/**
	 * Descompacta um id
	 * @param string $id
	 */
	public static function descompactaId($id) {
		if ($id != null) {
			$var    = base64_decode($id);
			$vars   = explode("&",$var);
			for ($i = 0; $i < sizeof($vars); $i++) {
				if ($vars[$i] != '') {
					list($variavel,$valor)  = explode('=',$vars[$i]);
					eval('global $'.$variavel.';');
					eval('$'.$variavel.' = "'.$valor.'";');
				}
			}
		}
	}
	
	/**
	 * Retornar os números de uma string
	 * @param string $str
	 * @return number
	 */
	public static function getNumbers($str) {
		preg_match_all('/\d+/', $str, $matches);
		return implode("", $matches[0]);
	}
	
	/**
	 *
	 * Resgatar o caminho completo do arquivo por extensão
	 * @param string $arquivo
	 * @param string $extensao
	 * @param string $tipo
	 */
	public static function getCaminhoCorrespondente($arquivo,$extensao,$tipo = \Zage\ZWS::CAMINHO_ABSOLUTO) {
	
		/** Resgata o nome base do arquivo **/
		$base   = pathinfo($arquivo,PATHINFO_BASENAME);
	
		/** Resgata o nome do arquivo sem a extensão **/
		$base   = substr($base,0,strpos($base,'.'));
	
		/** define o tipo padrão **/
		if (!$tipo)     $tipo   = \Zage\ZWS::CAMINHO_ABSOLUTO;
	
		
		switch (strtolower($extensao)) {
			case \Zage\ZWS::EXT_HTML:
				($tipo == \Zage\ZWS::CAMINHO_ABSOLUTO) ? $dir = HTML_PATH : $dir = HTML_URL;
				break;
			case \Zage\ZWS::EXT_DP:
				($tipo == \Zage\ZWS::CAMINHO_ABSOLUTO) ? $dir = DP_PATH : $dir = DP_URL;
				break;
			case \Zage\ZWS::EXT_XML:
				($tipo == \Zage\ZWS::CAMINHO_ABSOLUTO) ? $dir = XML_PATH : $dir = XML_URL;
				break;
			case \Zage\ZWS::EXT_PHP:
				($tipo == \Zage\ZWS::CAMINHO_ABSOLUTO) ? $dir = BIN_PATH : $dir = BIN_URL;
				break;
			default:
				return ($arquivo);
				break;
		}
	
		return ($dir . '/' .$base . "." . $extensao);
	}
	
	
}
