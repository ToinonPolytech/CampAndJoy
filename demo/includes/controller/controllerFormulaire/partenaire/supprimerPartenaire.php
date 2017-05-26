<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if(isset($_POST['id']) && isset($_SESSION['id'])){
	
	$db = new Database();
$db->delete("partenaire",array('id' => $_POST['id']),NULL); 

echo 'Partenaire supprimé ';

}
else
{
	echo "erreur lors de la suppression";
	
	
}