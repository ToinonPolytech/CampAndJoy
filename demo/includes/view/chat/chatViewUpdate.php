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
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	$database->prepare("SELECT idSearch, MAX(date) FROM messagerie WHERE (expediteur=:exp OR destinataire=:des) GROUP BY (idSearch)");
	$database->execute(array("exp" => $_SESSION["id"], "des" => $_SESSION["id"]));
	$arrayUser=array();
	while ($data=$database->fetch())
	{
		$arrayUser[]=$data["idSearch"]-$_SESSION["id"];
	}
	$js='';
	$length_to_cut=20;
	foreach ($arrayUser as $id)
	{
		$database->setOrderCol("date");
		$database->setAsc();
		$database->select("messagerie", array("OR" => array("destinataire" => $_SESSION["id"], "expediteur" => $_SESSION["id"]), "idSearch" => $id+$_SESSION["id"]), array("date", "message"));
		$data=$database->fetch();
		$msg=htmlentities(substr($data["message"], 0, $length_to_cut));
		$contactDate=$data["date"];
		$month=getMonthFromNumber(date("n", $contactDate));
		$newMessages=$database->count("messagerie", array("destinataire" => $_SESSION["id"], "idSearch" => $id+$_SESSION["id"], "lu" => "0"));
		if (strlen($month)>4) { $month=substr($month, 0, 3); }
		if(strlen($data["message"])>=$length_to_cut){ $msg.="..."; }
		if ($contactDate>$_SESSION["last_chatUpdate"])
		{
			$database->setOrderCol(NULL);
			$database->setAsc();
			$database->select("users", array("id" => $id), array("nom", "prenom", "id", "photo", "statut"));
			$data2=$database->fetch();
			if ($newMessages)
			{
				$ajout="";
				if (isset($data2["photo"]) && !empty($data2["photo"]))
				{
					$ajout="<img class=\"contact-photo\" src=\"".htmlentities($data2["photo"])."\">";
				}
				$js.='$(".chat-list>.chat_cont").prepend("<div class=\'chat-contact\' id=\'contact-'.$id.'\' name=\'contact-'.$id.'\'><div class=\'chat-i-col-right w-clearfix\'><div class=\'contact-avat\'>'.$ajout.'<div class=\'no-avatar\'><div>'.htmlentities($data2["prenom"][0].'.'.$data2["nom"][0]).'</div></div></div><div class=\'contact-meta\'><h5 class=\'contact-name\' id=\'contact-name\'>'.htmlentities($data2["prenom"].' '.$data2["nom"]).'</h5><div class=\'contact-little-text\'>'.$msg.'</div></div><div class=\'contact-status '.getStatutUser($data2["statut"]).'\'></div></div><div class=\'chat-i-col-left w-clearfix\'><div class=\'contact-day\'>'.date("d", $contactDate).' '.$month.'</div><div class=\'contact-new-msg-number\'><div>'.$newMessages.'</div></div></div></div>");';
			}
			else
			{
				$ajout="";
				if (isset($data2["photo"]) && !empty($data2["photo"]))
				{
					$ajout="<img class=\"contact-photo\" src=\"".htmlentities($data2["photo"])."\">";
				}
				$js.='$(".chat-list>.chat_cont").prepend("<div class=\'chat-contact\' id=\'contact-'.$id.'\' name=\'contact-'.$id.'\'><div class=\'chat-i-col-right w-clearfix\'><div class=\'contact-avat\'>'.$ajout.'<div class=\'no-avatar\'><div>'.htmlentities($data2["prenom"][0].'.'.$data2["nom"][0]).'</div></div></div><div class=\'contact-meta\'><h5 class=\'contact-name\' id=\'contact-name\'>'.htmlentities($data2["prenom"].' '.$data2["nom"]).'</h5><div class=\'contact-little-text\'>'.$msg.'</div></div><div class=\'contact-status '.getStatutUser($data2["statut"]).'\'></div></div><div class=\'chat-i-col-left w-clearfix\'><div class=\'contact-day\'>'.date("d", $contactDate).' '.$month.'</div></div></div>");';
			}
		}
		else
		{
			$js.='$("#contact-'.$id.'").find(".contact-little-text").html("'.str_replace('"', '\\"', $msg).'");';
			$js.='$("#contact-'.$id.'").find(".chat-i-col-left.w-clearfix").remove();';
			if ($newMessages)
			{		
				$js.='$("#contact-'.$id.'").append("<div class=\"chat-i-col-left w-clearfix\"><div class=\"contact-day\">'.date("d", $contactDate).' '.$month.'</div><div class=\"contact-new-msg-number\"><div>'.$newMessages.'</div></div></div>");';
				$js.='var tempContact=$("#contact-'.$id.'").prop("outerHTML"); $("#contact-'.$id.'").remove(); $(".chat-list>.chat_cont").prepend(tempContact);';
			}
			else
			{
				$js.='$("#contact-'.$id.'").append("<div class=\"chat-i-col-left w-clearfix\"><div class=\"contact-day\">'.date("d", $contactDate).' '.$month.'</div></div>");';
			}
			$database->setOrderCol(NULL);
			$database->setAsc();
			$js.='$("#contact-'.$id.'").children(".chat-i-col-right.w-clearfix").find("div[class^=\'contact-status\']").remove();
			$("#contact-'.$id.'").children(".chat-i-col-right.w-clearfix").append("<div class=\'contact-status '.getStatutUser($database->getValue("users", array("id" => $id), "statut")).'\'></div>");';
		}
	}
	$database=new Database();
	$database->setOrderCol("date");
	$database->setDesc();
	$database->select("notifications", array("idUser" => $_SESSION["id"], "vu" => "0"));
	$notifs=false;
	while ($data=$database->fetch())
	{
		$notifs=true;
		$month=getMonthFromNumber(date("n", $data["date"])); if (strlen($month)>4) { $month=substr($month, 0, 3); }
		$js.='$(".notif-list").prepend("<div class=\"chat-contact\" id=\"notif-'.$data["id"].'\" name=\"notif-'.$data["id"].'\" rel=\"'.$data["lien"].'\"><div class=\"chat-i-col-right w-clearfix\"><div class=\"contact-meta\"><h5 class=\"contact-name\">'.$data["titre"].'</h5><div class=\"contact-little-text\">'.$data["message"].'</div></div></div><div class=\"chat-i-col-left w-clearfix\"><div class=\"contact-day\">'.date("d", $data["date"]).' '.$month.' '.date("H:i", $data["date"]).'</div></div></div>");';
	}
	if ($notifs)
	{
		$js.='$("#no-notifs").remove();';
	}
	$database->setOrderCol(NULL);
	$database->update("notifications", array("idUser" => $_SESSION["id"]), array("vu" => 1));
	$_SESSION["last_chatUpdate"]=time();
?>
<script type="text/javascript" id="script_temp">
	<?php echo $js; ?>
	$("#script_temp").remove();
	$("div[id^='notif-']").unbind("click");
	$("div[id^='notif-']").on("click", function(){
		if ($(this).attr("rel"))
		{ 
			loadToMain($(this).attr("rel"), {});
			$(".close_chat").click();
		}
		return false;
	});
</script>