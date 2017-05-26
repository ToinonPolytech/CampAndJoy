<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,partenaire.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		?>
		<script type="text/javascript">
			$( document ).ready(function() {
				window.location.replace("index.php");
			});
		</script>
		<?php
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if(!isStaff())
	{
		?>
		<script type="text/javascript">
			$( document ).ready(function() {
				window.location.replace("index.php");
			});
		</script>
		<?php
		exit();
	}
	$partenaire = new Partenaire($_GET['id']);
	if($partenaire->getIdUser()!=0)
	{
			$db = new Database();
			$db->select('users',array('id' => $partenaire->getIdUser()),array('prenom','nom'));
			$data=$db->fetch();
	}
	
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Modification d'un partenaire</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Modifier <?php echo $partenaire->getNom();?></h4>
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
					<input class="campandjoy_input w-input" maxlength="256" id="nom"  name="nom" value="<?php echo $partenaire->getNom();?>" type="text">
					<label for="field">Informations :</label>
					<textarea class="campandjoy_input w-input" id="libelle" maxlength="5000" name="libelle" ><?php echo $partenaire->getLibelle();?>"</textarea>
					<label for="name">Téléphone :</label>
					<input class="campandjoy_input w-input" maxlength="256" id="telephone"  name="telephone" value="<?php echo $partenaire->getTelephone();?>" type="text">
					<label for="name">Site web:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="siteWeb"  name="siteWeb" value="<?php echo $partenaire->getSiteWeb();?>" type="text">
					<label for="name">E-mail de contact:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="mail"  name="mail"value="<?php echo $partenaire->getMail();?>" type="text">
					<?php
					if($partenaire->getIdUser()!=0)
					{
						?>
						<label for="name">Compte utilisateur associé :</label>
						<input class="campandjoy_input w-input" maxlength="256" id="name_user" name="name_user" value="<?php echo $data['prenom'].' '.$data['nom'];?>" type="text"/>
						<input id="id_user" name="id_user" value="<?php echo $partenaire->getIdUser();?>" type="hidden"/>	
						<?php
					}
					else
					{
						?>
						<a class="primary_btn w-button" href="#" onclick="$('#name_user').toggle('fast');">Désigner un utilisateur pour ce partenaire</a>
						<input class="campandjoy_input w-input" maxlength="256" id="name_user" name="name_user" style="display:none;" placeholder="Nom ou prénom de l'utilisateur" type="text"/>
						<input id="id_user" name="id_user" type="hidden"/>
						<?php
					}
					?>					
					<a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>			
				<div id="part_2" style="display:none;">
					<div class="flex_list_photo">
						<?php
						$c=1;
						foreach (explode(",", $partenaire->getPhotos()) as $url)
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
					<input type="hidden" value="<?php echo $_GET['id']; ?>" id="id" name="id" />
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a href="#form-lieu" class="primary_btn w-button form_button" onclick="removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('partenaire.controllerForm.php')); ?>',((window.FormData) ? new FormData($('#form-part')[0]) : $('#form-part').serialize()), '#form-part', 'prepend', true); return false;">Valider</a>	
				</div>
				
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">var delay;var lastSearch="";function launchSearch(inp){	delay=Date.now();	setTimeout(function(){		if (parseInt(delay+500)<=Date.now())		{			if (lastSearch!=$("#name_user").val())			{				lastSearch=$("#name_user").val();				loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('ajoutUserPartenaire.php')); ?>",{"nom" : lastSearch}, "#name_user", "after");			}		}	}, 500);}$("input[id='name_user']").on("keypress", function(){	launchSearch("name_user");});$( document ).ready(function() {	initAddPhotos();});
	$( document ).ready(function() {
		$(".page_name").html("Administration");
	});
</script>
