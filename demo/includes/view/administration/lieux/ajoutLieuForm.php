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
	if(!isStaff())
	{
		exit();
	}
	function makeDay($name_day)
	{
		?>
		<label class="control control--checkbox"><?php echo ucfirst($name_day); ?> Ouvert ?
			<input type="checkbox" onclick="$('#<?php echo $name_day; ?>_horaires').toggle();" />
			<div class="control__indicator"></div>
		</label>
		<div id="<?php echo $name_day; ?>_horaires" style="display:none;">
			<div class="horizontal_form">
				<input class="campandjoy_input w-input" name="horaire_open_<?php echo $name_day; ?>_1" id="horaire_open_<?php echo $name_day; ?>_1" placeholder="Heure d'ouverture" type="text">
				<input class="campandjoy_input w-input" name="horaire_close_<?php echo $name_day; ?>_1" id="horaire_close_<?php echo $name_day; ?>_1" placeholder="Heure de fermeture" type="text">
				<img alt="+" onclick="addHoraires('<?php echo $name_day; ?>');" id="button_plus_<?php echo $name_day; ?>" name="button_plus_<?php echo $name_day; ?>" /><br/>
			</div>
		</div>
		<?php
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Ajout d'un espace commun</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Ajout d'un espace commun</h4>
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
					<input class="campandjoy_input w-input" data-name="Name" maxlength="256" id="nom"  name="nom" placeholder="Nom" type="text">
					<label for="field">Description:</label>
					<textarea class="campandjoy_input w-input" id="description" maxlength="5000" name="description" placeholder="Description"></textarea>
					<br/><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>			
				<div id="part_2" style="display:none;">
					<a class="primary_btn w-button" href="#" onclick="horaireButton(); return false;">Ce lieu possède des horaires</a>
					<input class="campandjoy_input w-input" type="hidden" value="0" id="horaire" name="horaire" />
					<div id="horaire_hide" style="display:none;">
						<label for="horaires">Horaires d'ouvertures</label><br/>
						<?php makeDay("lundi"); ?>						
						<?php makeDay("mardi"); ?>
						<?php makeDay("mercredi"); ?>
						<?php makeDay("jeudi"); ?>
						<?php makeDay("vendredi"); ?>
						<?php makeDay("samedi"); ?>
						<?php makeDay("dimanche"); ?>
					</div></br>
					<label class="control control--checkbox"> Autoriser les réservations de cet espace
					<input type="checkbox" id="estReservable" name="estReservable" />
					<div class="control__indicator"></div>
					</label>
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_3" style="display:none;">
					<div class="flex_list_photo">
						<div class="photo_item"><img class="photo_image" src="images/image-placeholder.svg">
							<div class="add_photo_content" data-ix="add-photo-trigger">
								<div class="add_block"></div>
								<div class="text-block-2">Ajouter une photo</div>
							</div>
							<div class="add_photo_overlay"></div>
							<input type="file" onchange="imageUpload($(this));" name="image1" id="image1" accept="image/*" style="display:none;"/>
						</div>					
					</div>	
					<br/><a class="primary_btn w-button" onclick="$('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Retour</a><a href="#form-lieu" class="primary_btn w-button form_button" onclick="removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('lieuCommun.controllerForm.php')); ?>',((window.FormData) ? new FormData($('#form-lieu')[0]) : $('#form-lieu').serialize()), '#form-lieu', 'prepend', true); return false;">Créer</a>	
				</div>
				
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/administration/ajout_lieu.js">
	$( document ).ready(function() {
		$(".page_name").html("Administration");
	});
</script></script>




