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
	$db = new Database();
	$db->setOrderCol("p.nom");
	$db->selectJoin("partenaire AS p", array(" users AS u ON idUser=u.id"), "", array("u.nom AS unom", "prenom", "p.id", "idUser", "p.nom", "description", "mail", "url", "telephone", "photos"));
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Partenaires du Camping</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">LES PARTENAIRES</h2>
			<div class="section_divide"></div>
			<p class="paragraph">Voici la liste de nos partenaires et de leurs services.&nbsp;</p>
		</div>
		<div class="c_section">
		<?php
			while ($data=$db->fetch())
			{
				$photos=explode(",", $data["photos"]);
				$photos_basic="https://placehold.it/300";
				$name="300.png";
				if (isset($photos[0]))
				{
					$photos_basic=$photos[0];
					$infos_photos=explode("/", $photos_basic);
					$name=$infos_photos[count($infos_photos)-1];
				}
				?>
				<div class="cs_sub_section">
					<div class="cs_col2">
						<div class="restaurant_picture_block">
							<a class="restauration_gallery w-inline-block w-lightbox" href="#">
								<img class="restauration_thumb" sizes="(max-width: 479px) 91vw, (max-width: 767px) 88vw, (max-width: 991px) 35vw, 372px" src="<?php echo $photos_basic; ?>" srcset="<?php echo $photos_basic; ?>">
								<div class="restauration_gallery_overlay" data-ix="show-gallery-icon">
									<img class="restauration_gallery_icon" data-ix="hide-gallery-icon" src="images/search.svg">
								</div>
								<script class="w-json" type="application/json">
									{ "items": [{
										"type": "image",
										"_id": "<?php echo uniqid(); ?>",
										"fileName": "<?php echo $name; ?>",
										"origFileName": "<?php echo $name; ?>",
										"width": 300,
										"height": 300,
										"url": "<?php echo $photos_basic; ?>"
									}] }
								</script>
							</a>
						</div>
					</div>
					<div class="cs_col3 w-clearfix">
						<div class="restaurant_name"><?php echo htmlentities($data["nom"]); ?></div>
						<div class="restaurant_place_and_cost">
							<span class="place_available"><?php if (!empty($data["url"])) { ?><strong>Site web :</strong> <a target="new" href="<?php echo htmlentities($data["url"]); ?>"><?php echo htmlentities($data["url"])."</a>"; } ?></span>&nbsp;
						</div>
						<p class="restaurant_description"><?php echo str_replace("\n", "<br/>", $data["description"]); ?></p>
						<span><b><a href="/demo/service/partenaire/<?php echo $data["id"]; ?>" class="emn_link">En savoir plus</a></b></span>
						<div class="program_event_master_block" id="<?php echo $data["id"]; ?>">
							<div class="organizer_block w-clearfix">
								<img class="image-2" src="images/mathiew.svg">
								<div class="event_master_name">
									<a href="#" class="emn_link"><?php echo $data["unom"]." ".$data["prenom"]; ?></a>
								</div>
								Pour le contacter : 
								<a class="link_contact_organizer" href="mailto:<?php echo htmlentities($data["mail"]); ?>">Mail <?php echo htmlentities($data["mail"]); ?></a> 
								<a class="link_contact_organizer" href="tel:<?php echo htmlentities($data["telephone"]); ?>">Tél. <?php echo htmlentities($data["telephone"]); ?></a>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$( document ).ready(function() {
		$(".page_name").html("Partenaires");
		<?php if ($includeDone) { ?>
		Webflow.destroy();
		setTimeout(function(){ $(Webflow.ready); }, 1);
		<?php } ?>
	});
</script>