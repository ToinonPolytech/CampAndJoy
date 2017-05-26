<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,problemeTechnique.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
		exit();

	if (!isset($_GET["id"]))
		exit();

	$pbTech=new PbTech($_GET["id"]);
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$userSignalement=new User($pbTech->getIdUser());
	if ($_SESSION["id"]!=$pbTech->getIdUser() && (!isStaff() || !$cuser->can(CAN_MANAGE_PROBLEME_TECHNIQUE)))
		exit();
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Signalement d'un problème technique</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<h4>
			<?php if ($userSignalement->getId()!=$_SESSION["id"])
			{
			?>
			Signalement de <?php echo htmlentities($userSignalement->getPrenom()." ".$userSignalement->getNom()); ?>
			<?php
			}
			else
			{
				echo "Votre signalement";
			}
			?>
		</h4>
		<?php
		foreach ($pbTech->getPhotos() as $src)
		{
			if (!empty($src))
			{
				$photos_basic=$src[0];
				$infos_photos=explode("/", $photos_basic);
				$name=$infos_photos[count($infos_photos)-1];
				?>
				<a class="restauration_gallery w-inline-block w-lightbox" href="#">
					<img width="200" height="200" class="restauration_thumb" src="<?php echo htmlentities($src); ?>" srcset="<?php echo htmlentities($src); ?>">
					<div class="restauration_gallery_overlay" data-ix="show-gallery-icon">
						<img class="restauration_gallery_icon" data-ix="hide-gallery-icon" src="images/search.svg">
					</div>
					<script class="w-json" type="application/json">
						{ "items": [{
							"type": "image",
							"_id": "<?php echo uniqid(); ?>",
							"fileName": "<?php echo htmlentities($name); ?>",
							"origFileName": "<?php echo htmlentities($name); ?>",
							"width": 300,
							"height": 300,
							"url": "<?php echo htmlentities($src); ?>"
						}] }
					</script>
				</a>
				<?php
			}
		}
		?>
		<br/>
		<label class="control control--radio"> Dans le logement
			  <input disabled id="isBungalow" name="isBungalow" type="radio" value="true" <?php if ($pbTech->getIsBungalow()) { echo "checked"; } ?> >
			  <div class="control__indicator"></div>
		</label>
		<label class="control control--radio"> Autres
			  <input disabled id="isBungalow" name="isBungalow" type="radio" value="false" <?php if (!$pbTech->getIsBungalow()) { echo "checked"; } ?>>
			  <div class="control__indicator"></div>
		</label>	 
		<div class="ticket-r">
			<div class="ticket-receiver">
				<div class="t-top">
					<div class="from-name">De : <?php if ($userSignalement->getId()!=$_SESSION["id"]) { echo htmlentities($userSignalement->getPrenom()." ".$userSignalement->getNom()); } else { echo "Vous"; } ?></div>
				</div>
				<div class="t-content">
					<p><?php echo htmlentities($pbTech->getDescription()); ?></p>
				</div>
				<div class="t-botom">
					<span><?php echo date("d/m/Y H:i", $pbTech->getTimeCreated()); ?></span>
				</div>
			</div>
		</div>
		<?php
			$database=new Database();
			$database->setOrderCol("problemes_technique_info.id");
			$database->setAsc();
			$database->selectJoin("problemes_technique_info", array(" users ON idUser=users.id "), array("idPbTech" => $_GET["id"]), array("time", "idUser", "message", "nom", "prenom"));
			$lastAnswer=$userSignalement->getId();
			while ($data=$database->fetch())
			{
				if ($data["idUser"]==$userSignalement->getId())
				{
				?>
				<div class="ticket-r">
					<div class="ticket-receiver">
						<div class="t-top">
							<div class="from-name">De : <?php if ($data["idUser"]!=$_SESSION["id"]) { echo htmlentities($data["prenom"]." ".$data["nom"]); } else { echo "Vous"; } ?></div>
						</div>
						<div class="t-content">
							<p><?php echo htmlentities($data["message"]); ?></p>
						</div>
						<div class="t-botom">
							<span><?php echo date("d/m/Y H:i", $data["time"]); ?></span>
						</div>
					</div>
				</div>
				<?php
				}
				else
				{
				?>
				<div class="ticket-s">
					<div class="ticket-sender">
						<div class="t-top">
							<div class="from-name">De : <?php if ($data["idUser"]!=$_SESSION["id"]) { echo htmlentities($data["prenom"]." ".$data["nom"]); } else { echo "Vous"; } ?></div>
						</div>
						<div class="t-content">
							<p><?php echo htmlentities($data["message"]); ?></p>
						</div>
						<div class="t-botom">
							<span><?php echo date("d/m/Y H:i", $data["time"]); ?></span>
						</div>
					</div>
				</div>
				<?php	
				}
				$lastAnswer=$data["idUser"];
			}
			if (isStaff() || $lastAnswer!=$_SESSION["id"])
			{
				?>
				<form id="message_form" name="message_form">
					<textarea id="message" name="message" class="campandjoy_input w-input" placeholder="Votre message..."></textarea>
					<button class="primary_btn w-button" onclick="loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("problemeTechniqueInfo.controllerForm.php")); ?>', {idPbTech:<?php echo $_GET["id"]; ?>, message:$('#message').val()}, '#message_form', 'before'); return false;">Répondre</button>
				</form>
				<?php
			}
		?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$(".page_name").html("Problème > Consulter");
	});
</script>