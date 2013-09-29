<?php

/**
 * parametro
 * 
 * @package: parametro
 * @created: 03/04/2013
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class parametro {

	
	private $valor;
	private $descricao;
	
	
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $system;

		$system->log->debug->debug(__CLASS__.": nova Instância");
	}
	
	/**
	 * @return the $valor
	 */
	public function getValor() {
		return $this->valor;
	}

	/**
	 * @return the $descricao
	 */
	public function getDescricao() {
		return $this->descricao;
	}

	/**
	 * @param field_type $valor
	 */
	public function setValor($valor) {
		$this->valor = $valor;
	}

	/**
	 * @param field_type $descricao
	 */
	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}

	/**
	 * Salva um parametro
	 * @param unknown $codigo
	 * @param unknown $valor
	 * @return string|Ambigous <NULL, string>
	 */
    public static function salva ($codigo,$valor) {
		global $system;
		
		if (!$codigo) {
			DHCErro::halt(__CLASS__.' Falta de parâmetros !!!');
		}
		
		/** Checar se o parametro existe **/
		if (parametro::existe($codigo) == false) {
			DHCErro::halt(__CLASS__.' Parâmetro não existe !!!');
		}else{
			try {
				$info	= parametro::getInfo($codigo);
				 
				$system->geraEvento($system->getCodUsuario(),'U',parametro::getCodDicionario(),parametro::concatDadosEventos(1,$codigo),parametro::concatDadosEventos(0,$codigo,$info->DESCRICAO,$valor));

				$system->db->con->beginTransaction();
				$system->db->Executa("
				UPDATE 	PARAMETROS
				SET		VALOR			= ?
				WHERE	CODIGO			= ?",
						array($valor,$codigo)
				);
				$system->db->con->commit();
				return(null);
			} catch (Exception $e) {
				$system->db->con->rollback();
				return('Erro: '.$e->getMessage());
			}
				
		}
    }
	
    
	/**
	 * 
	 * Lista os parametros
	 */
    public static function lista ($nome = null) {
		global $system;
		
		$and	= null;
		
		if ($nome != null)	$and = "AND 	DESCRICAO 	LIKE '%".$nome."%'";    	
		
		return (
    		$system->db->extraiTodos("
				SELECT	*
				FROM	PARAMETROS P
				WHERE	1 = 1
    			$and
				ORDER	BY P.CODIGO
			")
   		);
    }

    
    /**
     * Verifica se o parametro existe
     *
     * @param integer $codigo
     * @return array
     */
    public static function existe ($codigo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	PARAMETROS P
				WHERE 	P.CODIGO	= '".$codigo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Resgata as informações do parametro
     *
     * @param integer $codigo
     * @return array
     */
    public static function getInfo ($codigo) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	P.*
				FROM	PARAMETROS P
				WHERE   P.CODIGO 	= '".$codigo."'
			")
   		);	
    }
    
    /**
     * Concatenar os dados para gerar log de eventos
     * @param unknown $busca
     * @param unknown $codigo
     * @param string $nome
     * @param string $email
     * @param string $codCidade
     * @param string $endereco
     * @param string $bairro
     * @return string
     */
    public static function concatDadosEventos ($busca,$codigo,$descricao = null,$valor = null) {
    	global $system;
    	$s		= $system->getCaracSepEvento();
    		
    	if ($busca == 1) {
    		$info	= parametro::getInfo($codigo);
    		return ($info->CODIGO.$s.$info->DESCRICAO.$s.$info->VALOR);
    	}else {
    		return ($codigo.$s.$descricao.$s.$valor);
    	}
    }
    
    /**
     * Resgatar o código do dicionário dessa tabela
     */
    public static function getCodDicionario() {
    	global $system;
    
    	$info = $system->db->extraiPrimeiro("
				SELECT	CODIGO
				FROM	DICIONARIO_DADOS DD
				WHERE 	DD.NOME		= 'PARAMETROS'
		");
    
    	if (isset($info->CODIGO)) {
    		return $info->CODIGO;
    	}else{
    		DHCErro::halt('Código do dicionário não encontrado !!!');
    	}
    }
    

}