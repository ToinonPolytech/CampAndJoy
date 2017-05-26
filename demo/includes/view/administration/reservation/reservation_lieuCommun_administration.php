<?php
if (!isset($_SESSION))
	exit();

$timestamp=time();
if (isset($_GET["date"]))
{
	$timestamp=strtotime($_GET["date"]);
	if ($timestamp===false)
		$timestamp=time();
	else
	{
		$array=explode("-", $_GET["date"]);
		$date=$array[2]."/".$array[1]."/".$array[0];
	}
}
?>
<div class="section_title_wrapper">
	<h2 class="heading"><?php echo htmlentities($database->getValue("lieu_commun", array("id" => $_GET["id"]), "nom")); ?></h2>
	<div class="section_divide"></div>
	<div class="fliter_block">
		<div class="w-form">
			<form id="formRestaurant" name="formRestaurant">
				<input <?php if (isset($date)) { echo "value='".$date."'"; } ?> class="campandjoy_input w-input" data-name="datepicker" id="datepicker" maxlength="256" name="datepicker" placeholder="Date" type="text">
			</form>
			<a class="primary_btn w-button wrapp_btn_null" href="/demo/<?php echo LANG_USER; ?>/administration/reservation/LIEU_COMMUN/<?php echo $_GET["id"]; ?>">Voir les dernières réservation</a>
		</div>
	</div>
	<p class="paragraph"><?php if (isset($date)) { echo "Voici la liste des réservations pour le ".$date; } else { echo "Voici les 20 dernières réservations qui ont été effectuées"; } ?> .&nbsp;</p>
</div>
<?php
if ((isset($date) && $database->count("reservation", array("type" => $_GET["categorie"], "id" => $_GET["id"], "time" => array($timestamp, $timestamp+24*3600)))<=0) || (!isset($date) && $database->count("reservation", array("type" => $_GET["categorie"], "id" => $_GET["id"]))<=0))
{
	?>
	<div class="info message_block">
		<p>Aucune réservation n'a été effectué <?php if (isset($date)) { echo "pour le ".$date; } ?>.</p>
	</div>
	<?php
}
else
{
	?>
	<div class="custom_table">
		<div class="custom_table_head">
			<div class="ctl_head_line_color custom_table_line">
				<div class="custom_table_col">
					<h6 class="head_text">Date</h6>
				</div>
				<div class="custom_table_col">
					<h6 class="head_text">Vacancier</h6>
				</div>
				<div class="custom_table_col">
					<h6 class="head_text">Options</h6>
				</div>
			</div>
		</div>
		<div class="custom_table_body">
		<?php
			if (isset($date))
			{
				$database->setOrderCol("time");
				$database->setAsc();
				$database->selectJoin("reservation AS r", array(' users AS u ON idUser=u.id '), array("type" => $_GET["categorie"], "r.id" => $_GET["id"], "time" => array($timestamp, $timestamp+24*3600)), array("r.id", "idUser", "time", "nom", "prenom"));
			}
			else
			{
				$database->setOrderCol("r.id");
				$database->setDesc();
				$database->setLimit(20);
				$database->selectJoin("reservation AS r", array(' users AS u ON idUser=u.id '), array("type" => $_GET["categorie"], "r.id" => $_GET["id"]), array("r.id", "idUser", "time", "nom", "prenom"));
			}
			while ($data=$database->fetch())
			{
			?>
				<div class="ctl_body_line custom_table_line">
					<div class="custom_table_col">
						<div><?php echo date("d/m/Y H:i", $data["time"]); ?></div>
					</div>
					<div class="custom_table_col">
						<div><?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?></div>
					</div>
					<div class="custom_table_col">
						<a class="action_link" href="#" onclick="contact(<?php echo $data["idUser"]; ?>); return false;">[Contacter]</a>
					</div>
				</div>	
			<?php
			}
			?>
		</div>
	</div>
	<?php
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$.datetimepicker.setLocale('fr');
	$('#datepicker').datetimepicker({
		timepicker:false,
		formatDate:'d.m.y',
		format:'d/m/y',
		onSelectDate:function(ct,$i){
			seekRestaurantAdmin('/demo/<?php echo LANG_USER; ?>/administration/reservation/LIEU_COMMUN/<?php echo $_GET["id"]; ?>/');
		}
	});
});
function seekRestaurantAdmin(page)
{
	var argsPage="";
	if ($("#datepicker").val()!="")
	{
		argsPage=$("#datepicker").val();
	}
	if (argsPage!="")
		loadToMain(page+argsPage, {});
}
</script>