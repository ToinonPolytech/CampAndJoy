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
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	if(!isStaff() || !$cuser->can(CAN_CREATE_ACCOUNT_STAFF))
	{
		exit();
	}
	if (isset($_POST["id"]) && !$database->count("users", array("id" => $_POST["id"])))
	{
		exit();
	}
	if (isset($_POST["access_level"]) && isset($_POST["email"]) && isset($_POST["emplacement"]) && isset($_POST["date_arrivee"]) && isset($_POST["date_depart"]) && isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["comptes_affi"]))
	{
		if (getNumberFromAccessLevel($_POST["access_level"])>getNumberFromAccessLevel($user->getAccessLevel()))
		{
			exit();
		}
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php	 
		$email=htmlentities($_POST["email"]);
		$emplacement=htmlentities($_POST["emplacement"]);
		$date_arrivee=htmlentities($_POST["date_arrivee"]);
		$date_depart=htmlentities($_POST["date_depart"]);
		$nom=htmlentities($_POST["nom"]);
		$prenom=htmlentities($_POST["prenom"]);
		$compte_affi=htmlentities($_POST["comptes_affi"]);
		$access_level=htmlentities($_POST["access_level"]);
		if (empty($emplacement))
			$emplacement=-1;
		if (empty($date_arrivee))
			$timeArrive=-1;
		else
		{
			$temp=explode("/", $date_arrivee);
			$timeArrive=strtotime($temp[2]."-".$temp[1]."-".$temp[0]);
		}
		if (empty($date_depart))
			$timeDepart=-1;
		else
		{
			$temp=explode("/", $date_depart);
			$timeDepart=strtotime($temp[2]."-".$temp[1]."-".$temp[0]);
		}
		if (isset($_POST["id"]))
		{
			$database->select("users", array("id" => $_POST["id"]), array("clef", "infoId", "droits"));
			$data=$database->fetch();
			$user_edit=new User(NULL, NULL, NULL, $data["droits"], NULL, NULL, NULL, NULL);
			$cuser_edit=new Controller_User($user_edit);
		}
		$droits=0;
		foreach($CAN_infos as $indice => $infos)
		{
			if ((isset($cuser_edit) && $cuser_edit->can($indice) && (!$cuser->can($indice) || (isset($_POST["droit_".$indice]) && $_POST["droit_".$indice]=="on"))) || ($cuser->can($indice) && isset($_POST["droit_".$indice]) && $_POST["droit_".$indice]=="on"))
			{
				$droits+=pow(2, $indice);
			}
		}
		if ($access_level=="CLIENT")
		{
			foreach($CAN_USER_infos as $indice => $infos)
			{
				if (isset($_POST["droit_".$indice]) && $_POST["droit_".$indice]=="on")
				{
					$droits+=pow(2, $indice);
				}
			}
		}
		else
		{
			foreach($CAN_STAFF_infos as $indice => $infos)
			{
				if ((isset($cuser_edit) && $cuser_edit->can($indice) && (!$cuser->can($indice) || (isset($_POST["droit_".$indice]) && $_POST["droit_".$indice]=="on"))) || ($cuser->can($indice) && isset($_POST["droit_".$indice]) && $_POST["droit_".$indice]=="on"))
				{
					$droits+=pow(2, $indice);
				}
			}
		}
		$clef=$cuser->generateKey();
		$userInfos=new UserInfo(NULL, $emplacement, $email, $timeDepart, $timeArrive, $clef, $compte_affi);
		if (isset($_POST["id"]))
		{	
			$userInfos->setId($data["infoId"]);
			$userInfos->setClef($data["clef"]);
		}
		$cuserInfos=new Controller_UserInfo($userInfos);
		$userCreate=new User(NULL, NULL, $access_level, $droits, $nom, $prenom, NULL, $clef);
		if (isset($_POST["id"]))
		{
			$userCreate->setUserInfos($userInfos);
			$userCreate->setId($_POST["id"]);
			$userCreate->setClef($data["clef"]);
		}
		if (isset($_POST["id"]))
			$cuserCreate=new Controller_User($userCreate);
		else
			$cuserCreate=new Controller_User($userCreate, false);
		
		if ($cuserCreate->isGood())
		{
			if ($cuserInfos->isGood())
			{
				$userInfos->saveToDb();
				$userCreate->setUserInfos($userInfos);
				$cuserCreate=new Controller_User($userCreate);
				if ($cuserCreate->isGood())
				{
					$userCreate->saveToDb();
					?>
					<script type="text/javascript">
						loadToMain("/demo/<?php echo LANG_USER; ?>/administration/compte", {}, function(){
							$("#nom").val("<?php echo htmlentities($userCreate->getNom()); ?>");
							$("#type").val("<?php echo htmlentities($userCreate->getAccessLevel()); ?>");
							<?php if ($userCreate->getUserInfos()->getEmplacement()>0) { ?>$("#emplacement").val("<?php echo htmlentities($userCreate->getUserInfos()->getEmplacement()); ?>");<?php } ?>
							<?php if ($userCreate->getUserInfos()->getTimeArrive()>0) { ?>$("#date_arrive").val("<?php echo date("d/m/Y", $userCreate->getUserInfos()->getTimeArrive()); ?>");<?php } ?>
							<?php if ($userCreate->getUserInfos()->getTimeDepart()>0) { ?>$("#date_depart").val("<?php echo date("d/m/Y", $userCreate->getUserInfos()->getTimeDepart()); ?>");<?php } ?>
							launchSearch();
							$("#search").before('<div class="message_block success" id="reponse_controller_msg"><p>L\'utilisateur a bien été <?php if (!isset($_POST["id"])) { ?>créé.<br/> Voici sa clef : <?php echo $userCreate->getClef(); ?> <?php } else { echo "modifié"; } ?></p></div>');
							setTimeout(function(){ $(".message_block.success").remove(); }, 5000);
						});
					</script>
					<?php
				}
				else
				{
					?>
					<div class="message_block error" id='reponse_controller_msg'>
						<p><?php echo $cuserCreate->getError(); ?></p>
					</div>
					<?php
					if (!isset($_POST["id"]))
					{
						$userInfos->setDeleted(true);
						$userInfos->saveToDb();
					}
				}
			}
			else
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p><?php echo $cuserInfos->getError(); ?></p>
				</div>
				<?php
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $cuserCreate->getError(); ?></p>
			</div>
			<?php
		}
	}
?>