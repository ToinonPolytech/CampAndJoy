<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,lieuCommun.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if(!isStaff() || ! isset($_GET['id']))
	{
		exit();
	}
	function makeDay($d, $name_day, $horaires)
	{
		?>
		<label class="control control--checkbox"><?php echo ucfirst($name_day); ?> Ouvert ?
			<input type="checkbox" onclick="$('#<?php echo $name_day; ?>_horaires').toggle();" id="checkbox_<?php echo $name_day; ?>" />
			<div class="control__indicator"></div>
		</label>
		<div id="<?php echo $name_day; ?>_horaires" style="display:none;">
			<?php
				$j=1;
				$i=0;
				$array_horaires=array();
				while($i<48)
				{
					while ($i<48 && !$horaires[$d][$i])
					{
						$i++;
					}
					if ($i==0 && $horaires[$d][47] && !in_array($i, $array_horaires))
					{
						while ($i<48 && $horaires[$d][$i])
						{
							$array_horaires[]=$i;
							$i++;
						}
						$seconde_horaire=$i-1;
						$i=47;
						while ($i>0 && $horaires[$d][$i])
						{
							$array_horaires[]=$i;
							$i--;
						}
						$first_horaire=$i+1;
						if ($j>1) echo "</div><br/>";
						?>
						<div class="horizontal_form">
							<input class="campandjoy_input w-input" type="text" name="horaire_open_<?php echo $name_day; ?>_<?php echo $j ?>" id="horaire_open_<?php echo $name_day; ?>_<?php echo $j ?>" placeholder="Heure d'ouverture" value="<?php if (floor($first_horaire/2)<10) echo 0; echo floor($first_horaire/2); echo ":"; if ($first_horaire%2==1) echo 30; else echo "00"; ?>" />
							<input class="campandjoy_input w-input" type="text" name="horaire_close_<?php echo $name_day; ?>_<?php echo $j ?>" id="horaire_close_<?php echo $name_day; ?>_<?php echo $j ?>" placeholder="Heure de fermeture" value="<?php if (floor($seconde_horaire/2)<10) echo 0; echo floor($seconde_horaire/2); echo ":"; if ($seconde_horaire%2==1) echo 30; else echo "00"; ?>" />
						<?php
						$j++;
						$i=$seconde_horaire;
					}
					else
					{
						if ($i<48 && !in_array($i, $array_horaires))
						{
							$first_horaire=$i;
							if ($i==47 || $horaires[$d][$i+1])
							{
								if ($i!=47)
								{
									while ($i<48 && $horaires[$d][$i])
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
							if ($j>1) echo "</div><br/>";
							?>
							<div class="horizontal_form">
								<input class="campandjoy_input w-input" type="text" name="horaire_open_<?php echo $name_day; ?>_<?php echo $j ?>" id="horaire_open_<?php echo $name_day; ?>_<?php echo $j ?>" placeholder="Heure d'ouverture" value="<?php if (floor($first_horaire/2)<10) echo 0; echo floor($first_horaire/2); echo ":"; if ($first_horaire%2==1) echo 30; else echo "00"; ?>" />
								<input class="campandjoy_input w-input" type="text" name="horaire_close_<?php echo $name_day; ?>_<?php echo $j ?>" id="horaire_close_<?php echo $name_day; ?>_<?php echo $j ?>" placeholder="Heure de fermeture" value="<?php if (floor($seconde_horaire/2)<10) echo 0; echo floor($seconde_horaire/2); echo ":"; if ($seconde_horaire%2==1) echo 30; else echo "00"; ?>" /> 
							<?php
							$j++;
						}
					}
					$i++;
				}
				if ($j==1)
				{
					?>
					<div class="horizontal_form">
						<input class="campandjoy_input w-input" type="text" name="horaire_open_<?php echo $name_day; ?>_1" id="horaire_open_<?php echo $name_day; ?>_1" placeholder="Heure d'ouverture" />
						<input class="campandjoy_input w-input" type="text" name="horaire_close_<?php echo $name_day; ?>_1" id="horaire_close_<?php echo $name_day; ?>_1" placeholder="Heure de fermeture" /> 
					<?php
				}
				else
				{
					?>
					<script type="text/javascript">
						$("#checkbox_<?php echo $name_day; ?>").click();
					</script>
					<?php
				}
			?>
			<img src="unknow" alt="+" onclick="addHoraires('<?php echo $name_day; ?>');" id="button_plus_<?php echo $name_day; ?>" name="button_plus_<?php echo $name_day; ?>" />
			</div>
		</div>
		<?php
	}
	$lieu = new LieuCommun($_GET['id']); 
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Modification d'un espace commun</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Modifier <?php echo htmlentities($lieu->getNom());?></h4>
			<div class="step_head">
				<div class="step_current_item step_list_item">
					<div class="step_current_number step_number">
						<div class="current_step_title step_title">Informations</div>
						<div>1</div>
					</div>
					<div class="step_current_arrow"></div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Réservation</div>
						<div>2</div>
					</div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Photos</div>
						<div>3</div>
					</div>
				</div>
			</div>
		</div>
		<div class="w-form"> 
			<form data-name="Email Form 2" id="form-lieu" name="form-lieu" enctype="multipart/form-data">
				<div id="part_1">
					<label for="name">Nom:</label>
					<input class="campandjoy_input w-input" data-name="Name" maxlength="256" id="nom"  name="nom" value="<?php echo htmlentities($lieu->getNom());?>" type="text">
					<label for="field">Description:</label>
					<textarea class="campandjoy_input w-input" id="description" maxlength="5000" name="description" ><?php echo htmlentities($lieu->getDescription());?></textarea>
					<br/><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>			
				<div id="part_2" style="display:none;">
					<a class="primary_btn w-button" href="#" onclick="horaireButton(); return false;">Ce lieu possède des horaires</a>
					<input class="campandjoy_input w-input" type="hidden" value="0" id="horaire" name="horaire" />
					<div id="horaire_hide" <?php if(empty($lieu->getHeureReservable())){ echo 'style="display:none;"';}?>>
						<label for="horaires">Horaires d'ouvertures</label><br/>
						<?php makeDay(1, "lundi", unserialize($lieu->getHeureReservable())); ?>						
						<?php makeDay(2, "mardi", unserialize($lieu->getHeureReservable())); ?>
						<?php makeDay(3, "mercredi", unserialize($lieu->getHeureReservable())); ?>
						<?php makeDay(4, "jeudi", unserialize($lieu->getHeureReservable())); ?>
						<?php makeDay(5, "vendredi", unserialize($lieu->getHeureReservable())); ?>
						<?php makeDay(6, "samedi", unserialize($lieu->getHeureReservable())); ?>
						<?php makeDay(0, "dimanche", unserialize($lieu->getHeureReservable())); ?>
					</div></br>
					<label class="control control--checkbox"> Autoriser les réservations de cet espace
					<input type="checkbox" id="estReservable" name="estReservable"<?php if($lieu->getEstReservable()){echo 'checked';}?>/>
					<div class="control__indicator"></div>
					</label>
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_3" style="display:none;">
					<div class="flex_list_photo">
						<?php
							$c=1;
							foreach (explode(",", $lieu->getPhotos()) as $url)
							{
								if (!empty($url))
								{
									?>
									<div class="photo_item">
										<img class="photo_image" src="<?php echo htmlentities($url); ?>">
										<input type="file" onchange="imageUpload($(this));" value="<?php echo $url; ?>" name="image<?php echo $c; ?>" id="image<?php echo $c; ?>" accept="image/*" style="display:none;"/>
										<div class="photo_delete" onclick="deleteUpload($(this)); if ($('#image<?php echo $c; ?>_delete')) { $('#image<?php echo $c; ?>_delete').val('1'); }"></div>
									</div>
									<input type="hidden" name="image<?php echo $c; ?>_delete" id="image<?php echo $c; ?>_delete" value="0" />
									<?php
									$c++;
								}
							}
							if ($c<=5)
							{
								?>
								<div class="photo_item"><img class="photo_image" src="images/image-placeholder.svg">
									<div class="add_photo_content" data-ix="add-photo-trigger">
										<div class="add_block"></div>
										<div class="text-block-2">Ajouter une photo</div>
									</div>
									<div class="add_photo_overlay"></div>
									<input type="hidden" name="MAX_FILE_SIZE" value="5048000" />
									<input type="file" onchange="imageUpload($(this));" name="image<?php echo $c; ?>" id="image<?php echo $c; ?>" accept="image/*" style="display:none;"/>
								</div>
								<?php
							}
						?>
					</div><br/>
					<br/><a class="primary_btn w-button" onclick="$('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Retour</a><a href="#form-lieu" class="primary_btn w-button form_button" onclick="removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('lieuCommun.controllerForm.php')); ?>',((window.FormData) ? new FormData($('#form-lieu')[0]) : $('#form-lieu').serialize()), '#form-lieu', 'prepend', true); return false;">Créer</a>	
				</div>
				
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/administration/ajout_lieu.js"></script>