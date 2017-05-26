<?php 
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,equipe.class.php,equipe.controller.class.php,equipe_membres.class.php,equipe_membres.controller.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if(isset($_POST['nom']))
{
	$equipe = new Equipe(NULL,$_POST['nom'],time(),0);
	$equipeController = new Controller_Equipe($equipe);
	if($equipeController->isGood())
	{
		$equipe->saveToDb();		
		$equipe_mb= new Equipe_Membres($equipe->getId(),$_SESSION['id'],1); 
		$equipe_mbController = new Controller_Equipe_Membres($equipe_mb);
		if($equipe_mbController->isGood())
		{
			$equipe_mb->saveToDb(); 
			?>
			<div class="message_block success" id='reponse_controller_msg'>
				<p>Equipe ajoutée</p>
			</div>
			<script type="text/javascript">
				$(".custom_table_body").prepend('<div id="<?php echo $equipe->getId(); ?>" class="ctl_body_line custom_table_line"><div class="custom_table_col"><div><?php echo htmlentities($equipe->getNom()); ?></div></div><div class="custom_table_col"><div>0</div></div><div class="custom_table_col"><a class="action_link" data-ix="show-modal-msg" data-title="Suppression" data-message="Attention vous vous apprêtez à supprimer cette équipe. Tous les points seront perdus." data-on-confirm="webflowCampandJoy(\'close-modal-msg\'); loadTo(\'<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('supprimerEquipe.php')); ?>\', {id :<?php echo $equipe->getId();?>}, \'#retour\', \'prepend\');"  data-on-refuse="webflowCampandJoy(\'close-modal-msg\');" data-type="error info success" href="#">[Supprimer]</a></div></div>');
			</script>
			<?php
		}
		else
		{	
			$db = new Database();
			$db->delete("equipe", array("id" => $equipe->getId()));
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $equipe_mbController->getError();?></p>
			</div>
			<?php
		}
	}
	else
	{
		?>
		<div class="message_block error" id='reponse_controller_msg'>
			<p><?php echo $equipeController->getError();?></p>
		</div>
		<?php
	}
}
?> 
