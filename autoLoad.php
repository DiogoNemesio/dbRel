<?php
set_include_path ( get_include_path () . PATH_SEPARATOR . CLASS_PATH );

//$dhcLoader	= new DHCLoader();
//spl_autoload($dhcLoader);
//spl_autoload_register('DHCLoader::autoload');

$loader = Zend_Loader_Autoloader::getInstance();

//$loader->setFallbackAutoloader(false);
//$loader->registerNamespace('Zend_');

#$loader->pushAutoloader(array('DHCLoader','autoload'),'');

//$loader->pushAutoloader('DHCAutoLoad');

spl_autoload_register ( '\Zage\Loader::autoload' );
