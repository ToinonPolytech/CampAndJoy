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
	$where=array("access_level" => 'PARTENAIRE');
	if (isset($_POST["nom"]) && !empty(trim($_POST["nom"])))
	{
		$name=trim($_POST["nom"]);
		$where["OR"] = array("nom" => array(" LIKE ", "%".$name."%"), "prenom" => array(" LIKE ", "%".$name."%"));
	}
	$database=new Database();
	if ($database->count("users", $where)>0)
	{
		?>
		<div class="custom_table">
			<div class="custom_table_head">
				<div class="ctl_head_line_color custom_table_line">
					<div class="custom_table_col">
						<h6 class="head_text">Personne</h6>
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
				$database->select("users", $where, array("id", "nom", "prenom", "access_level"));
				while ($data=$database->fetch())
				{
				?>
					<div class="ctl_body_line custom_table_line">
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php echo $data["access_level"]; ?></div>
						</div>
						<div class="custom_table_col">
							<a class="action_link" href="#" onclick="selectThisUser('<?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?>', '<?php echo $data["id"]; ?>'); return false;">[Sélectionner]</a>
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
			<p>Il n'existe aucun utilisateur ayant pour nom ou prénom : <?php echo htmlentities($name); ?>.</p>
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
	function selectThisUser(name, id)
	{
		$("#name_user").val(name);
		$("#id_user").val(id);
		$(".custom_table.saw").remove();
	}
	$("#id_user").val("");
</script>