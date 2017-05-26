<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,userInfo.class.php,userInfos.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
		exit();
	
	$userLog = new User($_SESSION["id"]);
	$controllerLog = new Controller_User($userLog);
	if ($userLog->getAccessLevel()=="CLIENT" || $userLog->getAccessLevel()=="PARTENAIRE")
		exit();
?>
<div class="alert alert-danger" role="alert" name="infoErreur" id="infoErreur">
	<?php 
		if(isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['numPlace']) && isset($_POST['mail']) && isset($_POST['date']) && isset($_POST['type']) && $date=strtotime($_POST["date"])!==false)
		{
			$droits=0;
			if ($_POST["type"]=="CLIENT")
			{
				for ($i=$puissance;$i>0;$i--)
				{
					$droits+=$i;
				}
			}
			$user = new User(NULL, NULL, htmlspecialchars($_POST['type']), $droits, htmlspecialchars($_POST['nom']), htmlspecialchars($_POST['prenom']), NULL);
			if (isset($_POST["id"]))
			{
				if (!$controllerLog->can(CAN_EDIT_ACCOUNT_STAFF))
					exit();

				$user->setId($_POST["id"]);
				$db = new Database();
				$userInfos= new UserInfo($db->getValue("users", array("id" => $_POST["id"]), "infoId"));
				$user->setClef($db->getValue("users", array("id" => $_POST["id"]), "clef"));
			}
			else
			{
				if (!$controllerLog->can(CAN_CREATE_ACCOUNT_STAFF))
					exit();
				
				$controllerUser = new Controller_User($user);
				$clef = $controllerUser->generateKey();
				$userInfos = new UserInfo(NULL, htmlspecialchars($_POST['numPlace']), htmlspecialchars($_POST['mail']), strtotime(htmlspecialchars($_POST["date"])), $clef);
				$user->setClef($clef);
			}
			$user->setUserInfos($userInfos);
			$controllerUser = new Controller_User($user, false); // On met à jour notre controller
			$controllerUserInfo = new Controller_UserInfo($userInfos);
			if ($controllerUserInfo->isGood() && $controllerUser->isGood())
			{
				$userInfos->saveToDb();
				$user->saveToDb();
				$success=true;
			}
		}
		if (isset($success))
		{
			echo 'La clef de connexion du client est : '.$user->getClef();
		}
	?>
</div>