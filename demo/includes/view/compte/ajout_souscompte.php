<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
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
	if (!$cuser->can("CAN_CREATE_SUBACCOUNT"))
	{
		header("Location:/demo/".LANG_USER."/compte");
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Création de compte affilié</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Créer un compte affilié</h4>
			<div class="step_head">
				<div class="step_current_item step_list_item">
					<div class="step_current_number step_number">
						<div>1</div>
						<div class="step_title">Identité</div>
					</div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div>2</div>
						<div class="step_title">Droits</div>
					</div>
				</div>
			</div>
		</div>
		<div class="w-form step_content">
			<form data-name="Email Form 2" id="creerCompte" name="creerCompte">
				<div id="part_1" class="horizontal_form">
					<input class="campandjoy_input w-input" id="prenom" maxlength="256" name="prenom" placeholder="Prénom" type="text" required="required">
					<input class="campandjoy_input w-input" maxlength="256" id="name"  name="name" placeholder="Nom" type="text" required="required">
					<br/><a class="primary_btn w-button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_2" style="display:none;">
					<?php
						foreach($CAN_infos as $indice => $infos)
						{
							?>
							<label class="control control--checkbox"><?php echo $infos; ?>
								<input type="checkbox"id="droit_<?php echo $indice; ?>" name="droit_<?php echo $indice; ?>" type="checkbox" checked>
								<div class="control__indicator"></div>
							</label>
							<?php
						}
						foreach($CAN_USER_infos as $indice => $infos)
						{
							?>
							<label class="control control--checkbox"><?php echo $infos; ?>
								<input type="checkbox"id="droit_<?php echo $indice; ?>" name="droit_<?php echo $indice; ?>" type="checkbox" checked>
								<div class="control__indicator"></div>
							</label>
							<?php
						}
					?>
					<a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); $(this).remove();  return false;">Retour</a><a class="primary_btn w-button form_button" onclick="removeStep(); $('#part_1').show(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('subUser.controllerForm.php')); ?>', $('#creerCompte').serialize(), '#creerCompte', 'before');" href="#creerCompte">Créer</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $(".page_name").html("Compte > Créer");
});
</script>