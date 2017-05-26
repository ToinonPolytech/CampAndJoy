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
		
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Un problème ? Une question ?</h1>
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
				<p class="s_paragraph">Une question ? Besoin d'une information ?</p>
				<a class="s_btn w-button" href="#">Nous contacter</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">Vous rencontrez un problème ?</p>
				<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/problemeTechnique/signaler">Signaler un incident technique</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">A la recherche d'une information ? </p>
				<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/FAQ">Consulter notre Foire Aux Questions</a>
			</div>
			<?php 
			$db= new Database(); 
			if($db->count('problemes_technique',array('idUsers' => $_SESSION['id']))>0)
			{
				?>
				<div class="cs_col_1">
					<p class="s_paragraph"> Où en sont mes problèmes ? </p>
					<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/problemeTechnique/mesProblemes">Consulter les incidents déjà signalés</a>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$(".page_name").html("Contact");
	});
</script>