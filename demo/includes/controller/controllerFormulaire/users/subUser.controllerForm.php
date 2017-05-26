<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	$userParent=new User($_SESSION["id"]);
	$controller=new Controller_User($userParent);
	?>
	<script type="text/javascript">
		$("div[id='reponse_controller_msg_read']").remove();
		$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
	</script>
	<?php	 
	if (!$controller->can(CAN_CREATE_SUBACCOUNT))
	{
		header("Location:/demo/".LANG_USER."/compte");
		exit();
	}
	if(isset($_POST['name']) && isset($_POST['prenom']))
	{
		$nom=$_POST['name'];
		$prenom=$_POST['prenom'];
		$type="CLIENT";
		$droits=0;
		foreach($CAN_infos as $indice => $infos)
		{
			if (isset($_POST["droit_".$indice]) && $_POST["droit_".$indice]=="on")
			{
				$droits+=pow(2, $indice);
			}
		}
		foreach($CAN_USER_infos as $indice => $infos)
		{
			if (isset($_POST["droit_".$indice]) && $_POST["droit_".$indice]=="on")
			{
				$droits+=pow(2, $indice);
			}
		}
		$sousUser = new User(NULL, NULL, $type, $droits, $nom, $prenom); 
		$controllerSousUser = new Controller_User($sousUser, false); 	
		if($controllerSousUser->isGood())
		{
			if (isset($_POST["id"]))
			{
				$db=new Database();
				$id=$_POST["id"];
				$sousUser->setId($id);
				$sousUser->setUserInfos(new UserInfo($db->getValue("users", array("id" => $id), "infoId")));
				if (!$controller->canEdit($sousUser))
				{
					header("Location:/demo/".LANG_USER."/compte");
					exit();
				}
				$sousUser->setClef($db->getValue("users", array("id" => $id), "clef"));
				$controllerSousUser = new Controller_User($sousUser); 	
				if($controllerSousUser->isGood())
				{
					$sousUser->saveToDb(); 
					?>
					<script type="text/javascript">
						$("#modifierCompte").remove();
						loadToMain("/demo/<?php echo LANG_USER; ?>/compte", {}, function(){ $("div[data-w-tab='Tab 2']").prepend('<div class="message_block success" id="reponse_controller_msg"><p>Le compte de <?php echo htmlentities($prenom)." ".htmlentities($nom); ?> a bien été modifié.</p></div>'); setTimeout(function(){ $("#reponse_controller_msg").fadeOut("slow", function(){ $(this).remove(); }); }, 3000); $("a[data-w-tab='Tab 2']").click(); });		
					</script>
					<?php
				}
				else
				{
					?>
					<div class="message_block error" id='reponse_controller_msg'>
						<p><?php echo $controllerSousUser->getError(); ?></p>
					</div>
					<?php
				}
			}
			else
			{
				$sousUser->setUserInfos($userParent->getUserInfos());
				$sousUser->setClef($controllerSousUser->generateKey());
				$controllerSousUser = new Controller_User($sousUser); 	
				if($controllerSousUser->isGood())
				{
					$sousUser->saveToDb(); 
					?>
					<script type="text/javascript">
						$("#modifierCompte").remove();
						loadToMain("/demo/<?php echo LANG_USER; ?>/compte", {}, function(){ $("div[data-w-tab='Tab 2']").prepend('<div class="message_block success" id="reponse_controller_msg"><p>Le compte de <?php echo htmlentities($prenom)." ".htmlentities($nom); ?> a bien été créé. <br/> Voici sa clef d\'activation : <?php echo $sousUser->getClef(); ?></p></div>'); setTimeout(function(){ $("#reponse_controller_msg").fadeOut("slow", function(){ $(this).remove(); }); }, 3000); $("a[data-w-tab='Tab 2']").click(); });		
					</script>
					<?php
				}
				else
				{
					?>
					<div class="message_block error" id='reponse_controller_msg'>
						<p><?php echo $controllerSousUser->getError(); ?></p>
					</div>
					<?php
				}
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $controllerSousUser->getError(); ?></p>
			</div>
			<?php
		}
	}
?> 