<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,activities.class.php,reservation.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	$can_edit_personnes=true;
	if (!$cuser->can(CAN_RESERVE_ACTIVITIES) && !$cuser->can(CAN_RESERVE_RESTAURANT) && !$cuser->can(CAN_RESERVE_LIEU_COMMUN))
	{
		exit();
	}
	if (isset($_POST['id']) && isset($_POST['type']) && isset($_SESSION['id']))
	{
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php
		$can_edit_date=false;
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
			<div class="message_block error"  style="width:100%;text-align:center">
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
			<div class="message_block error"  style="width:100%;text-align:center">
				<p>Vous ne pouvez pas modifier une réservation qui a déjà eu lieu.</p>
			</div>
			<?php
			exit();
		}
		if ($reservation->getValide()==0)
		{
			?>
			<div class="message_block error"  style="width:100%;text-align:center">
				<p>Vous ne pouvez pas modifier une réservation qui est en attente de validation.</p>
			</div>
			<?php
			exit();
		}
		if ($_POST['type']=="RESTAURANT")
		{
			$can_edit_date=true;
			if (!$cuser->can(CAN_RESERVE_RESTAURANT))
			{
				?>	
				<div class="message_block error"  style="width:100%;text-align:center">
					<p>Vous n'avez pas les droits pour modifier une réservation.</p>
				</div>
				<?php
				exit();
			}
			if ($reservation->getTime()<time()+24*3600*1)
			{
				?>			
				<div class="message_block error"  style="width:100%;text-align:center">
					<p>Vous ne pouvez pas modifier cette réservation au restaurant qui a lieu dans moins de 24heures.</p>
				</div>
				<?php
				exit();
			}
			$nom="Restaurant : ".htmlentities($database->getValue("restaurant", array("id" => $_POST["id"]), "nom"));
			$heureOuverture=unserialize($database->getValue("restaurant", array("id" => $_POST["id"]), "heureOuverture"));
			?>
			<script type="text/javascript">
				$.datetimepicker.setLocale('fr');
				var logic = function( currentDateTime )
				{
					<?php
					$arrayDay=array();
					foreach ($heureOuverture as $day => $horaires)
					{
						$done=false;
						?>
						if(currentDateTime.getDay()==<?php echo $day; ?>)
						{
							this.setOptions({
							  allowTimes:[
							<?php
							foreach ($horaires as $i => $open)
							{
								$texte="";
								if ($open)
								{
									if ($done) echo ",";
									if (floor($i/2)<10) $texte.="0"; $texte.=floor($i/2); $texte.=":"; if ($i%2==1) $texte.="30"; else $texte.="00";
									echo "'".$texte."'";
									$done=true;
								}
							}
							?>]});
						}
						<?php
						if (!$done)
						{
							$arrayDay[]=$day;
						}
					}
					?>
				};
				$(document).ready(function(){
					$('#datepicker').datetimepicker({
						formatDate:'d.m.y',
						format:'d/m/y',
						onChangeDateTime:logic,
						onShow:logic,
						minDate:0,
						disabledWeekDays:[<?php
						$done=false;
						foreach ($arrayDay as $d)
						{
							if ($done) echo ",";
							echo $d;
							$done=true;
						}
						?>]
					});
				});
			</script>
			<?php
		}
		else if ($_POST['type']=="ACTIVITE")
		{
			if (!$cuser->can(CAN_RESERVE_ACTIVITIES))
			{
				?>	
				<div class="message_block error"  style="width:100%;text-align:center">
					<p>Vous n'avez pas les droits pour modifier une réservation.</p>
				</div>
				<?php
				exit();
			}
			$act=new Activite($_POST["id"]);
			if ($act->getPrix()>0)
			{
				?>
				<div class="message_block error"  style="width:100%;text-align:center">
					<p>Vous ne pouvez pas modifier une réservation d'activité payante.</p>
				</div>
				<?php
				exit();
			}
			if ($act->getFinReservation()<time())
			{
				?>		
				<div class="message_block error"  style="width:100%;text-align:center">
					<p>Vous ne pouvez pas modifier une réservation après la clôture de celle ci.</p>
				</div>
				<?php
				exit();
			}
			$nom="Activité : ".$act->getNomByLang();
		}
		else if ($_POST['type']=="ETAT_LIEUX")
		{
			?>
			<div class="message_block error"  style="width:100%;text-align:center">
				<p>Vous ne pouvez pas modifier une réservation pour un état des lieux.</p>
			</div>
			<?php
			exit();
			
			$nom="État des Lieux : ".$database->getValue("users", array("id" => $_POST["id"]), "prenom")." ".$database->getValue("users", array("id" => $_POST["id"]), "nom");
		}
		else if ($_POST['type']=="LIEU_COMMUN")
		{
			$can_edit_personnes=false;
			$can_edit_date=true;
			if (!$cuser->can(CAN_RESERVE_LIEU_COMMUN))
			{
				?>	
				<div class="message_block error"  style="width:100%;text-align:center">
					<p>Vous n'avez pas les droits pour modifier cette réservation.</p>
				</div>
				<?php
				exit();
			}
			$nom="Lieu : ".htmlentities($database->getValue("lieu_commun", array("id" => $_POST["id"]), "nom"));
		}
		if (!isset($nom))
			exit();
		
		if (isset($_POST["from"]) && $_POST["from"]=="myaccount")
		{
			?>
			<div class="ctl_body_line custom_table_line" id='res_<?php echo $reservation->getId(); ?>_form'>
				<div class="custom_table_col">
					<div>
						<?php echo htmlentities($nom); ?>
					</div>
				</div>
				<div class="custom_table_col">
					<div>
						<?php
						if ($can_edit_date)
						{
							?>
							<input class="campandjoy_input w-input" id="datepicker" maxlength="256" name="datepicker" placeholder="Date et Heure" type="text" value="<?php echo date("d/m/Y H:i", $reservation->getTime()); ?>">
							<?php
						}
						?>
					</div>
				</div>
				<div class="custom_table_col">
					<div>
						<?php
						if ($can_edit_personnes)
						{
							?>
							<input value="<?php echo htmlentities($reservation->getNbrPersonne()); ?>" min="1" class="campandjoy_input w-input" id="nbrPersonnes" maxlength="256" name="nbrPersonnes" placeholder="Nombre de personnes" type="number">
							<?php
						}
						?>
					</div>
				</div>
				<div class="custom_table_col">
					<div>
					<a class="primary_btn w-button wrapp_btn_null" data-on-confirm="webflowCampandJoy('close-modal-msg'); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('modifierReservation.controllerForm.php')); ?>', {nbrPersonnes : $('#nbrPersonnes').val(), <?php if ($can_edit_date) { ?>time : $('#datepicker').val(), <?php } ?> idAuto : <?php echo $reservation->getIdAuto(); if (isset($_POST["from"])) { echo ", from : '".htmlentities($_POST["from"])."'"; } ?>}, '#reservation_table', 'before');" data-ix="show-modal-msg" data-title="Modifier votre réservation" data-message="Êtes vous certains de modifier la réservation ?" data-on-refuse="webflowCampandJoy('close-modal-msg');" data-type="error" href="#">Modifier</a>
					</div>
				</div>
			</div>
			<?php
		}
		else
		{
			?>
			<script type="text/javascript">
				$("#modif<?php echo $reservation->getId(); ?>").toggle();
			</script>
			<div class="program_event_master_block" id='res_<?php echo $reservation->getId(); ?>_form'>
				<div class="organizer_block w-clearfix horizontal_form">
					<?php
					if ($can_edit_date)
					{
						?>
						<input class="campandjoy_input w-input" id="datepicker" maxlength="256" name="datepicker" placeholder="Date et Heure" type="text" value="<?php echo date("d/m/Y H:i", $reservation->getTime()); ?>">
						<?php
					}
					if ($can_edit_personnes)
					{
					?>
						<input value="<?php echo htmlentities($reservation->getNbrPersonne()); ?>" min="1" class="campandjoy_input input_width_1 w-input" id="nbrPersonnes" maxlength="256" name="nbrPersonnes" placeholder="Nombre de personnes" type="number" onkeypress="checkGoodInput(event);">  personne(s)</span>	
					<?php
					}
					?>
				</div>
				<a class="primary_btn w-button wrapp_btn_null" data-on-confirm="webflowCampandJoy('close-modal-msg'); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('modifierReservation.controllerForm.php')); ?>', {nbrPersonnes : $('#nbrPersonnes').val(), <?php if ($can_edit_date) { ?>time : $('#datepicker').val(), <?php } ?> idAuto : <?php echo $reservation->getIdAuto(); if (isset($_POST["from"])) { echo ", from : '".htmlentities($_POST["from"])."'"; } ?>}, '#<?php echo $reservation->getId(); ?>', 'append');" data-ix="show-modal-msg" data-title="Modifier votre réservation" data-message="Êtes vous certains de modifier la réservation ?" data-on-refuse="webflowCampandJoy('close-modal-msg');" data-type="error" href="#">Modifier</a>
			</div>
			<?php
		}
	}
?>