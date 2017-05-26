<?php
	if (!isset($require))
	{
		$require="";
	}
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (auth())
	{
		header("Location:index.php");
	}
	$database = new Database();
	if (!isset($_GET["new"]) && isset($_COOKIE["clef"]) && $database->count('users', array("clef" => htmlspecialchars($_COOKIE["clef"])))==1)
	{ 
		$exist=true;
		$database->select("users", array("clef" => htmlspecialchars($_COOKIE["clef"])), array("nom", "prenom", "code"));
		$data=$database->fetch();
	}
?>
<div class="page_container">
	<div class="home_center_block">
		<div class="home_content">
			<div class="h_c_inner">
				<div class="form_wrapper w-form">
					<form>
						<div <?php if (isset($exist)){ ?>class="page_two" data-ix="pagetwoopacity"<?php } else { ?>class="page_one" data-ix="welcomepageonetriggerload"<?php } ?>>
							<h1><?php __(BONJOUR); ?></h1>
							<input class="input_style w-input" id="clef" maxlength="6" name="clef" placeholder="<?php __(DEMANDE_CLEF); ?>" required="required" type="text" onkeypress="runScript(event, $('#button_first_step'));">
							<div class="step_line">
								<div class="step_line_left_col"></div>
								<a class="step_btn w-button" id="button_first_step" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('connexionUser.controller.php')); ?>', {clef : $('#clef').val()}, 'body', 'append'); return false;"><?php __(SE_CONNECTER); ?> 1/2</a>
							</div>
						</div>
						<div <?php if (!isset($exist)){ ?>class="page_two" data-ix="pagetwoopacity"<?php } else { ?>class="page_one" data-ix="welcomepageonetriggerload"<?php } ?>>
							<h1><?php if (isset($exist)) { __(BONJOUR_PRENOM_NOM, $data["prenom"], $data["nom"]); } ?></h1>
							<input onkeypress="runScript(event, $('#button_second_step'));" class="input_style w-input" id="code" maxlength="4" name="code" placeholder="<?php if ($data["code"]==NULL){ __(CREER_CODE); }else{ __(DEMANDE_CODE); } ?>" required="required" type="password"/>
							<div class="step_line">
								<div class="step_line_left_col">
									<a class="h_c_form_link" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('connexionUser.controller.php')); ?>', {clef : null}, 'body', 'append', false, function(){ if ($('.h_c_form_link').parent().parent().parent().hasClass('page_one')) { webflowCampandJoy('welcomepageonetrigger'); $('.page_two').addClass('page_oneTemp').removeClass('page_two'); $('.page_one').addClass('page_two').removeClass('page_one'); $('.page_oneTemp').addClass('page_one').removeClass('page_oneTemp'); } else { webflowCampandJoy('welcomepagetwotriggertopageone'); } }); return false;" href="#"><?php __(CE_NEST_PAS_VOUS); ?></a>
								</div>
								<a class="step_btn w-button" id="button_second_step" href="#log" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('connexionUser.controller.php')); ?>', {code : $('#code').val()}, 'body', 'append'); return false;"><?php __(SE_CONNECTER); ?> 2/2</a>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="h_c_overlay"></div>
		</div>
	</div>
</div>