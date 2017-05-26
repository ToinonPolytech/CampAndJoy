<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,database.class.php,";
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
	$db = new Database();
	if (!isset($_GET["id"]) || $db->count("users", array("id" => $_GET["id"]))==0)
	{
		header("Location:/demo/".LANG_USER."/index.php");
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$id=$_GET["id"];
	$subUser=new User($id);
	$csubUser=new Controller_User($subUser);
	if (!$cuser->canEdit($subUser))
	{
		header("Location:/demo/".LANG_USER."/compte");
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Modifier le compte affilié</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Modifier le compte affilié</h4>
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
			<form id="modifierCompte" name="modifierCompte">
				<div id="part_1" class="horizontal_form">
					<input class="campandjoy_input w-input" id="prenom" maxlength="256" name="prenom" placeholder="Prénom" type="text" required="required" value="<?php echo htmlentities($subUser->getPrenom()); ?>">
					<input class="campandjoy_input w-input" maxlength="256" id="name"  name="name" placeholder="Nom" type="text" required="required" value="<?php echo htmlentities($subUser->getNom()); ?>">
					<a class="primary_btn w-button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_2" style="display:none;">
					<?php
						foreach($CAN_infos as $indice => $infos)
						{
							?>
							<label class="control control--checkbox"><?php echo $infos; ?>
								<input type="checkbox"id="droit_<?php echo $indice; ?>" name="droit_<?php echo $indice; ?>" type="checkbox" <?php if ($csubUser->can($indice)) { echo "checked"; } ?>>
								<div class="control__indicator"></div>
							</label>
							<?php
						}
						foreach($CAN_USER_infos as $indice => $infos)
						{
							?>
							<label class="control control--checkbox"><?php echo $infos; ?>
								<input type="checkbox"id="droit_<?php echo $indice; ?>" name="droit_<?php echo $indice; ?>" type="checkbox" <?php if ($csubUser->can($indice)) { echo "checked"; } ?>>
								<div class="control__indicator"></div>
							</label>
							<?php
						}
					?>
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="removeStep(); $('#part_1').show(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('subUser.controllerForm.php')); ?>', $('#modifierCompte').serialize()+'&id=<?php echo $id; ?>', '#modifierCompte', 'before');" href="#modifierCompte">Modifier</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $(".page_name").html("Compte > Modifier");
});
</script>