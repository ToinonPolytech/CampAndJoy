var delay;
var lastSearch="";
function launchSearch(inp)
{
	delay=Date.now();
	setTimeout(function(){
		if (parseInt(delay+500)<=Date.now())
		{
			if (lastSearch!=$("#name_user").val())
			{
				lastSearch=$("#name_user").val();
				loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('ajoutUserPartenaire.php')); ?>",{"nom" : lastSearch}, "#name_user", "after");
			}
		}
	}, 500);
}
$("input[id='name_user']").on("keypress", function(){
	launchSearch("name_user");
});
$( document ).ready(function() {
	initAddPhotos();
});