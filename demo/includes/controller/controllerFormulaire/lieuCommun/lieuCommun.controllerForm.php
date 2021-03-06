<?php 
	if (!isset($require))
	{
		$require="";
	}
	$require.="lieuCommun.class.php,lieuCommun.controller.class.php,images.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
		exit();
	
	if (isset($_POST['nom']) && isset($_POST['description']))
	{	
		?>
		<script type="text/javascript">
			$("div[id='reponse_controller_msg_read']").remove();
			$("div[id='reponse_controller_msg']").attr("id", 'reponse_controller_msg_read');
		</script>
		<?php	
		$photos="";
		if (isset($_FILES))
		{
			$dir="lieuCommun";
			$maxsize=2048000;
			$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' , 'bmp' );
			$imagesUpload=new Image_upload($_FILES, $maxsize, $extensions_valides, $dir);
			$photos=$imagesUpload->getUrl();
		}
		if (!isset($imagesUpload) || !$imagesUpload->getError())
		{
			$horaires=array();
			for ($i=0;$i<7;$i++)
			{
				$horaires[$i]=array();
				for ($j=0;$j<48;$j++)
				{
					$horaires[$i][$j]=false;
				}
			}
			$estReservable=false;
			if (isset($_POST["estReservable"]) && $_POST["estReservable"])
			{
				$estReservable=true;
			}
			foreach ($_POST as $key => $value)
			{
				if (strstr($key, "horaire_open"))
				{
					$key_close=str_replace("horaire_open", "horaire_close", $key);
					if (isset($_POST[$key_close]) && !empty($_POST[$key_close]) && !empty($value))
					{
						$temp=explode("_", str_replace("horaire_open_", "", $key));
						$day=$temp[0];
						$day_number=0;
						if ($day=="lundi") $day_number=1;
						else if ($day=="mardi") $day_number=2;
						else if ($day=="mercredi") $day_number=3;
						else if ($day=="jeudi") $day_number=4;
						else if ($day=="vendredi") $day_number=5;
						else if ($day=="samedi") $day_number=6;
						// On doit gérer les horaires de $horaires[$day_number]
						$temp=explode(":", $value);
						$debut_heure=$temp[0]*2;
						if ($temp[1]==30) $debut_heure+=1;
						$temp=explode(":", $_POST[$key_close]);
						$fin_heure=$temp[0]*2;
						if ($temp[1]==30) $fin_heure+=1;
						if ($fin_heure>=$debut_heure)
						{
							for ($i=$debut_heure;$i<=$fin_heure;$i++)
							{
								$horaires[$day_number][$i]=true;
							}
						}
						else
						{
							for ($i=$debut_heure;$i<=47;$i++)
							{
								$horaires[$day_number][$i]=true;
							}
							for ($i=0;$i<=$fin_heure;$i++)
							{
								$horaires[$day_number][$i]=true;
							}
						}
					}
				}
			}
			$horaires=serialize($horaires);
			$LC = new lieuCommun(NULL,htmlspecialchars($_POST['nom']), htmlspecialchars($_POST['description']), $estReservable, $horaires, $photos);
			
			if(isset($_POST['id']))
				$LC->setId($_POST['id']);
			
			$LCController=new Controller_LieuCommun($LC);
			if($LCController->isGood()){
				$LC->saveToDb();
				?>
				<div class="message_block success" id='reponse_controller_msg'>
					<p>Le lieu a bien été <?php if(isset($_POST['id'])) { echo 'modifié'; } else { echo 'créé'; } ?>.</p>
				</div>		
				<script type="text/javascript">
					$("div[id*='part_']").show();
				</script>
				<?php
			}
			else
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p><?php echo $LCController->getError(); ?></p>
				</div>
				<script type="text/javascript">
					$("div[id*='part_']").show();
				</script>
				<?php
				$imagesUpload->cancel();
				exit();
			}
		}
	}	
?>