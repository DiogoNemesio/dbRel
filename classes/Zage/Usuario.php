<?php

namespace Zage;

/**
 * Gerenciamento de usuários
 *
 * @package \Zage\Usuario
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 * @created 17/07/2013
 */
abstract class Usuario {

	/**
	 * código do usuário
	 */
	private $codUsuario;
	
	/**
	 * usuário
	 */
	private $usuario;
	
	/**
	 * Nome
	 */
	private $nome;
	
	/**
	 * Tipo
	 */
	private $tipo;
	
    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $system,$log;
		
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	/**
	 * Autenticar o usuário no banco
	 *
	 * @param string $usuario
	 * @param string $senha
	 * @return boolean
	 */
    public abstract function autenticar ($usuario,$senha);
    
    /**
     * Resgata as informações do usuário
     *
     * @param integer $usuario
     * @return array
     */
    public abstract function getInfo ($codUsuario);
    
    /**
     * Resgatar o código do usuário
     *
     * @return string
     */
    public function getCodUsuario () {
    	return $this->codUsuario;
    }
    
    /**
     * Definir o código do usuário
     *
     * @param string $codUsuario
     */
    public function setCodUsuario ($codUsuario) {
    	$this->codUsuario = $codUsuario;
    }
    
    /**
     * Resgatar a identificação do usuário
     *
     * @return string
     */
    public function getUsuario () {
    	return $this->usuario;
    }
    
    /**
     * Definir a identificação do usuário
     *
     * @param string $usuario
     */
    public function setUsuario ($usuario) {
    	$this->usuario = $usuario;
    }
    
    /**
     * Resgatar o nome do usuário
     *
     * @return string
     */
    public function getNome () {
    	return $this->nome;
    }
    
    /**
     * Definir o nome do usuário
     *
     * @param string $nome
     */
    public function setNome ($nome) {
    	$this->nome = $nome;
    }

    /**
     * Resgatar o tipo do usuário
     *
     * @return string
     */
    public function getTipo () {
    	return $this->tipo;
    }
    
    /**
     * Definir o tipo do usuário
     *
     * @param string $tipo
     */
    public function setTipo ($tipo) {
    	$this->tipo = $tipo;
    }
    
}
