<?php
if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,problemeTechnique.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if (!auth())
{
	exit();
}

$db = new Database();
$db->setOrderCol('time_start');
$db->setDesc(); 
$db->select("problemes_technique");
$db2 = new Database(); 


?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Problèmes techniques signalés</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">Problèmes techniques signalés</h2>
			<div class="section_divide"></div>
			<div id="retour"></div>
			<div class="custom_table">
				<div class="custom_table_head">
					
					<div class="ctl_head_line_color custom_table_line">
						<div class="custom_table_col">
							<h6 class="head_text">Signalé par</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Date</h6>
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
						$db2->select("users",array('id' => $data['idUsers']),array('nom','prenom'));
						$user = $db2->fetch();
						?>			
						<div class="ctl_body_line custom_table_line" id="<?php echo $data["id"]; ?>">
							<div class="custom_table_col">
								<div><?php echo htmlentities($user['prenom'])." ".htmlentities($user['nom']); ?></div>
							</div>
							<div class="custom_table_col">
								<div><?php echo date("d/m/y H:m",$data['time_start']);?></div>
							</div>
							<div class="custom_table_col">
								<div id="resolution"><?php if($data['solved']=='EN_COURS'){echo 'En cours';}else if($data['solved']=='RESOLU'){echo 'Résolu';}else{echo 'En attente';}?></div>
							</div>
							
							<div class="custom_table_col">
								<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/problemeTechnique/vue/<?php echo $data['id'];?>">[Voir/Répondre]</a>
								<?php
								if($data['solved']!='EN_COURS' && $data['solved']!='RESOLU')
								{
								?>
								<a class="action_link" href="#" id="en_cours" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('etatProblemeTechnique.php')); ?>', {etat : 'EN_COURS', idPbTech :<?php echo $data['id'];?>}, '#retour', 'prepend'); return false;">[En cours]</a>
								<?php 
								}
								if($data['solved']!='RESOLU')
								{
								?>
								<a class="action_link" href="#" id="resolu" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('etatProblemeTechnique.php')); ?>', {etat : 'RESOLU', idPbTech :<?php echo $data['id'];?>}, '#retour', 'prepend'); return false;">[Résolu]</a>								
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
<script type="text/javascript">
	$( document ).ready(function() {
		$(".page_name").html("Administration");
	});
</script>