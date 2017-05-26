<?php 
if (!isset($require))
{
	$require="";
}
$require.="problemeTechnique.class.php,problemeTechnique.controller.class.php,database.class.php";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if(isset($_POST['etat']) && isset($_POST['idPbTech'])) // Nos variables existent, alors on peut les utiliser
{
	?>
	<script type="text/javascript">
		$("div[id='reponse_controller_msg_read']").remove();
		$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
	</script>
	<?php	
	//manque sécu pour vérifier que le user staff peut changer l'état 
	$pbtech = new PbTech(htmlspecialchars($_POST['idPbTech']));
	$controllerPbTech = new Controller_PbTech($pbtech);
	if($pbtech->getSolved()!='RESOLU' || ($_POST['etat']=='EN_COURS' && $pbtech->getSolved()=='NON_RESOLU')){
		$pbtech->setSolved($_POST['etat']); 
	}
	$pbtech->setSolved(htmlspecialchars($_POST['etat']));

	if ($controllerPbTech->isGood())
	{	
		if ($_POST['etat']=='RESOLU')
		{
			$message="Votre problème technique a été résolu.";
		}
		else if ($_POST['etat']=='EN_COURS')
		{
			$message="Un membre de l'équipe technique s'occupe de votre problème.";
		}
		$database = new Database();
		$database->create("notifications", array("idUser" => $pbtech->getIdUser(), "titre" => "L'état de problème technique a été modifié.", "message" => $message, "lien" => "/demo/fr/problemeTechnique/mesProblemes", "date" => time()));
		$pbtech->saveToDb();
		if($_POST['etat']=='RESOLU')
		{
			?>
			<script type="text/javascript">
				$("#resoluButton").remove();
				$("#<?php echo $_POST["idPbTech"]; ?>").find("a[id='resolu']").remove();
			</script>
			<?php
		}
		?>
		<script type="text/javascript">
			$("#enCoursButton").remove();
			$("#<?php echo $_POST["idPbTech"]; ?>").find("a[id='en_cours']").remove();
			$("#<?php echo $_POST["idPbTech"]; ?>").find("div[id='resolution']").html("<?php if($_POST['etat']=='EN_COURS'){echo 'En cours';}else if($_POST['etat']=='RESOLU'){echo 'Résolu';} ?>");
		</script>
		
		<div class="message_block success" id='reponse_controller_msg'>
			<p>Problème marqué comme <?php if($_POST['etat']=='EN_COURS'){echo 'en cours';}else if($_POST['etat']=='RESOLU'){echo 'résolu';} ?></p>
		</div>		
		<?php		
	}	
	else
	{
		?>
		<div class="message_block error" id='reponse_controller_msg'>
			<p><?php echo $controllerPbTech->getError(); ?></p>
		</div>
		<?php
	}	
}
?> 