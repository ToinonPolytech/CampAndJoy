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
	if(!isStaff() || !$cuser->can("CAN_ADD_RESTAURANT_STAFF"))
	{
		exit();
	}
	function makeDay($name_day)
	{
		?>
		<label class="control control--checkbox"><?php echo ucfirst($name_day); ?> Ouvert ?
			<input type="checkbox" onclick="$('#<?php echo $name_day; ?>_horaires').toggle();" />
			<div class="control__indicator"></div>
		</label>
		<div id="<?php echo $name_day; ?>_horaires" style="display:none;">
			<div class="horizontal_form">
				<input class="campandjoy_input w-input" data-name="Name" maxlength="50" name="horaire_open_<?php echo $name_day; ?>_1" id="horaire_open_<?php echo $name_day; ?>_1" placeholder="Heure d'ouverture" type="text">
				<input class="campandjoy_input w-input" data-name="Name" maxlength="50" name="horaire_close_<?php echo $name_day; ?>_1" id="horaire_close_<?php echo $name_day; ?>_1" placeholder="Heure de fermeture" type="text">
				<img alt="+" onclick="addHoraires('<?php echo $name_day; ?>');" id="button_plus_<?php echo $name_day; ?>" name="button_plus_<?php echo $name_day; ?>" /><br/>
			</div>
		</div>
		<?php
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Ajout d'un restaurant</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="step_section">
			<h4 class="step_head_title">Ajout d'un restaurant</h4>
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
						<div class="step_title">Horaires</div>
						<div>2</div>
					</div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Photos</div>
						<div>3</div>
					</div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Gérants</div>
						<div>4</div>
					</div>
				</div>
				<div class="step_list_item">
					<div class="step_number">
						<div class="step_title">Menu</div>
						<div>5</div>
					</div>
				</div>
			</div>
		</div>
		<div class="w-form">
			<form id="form-rest" name="form-rest" enctype="multipart/form-data">
				<div id="part_1" name="part_1">
					<label for="name">Nom:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="name"  name="name" placeholder="Nom" type="text">
					<label for="name">Téléphone:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="telephone"  name="telephone" placeholder="Téléphone" type="text">	
					<label for="name">Email:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="email"  name="email" placeholder="Email" type="text">	
					<label for="name">Capacité:</label>
					<input class="campandjoy_input w-input" maxlength="256" id="capacite"  name="capacite" placeholder="Capacité de votre restaurant" type="number">
					<a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_2" name="part_2" style="display:none;">
					<label for="horaires">Horaires d'ouvertures</label><br/>	
					<?php makeDay("lundi"); ?>						
					<?php makeDay("mardi"); ?>
					<?php makeDay("mercredi"); ?>
					<?php makeDay("jeudi"); ?>
					<?php makeDay("vendredi"); ?>
					<?php makeDay("samedi"); ?>
					<?php makeDay("dimanche"); ?>
					<a class="primary_btn w-button" onclick="goPrevStep(); $('#part_1').toggle('fast'); $('#part_2').toggle('fast'); return false;">Retour</a> <a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_3" name="part_3" style="display:none;">
					<div class="flex_list_photo">
						<div class="photo_item"><img class="photo_image" src="images/image-placeholder.svg"/>
							<div class="add_photo_content" data-ix="add-photo-trigger">
								<div class="add_block"></div>
								<div class="text-block-2">Ajouter une photo</div>
							</div>
							<div class="add_photo_overlay"></div>
							<input type="file" onchange="imageUpload($(this));" name="image1" id="image1" accept="image/*" style="display:none;"/>
						</div>
					</div><br/>
					<a class="primary_btn w-button" onclick="goPrevStep(); $('#part_2').toggle('fast'); $('#part_3').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_3').toggle('fast'); $('#part_4').toggle('fast'); return false;">Suivant</a>
				</div>
				<div id="part_4" name="part_4" style="display:none;">
					<label for="name">Désigner les gérants du restaurant <img class="tooltip" title="Ils pourront voir les réservations effectuées, modifier le menu..." src="images/state_info.png" width="17px" height="17px"/>:</label>
					<span id="plus_user" name="plus_user" onclick="plus_user($(this));">+</span><br/>
					<a class="primary_btn w-button" onclick="goPrevStep(); $('#part_3').toggle('fast'); $('#part_4').toggle('fast'); return false;">Retour</a><a class="primary_btn w-button form_button" onclick="goNextStep(); $('#part_4').toggle('fast'); $('#part_5').toggle('fast'); return false;">Suivant</a>	
				</div>
				<div id="part_5" name="part_5" style="display:none;">
					<span id="plus_categ" name="plus_categ" onclick="plus_categ($(this));">+</span><br/>
					<a class="primary_btn w-button" onclick="goPrevStep(); $('#part_4').toggle('fast'); $('#part_5').toggle('fast'); return false;">Retour</a><a href="#form-rest" class="primary_btn w-button form_button" onclick="removeStep(); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('restaurant.controllerForm.php')); ?>', ((window.FormData) ? new FormData($('#form-rest')[0]) : $('#form-rest').serialize()), '#form-rest', 'prepend', true); return false;">Ajouter</a>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$("input[name^='horaire_close'],input[name^='horaire_open']").datetimepicker({
	startDate:new Date(),
	format:'H:i',
	datepicker:false,
	timepicker:true,
	step:30
});
function addHoraires(day)
{
	var text=String($("input[name^='horaire_close_"+day+"']:last").attr("id"));
	var id=parseInt(text.replace("horaire_close_", ""))+parseInt(1);
	var varcode='<div class="horizontal_form"><input class="campandjoy_input w-input" type="text" name="horaire_open_'+day+'_'+id+'" id="horaire_open_'+day+'_'+id+'" placeholder="Heure douverture" /> <input class="campandjoy_input w-input" type="text" name="horaire_close_'+day+'_'+id+'" id="horaire_close_'+day+'_'+id+'" placeholder="Heure de fermeture" /><img alt="+" onclick="addHoraires(\''+day+'\');" id="button_plus_'+day+'" name="button_plus_'+day+'" /></div>';
	$("#button_plus_"+day).remove();
	$("input[name^='horaire_close_"+day+"']:last").parent("div").after(varcode);
	$("#horaire_open_"+day+"_"+id).datetimepicker({
		startDate:new Date(),
		format:'H:00',
		datepicker:false,
		timepicker:true,
		step:30
	});
	$("#horaire_close_"+day+"_"+id).datetimepicker({
		startDate:new Date(),
		format:'H:00',
		datepicker:false,
		timepicker:true,
		step:30
	});
}
var delay;
$(document).ready(function(){
	$(".page_name").html("Restaurant > Ajout");
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
function plus_user(object)
{
	var d=parseInt($("input[id^='name_user']").length+1);
	if (d==1)
	{
		object.after('<div id="name_user_'+d+'_div" class="horizontal_form"><input class="campandjoy_input w-input" maxlength="256" id="name_user_'+d+'"  name="name_user_'+d+'" placeholder="Nom Ou Prénom de l\'utilisateur" type="text"><input class="campandjoy_input w-input" maxlength="256" id="id_user_'+d+'"  name="id_user_'+d+'" type="hidden">'+object.prop("outerHTML")+'</div>');
	}
	else
	{
		object.parent('div').after('<div id="name_user_'+d+'_div" class="horizontal_form"><input class="campandjoy_input w-input" maxlength="256" id="name_user_'+d+'"  name="name_user_'+d+'" placeholder="Nom Ou Prénom de l\'utilisateur" type="text"><input class="campandjoy_input w-input" maxlength="256" id="id_user_'+d+'"  name="id_user_'+d+'" type="hidden">'+object.prop("outerHTML")+'</div>');
	}
	object.remove();
	$("input[id='name_user_"+d+"']").on("keypress", function(){
		var o=$(this);
		var p=o.val();
		delay=Date.now();
		o.next("input").val("");
		setTimeout(function(){
			if (parseInt(delay+500)<=Date.now())
			{
				loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchUserByName.php')); ?>", {"nom" : p, "input" : o.attr("id")}, "#name_user_"+d+"_div", "after");
			}
		}, 500);
	});
}
function plus_choix(object, n)
{
	var d=parseInt($("input[id^='categorie_"+n+"']").length/2+1);
	if (d==1)
	{
		object.after('<label for="type_menu">Choix '+d+'</label><div id="categorie_'+n+'_'+d+'_div" class="horizontal_form"><input class="campandjoy_input w-input" type="text" name="categorie_'+n+'_'+d+'_nom" id="categorie_'+n+'_'+d+'_nom" placeholder="Tarte à la Pomme" /><input class="campandjoy_input w-input" type="number" name="categorie_'+n+'_'+d+'_prix" id="categorie_'+n+'_'+d+'_prix" placeholder="Prix en euros" />'+object.prop("outerHTML")+'</div>');
	}
	else
	{
		object.parent("div").after('<label for="type_menu">Choix '+d+'</label><div id="categorie_'+n+'_'+d+'_div" class="horizontal_form"><input class="campandjoy_input w-input" type="text" name="categorie_'+n+'_'+d+'_nom" id="categorie_'+n+'_'+d+'_nom" placeholder="Tarte à la Pomme" /><input class="campandjoy_input w-input" type="number" name="categorie_'+n+'_'+d+'_prix" id="categorie_'+n+'_'+d+'_prix" placeholder="Prix en euros" />'+object.prop("outerHTML")+'</div>');
	}
	object.remove();
}
function plus_categ(object)
{
	var d=parseInt($("input[id^='categorie_name']").length+1);
	if (d==1)
	{
		object.after('<div><label for="type_menu">Le nom de la catégorie:</label><div class="horizontal_form"><input type="text" class="campandjoy_input w-input" name="categorie_name_'+d+'" id="categorie_name_'+d+'" placeholder="Entéres, Plats, Dessert..." />'+object.prop("outerHTML")+'</div><span id="plus_choix_'+d+'" name="plus_choix_'+d+'" onclick="plus_choix($(this), '+d+');">+</span><br/></div>');
	}
	else
	{
		object.parent("div").parent("div").after('<div><label for="type_menu">Le nom de la catégorie:</label><div class="horizontal_form"><input type="text" class="campandjoy_input w-input" name="categorie_name_'+d+'" id="categorie_name_'+d+'" placeholder="Entéres, Plats, Dessert..." />'+object.prop("outerHTML")+'</div><span id="plus_choix_'+d+'" name="plus_choix_'+d+'" onclick="plus_choix($(this), '+d+');">+</span><br/></div>');
	}
	object.remove();
	$("#plus_choix_"+d).click();
}
$("#plus_user").click();
$("#plus_categ").click();
$( document ).ready(function() {
		$(".page_name").html("Administration");
	});
</script>