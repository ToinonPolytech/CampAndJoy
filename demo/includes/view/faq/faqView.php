<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
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
		$db = new Database();
		$db->select('faq');
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Foire Aux Questions</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">FAQ : Frequently Asked Questions</h2>
			<?php if(isStaff()){ ?> <a href="demo/<?php echo LANG_USER;?>/administration/FAQ/gestion" style="float:right">[Gérer les FAQ]</a><?php } ?>
			<div class="section_divide"></div>
			<div align="center">
				<p class="paragraph">Trouvez ici toutes les questions qui nous sont régulièrement posées et leurs réponses. Si vous ne trouvez pas la vôtre n'hésitez pas à <a href="#">nous contacter</a>&nbsp;</p>
			</div>
			<?php 
			while($data=$db->fetch())
			{
				?>
					<h4><?php echo $data['question'];?>  <i class="fa fa-plus-square" onclick="$('#sfaq_<?php echo $data['id'];?>').toggle('fast');return false;" aria-hidden="true"></i></h4>
					<div id="faq_<?php echo $data['id'];?>" style="display:none">
						<p><?php echo $data['reponse'];?></p>
					</div>
					<div class="customcaj_separator"></div>		
				<?php
			}
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$( document ).ready(function() {
		$(".page_name").html("FAQ");
	});
</script>