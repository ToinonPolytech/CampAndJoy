<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,activities.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	
	if (!auth())
	{
		?>
		<script type="text/javascript">
			$( document ).ready(function() {
				window.location.replace("index.php");
			});
		</script>
		<?php
		exit();
	}
	$user = new User($_GET['id']);
	$photo = explode(',',$user->getPhoto());
	?>
	<div class="blur_bg"></div>
		<div class="welcome_msg" data-ix="onpageload">
			<h1 class="h1_color">Profil de <?php echo $user->getPrenom().' '.$user->getNom();?></h1>
		</div>
	<div class="hero_section">
		<div class="w-container">
			<div class="section_title_wrapper">
				<h2 class="heading"><?php echo $user->getPrenom().' '.strtoupper($user->getNom());?></h2>
				<div class="section_divide"></div>
				<img src="<?php echo $photo[0]; ?>" width="200px" height="200px" style="float:left;margin-right:10px"/>
				<p><?php if(!empty($user->getDescription())){echo $user->getDescription();}else{ echo "Cette personne pas encore renseigné de description";}?></p>
				<a class="link_contact_organizer">Contacter </a>
			</div>
		</div>
	</div>


	
