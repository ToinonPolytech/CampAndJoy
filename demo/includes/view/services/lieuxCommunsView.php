<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$can_reserved=$cuser->can(CAN_RESERVE_LIEU_COMMUN);
	$db = new Database();
	$db2 = new Database();
	$db->setOrderCol("nom");
	$db->select("lieu_commun");
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Nos espaces communs</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">NOS ESPACES COMMUNS</h2>
			<div class="section_divide"></div>
			<p class="paragraph">Voici la liste des espaces et installations à votre disposition sur notre camping.&nbsp;</p>
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
				$horaires=unserialize($data["timeReservation"]);
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
						<h5 class="restau_name_subtitle"><?php echo htmlentities($data["nom"]); ?> <?php if ($data["estReservable"]) { ?><a class="tags_name" style="float:right;" href="#">Réservable</a><?php } else { ?><a class="tags_name" style="float:right;" href="#">Sans réservation</a><?php } ?></h5>
						<div class="block_2">
							<div class="flex_1">
								<p class="restaurant_description"><?php echo htmlentities($data["description"]); ?></p>
								<div class="hourly_block">
									<span class="hourly_title">Horaires d'ouverture</span><br/>
									  <?php
										$horaires_exist=false;
										$disabledDays="[";
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
												$horaires_exist=true;
												echo getDayFromNumber($day). " : ";
												echo $texte;
												echo "<br/>";
											}
											else
											{
												if ($disabledDays!="[")
													$disabledDays.=",";

												$disabledDays.=$day;
											}
										}
										if (!$horaires_exist)
										{
											echo "Pas d'information.";
										}
										$disabledDays.="]";
									?>
								</div>
							</div>
						</div>
						<?php  
							if(isStaff())
							{
								?>
								<a class="primary_btn w-button" href="/demo/<?php echo LANG_USER; ?>/administration/lieuCommun/modifier/<?php echo $data['id'];?>" >Modifier</a>
								<?php
							}
							if ($can_reserved && $data["estReservable"])
							{
								?>
								<div id="<?php echo $data["id"]; ?>_res">
									<a class="float_left primary_btn w-button" href="#" onclick="$('#<?php echo $data["id"]; ?>_res,#reservation_<?php echo $data["id"]; ?>').toggle('fast');  return false;">Réserver</a>
								</div>
								<form class="horizontal_form" style="display:none;" onsubmit="return false;" id="reservation_<?php echo $data["id"]; ?>" name="reservation_<?php echo $data["id"]; ?>">
									<input data-days="<?php echo $disabledDays; ?>" type="text" name="date" id="date" rel="<?php echo $data["id"]; ?>" class="campandjoy_input input_width_1 w-input" placeholder="Date" />
								</form>
								<?php
							}
						?>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$( document ).ready(function() {
		$(".page_name").html("Installations");
		$.datetimepicker.setLocale('fr');
		$('input[id="date"]').datetimepicker({
			timepicker:false,
			formatDate:'d.m.y',
			format:'d/m/y',
			onSelectDate:function(dp,$input){
				$("#time_"+$input.attr("rel")).remove();
				$("#button_"+$input.attr("rel")).remove();
				$("#duree_"+$input.attr("rel")).remove();
				loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("lieuxCommunsViewForm.php")); ?>", {id:$input.attr("rel"), date:$input.val()}, "#reservation_"+$input.attr("rel"), "append");
			},
			onShow:function(db,$input){
				this.setOptions({
					disabledWeekDays:$input.attr("data-days")
				})
			}
		});
	});
</script>