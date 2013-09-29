<?php

/**
 * @package: DHCUtil
 * @created: 27/11/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 *
 * Rotinas diversas
 */

class DHCUtil {


        /**
         * Construtor
         *
         */
        private function __construct() {

                /** Definindo Varáveis globais **/
                global $system;

                $system->log->debug->debug("DHCUtil: nova instância");

        }

        /**
         * Validar e-mail
         *
         * @param string $email
         * @return boolean
         */
        public static function validarEMail($email) {
                $validator = new Zend_Validate_EmailAddress();
                return $validator->isValid($email);
        }

        /**
         * Retorna o conteudo de um arquivo
         *
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

                        } catch (Exception $e) {
                                DHCErro::halt('Código do Erro: "getConteudoArquivo"');
                        }
                }else{
                        return null;
                }
        }


        /**
         * Implementação de Anti injeção de SQL
         *
         * @param string $string
         * @return string
         */
        public static function antiInjection($string) {

                /** remove palavras que contenham sintaxe sql **/
                $string = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i","",$string);

                /** limpa espaços vazio **/
                //$string = trim($string);

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
         *
         * @param int $mes
         * @return string
         */
        public static function mesPorExtenso($mes) {
                $mes    = (int) $mes;
                switch (fmod($mes,12)) {
                        case 1:
                                return('Janeiro');
                        case 2:
                                return('Fevereiro');
                        case 3:
                                return('Março');
                        case 4:
                                return('Abril');
                        case 5:
                                return('Maio');
                        case 6:
                                return('Junho');
                        case 7:
                                return('Julho');
                        case 8:
                                return('Agosto');
                        case 9:
                                return('Setembro');
                        case 10:
                                return('Outubro');
                        case 11:
                                return('Novembro');
                        default:
                                return('Dezembro');
                }
        }
        
        /**
         * Descobrir o mime type de um arquivo
         *
         * @param string $arquivo
         * @return string
         */
        public static function getMimeType($arquivo) {
        	return(MIME_Type::autoDetect($arquivo));
        }
        
        /**
         * Descompactar um arquivo, retornando o conteúdo descompactado
         *
         * @param string $arquivo
         * @return string $arquivo_descomprimido
         */
        public static function descompacta ($arquivo) {
        	
        	/** Verifica se o arquivo existe e pode ser lido **/
        	if ((!file_exists($arquivo)) || (!is_readable($arquivo))) return false;
        	
        	/** Verifica o mime type do arquivo **/
        	switch (DHCUtil::getMimeType($arquivo)) {
        		case 'application/x-bzip2':
        			try {
        				$bz = bzopen($arquivo, "r");
						while (!feof($bz)) {
	      					$arquivo_descomprimido .= bzread($bz, 4096);
						}
						bzclose($bz);
						return ($arquivo_descomprimido);
        			} catch (Exception $e) {
        				DHCErro::halt('Erro ao tentar descompactar o arquivo: '.$arquivo. ' Trace: '.$e->getTraceAsString());
        			}
        	}
        	
        }
        
        /**
         * Checar se um IP é válido
         *
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
	 * Retornar o Html de um SwatObject
	 *
	 * @param SwatObject $swatObject
	 * @return string
	 */
	public static function getHtmlFromSwat(SwatObject $swatObject) {
		ob_start();
		$swatObject->display();
		return ob_get_clean();
	}
	
	/**
	 * Retornar um número em formato de moeda (BR)
	 *
	 * @param number
	 * @return string
	 */
	function to_money($n) {
		$temp = str_replace(",",".",$n);
		return('R$ '.number_format($temp, 2, ',', '.'));
	}

	/**
	 * Retornar um número formatado
	 *
	 * @param number
	 * @return string
	 */
	function to_number($n) {
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
	function getFirstDayOfMonth($data) {
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
	function getLastDayOfMonth($data) {
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
	function formatCGC($cgc) {
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
	function retiraEspacos($string) {
		
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
	function qtdStr($chr,$qtd) {
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
	function lpad( $string, $length, $pad = ' ' ) { 
		return str_pad( $string, $length, $pad, STR_PAD_LEFT );
	}
	
	/**
	 * Adicionar caracteres a direita de uma string
	 *
	 * @param string
	 * @return string 
	 */
	function rpad( $string, $length, $pad = ' ' ) { 
		return str_pad( $string, $length, $pad, STR_PAD_RIGHT );
	}
	
	/**
	 * Formatar um CEP
	 *
	 * @param number 
	 * @return string 
	 */
	function formatCEP($cep) {
		if ((strlen($cep)) !== 8)  {
			return $cep;
		}else{
			return (substr($cep,0,5) . '-'.substr($cep,5,3));
		}
	}

	/**
	 * Validar uma Data
	 *
	 * @param date (dd/mm/yyyy)
	 * @return boolean 
	 */
	function validaData($data) {
		$dia	= substr($data,0,2);
		$barra1	= substr($data,2,1);
		$mes	= substr($data,3,2);
		$barra2	= substr($data,5,1);
		$ano	= substr($data,6,4);
		
		/** Verifica se o dia / mes / ano são numéricos **/
		if (!is_numeric($dia) || !is_numeric($mes) || !is_numeric($ano)) {
			return false;
		}
		
		/** Verifica se o ano é maior que 1800 **/
		if ($ano < 1800) {
			return false;
		}
		
		/** Verifica se a data é válida **/
		if (($barra1 !== '/') || ($barra2 !== '/') || (checkdate($mes,$dia,$ano) == false)) {
			return false;
		}

		return true;
		
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
	 * Resgatar o caminho completo do arquivo por extensão
	 * @param string $arquivo
	 * @param string $extensao
	 * @param string $tipo
	 * @param string $default
	 */
	public static function getCaminhoCorrespondente($arquivo,$extensao,$tipo = ZG_PATH) {
	
		/** Resgata o nome base do arquivo **/
		$base   = pathinfo($arquivo,PATHINFO_BASENAME);
	
		/** Resgata o nome do arquivo sem a extensão **/
		$base   = substr($base,0,strpos($base,'.'));
	
		/** define o tipo padrão **/
		if (!$tipo)     $tipo   = ZG_PATH;
	
		if (!$extensao) {
			return ($arquivo);
		}elseif (strtolower($extensao) == "html") {
			($tipo == ZG_PATH) ? $dir = HTML_PATH : $dir = HTML_URL;
			$ext    = ".html";
		}elseif (strtolower($extensao) == "dp") {
			($tipo == ZG_PATH) ? $dir = DP_PATH : $dir = DP_URL;
			$ext    = ".dp.php";
		}elseif (strtolower($extensao) == "xml") {
			($tipo == ZG_PATH) ? $dir = XML_PATH : $dir = XML_URL;
			$ext    = ".xml";
		}elseif (strtolower($extensao) == "bin") {
			($tipo == ZG_PATH) ? $dir = BIN_PATH : $dir = BIN_URL;
			$ext    = ".php";
		}else{
			return ($arquivo);
		}
	
		return ($dir . '/' .$base . $ext);
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
	
}
