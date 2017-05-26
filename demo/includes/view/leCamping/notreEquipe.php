<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="user.class.php,user.controller.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
		$db = new Database();
	function presentation($type){
		global $db;
		$db->select("users", array('access_level' => $type));
		?>
		<table>
		<?php
		while($data=$db->fetch()){
			$photos_array=explode(",", $data["photo"]);
			?>
			<tr>
				<td style="padding-top:15px;">
					<?php if (is_array($photos_array) && !empty($photos_array[0])) { ?><img src="<?php echo $photos_array[0]; ?>" width="100px" height="100px" ><?php } ?>
				</td>		
				<td style="padding-left:50px;padding-top:15px;">
					<p class="event_master_name"><?php echo $data['prenom'].' '.strtoupper($data['nom']);?></p><br/>
					<p><?php if(!empty($data['description'])){echo $data['description'];}else{echo "Aucune description n'a été fournie pour cette personne";} ?> 
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Notre équipe</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">NOTRE EQUIPE</h2>
			<div class="section_divide"></div>
			<img src="/demo/images/equipe.jpg" style="width:304px;height:228px;">
			<p class="paragraph">Présentation de notre équipe, qui oeuvre pour que votre séjour soit inoubliable.&nbsp;</p>
			<h4>LES GERANTS  <i class="fa fa-plus-square" onclick="$('#patron_hide').toggle('fast');return false;" aria-hidden="true"></i></h4>
			<div id="patron_hide" style="display:none;"><?php presentation("PATRON"); ?></div>
			<div class="customcaj_separator"></div>
			<h4>LES ANIMATEURS  <i class="fa fa-plus-square" onclick="$('#anim_hide').toggle('fast');return false;" aria-hidden="true"></i></h4>
			<div id="anim_hide" style="display:none;"><?php presentation("ANIMATEUR"); ?></div>
			<div class="customcaj_separator"></div>
			<h4>LA RECEPTION  <i class="fa fa-plus-square" onclick="$('#accueil_hide').toggle('fast');return false;" aria-hidden="true"></i></h4>
			<div id="accueil_hide" style="display:none;"><?php presentation("ACCUEIL"); ?></div>
			<div class="customcaj_separator"></div>
			<h4>LES TECHNICIENS  <i class="fa fa-plus-square" onclick="$('#technicien_hide').toggle('fast');return false;" aria-hidden="true"></i></h4>
			<div id="technicien_hide" style="display:none;"><?php presentation("TECHNICIEN"); ?></div>
			<div class="customcaj_separator"></div>
			<h4>NOS SURVEILLANTS DE BASSIN  <i class="fa fa-plus-square" onclick="$('#sauveteur_hide').toggle('fast');return false;" aria-hidden="true"></i></h4>
			<div id="sauveteur_hide" style="display:none;"><?php presentation("SAUVETEUR"); ?></div>
			<div class="customcaj_separator"></div>			
		</div>
	</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
	$(".page_name").html("Le Camping");
});
</script>