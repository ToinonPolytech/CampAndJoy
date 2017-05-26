<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,activities.class.php,user.class.php,user.controller.class.php,reservation.class.php,reservation.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	
	if (!auth())
	{
		exit();
	}
	$duree=0;
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if (isset($_POST["id"]) && isset($_POST["type"])  && isset($_POST["nbrPersonnes"]))
	{
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php	 
		$id=$_POST["id"];
		if(isset($_POST["idEquipe"]))
		{
			$idEquipe =  $_POST["idEquipe"];
		}
		else
		{
			$idEquipe = 0 ; 
		}
		if ($_POST["type"]=="LIEU_COMMUN")
		{
			if (!$cuser->can(CAN_RESERVE_LIEU_COMMUN))
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous n'avez pas les droits pour réserver.</p>
				</div>
				<?php
				exit();
			}
			if (!isset($_POST["duree"]) || empty($_POST["duree"]))
				exit();
			
			$duree=$_POST["duree"];
		}
		if ($_POST["type"]=="ACTIVITE")
		{
			if (!$cuser->can(CAN_RESERVE_ACTIVITIES))
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous n'avez pas les droits pour réserver.</p>
				</div>
				<?php
				exit();
			}
			$db=new Database();
			$time=$db->getValue("activities", array("id" => $id), "time_start");
		}
		else if ($_POST["type"]=="ETAT_LIEUX")
		{
			$time=$_POST["time"];
			if ($time<time())
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Une erreur est survenue.</p>
				</div>
				<?php
				exit();
			}
			$db=new Database();
			$db->select("etat_lieux", array("debutTime" => array("<=", $time), "finTime" => array(">=", $time), array("idUser", "debutTime", "finTime", "duree_moyenne")));
			$db2=new Database();
			$staffDispo=array();
			while ($data=$db->fetch())
			{
				/// Il ne doit pas avoir de réservation pendant les horaires sélectionnés par le user
				if ($db2->count("reservation", array("id" => $data["idUser"], "type" => "ETAT_LIEUX", "time" => array($time-$data["duree_moyenne"]+1, $time+$data["duree_moyenne"]-1)))==0)
				{
					$count=$db2->count("reservation", array("id" => $data["idUser"], "type" => "ETAT_LIEUX", "time" => array($data["debutTime"], $data["finTime"])));
					$staffDispo[$data["idUser"]]=$count;
				}
			}
			arsort($staffDispo); // On tri de manière décroissante en conservant les index
			$id=key($staffDispo);
		}
		else if (isset($_POST["time"]))
		{
			$time=$_POST["time"];
		}
		else if (isset($_POST["date"]) && isset($_POST["heure"]))
		{
			if (!empty($_POST["date"]) && !empty($_POST["heure"]))
			{
				$temp=explode("/", $_POST["date"]);
				$temp2=explode(":", $_POST["heure"]);
				if (count($temp2)==2 && count($temp)==3 && ($temp2[1]=="00" || $temp2[1]=="30"))
				{
					$time=strtotime($temp[2]."-".$temp[1]."-".$temp[0]." ".$_POST["heure"]);
					if ($time===false)
					{
						?>
						<div class="message_block error" id='reponse_controller_msg'>
							<p>Une erreur est survenue.</p>
						</div>
						<?php
						exit();
					}
				}
				else
				{
					?>
					<div class="message_block error" id='reponse_controller_msg'>
						<p>Une erreur est survenue.</p>
					</div>
					<?php
					exit();
				}
			}
			else
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Merci de remplir tout les champs.</p>
				</div>
				<?php
				exit();
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p>Une erreur est survenue.</p>
			</div>
			<?php
			exit();
		}
		if ($_POST["type"]=="RESTAURANT")
		{
			if (!$cuser->can(CAN_RESERVE_RESTAURANT))
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>Vous n'avez pas les droits pour réserver.</p>
				</div>
				<?php
				exit();
			}
		}
		$reservation = new Reservation($id, $_POST["type"], $_SESSION["id"], $_POST["nbrPersonnes"], $time, 1, $duree);
		$reservationController = new Controller_Reservation($reservation);
		if ($reservationController->isGood())
		{
			$reservation->saveToDb();
			if ($_POST["type"]=="LIEU_COMMUN")
			{
				?>
				<script type="text/javascript">
					$("#reservation_<?php echo $id; ?>").html('<div class="message_block success" id="reponse_controller_msg"><p>Votre réservation a bien été enregistrée.</p></div>');
				</script>
				<?php
			}
			else if ($_POST["type"]=="RESTAURANT")
			{
				?>
				<script type="text/javascript">
					$("#<?php echo $id; ?>_res").html('<div class="message_block success" id="reponse_controller_msg"><p>Votre réservation a bien été enregistrée.</p></div>');
				</script>
				<?php
			}
			else if ($_POST["type"]=="ACTIVITE")
			{
			?>
				<script type="text/javascript">
					$(".activity_<?php echo $id; ?>").find(".place_available").children("strong").html(parseInt($(".activity_<?php echo $id; ?>").find(".place_available").children("strong").html())-parseInt(<?php echo $reservation->getNbrPersonne(); ?>));
					$("#<?php echo $id; ?>_res").html('<div class="message_block success" id="reponse_controller_msg"><p>Votre réservation a bien été enregistrée.</p></div>');
				</script>
			<?php
			}
			else
			{
				?>
				<div class="message_block success" id="reponse_controller_msg"><p>Votre réservation a bien été enregistrée.</p></div>
				<?php
				
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $reservationController->getError(); ?></p>
			</div>
			<?php
		}
	}
?> 