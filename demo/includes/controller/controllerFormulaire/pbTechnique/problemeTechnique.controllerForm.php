<?php 
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,images.class.php,problemeTechnique.class.php,problemeTechnique.controller.class.php,user.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if(isset($_SESSION['id']) && isset($_POST['description']))
	{	
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php	
		$user=new User($_SESSION["id"]);
		$photos="";
		if (isset($_FILES))
		{
			$dir="pbTechniques";
			$maxsize=2048000;
			$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' , 'bmp' );
			$imagesUpload=new Image_upload($_FILES, $maxsize, $extensions_valides, $dir);
			$photos=$imagesUpload->getUrl();
		}	
		if (!isset($imagesUpload) || !$imagesUpload->getError())
		{	
			if(!isset($_POST['isBungalow']))
				$isBungalow=false; 
			else
				$isBungalow=true;
			
			$pbTech = new PbTech(NULL, htmlspecialchars($_SESSION['id']), time(), htmlspecialchars ($_POST['description']), $isBungalow, $photos);
			$pbTechController = new Controller_PbTech($pbTech);
			if($pbTechController->isGood())
			{	
				if(isset($_POST['id']))
				{
					$pbTech->setId( htmlspecialchars ($_POST['id']));
					
				}
				$pbTech->saveToDb();
				?>
					<div class="message_block success" id='reponse_controller_msg'>
						<p>Le problème a bien été <?php if(isset($_POST['id'])) { echo 'modifié'; } else { echo 'signalé'; } ?>.</p>
					</div>
				<?php
				if(!isset($_POST['id']))
				{
					$database = new Database();
					$database2 = new Database();
					$database->selectJoin("users AS u", array(" userinfos AS ui ON infoId=ui.id "), array("access_level" => "TECHNICIEN", "OR" => array("time_depart" => -1, "time_depart" => array(">", time()))), "u.id AS id");
					while ($data=$database->fetch())
					{
						$database2->create("notifications", array("idUser" => $data["id"], "titre" => "Un nouveau problème technique a été créé.", "message" => "L'utilisateur ".$user->getPrenom()." a signalé un problème.", "lien" => "/demo/fr/administration/problemesTechniques/vue/".$pbTech->getId(), "date" => time()));
					}
				}
			}
			else
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p><?php echo $pbTechController->getError(); ?></p>
				</div>
				<?php
				$imagesUpload->cancel();
				exit();
			}
		}
		else
		{
			$imagesUpload->cancel();
		}
	}
	else
	{	
		$imagesUpload->cancel();
	}
?> 