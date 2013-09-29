<?php
header("Content-type:text/xml");
print("<?xml version=\"1.0\"?>");

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}


if (isset($_GET['codMenuPai'])) 		$codMenuPai			= DHCUtil::antiInjection($_GET['codMenuPai']);
if (isset($_GET['codTipoUsuario'])) 	$codTipoUsuario 	= DHCUtil::antiInjection($_GET['codTipoUsuario']);


if ((isset($codMenuPai)) and (isset($codTipoUsuario))) {
   /**
 	* Resgatar os menus
 	*/
	$menus			= \DBRel\Menu::DBGetMenuItensTipoUsuario($codTipoUsuario,$codMenuPai);
	$xmlObj			= new DHCXMLConDataView();
	$xmlObj->loadArray($menus);
	//$system->log->debug->debug("XML: ".$xmlObj->getXML());
	
	print $xmlObj->getXML();
}
