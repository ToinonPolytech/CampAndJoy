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
	if (isset($_POST["nom"]) && !empty(trim($_POST["nom"])))
	{
		$name=trim($_POST["nom"]);
		$temp=explode(" ", $name);
		if (count($temp)>1)
		{
			$nom=$temp[1];
			$prenom=$temp[0];
		}
		else
		{
			$nom=$name;
			$prenom=$name;
		}
	}
	else
	{
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#resultSearch.saw").remove();
				$("#resultSearch").addClass("saw");
				$(".info.message_block.saw").remove();
				$(".info.message_block").addClass("saw");
			});
		</script>
		<?php
		exit();
	}
	if (isset($_POST["input"]))
	{
		$input=$_POST["input"];
	}
	else
	{
		exit();
	}
	$idEquipe=str_replace('ajoutMb_', '', $_POST["input"]);
	$database=new Database();
	$database->prepare("SELECT COUNT(*) FROM users WHERE id NOT IN (SELECT idUser FROM equipe_membres WHERE idEquipe=:idEquipe) AND (nom LIKE :nom OR prenom LIKE :prenom)");
	$database->execute(array("idEquipe" => $idEquipe, "nom" => '%'.$nom.'%', "prenom" => '%'.$prenom.'%'));
	if ($database->fetchColumn()>0)
	{
		?>
		<div class="custom_table" id="resultSearch">
			<div class="custom_table_head">
				<div class="ctl_head_line_color custom_table_line">
					<div class="custom_table_col">
						<h6 class="head_text">Personne</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Emplacement</h6>
					</div>
					<div class="custom_table_col">
						<h6 class="head_text">Options</h6>
					</div>
				</div>
			</div>
			<div class="custom_table_body">
			<?php
				$database->prepare("SELECT users.id AS id, nom, prenom, emplacement FROM users LEFT JOIN userinfos AS ui ON ui.id=infoId WHERE users.id NOT IN (SELECT idUser FROM equipe_membres WHERE idEquipe=:idEquipe) AND (nom LIKE :nom OR prenom LIKE :prenom) ORDER BY nom ASC LIMIT 20");
				$database->execute(array("idEquipe" => $idEquipe, "nom" => '%'.$nom.'%', "prenom" => '%'.$prenom.'%'));
				while ($data=$database->fetch())
				{
				?>
					<div class="ctl_body_line custom_table_line">
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?></div>
						</div>
						<div class="custom_table_col">
							<div><?php echo htmlentities($data["emplacement"]); ?></div>
						</div>
						<div class="custom_table_col">
							<a class="action_link" href="#" onclick="loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("equipeMembres.controllerForm.php")); ?>', {idUser:<?php echo $data["id"]; ?>, droits:$('#droit_modif').is(':checked'), idEquipe:<?php echo htmlentities($idEquipe); ?>}, '#<?php echo htmlentities($_POST["input"]); ?>', 'before'); return false;">[Ajouter]</a>
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
			<p>Il n'existe aucun utilisateur ayant pour nom ou prénom <?php echo htmlentities($name); ?> et n'étant pas déjà dans votre équipe.</p>
		</div>
		<?php
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#resultSearch.saw").remove();
		$("#resultSearch").addClass("saw");
		$(".info.message_block.saw").remove();
		$(".info.message_block").addClass("saw");
	});
</script>