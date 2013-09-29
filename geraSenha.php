<?php

include_once('classes/classe.DHCCrypt.php');

$usuario	= "admin";
$senha		= "admin";

$crypt = new DHCCrypt();
echo "\n".$crypt->encrypt('dbRelPasswd','dbRelUser')."\n";

echo "MD5\n";
#echo md5($usuario."|drf|".$senha)."\n";

?>
