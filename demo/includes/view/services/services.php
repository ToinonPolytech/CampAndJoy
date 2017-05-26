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
	<h1 class="h1_color">Services du camping</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">NOS SERVICES</h2>
			<div class="section_divide"></div>
			<p class="paragraph">Profitez au mieux des services disponibles dans notre Camping.&nbsp;</p>
		</div>
		<div class="c_section">
			<?php
			$user=new User($_SESSION["id"]);
			$cuser=new Controller_User($user);
			if ($user->getUserInfos()->getClef()==$_COOKIE["clef"])
			{
				?>
				<div class="cs_col_1">
					<p class="s_paragraph">Plus besoin de venir faire la queue à l'accueil</p>
					<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/service/etatDesLieux">Réserver votre état des lieux</a>
				</div>
				<?php
			}
			?>
			<div class="cs_col_1">
				<p class="s_paragraph">Envie d'utiliser l'un de nos barbecues ?
				<br>Vous n'avez pas faim ? Vous êtes plutôt terrain de tennis ?</p>
				<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/service/nosEspaces">Consulter nos installations</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">Vous êtes plutôt restaurant aujourd'hui ?
				<br>Regardez la liste des restaurants les plus proches de vous !</p>
				<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/service/restaurant">Consulter les restaurants</a>
			</div>
			<div class="cs_col_1">
				<p class="s_paragraph">Envie de faire un tour de bateau ?
				<br>Ou vous préférez peut être les promenades à cheval ?</p>
				<a class="s_btn w-button" href="/demo/<?php echo LANG_USER; ?>/service/partenaire">Consulter les partenaires</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
    $(".page_name").html("Services");
});
</script>