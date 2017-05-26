$("input[type='radio']").click(function(){
	if (!$("#part_1bis").is(":visible"))
	{
		$("#part_1bis").toggle("fast");
	}
	if ($(this).val()=="true")
	{
		$("#description").attr("placeholder", "Description de votre problème");
	}
	else
	{
		$("#description").attr("placeholder", "Pensez à décrire le lieu de votre problème et la description de celui ci");
	}
});
$( document ).ready(function() {
	initAddPhotos();
});