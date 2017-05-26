 <?php
 if (!isset($require))
{
	$require="";
}
$require.="activities.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if (isset($_POST['id']))
{	
	$act=new Activite(htmlspecialchars($_POST['id']));
	if($act->getIdOwner()==$_SESSION['id'] || $_SESSION['access_level']!='CLIENT')
	{
		$act->setDeleted(true);
		$act->saveToDb();
		echo 'Activité supprimée';
	}
	else
	{
		echo "ERREUR : vous n'avez pas les droits pour supprimer cette activité ";
	}
	
}
else
{
	echo "erreur lors de la suppression";
}