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
	$db = new Database();
	if (!isset($_GET["id"]) || !$db->count("partenaire", array("id" => $_GET["id"])))
	{
		?>
		<script type="text/javascript">
			$( document ).ready(function() {
				loadToMain('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('partenaires.php')); ?>', {}, '/demo/service/partenaire');
			});
		</script>
		<?php
		exit();
	}
	$id=$_GET["id"];
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$db->selectJoin("partenaire AS p", array(" users AS u ON idUser=u.id"), array("p.id" => $id), array("u.nom AS unom", "prenom", "idUser", "p.nom", "description", "mail", "url", "telephone", "photos"));
	$data=$db->fetch();
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Partenaire du Camping</h1>
	<p class="wm_p"><?php echo htmlentities($data["nom"]); ?></p>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">SES SERVICES</h2>
			<div class="section_divide"></div>
			<p class="paragraph">Voici la liste des services proposés par <?php echo htmlentities($data["nom"]); ?>.&nbsp;</p>
		</div>
		<div class="c_section">
			DESIGN A FAIRE ++ DB A FAIRE AUSSI
		</div>
	</div>
</div>
<script type="text/javascript">
	$( document ).ready(function() {
		$(".page_name").html("Partenaire");
		<?php if ($includeDone) { ?>
		Webflow.destroy();
		setTimeout(function(){ $(Webflow.ready); }, 1);
		<?php } ?>
	});
</script>