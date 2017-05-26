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
	<h1 class="h1_color">Ajout d'un partenaire</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Ajout d'un espace commun</h4>
			<div class="step_head">
				<div class="step_current_item step_list_item">
					<div class="step_current_number step_number">
						<div class="current_step_title step_title">Partenaire</div>
						<div>1</div>
					</div>
					<div class="step_current_arrow"></div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Photos</div>
						<div>2</div>
					</div>
				</div>
			</div>
		</div>
		<div class="w-form"> 
			<form data-name="Email Form 2" id="form-part" name="form-part" enctype="multipart/form-data">
				<div id="part_1">
					<label for="name">Nom:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="nom"  name="nom" placeholder="Nom" type="text">
					<label for="field">Informations :</label>
					<textarea class="campandjoy_input w-input" id="libelle" maxlength="5000" name="libelle" placeholder="Décrivez votre partenaire : services proposés, tarifs, horaires ... "></textarea>
					<label for="name">Téléphone :</label>
					<input class="campandjoy_input w-input" maxlength="256" id="telephone"  name="telephone" placeholder="0607080901" type="text">
					<label for="name">Site web:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="siteWeb"  name="siteWeb" placeholder="partenaire.fr" type="text">
					<label for="name">E-mail de contact:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="mail"  name="mail" placeholder="http://partenaire@hébergeur.fr" type="text">
					<a class="primary_btn w-button" href="#" onclick="$('#name_user').toggle('fast'); return false;">Désigner un utilisateur pour ce partenaire</a>
					<input class="campandjoy_input w-input" maxlength="256" id="name_user" name="name_user" style="display:none;" placeholder="Nom ou prénom de l'utilisateur" type="text"/>
					<input id="id_user" name="id_user" type="hidden"/>
					<input class="campandjoy_input w-input" type="hidden" value="0" id="horaire" name="horaire" />
					<br/><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>			
				<div id="part_2" style="display:none;">
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
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a href="#form-lieu" class="primary_btn w-button form_button" onclick="removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('partenaire.controllerForm.php')); ?>',((window.FormData) ? new FormData($('#form-part')[0]) : $('#form-part').serialize()), '#form-part', 'prepend', true); return false;">Créer</a>	
				</div>				
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
var delay;
var lastSearch="";
function launchSearch(inp)
{
	delay=Date.now();
	setTimeout(function(){
		if (parseInt(delay+500)<=Date.now())
		{
			if (lastSearch!=$("#name_user").val())
			{
				lastSearch=$("#name_user").val();
				loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('ajoutUserPartenaire.php')); ?>",{"nom" : lastSearch}, "#name_user", "after");
			}
		}
	}, 500);
}
$("input[id='name_user']").on("keypress", function(){
	launchSearch("name_user");
});
$( document ).ready(function() {
	initAddPhotos();
});
$( document ).ready(function() {
		$(".page_name").html("Administration");
	});
</script>




