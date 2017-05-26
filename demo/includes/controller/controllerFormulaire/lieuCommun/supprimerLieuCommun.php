<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if(isset($_POST['id'])){
	
	$db = new Database();
$db->delete("lieu_commun",array('id' => $_POST['id'])); 

echo "Le lieu a été supprimé";

}
else
{
	echo "erreur lors de la suppression";
	
	
}
?>