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
	if((!isStaff() || ($_GET["categorie"]=="ACTIVITE" && (!$cuser->can("CAN_CREATE_ACTIVITIES") || $database->count("activities", array("id" => $_GET["id"]))<=0))) || 
	($_GET["categorie"]=="RESTAURANT" && !$cuser->can("CAN_ADD_RESTAURANT") && ($database->count("restaurant", array("id" => $_GET["id"]))<=0 || !isRestaurateur($_GET["id"]))) ||
	(!isStaff() || ($_GET["categorie"]=="ETAT_LIEUX" && $database->count("users", array("id" => $_GET["id"]))<=0)) ||
	(!isStaff() || ($_GET["categorie"]=="LIEU_COMMUN" && (!$cuser->can("CAN_ADD_LIEU_COMMUN_STAFF") || $database->count("lieu_commun", array("id" => $_GET["id"], "estReservable" => 1))<=0))))
	{
		if (isStaff() || $_GET["categorie"]!="ACTIVITE" || !$cuser->can("CAN_CREATE_ACTIVITIES") || !$database->count("activities", array("id" => $_GET["id"], "idDirigeant" => $_SESSION["id"])))
		{
			exit();
		}
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
			else if (isStaff() && $_GET["categorie"]=="RESTAURANT")
			{
				include i("reservation_rest_administration.php");
			}
			else if (isStaff() && $_GET["categorie"]=="ETAT_LIEUX")
			{
				
			}
			else if (isStaff() && $_GET["categorie"]=="LIEU_COMMUN")
			{
				include i("reservation_lieuCommun_administration.php");
			}
		?>
	</div>
</div>