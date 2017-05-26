<?php 
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,faq.class.php,faq.controller.class.php,faq.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");

if(isset($_POST['id'])){
	
	$faq = new FAQ($_POST['id']);
	$faq->setDeleted(true);
	$faq->saveToDb();
	?>
	<div class="message_block success" id='reponse_controller_msg'>
		<p>Question supprimée avec succès</p>
	</div>
	<script type="text/javascript">
		$("#faq_<?php echo htmlentities($_POST['id']); ?>").toggle("fast");
	</script>
	<?php	
}
else
{
	?>
	<div class="message_block error" id='reponse_controller_msg'>
		<p>Erreur lors de la suppression</p>
	</div>
	<?php
}