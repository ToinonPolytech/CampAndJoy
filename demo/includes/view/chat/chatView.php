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
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if((isStaff() && !$cuser->can(SEND_MESSAGE_STAFF)) || (!isStaff() && !$cuser->can(SEND_MESSAGE)))
	{
		$send_first=false;
	}
	else
	{
		$send_first=true;
	}
	$_SESSION["last_chatUpdate"]=time();
?>
<div class="chat_block">
    <div class="chat_head">
		<div class="chat_head_item"><img class="btn_prev" data-ix="backchatlist" src="images/left-arrow.svg" width="15" id="left_arrow" style="display:none;">
			<div class="chat_owner_name">Centre de Notification</div>
		</div>
		<div class="chat_head_item_right">
			<img class="close_chat" data-ix="close-chat" onclick="stopRefreshChat();stopRefreshMessages();" src="images/cancel.svg" width="15">
		</div>
    </div>
    <div class="tabs w-tabs" data-duration-in="300" data-duration-out="100">
		<div class="tabs-menu-2 w-tab-menu">
			<a class="list-notif tab-link w--current w-inline-block w-tab-link" data-w-tab="Tab 1" onclick="editTitle('Centre de Notification');"></a>
			<a class="list-chat tab-link w-inline-block w-tab-link" data-w-tab="Tab 2" onclick="editTitle('Vos conversations');"></a>
		</div>
		<div class="tabs-content w-tab-content">
			<div class="tab-pane-2 w--tab-active w-tab-pane" data-w-tab="Tab 1">
				<div class="notif-list">
					<div class="chat_cont">
					<?php
						$notifs=false;
						$database=new Database();
						$database->setOrderCol("date");
						$database->setDesc();
						$database->select("notifications", array("idUser" => $_SESSION["id"]));
						while ($data=$database->fetch())
						{
							$notifs=true;
							?>
							<div class="chat-contact" id="notif-<?php echo $data["id"]; ?>" name="notif-<?php echo $data["id"]; ?>" rel="<?php echo $data["lien"]; ?>">
								<div class="chat-i-col-right w-clearfix">
									<div class="contact-meta">
										<h5 class="contact-name"><?php echo $data["titre"]; ?></h5>
										<div class="contact-little-text"><?php echo $data["message"]; ?></div>
									</div>
								</div>
								<div class="chat-i-col-left w-clearfix">
									<div class="contact-day"><?php echo date("d", $data["date"]); ?> <?php $month=getMonthFromNumber(date("n", $data["date"])); if (strlen($month)>4) { echo substr($month, 0, 3); } else { echo $month; } echo " ".date("H:i", $data["date"]); ?></div>
								</div>
							</div>	
							<?php
						}
						$database->update("notifications", array("idUser" => $_SESSION["id"]), array("vu" => 1));
						if (!$notifs)
						{
							?>
							<div class="chat-contact" id="no-notifs" name="no-notifs">
								<div class="chat-i-col-right w-clearfix">
									<div class="contact-meta">
										<h5 class="contact-name">Aucune notification</h5>
										<div class="contact-little-text">Vous n'avez pour le moment aucune notification</div>
									</div>
								</div>
							</div>	
							<?php
						}
					?>
					</div>
				</div>
			</div>
			<div class="tab-pane-2 w-tab-pane" data-w-tab="Tab 2">
				<div class="chat-list">
					<?php 
					if ($send_first)
					{
						?>
						<div class="head_chat">
							<div class="search">
								<div class="form-wrapper w-form">
									<input class="search-input w-input" data-name="Search" id="Search" maxlength="256" name="Search" placeholder="Rechercher une conversation" type="text">
								</div>
								<img class="btn-send-search" src="images/search-1.svg" width="20">
							</div>
						</div>
						<?php
					}
					?>
					<div class="chat_cont">
						<?php
							$database=new Database();
							$database2=new Database();
							$database->prepare("SELECT idSearch, MAX(date) FROM messagerie WHERE expediteur=:exp OR destinataire=:des GROUP BY (idSearch)");
							// MAX(date) permet de trier par DESC...
							$database->execute(array("exp" => $_SESSION["id"], "des" => $_SESSION["id"]));
							$arrayConv=array();
							while ($data=$database->fetch())
							{
								$arrayConv[]=$data["idSearch"];
							}
							foreach ($arrayConv as $idSearchConv)
							{
								$database->setOrderCol("date");
								$database->setLimit(1);
								$database->setDesc();
								$database->select("messagerie", array("OR" => array("expediteur" => $_SESSION["id"], "destinataire" => $_SESSION["id"]), "idSearch" => $idSearchConv));
								$data=$database->fetch();
								$userConv=($data["expediteur"]==$_SESSION["id"])?$data["destinataire"]:$data["expediteur"];
								$database2->select("users", array("id" => $userConv), array("id", "nom", "prenom", "statut", "photo"));
								$data2=$database2->fetch();
								$length_to_cut=20;
								$database->setLimit(NULL);
								$nombreNewMessages=$database->count("messagerie", array("destinataire" => $_SESSION["id"], "idSearch" => $idSearchConv, "lu" => "0"));
								?>
								<div class="chat-contact" id="contact-<?php echo $userConv; ?>" name="contact-<?php echo $userConv; ?>">
									<div class="chat-i-col-right w-clearfix">
										<div class="contact-avat">
											<?php 
											if (isset($data2["photo"]) && !empty($data2["photo"]))
											{
												?>
												<img style="height:100%;" class="contact-photo" src="<?php echo htmlentities(str_replace(",", "", $data2["photo"])); ?>">
												<?php
											}
											else
											{
											?>
											<div class="no-avatar">
												<div><?php echo htmlentities($data2["prenom"][0].".".$data2["nom"][0]); ?></div>
											</div>
											<?php
											}
											?>
										</div>
										<div class="contact-meta">
											<h5 class="contact-name" id="contact-name"><?php echo htmlentities($data2["prenom"]." ".$data2["nom"]); ?></h5>
											<div class="contact-little-text"><?php echo htmlentities(substr($data["message"], 0, $length_to_cut)); if(strlen($data["message"])>=$length_to_cut){ echo "..."; } ?></div>
										</div>
										<div class="contact-status <?php echo getStatutUser($data2["statut"]); ?>"></div>
									</div>
									<div class="chat-i-col-left w-clearfix">
										<div class="contact-day"><?php echo date("d", $data["date"]); ?> <?php $month=getMonthFromNumber(date("n", $data["date"])); if (strlen($month)>4) { echo substr($month, 0, 3); } else { echo $month; } ?></div>
										<?php 
										if ($nombreNewMessages)
										{
										?>
											<div class="contact-new-msg-number">
												<div><?php echo $nombreNewMessages; ?></div>
											</div>
										<?php
										}
										?>
									</div>
								</div>
								<?php
							}
						?>
					</div>
				</div>
				<div class="chat-message" id="chat-messages"></div>
			</div>
		</div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("div[id^='contact-']").on("click", function(){
			var selector=$(this);
			loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("chatMessages.php")); ?>', {id:String($(this).attr("id")).replace("contact-", "")}, "#chat-messages", "replace", false, function(){ webflowCampandJoy("showchatmessage"); if (selector.find(".contact-new-msg-number").length>0){ clearTimeout(headerCentreNotif); loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("headerChat.php")); ?>', {}, "body", "prepend", false, function(){ $(".chat-notification-block").next(".chat-notification-block").remove(); }); } });
			$("#left_arrow").show();
			return false;
		});
		$("div[id^='notif-']").on("click", function(){
			if ($(this).attr("rel"))
			{ 
				loadToMain($(this).attr("rel"), {});
				$(".close_chat").click();
			}
			return false;
		});
		<?php 
		if ($send_first)
		{
		?>
			var input_content = $.trim($(this).val());
			var delay_searchChat=0;
			$("#Search").on("keyup", function(){
				delay_searchChat=Date.now();
				last_searchChat=$(this).val();
				last_searchChat=$.trim(last_searchChat);
				setTimeout(function(){
					if (parseInt(delay_searchChat+750)<=Date.now())
					{
						$(".chat-contact.newcontact").remove();
						$("h5[id='contact-name']").parent().parent().parent().show();
						if (last_searchChat)
						{
							$("h5[id='contact-name']").not(':icontains('+last_searchChat+')').parent().parent().parent().hide();
							loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("findNewConv.php")); ?>', {search:last_searchChat}, ".chat-list>.chat_cont", "append");		
						}
					}
				}, 750);
			});
		<?php
		}
		?>
	});
	$("#left_arrow").on("click", function(){
		stopRefreshMessages();
		$(this).hide();
	});
	jQuery.expr[':'].icontains = function(a, i, m) {
	  return jQuery(a).text().toUpperCase()
		  .indexOf(m[3].toUpperCase()) >= 0;
	};
</script>