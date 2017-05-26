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
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Signalement d'un problème technique</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<h4>Signaler votre problème</h4>
		
		<div class="w-form"> 
			<form data-name="Email Form 2" id="form-pbt" name="form-pbt" enctype="multipart/form-data">
				<label for="field">Le problème a lieu :  </label>
				<label class="control control--radio"> Dans mon logement
					  <input id="isBungalow" name="isBungalow" type="radio" value="true">
					  <div class="control__indicator"></div>
				</label>
				<label class="control control--radio"> Autres
					  <input id="isBungalow" name="isBungalow" type="radio" value="false">
					  <div class="control__indicator"></div>
				</label>	 
				<div id="part_1bis" style="display:none;">
					<label for="field">Expliquez votre problème</label>
					<textarea class="campandjoy_input w-input" id="description" maxlength="10000" name="description" placeholder="Description du problème"></textarea>
					<div class="flex_list_photo">
						<div class="photo_item"><img class="photo_image" src="images/image-placeholder.svg">
							<div class="add_photo_content" data-ix="add-photo-trigger">
								<div class="add_block"></div>
								<div class="text-block-2">Ajouter une photo</div>
							</div>
							<div class="add_photo_overlay"></div>
							<input type="file" onchange="imageUpload($(this));" name="image1" id="image1" accept="image/*" style="display:none;"/>
						</div>					
					</div></br>
					<a href="#form-lieu" class="primary_btn w-button" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('problemeTechnique.controllerForm.php')); ?>',((window.FormData) ? new FormData($('#form-pbt')[0]) : $('#form-pbt').serialize()), '#form-pbt', 'prepend', true); return false;">Signaler</a>	
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/ajout_pbtech.js">
$( document ).ready(function() {
	$(".page_name").html("Contact");
});
</script>