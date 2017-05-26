<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	if (!isset($_POST["id"]))
	{
		exit();
	}
	$userConvId=$_POST["id"];
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$userConv=new User($userConvId);
	$database=new Database();
	$database->setOrderCol("date");
	$database->setAsc();
	$database->select("messagerie", array("OR" => array("destinataire" => $_SESSION["id"], "expediteur" => $_SESSION["id"]), "idSearch" => ($_SESSION["id"]+$userConvId)));
?>
<div class="border-bot head_chat">
	<h5><?php echo htmlentities($userConv->getPrenom()." ".$userConv->getNom()); ?></h5>
</div>
<div class="chat_cont" id="chat-cont-<?php echo $userConvId; ?>">
	<?php
		$_SESSION["lastUpdateMessages"]=time();
		$last_time=0;
		$last_sender=$_SESSION["id"];
		while ($data=$database->fetch())
		{
			$last_sender=$data["expediteur"];
			if (!isset($start))
			{
				$start=true;
				?>
				<div class="chat-started-at"><?php echo date("d/m/Y", $data["date"])." à ".date("H:i", $data["date"]); ?></div>
				<?php
			}
			$class="replies";	
			if ($data["expediteur"]!=$_SESSION["id"])
			{
				$class="sent";
			}
			?>
			<div class="<?php echo $class; if ($last_time+30*60<$data["date"]) { echo " hours_writed"; } ?>" <?php if ($data["lu"]==0 && $data["destinataire"]==$_SESSION["id"] && !isset($non_lu)) { ?>id="non_lu"<?php $non_lu=$data["id"]; } ?>>
				<div class="<?php echo $class; ?>-message">
					<p><?php echo htmlentities($data["message"]); ?></p>
					<div class="<?php echo $class; ?>-msg-date-time" <?php if ($last_time+30*60>=$data["date"]) { echo 'style="display:none;"'; } ?>><?php echo date("d/m H:i", $data["date"]); ?></div>
				</div>
			</div>
			<?php
			$last_time=$data["date"];
		}
		if (isset($non_lu))
		{
			$database->update("messagerie", array("destinataire" => $_SESSION["id"], "idSearch" => $_SESSION["id"]+$userConvId, "id" => array(">=", $non_lu)), array("lu" => 1));
		}
	?>
</div>
<?php
	$send=((isStaff() && $cuser->can(SEND_MESSAGE_STAFF)) || (!isStaff() && $cuser->can(SEND_MESSAGE) && ($userConv->getAccessLevel()=="CLIENT" || $last_sender!=0)));
?>
<div class="chat_bot" <?php if (!$send) { echo "style='display:none;'"; } ?>>
	<div class="form-wrapper-2 w-form">
		<form class="message-text" id="wf-form-Send-Message" name="wf-form-Send-Message">
			<input class="input-msg-text w-input" id="message" maxlength="256" name="message" placeholder="Tapez votre message..." type="text">
			<div class="btn-send-block"></div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#wf-form-Send-Message").on("submit", function(evt){
			if (String($("#message").val()).trim().length>0)
			{
				var messageTxt=$("#message").val();
				$("#message").val("");
				loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("sendMessage.controllerForm.php")); ?>', {message:messageTxt, userConf:<?php echo $userConvId; ?>}, "#chat-cont-<?php echo $userConvId; ?>", "append");
			}
			evt.preventDefault();
			return false;
		});
		$("#wf-form-Send-Message").find(".btn-send-block").on("click", function(){
			$("#wf-form-Send-Message").submit();
		});
		<?php
		if (isset($non_lu))
		{
			?>
			setTimeout(function() { $("#chat-cont-<?php echo $userConvId; ?>").scrollTop($("#non_lu").offset().top); }, 1000);
			<?php
		}
		else
		{
			?>
			setTimeout(function() { $("#chat-cont-<?php echo $userConvId; ?>").scrollTop($("#chat-cont-<?php echo $userConvId; ?>").prop("scrollHeight")); }, 1000);
			<?php
		}	
		?>
		stopRefreshMessages();
		startRefreshMessages(function(){ 
			loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatMessagesUpdate.php")); ?>', {id:<?php echo $userConvId; ?>}, "#chat-cont-<?php echo $userConvId; ?>", "append", false, 
			function(){ 
				$("#chat-cont-<?php echo $userConvId; ?>").scrollTop($("#chat-cont-<?php echo $userConvId; ?>").prop("scrollHeight")); 
			}); 
		});
	});
</script>