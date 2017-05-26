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
	$database=new Database();
	if(!isStaff() || !$cuser->can(CAN_CREATE_ACCOUNT_STAFF))
	{
		exit();
	}
	if (!isset($_POST["access_level"]) && getNumberFromAccessLevel($_POST["access_level"])>getNumberFromAccessLevel($user->getAccessLevel()))
	{
		exit();
	}
	if (isset($_POST["id"]) && !$database->count("users", array("id" => $_POST["id"])))
	{
		exit();
	}
	else if (isset($_POST["id"]))
	{
		$user_edit=new User($_POST["id"]);
		$cuser_edit=new Controller_User($user_edit);
	}
	
	?>
	<h4>Droits Communs</h4>
	<?php
	foreach($CAN_infos as $indice => $infos)
	{
		?>
		<label class="control control--checkbox"><?php echo $infos; ?>
			<input <?php if (!$cuser->can($indice)){ echo "disabled"; } ?> type="checkbox" id="droit_<?php echo $indice; ?>" name="droit_<?php echo $indice; ?>" type="checkbox" <?php if ((isset($listesTypes[$_POST["access_level"]]) && in_array($indice, $listesTypes[$_POST["access_level"]])) || (isset($cuser_edit) && $cuser_edit->can($indice))) { echo "checked"; } ?>>
			<div class="control__indicator"></div>
		</label>
		<?php
	}
	if ($_POST["access_level"]=="CLIENT")
	{
		?>
		<h4>Droits Clients</h4>
		<?php
		foreach($CAN_USER_infos as $indice => $infos)
		{
			?>
			<label class="control control--checkbox"><?php echo $infos; ?>
				<input type="checkbox" id="droit_<?php echo $indice; ?>" name="droit_<?php echo $indice; ?>" type="checkbox" <?php if ((isset($listesTypes[$_POST["access_level"]]) && in_array($indice, $listesTypes[$_POST["access_level"]])) || (isset($cuser_edit) && $cuser_edit->can($indice))) { echo "checked"; } ?>>
				<div class="control__indicator"></div>
			</label>
			<?php
		}
	}
	else
	{
		?>
		<h4>Droits Staff</h4>
		<?php
		foreach($CAN_STAFF_infos as $indice => $infos)
		{
			?>
			<label class="control control--checkbox"><?php echo $infos; ?>
				<input <?php if (!$cuser->can($indice)){ echo "disabled"; } ?> type="checkbox" id="droit_<?php echo $indice; ?>" name="droit_<?php echo $indice; ?>" type="checkbox" <?php if ((isset($listesTypes[$_POST["access_level"]]) && in_array($indice, $listesTypes[$_POST["access_level"]])) || (isset($cuser_edit) && $cuser_edit->can($indice))) { echo "checked"; } ?>>
				<div class="control__indicator"></div>
			</label>
			<?php
		}
	}
	if (isset($_POST["id"]))
	{
		?>
		<input type="hidden" id="id" name="id" value="<?php echo htmlentities($_POST["id"]); ?>" />
		<?php
	}
?>
<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Retour</a><a href="#form-compte" class="primary_btn w-button form_button" onclick="endForm(); removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('createUser.controllerForm.php')); ?>', $('#form-compte').serialize(), '#form-compte', 'prepend');"><?php if (isset($_POST["id"])) { echo "Modifier"; } else { echo "Créer"; } ?></a>	
<script type="text/javascript">
$('.tooltip').tooltipster({
	delay: 50,
	maxWidth: 500,
	speed: 300,
	interactive: true,
	contentCloning: true,
	contentAsHTML: true,
	animation: 'grow',
	trigger: 'hover'
});
</script>