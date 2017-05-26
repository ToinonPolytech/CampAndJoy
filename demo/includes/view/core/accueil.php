<?php 
if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	
if (!auth())
{
	exit();
}

?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Bienvenue dans notre camping 5 étoiles</h1>
	<p class="wm_p">Nous restons à votre disposition pour répondre à toutes vos questions.</p>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="row w-row">
			<div class="overall_col w-clearfix w-col w-col-4">
				<img class="intro_icon" src="images/camping.svg">
				<h4 class="color_2">Activités à venir </h4>
				<p>Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte.</p>
			</div>
			<div class="overall_col w-clearfix w-col w-col-4">
				<img class="intro_icon" src="images/code.svg">
				<h4 class="color_2">Nos Services</h4>
				<p>Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte.</p>
			</div>
			<div class="overall_col w-clearfix w-col w-col-4">
				<img class="intro_icon" src="images/up-right-arrow-in-a-circle.svg">
				<h4 class="color_2">Notre objectif</h4>
				<p>Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un peintre anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte.</p>
			</div>
		</div>
	</div>
</div>
<div class="hero_section proccess">
	<h4 class="color_1 hero_center_title">NOS PARTENAIRES</h4>
	<div class="flex_container w-container">
		<div class="flex_container_col" data-ix="process1"><img class="image" src="https://d3e54v103j8qbb.cloudfront.net/img/image-placeholder.svg">
			<div>
				<h5>Partner Name</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim&nbsp;</p>
			</div>
		</div>
		<div class="flex_container_col" data-ix="process1"><img class="image" src="https://d3e54v103j8qbb.cloudfront.net/img/image-placeholder.svg">
			<div>
				<h5>Partner Name</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim&nbsp;</p>
			</div>
		</div>
		<div class="flex_container_col" data-ix="process1"><img class="image" src="https://d3e54v103j8qbb.cloudfront.net/img/image-placeholder.svg">
			<div>
				<h5>Partner Name</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim&nbsp;</p>
			</div>
		</div>
		<div class="flex_container_col" data-ix="process1"><img class="image" src="https://d3e54v103j8qbb.cloudfront.net/img/image-placeholder.svg">
			<div>
				<h5>Partner Name</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim&nbsp;</p>
			</div>
		</div>
		<div class="flex_container_col" data-ix="process1"><img class="image" src="https://d3e54v103j8qbb.cloudfront.net/img/image-placeholder.svg">
			<div>
				<h5>Partner Name</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim&nbsp;</p>
			</div>
		</div>
		<div class="flex_container_col" data-ix="process1"><img class="image" src="https://d3e54v103j8qbb.cloudfront.net/img/image-placeholder.svg">
			<div>
				<h5>Partner Name</h5>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse varius enim&nbsp;</p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$("div[class='flex_container_col']").each(function(){
		$(this).removeAttr("data-ix");
	});
</script>