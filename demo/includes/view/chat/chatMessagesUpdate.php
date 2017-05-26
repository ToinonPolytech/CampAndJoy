<?php
	if (!isset($require))
	{
		$require="";
	}
	$isAutomatic=true;
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
	if (getStatutUser($_SESSION["lastUpdate"])=="online")
	{
		$database->update("messagerie", array("destinataire" => $_SESSION["id"], "idSearch" => $_SESSION["id"]+$userConvId, "date" => array(">", $_SESSION["lastUpdateMessages"])), array("lu" => 1));
	}
	$database->setOrderCol("date");
	$database->setDesc();
	$database->select("messagerie", array("OR" => array("destinataire" => $_SESSION["id"], "expediteur" => $_SESSION["id"]), "idSearch" => $_SESSION["id"]+$userConvId), array("date"));
	$data=$database->fetch();
	$last_time=0;
	if (isset($data["date"]))
	{
		$last_time=$data["date"];
	}
	$database->setOrderCol("date");
	$database->setAsc();
	$database->select("messagerie", array("OR" => array("destinataire" => $_SESSION["id"], "expediteur" => $_SESSION["id"]), "idSearch" => $_SESSION["id"]+$userConvId, "date" => array(">", $_SESSION["lastUpdateMessages"])));
	$_SESSION["lastUpdateMessages"]=time();
	while ($data=$database->fetch())
	{
		if (!isset($start) && !$database->count("messagerie", array("OR" => array("destinataire" => $_SESSION["id"], "expediteur" => $_SESSION["id"]), "idSearch" => $_SESSION["id"]+$userConvId, "date" => array("<=", $_SESSION["lastUpdateMessages"]))))
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
		<div class="<?php echo $class; if ($last_time+30*60<$data["date"]) { echo " hours_writed"; } ?>" <?php if ($data["lu"]==0 && $data["destinataire"]==$_SESSION["id"] && !isset($non_lu)) { ?>id="non_lu"<?php $non_lu="true"; } ?>>
			<div class="<?php echo $class; ?>-message">
				<p><?php echo htmlentities($data["message"]); ?></p>
				<div class="<?php echo $class; ?>-msg-date-time" <?php if ($last_time+30*60>=$data["date"]) { echo 'style="display:none;"'; } ?>><?php echo date("d/m H:i", $data["date"]); ?></div>
			</div>
		</div>
		<?php
		$last_time=$data["date"];
	}
	if (isset($class))
	{
		?>
		<script type="text/javascript">
			$(".chat_bot").show();
		</script>
		<?php
	}
?>