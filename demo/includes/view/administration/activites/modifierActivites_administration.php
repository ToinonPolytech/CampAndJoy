<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,activities.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	if (!isset($_GET["id"]))
	{
		exit();
	}
	$id=$_GET["id"];
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$act=new Activite($id);
	if(!$cuser->can(CAN_CREATE_ACTIVITIES) || (!isStaff() && $act->getIdOwner()!=$_SESSION["id"]) ||  (isStaff() && $act->getIdOwner()!=$_SESSION["id"] && !$staffCanEditOther))
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Modifier une activité</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Modifier une activité</h4>
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
		<?php
			if ($act->getIdRecurrente()>0)
			{
				?>
				<div class="info message_block">
					<p>Vous allez modifier une activité qui est une reprogrammation. <br/>
					Cela n'affectera que cette activité. Pour modifier toutes les activités qui y sont reliés cliquez <a href="/demo/<?php echo LANG_USER; ?>/administration/activites/modifier/<?php echo $act->getIdRecurrente(); ?>">ici</a></p>
				</div>
				<?php
			}
			$nom=unserialize($act->getNom());
			$desc=unserialize($act->getDescriptif());		
			?>
			<form id="form-act" name="form-act" enctype="multipart/form-data">
				<div id="part_1">
					<label for="name">Nom:</label>
					<input <?php if (isset($nom[LANG_USER])) { ?>value="<?php echo htmlentities($nom[LANG_USER]); ?>" <?php } ?> class="campandjoy_input w-input"  maxlength="256" id="name"  name="name" placeholder="Nom en <?php echo $lang_available[LANG_USER]; ?>" type="text">
					<label for="timeStart">Date et heure de début:</label>
					<input value="<?php echo date("d-m-Y H:i", $act->getDate()); ?>" class="campandjoy_input w-input" id="timeStart" maxlength="256" name="timeStart" placeholder="" required="required" type="text">
					<label for="field">Description:</label>
					<textarea class="campandjoy_input w-input" id="description" maxlength="5000" name="description" placeholder="Description en <?php echo $lang_available[LANG_USER]; ?>"><?php if (isset($desc[LANG_USER])) { echo htmlentities($desc[LANG_USER]); } ?></textarea>
					<label for="name">Durée approximative :</label>
					<div class="horizontal_form">
						<input value="<?php echo htmlentities($act->getDuree()); ?>" class="campandjoy_input w-input" id="duree" min="1" maxlength="256" name="duree" placeholder="Durée en minutes" type="number">
						<input value="<?php echo date("d-m-Y H:i", $act->getDate()+$act->getDuree()); ?>" class="campandjoy_input w-input" id="timeEnd" maxlength="256" name="timeEnd" placeholder="Date de fin" required="required" type="text">
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
							<option value="<?php echo htmlspecialchars($data["id"]); ?>" <?php if ($data["nom"]==$act->getLieu()) { echo "selected"; $lieu_selected=true; } ?>><?php echo htmlspecialchars($data["nom"]); ?></option>
							<?php
						}
						?>
						<option value="-1" <?php if (!isset($lieu_selected)) { echo "selected"; } ?>>Autre</option>
					</select>
					<input value="<?php if (!isset($lieu_selected)) { echo htmlentities($act->getLieu()); } ?>" <?php if (isset($lieu_selected)) { ?>style="display:none;"<?php } ?> class="campandjoy_input w-input"  id="lieu_autre" maxlength="256" name="lieu_autre" placeholder="Merci de présicer">
					<label for="type">Sélectionner le(s) type(s) correspondant à votre activité : </label><br/>			
					<div class="activity_tag custom_tag1 w-clearfix">
						<?php
							$tags_act=explode(" ", $act->getType());
							foreach ($listeTypes as $tag)
							{
								?>
								<a class="tags_name <?php if (in_array($tag, $tags_act)) { ?>tags_name_checked<?php } ?>" href="#"><?php echo $tag; ?></a>
								<?php
							}
						?>
					</div>
					<?php
					if (isStaff())
					{
					?>
					<label for="points">Points disponibles</label>
					<input value="<?php echo htmlentities($act->getPoints()); ?>" class="campandjoy_input w-input" type="number" min="0" maxlength="256" name="points" id="points" value="0"/><br/>
					<label for="prix">Prix €</label>
					<input value="<?php echo htmlentities($act->getPrix()); ?>" class="campandjoy_input w-input"  maxlength="256" type="text" name="prix" id="prix" value="0"/><br/>
					<label for="prix">Le membre de l'équipe en charge</label>
					<select class="w-select" id="animateurEnCharge" name="animateurEnCharge">
						<?php
							$database->select("users", array("access_level" => array("!=", "CLIENT")));
							while ($d=$database->fetch())
							{
								?>
								<option value="<?php echo $d["id"]; ?>" <?php if ($act->getIdOwner()==$d["id"]) { echo "selected"; } ?>><?php echo htmlentities($d["prenom"]." ".$d["nom"]); ?></option>
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
					<input class="campandjoy_input w-input" type="hidden" value="<?php if ($act->getMustBeReserved()) { echo '1'; } else { echo '0'; } ?>" id="is_reservable" name="is_reservable" />
					<div id="reservable_hide" <?php if (!$act->getMustBeReserved()) { echo 'style="display:none;"'; } ?>>
						<label for="type">Les dates limites de réservation : </label><br/>	
						<div class="horizontal_form">
							<input value="<?php if ($act->getMustBeReserved()) { echo date("d-m-Y H:i", $act->getDebutReservation()); } ?>" class="campandjoy_input w-input" id="debutReservation" maxlength="256" name="debutReservation" placeholder="Début" required="required" type="text">
							<input value="<?php if ($act->getMustBeReserved()) { echo date("d-m-Y H:i", $act->getFinReservation()); } ?>" class="campandjoy_input w-input" id="finReservation" maxlength="256" name="finReservation" placeholder="Fin" required="required" type="text">
						</div>
						<label class="control control--checkbox">Votre activité dispose t'elle d'un nombre de place limitée ?
							<input onclick="$('#placeslim_hide').toggle();" type="checkbox" id="places_limcheck" name="places_limcheck" <?php if ($act->getPlacesLim()!=-1) { echo 'checked'; } ?>>
							<div class="control__indicator"></div>
						</label>
						<div id='placeslim_hide' <?php if ($act->getPlacesLim()==-1) { echo 'style="display:none;"'; } ?>>
							<?php
								$min=0;
								$db2=new Database();
								if ($act->getPlacesLim()>0)
								{
									$db2->select("reservation", array("type" => "ACTIVITE", "id" => $id), "nbrPersonne");
									while ($d=$db2->fetch()) { $min+=$d["nbrPersonne"]; }
								}
							?>
							<label for="prix">Nombre de places (Min : <?php echo $min; ?>)</label>
							<input value="<?php echo htmlentities($act->getPlacesLim()); ?>" class="campandjoy_input w-input"  maxlength="256" type="number" min="<?php echo $min; ?>" name="placesLim" id="placesLim" value="0"/><br/>
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
					<?php
						$isRecurrente=$database->count("activities", array("idRecurrente" => $id));
					?>
					<?php if (!$isRecurrente){ ?>
						<div class="info message_block"><p>Vous pouvez reprogrammer automatiquement votre activité. Cela vous évitera de devoir remplir de nouveau le formulaire si vous proposer de nouveau celle-ci régulièrement.</p></div>
						<a class="primary_btn w-button" href="#" onclick="recurrenteButton(); return false;">Reprogrammer l'activité</a>
					<?php }else{ ?>
						<div class="info message_block"><p>Sélectionnez les activités qui devront hérités des modifications de celle-ci. <br/> Si certaines des activités ont déjà été modifiés les changements ne les affecterons pas.</p></div>
						<div class="custom_table">
							<div class="custom_table_head">
								<div class="ctl_head_line_color custom_table_line">
									<div class="custom_table_col">
										<h6 class="head_text">Date</h6>
									</div>
									<div class="custom_table_col">
										<h6 class="head_text">Spécificités</h6>
									</div>
									<div class="custom_table_col">
										<h6 class="head_text">
											<label class="control control--checkbox">&nbsp;
												<input type="checkbox"id="act_all" name="act_all" type="checkbox" checked>
												<div class="control__indicator"></div>
											</label>
										</h6>
									</div>	
								</div>			
							</div>			
							<div class="custom_table_body">
							<?php
								$database->select("activities", array("idRecurrente" => $id));
								while ($data=$database->fetch())
								{
									$text="Les dates (Début, réservation) <br/>";
									if (strcmp($data["duree"], $act->getDuree())!==0)
									{
										$text.="Durée : ".htmlentities($data["duree"])."<br/>";
									}
									/*if (strcmp($data["nom"], $act->getNom())!==0)
									{
										$text.="Nom : ".htmlentities($data["nom"])."<br/>";
									}
									if (strcmp($data["description"], $act->getDescriptif())!==0)
									{
										$text.="Description <br/>";
									}*/
									if (strcmp($data["type"], $act->getType())!==0)
									{
										$text.="Tags <br/>";
									}
									if (strcmp($data["lieu"], $act->getLieu())!==0)
									{
										$text.="Lieu : ".htmlentities($data["lieu"])."<br/>";
									}
									if (strcmp($data["points"], $act->getPoints())!==0)
									{
										$text.="Points : ".htmlentities($data["points"])."<br/>";
									}
									if (strcmp($data["prix"], $act->getPrix())!==0)
									{
										$text.="Prix : ".htmlentities($data["prix"])."€<br/>";
									}
									if (strcmp($data["mustBeReserved"], $act->getMustBeReserved())!==0)
									{
										$text.="Si l'activité est réservable <br/>";
									}
									if (strcmp($data["capaciteMax"], $act->getPlacesLim())!==0)
									{
										$text.="Nombre de places : ".htmlentities($data["capaciteMax"])."<br/>";
									}
									$text="Les données suivantes ne seront pas modifiées : <br/>".$text;
								?>
								<div class="ctl_body_line custom_table_line">
									<div class="custom_table_col">
										<div><?php echo date("d/m/Y H:i", $data["time_start"]); ?></div>
									</div>
									<div class="custom_table_col">
										<div><img class="tooltip" title="<?php echo $text; ?>" src="images/state_info.png" width="17px" height="17px"/></div>
									</div>
									<div class="custom_table_col" style="width:100px;">
										<label class="control control--checkbox">&nbsp;
											<input type="checkbox" id="act_<?php echo $data["id"]; ?>" name="act_<?php echo $data["id"]; ?>" type="checkbox" checked>
											<div class="control__indicator"></div>
										</label>
									</div>
									<div class="custom_table col">
										<div><a href="<?php echo $data["id"]; ?>" class="primary_btn w-button">Aller à cette activité</a></div>
									</div>
								</div>
								<?php
								}
								?>
							</div>
						</div>
					<?php } ?>
						<input class="campandjoy_input w-input" type="hidden" value="<?php echo $isRecurrente; ?>" id="is_recurrente" name="is_recurrente" />
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
							<input class="campandjoy_input w-input"  type="text" name="finRecurrence" id="finRecurrence" maxlength="256"/><br/>
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
								<input class="lang_checkbox" type="checkbox" id="<?php echo $langSign; ?>" name="<?php echo $langSign; ?>" <?php if (isset($nom[$langSign])) { echo "checked"; } ?>>
								<div class="control__indicator"></div>
							</label>
							<?php
							if (isset($nom[$langSign]))
							{
								?>
								<div id="_<?php echo $langSign; ?>">
									<label for="name_<?php echo $langSign; ?>">Nom:</label>
									<input value="<?php echo htmlentities($nom[$langSign]); ?>" class="campandjoy_input w-input" maxlength="256" id="name_<?php echo $langSign; ?>"  name="name_<?php echo $langSign; ?>" placeholder="Nom" type="text">
									<label for="field">Description:</label>
									<textarea class="campandjoy_input w-input" id="description_<?php echo $langSign; ?>" maxlength="5000" name="description_<?php echo $langSign; ?>" placeholder="Description"><?php echo htmlentities($desc[$langSign]); ?></textarea>
								</div>
								<?php
							}
						}
					}
					?>					
					<br/><a class="primary_btn w-button" onclick="goPrevStep(); $('#part_<?php if (isStaff()) echo 3; else echo 2; ?>').toggle('fast'); $('#part_<?php if (isStaff()) echo 4; else echo 3; ?>').toggle('fast'); return false;">Retour</a><a href="#form-act" class="primary_btn w-button form_button" onclick="removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('activite.controllerForm.php')); ?>', getDataActForm(), '#form-act', 'prepend'); return false;">Modifier</a>			
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$("input[class='lang_checkbox']").on("click",function(){
	if ($(this).is(":checked"))
	{
		$(this).parent("label").after('<div id="_'+$(this).attr("id")+'"><label for="name_'+$(this).attr("id")+'">Nom:</label><input value="'+$("#name").val()+'" class="campandjoy_input w-input" maxlength="256" id="name_'+$(this).attr("id")+'"  name="name_'+$(this).attr("id")+'" placeholder="Nom" type="text"><label for="field">Description:</label><textarea class="campandjoy_input w-input" id="description_'+$(this).attr("id")+'" maxlength="5000" name="description_'+$(this).attr("id")+'" placeholder="Description">'+$("#description").val()+'</textarea></div>');
	}
	else
	{
		$("#_"+$(this).attr("id")).remove();
	}		
});
$("#recurrence").on("change", function(){
	if ($(this).val()==-1)
	{
		$("label[for='finRecurrence']").hide();
		$("#finRecurrence").hide();
		$("input[id^='dateRecurrence_']").show();
		$("input[id^='dateRecurrence_']").last().after('<span id="dateRecurrence_add">+</span>');
		$("#dateRecurrence_add").on("click", function(){
			var n=parseInt($("input[id^='dateRecurrence_']").length)+parseInt(1);
			$(this).before('<input class="campandjoy_input w-input" type="text" name="dateRecurrence_'+n+'" id="dateRecurrence_'+n+'" maxlength="256"  placeholder="Date de reprogrammation"/>');
			$("#dateRecurrence_"+n).datetimepicker({
				startDate:new Date(),
				format:'d-m-Y H:i',
				formatDate:'d-m-Y H:i',
				onShow:function( ct ){
				   this.setOptions({
					minDate:$('#timeStart').val()?$('#timeStart').val():0
				   })
				  }
			});
		});
	}
	else
	{
		$("label[for='finRecurrence']").show();
		$("#finRecurrence").show();
		$("input[id^='dateRecurrence_']").hide();
		$("#dateRecurrence_add").remove();
	}
});
$("#dateRecurrence_1").datetimepicker({
	startDate:new Date(),
	format:'d-m-Y H:i',
	formatDate:'d-m-Y H:i',
	onShow:function( ct ){
	   this.setOptions({
		minDate:$('#timeStart').val()?$('#timeStart').val():0
	   })
	  }
});
$("#lieu").on("change", function(){
	if ($(this).val()==-1)
	{
		$("#lieu_autre").toggle();
	}
	else
	{
		$("#lieu_autre").toggle();
	}
});
$("#timeStart").datetimepicker({
	startDate:new Date(),
	format:'d-m-Y H:i',
	formatDate:'d-m-Y H:i',
	onChangeDateTime:function(dp,$input){
		$("#finReservation").val($input.val());
	},
	onShow:function( ct ){
		this.setOptions({
		minDate:0
		})
	}
});
$("#timeEnd").datetimepicker({
	startDate:new Date(),
	format:'d-m-Y H:i',
	formatDate:'d-m-Y H:i',
	onChangeDateTime:function(dp,$input){
		if ($('#timeStart').val()){
			$("#duree").val(Math.floor(($("#timeEnd").datetimepicker('getValue').getTime()-$('#timeStart').datetimepicker('getValue').getTime())/60000));
		}
	},
	onShow:function( ct ){
		this.setOptions({
		minDate:$('#timeStart').val()
		})
	}
});
$("#debutReservation").datetimepicker({
	startDate:new Date(),
	format:'d-m-Y H:i',
	formatDate:'d-m-Y H:i',
	onShow:function( ct ){
	   this.setOptions({
		minDate:0,
		maxDate:$('#timeStart').val()
	   })
	  }
});
$("#finReservation").datetimepicker({
	startDate:new Date(),
	format:'d-m-Y H:i',
	formatDate:'d-m-Y H:i',
	onShow:function( ct ){
	   this.setOptions({
		minDate:$('#debutReservation').val(),
		maxDate:$('#timeStart').val()
	   })
	  }
});
$("#finRecurrence").datetimepicker({
	startDate:new Date(),
	format:'d-m-Y',
	formatDate:'d-m-Y H:i',
	timepicker:false,
	onShow:function( ct ){
	   this.setOptions({
		minDate:$('#timeStart').val()
	   })
	  }
});
$("a[class*='tags_name']").on('click', function(){
	if ($(this).hasClass('tags_name_checked'))
		$(this).removeClass('tags_name_checked');
	else
		$(this).addClass('tags_name_checked');
});
function getDataActForm()
{
	var tags="";
	$(".tags_name.tags_name_checked").each(function(){
		if (tags!="")
			tags=tags+",";
		tags=tags+$(this).html();
	});
	var dataForm=$('#form-act').serialize();
	dataForm=dataForm+"&type="+tags+"&id=<?php echo $id; ?>";
	return dataForm;
}
function reservableButton()
{
	if ($('#reservable_hide').css('display')=="none")
	{ 
		$('#is_reservable').val('1'); 
	} 
	else 
	{ 
		$('#is_reservable').val('0'); 
	} 
	$('#reservable_hide').toggle('fast'); 
}
function recurrenteButton()
{
	if ($('#estRecurrente_hide').css('display')=="none")
	{ 
		$('#is_recurrente').val('1'); 
	} 
	else 
	{ 
		$('#is_recurrente').val('0'); 
	} 
	$('#estRecurrente_hide').toggle('fast'); 
}
$("#duree").on("change", function dureeChange(event){
	if ($('#timeStart').val()){
		var d=new Date();
		d.setTime(parseInt($("#duree").val()*60*1000)+parseInt($("#timeStart").datetimepicker('getValue').getTime()));
		$("#timeEnd").val(String(d.toLocaleString()).replace(new RegExp("/","gi"), "-").replace("à ", ""));
	}
	});	$("#act_all").on("click", function(){		if ($(this).is(":checked"))		{			$(".custom_table_body").find("input[id^='act']").prop("checked", true);		}		else		{			$(".custom_table_body").find("input[id^='act']").prop("checked", false);		}	});
$( document ).ready(function() {
	$(".page_name").html("Activites > Modifier");
	initAddPhotos();
});
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
$(document).ready(function() {
		$(".page_name").html("Administration");
	});
</script>