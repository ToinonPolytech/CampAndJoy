<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
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
	$dateUrl="";
	$dateGet="";
	$timestamp=time();
	if (isset($_GET["date"]))
	{
		$timestamp=strtotime($_GET["date"]);
		if ($timestamp===false)
			$timestamp=time();
		else
		{
			$dateUrl=date("d", $timestamp)."/".date("m", $timestamp)."/".date("y", $timestamp)."/";
			$dateGet="date=".$_GET["date"]."&";
		}
	}
	$x = date("w", $timestamp)-6;
	if ($x<0)
		$x+=7;
	
	$timeDeb = mktime(0,0,0,date("n", $timestamp),date("d", $timestamp)-($x)%7,date("y", $timestamp));
	$timeFin = $timeDeb+6*24*3600;
	$timePrevWeek=$timeDeb-7*24*3600;
	$timeNextWeek=$timeFin+1*24*3600;
	
	if (date("I", $timeDeb)!=date("I", $timePrevWeek)) // Heure d'été ?!
	{
		$timePrevWeek+=1*3600;
	}
	$filtersUrl="";
	$filtersGet="";
	$array_tags=array();
	if (isset($_GET["onlyCamping"]))
	{
		if ($_GET["onlyCamping"]==1)
		{
			$filtersUrl="/1";
			$filtersGet.="&onlyCamping=1";
		}
		else
		{
			$filtersUrl="/0";
			$filtersGet="&onlyCamping=0";
		}
	}
	if (isset($_GET["tags"]))
	{
		$array_tags=explode(",", $_GET["tags"]);
		$tags_add=false;
		foreach ($array_tags as $tag)
		{
			if (in_array($tag, $listeTypes))
			{
				if (!$tags_add)
				{
					$tags_add=true;
					$filtersUrl.="/";
					$filtersGet.="&tags=";
				}
				else
				{
					$filtersUrl.=",";
					$filtersGet.=",";
				}
				$filtersUrl.=$tag;
				$filtersGet.=$tag;
			}
		}
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Planning du camping</h1>
</div>
<div class="hero_section">
	<div class="w-container" id="actContainer">
		<div class="section_title_wrapper">
			<h2 class="heading">NOTRE PROGRAMME</h2>
			<div class="section_divide"></div>
			<div class="filter_title" style="display:none;">Filtres de recherche :</div>
			<div <?php if ($filtersUrl==""){ ?>style="display:none;"<?php } ?>>
				<div class="w-form">
					<form class="horizontal_form" id="formFilters" name="formFilters">
						<div class="activity_tag custom_tag1 w-clearfix">
							<div class="activity_tag_title">Tags :</div>
							<?php
								foreach ($listeTypes as $tag)
								{
									?>
									<a class="tags_name <?php if (in_array($tag, $array_tags)) { echo 'tags_name_checked'; } ?>" href="#"><?php echo $tag; ?></a>
									<?php
								}
							?>
							<div class="required_checkbox w-checkbox">
								<label class="control control--checkbox">Rechercher uniquement les activités proposés par le camping
									<input type="checkbox" id="onlyCamping" name="onlyCamping" <?php if ($filtersUrl=="" || $_GET["onlyCamping"]==1) { echo 'checked'; } ?>>
                                    <div class="control__indicator"></div>
                                </label>
							</div>
						</div><a class="primary_btn w-button" href="#" onclick="activitesSearch('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('activitesCamping.php')); ?>'); return false;">Lancer la recherche</a>
					</form>	
				</div>
			</div>
			<?php
				if (!isStaff())
				{
					?>
					<h4>Le saviez-vous ?</h4>
					<p>Vous pouvez vous aussi <a href="/demo/<?php echo LANG_USER; ?>/activites/ajout">Proposer votre activité</a> au reste du camping !<br/></p>
					<?php
				}
			?>
			<p class="paragraph">Consultez le planning des activités conçu par notre équipe. Vous pouvez à partir d'ici réserver ou contacter l'animateur en charge de l'activité pour plus d'information.&nbsp;</p>
		</div>
		<div class="custom_table">
			<div style="float:left;" class="week_text"><a href="#" class="emn_link" onclick='if ($(".filter_title").css("display")=="none") { $(this).html("- Filtres"); } else { $(this).html("+ Filtres"); } $(".filter_title").toggle("medium"); $(".filter_title").next("div").toggle("medium");  return false;'><?php if ($filtersUrl==""){ echo '+'; } else { echo '-'; } ?> Filtres</a></div>
			<div class="week_nav">
				<div class="week_text"><?php echo getMonthFromNumber(date("n", $timeDeb)); ?> <?php echo date("Y", $timeDeb); ?> - Du <?php echo date("d", $timeDeb)." au ".date("d", $timeFin); ?></div>
				<div class="prev_arrow" onclick="loadToMain('/demo/<?php echo LANG_USER; ?>/planning/<?php echo date("d", $timePrevWeek); ?>/<?php echo date("m", $timePrevWeek); ?>/<?php echo date("y", $timePrevWeek); ?><?php echo $filtersUrl; ?>', {});"></div>
				<div class="next_arrow" onclick="loadToMain('/demo/<?php echo LANG_USER; ?>/planning/<?php echo date("d", $timeNextWeek); ?>/<?php echo date("m", $timeNextWeek); ?>/<?php echo date("y", $timeNextWeek); ?><?php echo $filtersUrl; ?>', {});"></div>
			</div>
			<div class="w-tabs" data-duration-in="300" data-duration-out="100">
				<div class="tabs-menu w-tab-menu">
					<?php
						for ($i=0;$i<7;$i++)
						{
							$timeTemp=$timeDeb+$i*24*3600;
							$rajout=true;
							if ($timeDeb<time() && $timeFin>time())
								$rajout=false;
							?>
							<a class="tab_link w-inline-block w-tab-link" data-w-tab="Tab <?php echo $i+1; ?>">
								<div class="tab_button_title"><?php echo getDayFromNumber(date("w", $timeTemp)); ?></div>
								<div class="tab_link_subtitle"><?php echo date("d", $timeTemp); ?></div>
								<?php if ($timeTemp<=time() && $timeTemp+24*3600>=time()) { ?><div class="notification_new_event"></div> <?php } ?>
							</a>
							<?php
						}
					?>
				</div>
				<div class="w-tab-content">
					<?php
						$db = new Database();
						for ($i=0;$i<7;$i++)
						{
							$timeTemp=$timeDeb+$i*24*3600;
							?>
							<div class="tab-pane w-tab-pane" data-w-tab="Tab <?php echo $i+1; ?>">
								<ul class="program_list w-list-unstyled">
									<?php
										if ($filtersUrl=="" || $_GET["onlyCamping"]==1)
											$db->selectJoin("activities AS a", array(" users AS u ON u.id=a.idDirigeant"), array("time_start" => array($timeTemp, $timeTemp+3600*24), "access_level" => array("!=", "CLIENT")),array("time_start", "a.nom", "a.type", "a.mustBeReserved", "a.prix", "a.points", "a.duree", "a.description", "u.nom AS unom", "u.prenom", "u.id AS uid", "a.id", "a.capaciteMax", "debutReservation", "finReservation"));
										else
											$db->selectJoin("activities AS a", array(" users AS u ON u.id=a.idDirigeant"), array("time_start" => array($timeTemp, $timeTemp+3600*24)),array("time_start", "a.nom", "a.type", "a.mustBeReserved", "a.prix", "a.points", "a.duree", "a.description", "u.nom AS unom", "u.prenom", "u.id AS uid", "a.id", "a.capaciteMax", "debutReservation", "finReservation"));
										
										$activitesArray=array();
										while ($data=$db->fetch())
										{
											if (!isset($activitesArray[$data["time_start"]]))
												$activitesArray[$data["time_start"]]=array();
											
											
											$show=true;
											if (isset($_GET["tags"]))
											{
												$dataTag=explode(" ", $data["type"]);
												foreach ($array_tags as $tag)
												{
													if (!in_array($tag, $dataTag))
													{
														$show=false;
													}
												}
											}
											if ($show)
												$activitesArray[$data["time_start"]][]=$data;
										}
										$act_found=false;
										foreach ($activitesArray as $array)
										{
											if (!empty($array))
											{
												$act_found=true;
												?>
												<li class="program_list_item">
												<?php
													foreach ($array as $data)
													{
														$nomAct=unserialize($data["nom"]);
														$descAct=unserialize($data["description"]);
														$nom=current($nomAct);
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
														$capacite=$data["capaciteMax"];
														if ($capacite>0)
														{
															$db->select("reservation", array("type" => "ACTIVITE", "id" => $data["id"]), "nbrPersonne");
															while ($d=$db->fetch()) { $capacite-=$d["nbrPersonne"]; }
														}
														?>
														<div class="activity_<?php echo $data["id"]; ?>">
															<a class="program_link" href="#"><?php echo htmlentities($nom); ?></a>
															<div class="activity_duration">Heure de fin : <?php echo date("H:i", $data["time_start"]+$data["duree"]*60); ?></div>
															<?php if ($data["time_start"]>=time()) { ?><div class="restaurant_place_and_cost"><span class="place_available"><?php if ($capacite>0) { ?><strong><?php echo $capacite; ?></strong> places disponibles <?php } else if ($data["capaciteMax"]>0) { ?><strong style="color:red;">Complet</strong><?php } else { ?>Pas de limites de places<?php } ?></span>&nbsp;</div><?php } ?>
															<div class="activity_tag w-clearfix">
																<div class="activity_tag_title">Tags :</div>
																<?php
																	foreach (explode(" ", $data["type"]) as $tag)
																	{
																		
																		?>
																			<a class="tags_name" href="#"><?php echo htmlentities($tag); ?></a>
																		<?php
																		
																	}
																	$can_reserved=false;
																	if ($data["mustBeReserved"]==1)
																	{
																		?>
																		<a class="tags_name" href="#">RÉSERVABLE</a>
																		<?php
																		$can_reserved=($data["debutReservation"]<=time() && $data["finReservation"]>=time() && $cuser->can(CAN_RESERVE_ACTIVITIES) && $db->count("reservation", array("id" => $data["id"], "type" => "ACTIVITE", "idUser" => $_SESSION["id"]))==0) ? true : false;
																		// Il peut réserver d'après ses droits et il n'a pas déjà reservé cette activité !
																	}
																	if ($data["points"]>0)
																	{
																		?>
																		<a class="tags_name" href="#">POINTS</a>
																		<?php
																	}
																?>
																<a class="tags_name" href="#"><?php if ($data["prix"]>0) { echo 'PAYANTE'; } else { echo 'GRATUITE'; } ?></a>
															</div>
															<p class="p_program"><?php echo htmlentities($desc); ?></p>
															<div class="program_event_master_block" id="<?php echo $data["id"]; ?>">
																<div class="organizer_block w-clearfix">
																	<img class="image-2" src="images/mathiew.svg">
																	<div class="event_master_name">
																		<a href="/demo/<?php echo LANG_USER; ?>/profil/<?php echo $data["uid"]; ?>" class="emn_link"><?php echo $data["unom"]." ".$data["prenom"]; ?></a>
																	</div>
																	<a class="link_contact_organizer" onclick="contact(<?php echo $data["uid"]; ?>); return false;">Contacter l'organisateur</a>
																</div>
																<?php
																	if (isStaff() || $data["uid"]==$_SESSION["id"])
																	{
																		if ($cuser->can(CAN_CREATE_ACTIVITIES) && ((isStaff() && ($staffCanEditOther || $data["uid"]==$_SESSION["id"])) || (!isStaff() && $data["uid"]==$_SESSION["id"])))
																		{
																			?>
																			<a class="primary_btn w-button" href="/demo/<?php echo LANG_USER; ?>/activites/modifier/<?php echo $data["id"]; ?>">Modifier l'activité</a>
																			<a class="primary_btn w-button" href="/demo/<?php echo LANG_USER; ?>/reservation/ACTIVITE/<?php echo $data["id"]; ?>">Voir les réservations</a>
																			<?php
																		}
																	}
																?>
																<?php 
																	if ($data["uid"]!=$_SESSION["id"])
																	{
																		if ($can_reserved)
																		{ 
																		?>
																			<a class="btn_set_aside w-button" href="#" onclick="animationTest('#<?php echo $data["id"]; ?>'); return false;">Réserver</a>
																		<?php
																		} 
																		elseif ($data["debutReservation"]<=time() && $data["finReservation"]>=time() && $data["mustBeReserved"]==1 && $cuser->can(CAN_RESERVE_ACTIVITIES) && $data["prix"]<=0)
																		{ 
																		?>
																			<a class="btn_set_aside w-button" id="modif<?php echo $data["id"]; ?>" name="modif<?php echo $data["id"]; ?>" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('modifierReservationForm.php')); ?>', {id : <?php echo $data["id"]; ?>, type : 'ACTIVITE'}, '#<?php echo $data["id"]; ?>', 'append'); return false;">Modifier ma Réservation</a>
																			<a class="btn_set_aside w-button" id="cancel<?php echo $data["id"]; ?>" name="cancel<?php echo $data["id"]; ?>" style="color:red;" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('supprimerReservation.controllerForm.php')); ?>', {id : <?php echo $data["id"]; ?>, type : 'ACTIVITE'}, '#cancel<?php echo $data["id"]; ?>', 'after'); return false;">Annuler ma Réservation</a>
																		<?php
																		} 
																	}
																?>
															</div>
															<?php 
															if ($can_reserved && $data["uid"]!=$_SESSION["id"]) 
															{ 
																?>
																<div class="program_event_master_block" id="<?php echo $data["id"]; ?>_res" style="display:none;">
																	<div class="organizer_block w-clearfix horizontal_form">
																	<input class="campandjoy_input input_width_1 w-input" type="number" min="1" <?php if ($capacite>0) { echo 'max="'.$capacite.'"'; }  ?> placeholder="Personnes ?" name="nbrReservation<?php echo $data["id"]; ?>" id="nbrReservation<?php echo $data["id"]; ?>" value="1" <?php if ($data["prix"]>0) { ?>onchange="$('#prix_res_<?php echo $data["id"]; ?>').html($(this).val()*<?php echo $data["prix"]; ?>);" <?php } ?> onkeypress="checkGoodInput(event);"/> personne(s)
																	</div>
																	<?php 
																	if ($data["prix"]>0)
																	{
																		?>
																		<a class="btn_set_aside w-button" href="#" onclick="animationPaiement(<?php echo $data["id"]; ?>, $('#nbrReservation<?php echo $data["id"]; ?>').val()); return false;">Finaliser (<span id="prix_res_<?php echo $data["id"]; ?>"><?php echo $data["prix"]; ?></span>€)</a>
																		<?php
																	}
																	else
																	{
																		?>
																		<a class="btn_set_aside w-button" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('reservation.controllerForm.php')); ?>', {id : <?php echo $data["id"]; ?>, type : 'ACTIVITE', nbrPersonnes : $('#nbrReservation<?php echo $data["id"]; ?>').val()}, '#<?php echo $data["id"]; ?>_res', 'prepend'); return false;">Finaliser</a>
																		<?php
																	}
																	?>
																</div>
																<?php 
																if ($data["prix"]>0)
																{
																	?>
																	<div style="display:none;" id="paiement_<?php echo $data["id"]; ?>" name="paiement_<?php echo $data["id"]; ?>">
																		
																	</div>
																	<?php
																}
															} 
															?>
															<div class="program_time">
																<div><?php echo date("H:i", $data["time_start"]); ?></div>
															</div>
														</div>
														<?php
													}
													?>
												</li>
												<?php
											}
										}
										if (!$act_found)
										{
											if ($filtersUrl!="")
											{
												?>
												<div class="info message_block">
													<p>Il n'y a aucune activité selon vos critères de recherche. <br/></p>
												</div>
												<?php
											}
											else if ($timeTemp>=time())
											{
												?>
												<div class="info message_block">
													<p>Nous n'avons pas encore ajouté d'activité. <br/></p>
												</div>
												<?php
											}
											else
											{
												?>
												<div class="info message_block">
													<p>Nous n'avions pas encore d'activité à vous proposer. <br/></p>
												</div>
												<?php
											}
										}
									?>
								</ul>
							</div>
							<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
    $(".page_name").html("Planning");
	if ($(".notification_new_event").length>0)
	{
		$(".notification_new_event").parent().click();
	}
	else
	{
		$("a[class='tab_link w-inline-block w-tab-link']").first().click();
	}
	$("#formFilters").find("a[class*='tags_name']").on('click', function(){
	if ($(this).hasClass('tags_name_checked'))
		$(this).removeClass('tags_name_checked');
	else
		$(this).addClass('tags_name_checked');
});
});
function activitesSearch(url){
	var onlyCamping=0;
	if ($("#onlyCamping").is(":checked"))
		onlyCamping=1;
	
	var tags="";
	$(".tags_name.tags_name_checked").each(function(){
		if (tags!="")
			tags=tags+",";
		tags=tags+$(this).html();
	});
	
	loadToMain('/demo/<?php echo LANG_USER; ?>/planning/<?php echo $dateUrl; ?>'+onlyCamping+'/'+tags, {});
}
function checkGoodInput(e)
{
	if ((e.which<47 || e.which>57) && e.which!=8 && e.which!=0)
	{
		e.preventDefault();
	}
}
function animationBack(idAct)
{
	$("#"+idAct+"_res").show();
	$("#paiement_"+idAct).hide().html("");
}
function animationPaiement(idAct, count)
{
	$("#"+idAct+"_res").hide();
	$("#paiement_"+idAct).show();
	loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("form_paiement.php")); ?>', {type:'ACTIVITE', id:idAct, place:count}, '#paiement_'+idAct, 'replace');
}
function selectPaiement(ct, cts)
{
	$(".custom_table").hide();
	
}
</script>