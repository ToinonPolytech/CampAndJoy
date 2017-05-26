<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,equipe_membres.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);	
	if (!isset($_POST["id"]))
	{
		exit();
	}
	$idEquipe=$_POST["id"];
	$em1 = new Equipe_Membres($idEquipe,$_SESSION['id']); 
	if($em1->getPeutModifier()==NULL)
	{
		exit();
	}
	else
	{
		$database=new Database();
		$database->setAsc();
		$database->setOrderCol("nom");
		$database->selectJoin("users AS u", array(" equipe_membres AS em ON u.id=em.idUser "), array("em.idEquipe" => $idEquipe), array("id", "nom", "prenom", "peutModifier"));
		?>
		<br/><a href="#" class="primary_btn w-button" onclick='$("#tables_all_equipes").show(); $("#viewMembres").remove(); $(this).remove();'>Retour</a>
		<div class="custom_table" id="viewMembres" style="display:none;">
			<div class="custom_table_head">
				<div class="ctl_head_line_color custom_table_line">
					<div class="custom_table_col">
						<h6 class="head_text">Personne</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Gestion de l'équipe</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Options</h6>
					</div>
				</div>
			</div>
			<div class="custom_table_body">
				<?php
				while ($data=$database->fetch())
				{
					?>
						<div class="ctl_body_line custom_table_line" id="user_<?php echo $data["id"]; ?>">
							<div class="custom_table_col">
								<div><?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?></div>
							</div>
							<div class="custom_table_col">
								<div>
									<label class="control control--checkbox">Peut-il gérer l'équipe ?
										<input rel="<?php echo $idEquipe; ?>" id="droit_modif<?php echo $data["id"]; ?>" name="droit_modif<?php echo $data["id"]; ?>" type="checkbox" <?php if(!$em1->getPeutModifier() || $data["id"]==$_SESSION["id"]) { echo "disabled "; } if ($data["peutModifier"]) { echo "checked"; } ?>>
										<div class="control__indicator"></div>
									</label>
								</div>
							</div>
							<div class="custom_table_col">
							<?php
								if($em1->getPeutModifier() && $data["id"]!=$_SESSION["id"])
								{
									?>
									<a class="action_link" href="#" data-ix="show-modal-msg" data-type="error" data-title="Êtes-vous certains ?" data-message="Vous êtes sur le point de retirer <?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?> de votre équipe." data-on-confirm="loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("supprimerMembre.php")); ?>', {idUser:<?php echo htmlentities($data["id"]); ?>, idEquipe:<?php echo htmlentities($idEquipe); ?>}, '#viewMembres', 'before'); webflowCampandJoy('close-modal-msg');" data-on-refuse="webflowCampandJoy('close-modal-msg');">[Retirer]</a>
									<?php
								}
								?>
							</div>
						</div>	
					<?php
				}
				?>
			</div>
		</div>
		<script type="text/javascript">
			$("#tables_all_equipes").slideUp();
			$("#viewMembres").slideDown();
			$(document).ready(function(){
				$(".page_name").html("Administration");
				$("input[type='checkbox']").on("click", function(){
					loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("equipeMembres.controllerForm.php")); ?>', {idUser:String($(this).attr("id")).replace("droit_modif", ""), droits:$(this).is(':checked'), idEquipe:$(this).attr("rel")}, '#viewMembres', 'before');
				});
			});
			
		</script>
	<?php
	}
?>