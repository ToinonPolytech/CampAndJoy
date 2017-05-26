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
	if(!isStaff())
	{
		exit();
	}
	$where=array();
	if (isset($_POST["nom"]) && !empty(trim($_POST["nom"])))
	{
		$name=trim($_POST["nom"]);
		$where["OR"] = array("nom" => array(" LIKE ", "%".$name."%"), "prenom" => array(" LIKE ", "%".$name."%"));
	}
	if (isset($_POST["emplacement"]) && !empty(trim($_POST["emplacement"])))
	{
		$name=trim($_POST["emplacement"]);
		$where["emplacement"] = $_POST["emplacement"];
	}
	if (isset($_POST["type"]) && $_POST["type"]!=-1)
	{
		$where["access_level"]=$_POST["type"];
	}
	if (isset($_POST["date_arrive"]) && !empty(trim($_POST["date_arrive"])))
	{
		$temp=explode("/", $_POST["date_arrive"]);
		$time=strtotime($temp[2]."-".$temp[1]."-".$temp[0]);
		if ($time!==false)
			$where["time_arrive"] = $time;
	}
	if (isset($_POST["date_depart"]) && !empty(trim($_POST["date_depart"])))
	{
		$temp=explode("/", $_POST["date_depart"]);
		$time=strtotime($temp[2]."-".$temp[1]."-".$temp[0]);
		if ($time!==false)	
			$where["time_depart"] = strtotime($temp[2]."-".$temp[1]."-".$temp[0]);
	}
	
	$database=new Database();
	if (!empty($where) && $database->count("users AS u LEFT JOIN userinfos AS ui ON u.infoId=ui.id", $where)>0)
	{
		?>
		<div class="custom_table">
			<div class="custom_table_head">
				<div class="ctl_head_line_color custom_table_line">
					<div class="custom_table_col">
						<h6 class="head_text">Clef</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Utilisateur</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Date d'arrivée</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Date de départ</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Emplacement</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Type de compte</h6>
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
				$database->selectJoin("users AS u", array(" userinfos AS ui ON u.infoId=ui.id "), $where, array("u.id", "infoId", "nom", "u.clef", "prenom", "time_arrive", "time_depart", "emplacement", "access_level"));
				$database2=new Database();
				while ($data=$database->fetch())
				{
				?>
					<div class="ctl_body_line custom_table_line">
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["clef"]); ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php if ($data["time_arrive"]>0) { echo date("d/m/Y", $data["time_arrive"]); } else { echo "Non informée"; } ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php if ($data["time_depart"]>0) { echo date("d/m/Y", $data["time_depart"]); } else { echo "Non informée"; } ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["emplacement"]); ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["access_level"]); ?></div>
						</div>
						<div class="custom_table_col">
							<?php if ($cuser->can("CAN_CREATE_ACCOUNT_STAFF")) { ?>
								<a class="action_link" href="/demo/administration/compte/modifier/<?php echo $data["id"]; ?>">[Modifier]</a>
							<?php } ?>
							<a class="action_link" href="/demo/administration/compte/voir/<?php echo $data["id"]; ?>">[Informations]</a>
						</div>
					</div>	
				<?php
				}
				?>
			</div>
		</div>
		<?php
	}
	else if (!empty($where))
	{
		?>
		<div class="info message_block">
			<p>Aucun utilisateur n'a été trouvé.</p>
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