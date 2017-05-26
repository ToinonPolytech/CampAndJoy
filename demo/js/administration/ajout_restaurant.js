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