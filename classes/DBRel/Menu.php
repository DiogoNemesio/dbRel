<?php

namespace DBRel;

/**
 * Menu
 * 
 * @package: Menu
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Menu {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $system,$log,$db;

		$log->debug(__CLASS__.": nova Instância");
	}
	
    /**
     * Resgata os menus por tipo de usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function DBGetMenuItens($usuario) {
		global $system,$log,$db;
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M,
						MENU_TIPO_USUARIO MTU,
						USUARIOS U
				WHERE	M.CODIGO 				= MTU.COD_MENU
				AND		MTU.COD_TIPO_USUARIO 	= U.COD_TIPO
				AND		U.USUARIO				= '".$usuario."'
				ORDER	BY M.NIVEL,MTU.ORDEM
			")
   		);
    }
    
    /**
     * Resgata os menus por tipo de usuário
     *
     * @param integer $COD_TIPO_USUARIO
     * @param integer $menuPai
     * @return array
     */
	public static function DBGetMenuItensTipoUsuario($COD_TIPO_USUARIO,$menuPai = null) {
		global $system,$log,$db;
    	if ($menuPai != null) {
    		$where	= " AND	M.COD_MENU_PAI	= '".$menuPai."'";
    	}else{
    		$where	= " AND	M.NIVEL	= '0'";
    	}
    	
    	return (
    		$system->db->extraiTodos("
				SELECT	M.NOME menu,M.DESCRICAO descricao,M.CODIGO codMenu,M.*
				FROM	MENU M,
						MENU_TIPO_USUARIO MTU
				WHERE	M.CODIGO 				= MTU.COD_MENU
				AND		MTU.COD_TIPO_USUARIO 	= '".$COD_TIPO_USUARIO."'
				$where
				ORDER	BY NIVEL,ORDEM
			")
   		);
    }

    /**
     * Resgata os menus que o tipo de usuário não possue
     *
     * @param integer $COD_TIPO_USUARIO
     * @param integer $menuPai
     * @return array
     */
	public static function DBGetMenuIndispTipoUsuario($COD_TIPO_USUARIO,$menuPai = null) {
		global $system,$log,$db;
    	if ($menuPai != null) {
    		$where	= "
    			AND		(M.CODIGO			= '".$menuPai."'
						OR
						 M.COD_MENU_PAI		= '".$menuPai."'
						) ";
    	}else{
    		$where	= "AND	M.NIVEL	= '0'";
    	}
    	
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*,M.CODIGO codMenu,M.NOME menu
				FROM	MENU M
				WHERE	M.CODIGO NOT IN (
					SELECT	COD_MENU
					FROM	MENU_TIPO_USUARIO MTU
					WHERE	MTU.COD_TIPO_USUARIO 	= '".$COD_TIPO_USUARIO."'
				)
				$where
				ORDER	BY NIVEL
			")
   		);
    }

    /**
     * Resgata a lista de menus
     *
     * @param integer $COD_TIPO_USUARIO
     * @param integer $menuPai
     * @return array
     */
    public static function DBGetListMenus($nivel = null) {
		global $system,$log,$db;
    	if ($nivel !== null) {
    		$where	= "	WHERE		M.NIVEL = '".$nivel."'";
    	}else{
    		$where	= " ";
    	}
    	
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M
				$where
				ORDER	BY NOME
			")
   		);
    }

    
    /**
	 * Resgatar a lista de Tipos de Usuários
     */
    public static function DBGetListTipoMenu() {
		global $system;
    	return (
    		$system->db->extraiTodos("
	    		SELECT	*
	    		FROM	TIPO_MENU
	    		ORDER BY NOME
    		")
    	);
    }

    /**
     * Resgata os dados de um Menu
     *
     * @param integer $codMenu
     * @return array
     */
    public static function DBGetInfoMenu($codMenu) {
		global $system;
    	$return	= $system->db->extraiPrimeiro("
			SELECT	M.*
			FROM	MENU M
			WHERE	M.CODIGO		= '".$codMenu."'
		");
   		if (isset($return->CODIGO)) {
   			return ($return);
   		}else{
   			return(null);
   		}
    }

    /**
     * Verifica se já existe o menu
     *
     * @param integer $Menu
     * @param integer $codMenuPai
     * @return boolean
     */
    public static function existeMenu($menu,$codMenuPai) {
    	if (!$codMenuPai) $codMenuPai = 0;
		global $system,$log,$db;
    	$return	= $system->db->extraiPrimeiro("
				SELECT	COUNT(*) num
				FROM	MENU M
				WHERE	M.NOME 						= '".$menu."'
				AND		IFNULL(M.COD_MENU_PAI,0)	= '".$codMenuPai."'
			");
   		if ((isset($return->num)) && ($return->num > 0)) {
   			return (true);
   		}else{
   			return(false);
   		}
    }

    /**
     * Resgata a ordem de um Menu
     *
     * @param integer $codMenu
     * @return array
     */
    public static function DBGetOrdemMenu($COD_TIPO_USUARIO,$codMenu) {
		global $system,$log,$db;
    	$return	= $system->db->extraiPrimeiro("
				SELECT	MTU.ORDEM
				FROM	MENU_TIPO_USUARIO MTU
				WHERE	MTU.COD_MENU					= '".$codMenu."'
				AND		MTU.COD_TIPO_USUARIO			= '".$COD_TIPO_USUARIO."'
			");
   		if (isset($return->ORDEM)) {
   			return ($return->ORDEM);
   		}else{
   			return(null);
   		}
    }

    /**
     * Verifica se o menu está disponível para um tipo de Usuário
     *
     * @param integer $codMenu
     * @param integer $COD_TIPO_USUARIO
     * @return boolean
     */
    public static function DBMenuEstaDisponivelTipoUsuario($codMenu,$COD_TIPO_USUARIO) {
		global $system,$log,$db;
    	$return	= $system->db->extraiPrimeiro("
				SELECT	COUNT(*) num
				FROM	MENU_TIPO_USUARIO MTU
				WHERE	MTU.COD_MENU			= '".$codMenu."'
				AND		MTU.COD_TIPO_USUARIO	= '".$COD_TIPO_USUARIO."'
			");
   		if ((isset($return->num)) && ($return->num > 0)) {
   			return (true);
   		}else{
   			return(false);
   		}
    }


    /**
	 * Resgatar um array com a árvore completa de um menu
     */
    public static function getArrayArvoreMenu($codMenu) {
    	$array		= array();
    	$info 		= \DBRel\Menu::DBGetInfoMenu($codMenu);
    	
    	if (!$info) return ($array);
    	$codMenuPai	= $info->COD_MENU_PAI;
    	$array[]	= $info->CODIGO;
    	
    	while ($codMenuPai != '') {
    		$info		= \DBRel\Menu::DBGetInfoMenu($codMenuPai);
    		$codMenuPai	= $info->COD_MENU_PAI;
    		$array[]	= $info->CODIGO;
	    	if (!$info) return (array_reverse($array));
    	}
    	
    	return (array_reverse($array));
    }
    
    /**
	 * Resgatar um array com a árvore completa de um menu com a Url
     */
    public static function getArrayArvoreMenuUrl($codMenu) {
		global $system,$log,$db;
    	
    	$array		= array();
    	$info 		= \DBRel\Menu::DBGetInfoMenu($codMenu);
    	
    	if (!$info) return ($array);
    	$codMenuPai				= $info->COD_MENU_PAI;
    	$array[$info->CODIGO]	= $info;
    	
    	while ($codMenuPai != '') {
    		$info		= \DBRel\Menu::DBGetInfoMenu($codMenuPai);
    		$codMenuPai	= $info->COD_MENU_PAI;
    		$array[$info->CODIGO]	= $info;
	    	if (!$info) return (array_reverse($array));
    	}
    	
    	return (array_reverse($array));
    }
    
    /**
	 * Resgatar um array com os dependentes de um menu
     */
    public static function getArrayDependentesMenu($codMenu,&$array) {
		global $system,$log,$db;
    	$dependentes	= \DBRel\Menu::DBGetDependentesMenu($codMenu);
    	for ($i = 0; $i < sizeof($dependentes); $i++) {
    		$array[]	= $dependentes[$i]->CODIGO;
    		\DBRel\Menu::getArrayDependentesMenu($dependentes[$i]->CODIGO,$array);
    	}
    }

    /**
     * Resgata os dependentes direto de um menu
     *
     * @param integer $codMenu
     * @return array
     */
    public static function DBGetDependentesMenu($codMenu) {
		global $system,$log,$db;
    	return ($system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M
				WHERE	M.COD_MENU_PAI				= '".$codMenu."'
			")
    	);
    }

    /**
	 * Associa menu a um tipo de Usuário
     */
    public static function addMenuTipoUsuario($codMenuDe,$codMenuPara,$COD_TIPO_USUARIO,$codMenuPai) {
		global $system,$log,$db;
    	
    	/** Resgata as informações dos menus **/
    	$infoDe			= \DBRel\Menu::DBGetInfoMenu($codMenuDe);
    	$infoPara		= \DBRel\Menu::DBGetInfoMenu($codMenuPara);

    	if (!$infoDe) 	return false;
    	
    	if (!$infoPara) {
    		$dispPara	= false;
    	}else{
    		$dispPara	= true;
    	}
    	
    	/** Verifica se o menu de origem já está disponível para o usuário **/
    	$dispDe	= \DBRel\Menu::DBMenuEstaDisponivelTipoUsuario($codMenuDe,$COD_TIPO_USUARIO);
    	

    	/** Verifica a ordem do menu de **/
    	$ordemDe 	= \DBRel\Menu::DBDescobreOrdemMenu($COD_TIPO_USUARIO,$codMenuPai,$codMenuPara);
    	
   		if ($dispPara) {
	    	//$system->db->debug->debug("2");
   			$return = \DBRel\Menu::DBAvancaOrdemMenu($COD_TIPO_USUARIO,$codMenuPai,$ordemDe);
   			if ($return) $system->halt($return);
   		}

   		/** Disponibiliza o menu para o tipo do usuário caso não esteja disponível **/
    	if (!$dispDe) {
			//$system->db->debug->debug("3");
    		$return = \DBRel\Menu::DBaddMenuTipoUsuario($codMenuDe,$COD_TIPO_USUARIO,$ordemDe);
    		if ($return) $system->halt($return);
    	}else{
	    	/** Alter a ordem do menu de **/
   			$return = \DBRel\Menu::DBAlteraOrdemMenu($COD_TIPO_USUARIO,$codMenuDe,$ordemDe);
   			if ($return) $system->halt($return);
    	}
    }

    /**
	 * Desassocia um menu de um tipo de Usuário
     */
    public static function delMenuTipoUsuario($codMenuDe,$COD_TIPO_USUARIO,$codMenuPai) {
		global $system,$log,$db;
    	
    	/** Resgata as informações dos menus **/
    	$infoDe			= \DBRel\Menu::DBGetInfoMenu($codMenuDe);

    	if (!$infoDe) 	return false;
    	
    	/** Verifica se o menu de origem já está disponível para o usuário **/
    	$dispDe	= \DBRel\Menu::DBMenuEstaDisponivelTipoUsuario($codMenuDe,$COD_TIPO_USUARIO);
    	
    	if (!$dispDe) return false;

    	/** Verifica a ordem do menu de **/
    	$ordem 	= \DBRel\Menu::DBGetOrdemMenu($COD_TIPO_USUARIO,$codMenuDe);
    	
		$return = \DBRel\Menu::DBDiminuiOrdemMenu($COD_TIPO_USUARIO,$codMenuPai,$ordem);
		if ($return) $system->halt($return);
    	
   		$return = \DBRel\Menu::DBdelMenuTipoUsuario($codMenuDe,$COD_TIPO_USUARIO);
   		
   		/** Desassocia os dependentes **/
   		$dependentes	= array();
   		\DBRel\Menu::getArrayDependentesMenu($codMenuDe,$dependentes);
   		for ($i = 0; $i < sizeof($dependentes); $i++) {
   			$return = \DBRel\Menu::DBdelMenuTipoUsuario($dependentes[$i],$COD_TIPO_USUARIO);
   		}
    }

    /**
	 * Associa menu a um tipo de Usuário no banco
     */
    protected function DBaddMenuTipoUsuario($codMenu,$COD_TIPO_USUARIO,$ordem) {
		global $system,$log,$db;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO MENU_TIPO_USUARIO (COD_MENU, COD_TIPO_USUARIO,ORDEM) VALUES (?,?,?)",
				array($codMenu,$COD_TIPO_USUARIO,$ordem)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Desassocia menu a um tipo de Usuário no banco
     */
    protected function DBdelMenuTipoUsuario($codMenu,$COD_TIPO_USUARIO) {
		global $system,$log,$db;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM MENU_TIPO_USUARIO WHERE COD_MENU = ? AND COD_TIPO_USUARIO = ?",
				array($codMenu,$COD_TIPO_USUARIO)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Desassocia menu de todos os tipos de usuários
     */
    protected function DBDesassociaMenu($codMenu) {
		global $system,$log,$db;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM MENU_TIPO_USUARIO WHERE COD_MENU = ?",
				array($codMenu)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Exclui um Menu
     */
    protected function DBExcluiMenu($codMenu) {
		global $system,$log,$db;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM MENU WHERE CODIGO = ?",
				array($codMenu)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }
    
    /**
	 * Altera a ordem de um menu
     */
    protected function DBAlteraOrdemMenu($COD_TIPO_USUARIO,$codMenu,$ordem) {
		global $system,$log,$db;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ORDEM = ? WHERE MTU.COD_MENU = ? AND MTU.COD_TIPO_USUARIO = ?",
				array($ordem,$codMenu,$COD_TIPO_USUARIO)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Avança a ordem dos menus em 1 posicao para frente
     */
    protected function DBAvancaOrdemMenu($COD_TIPO_USUARIO,$codMenuPai,$ordem) {
		global $system,$log,$db;
    	if ($codMenuPai == null) $codMenuPai = 0;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ORDEM = MTU.ORDEM+1 WHERE MTU.ORDEM >= ? AND MTU.COD_MENU IN (SELECT M.CODIGO FROM MENU M WHERE IFNULL(M.COD_MENU_PAI,0) = ?) AND MTU.COD_TIPO_USUARIO = ?",
				array($ordem,$codMenuPai,$COD_TIPO_USUARIO)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Diminui a ordem dos menus em 1 posicao
     */
    protected function DBDiminuiOrdemMenu($COD_TIPO_USUARIO,$codMenuPai,$ordem) {
		global $system,$log,$db;
    	if ($codMenuPai == null) $codMenuPai = 0;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ORDEM = MTU.ORDEM-1 WHERE MTU.ORDEM > ? AND MTU.COD_MENU IN (SELECT M.CODIGO FROM MENU M WHERE IFNULL(M.COD_MENU_PAI,0) = ?) AND MTU.COD_TIPO_USUARIO = ?",
				array($ordem,$codMenuPai,$COD_TIPO_USUARIO)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Descobre a ordem de um novo menu
     */
    public static function DBDescobreOrdemMenu($COD_TIPO_USUARIO,$codMenuPai,$codMenu = null) {
		global $system,$log,$db;
    	if ($codMenu != null) {
    		$where	= " AND M.CODIGO	= '".$codMenu."'";
    	}else{
    		$where	= " ";
    	}
    	
    	if ($codMenuPai == null) {
    		$codMenuPai	= '0';
    	}
    	
    	$return	= $system->db->extraiPrimeiro("
				SELECT	IFNULL(MAX(MTU.ordem),0) ORDEM
				FROM	MENU_TIPO_USUARIO MTU,
						MENU M
				WHERE	M.CODIGO					= MTU.COD_MENU
				AND		IFNULL(M.COD_MENU_PAI,'0')	= '".$codMenuPai."'
				AND		MTU.COD_TIPO_USUARIO		= '".$COD_TIPO_USUARIO."'
				$where
			");
   		if (isset($return->ORDEM)) {
   			if (($codMenu == null) || ($return->ORDEM == 0)) {
   				return ($return->ORDEM+1);
   			}else{
   				return ($return->ORDEM);
   			}
   		}else{
   			return(null);
   		}
    }
    
    /**
	 * Salva Informações de um Menu
     */
    public static function DBSalvaInfoMenu($codMenu,$menu,$descricao,$codTipo,$link,$nivel,$codMenuPai,$icone) {
		global $system,$log,$db;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE	MENU M
				SET 	M.NOME			= ?,
						M.DESCRICAO		= ?,
						M.COD_TIPO		= ?,
						M.LINK			= ?,
						M.NIVEL			= ?,
						M.COD_MENU_PAI	= ?,
						M.ICONE			= ?
				WHERE	M.CODIGO 		= ?
			",
			array($menu,$descricao,$codTipo,$link,$nivel,$codMenuPai,$icone,$codMenu)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Cadastra um novo menu no banco
     */
    protected function DBCriaMenu($codMenu,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone) {
		global $system,$log,$db;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO MENU (CODIGO,NOME,DESCRICAO,COD_TIPO,LINK,NIVEL,COD_MENU_PAI,ICONE) VALUES (?,?,?,?,?,?,?,?)",
				array($codMenu,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Cria um novo menu
     */
    public static function criaMenu($menu,$descricao,$codTipo,$link,$codMenuPai,$icone) {
		global $system,$log,$db;
		
		/**
		 * Descobre o nível da árvore através do codMenuPai
		 */
		if ($codMenuPai == '' || !$codMenuPai || $codMenuPai == 'NULL') {
			$nivelArvore	= '0';
			$codMenuPai		= null;
		}else{
			$infoPai		= \DBRel\Menu::DBGetInfoMenu($codMenuPai);
			if (!$infoPai) {
				return 'Menu Pai não encontrado';
			}
			$nivelArvore	= $infoPai->NIVEL + 1;
		}
		
		if ($codTipo == 'M') {
			$link	= '';
		}
		
		/**
		 * Verifica se já existe menu
		 */
		if (\DBRel\Menu::existeMenu($menu,$codMenuPai) == true) {
			$system->halt('Menu já existe !!!',false,false,true);
		}else{
			$return	= \DBRel\Menu::DBCriaMenu(null,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone);
			if ($return) {
				$system->halt($return);
			}
		}
    }
    
    /**
	 * Exclui um menu
     */
    public static function excluiMenu($codMenu) {
		global $system,$log,$db;
		
		/**
		 * Resgata o array de dependentes
		 */
		$dependentes = array();
		\DBRel\Menu::getArrayDependentesMenu($codMenu,$dependentes);
		
		/** Desassocia todos os dependentes **/
		for ($i = 0; $i < sizeof($dependentes); $i++) {
			$return = \DBRel\Menu::DBDesassociaMenu($dependentes[$i]);
			if ($return) return ($return);
		}
		
		/** Exclui todos os dependentes **/
		for ($i = 0; $i < sizeof($dependentes); $i++) {
			$return = \DBRel\Menu::DBExcluiMenu($dependentes[$i]);
			if ($return) return ($return);
		}
		
		/** Desassocia o menu **/
		$return = \DBRel\Menu::DBDesassociaMenu($codMenu);
		if ($return) return ($return);
		
		/** Exclui o menu **/
		$return = \DBRel\Menu::DBExcluiMenu($codMenu);
		if ($return) return ($return);

		return (null);
    }

    
}