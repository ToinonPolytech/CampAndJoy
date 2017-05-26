 <?php
 if (!isset($require))
{
	$require="";
}
$require.="database.class.php,equipe.class.php,equipe_membres.class.php,user.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if(isset($_POST['id']) && isset($_SESSION['id'])){	
	$user = new User($_SESSION["id"]);
	$em = new Equipe_membres($_POST['id'],$_SESSION['id']);
	$equipe = new Equipe($_POST['id']);
	if($em->getPeutModifier() || isStaff())
	{	
		$database= new Database();
		$database2 = new Database();
		$database->select("equipe_membres", array("idEquipe" => $_POST['id']), "idUser");
		while ($data=$database->fetch())
		{
			$database2->create("notifications", array("idUser" => $data['idUser'], "titre" => "Votre équipe a été supprimée.", "message" => $user->getPrenom()." a supprimé l'équipe : ".$equipe->getNom(), "lien" => "", "date" => time()));
		}
		$equipe->setDeleted(true); 
		$equipe->saveToDb(); 
		?>
		<div class="message_block success" id='reponse_controller_msg'>
			<p>Equipe supprimée</p>
		</div>
		<script type="text/javascript">
			$("#<?php echo $_POST['id']; ?>").remove();
		</script>
		<?php
	}
	else 
	{
		?>
		<div class="message_block error" id='reponse_controller_msg'>
			<p>Vous n'avez pas les droits pour supprimer cette équipe</p>
		</div>
		<?php	
	}
}
else
{
	?>
	<div class="message_block error" id='reponse_controller_msg'>
		<p>Une erreur s'est produite lors de la suppression</p>
	</div>
	<?php	
}
?>