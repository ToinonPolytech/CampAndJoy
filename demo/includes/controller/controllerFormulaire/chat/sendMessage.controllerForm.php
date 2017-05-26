<?php 
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
		exit();

	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if((isStaff() && !$cuser->can(SEND_MESSAGE_STAFF)) || (!isStaff() && !$cuser->can(SEND_MESSAGE)))
	{
		exit();
	}
	if (isset($_POST["message"]) && isset($_POST["userConf"]) && is_numeric($_POST["userConf"]))
	{
		$userConv=new User($_POST["userConf"]);
		$database=new Database();
		$database->setOrderCol("date");
		$database->setDesc();
		$database->select("messagerie", array("OR" => array("destinataire" => $_SESSION["id"], "expediteur" => $_SESSION["id"]), "idSearch" => ($_SESSION["id"]+$_POST["userConf"])), array("date"));
		$data=$database->fetch();
		$last_time=0;
		if (isset($data["date"]))
		{
			$last_time=$data["date"];
		}
		else
		{
			?>
			<div class="chat-started-at"><?php echo date("d/m/Y", time())." à ".date("H:i", time()); ?></div>
			<?php
		}
		$_SESSION["lastUpdateMessages"]=time();
		if (((isStaff() && $cuser->can(SEND_MESSAGE_STAFF)) || (!isStaff() && $cuser->can(SEND_MESSAGE)))) //  && ($userConv->getAccessLevel()=="CLIENT" || $last_time!=0) c.f todo
		{
			$database->create("messagerie", array("idSearch" => $_SESSION["id"]+$_POST["userConf"], "destinataire" => $_POST["userConf"], "expediteur" => $_SESSION["id"], "message" => trim($_POST["message"]), "date" => time(), "lu" => 0));
			$uniq=uniqid();
			?>
			<div class="replies <?php if ($last_time+30*60<time()) { echo "hours_writed"; } ?>">
				<div class="replies-message">
					<p><?php echo htmlentities(trim($_POST["message"])); ?></p>
					<div class="replies-msg-date-time" <?php if ($last_time+30*60>=time()) { echo "style='display:none;'"; } ?>><?php echo date("d/m H:i", time()); ?></div>
				</div>
			</div>
			<script type="text/javascript" id="scriptTemp">
				$("#chat-cont-<?php echo $_POST["userConf"]; ?>").scrollTop($("#chat-cont-<?php echo $_POST["userConf"]; ?>").prop("scrollHeight"));
				$("#scriptTemp").remove();
			</script>
			<?php
		}
	}
?>