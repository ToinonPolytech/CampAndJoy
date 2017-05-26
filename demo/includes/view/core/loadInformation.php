<?php
	/***
		PAGE VERIFIANT TOUTES LES NOTIFICATIONS // MESSAGES // ECT.. POUR L'INDIQUER A L'UTILISATEUR QUAND IL CHARGE UNE PAGE
		
		RECHARGEMENT NOTIFICATIONS
	***/
	$array_listes_modal=array();
	if (auth())
	{
		if (!isset($isAutomatic) && getStatutUser($_SESSION["lastUpdate"]-5)!="online") // Il ne faut pas éxecuter ce script quand on fait appel à un chargement automatique (Exemple : headerChat.php)
		{
			$database=new Database();
			$database->update("users", array("id" => $_SESSION["id"]), array("statut" => time()));
			$_SESSION["lastUpdate"]=time();
		}
		if (isClient())
		{
			if ($_SESSION["new_user"])
			{
				$database=new Database();
				$allowHeures="";
				foreach ($heuresAccueil as $h)
				{
					if ($allowHeures!="")
						$allowHeures.=",";
					
					$allowHeures.='"'.$h.'"';
				}
				$id_unique_modal=uniqid();
				$array_listes_modal[$id_unique_modal]='<div class="modal_message'.$id_unique_modal.' modal_message_dynamic" style="transform-style:preserve-3d;display:flex;">
					<div class="modal_container">
						<div class="w-container infos_msg">
							<h2 id="title_modal'.$id_unique_modal.'">Dites nous en plus !</h2>
							<p id="message_modal'.$id_unique_modal.'">
								Vous arrivez le '.date("d/m/Y", $database->getValue("userinfos ui LEFT JOIN users u ON ui.id=u.infoId", array("u.id" => $_SESSION["id"]), "time_arrive")).' pour profiter d\'un séjour dans notre camping. Pour que tous se passe au mieux, renseignez nous votre heure d\'arrivée.<br/>
							</p>
							<div style="text-align:center;" class="horizontal_form">
								<input style="margin-left:320px;" class="campandjoy_input w-input input_width_1" type="text" name="datetime_'.$id_unique_modal.'" id="datetime_'.$id_unique_modal.'" placeholder="Votre heure d\'arrivée" />
							</div>
							<div class="bottom_btn'.$id_unique_modal.'">
								<a data-ix="close-modal-msg'.$id_unique_modal.'" class="primary_btn space_beetween_btn w-button alert_btn" href="#" id="refuse_button'.$id_unique_modal.'"  onclick=\'$(".modal_message'.$id_unique_modal.'.modal_message_dynamic").remove();\'>Une prochaine fois</a>
								<a class="primary_btn w-button" href="#" id="confirm_button'.$id_unique_modal.'" onclick="loadTo(\''.str_replace($_SERVER["DOCUMENT_ROOT"], "", i("heureArrivee.controllerForm.php")).'\', {h:$(\'#datetime_'.$id_unique_modal.'\').val(), uniqId:\''.$id_unique_modal.'\'}, \'.modal_container\', \'prepend\');">Valider</a>
							</div>
							<div class="btn_close_modal" data-ix="close-modal-msg'.$id_unique_modal.'" onclick=\'$(".modal_message'.$id_unique_modal.'.modal_message_dynamic").remove();\'></div>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					$(document).ready(function(){
						Webflow.require("ix").init([
							{"slug":"close-modal-msg'.$id_unique_modal.'","name":"Close_modal_msg'.$id_unique_modal.'","value":{"style":{},"triggers":[{"type":"click","selector":".modal_message'.$id_unique_modal.'","preserve3d":true,"stepsA":[{"opacity":0,"transition":"transform 250ms ease 0, opacity 250ms ease 0","scaleX":5,"scaleY":5,"scaleZ":1},{"display":"none"}],"stepsB":[]}]}}
						]);
						$("#datetime_'.$id_unique_modal.'").datetimepicker({
							startDate:new Date(),
							format:"H:i",
							formatDate:"H:i",
							datepicker:false,
							allowTimes:['.$allowHeures.'],
							step:5
						});
					});
				</script>';
				$_SESSION["new_user"]=false; // Cela ne s'éxecute donc, qu'a la connexion
			}
		}
		else if (isStaff())
		{
			$database=new Database();
			$database->prepare("SELECT id, nom FROM activities WHERE rewarded=0 AND time_start+duree*60<:time AND idDirigeant=:user AND points>0");
			$database->execute(array("time" => time(), "user" => $_SESSION["id"]));
			while ($data=$database->fetch())
			{
				$id_unique_modal=uniqid();
				$array_listes_modal[$id_unique_modal]='<div class="modal_message'.$id_unique_modal.' modal_message_dynamic" style="transform-style:preserve-3d;display:flex;">
					<div class="modal_container">
						<div class="w-container infos_msg">
							<h2 id="title_modal'.$id_unique_modal.'">Récompenser le(s) gagnant(s)</h2>
							<p id="message_modal'.$id_unique_modal.'">
								L`activité <b>'.htmlentities(getDataByLang($data["nom"])).'</b> est finis et aucun gagnant n\'a été désigné. Veuillez attribuer les points à un ou plusieurs gagnants.<br/>
							</p>
							<div style="text-align:center;" class="horizontal_form" id="form_'.$id_unique_modal.'">
								<input style="margin-left:320px;" class="campandjoy_input w-input input_width_1" type="text" name="search_name_'.$id_unique_modal.'" id="search_name_'.$id_unique_modal.'" placeholder="Nom de l\'utilisateur" />
								<input type="hidden" name="search_id_'.$id_unique_modal.'" id="search_id_'.$id_unique_modal.'" />
							</div>
							<br/>
							<div class="bottom_btn'.$id_unique_modal.'">
								<a data-ix="close-modal-msg'.$id_unique_modal.'" class="primary_btn space_beetween_btn w-button alert_btn" href="#" id="refuse_button'.$id_unique_modal.'"  onclick=\'$(".modal_message'.$id_unique_modal.'.modal_message_dynamic").remove();\'>Une prochaine fois</a>
								<a class="primary_btn w-button" href="#" id="confirm_button'.$id_unique_modal.'" onclick="alert(\'to do\'); return false;">Récompenser</a>
							</div>
							<div class="btn_close_modal" data-ix="close-modal-msg'.$id_unique_modal.'" onclick=\'$(".modal_message'.$id_unique_modal.'.modal_message_dynamic").remove();\'></div>
						</div>
					</div>
				</div>
				<script type="text/javascript">
					$(document).ready(function(){
						Webflow.require("ix").init([
							{"slug":"close-modal-msg'.$id_unique_modal.'","name":"Close_modal_msg'.$id_unique_modal.'","value":{"style":{},"triggers":[{"type":"click","selector":".modal_message'.$id_unique_modal.'","preserve3d":true,"stepsA":[{"opacity":0,"transition":"transform 250ms ease 0, opacity 250ms ease 0","scaleX":5,"scaleY":5,"scaleZ":1},{"display":"none"}],"stepsB":[]}]}}
						]);
						$("#search_name_'.$id_unique_modal.'").on("keyup", function(){
							loadTo(\''.str_replace($_SERVER["DOCUMENT_ROOT"], "", i("searchUserByName.php")).'\', {nom:$(\'#search_name_'.$id_unique_modal.'\').val(), input:\'search_name_'.$id_unique_modal.'\', limit:10}, \'#form_'.$id_unique_modal.'\', \'after\');
						});
					});
				</script>';
			}
		}
	}
?>