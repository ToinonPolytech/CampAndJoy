<?php 
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,faq.class.php,faq.controller.class.php,faq.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if(isset($_POST['question']) && isset($_POST['reponse']))
{
	$faq = new FAQ(NULL,$_POST['question'],$_POST['reponse']);
	$faqController = new Controller_FAQ($faq);
	if($faqController->isGood())
	{
		if(isset($_POST['id']))
		{
			$faq->setId($_POST['id']);
		}
		$faq->saveToDb(); 
		?>
		<div class="message_block success" id='reponse_controller_msg'>
			<p>Question <?php if(isset($_POST['id'])){echo 'modifiée';}else{echo 'ajoutée';} ?></p>
		</div>
		<?php	
		if(isset($_POST['id']))
		{
			?>
			<script type="text/javascript">
			$('#modif_<?php echo $_POST['id'];?>').toggle('fast');
			//je cherche à modifier le contenu de la balise qu'on a modifié pour l'afficher à nouveau mais ne fonctionne pas 
			$('#faq_<?php echo $_POST['id'];?>').children(".custom_table_col").first().html("<?php echo htmlentities($_POST['question']); ?>");
			$('#faq_<?php echo $_POST['id'];?>').children(".custom_table_col").first().next().html("<?php echo htmlentities($_POST['reponse']); ?>");
			$('#faq_<?php echo $_POST['id'];?>').toggle('fast');
			</script>
			<?php
		}
	}
	else
	{
		?>
		<div class="message_block error" id='reponse_controller_msg'>
			<p><?php echo $faqController->getError();?></p>
		</div>
		<?php
	}
}
?> 
