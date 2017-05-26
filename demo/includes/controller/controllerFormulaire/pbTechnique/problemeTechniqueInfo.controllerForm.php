<?php 
if (!isset($require))
{
	$require="";
}
$require.="user.class.php,user.controller.class.php,problemeTechnique.class.php,problemeTechniqueInfo.class.php,problemeTechniqueInfo.controller.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if (!auth())
	exit();

if(isset($_POST['idPbTech']) && isset($_POST['message'])) // Nos variables existent, alors on peut les utiliser
{
	?>
	<script type="text/javascript">
		$("div[id='reponse_controller_msg_read']").remove();
		$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
	</script>
	<?php	
	$pbTech=new PbTech($_POST["idPbTech"]);
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$userSignalement=new User($pbTech->getIdUser());
	if ($_SESSION["id"]!=$pbTech->getIdUser() && (!isStaff() || !$cuser->can(CAN_MANAGE_PROBLEME_TECHNIQUE)))
		exit();
	
	$objectPbTechInfo = new PbTechInfo(NULL,$_POST['idPbTech'], $_SESSION['id'], time(), $_POST['message']);
	$controllerPbTechInfo = new Controller_PbTechInfo($objectPbTechInfo);
	if ($controllerPbTechInfo->isGood())
	{
		$objectPbTechInfo->saveToDb();
		if ($_SESSION["id"]==$userSignalement->getId())
		{
		?>
		<div class="ticket-r">
			<div class="ticket-receiver">
				<div class="t-top">
					<div class="from-name">De : Vous</div>
				</div>
				<div class="t-content">
					<p><?php echo htmlentities($_POST["message"]); ?></p>
				</div>
				<div class="t-botom">
					<span><?php echo date("d/m/Y H:i"); ?></span>
				</div>
			</div>
		</div>
		<?php
		}
		else
		{
		?>
		<div class="ticket-s">
			<div class="ticket-sender">
				<div class="t-top">
					<div class="from-name">De : Vous</div>
				</div>
				<div class="t-content">
					<p><?php echo htmlentities($_POST["message"]); ?></p>
				</div>
				<div class="t-botom">
					<span><?php echo date("d/m/Y H:i"); ?></span>
				</div>
			</div>
		</div>
		<?php	
		}
	}	
	else
	{
		?>
		<div class="message_block error" id='reponse_controller_msg'>
			<p><?php echo $controllerPbTechInfo->getErrors(); ?></p>
		</div>
		<?php
	}	
}
?> 