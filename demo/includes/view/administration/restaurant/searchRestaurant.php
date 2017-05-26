<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	$is_restaurateur=false;
	$where=array();
	$where["idsUsers"]=array(" LIKE ", "%".$_SESSION["id"]."%");
	$database->select("restaurant AS r", $where, array("idsUsers"));
	while ($data=$database->fetch() && !$is_restaurateur)
	{
		$temp=explode(",", $data["idsUsers"]);
		foreach ($temp as $v)
		{
			if ($v==$_SESSION["id"])
				$is_restaurateur=true;
		}
	}
	if((!isStaff() && !$is_restaurateur)  || !$cuser->can("CAN_ADD_RESTAURANT"))
	{
		exit();
	}
	if (isset($_POST["nom"]) && !empty(trim($_POST["nom"])))
	{
		$name=trim($_POST["nom"]);
		$where["r.nom"] = array(" LIKE ", "%".$name."%");
	}
	if ($database->count("restaurant AS r", $where)>0)
	{
		?>
		<div class="custom_table">
			<div class="custom_table_head">
				<div class="ctl_head_line_color custom_table_line">
					<div class="custom_table_col">
						<h6 class="head_text">Nom</h6>
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
				$database->select("restaurant AS r", $where, array("r.id", "r.nom"));
				while ($data=$database->fetch())
				{
				?>
					<div class="ctl_body_line custom_table_line">
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["nom"]); ?></div>
						</div>
						<div class="custom_table_col">
							<a class="action_link" href="/demo/administration/restaurant/modifier/<?php echo $data["id"]; ?>">[Modifier]</a> 
							<a class="action_link" href="/demo/administration/reservation/RESTAURANT/<?php echo $data["id"]; ?>">[Réservations]</a>
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
			<p>Il n'existe aucun restaurant que vous pouvez administré et ayant un nom semblable.</p>
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