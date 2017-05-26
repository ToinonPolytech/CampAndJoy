<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	if(!isStaff())
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Recherche d'activité</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">Rechercher une activité</h2>
			<div class="section_divide"></div>
			<div class="fliter_block">
				<div class="w-form">
					<form class="horizontal_form">
						<input class="campandjoy_input w-input" id="nom" maxlength="256" name="nom" placeholder="Nom de l'activité" type="text"> 
						<input <?php if (isset($date)) { echo "value='".$date."'"; } ?> class="campandjoy_input w-input" data-name="datepicker" id="datepicker" maxlength="256" name="datepicker" placeholder="Date" type="text">
						<select id="users" name="users" class="w-select campandjoy_input">
							<option value="-1">L'animateur en charge</option>
							<?php
								$database->selectJoin("activities AS a", array(" users AS u ON u.id=idDirigeant "), array("access_level" => array("!=", "CLIENT")), array("DISTINCT u.nom", "u.prenom", "u.id"));
								while ($data=$database->fetch())
								{
									?>
									<option value="<?php echo $data["id"]; ?>"><?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?></option>
									<?php
								}
							?>
						</select> 
					</form>
					<a class="primary_btn w-button wrapp_btn_null" href="#" onclick='loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchActivites.php')); ?>", {}, ".section_title_wrapper", "after"); return false;'>Voir les dernières activités créées</a>
				</div>
			</div>
			<p class="paragraph">Vous pouvez trouver toutes les activités facilement grâce aux champs de recherche ci-dessus.<br/> Par défaut les 20 dernières activités créées sont affichés.&nbsp;</p>
		</div>
	</div>
</div>
<script type="text/javascript">
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

$(document).ready(function() {
		$(".page_name").html("Administration");
	});
</script>