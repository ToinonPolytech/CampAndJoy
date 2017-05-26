<?php 
if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,faq.class.php,";
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
	if(!isStaff())
	{
		exit();
	}
	$db = new Database();
	$db->select('faq');		
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Gestion de la FAQ</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div id="retour"></div>
		<div class="section_title_wrapper">
			<h2 class="heading">Gérez votre Foire Aux Questions</h2>
			<p>Ici vous pouvez ajouter, modifier ou supprimer les questions qui vous sont régulièrement 
			posées et auxquelles vous souhaitez que votre client ait accès</p>
			<div class="section_divide"></div>
			<a class="action_link" href="#" onclick="$('#ajoutFAQ-hide').toggle('fast');$(this).remove();return false;">[Ajouter une question]</a>
			<div id="ajoutFAQ-hide" style="display:none">
				<label for="nom">Question</label>
				<input class="campandjoy_input w-input" type="text" name="question" id="question"/>
				<label for="nom">Réponse</label>
				<textarea class="campandjoy_input w-input" type="text" name="reponse" id="reponse"></textarea>
				<button class="primary_btn w-button" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('faq.controllerForm.php')); ?>',{question : $('#question').val(), reponse : $('#reponse').val()}, '#ajoutFAQ-hide', 'prepend', false); return false;">Ajouter</button>
			</div>
			<div class="custom_table">
				<div class="custom_table_head">
					<div class="ctl_head_line_color custom_table_line">
						<div class="custom_table_col">
							<h6 class="head_text">Question</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Réponse</h6>
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
						?>				
						<div class="ctl_body_line custom_table_line" id="faq_<?php echo $data["id"]; ?>">	
							<div class="custom_table_col">
									<div><b><?php echo htmlentities($data['question']);?></b></div>
								</div>
								<div class="custom_table_col">
									<div><i><?php echo htmlentities($data['reponse']);?></i></div>
								</div>							
								<div class="custom_table_col">	
										<a class="action_link" onclick="showModif('<?php echo $data['id'];?>');return false;" href="#">[Modifier]</a>
										<a class="action_link" data-ix="show-modal-msg" data-title="Suppression" data-message="Attention vous vous apprêtez à supprimer cette question." data-on-confirm="webflowCampandJoy('close-modal-msg'); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('supprimerFAQ.php')); ?>', {id :<?php echo $data['id'];?>}, '#retour', 'prepend');"  data-on-refuse="webflowCampandJoy('close-modal-msg');" data-type="error" href="#">[Supprimer]</a>
								</div>
						</div>
						<div class="ctl_body_line custom_table_line" id="modif_<?php echo $data['id'];?>" style="display:none">
							<div class="custom_table_col">
								<textarea class="campandjoy_input w-input" type="text" name="question" value="" id="questionModif_<?php echo $data['id'];?>"><?php echo htmlentities($data['question']);?></textarea>
							</div>
							<div class="custom_table_col">
								<textarea class="campandjoy_input w-input" type="text" name="reponse" value="" id="reponseModif_<?php echo $data['id'];?>"><?php echo htmlentities($data['reponse']);?></textarea>
							</div>
							<div class="custom_table_col">	
								<a class="action_link" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('faq.controllerForm.php')); ?>',{question : $('#questionModif_<?php echo $data['id'];?>').val(), reponse : $('#reponseModif_<?php echo $data['id'];?>').val(), id:<?php echo $data['id'];?>}, '#retour', 'prepend', false);return false;" href="#">[Valider]</a>
								<a class="action_link" onclick="showModif('<?php echo $data['id'];?>');return false;" href="#">[Annuler]</a>
							</div>						
						</div>			
						<?php					
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function showModif(idFAQ)
	{
		$('#faq_'+idFAQ).toggle('fast');
		$('#modif_'+idFAQ).toggle('fast');		
	}
	$( document ).ready(function() {
		$(".page_name").html("Administration");
	});
</script>
