<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		header("Location:index.php");
		exit();
	}
?>
<div class="hero_section">
	<div class="w-container">
		<div class="horizontal_form">
			<div class="board-box board-left" >
				<h4>A la une dans notre camping :</h4>
				<p>Ce soir : la soirée kitch de la semaine. A ne pas rater ! <a href="#">[En savoir plus]</a><p>
			</div>
			<div class="board-box board-right">
				<h4>Température de la piscine : </h4>
				<h1 align="center">22°c</h1>
			</div>
		</div>
		<div class="horizontal_form">
			<div class="board-box board-left">
				<h4>Vos actualités</h4>
				<p>Vous avez une réservation pour Balade à Poney, le 22/06 à 14h30<p>
			</div>
			<div class="board-box board-right">
			<!-- widget meteo -->
				<div id="widget_bfac237c6ff8fb856e3ec4b8821d5ebd">
				<span id="t_bfac237c6ff8fb856e3ec4b8821d5ebd">Météo Montpellier</span>
				<span id="l_bfac237c6ff8fb856e3ec4b8821d5ebd"><a href="http://www.mymeteo.info/r/montpellier_k">Temps &agrave; Montpellier</a></span>
				<script type="text/javascript">
				(function() {
					var my = document.createElement("script"); my.type = "text/javascript"; my.async = true;
					my.src = "https://services.my-meteo.com/widget/js?ville=244&format=vertical&nb_jours=1&temps&icones&vent&precip&c1=393939&c2=a9a9a9&c3=e6e6e6&c4=ffffff&c5=4dd92c&c6=d21515&police=0&t_icones=1&x=160&y=123&d=0&id=bfac237c6ff8fb856e3ec4b8821d5ebd";
					var z = document.getElementsByTagName("script")[0]; z.parentNode.insertBefore(my, z);
				})();
				</script>
				</div>
				<!-- widget meteo -->
			</div>
		
				
				
		</div>
	</div>
</div>
