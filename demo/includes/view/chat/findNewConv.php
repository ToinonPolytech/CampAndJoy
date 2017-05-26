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
	if (!isset($_POST["search"]))
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if ((isStaff() && !$cuser->can(SEND_MESSAGE_STAFF)) || (!isStaff() && !$cuser->can(SEND_MESSAGE)))
	{
		exit();
	}
	$database=new Database();
	$database2=new Database();
	$search=$_POST["search"];
	/*$database->prepare("SELECT nom, prenom, id, photo, statut FROM users LEFT JOIN messagerie ON idSearch=id+:idUser WHERE (nom LIKE :nom OR prenom LIKE :prenom) AND idSearch=id+:idSearch");
	$database->execute(array("idUser" => $_SESSION["id"], "nom" => "%".$search."%", "prenom" => "%".$search."%", "idSearch" => $_SESSION["id"]));*/
	
	if (!isStaff())
	{
		$database->select("users", array("OR" => array("nom" => array(" LIKE ", "%".$search."%"), "prenom" => array(" LIKE ", "%".$search."%")), "access_level" => "CLIENT"), array("nom", "prenom", "id", "photo", "statut"));
	}
	else
	{
		$database->select("users", array("OR" => array("nom" => array(" LIKE ", "%".$search."%"), "prenom" => array(" LIKE ", "%".$search."%"))), array("nom", "prenom", "id", "photo", "statut"));
	}
	while ($data=$database->fetch())
	{
		$userConv=$data["id"];
		if (!$database2->count("messagerie", array("idSearch" => $userConv+$_SESSION["id"], "OR" => array("destinataire" => $_SESSION["id"], "expediteur" => $_SESSION["id"]))))
		{
		?>
		<div class="chat-contact newcontact" id="contact-<?php echo $userConv; ?>" name="contact-<?php echo $userConv; ?>">
			<div class="chat-i-col-right w-clearfix">
				<div class="contact-avat">
					<?php 
					if (isset($data["photo"]) && !empty($data["photo"]))
					{
						?>
						<img class="contact-photo" src="<?php echo htmlentities($data["photo"]); ?>">
						<?php
					}
					?>
					<div class="no-avatar">
						<div><?php echo htmlentities($data["prenom"][0].".".$data["nom"][0]); ?></div>
					</div>
				</div>
				<div class="contact-meta">
					<h5 class="contact-name" id="contact-name"><?php echo htmlentities($data["prenom"]." ".$data["nom"]); ?></h5>
					<div class="contact-little-text">Commencez une nouvelle conversation !</div>
				</div>
				<div class="contact-status <?php echo getStatutUser($data["statut"]); ?>"></div>
			</div>
		</div>
		<?php
		}
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".chat-contact.newcontact").on("click", function(){
			var selector=$(this);
			loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatMessages.php")); ?>', {id:String($(this).attr("id")).replace("contact-", "")}, "#chat-messages", "replace", false, function(){ webflowCampandJoy("showchatmessage"); if (selector.find(".contact-new-msg-number").length>0){ clearTimeout(headerCentreNotif); loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("headerChat.php")); ?>', {}, "body", "prepend", false, function(){ $(".chat-notification-block").next(".chat-notification-block").remove(); }); } });
			$("#left_arrow").show();
			return false;
		});
	});
</script>