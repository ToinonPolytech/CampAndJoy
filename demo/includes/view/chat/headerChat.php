<?php
	if (!isset($require))
	{
		$require="";
	}
	$isAutomatic=true;
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$database=new Database();
	$messages_non_lu=$database->count("messagerie", array("destinataire" => $_SESSION["id"], "lu" => "0"));
	$nombres_notif_non_vu=$database->count("notifications", array("idUser" => $_SESSION["id"], "vu" => "0"));
	if ($messages_non_lu>0 && (!$nombres_notif_non_vu || !$_SESSION["msg_chat"]))
	{
		$_SESSION["msg_chat"]=true;
		?>
		<div class="chat-notification-block" data-ix="triggernewnotification">
			<a class="notif w-inline-block" href="#">
			  <div class="text-block-3">Vous avez <span class="new-message-notif"><?php echo $messages_non_lu; ?></span> message<?php if ($messages_non_lu>1) { echo 's'; } ?> non lu</div>
			  <div class="anim-border"></div>
			</a>
		</div>
		<script type="text/javascript" id="script_temp">
			nextTitle="Vous avez <?php echo $messages_non_lu; ?> message<?php if ($messages_non_lu>1) { echo 's'; } ?> non lu.";
			titleAnim(true);
			$(".notif.w-inline-block").on("click", function(){
				$(".chat_block").remove();
				loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatView.php")); ?>', {}, "body", "append", false, function(){ webflowCampandJoy("showchat");  setTimeout(function(){ $(".tabs-menu-2").find("a[data-w-tab='Tab 2']").click(); }, 500); startRefreshChat(function(){ loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatViewUpdate.php")); ?>', {}, ".chat_block", "prepend"); }); });
			});
			$("#script_temp").remove();
		</script>
		<?php
	}
	else if ($nombres_notif_non_vu>0)
	{
		$_SESSION["msg_chat"]=false;
		?>
		<div class="chat-notification-block" data-ix="triggernewnotification">
			<a class="notif w-inline-block" href="#">
			  <div class="text-block-3">Vous avez <span class="new-message-notif"><?php echo $nombres_notif_non_vu; ?></span> nouvelle<?php if ($nombres_notif_non_vu>1) { echo 's'; } ?> notification<?php if ($nombres_notif_non_vu>1) { echo 's'; } ?></div>
			  <div class="anim-border"></div>
			</a>
		</div>
		<script type="text/javascript" id="script_temp">
			nextTitle="Vous avez <?php echo $nombres_notif_non_vu; ?> nouvelle<?php if ($nombres_notif_non_vu>1) { echo 's'; } ?> notification<?php if ($nombres_notif_non_vu>1) { echo 's'; } ?>.";
			titleAnim(true);
			$(".notif.w-inline-block").on("click", function(){
				$(".chat_block").remove();
				loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatView.php")); ?>', {}, "body", "append", false, function(){ webflowCampandJoy("showchat"); startRefreshChat(function(){ loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatViewUpdate.php")); ?>', {}, ".chat_block", "prepend"); }); });
			});
			$("#script_temp").remove();
		</script>
		<?php
	}
	else
	{
		?>
		<div class="chat-notification-block">
			<a class="notif w-inline-block" href="#">
				<div class="text-block-3">Ouvrir votre <span class="new-message-notif">centre de notification</span></div>
			</a>
		</div>
		<script type="text/javascript" id="script_temp">
			stopTitleAnim();
			$("title").html(basicTitle);
			$(".notif.w-inline-block").on("click", function(){
				if ($(".chat_block").length>0)
				{
					webflowCampandJoy("showchat");
				}
				else
				{
					loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatView.php")); ?>', {}, "body", "append", false, function(){ webflowCampandJoy("showchat"); startRefreshChat(function(){ loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatViewUpdate.php")); ?>', {}, ".chat_block", "prepend"); }); });
				}
			});
			$("#script_temp").remove();
		</script>
		
		<?php
	}
?>
<script type="text/javascript" id="script_temp">
	if (typeof headerCentreNotif!="undefined")
	{
		clearTimeout(headerCentreNotif);
	}
	headerCentreNotif=setTimeout(function() { loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("headerChat.php")); ?>', {}, "body", "prepend", false, function(){ $(".chat-notification-block").next(".chat-notification-block").remove(); }); }, <?php if ($nombres_notif_non_vu>0) { echo 2000; } else { echo 10000; } ?>);
	$("#script_temp").remove();
</script>