<?php 
if (!isset($require))
{
	$require="";
}
$require.="user.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Client extends User
{
	/**
		On pourra définir des fonctions réservées uniquement au client si il y a
	**/
}
?>