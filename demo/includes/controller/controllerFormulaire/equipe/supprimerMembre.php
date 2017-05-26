<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,equipe_membres.class.php,equipe_membres.controller.class.php,equipe.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	if(isset($_POST['idUser']) && isset($_POST['idEquipe']) && $_SESSION["id"]!=$_POST['idUser'])
	{
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php	 	
		$em1 = new Equipe_Membres($_POST['idEquipe'],$_SESSION['id']); 
		if($em1->getPeutModifier())
		{
			$database=new Database();
			$database->select("users", array("id" => $_POST['idUser']), array("nom", "prenom"));
			$data=$database->fetch();
			$em2 = new Equipe_Membres($_POST['idEquipe'],$_POST['idUser']);	
			if($em2->getPeutModifier()!=NULL)
			{
				$em2->setDeleted(true);
				$em2->saveToDb();
				$user = new User($_SESSION["id"]);
				$equipe = new Equipe($_POST['idEquipe']);
				
				$database->create("notifications", array("idUser" => $_POST['idUser'], "titre" => "Vous avez été renvoyé d'une équipe.", "message" => $user->getPrenom()." vous a retiré de ".$equipe->getNom(), "lien" => "", "date" => time()));
				
				?>
				<div class="message_block success" id='reponse_controller_msg'>
					<p><?php echo htmlentities($data["prenom"]." ".$data["nom"]); ?> n'est dorénavant plus dans votre équipe.</p>
				</div>
				<script type="text/javascript">
					$("#user_<?php echo htmlentities($_POST['idUser']); ?>").toggle("medium", function(){ $(this).remove(); });
					$("#reponse_controller_msg_read").fadeOut(10000, function(){ $(this).remove(); });
					$("#<?php echo $_POST['idEquipe']; ?>").children(".custom_table_col").next(".custom_table_col").children("div").html(parseInt($("#<?php echo $_POST['idEquipe']; ?>").children(".custom_table_col").next(".custom_table_col").children("div").html())-parseInt(1));
				</script>
				<?php
			}
			else
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p><?php echo htmlentities($data["prenom"]." ".$data["nom"]); ?> n'est pas dans votre équipe.</p>
				</div>
				<?php
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p>Vous ne pouvez pas modifier l'équipe.</p>
			</div>
			<?php
		}
	}
?>