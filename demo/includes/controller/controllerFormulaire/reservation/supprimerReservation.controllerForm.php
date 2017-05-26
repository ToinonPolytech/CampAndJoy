<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,activities.class.php,reservation.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	
	if (isset($_POST['id']) && isset($_POST['type']) && isset($_SESSION['id']))
	{
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php
		$time=NULL;
		if (isset($_POST["time"]))
		{
			$time=$_POST["time"];
		}
		$database=new Database();
		if (($time==NULL && !$database->count("reservation", array("id" => $_POST["id"], "idUser" => $_SESSION["id"], "type" => $_POST["type"]))) || 
		($time!=NULL && !$database->count("reservation", array("id" => $_POST["id"], "idUser" => $_SESSION["id"], "type" => $_POST["type"], "time" => $time))))
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p>Cette réservation n'existe pas.</p>
			</div>
			<?php
			exit();
		}
		$reservation=new Reservation(htmlspecialchars($_POST['id']), htmlspecialchars($_POST['type']), $_SESSION['id']);
		if ($time!=NULL && $_POST['type']=="RESTAURANT")
		{
			$reservation->setTime($time);
		}
		if ($reservation->getTime()<time())
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p>Vous ne pouvez pas annuler une réservation qui a déjà eu lieu.</p>
			</div>
			<?php
			exit();
		}
		if ($_POST['type']=="RESTAURANT")
		{
			if (!$cuser->can(CAN_RESERVE_RESTAURANT))
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous n'avez pas les droits pour retirer une réservation.</p>
				</div>
				<?php
				exit();
			}
			if ($reservation->getTime()<time()+24*3600*1)
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous ne pouvez pas annuler cette réservation au restaurant qui a lieu dans moins de 24heures.</p>
				</div>
				<?php
				exit();
			}
		}
		else if ($_POST['type']=="ACTIVITE")
		{
			if (!$cuser->can(CAN_RESERVE_ACTIVITIES))
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous n'avez pas les droits pour retirer une réservation.</p>
				</div>
				<?php
				exit();
			}
			$act=new Activite($_POST["id"]);
			if ($act->getPrix()>0)
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous ne pouvez pas annuler une réservation d'activité payante.</p>
				</div>
				<?php
				exit();
			}
			if ($act->getFinReservation()<time())
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous ne pouvez pas annuler une réservation après la clôture de celle ci.</p>
				</div>
				<?php
				exit();
			}
		}
		else if ($_POST['type']=="ETAT_LIEUX")
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p>Vous ne pouvez pas annuler une réservation pour un état des lieux.</p>
			</div>
			<?php
			exit();
		}
		else if ($_POST['type']=="LIEU")
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p>TO DO</p>
			</div>
			<?php
			exit();
		}
		$reservation->setDeleted(true);
		$reservation->saveToDb();
		?>
		<div class="message_block success" id='reponse_controller_msg'>
			<p>La réservation a bien été annulée.</p>
		</div>
		<?php
			if (isset($_POST["from"]) && $_POST["from"]=="myaccount")
			{
				?>
				<script type="text/javascript">
					$("#res_<?php echo $_POST["id"]; ?>").toggle('fast', function(){ $(this).remove(); });
					$("#reponse_controller_msg_read").fadeOut(10000, function(){ $(this).remove(); });
				</script>
				<?php
			}
			else
			{
				?>
				<script type="text/javascript">
					$(".activity_<?php echo $_POST["id"]; ?>").find(".place_available").children("strong").html(parseInt($(".activity_<?php echo $_POST["id"]; ?>").find(".place_available").children("strong").html())+parseInt(<?php echo $reservation->getNbrPersonne(); ?>));
					$("#modif<?php echo $_POST["id"]; ?>, #cancel<?php echo $_POST["id"]; ?>").remove();
				</script>
				<?php
			}
	}
?>
