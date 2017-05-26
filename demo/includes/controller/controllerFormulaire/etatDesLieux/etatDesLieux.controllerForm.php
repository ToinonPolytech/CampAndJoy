<?php 
if (!isset($require))
{
	$require="";
}
$require.="etatDesLieux.class.php,etatDesLieux.controller.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if(isset($_POST['users']) && isset($_POST['dateDeb']) && isset($_POST['dateFin']) && isset($_POST['duree']))
{	
	$edl = new EtatDesLieux(NULL,htmlspecialchars($_POST['users']), htmlspecialchars(strtotime($_POST['dateDeb'])), htmlspecialchars(strtotime($_POST['dateFin'])), htmlspecialchars($_POST['duree']));	
	$edlController = new Controller_EtatDesLieux($edl);
	if(isset($_POST['id']))
	{
		$edl->setId($_POST['id']);
	}
	if($edlController->isGood())
	{
		$edl->saveToDb();
		echo "Mise à jour des états des lieux réussie ";
	}
}
else
{
	echo "ERREUR : Un problème est survenu lors de l'envoi du formulaire.";
}
?>