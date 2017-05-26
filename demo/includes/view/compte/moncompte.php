<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,activities.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if ($includeDone)
	{
		?>
		<script type="text/javascript">
			$( document ).ready(function() { Webflow.require('tabs').redraw(); });
		</script>
		<?php
	}
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Gestion du compte</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="custom_table">
			<div class="w-tabs" data-duration-in="300" data-duration-out="100">
				<div class="tabs-menu w-tab-menu">
					<a class="tab_link w--current w-inline-block w-tab-link" data-w-tab="Tab 1">
						<div class="tab_button_title">Vos données</div>
					</a>
					<?php
					if ($cuser->can(CAN_CREATE_SUBACCOUNT))
					{
						?>
						<a class="tab_link w-inline-block w-tab-link" data-w-tab="Tab 2">
							<div class="tab_button_title">Comptes affiliés</div>
						</a>
						<?php
					}
					?>
					<?php
					if ($cuser->can(CAN_CREATE_ACTIVITIES))
					{
						?>
						<a class="tab_link w-inline-block w-tab-link" data-w-tab="Tab 3">
							<div class="tab_button_title">Mes activités</div>
						</a>
						<?php
					}
					?>
					<a class="tab_link w-inline-block w-tab-link" data-w-tab="Tab 4">
						<div class="tab_button_title">Votre historique</div>
					</a>
				</div>
				<div class="w-tab-content">
					<div class="tab-pane w--tab-active w-tab-pane" data-w-tab="Tab 1">
						<ul class="program_list w-list-unstyled">
							<p class="h1_color color_2">
								Date d'arrivée : <?php if ($user->getUserInfos()->getTimeArrive()>0) { echo date("d/m/Y", $user->getUserInfos()->getTimeArrive()); } else { echo "Non spécifié"; }?><br/>
								Date de départ : <?php if ($user->getUserInfos()->getTimeDepart()>0) { echo date("d/m/Y", $user->getUserInfos()->getTimeDepart()); } else { echo "Non spécifié"; }?><br/>
								Vous êtes situé à l'emplacement : <?php echo $user->getUserInfos()->getEmplacement(); ?><br/>
							</p>
							<?php
								if ($database->count("reservation", array("idUser" => $_SESSION["id"])))
								{
									?>
									<h4 class="color_1">Vos Réservations</h4>
									<div class="custom_table" id="reservation_table" name="reservation_table">
										<div class="custom_table_head">
											<div class="ctl_head_line_color custom_table_line">
												<div class="custom_table_col">
													<h6 class="head_text">Nom</h6>
												</div>
												<div class="custom_table_col">
													<h6 class="head_text">Date</h6>
												</div>
												<div class="custom_table_col">
													<h6 class="head_text">Places</h6>
												</div>
												<div class="custom_table_col">
													<h6 class="head_text">Options</h6>
												</div>
											</div>
										</div>
										<div class="custom_table_body">
											<?php
												$database->setOrderCol("idAuto");
												$database->setLimit(20);
												$database->setDesc();
												$database->select("reservation", array("idUser" => $user->getId()));
												$database2=new Database();
												while ($data=$database->fetch())
												{
													$can_edit=false;
													$can_cancel=false;
												?>
												<div class="ctl_body_line custom_table_line" id="res_<?php echo $data["id"]; ?>" name="res_<?php echo $data["id"]; ?>">
													<div class="custom_table_col">
														<div>
														<?php
														$nbrPlaces=$data["nbrPersonne"];
														if ($data["type"]=="ACTIVITE")
														{
															$act=new Activite($data["id"]);
															if ($data["time"]>=time() && $cuser->can(CAN_RESERVE_ACTIVITIES) && $act->getPrix()==0 && $act->getFinReservation()>=time())
															{
																$can_edit=true;
																$can_cancel=true;
															}
															echo "Activité : ".htmlentities($act->getNomByLang());
														}
														else if ($data["type"]=="RESTAURANT")
														{
															if ($data["time"]>=time()+24*3600*1 && $cuser->can(CAN_RESERVE_RESTAURANT))
															{
																$can_edit=true;
																$can_cancel=true;
															}
															echo "Restaurant : ".htmlentities($database2->getValue("restaurant", array("id" => $data["id"]), "nom"));
														}
														else if($data["type"]=="LIEU_COMMUN")
														{
															$nbrPlaces="N.A";
															if ($data["time"]>=time() && $cuser->can(CAN_RESERVE_LIEU_COMMUN))
															{
																$can_cancel=true;
																$can_edit=false;
															}
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
														<div><?php echo date("d/m/Y", $data["time"]); ?></div>
													</div>
													<div class="custom_table_col">
														<div><?php echo htmlentities($nbrPlaces); ?></div>
													</div>
													<div class="custom_table_col">
														<div>
															<?php
															$options=false;
															if ($data["valide"])
															{
																if ($can_cancel)
																{
																	$options=true;
																	?>
																	<a class="action_link" href="#" data-on-confirm="webflowCampandJoy('close-modal-msg'); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('supprimerReservation.controllerForm.php')); ?>', {id : <?php echo $data["id"]; ?>, type : '<?php echo $data["type"]; ?>', time : <?php echo $data["time"]; ?>, from : 'myaccount'}, '#reservation_table', 'before');" data-ix="show-modal-msg" data-title="Annuler une réservation" data-message="Êtes vous certains d'annuler la réservation ?" data-on-refuse="webflowCampandJoy('close-modal-msg');" data-type="error">[Annuler]</a>
																	<?php
																}
																if ($can_edit)
																{
																	$options=true;
																	?>
																	<a class="action_link" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('modifierReservationForm.php')); ?>', {id : <?php echo $data["id"]; ?>, type : '<?php echo $data["type"]; ?>', time : <?php echo $data["time"]; ?>, from : 'myaccount'}, '#res_<?php echo $data["id"]; ?>', 'before', false, function(){ $('#res_<?php echo $data["id"]; ?>').toggle(); }); return false;">[Modifier]</a>
																	<?php
																}
															}
															else if ($can_cancel || $can_edit)
															{
																?>
																En cours de validation
																<?php
															}
															else if (!$options)
															{
																?>
																Pas d'option disponible
																<?php
															}
															?>
														</div>
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
									<div class="info message_block">
										<p>Vous n'avez effectué aucune réservation pour le moment. Vite venez voir nos <a href="/demo/<?php echo LANG_USER; ?>/planning">activités</a> et la listes des <a href="/demo/<?php echo LANG_USER; ?>/service">services</a> que nous proposons.</p>
									</div>
									<?php
								}
								?>
						</ul>
					</div>
					<?php
					if ($cuser->can(CAN_CREATE_SUBACCOUNT))
					{
						?>
						<div class="tab-pane w-tab-pane" data-w-tab="Tab 2">
							<ul class="program_list w-list-unstyled">
								<div class="info message_block">
									<p>Un compte affilié, est un compte destiné aux personnes vivant avec vous sur notre camping. Vous pouvez configurer facilement les paramètres de ces derniers. <br/>
									Cela est en effet très pratique pour que vos enfants est accès à notre service tout en limitant leurs actions.</p>
								</div>
								<?php
									$nombreComptesAffilies=$database->count("users", array("infoId" => $user->getUserInfos()->getId()));
									if ($user->getUserInfos()->getComptesMax()>$nombreComptesAffilies)
									{ 
									?>
										<a class="btn_set_aside w-button" href="/demo/<?php echo LANG_USER; ?>/compte/ajout">Rajouter un sous-compte</a><br/>
									<?php 
									}
								?>
								<div class="customcaj_separator"></div>
								<?php
									if ($nombreComptesAffilies==1)
									{
										?>
										<div class="error message_block">
											<p>Vous n'avez aucun compte affilié pour le moment.</p>
										</div>
										<?php
									}
									else
									{
										$database->select("users", array("infoId" => $user->getUserInfos()->getId(), "clef" => array("!=", $user->getUserInfos()->getClef())), array("clef", "prenom", "nom", "id"));
										?>
										<div class="custom_table">
											<div class="custom_table_head">
												<div class="ctl_head_line_color custom_table_line">
													<div class="custom_table_col">
														<h6 class="head_text">Identifiant</h6>
													</div>
													<div class="custom_table_col">
														<h6 class="head_text">Personne</h6>
													</div>
													<div class="custom_table_col">
														<h6 class="head_text">Options</h6>
													</div>
												</div>
											</div>
											<div class="custom_table_body">
											<?php
												while ($data=$database->fetch())
												{
												?>
													<div class="ctl_body_line custom_table_line">
														<div class="custom_table_col">
															<div><?php echo htmlentities($data["clef"]); ?></div>
														</div>
														<div class="custom_table_col">
															<div><?php echo htmlentities($data["prenom"]." ".$data["nom"]); ?></div>
														</div>
														<div class="custom_table_col">
															<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/compte/modifier/<?php echo $data["id"]; ?>">[Modifier]</a>
														</div>
													</div>	
												<?php
												}
												?>
												</div>
										</div>
										<?php
									}
								?>
							</ul>
						</div>
						<?php
					}
					?>
					<?php
					if ($cuser->can(CAN_CREATE_ACTIVITIES))
					{
					?>
					<div class="tab-pane w-tab-pane" data-w-tab="Tab 3">
						<ul class="program_list w-list-unstyled">
							<a class="btn_set_aside w-button" href="/demo/<?php echo LANG_USER; ?>/activites/ajout">Proposer une activité</a><br/>
							<?php
								$database->setOrderCol("time_start");
								$database->setDesc();
								$database->setStart(NULL);
								$database->setLimit(NULL);
								if ($database->count("activities", array("idDirigeant" => $_SESSION["id"])))
								{
									$database->select("activities", array("idDirigeant" => $_SESSION["id"]));
									?>
									<div class="custom_table">
										<div class="custom_table_head">
											<div class="ctl_head_line_color custom_table_line">
												<div class="custom_table_col">
													<h6 class="head_text">Nom</h6>
												</div>
												<div class="custom_table_col">
													<h6 class="head_text">Date</h6>
												</div>
												<div class="custom_table_col">
													<h6 class="head_text">Nombre d'inscrits</h6>
												</div>
												<div class="custom_table_col">
													<h6 class="head_text">Options</h6>
												</div>
											</div>
										</div>
										<div class="custom_table_body">
										<?php
											while ($data=$database->fetch())
											{
												$database2->select("reservation", array("id" => $data["id"], "type" => 'ACTIVITE'), "SUM(nbrPersonne) AS nbrInscrits");
												$d=$database2->fetch();
												$inscrits=$d["nbrInscrits"]+0;
												$nomAct=unserialize($data["nom"]);
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
											?>
												<div class="ctl_body_line custom_table_line">
													<div class="custom_table_col">
														<div><?php echo htmlentities($nom); ?></div>
													</div>
													<div class="custom_table_col">
														<div><?php echo date("d/m/Y H:i", $data["time_start"]); ?></div>
													</div>
													<div class="custom_table_col">
														<div><?php echo htmlentities($inscrits); ?></div>
													</div>
													<div class="custom_table_col">
														<?php 
														if ($inscrits==0 && $data["time_start"]>time())
														{
															?>
															<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/activites/modifier/<?php echo $data["id"]; ?>">[Modifier]</a>
															<?php
														}
														?>
														<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/reservation/ACTIVITE/<?php echo $data["id"]; ?>">[Voir]</a>
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
									<div class="error message_block">
										<p>N'hésitez pas à proposer une activité.</p>
									</div>
									<?php
								}
							?>
						</ul>
					</div>
					<?php
					}
					?>
					<div class="tab-pane w-tab-pane" data-w-tab="Tab 4">
						<ul class="program_list w-list-unstyled">
							<div class="custom_table">
								<div class="custom_table_head">
									<div class="ctl_head_line_color custom_table_line">
										<div class="custom_table_col">
											<h6 class="head_text">Montant</h6>
										</div>
										<div class="custom_table_col">
											<h6 class="head_text">Catégorie</h6>
										</div>
										<div class="custom_table_col">
											<h6 class="head_text">État</h6>
										</div>
										<div class="custom_table_col">
											<h6 class="head_text">Date</h6>
										</div>
										<div class="custom_table_col">
											<h6 class="head_text">Options</h6>
										</div>
									</div>
								</div>
								<div class="custom_table_body">
								<?php
									$database->setOrderCol("timestamp");
									$database->setDesc();
									$database->select("logs_achats", array("idUser" => $_SESSION["id"]), array("categorie", "id_categorie", "reference", "timestamp", "statut", "montant"));
									while ($data=$database->fetch())
									{
									?>
										<div class="ctl_body_line custom_table_line">
											<div class="custom_table_col">
												<div><?php echo ($data["montant"]/100)."€"; ?></div>
											</div>
											<div class="custom_table_col">
												<div><?php if ($data["categorie"]=="ACTIVITE") { echo "Activité"; } else { echo $data["categorie"]; } ?></div>
											</div>
											<div class="custom_table_col">
												<div><?php if ($data["statut"]=="ACCEPTED") { echo "<b>Accepté</b>"; } elseif ($data["statut"]=="EN_ATTENTE") { echo "<b>En attente</b>"; } else { echo "<b>Refusé</b>"; } ?></div>
											</div>
											<div class="custom_table_col">
												<div><?php echo date("d/m/Y H:i:s", $data["timestamp"]); ?></div>
											</div>
											<div class="custom_table_col">
												<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/paiement/<?php if ($data["statut"]=="ACCEPTED") { echo "done"; } elseif ($data["statut"]=="EN_ATTENTE") { echo "done"; } else { echo "refuse"; } ?>?Ref=<?php echo $data["reference"]; ?>">[Plus d'infos]</a>
											</div>
										</div>	
									<?php
									}
									?>
									</div>
							</div>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
    $(".page_name").html("Mon compte");
});
</script>