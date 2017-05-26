function addHoraires(day)
{
	var text=String($("input[name^='horaire_close_"+day+"']:last").attr("id"));
	var id=parseInt(text.replace("horaire_close_"+day+"_", ""))+parseInt(1);
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
function horaireButton()
{
	if ($('#horaire_hide').css('display')=="none")
	{ 
		$('#horaire').val('1'); 
	} 
	else 
	{ 
		$('#horaire').val('0'); 
	} 
	$('#horaire_hide').toggle('fast'); 
}
$(document).ready(function() {
	initAddPhotos();
	$("input[name^='horaire_close'],input[name^='horaire_open']").datetimepicker({
		startDate:new Date(),
		format:'H:i',
		datepicker:false,
		timepicker:true,
		step:30
	});
});