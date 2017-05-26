<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,problemeTechnique.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$db = new Database();
	$db->select("problemes_technique",array( 'idUsers' => $_SESSION['id']));
	$db2 = new Database(); 
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Mes problèmes</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">Mes problèmes techniques</h2>
			<div class="section_divide"></div>
			<div class="custom_table">
				<div class="custom_table_head">
					<div class="ctl_head_line_color custom_table_line">
						<div class="custom_table_col">
							<h6 class="head_text">Description</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Signalé le</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Résolution</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Action</h6>
						</div>
						
					</div>
				</div>
				<div class="custom_table_body">
					<?php
					while($data=$db->fetch())
					{	
						$pbInfoExiste=$db2->count('problemes_technique_info', array('idPbTech' => $data['id']));
						?>			
						<div class="ctl_body_line custom_table_line">
							<div class="custom_table_col">
								<div><?php echo $data['description'];?></div>
							</div>
							<div class="custom_table_col">
								<div><?php echo date("d/m/y H:m",$data['time_start']);?></div>
							</div>
							<div class="custom_table_col">
								<div><?php echo $data['solved'];?></div>
							</div>
							
							<div class="custom_table_col">
							<?php
								if ($data['solved']=="NON_RESOLU")
								{
									?>
									<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/problemeTechnique/modifier/<?php echo $data['id'];?>">[Modifier]</a>
									<?php
									if(!$pbInfoExiste)
									{
									?>
										<a class="action_link" href="#" data-on-confirm="webflowCampandJoy('close-modal-msg'); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('supprimerProblemeTechnique.php')); ?>', {id : <?php echo $data["id"]; ?>}, '.custom_table_body', 'before');" data-ix="show-modal-msg" data-title="Supprimer mon signalement" data-message="Êtes vous certain de supprimer le signalement ?" data-on-refuse="webflowCampandJoy('close-modal-msg');" data-type="error">[Supprimer]</a>			
									<?php
									}
								}
								else
								{
									?>
									<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/problemeTechnique/vue/<?php echo $data['id'];?>">[Voir plus]</a>
									<?php
								}
							?>
							</div>
						</div>
					<?php	
					}
					?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" >
$( document ).ready(function() {
	$(".page_name").html("Mes problèmes");
});
</script>