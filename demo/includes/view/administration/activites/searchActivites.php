<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if(!isStaff() || !$cuser->can("CAN_CREATE_ACTIVITIES"))
	{
		exit();
	}
	$where=array();
	if (isset($_POST["nom"]) && !empty(trim($_POST["nom"])))
	{
		$name=trim($_POST["nom"]);
		$where["a.nom"] = array(" LIKE ", "%".$name."%");
	}
	if (isset($_POST["date"]))
	{
		$ex=explode("/", $_POST["date"]);
		if (count($ex)==3)
		{
			$timestamp=strtotime($ex[2]."-".$ex[1]."-".$ex[0]);
			if ($timestamp===false)
				$timestamp=time();
			else
			{
				$date=$_POST["date"];
				$where["time_start"] = array($timestamp, $timestamp+3600*24);
			}
		}
	}
	if (isset($_POST["user"]) && $_POST["user"]!=-1)
	{
		$user_id=$_POST["user"];
		$where["idDirigeant"] = $user_id;
	}
	$database=new Database();
	if ($database->count("activities AS a", $where)>0)
	{
		?>
		<div class="custom_table">
			<div class="custom_table_head">
				<div class="ctl_head_line_color custom_table_line">
					<div class="custom_table_col">
						<h6 class="head_text">Date</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Activité</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Animateur en Charge</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Options</h6>
					</div>
				</div>
			</div>
			<div class="custom_table_body">
			<?php
				$database->setAsc();
				$database->setOrderCol("nom");
				$database->setLimit(20);
				$database->selectJoin("activities AS a", array(" users AS u ON idDirigeant=u.id "), $where, array("a.id", "a.nom", "time_start", "idDirigeant", "u.nom AS unom", "prenom", "mustBeReserved"));
				$database2=new Database();
				while ($data=$database->fetch())
				{
					if ($data["mustBeReserved"])
					{
						$countReservation=0;
						$database2->select("reservation", array("id" => $data["id"], "type" => "ACTIVITE"), "nbrPersonne");
						while ($d=$database2->fetch())
						{
							$countReservation+=$d["nbrPersonne"];
						}
					}
					$nomAct=unserialize($data["nom"]);
					$nom=current($nomAct);
					if (!isset($nomAct[LANG_USER]))
					{
						if (LANG_USER!=DEFAULT_LANGUE)
						{
							if (isset($nomAct[DEFAULT_LANGUE_ETRANGER]))
							{
								$nom=$nomAct[DEFAULT_LANGUE_ETRANGER];
							}
							else if (isset($nomAct[DEFAULT_LANGUE]))
							{
								$nom=$nomAct[DEFAULT_LANGUE];
							}
						}
						else if (isset($nomAct[DEFAULT_LANGUE]))
						{
							$nom=$nomAct[DEFAULT_LANGUE];
						}
					}
				?>
					<div class="ctl_body_line custom_table_line">
						<div class="custom_table_col">
							<div><?php echo date("d/m/Y H:i", $data["time_start"]); ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php echo htmlentities($nom); ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["prenom"])." ".htmlentities($data["unom"]); ?></div>
						</div>
						<div class="custom_table_col">
							<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/administration/activites/modifier/<?php echo $data["id"]; ?>">[Modifier]</a> 
							<?php if ($data["mustBeReserved"] && $countReservation>0) { ?>
								<a class="action_link" href="/demo/<?php echo LANG_USER; ?>/administration/reservation/ACTIVITE/<?php echo $data["id"]; ?>">[<?php echo $countReservation; ?> Réservation<?php if ($countReservation>1) { echo 's'; } ?>]</a>
							<?php } ?>
						</div>
					</div>	
				<?php
				}
				?>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="info message_block">
			<p>Il n'existe aucune activité ayant ces critères de recherche.</p>
		</div>
		<?php
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".custom_table.saw").remove();
		$(".custom_table").addClass("saw");
		$(".info.message_block.saw").remove();
		$(".info.message_block").addClass("saw");
	});
</script>