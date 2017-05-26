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
	if(!isStaff())
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Administration - Services</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">Index</h2>
			<div class="section_divide"></div>
			<p class="paragraph">Que souhaitez-vous faire ?&nbsp;</p>
		</div>
		<div class="c_section">
			<div class="cs_col_1">
				<p class="s_paragraph">Pour trouver puis chercher/modifier un espace</p>
				<a class="s_btn w-button" href="/demo/administration/lieuCommun/recherche">Rechercher un lieu ?</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">Pour rajouter un espace commun<p>
				<a class="s_btn w-button" href="/demo/administration/lieuCommun/ajout">Créer un espace</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">Pour rajouter un restaurant<p>
				<a class="s_btn w-button" href="/demo/administration/restaurant/ajout">Ajouter un restaurant</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">Pour chercher/modifier un restaurant<p>
				<a class="s_btn w-button" href="/demo/administration/restaurant/recherche">Chercher un restaurant</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$( document ).ready(function() {
		$(".page_name").html("Administration");
	});
</script>