<?php 
if (!isset($require))
{
	$require="";
}
$require.="user.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Staff extends User
{
	/**
		Ici les fonctions que seul les membres de l'équipe pourront utiliser
		On définira des enfants à cette classe
		Voici des exemples :
			- public function createUser();
			- public function addPoints($idUser, $points);
	**/
}
?>