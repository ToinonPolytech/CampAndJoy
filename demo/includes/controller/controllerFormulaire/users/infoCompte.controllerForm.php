<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,images.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
		exit();
	$user = new User($_SESSION['id']);	

	if(isset($_POST['description']) || isset($_FILES)){
		$photos="";
		if (isset($_FILES))
		{
			$dir="user";
			$maxsize=2048000;
			$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' , 'bmp' );
			$imagesUpload=new Image_upload($_FILES, $maxsize, $extensions_valides, $dir);
			$photos=$imagesUpload->getUrl();
		}
		
		if(isset($_POST['description']))
		{
			$user->setDescription($_POST['description']);
		}
		if(isset($imagesUpload) && !$imagesUpload->getError() && !empty($photos))
		{
			$user->setPhoto($photos);
		}
		$cuser = new Controller_User($user);
		if($cuser->isGood())
		{
			$user->saveToDb();
			?>
			<div class="message_block success" id='reponse_controller_msg'>
				<p>Vos informations ont été mises à jour.</p>
			</div>				
			<?php
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $cuser->getError(); ?></p>
			</div>
			<?php
			$imagesUpload->cancel();
			exit();
		}
	}
	else
	{
		?>
		<div class="message_block error" id='reponse_controller_msg'>
			<p>ERREUR : Un problème est survenu lors de l'envoi du formulaire.</p>
		</div>
		<?php
	}
?>