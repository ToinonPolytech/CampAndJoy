<?php 
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,activities.class.php,activite.controller.class.php,user.class.php,user.controller.class.php,images.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
		exit();

	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if(!$cuser->can(CAN_CREATE_ACTIVITIES))
	{
		exit();
	}
	if (isset($_POST["name"]) && isset($_POST["timeStart"]) && isset($_POST["description"]) && isset($_POST["duree"]) && 
	isset($_POST["lieu"]) && isset($_POST["lieu_autre"]) && isset($_POST["is_reservable"]) &&
	isset($_POST["debutReservation"]) && isset($_POST["finReservation"]) && isset($_POST["placesLim"]) && isset($_POST["type"]) && 
	(!isStaff() || (isset($_POST["points"]) && isset($_POST["prix"]) && isset($_POST["recurrence"]) && isset($_POST["finRecurrence"]) && isset($_POST["animateurEnCharge"]))))
	{
		if (!isStaff())
		{
			$animateurEnCharge=$_SESSION["id"];
			$points=0;
			$prix=0;
			$recurrence=0;
			$finRecurrence=0;
			$is_recurrente=0;
		}
		else
		{
			$animateurEnCharge=$_POST["animateurEnCharge"];
			$points=$_POST["points"];
			$prix=$_POST["prix"];
			$recurrence=$_POST["recurrence"];
			$is_recurrente=$_POST["is_recurrente"];
			$finRecurrence=$_POST["finRecurrence"];
		}
		$name=array(LANG_USER => $_POST["name"]);
		$timeStart=$_POST["timeStart"];
		$description=array(LANG_USER => $_POST["description"]);
		$duree=$_POST["duree"];
		$lieu=$_POST["lieu"];
		if ($lieu==-1)
			$lieu=$_POST["lieu_autre"];
		
		$mustBeReserved=$_POST["is_reservable"];
		$debutReservation=$_POST["debutReservation"];
		$finReservation=$_POST["finReservation"];
		$placesLim=$_POST["placesLim"];	
		$havePlacesLim=(isset($_POST["places_limcheck"])) ? 1 : 0;

		
		foreach ($lang_available as $langSign => $langExplain)
		{
			if ($langSign!=LANG_USER)
			{
				if (isset($_POST["name_".$langSign]))
				{
					$name[$langSign]=$_POST["name_".$langSign];
				}
				if (isset($_POST["description_".$langSign]))
				{
					$description[$langSign]=$_POST["description_".$langSign];
				}
			}
		}
		if ($havePlacesLim==0)
			$placesLim=0;
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php	 	
		
		if ($_POST["lieu"]!=-1)
		{
			$db = new Database();
			if ($db->count("lieu_commun", array("id" => $_POST["lieu"]))!=1)
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p>L'emplacement sélectionné est introuvable</p>
				</div>
				<script type="text/javascript">
					$("#part_1, #part_2, #part_3, #part_4").show();
				</script>
				<?php
				
				exit();
			}
			$lieu=$db->getValue("lieu_commun", array("id" => $_POST["lieu"]), "nom");
		}
		$type="";
		if (isset($_POST["type"]))
		{
			foreach (explode(",", $_POST["type"]) as $tag)
			{
				if (in_array($tag, $listeTypes))
				{
					if ($type!="")
						$type.=" ";
					
					$type.=$tag;
				}
			}
		}
		$act = new Activite(NULL,strtotime($timeStart),serialize($name),serialize($description),$duree,$lieu,$type,$placesLim,$prix,$animateurEnCharge,$points,$mustBeReserved,strtotime($debutReservation),strtotime($finReservation));
		$actController = new Controller_Activite($act);
		if(isset($_POST['id']))
		{
			if (!isset($db))
				$db=new Database();
			
			$db->select("activities", array("id" => $_POST["id"]), array("idRecurrente", "idDirigeant"));
			$data=$db->fetch();
			$act->setId($_POST['id']);
			if ($data["idRecurrente"]!=-1)
			{
				$act->setIdRecurrente($data["idRecurrente"]);
			}
			if((!isStaff() && $data["idDirigeant"]!=$_SESSION["id"]) ||  (isStaff() && $data["idDirigeant"]!=$_SESSION["id"] && !$staffCanEditOther))
			{
				exit();
			}
		}
		if($actController->isGood())
		{
			$act->saveToDb();
			if($is_recurrente)
			{
				if(!isset($_POST['id']))
				{
					$rec=$act->getDate();
					$debutReservationrec=strtotime($debutReservation);
					$finReservationrec=strtotime($finReservation);
					$dateRecurrenceCount=1;
					while(($_POST['recurrence']!=-1 && $rec<=strtotime($_POST['finRecurrence'])) || ($_POST['recurrence']==-1 && isset($_POST['dateRecurrence_'.$dateRecurrenceCount])))
					{	
						if($_POST['recurrence']==1){
							$rec+=24*3600*1;
							$debutReservationrec+=24*3600*1;
							$finReservationrec+=24*3600*1;
						}else if($_POST['recurrence']==2){
							$rec+=24*3600*2;
							$debutReservationrec+=24*3600*2;
							$finReservationrec+=24*3600*2;
						}else if($_POST['recurrence']==7){
							$rec+=24*3600*7;
							$debutReservationrec+=24*3600*7;
							$finReservationrec+=24*3600*7;
						}else if ($_POST['recurrence']==30){
							$rec+=24*3600*cal_days_in_month(CAL_GREGORIAN, date("m", $rec), date("Y", $rec));
							$debutReservationrec+=24*3600*cal_days_in_month(CAL_GREGORIAN, date("m", $rec), date("Y", $rec));
							$finReservationrec+=24*3600*cal_days_in_month(CAL_GREGORIAN, date("m", $rec), date("Y", $rec));
						}
						else
						{
							$diff=strtotime($_POST['dateRecurrence_'.$dateRecurrenceCount])-$rec;
							$rec+=$diff;
							$debutReservationrec+=$diff;
							$finReservationrec+=$diff;
							$dateRecurrenceCount++;
						}
						if ($rec!==false)
						{
							$actR= new Activite(NULL,$rec,$act->getNom(),$act->getDescriptif(),$act->getDuree(),$act->getLieu(),$type,$placesLim,$prix,$animateurEnCharge,$points,$mustBeReserved,$debutReservationrec,$finReservationrec,$act->getId());
							$actRC = new Controller_Activite($actR);
							if($actRC->isGood())
								$actR->saveToDb();
							else
							{
								?>
								<div class="message_block error" id='reponse_controller_msg'>
									<p><?php echo $actRC->getError(); ?></p>
								</div>
								<script type="text/javascript">
									$("#part_1, #part_2, #part_3, #part_4").show();
								</script>
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
							<script type="text/javascript">
								$("#part_1, #part_2, #part_3, #part_4").show();
							</script>
							<?php
							
							exit();
						}
					}
					?>
					<div class="message_block success" id='reponse_controller_msg'>
						<p>Les activités ont bien été <?php if(isset($_POST['id'])) { echo 'modifiées'; } else { echo 'créées'; } ?>.</p>
					</div>
					<script type="text/javascript">
						$("#part_1, #part_2, #part_3, #part_4").show();
					</script>
					<?php
				}
				else
				{
					$database=new Database();
					$database->select("activities", array("idRecurrente" => $_POST["id"]), "id");
					while ($data=$database->fetch())
					{
						if (isset($_POST["act_".$data["id"]]))
						{
							$act_rec=new Activite($data["id"]);
							if (strcmp($act_rec->getDuree(), $act->getDuree())!==0)
							{
								$act_rec->setDuree($act->getDuree());
							}
							if (strcmp($act_rec->getNom(), $act->getNom())!==0)
							{
								$act_rec->setNom($act->getNom());
							}
							if (strcmp($act_rec->getDescriptif(), $act->getDescriptif())!==0)
							{
								$act_rec->setDescriptif($act->getDescriptif());
							}
							if (strcmp($act_rec->getType(), $act->getType())!==0)
							{
								$act_rec->setType($act->getType());
							}
							if (strcmp($act_rec->getLieu(), $act->getLieu())!==0)
							{
								$act_rec->setLieu($act->getLieu());
							}
							if (strcmp($act_rec->getPoints(), $act->getPoints())!==0)
							{
								$act_rec->setPoints($act->getPoints());
							}
							if (strcmp($act_rec->getPrix(), $act->getPrix())!==0)
							{
								$act_rec->setPrix($act->getPrix());
							}
							if (strcmp($act_rec->getMustBeReserved(), $act->getMustBeReserved())!==0)
							{
								$act_rec->setMustBeReserved($act->getMustBeReserved());
							}
							if (strcmp($act_rec->getPlacesLim(), $act->getPlacesLim())!==0)
							{
								$act_rec->setPlacesLim($act->getPlacesLim());
							}
							$actRC = new Controller_Activite($act_rec);
							if($actRC->isGood())
								$act_rec->saveToDb();
							else
							{
								?>
								<div class="message_block error" id='reponse_controller_msg'>
									<p><?php echo $actRC->getError(); ?></p>
								</div>
								<script type="text/javascript">
									$("#part_1, #part_2, #part_3, #part_4").show();
								</script>
								<?php
								
								exit();
							}
						}
					}
					if (isset($act_rec))
					{
					?>
						<div class="message_block success" id='reponse_controller_msg'>
							<p>Les activités ont bien été modifiées.</p>
						</div>
						<script type="text/javascript">
							$("#part_1, #part_2, #part_3, #part_4").show();
						</script>
					<?php
					}
					else
					{
					?>
						<div class="message_block success" id='reponse_controller_msg'>
							<p>L'activité a bien été modifiée.</p>
						</div>
						<script type="text/javascript">
							$("#part_1, #part_2, #part_3, #part_4").show();
						</script>
					<?php
					}
				}
			}
			else
			{
				?>
				<div class="message_block success" id='reponse_controller_msg'>
					<p>L'activité a bien été <?php if(isset($_POST['id'])) { echo 'modifiée'; } else { echo 'créée'; } ?>.</p>
				</div>
				<script type="text/javascript">
					$("#part_1, #part_2, #part_3, #part_4").show();
				</script>
				<?php
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $actController->getError(); ?></p>
			</div>
			<script type="text/javascript">
				$("#part_1, #part_2, #part_3, #part_4").show();
			</script>
			<?php
			
		}			
	}
?>
