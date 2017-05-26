<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if ($_GET["categorie"]!="ACTIVITE" && $_GET["categorie"]!="RESTAURANT" && $_GET["categorie"]!="ETAT_LIEUX" && $_GET["categorie"]!="LIEU_COMMUN")
	{
		exit();
	}
	$database=new Database();
	if(!isStaff() || 
	($_GET["categorie"]=="ACTIVITE" && (!$cuser->can("CAN_CREATE_ACTIVITIES") || $database->count("activities", array("id" => $_GET["id"]))<=0)) || 
	($_GET["categorie"]=="RESTAURANT" && ($database->count("restaurant", array("id" => $_GET["id"]))<=0 || !isRestaurateur($_GET["id"]))) ||
	($_GET["categorie"]=="ETAT_LIEUX" && $database->count("users", array("id" => $_GET["id"]))<=0) ||
	($_GET["categorie"]=="LIEU_COMMUN" && (!$cuser->can("CAN_CREATE_ACTIVITIES") || $database->count("lieu_commun", array("id" => $_GET["id"], "estReservable" => 1))<=0)))
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Les réservations</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<?php
			if ($_GET["categorie"]=="ACTIVITE")
			{
				include i("reservation_act_administration.php");
			}
			else if ($_GET["categorie"]=="RESTAURANT")
			{
				include i("reservation_rest_administration.php");
			}
			else if ($_GET["categorie"]=="ETAT_LIEUX")
			{
				
			}
			else if ($_GET["categorie"]=="LIEU_COMMUN")
			{
				
			}
		?>
	</div>
</div>