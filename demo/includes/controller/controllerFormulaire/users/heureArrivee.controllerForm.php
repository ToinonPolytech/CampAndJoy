<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,userInfo.class.php,userInfos.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	?>
	<script type="text/javascript">
		$('div[class*="message_block"]').remove();
	</script>
	<?php
	$user=new User($_SESSION["id"]);
	if ($user->getUserInfos()->getTimeArrive()>time() && date("H", $user->getUserInfos()->getTimeArrive())==0 && date("i", $user->getUserInfos()->getTimeArrive())==0)
	{
		if (isset($_POST["h"]))
		{
			$temp=explode(":", $_POST["h"]);
			if (count($temp)==2)
			{
				$exist=false;
				foreach ($heuresAccueil as $h)
				{
					$temp2=explode(":", $h);
					if ($temp2[0]+0==$temp[0]+0 && $temp2[1]+0==$temp[1]+0)
					{
						$user->getUserInfos()->setTimeArrive($user->getUserInfos()->getTimeArrive()+$temp[0]*3600+$temp[1]*60);
						$user->getUserInfos()->saveToDb();
						?>
						<script type="text/javascript">
							$('div[class*="message_block"]').remove();
							$('.modal_message<?php echo htmlentities($_POST["uniqId"]); ?>.modal_message_dynamic').find('.w-container.infos_msg').html("<div style='text-align:center;' class='message_block success'>Merci de cette information. Nous allons l'utiliser pour vous permettre de profiter au mieux de votre séjour.</div><br/><a class='primary_btn space_beetween_btn w-button alert_btn' href='#' onclick='webflowCampandJoy(\"close-modal-msg<?php echo htmlentities($_POST["uniqId"]); ?>\"); $(\".modal_message<?php echo htmlentities($_POST["uniqId"]); ?>.modal_message_dynamic\").remove();'>Fermer</a>");
						</script>
						<?php
						exit();
					}
				}
				?>
				<script type="text/javascript">
					$('.modal_message<?php echo htmlentities($_POST["uniqId"]); ?>.modal_message_dynamic').prepend("<div style='text-align:center;' class='message_block error'>Désolé mais cette heure n\'est pas disponible.</div>");
				</script>
				<?php
			}
			else
			{
				?>
				<script type="text/javascript">
					$('.modal_message<?php echo htmlentities($_POST["uniqId"]); ?>.modal_message_dynamic').prepend("<div style='text-align:center;' class='message_block error'>Merci de remplir votre heure d'arrivée.</div>");
				</script>
				<?php
			}
		}
	}
?>