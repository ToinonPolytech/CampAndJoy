<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);	
	if (!$cuser->can(CAN_PAY))
	{
		exit();
	}
	if (!isset($_GET["Ref"]))
	{
		exit();
	}
	$reference=$_GET["Ref"];
	$database=new Database();
	if (!$database->count("logs_achats", array("reference" => $reference, "idUser" => $_SESSION["id"])))
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Information</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div id="retour"></div>
		<div class="section_title_wrapper">
			<h2 class="heading">Votre paiement</h2>
			<div class="section_divide"></div>
			<?php
				if ($_GET["action"]=="cancel") { $type="info"; $message="Vous avez annulé votre paiement. Vous n'avez pas été débité."; }
				if ($_GET["action"]!="cancel")
				{
					$database->select("logs_achats", array("reference" => $reference, "idUser" => $_SESSION["id"]));
					$data=$database->fetch();
					?>
					<h4 class="color_1">Détails</h4>
					<?php
						if ($data["categorie"]=="ACTIVITE")
						{
							$database->select("activities", array("id" => $data["id_categorie"]), array("time_start", "nom", "type", "mustBeReserved", "prix", "points", "duree", "description", "id", "capaciteMax"));
							$dataAct=$database->fetch();
							$nomAct=unserialize($dataAct["nom"]);
							$nom=current($nomAct);
							$descAct=unserialize($dataAct["description"]);
							$desc=current($descAct);
							if (!isset($nomAct[LANG_USER]))
							{
								if (LANG_USER!=DEFAULT_LANGUE)
								{
									if (isset($nomAct[DEFAULT_LANGUE_ETRANGER]))
									{
										$nom=$nomAct[DEFAULT_LANGUE_ETRANGER];
									}
									else if (isset($nomAct[DEFAULT_LANGUE]))
									{
										$nom=$nomAct[DEFAULT_LANGUE];
									}
								}
								else if (isset($nomAct[DEFAULT_LANGUE]))
								{
									$nom=$nomAct[DEFAULT_LANGUE];
								}
							}
							else
							{
								$nom=$nomAct[LANG_USER];
							}
							if (!isset($descAct[LANG_USER]))
							{
								if (LANG_USER!=DEFAULT_LANGUE)
								{
									if (isset($descAct[DEFAULT_LANGUE_ETRANGER]))
									{
										$desc=$descAct[DEFAULT_LANGUE_ETRANGER];
									}
									else if (isset($descAct[DEFAULT_LANGUE]))
									{
										$desc=$descAct[DEFAULT_LANGUE];
									}
								}
								else if (isset($descAct[DEFAULT_LANGUE]))
								{
									$desc=$descAct[DEFAULT_LANGUE];
								}
							}
							else
							{
								$desc=$descAct[LANG_USER];
							}
							$capacite=$dataAct["capaciteMax"];
							if ($capacite>0)
							{
								$database->select("reservation", array("type" => "ACTIVITE", "id" => $dataAct["id"]), "nbrPersonne");
								while ($d=$database->fetch()) { $capacite-=$d["nbrPersonne"]; }
							}
							?>
							<h4>Réservation de l'activité qui se déroulera le <?php echo date("d/m/Y", $dataAct["time_start"]); ?></h4>
							<div class="activity">
								<a class="program_link" href="#"><?php echo htmlspecialchars($nom); ?></a>
								<div class="activity_duration">Heure de fin : <?php echo date("H:i", $dataAct["time_start"]+$dataAct["duree"]*60); ?></div>
								<div class="restaurant_place_and_cost"><span class="place_available"><?php if ($capacite>0) { ?><strong><?php echo $capacite; ?>/<?php echo $dataAct["capaciteMax"]; ?></strong> places <?php if ($dataAct["time_start"]<time()) { echo "étaient "; } ?>disponibles <?php } else { ?>Pas de limites de places<?php } ?></span>&nbsp;</div>
								<div class="activity_tag w-clearfix">
									<div class="activity_tag_title">Tags :</div>
									<?php
										foreach (explode(" ", $dataAct["type"]) as $tag)
										{
											
											?>
												<a class="tags_name" href="#"><?php echo htmlentities($tag); ?></a>
											<?php
											
										}
										if ($dataAct["mustBeReserved"]==1)
										{
											?>
											<a class="tags_name" href="#">RÉSERVABLE</a>
											<?php
										}
										if ($dataAct["points"]>0)
										{
											?>
											<a class="tags_name" href="#">POINTS</a>
											<?php
										}
									?>
									<a class="tags_name" href="#"><?php if ($dataAct["prix"]>0) { echo 'PAYANTE'; } else { echo 'GRATUITE'; } ?></a>
								</div>
								<p class="p_program"><?php echo htmlentities($desc); ?></p>
							</div>
							<?php
						}
					?>
					Date de la commande : <?php echo date("d/m/Y H:i:s", $data["timestamp"]); ?><br/>
					Montant : <?php echo ($data["montant"]/100)."€"; ?><br/>
					<?php
					if ($data["timestamp_effectue"]>0)
					{
						?>
						Dernière vérification : <?php echo date("d/m/Y H:i:s", $data["timestamp_effectue"]); ?><br/> 
						<?php
					}
					if ($data["type_paiement"]!=NULL && $data["type_carte"]!=NULL)
					{
						?>
						Moyen de paiement utilisé : <?php echo $data["type_paiement"]." - ".$data["type_carte"]; ?><br/> 
						<?php
					}
					if ($data["statut"]=="EN_ATTENTE")
					{
						$type="info";
						$message="Votre paiement est en attente. Votre réservation sera validée dès confirmation de celui-ci. <br/>";
						if (!empty($data["erreur"]))
						{
							$message.="Raison : ".$data["erreur"];
						}
					}
					else if ($data["statut"]=="ACCEPTED")
					{
						$type="success";
						$message="Votre paiement a bien été validé. Votre réservation est d'ores et déjà pris en compte.<br/> Vous pouvez accéder à la réservation d'<a class='action_link' href='/demo/".LANG_USER."/compte'>ici</a>.";
					}
					else if ($data["statut"]=="REFUSE")
					{
						if ($_GET["action"]=="done")
						{
							$type="info";
							$message="Votre paiement est en attente. Votre réservation sera pris en compte dès confirmation de celui-ci.";
						}
						else
						{
							$type="error";
							$message="Votre paiement n'a pas été accepté. Vous n'avez pas été débité. <br/> Message : ".$data["erreur"];
						}
					}
				}
			?>
			<div class="<?php echo $type; ?> message_block">
				<p><?php echo $message; ?></p>
			</div>
		</div>
	</div>
</div>