<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,user.class.php,user.controller.class.php,";
include($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
$database = new Database();
if (auth())
{
	$database->update("users", array("id" => $_SESSION["id"]), array("statut" => 0));
	$lang=$_SESSION["lang"];
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	session_destroy();
	?>
	<script type="text/javascript">window.location.replace("index.php");</script>
	<?php
	$_SESSION["lang"]=$lang;
	exit();
}
if ((isset($_POST["clef"]) && $database->count('users', array("clef" => $_POST["clef"]))) || (!isset($_POST["clef"]) && isset($_COOKIE["clef"]) && $database->count('users', array("clef" => $_COOKIE["clef"]))))
{
	if (isset($_POST["code"]))
	{
		if ($database->count('users', array("clef" => $_COOKIE["clef"])))
		{
			$database->select('users', array("clef" => $_COOKIE["clef"]), "id");
			$data = $database->fetch();
			$user=new User($data["id"]);
			if ($user->getCode()==NULL || $user->getCode()==hash("sha256", $prefix_hash.$_POST["code"]))
			{
				if ($user->getCode()==NULL) // Première connexion
				{
					if (strlen($_POST["code"])==4 && is_numeric($_POST["code"]))
					{
						$user->setCode(hash("sha256", $prefix_hash.$_POST["code"]));
						$user->saveToDb();
					}
					else
					{
						?>
						<script type="text/javascript">
							$('.msg_block').remove();
							$('.home_center_block').prepend('<div class="msg_block error_msg" style="color:white;"><div><?php __(ERREUR_REGEX_CODE); ?></div></div>');
							webflowCampandJoy('onloginsuccess');
						</script>
						<?php
						exit(); // On stop le fichier
					}
				}
				$controller= new Controller_User($user);
				if (!$controller->can(CAN_LOG))
				{
					?>
					<script type="text/javascript">
						$('.msg_block').remove();
						$('.home_center_block').prepend('<div class="msg_block error_msg" style="color:white;"><div><?php __(ERREUR_COMPTE_BLOQUE); ?></div></div>');
						webflowCampandJoy('onloginsuccess');
					</script>
					<?php
					exit(); // On stop le fichier
				}
				$_SESSION["id"]=$user->getId(); // Et hop ! On est connecté 
				$_SESSION["access_level"]=$user->getAccessLevel();
				$_SESSION["infoId"]=$user->getUserInfos()->getId();	
				$_SESSION["new_user"]=false;
				$_SESSION["lastUpdate"]=time();
				if ($user->getUserInfos()->getTimeArrive()>time() && date("H", $user->getUserInfos()->getTimeArrive())==0 && date("i", $user->getUserInfos()->getTimeArrive())==0)
				{
					$_SESSION["new_user"]=true;
				}
				$database->update("users", array("id" => $_SESSION["id"]), array("statut" => time()));
				?>
				<script type="text/javascript">
					$('.msg_block').remove();
					$('.home_center_block').prepend('<div class="msg_block success_msg" style="color:white;"><div><?php __(SUCCESS_CONNEXION); ?></div></div>');
					webflowCampandJoy('onloginsuccess');
					setTimeout(function(){ window.location.replace("index.php?logged=true"); }, 1000);
				</script>
				<?php
			}
			else
			{
				?>
				<script type="text/javascript">
					$('.msg_block').remove();
					$('.home_center_block').prepend('<div class="msg_block error_msg" style="color:white;"><div><?php __(ERREUR_MAUVAIS_CODE); ?></div></div>');
					webflowCampandJoy('onloginsuccess');
				</script>
				<?php
			}
		}
		else
		{
			?>
			<script type="text/javascript">
				$('.msg_block').remove();
				$('.home_center_block').prepend('<div class="msg_block error_msg" style="color:white;"><div><?php __(ERREUR_PROBLEME_SURVENU); ?></div></div>');
				webflowCampandJoy('onloginsuccess');
			</script>
			<?php
		}
	}
	else
	{
		setcookie("clef", $_POST["clef"], time()+7*3600*24, "/");
		$database->select('users', array("clef" => $_POST["clef"]), array("nom", "prenom", "code"));
		$data = $database->fetch();
		?>
		<script type="text/javascript">
			$('.msg_block').remove();
			webflowCampandJoy('welcomepageonetrigger');
			$(".page_two").children("h1").html("<?php __(BONJOUR_PRENOM_NOM, $data["prenom"], $data["nom"]); ?>");
			$(".page_two").children("input").attr("placeholder", "<?php if ($data["code"]==NULL) { __(CREER_CODE); } else { __(DEMANDE_CODE); } ?>");
			$("#clef").val("");
		</script>
		<?php
	}
}
else
{
	if (isset($_COOKIE["clef"]))
	{
		setcookie("clef", "", time()-20, "/");
		?>
		<script type="text/javascript">
			$('.msg_block').remove();
		</script>
		<?php
	}
	else
	{
		if (isset($_POST["clef"]) && empty($_POST["clef"]))
		{
			?>
			<script type="text/javascript">
				$('.msg_block').remove();
				$('.home_center_block').prepend('<div class="msg_block error_msg" style="color:white;"><div><?php __(ERREUR_EMPTY_CLEF); ?></div></div>');
				webflowCampandJoy('onloginsuccess');
			</script>
			<?php
		}
		else
		{
			?>
			<script type="text/javascript">
				$('.msg_block').remove();
				$('.home_center_block').prepend('<div class="msg_block error_msg" style="color:white;"><div><?php __(ERREUR_NO_EXIST_ACCOUNT); ?></div></div>');
				webflowCampandJoy('onloginsuccess');
			</script>
			<?php
		}
	}
}
?>