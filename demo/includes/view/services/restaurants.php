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
	$can_reserved=$cuser->can(CAN_RESERVE_RESTAURANT);

	$timestamp=time();
	if (isset($_GET["date"]) && isset($_GET["h"]) && isset($_GET["m"]) && ($_GET["m"]==00 || $_GET["m"]==30))
	{
		$timestamp=strtotime($_GET["date"]." ".$_GET["h"].":".$_GET["m"]);
		if ($timestamp===false)
			$timestamp=time();
		else
		{
			$array=explode("-", $_GET["date"]);
			$date=$array[2]."/".$array[1]."/".$array[0];
			$hours=$_GET["h"].":".$_GET["m"];
		}
	}
	if (isset($_GET["pers"]))
	{
		$pers=$_GET["pers"];
	}
	$db = new Database();
	$db2 = new Database();
	$db->setOrderCol("nom");
	$db->select("restaurant");
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Restaurants aux alentours du camping</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">LES RESTAURANTS</h2>
			<div class="section_divide"></div>
			<div class="fliter_block">
				<div class="filter_title">Filtres de recherche :</div>
				<div class="w-form">
					<form class="horizontal_form" data-name="Email Form" id="formRestaurant" name="formRestaurant">
						<input <?php if (isset($date)) { echo "value='".$date."'"; } ?> class="campandjoy_input input_width_1 w-input" data-name="datepicker" id="datepicker" maxlength="256" name="datepicker" placeholder="Date" type="text">
						<input <?php if (isset($hours)) { echo "value='".$hours."'"; } ?> class="campandjoy_input input_width_1 w-input" data-name="timepicker" id="timepicker" maxlength="256" name="timepicker" placeholder="Heure" type="text">
						<input <?php if (isset($pers)) { echo "value='".$pers."'"; } ?> class="campandjoy_input input_width_1 w-input" data-name="pers" id="pers" maxlength="256" name="pers" placeholder="Pers." min="1" type="number">
						<a class="primary_btn w-button wrapp_btn_null" href="#" onclick="seekRestaurant('/demo/<?php echo LANG_USER; ?>/service/restaurant/'); return false;">Trouver un restaurant</a>
					</form>
				</div>
			</div>
			<p class="paragraph">Voici la liste des restaurants qui sont les plus proches de notre camping. Pour une recherche plus précise, merci d'utiliser les filtres ci-dessus.&nbsp;</p>
		</div>
		<div class="c_section">
		<?php
			while ($data=$db->fetch())
			{
				$photos=explode(",", $data["photos"]);
				$photos_basic="images/300.png";
				$name="300.png";
				if (isset($photos[0]) && !empty($photos[0]))
				{
					$photos_basic=$photos[0];
					$infos_photos=explode("/", $photos_basic);
					$name=$infos_photos[count($infos_photos)-1];
				}
				$horaires=unserialize($data["heureOuverture"]);
				if (date("i", $timestamp)>=30)
					$open=$horaires[date("w", $timestamp)][date("H", $timestamp)*2+1];
				else
					$open=$horaires[date("w", $timestamp)][date("H", $timestamp)*2];
				
				$capacite=htmlentities($data["capacite"]);
				$db2->select("reservation", array("type" => "RESTAURANT", "id" => $data["id"], "time" => array($timestamp, $timestamp+30*60-1)), "nbrPersonne");
				while ($d=$db2->fetch()) { $capacite-=$d["nbrPersonne"]; }
				if ((!isset($pers) || $capacite>=$pers) && (!isset($date) || $open))
				{
				?>
					<div class="cs_sub_section">
						<div class="cs_col2">
							<div class="restaurant_picture_block">
								<a class="restauration_gallery w-inline-block w-lightbox" href="#">
									<img class="restauration_thumb" sizes="(max-width: 479px) 91vw, (max-width: 767px) 88vw, (max-width: 991px) 35vw, 372px" src="<?php echo $photos_basic; ?>" srcset="<?php echo $photos_basic; ?>">
									<div class="restauration_gallery_overlay" data-ix="show-gallery-icon">
										<img class="restauration_gallery_icon" data-ix="hide-gallery-icon" src="images/search.svg">
									</div>
									<script class="w-json" type="application/json">
										{ "items": [{
											"type": "image",
											"_id": "<?php echo uniqid(); ?>",
											"fileName": "<?php echo $name; ?>",
											"origFileName": "<?php echo $name; ?>",
											"width": 300,
											"height": 300,
											"url": "<?php echo $photos_basic; ?>"
										}] }
									</script>
								</a>
							</div>
						</div>
						<div class="cs_col3 w-clearfix">
							<h5 class="restau_name_subtitle">Description du restaurant <?php echo htmlentities($data["nom"]); ?></h5>
							<span><strong><?php echo $capacite; ?></strong> places disponibles</span>
							<div class="block_2">
								<div class="flex_1">
									<?php if (!isset($date)) { ?> <strong style="color:<?php if ($open) { echo "green"; } else { echo "red"; } ?>"><?php if ($open) { echo "Ouvert"; } else { echo "Fermé"; } ?></strong><?php }
									else {
									?>
									<p><strong style="color:<?php if ($open) { echo "green"; } else { echo "red"; } ?>"><?php if ($open) { echo "Ouvert"; } else { echo "Fermé"; } ?></strong> le <?php echo $date; ?> à <?php echo date("H:i", $timestamp); ?></p>
									<?php
									}?>
									<div class="hourly_block">
										<span class="hourly_title">Horaires d'ouverture</span><br/>
										  <?php
											foreach ($horaires as $day => $horaires_hours)
											{
												$texte="";
												$j=1;
												$i=0;
												$array_horaires=array();
												while($i<48)
												{
													while ($i<48 && !$horaires_hours[$i])
													{
														$i++;
													}
													if ($i==0 && $horaires_hours[47] && !in_array($i, $array_horaires))
													{
														while ($i<48 && $horaires_hours[$i])
														{
															$array_horaires[]=$i;
															$i++;
														}
														$seconde_horaire=$i-1;
														$i=47;
														while ($i>0 && $horaires_hours[$i])
														{
															$array_horaires[]=$i;
															$i--;
														}
														$first_horaire=$i+1;
														if ($j>1) $texte.=" || ";
														if (floor($first_horaire/2)<10) $texte.=0; $texte.=floor($first_horaire/2); $texte.=":"; if ($first_horaire%2==1) $texte.=30; else $texte.="00"; $texte.=" - ";
														if (floor($seconde_horaire/2)<10) $texte.=0; $texte.=floor($seconde_horaire/2); $texte.=":"; if ($seconde_horaire%2==1) $texte.=30; else $texte.="00";
														$j++;
														$i=$seconde_horaire;
													}
													else
													{
														if ($i<48 && !in_array($i, $array_horaires))
														{
															$first_horaire=$i;
															if ($i==47 || $horaires_hours[$i+1])
															{
																if ($i!=47)
																{
																	while ($i<48 && $horaires_hours[$i])
																	{
																		$array_horaires[]=$i;
																		$i++;
																	}
																}
																else
																{
																	$array_horaires[]=$i;
																	$i++;
																}
																$i--;
																$seconde_horaire=$i;
															}
															if ($j>1) $texte.=" || ";
															if (floor($first_horaire/2)<10) $texte.=0; $texte.=floor($first_horaire/2); $texte.=":"; if ($first_horaire%2==1) $texte.=30; else $texte.="00"; $texte.=" - ";
															if (floor($seconde_horaire/2)<10) $texte.=0; $texte.=floor($seconde_horaire/2); $texte.=":"; if ($seconde_horaire%2==1) $texte.=30; else $texte.="00";
															$j++;
														}
													}
													$i++;
												}
												if (!empty($texte))
												{
													echo getDayFromNumber($day). " : ";
													echo $texte;
													echo "<br/>";
												}
											}
										?>
									</div>
							    </div>
							</div>
							<?php
							if (!empty($data["menu"]))
							{
								?>
								<span onclick="$('#menu').toggle('fast');"> Voir le menu </span>
								<div class="block_1" style="display:none;" id="menu">
									<?php
										foreach (unserialize($data["menu"]) as $categorie_name=>$sub_array)
										{
											?>
											<h2 class="menu_title"><?php echo htmlentities($categorie_name); ?></h2>
											<?php
											foreach ($sub_array as $name => $prix)
											{
												?>
												<div class="flex_1">
													<h5><?php echo htmlentities($name); ?></h5>
													<div class="menu_price"><?php echo htmlentities($prix); ?>€</div>
												</div>
												<?php
											}
										}
									?>
								</div>
								<?php 
							}
							if ($can_reserved)
							{
								if ($db2->count("reservation", array("id" => $data["id"], "type" => "RESTAURANT", "idUser" => $_SESSION["id"]))==0)
								{
									if (isset($date) && isset($pers))
									{
									?>
										<div id="<?php echo $data["id"]; ?>_res">
											<a class="float_left primary_btn w-button" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('reservation.controllerForm.php')); ?>', {id : <?php echo $data["id"]; ?>, type : 'RESTAURANT', nbrPersonnes : <?php echo $pers; ?>, time : <?php echo $timestamp; ?>}, '#<?php echo $data["id"]; ?>_res', 'prepend'); return false;">Réserver <?php echo $pers; ?> place<?php if ($pers>1){ echo "s"; } ?></a>
										</div>
									<?php
									} 
									else 
									{ 
									?>
									<div id="<?php echo $data["id"]; ?>_res">
										<div id="part_1">
											<br/>
											<a class="float_left primary_btn w-button" href="#" onclick='$("#part_2, #part_1").toggle(); return false;'>Réserver</a>
										</div>
										<div id="part_2" style="display:none">
											<br/>
											<form class="horizontal_form" id="formRestaurant<?php echo $data["id"]; ?>" name="formRestaurant<?php echo $data["id"]; ?>">
												<input <?php if (isset($date)) { echo "value='".$date."'"; } ?> class="campandjoy_input input_width_1 w-input" id="datepicker<?php echo $data["id"]; ?>" maxlength="256" name="datepicker<?php echo $data["id"]; ?>" placeholder="Date" type="text">
												<input <?php if (isset($hours)) { echo "value='".$hours."'"; } ?> class="campandjoy_input input_width_1 w-input" id="timepicker<?php echo $data["id"]; ?>" maxlength="256" name="timepicker<?php echo $data["id"]; ?>" placeholder="Heure" type="text">
												<input <?php if (isset($pers)) { echo "value='".$pers."'"; } else { echo "value='1'"; } ?> class="campandjoy_input input_width_1 w-input" id="pers<?php echo $data["id"]; ?>" maxlength="256" name="pers<?php echo $data["id"]; ?>" placeholder="Pers." min="1" type="number" max="<?php echo htmlentities($capacite); ?>">
											</form>
											<a class="primary_btn w-button wrapp_btn_null" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('reservation.controllerForm.php')); ?>', {id : <?php echo $data["id"]; ?>, type : 'RESTAURANT', nbrPersonnes : $('#pers<?php echo $data["id"]; ?>').val(), date : $('#datepicker<?php echo $data["id"]; ?>').val(), heure : $('#timepicker<?php echo $data["id"]; ?>').val()}, '#<?php echo $data["id"]; ?>_res', 'append'); return false;">Finaliser</a>
										</div>
									</div>
									<?php 
									} 
								}
								else
								{
									?>
										<br/><a class="float_left primary_btn w-button" href="#" onclick="alert('TO DO'); return false;">Modifier ma Réservation</a>
									<?php
								}
							}
							?>
						</div>
					</div>
				<?php
				}
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
$.datetimepicker.setLocale('fr');
  $('input[id^="datepicker"]').datetimepicker({
  timepicker:false,
	formatDate:'d.m.y',
	format:'d/m/y',
	});
  $('input[id^="timepicker"]').datetimepicker({
	datepicker:false,
	format:'H:i',
	step:30
  });
</script>
<script type="text/javascript">
	$( document ).ready(function() {
		$(".page_name").html("Restaurant");
	});
</script>