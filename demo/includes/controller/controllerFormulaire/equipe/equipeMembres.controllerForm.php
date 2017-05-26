<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,user.class.php,equipe_membres.class.php,equipe_membres.controller.class.php,equipe.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if (!auth())
{
	exit();
}
if(isset($_POST['idUser']) && isset($_POST['idEquipe']))
{
	?>
	<script type="text/javascript">
		$("div[id='reponse_controller_msg_read']").remove();
		$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
	</script>
	<?php	 	
	$user = new User($_SESSION["id"]);
	$em1 = new Equipe_Membres($_POST['idEquipe'],$_SESSION['id']); 
	$equipe = new Equipe($_POST['idEquipe']);
	if($em1->getPeutModifier() && $_POST["idUser"]!=$_SESSION["id"])
	{
		$em2 = new Equipe_Membres($_POST['idEquipe'],$_POST['idUser']);	
		$oldPeutModifier=$em2->getPeutModifier();
		if (isset($_POST["droits"]) && $_POST["droits"]!="false")
		{
			$em2->setPeutModifier(1);
		}
		else
		{
			$em2->setPeutModifier(0);
		}
		$cem=new Controller_Equipe_Membres($em2);
		if($cem->isGood())
		{
			$db= new Database();
			$em2->saveToDb();
			?>
			<div class="message_block success" style="width:100%;text-align:center;" id='reponse_controller_msg'>
				<p>
				<?php
				$db->select("users", array("id" => $_POST["idUser"]), array("nom", "prenom"));
				$data=$db->fetch();
				if ($oldPeutModifier==NULL)
				{
					$db->create("notifications", array("idUser" => $_POST['idUser'], "titre" => "Vous avez été invité dans une équipe.", "message" => $user->getPrenom()." vous a invité dans ".$equipe->getNom(), "lien" => "/demo/fr/equipe/mesEquipes", "date" => time()));
					echo htmlentities($data["prenom"]." ".$data["nom"]." ");
					?>
					a bien été rajouté dans votre équipe.
					<script type="text/javascript">
						$("#<?php echo $_POST['idEquipe']; ?>").children(".custom_table_col").next(".custom_table_col").children("div").html(parseInt($("#<?php echo $_POST['idEquipe']; ?>").children(".custom_table_col").next(".custom_table_col").children("div").html())+parseInt(1));
					</script>
					<?php
				}
				else
				{
					?>
					Les droits de <?php echo htmlentities($data["prenom"]." ".$data["nom"]); ?> ont été mis à jour.
					<?php
				}
				?>
				</p>
				<script type="text/javascript">
					$("#ajoutMb_<?php echo $_POST['idEquipe']; ?>").toggle().find("input[id='name_user']").val("");
					$("#resultSearch.saw").remove();
					$("#reponse_controller_msg_read").fadeOut(10000, function(){ $(this).remove(); });
				</script>
			</div>
			<?php
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $cem->getError(); ?></p>
			</div>
			<?php
		}
	}
}
?>