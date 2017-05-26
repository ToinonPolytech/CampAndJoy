<?php
if (!isset($require))
{
	$require="";
}

$require.="problemeTechnique.class.php,database.class.php";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");

if(isset($_POST['id']) && auth())
{
	?>
	<script type="text/javascript">
		$("div[id='reponse_controller_msg_read']").remove();
		$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
	</script>
	<?php	
	$pbTech = new PbTech($_POST['id']);
	$db = new Database(); 
	if ($pbTech->getIdUser()==$_SESSION["id"] && !$db->count('problemes_technique_info', array('idPbTech' => $_POST['id'])) && $pbTech->getSolved()=="NON_RESOLU")
	{
		$pbTech->setDeleted(true);
		$pbTech->saveToDb();
		?>
		<div class="message_block success" id='reponse_controller_msg'>
			<p>Le signalement du problème a bien été supprimé</p>
		</div>
		<?php
		
	}
}
?>