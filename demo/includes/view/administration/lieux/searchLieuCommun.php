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
	if(!isStaff()  || !$cuser->can("CAN_ADD_LIEU_COMMUN_STAFF"))
	{
		exit();
	}
	$database=new Database();
	$where=array();
	if (isset($_POST["nom"]) && !empty(trim($_POST["nom"])))
	{
		$name=trim($_POST["nom"]);
		$where["nom"] = array(" LIKE ", "%".$name."%");
	}
	if ($database->count("lieu_commun", $where)>0)
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
				$database->select("lieu_commun", $where, array("id", "nom", "estReservable"));
				while ($data=$database->fetch())
				{
				?>
					<div class="ctl_body_line custom_table_line">
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["nom"]); ?></div>
						</div>
						<div class="custom_table_col">
							<a class="action_link" href="/demo/administration/lieuCommun/modifier/<?php echo $data["id"]; ?>">[Modifier]</a> 
							<?php if ($data["estReservable"]) { ?><a class="action_link" href="/demo/<?php echo LANG_USER; ?>/administration/reservation/LIEU_COMMUN/<?php echo $data["id"]; ?>">[Réservations]</a><?php } ?>
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
			<p>Il n'existe aucun lieu commun.</p>
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