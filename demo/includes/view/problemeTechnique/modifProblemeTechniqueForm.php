<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,problemeTechnique.class.php,problemeTechnique.controller.class.php";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	if(!isset($_GET['id']))
	{
		exit();
	}		
	$database=new Database();
	if ($database->count("problemes_technique", array("id" => $_GET["id"]))==0)
	{
		exit();
	}
	$pbt= new PbTech($_GET['id']);
	$cpbt = new Controller_Pbtech($pbt);
	if(!$cpbt->isOwner())
	{
		exit();
	}
	
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Modification d'un problème technique</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<h4>Mettez à jour votre problème</h4>
		
		<div class="w-form"> 
			<form data-name="Email Form 2" id="form-pbt" name="form-pbt" enctype="multipart/form-data">
				<label for="field">Le problème a lieu :  </label>
				<label class="control control--radio"> Dans mon logement
					  <input id="isBungalow" name="isBungalow" type="radio" value="true" checked=<?php if($pbt->getIsBungalow()==1)echo "checked";?>>
					  <div class="control__indicator"></div>
				</label>
				<label class="control control--radio"> Autre
					  <input id="isBungalow" name="isBungalow" type="radio" value="false" checked=<?php if($pbt->getIsBungalow()==1)echo "checked";?> >
					  <div class="control__indicator"></div>
				</label>	 
				<div id="desc1_hide" >
				<label for="field">Expliquez votre problème</label>
				<textarea class="campandjoy_input w-input" id="description" maxlength="10000" name="description"><?php echo htmlspecialchars($pbt->getDescription());?></textarea>
				</div>				
				<div class="flex_list_photo">
					<?php
						$c=1;
						foreach ($pbt->getPhotos() as $url)
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
				</div>						
				<input type="hidden" id="id" name="id" value="<?php echo $_GET['id']; ?>"/>
				<a href="#form-lieu" class="primary_btn w-button" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('problemeTechnique.controllerForm.php')); ?>',((window.FormData) ? new FormData($('#form-pbt')[0]) : $('#form-pbt').serialize()), '#form-pbt', 'prepend', true); return false;">Mettre à jour</a>	
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">	
	$( document ).ready(function() {
		initAddPhotos();		
		$(".page_name").html("Mes problèmes > Modifier");
});
</script>
	});
</script>