<?php 
if (!isset($require))
{
	$require="";
}
$require.="partenaire.class.php,partenaire.controller.class.php,images.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if (!auth())
	exit();

if(isset($_POST['nom']) && isset($_POST['libelle']) && isset($_POST['mail']) && isset($_POST['siteWeb']) && isset($_POST['telephone']))
{
	$photos="";
		if (isset($_FILES))
		{
			$dir="partenaire";
			$maxsize=2048000;
			$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' , 'bmp' );
			$imagesUpload=new Image_upload($_FILES, $maxsize, $extensions_valides, $dir);
			$photos=$imagesUpload->getUrl();
		}
		if(isset($_POST['id_user'])){
			$idUser= $_POST['id_user'];
		}
		else
		{
			$idUser=0;
		}
		if (!isset($imagesUpload) || !$imagesUpload->getError())
		{
			$partenaire = new Partenaire(NULL,$idUser, htmlspecialchars($_POST['nom']), htmlspecialchars($_POST['libelle']), htmlspecialchars($_POST['mail']), htmlspecialchars($_POST['siteWeb']), htmlspecialchars($_POST['telephone']),$photos);
			$partenaireController = new Controller_Partenaire($partenaire);
			if($partenaireController->isGood())
			{
				if(isset($_POST['id'])){
					$partenaire->setId($_POST['id']); 
				}
				$partenaire->saveToDb();
				?>
					<div class="message_block success" id='reponse_controller_msg'>
						<p>Le partenaire a bien été <?php if(isset($_POST['id'])) { echo 'modifié'; } else { echo 'créé'; } ?>.</p>
					</div>				
				<?php
			}
			else
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p><?php echo $partenaireController->getError(); ?></p>
				</div>
				<?php
				$imagesUpload->cancel();
				exit();
			}
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