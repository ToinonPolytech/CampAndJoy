<?php
if (!isset($require))
{
	$require="";
}
$require.="reservation.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if (isset($_POST['id']) && isset($_POST['type']) && isset($_SESSION['id']))
{
	$reservation=new Reservation(htmlspecialchars($_POST['id']), htmlspecialchars($_POST['type']), $_SESSION['id']);
	$reservation->setDeleted(true);
	$reservation->saveToDb();
	echo 'Réservation supprimée';
}
else
{
	echo "erreur lors de la suppression";
}





?>
