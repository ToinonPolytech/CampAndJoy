<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if ($includeDone)
	{
		?>
		<script type="text/javascript">
			$( document ).ready(function() { Webflow.require('tabs').redraw(); });
		</script>
		<?php
	}
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	if ($user->getUserInfos()->getClef()!=$_COOKIE["clef"])
	{
		//header("Location:/demo/compte");
		exit();
	}
	$dateDep= date('d/m/Y', $user->getUserInfos()->getTimeDepart());
	$debutJournee=strtotime(date('y-m-d', $user->getUserInfos()->getTimeDepart()));
	$finJournee=$debutJournee+3600*24;
	$db = new Database();
	$db2= new Database();
	$db->select('reservation',array('type' => 'ETAT_LIEUX', 'time' => array($debutJournee, $finJournee)),"time");
	$db2->select('etat_lieux',array('debutTime' => array('>=', $debutJournee), 'finTime' => array('<=', $finJournee)));
	$hDispo=array();
	$hPrise=array();
	while($res=$db->fetch())
	{
		if (isset($hPrise[$res['time']]))
			$hPrise[$res['time']]+=1;
		else
			$hPrise[$res['time']]=1;
	}
	while($edl=$db2->fetch())
	{	
		for($i=$edl['debutTime'];$i<=$edl['finTime'];$i+=60*$edl['duree_moyenne'])
		{
			if (isset($hDispo[$i]))
				$hDispo[$i]+=1;
			else
				$hDispo[$i]=1;
		}
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Réservation de l'état des Lieux</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<h4>Réserver votre état des lieux</h4>
		<div class="w-form">
			<form id="reserver" name="reserver" class="horizontal_form">
				<label for="horaire_edl"></label>
				<select class="w-select" name="horaire_edl" id="horaire_edl">
					<option value="-1">Sélectionner le créneau horaire qui vous convient le mieux</option>
				<?php 
					foreach ($hDispo as $key => $val)
					{
						if (!isset($hPrise[$key]) || $val-$hPrise[$key]>0)
						{
							echo "<option value='".$key."'>".date("H:i", $key)."</option>"; 
						}
					}
				?>
				</select>
				<a class="btn_set_aside w-button" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('reservation.controllerForm.php')); ?>', {nbrPersonnes : 1, type : 'ETAT_LIEUX', id : -1, time : $('#horaire_edl').val()}, '#reserver', 'before'); return false;">Réserver</a>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
    $(".page_name").html("Services > État des Lieux");
});
</script>