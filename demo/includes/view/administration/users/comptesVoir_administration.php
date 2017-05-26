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
	$database=new Database();
	$database2=new Database();
	if(!isStaff())
	{
		exit();
	}
	if (!isset($_GET["id"]) || !$database->count("users", array("id" => $_GET["id"])))
	{
		exit();
	}
	$user_see=new User($_GET["id"]);
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Les comptes</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading"><?php echo htmlentities($user_see->getPrenom()." ".$user_see->getNom()); ?></h2>
			<a class="primary_btn w-button heading" href="/demo/administration/compte/modifier/<?php echo $user_see->getId(); ?>">Modifier</a>
			<a class="primary_btn w-button heading" href="#" onclick="alert('TO DO');">Contacter</a>
			<div class="section_divide"></div>
			<?php
				if ($user_see->getClef()!=$user_see->getUserInfos()->getClef())
				{
					$database->select("users", array("clef" => $user_see->getUserInfos()->getClef()), array("nom", "prenom", "id"));
					$data=$database->fetch();
					?>
					<div class="message_block info" id='reponse_controller_msg'>
						<p>Ce compte, est un compte affilié au compte de : <a href="/demo/administration/compte/voir/<?php echo $data["id"]; ?>"><?php echo htmlentities($data["prenom"]." ".$data["nom"]); ?></a></p>
					</div>
					<?php
				}
			?>
			<br/>
			<p class="h1_color color_2">
				Date d'arrivée : <?php if ($user_see->getUserInfos()->getTimeArrive()>0) { echo date("d/m/Y", $user_see->getUserInfos()->getTimeArrive()); } else { echo "Non spécifié"; }?><br/>
				Date de départ : <?php if ($user_see->getUserInfos()->getTimeDepart()>0) { echo date("d/m/Y", $user_see->getUserInfos()->getTimeDepart()); } else { echo "Non spécifié"; }?><br/>
			</p>
			<div class="row w-row">
				<div class="w-col w-col-6">
					<h4 class="color_1">5 Dernières réservations</h4>
					<?php
					if ($database->count("reservation", array("idUser" => $user_see->getId())))
					{
						?>
						<div class="custom_table">
							<div class="custom_table_head">
								<div class="ctl_head_line_color custom_table_line">
									<div class="custom_table_col">
										<h6 class="head_text">Date</h6>
									</div>
									<div class="custom_table_col">
										<h6 class="head_text">Nom</h6>
									</div>
									<div class="custom_table_col">
										<h6 class="head_text">Options</h6>
									</div>
								</div>
							</div>
							<div class="custom_table_body">
								<?php
									$database->setOrderCol("idAuto");
									$database->setLimit(5);
									$database->setDesc();
									$database->select("reservation", array("idUser" => $user_see->getId()));
									while ($data=$database->fetch())
									{
									?>
									<div class="ctl_body_line custom_table_line">
										<div class="custom_table_col">
											<div><?php echo date("d/m/Y", $data["time"]); ?></div>
										</div>
										<div class="custom_table_col">
											<div>
											<?php
											if ($data["type"]=="ACTIVITE")
											{
												$nomAct=unserialize($database2->getValue("activities", array("id" => $data["id"]), "nom"));
												$nom=current($nomAct);
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
												echo "Activité : ".htmlentities($nom);
											}
											else if ($data["type"]=="RESTAURANT")
											{
												echo "Restaurant : ".htmlentities($database2->getValue("restaurant", array("id" => $data["id"]), "nom"));
											}
											else if($data["type"]=="LIEU")
											{
												echo "Lieu : ".htmlentities($database2->getValue("lieu_commun", array("id" => $data["id"]), "nom"));
											}
											else if ($data["type"]=="ETAT_LIEUX")
											{
												echo "État des Lieux : ".htmlentities($database2->getValue("users", array("id" => $data["id"]), "prenom")." ".$database2->getValue("users", array("id" => $data["id"]), "nom"));
											}
											?>
											</div>
										</div>
										<div class="custom_table_col">
											<div><a class="action_link" href="/demo/administration/reservation/<?php echo $data["type"]; ?>/<?php echo $data["id"]; ?>">[Voir]</a></div>
										</div>
									</div>
									<?php
									}
									$database->setOrderCol(NULL);
								?>
							</div>
						</div>
						<?php
					}
					else
					{
						?>
						<div class="message_block info" id='reponse_controller_msg'>
							<p>Aucune réservation n'a été effectué par cet utilisateur.</p>
						</div>
						<?php
					}
					?>
				</div>
				<div class="w-col w-col-6">
					<h4 class="color_1">5 Derniers problèmes signalés</h4>
					<?php
					if ($database->count("problemes_technique", array("idUsers" => $user_see->getId())))
					{
						?>
						<div class="custom_table">
							<div class="custom_table_head">
								<div class="ctl_head_line_color custom_table_line">
									<div class="custom_table_col">
										<h6 class="head_text">Date Signalement</h6>
									</div>
									<div class="custom_table_col">
										<h6 class="head_text">État</h6>
									</div>
									<div class="custom_table_col">
										<h6 class="head_text">Options</h6>
									</div>
								</div>
							</div>
							<div class="custom_table_body">
								<?php
									$database->setOrderCol("id");
									$database->setLimit(5);
									$database->setDesc();
									$database->select("problemes_technique", array("idUsers" => $user_see->getId()));
									while ($data=$database->fetch())
									{
									?>
									<div class="ctl_body_line custom_table_line">
										<div class="custom_table_col">
											<div><?php echo date("d/m/Y H:i", $data["time_start"]); ?></div>
										</div>
										<div class="custom_table_col">
											<div>
											<?php
												if ($data["solved"]=="NON_RESOLU")
												{
													echo "<b style='color:red;'>Non résolu</b>";
												}
												else if ($data["solved"]=="EN_COURS")
												{
													echo "<b style='color:orange;'>En cours</b>";
												}
												else if ($data["solved"]=="RESOLU")
												{
													echo "<b style='color:green;'>Résolu</b>";
												}
											?>
											</div>
										</div>
										<div class="custom_table_col">
											<div><a class="action_link" href="/demo/problemeTechnique/vue/<?php echo $data["id"]; ?>">[Gérer]</a></div>
										</div>
									</div>
									<?php
									}
								?>
							</div>
						</div>
						<?php
					}
					else
					{
						?>
						<div class="message_block info" id='reponse_controller_msg'>
							<p>Aucun problème technique n'a été signalé par cet utilisateur.</p>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<br/><br/>
		</div>
	</div>
</div>