<?php
if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,equipe.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
if (!auth())
{
	exit();
}

$db = new Database();
$db->selectJoin("equipe",array( 'equipe_membres AS em ON em.idEquipe=id'),array('em.idUser' =>$_SESSION['id']),array('id','score','nom','em.peutModifier AS peutModifier'));
$db2 = new Database(); 


?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Mes équipes</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div id="retour"></div>
		<div class="section_title_wrapper">
			<h2 class="heading">Mes équipes</h2>
			<p>Les équipes permettent de participer à des activités avec d'autres personnes, réaliser des défis, gagner des points afin peut être d'être sur le podium en fin de semaine. 
			Vous pourrez ainsi remporter des cadeaux inédits !</p>
			<div class="section_divide"></div>
			<a class="action_link" href="#" onclick="$('#form-equipe-hide').toggle('fast');$(this).remove();return false;">[Créer une équipe]</a>
			<div id="form-equipe-hide" style="display:none">
				<label for="nom">Nom de l'équipe</label>
				<form id="form-equipe"  class="horizontal_form">					
					<input class="campandjoy_input w-input" type="text" name="nom" id="nom"/>
					<button class="primary_btn w-button" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('equipe.controllerForm.php')); ?>',{nom : $('#nom').val()}, '#retour', 'prepend', false); return false;">Créer</button>
				</form>
			</div>
			<div class="custom_table" id="tables_all_equipes">
				<div class="custom_table_head">
					<div class="ctl_head_line_color custom_table_line">
						<div class="custom_table_col">
							<h6 class="head_text">Nom</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Nombre de membres</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Score</h6>
						</div>
						<div class="custom_table_col">
							<h6 class="head_text">Action</h6>
						</div>						
					</div>
				</div>
				<div class="custom_table_body">
					<?php
					while($data=$db->fetch())
					{	
						?>	
						<div id="<?php echo $data["id"]; ?>" class="ctl_body_line custom_table_line">
							<div class="custom_table_col">
								<div><?php echo htmlentities($data['nom']);?></div>
							</div>
							<div class="custom_table_col">
								<div><?php echo $db2->count("equipe_membres", array("idEquipe" => $data["id"]));?></div>
							</div>
							<div class="custom_table_col">
								<div><?php echo htmlentities($data['score']);?></div>
							</div>
							<div class="custom_table_col">
								<a class="action_link" onclick="showMembres('<?php echo $data["id"]; ?>'); return false;" href="#">[Voir les membres]</a>
								<?php 
								if($data['peutModifier'])
								{
									?>				
									<a class="action_link" data-ix="show-modal-msg" data-title="Suppression" data-message="Attention vous vous apprêtez à supprimer cette équipe. Tous les points seront perdus." data-on-confirm="webflowCampandJoy('close-modal-msg'); loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('supprimerEquipe.php')); ?>', {id :<?php echo $data['id'];?>}, '#retour', 'prepend');"  data-on-refuse="webflowCampandJoy('close-modal-msg');" data-type="error" href="#">[Supprimer]</a>
									<a class="action_link" onclick="$('#ajoutMb_<?php echo $data["id"]; ?>').toggle('fast');return false;" href="#">[Ajouter un membre]</a>																		
									<?php
								}
								?>
							</div>
						</div>		
						<div class="horizontal_form" id="ajoutMb_<?php echo $data["id"]; ?>" style="margin-top:10px;display:none">
							<div class="custom_table_col">
								<input class="campandjoy_input w-input" maxlength="256" id="name_user" name="name_user" placeholder="Nom ou prénom de l'utilisateur" type="text">
							</div>
							<div class="custom_table_col">
								<div>
									<label class="control control--checkbox">Peut-il gérer l'équipe ?
										<input type="checkbox"id="droit_modif" name="droit_modif" type="checkbox">
										<div class="control__indicator" style="margin-left:50px;"></div>
									</label>
								</div>
							</div>
							<div class="custom_table_col">
								<div>
								</div>
							</div>
						</div>							
					<?php					
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var delay;
	var lastSearch="";
	function launchSearch(inp)
	{
		delay=Date.now();
		setTimeout(function(){
			if (parseInt(delay+500)<=Date.now())
			{
				if (lastSearch!=$("#"+inp).find("input[id='name_user']").val())
				{
					lastSearch=$("#"+inp).find("input[id='name_user']").val();
					loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('ajoutMembreSearchUser.php')); ?>",{"nom" : lastSearch, input : inp}, "#"+inp, "after");
				}
			}
		}, 500);
	}
	$("input[id='name_user']").on("keypress", function(){
		launchSearch($(this).parent().parent().attr("id"));
	});
	function showMembres(idEquipe)
	{
		loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('membresEquipes.php')); ?>", {id:idEquipe}, "#tables_all_equipes", "before");
	}
	$(document).ready(function() {
		$(".page_name").html("Mes équipes");
	});
</script>