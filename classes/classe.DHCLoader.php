<?php

/**
 * @package: DHCLoader
 * @created: 27/11/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.1
 *
 * Carregador de classes
 */

class DHCLoader {

    public static function autoload($class) {
        /**
         * Tentar carregar as classes próprias !!
         */
		$prodDirs = array(CLASS_PATH,SHARED_CLASS_PATH,PKG_PATH . 'adodb/', PKG_PATH . 'adodb/drivers/');
		
		if (defined('MODULE_CLASS_PATH')) {
			$prodDirs[] = MODULE_CLASS_PATH;
		}
		
		for ($i=0; $i<sizeof($prodDirs); $i++) {
			//echo "PATH=".$prodDirs[$i]."\n";
			$file   = $prodDirs[$i] . '/classe.'.$class.'.php';
			if (file_exists($file)) {
				//parent::loadFile('classe.'.$class . '.php',$prodDirs[$i],true);
				Zend_Loader::loadFile('classe.'.$class . '.php',$prodDirs[$i],true);
				//include_once($file);
				return $class;
			}
        }
        return false;
	}
}