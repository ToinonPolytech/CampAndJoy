<?php 
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,restaurant.class.php,restaurant.controller.class.php,images.class.php,user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if(!isStaff() || !$cuser->can("CAN_ADD_RESTAURANT_STAFF"))
	{
		exit();
	}
	if (isset($_POST["name"]) && isset($_POST["telephone"]) && isset($_POST["email"]) && isset($_POST["capacite"]) && isset($_POST["name_user_1"]) && isset($_POST["id_user_1"]))
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
			$dir="restaurant";
			$maxsize=5048000;
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
			$idsGerant="";
			$menu=array();
			ksort($_POST);
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
				else if (strstr($key, "id_user_") && !empty(trim($value)))
				{
					if ($idsGerant!="")
						$idsGerant.=",";
					
					$idsGerant.=$value;
				}
				else if (strstr($key, "categorie_name") && !empty(trim($value)))
				{
					$temp=explode("_", $key);
					$id_categorie=$temp[count($temp)-1];
					$menu[$value]=array();
					$d=1;
					while (isset($_POST["categorie_".$id_categorie."_".$d."_prix"]) && isset($_POST["categorie_".$id_categorie."_".$d."_nom"]) && !empty(trim($_POST["categorie_".$id_categorie."_".$d."_prix"])) && !empty(trim($_POST["categorie_".$id_categorie."_".$d."_nom"])))
					{
						$menu[$value][trim($_POST["categorie_".$id_categorie."_".$d."_nom"])]=trim($_POST["categorie_".$id_categorie."_".$d."_prix"]);
						$d++;
					}
				}
			}
			$horaires=serialize($horaires);
			$menu=serialize($menu);
			$restaurant=new Restaurant(NULL, $_POST["name"], $_POST["telephone"], $_POST["email"], $idsGerant, $_POST["capacite"], $horaires, $photos, $menu);
			if (isset($_POST["id"]))
			{
				$restaurant->setId($_POST["id"]);
			}
			$controllerRestaurant=new Controller_Restaurant($restaurant);
			if ($controllerRestaurant->isGood())
			{
				$restaurant->saveToDb();
				?>
				<div class="message_block success" id='reponse_controller_msg'>
					<p>Le restaurant a bien été <?php if (isset($_POST["id"])) { echo "modifié"; } else { echo "créé"; } ?>.</p>
				</div>
				<script type="text/javascript">
					$("#part_1, #part_2, #part_3, #part_4, #part_5").show();
				</script>
				<?php
			}
			else
			{
				?>
				<div class="message_block error" id='reponse_controller_msg'>
					<p><?php echo $controllerRestaurant->getErrors(); ?></p>
				</div>
				<script type="text/javascript">
					$("#part_1, #part_2, #part_3, #part_4, #part_5").show();
					$("form").find("a").each(function(){
						if ($(this).html()=="Suivant" || $(this).html()=="Retour")
						{
							if ($(this).html()=="Suivant")
							{
								$(this).after("<div class='customcaj_separator'></div>");
							}
							$(this).remove();
						}
					});
				</script>
				<?php
				$imagesUpload->cancel();
				exit();
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p><?php echo $imagesUpload->getErrors(); ?></p>
			</div>
			<script type="text/javascript">
				$("#part_1, #part_2, #part_3, #part_4, #part_5").show();
				$("form").find("a").each(function(){
					if ($(this).html()=="Suivant" || $(this).html()=="Retour")
					{
						if ($(this).html()=="Suivant")
						{
							$(this).after("<div class='customcaj_separator'></div>");
						}
						$(this).remove();
					}
				});
			</script>
			<?php
			$imagesUpload->cancel();
			exit();
		}	
	}
?>