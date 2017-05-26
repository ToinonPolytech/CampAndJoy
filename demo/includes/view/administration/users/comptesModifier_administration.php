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
	if(!isStaff() || !$cuser->can(CAN_CREATE_ACCOUNT_STAFF))
	{
		exit();
	}
	$database=new Database();
	if (!isset($_GET["id"]) || !$database->count("users", array("id" => $_GET["id"])))
	{
		exit();
	}
	$user_edit=new user($_GET["id"]);
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Modification d'un compte</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Modification d'un compte</h4>
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
						<div class="step_title">Identité</div>
						<div>2</div>
					</div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Droits</div>
						<div>3</div>
					</div>
				</div>
			</div>
		</div>
		<div class="w-form"> 
			<form id="form-compte" name="form-compte">
				<div id="part_1">
					<label for="name">Email:</label>
					<input value="<?php echo htmlentities($user_edit->getUserInfos()->getEmail()); ?>" class="campandjoy_input w-input" maxlength="256" id="email" name="email" placeholder="Email" type="text">
					<label for="field">Emplacement:</label>
					<input value="<?php echo htmlentities($user_edit->getUserInfos()->getEmplacement()); ?>" class="campandjoy_input w-input" maxlength="256" id="emplacement" name="emplacement" placeholder="Emplacement (Laisser vide s'il n'a pas de logement)" type="text">
					<label for="name">Date d'arrivée:</label>
					<input value="<?php if ($user_edit->getUserInfos()->getTimeArrive()>0) { echo date("d/m/Y", $user_edit->getUserInfos()->getTimeArrive()); } ?>" class="campandjoy_input w-input" maxlength="256" id="date_arrivee" name="date_arrivee" placeholder="Date d'arrivée (Laisser vide s'il n'y en a pas)" type="text">
					<label for="name">Date de départ:</label>
					<input value="<?php if ($user_edit->getUserInfos()->getTimeDepart()>0) { echo date("d/m/Y", $user_edit->getUserInfos()->getTimeDepart()); } ?>" class="campandjoy_input w-input" maxlength="256" id="date_depart" name="date_depart" placeholder="Date de départ (Laisser vide s'il n'y en a pas)" type="text">
					<label for="name">Nombre de comptes affiliés maximum :</label>
					<input value="<?php echo htmlentities($user_edit->getUserInfos()->getComptesMax()); ?>" class="campandjoy_input w-input" maxlength="256" id="comptes_affi" name="comptes_affi" type="number" min="1">
					<br/><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>			
				<div id="part_2" style="display:none;">
					<div class="horizontal_form">
						<input value="<?php echo htmlentities($user_edit->getNom()); ?>" class="campandjoy_input w-input" maxlength="256" id="nom" name="nom" placeholder="Nom" type="text">
						<input value="<?php echo htmlentities($user_edit->getPrenom()); ?>" class="campandjoy_input w-input" maxlength="256" id="prenom" name="prenom" placeholder="Prénom" type="text">
						<select id="access_level" name="access_level" class="w-select campandjoy_input">
							<?php
								foreach ($listesTypes as $k => $v)
								{
									if (getNumberFromAccessLevel($k)<=getNumberFromAccessLevel($user->getAccessLevel()))
									{
										?>
										<option value="<?php echo $k; ?>" <?php if ($user_edit->getAccessLevel()==$k) { echo "selected"; } ?>><?php echo $k; ?></option>
										<?php
									}
								}
							?>
						</select> 
					</div>
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_3" style="display:none;">		
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#access_level").on("change", function(){
	loadTo("<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('droitsFormUser_administration.php')); ?>", {access_level : $("#access_level").val(), id : "<?php echo $user_edit->getId(); ?>"}, "#part_3", "replace");
});
loadTo("<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('droitsFormUser_administration.php')); ?>", {access_level : $("#access_level").val(), id : "<?php echo $user_edit->getId(); ?>"}, "#part_3", "replace");
$.datetimepicker.setLocale('fr');
$('#date_arrivee, #date_depart').datetimepicker({
	timepicker:false,
	formatDate:'d.m.y',
	format:'d/m/y'
});
</script>