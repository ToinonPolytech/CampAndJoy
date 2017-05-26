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
		$("#lieu_autre").show();
	}
	else
	{
		$("#lieu_autre").hide();
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
	dataForm=dataForm+"&type="+tags;
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
});
$(document).ready(function() {
	$(".page_name").html("Administration");
});