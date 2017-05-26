<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!isset($_POST["id"]) || !isset($_POST["date"]))
	{
		exit();
	}
	$array_post=explode("/", $_POST["date"]);
	if (count($array_post)!=3)
		exit();
	
	$time_debut_day=strtotime($array_post[2]."-".$array_post[1]."-".$array_post[0]);
	if ($time_debut_day===false)
		exit();
	
	
	$time_fin_day=$time_debut_day+3600*24;
	$database = new Database();
	$database->select("lieu_commun", array("id" => $_POST["id"]), "timeReservation");
	$data=$database->fetch();
	$timeReservation=@unserialize($data["timeReservation"]);
	
	if (!isset($timeReservation) || empty($timeReservation))
		exit();
	
	$database->select("reservation", array("id" => $_POST["id"], "type" => "LIEU_COMMUN", "time" => array($time_debut_day, $time_fin_day)), array("time", "duree"));
	$array=$timeReservation[date("w", $time_debut_day)];
	while ($data=$database->fetch())
	{
		$hours=date("H", $data["time"])*2+floor(date("i", $data["time"])/30);
		$hours_fin=date("H", $data["time"]+$data["duree"])*2+floor(date("i", $data["time"]+$data["duree"])/30);
		for ($i=$hours;$i<$hours_fin;$i++)
		{
			$array[$i]=false;
		}
	}
	if (!isset($_POST["hours"]))
	{
		for ($i=0;$i<48;$i++)
		{
			if ($i<47)
			{
				if ($array[$i] && !$array[$i+1])
					$array[$i]=false;
			}
			else
			{
				if ($array[$i] && !$array[0])
					$array[$i]=false;
			}
		}
		$allowedHours="[";
		foreach ($array as $index => $boolean)
		{
			if ($boolean)
			{
				if ($allowedHours!="[")
					$allowedHours.=",";
				
				if ($index%2)
				{
					$allowedHours.="'".(($index-1)/2).":30'";
				}
				else
				{
					$allowedHours.="'".($index/2).":00'";
				}
			}
		}
		$allowedHours.="]";
		?>
		<input type="text" name="time" id="time_<?php echo $_POST["id"]; ?>"  class="campandjoy_input input_width_1 w-input" placeholder="Heure" />
		<script type="text/javascript">
			$(document).ready(function(){
				$('input[id="time_<?php echo $_POST["id"]; ?>"]').datetimepicker({
					datepicker:false,
					format:"H:i",
					<?php if ($allowedHours=="[]")
					{
					?>
						timepicker:false,
					<?php
					}
					?>
					allowTimes:<?php echo $allowedHours; ?>,
					onSelectTime:function(dp,$input){
						$("#button_<?php echo $_POST["id"]; ?>").remove();
						$("#duree_<?php echo $_POST["id"]; ?>").remove();
						loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("lieuxCommunsViewForm.php")); ?>", {id:<?php echo htmlentities($_POST["id"]); ?>, date:'<?php echo htmlentities($_POST["date"]); ?>',hours:$input.val()}, "#reservation_<?php echo htmlentities($_POST["id"]); ?>", "append");
					}
				});
			});
		</script>
		<?php
	}
	else
	{
		$array_hours=explode(":", $_POST["hours"]);
		if (count($array_hours)==2)
		{
			if (isset($array[$array_hours[0]*2+floor($array_hours[1]/30)]) && $array[$array_hours[0]*2+floor($array_hours[1]/30)] && 
			isset($array[$array_hours[0]*2+floor($array_hours[1]/30)+1]) && $array[$array_hours[0]*2+floor($array_hours[1]/30)+1])
			{
				?>
				<select name="duree" id="duree_<?php echo $_POST["id"]; ?>" required class="w-select campandjoy_input input_width_1">
					<option value="" disabled selected hidden style="color:gray;">Durée</option>
					<?php
						for ($i=1;$array[$i+$array_hours[0]*2+floor($array_hours[1]/30)];$i++)
						{
							?>
							<option value="<?php echo $i*30*60; ?>"><?php echo $i*30; ?> minutes</option>
							<?php
						}
					?>
				</select>
				<button id="button_<?php echo $_POST["id"]; ?>" class="float_left primary_btn w-button" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('reservation.controllerForm.php')); ?>', {id : <?php echo $_POST["id"]; ?>, type : 'LIEU_COMMUN', nbrPersonnes : 1, date : $(this).parent().children('#date').val(), heure : $('#time_<?php echo htmlentities($_POST["id"]); ?>').val(), duree : $('#duree_<?php echo htmlentities($_POST["id"]); ?>').val()}, '#reservation_<?php echo htmlentities($_POST["id"]); ?>', 'after'); return false;">Réserver</button>
				<?php
			}
		}
	}
?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".page_name").html("Installations");
	});
</script>