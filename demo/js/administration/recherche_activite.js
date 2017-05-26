var delay;
function launchSearch()
{
	delay=Date.now();
	setTimeout(function(){
		if (parseInt(delay+500)<=Date.now())
		{
			loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchActivites.php')); ?>", {"nom" : $("#nom").val(), "date" : $("#datepicker").val(), "user" : $("#users").val()}, ".section_title_wrapper", "after");
		}
	}, 500);
}
$("input[id='nom']").on("keypress", function(){
	launchSearch();
});
$("select[id='users']").on("change", function(){
	launchSearch();
});
loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchActivites.php')); ?>", {}, ".section_title_wrapper", "after");
$.datetimepicker.setLocale('fr');
$('#datepicker').datetimepicker({
	timepicker:false,
	formatDate:'d.m.y',
	format:'d/m/y',
	onSelectDate:function(ct,$i){
	  launchSearch();
	}
});