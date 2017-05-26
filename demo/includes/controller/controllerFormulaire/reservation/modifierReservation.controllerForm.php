<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,activities.class.php,reservation.class.php,reservation.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	if (!$cuser->can(CAN_RESERVE_ACTIVITIES) && !$cuser->can(CAN_RESERVE_RESTAURANT) && !$cuser->can(CAN_RESERVE_LIEU_COMMUN))
	{
		exit();
	}
	if (isset($_POST['idAuto']) && isset($_POST['nbrPersonnes']))
	{
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php
		$can_edit_date=false;
		$database=new Database();
		if (!$database->count("reservation", array("idAuto" => $_POST["idAuto"], "idUser" => $_SESSION["id"])))
		{
			?>
			<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
				<p>Cette réservation n'existe pas.</p>
			</div>
			<?php
			exit();
		}
		$reservation=new Reservation($_POST['idAuto']);
		if ($reservation->getTime()<time())
		{
			?>
			<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
				<p>Vous ne pouvez pas modifier une réservation qui a déjà eu lieu.</p>
			</div>
			<?php
			exit();
		}
		if ($reservation->getValide()==0)
		{
			?>
			<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
				<p>Vous ne pouvez pas modifier une réservation qui est en attente de validation.</p>
			</div>
			<?php
			exit();
		}
		if ($reservation->getType()=="RESTAURANT")
		{
			$can_edit_date=true;
			if (!$cuser->can(CAN_RESERVE_RESTAURANT))
			{
				?>
				<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
					<p>Vous n'avez pas les droits pour modifier une réservation.</p>
				</div>
				<?php
				exit();
			}
			if ($reservation->getTime()<time()+24*3600*1)
			{
				?>
				<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
					<p>Vous ne pouvez pas modifier cette réservation au restaurant qui a lieu dans moins de 24heures.</p>
				</div>
				<?php
				exit();
			}
			$nom="Restaurant : ".htmlentities($database->getValue("restaurant", array("id" => $reservation->getId()), "nom"));
			$heureOuverture=unserialize($database->getValue("restaurant", array("id" => $reservation->getId()), "heureOuverture"));
		}
		else if ($reservation->getType()=="ACTIVITE")
		{
			if (!$cuser->can(CAN_RESERVE_ACTIVITIES))
			{
				?>
				<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
					<p>Vous n'avez pas les droits pour modifier une réservation.</p>
				</div>
				<?php
				exit();
			}
			$act=new Activite($reservation->getId());
			if ($act->getPrix()>0)
			{
				?>
				<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
					<p>Vous ne pouvez pas modifier une réservation d'activité payante.</p>
				</div>
				<?php
				exit();
			}
			if ($act->getFinReservation()<time())
			{
				?>
				<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
					<p>Vous ne pouvez pas modifier une réservation après la clôture de celle ci.</p>
				</div>
				<?php
				exit();
			}
			$nom="Activité : ".$act->getNom();
		}
		else if ($reservation->getType()=="ETAT_LIEUX")
		{
			?>
			<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
				<p>Vous ne pouvez pas modifier une réservation pour un état des lieux.</p>
			</div>
			<?php
			exit();
			
			$nom="État des Lieux : ".$database->getValue("users", array("id" => $reservation->getId()), "prenom")." ".$database->getValue("users", array("id" => $reservation->getId()), "nom");
		}
		else if ($reservation->getType()=="LIEU")
		{
			$can_edit_date=true;
			?>
			<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
				<p>TO DO</p>
			</div>
			<?php
			exit();
			
			$nom="Lieu : ".htmlentities($database2->getValue("lieu_commun", array("id" => $reservation->getId()), "nom"));
		}
		if ($can_edit_date && isset($_POST["time"]))
		{
			$temp=explode(" ", $_POST["time"]);
			if (count($temp)==2)
			{
				$dateTemp=explode("/", $temp[0]);
				$heureTemp=explode(":", $temp[1]);
				if (count($dateTemp)==3 && count($heureTemp)==2)
				{
					$time=strtotime($dateTemp[2]."-".$dateTemp[1]."-".$dateTemp[0]." ".$heureTemp[0].":".$heureTemp[1]);
				}
			}
			if (!isset($time) || $time===false)
			{
				?>
				<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
					<p>La date envoyé n'est pas correcte.</p>
				</div>
				<?php
				exit();
			}
			$reservation->setTime($time);
		}
		$reservation->setNbrPersonne($_POST["nbrPersonnes"]);
		$reservationController = new Controller_Reservation($reservation);
		if ($reservationController->isGood())
		{
			$reservation->saveToDb();
			?>
			<div class="message_block success" id='reponse_controller_msg' style="width:100%;text-align:center">
				<p>La réservation a bien été modifiée.</p>
			</div>
			<?php
			if (isset($_POST["from"]) && $_POST["from"]=="myaccount")
			{
				?>
				<script type="text/javascript">
					$('#res_<?php echo $reservation->getId(); ?>').children(".custom_table_col").next(".custom_table_col").next(".custom_table_col").html("<?php echo $reservation->getNbrPersonne(); ?>");
					$('#res_<?php echo $reservation->getId(); ?>_form').remove();
					$('#res_<?php echo $reservation->getId(); ?>').toggle();
				</script>
				<?php
			}
			else
			{
				?>
				<script type="text/javascript">
					<?php
					if ($reservation->getType()=="ACTIVITE")
					{
						$capacite=$act->getPlacesLim();
						if ($capacite>0)
						{
							$database->select("reservation", array("type" => "ACTIVITE", "id" => $reservation->getId()), "nbrPersonne");
							while ($d=$database->fetch()) { $capacite-=$d["nbrPersonne"]; }
						}
						?>
						$(".activity_<?php echo $reservation->getId(); ?>").find(".place_available").children("strong").html("<?php echo $capacite; ?>");
						<?php
					}
					?>
					$('#res_<?php echo $reservation->getId(); ?>_form').remove();
					$("#modif<?php echo $reservation->getId(); ?>").toggle();
				</script>
				<?php
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg' style="width:100%;text-align:center">
				<p><?php echo $reservationController->getError(); ?></p>
			</div>
			<?php
			exit();
		}
	}
?>