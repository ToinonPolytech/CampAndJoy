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
	if(!$cuser->can("CAN_CREATE_ACTIVITIES"))
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Ajout d'une activité</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Ajout d'une activité</h4>
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
				<?php
				if (isStaff())
				{
				?>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Reprogrammation</div>
						<div>3</div>
					</div>
				</div>
				<?php
				}
				?>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Traduction</div>
						<div><?php if (isStaff()) echo 4; else echo 3; ?></div>
					</div>
				</div>
			</div>
		</div>
		<div class="w-form">
			<form data-name="Email Form 2" id="form-act" name="form-act" enctype="multipart/form-data">
				<div id="part_1">
					<label for="name">Nom:</label>
					<input class="campandjoy_input w-input" data-name="Name" maxlength="256" id="name"  name="name" placeholder="Nom en <?php echo $lang_available[LANG_USER]; ?>" type="text">
					<label for="email">Date et heure de début:</label>
					<input class="campandjoy_input w-input" data-name="Email" id="timeStart" maxlength="256" name="timeStart" placeholder="" required="required" type="text">
					<label for="field">Description:</label>
					<textarea class="campandjoy_input w-input" id="description" maxlength="5000" name="description" placeholder="Description en <?php echo $lang_available[LANG_USER]; ?>"></textarea>
					<label for="name">Durée approximative :</label>
					<div class="horizontal_form">
						<input class="campandjoy_input w-input" id="duree" min="1" maxlength="256" name="duree" placeholder="Durée en minutes" type="number">
						<input class="campandjoy_input w-input" id="timeEnd" maxlength="256" name="timeEnd" placeholder="Date de fin" required="required" type="text">
					</div>
					<label for="lieu">L'emplacement :</label><br/>			
					<select class="w-select" name="lieu" id="lieu">
						<option value="0">Sélectionner un lieu</option>
						<?php
						$database = new Database();
						$database->setOrderCol("nom");
						$database->select("lieu_commun");
						while ($data=$database->fetch())
						{
							?>
							<option value="<?php echo htmlspecialchars($data["id"]); ?>"><?php echo htmlspecialchars($data["nom"]); ?></option>
							<?php
						}
						?>
						<option value="-1">Autre</option>
					</select>
					<input style="display:none;" class="campandjoy_input w-input" data-name="Name" id="lieu_autre" maxlength="256" name="lieu_autre" placeholder="Merci de présicer">
					<label for="type">Sélectionner le(s) type(s) correspondant à votre activité : </label><br/>			
					<div class="activity_tag custom_tag1 w-clearfix">
						<?php
							foreach ($listeTypes as $tag)
							{
								?>
								<a class="tags_name" href="#"><?php echo $tag; ?></a>
								<?php
							}
						?>
					</div>
					<?php
					if (isStaff())
					{
					?>
					<label for="points">Points disponibles</label>
					<input class="campandjoy_input w-input" data-name="Name" type="number" min="0" maxlength="256" name="points" id="points" value="0"/><br/>
					<label for="prix">Prix €</label>
					<input class="campandjoy_input w-input" data-name="Name" maxlength="256" type="text" name="prix" id="prix" value="0"/><br/>
					<label for="prix">Le membre de l'équipe en charge</label>
					<select class="w-select" id="animateurEnCharge" name="animateurEnCharge">
						<?php
							$database->select("users", array("access_level" => array("!=", "CLIENT")));
							while ($d=$database->fetch())
							{
								?>
								<option value="<?php echo $d["id"]; ?>" <?php if ($_SESSION["id"]==$d["id"]) { echo "selected"; } ?>><?php echo htmlentities($d["prenom"]." ".$d["nom"]); ?></option>
								<?php
							}
						?>
					</select>
					<?php
					}
					?>
					<a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_2" style="display:none;">
					<a class="primary_btn w-button" href="#" onclick="reservableButton(); return false;">Rendre l'activité réservable</a>
					<input class="campandjoy_input w-input" type="hidden" value="0" id="is_reservable" name="is_reservable" />
					<div id="reservable_hide" style="display:none;">
						<label for="type">Les dates limites de réservation : </label><br/>	
						<div class="horizontal_form">
							<input value="<?php echo date("d-m-Y H:i"); ?>" class="campandjoy_input w-input" data-name="Email" id="debutReservation" maxlength="256" name="debutReservation" placeholder="Début" required="required" type="text">
							<input class="campandjoy_input w-input" data-name="Email" id="finReservation" maxlength="256" name="finReservation" placeholder="Fin" required="required" type="text">
						</div>
						<label class="control control--checkbox">Votre activité dispose t'elle d'un nombre de places limité ?
							<input onclick="$('#placeslim_hide').toggle();" type="checkbox" id="places_limcheck" name="places_limcheck">
							<div class="control__indicator"></div>
						</label>
						<div id='placeslim_hide' style="display:none;">
							<label for="prix">Nombre de places</label>
							<input class="campandjoy_input w-input" data-name="Name" maxlength="256" type="number" min="0" name="placesLim" id="placesLim" value="0"/><br/>
						</div>
						<br/>		
					</div>
					<?php
					if (isStaff())
					{
						?>
						<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast');  return false;">Suivant</a>
						<?php
					}
					else
					{
						?>
						<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast');  return false;">Suivant</a>
						<?php
					}
					?>
				</div>
				<?php
				if (isStaff())
				{
					?>
					<div id="part_3" style="display:none;">
						<div class="info message_block">
							<p>Vous pouvez reprogrammer automatiquement votre activité. Cela vous évitera de devoir remplir de nouveau le formulaire si vous proposer de nouveau celle-ci régulièrement.</p>
						</div>
						<a class="primary_btn w-button" href="#" onclick="recurrenteButton(); return false;">Reprogrammer l'activité</a>
						<input class="campandjoy_input w-input" type="hidden" value="0" id="is_recurrente" name="is_recurrente" />
						<div id="estRecurrente_hide" style="display:none;">
							<br/>
							<label for="recurrence">Reprogrammer l'activité : </label>
							<select class="w-select" name="recurrence" id="recurrence">
								<option value="1">Tous les jours</option>
								<option value="2">Tous les deux jours</option>
								<option value="7">Chaque semaine</option>
								<option value="30">Chaque mois</option>
								<option value="-1">Choisir la/les dates précise(s)</option>
							</select></br>
							<input class="campandjoy_input w-input" type="text" name="dateRecurrence_1" id="dateRecurrence_1" maxlength="256" style="display:none;" placeholder="Date de reprogrammation"/>
							<label for="finRecurrence">Reprogrammer jusqu'au</label>
							<input class="campandjoy_input w-input" data-name="Name" type="text" name="finRecurrence" id="finRecurrence" maxlength="256"/><br/>
						</div>
						<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_3').toggle('fast'); $('#part_4').toggle('fast');  return false;">Suivant</a>	
					</div>
					<?php
				}
				?>
				<div id="part_<?php if (isStaff()) echo 4; else echo 3; ?>" style="display:none;">
					<div class="info message_block">
						<p>Ceci n'est pas obligatoire, mais vivement recommandé si vous souhaitez attirer et profiter des différences culturelles.</p>
					</div>
					<?php
					foreach ($lang_available as $langSign => $langExplain)
					{
						if ($langSign!=LANG_USER)
						{
							?>
							<label class="control control--checkbox">Proposer l'activité en <?php echo $langExplain; ?>
								<input class="lang_checkbox" type="checkbox" id="<?php echo $langSign; ?>" name="<?php echo $langSign; ?>">
								<div class="control__indicator"></div>
							</label>
							<?php
						}
					}
					?>
					
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_<?php if (isStaff()) echo 3; else echo 2; ?>').toggle('fast'); $('#part_<?php if (isStaff()) echo 4; else echo 3; ?>').toggle('fast'); return false;">Retour</a><a href="#form-act" class="primary_btn w-button form_button" onclick="removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('activite.controllerForm.php')); ?>', getDataActForm(), '#form-act', 'prepend'); return false;">Créer</a>			
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/administration/ajout_activite.js"></script>