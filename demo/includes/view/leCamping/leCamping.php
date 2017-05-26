<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Le camping</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">LE CAMPING</h2>
			<div class="section_divide"></div>
			<p class="paragraph">Informations générales sur notre camping.&nbsp;</p>
		</div>
		<div class="c_section">
			<div class="cs_col_1">
				<p class="s_paragraph">Quelle est la vitesse maximale autorisée dans le camping ? A quelle heure ferme les grilles ?</p>
				<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/leCamping/reglement">Consulter le règlement du camping</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">Qui gère notre camping ? Quels sont les noms des animateurs ?
				<br>Qui sont les techniciens qui oeuvrent dans l'ombre ? </p>
				<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/leCamping/notreEquipe">Présentation de l'équipe</a>
			</div>					
		</div>
	</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
    $(".page_name").html("Le Camping");
});
</script>