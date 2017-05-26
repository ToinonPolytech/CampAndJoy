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
	$is_restaurateur=false;
	$database->select("restaurant", array("idsUsers" => array(" LIKE ", "%".$_SESSION["id"]."%")), "idsUsers");
	while ($data=$database->fetch() && !$is_restaurateur)
	{
		$temp=explode(",", $data["idsUsers"]);
		foreach ($temp as $v)
		{
			if ($v==$_SESSION["id"])
				$is_restaurateur=true;
		}
	}
	if((!isStaff() && !$is_restaurateur)  || !$cuser->can("CAN_ADD_RESTAURANT"))
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Recherche de Restaurant</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">Rechercher un restaurant</h2>
			<div class="section_divide"></div>
			<div class="fliter_block">
				<div class="w-form">
					<form class="horizontal_form">
						<input class="campandjoy_input w-input" id="nom" maxlength="256" name="nom" placeholder="Nom du restaurant" type="text"> 
					</form>
					<a class="primary_btn w-button wrapp_btn_null" href="#" onclick='loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchRestaurant.php')); ?>", {}, ".section_title_wrapper", "after"); return false;'>Voir tous les restaurants</a>
				</div>
			</div>
			<p class="paragraph">Vous pouvez trouver tous les restaurants, que vous pouvez administrer, facilement grâce aux champs de recherche ci-dessus.<br/> Par défaut ils sont tous affichés.&nbsp;</p>
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
				loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchRestaurant.php')); ?>", {"nom" : $("#nom").val()}, ".section_title_wrapper", "after");
			}
		}, 500);
	}
	$("input[id='nom']").on("keypress", function(){
		launchSearch();
	});
	loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchRestaurant.php')); ?>", {}, ".section_title_wrapper", "after");
</script>