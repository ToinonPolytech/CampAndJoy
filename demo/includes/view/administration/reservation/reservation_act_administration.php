<?php
if (!isset($_SESSION)) // Si on est pas dans un include, on sort le visiteur ^^
	exit();

if ($database->count("reservation", array("type" => $_GET["categorie"], "id" => $_GET["id"]))<=0)
{
	?>
	<div class="info message_block">
		<p>Aucune réservation n'a été effectué pour cette activité.</p>
	</div>
	<?php
}
else
{
	?>
	<ul class="program_list w-list-unstyled">
	<?php
		$database->selectJoin("activities AS a", array(" users AS u ON u.id=a.idDirigeant"), array("a.id" => $_GET["id"]), array("time_start", "a.nom", "a.type", "a.mustBeReserved", "a.prix", "a.points", "a.duree", "a.description", "u.nom AS unom", "u.prenom", "u.id AS uid", "a.id", "a.capaciteMax"));
		$activitesArray=array();
		$data=$database->fetch();
		?>
		<li class="program_list_item">
			<?php
			$nomAct=unserialize($data["nom"]);
			$descAct=unserialize($data["description"]);
			$nom=current($nomAct);
			$desc=current($descAct);
			if (!isset($nomAct[LANG_USER]))
			{
				if (LANG_USER!=DEFAULT_LANGUE)
				{
					if (isset($nomAct[DEFAULT_LANGUE_ETRANGER]))
					{
						$nom=$nomAct[DEFAULT_LANGUE_ETRANGER];
					}
					else if (isset($nomAct[DEFAULT_LANGUE]))
					{
						$nom=$nomAct[DEFAULT_LANGUE];
					}
				}
				else if (isset($nomAct[DEFAULT_LANGUE]))
				{
					$nom=$nomAct[DEFAULT_LANGUE];
				}
			}
			else
			{
				$nom=$nomAct[LANG_USER];
			}
			if (!isset($descAct[LANG_USER]))
			{
				if (LANG_USER!=DEFAULT_LANGUE)
				{
					if (isset($descAct[DEFAULT_LANGUE_ETRANGER]))
					{
						$desc=$descAct[DEFAULT_LANGUE_ETRANGER];
					}
					else if (isset($descAct[DEFAULT_LANGUE]))
					{
						$desc=$descAct[DEFAULT_LANGUE];
					}
				}
				else if (isset($descAct[DEFAULT_LANGUE]))
				{
					$desc=$descAct[DEFAULT_LANGUE];
				}
			}
			else
			{
				$desc=$descAct[LANG_USER];
			}
			$capacite=$data["capaciteMax"];
			if ($capacite>0)
			{
				$database->select("reservation", array("type" => "ACTIVITE", "id" => $data["id"]), "nbrPersonne");
				while ($d=$database->fetch()) { $capacite-=$d["nbrPersonne"]; }
			}
			?>
			<h4>Date de l'activité : <?php echo date("d/m/Y", $data["time_start"]); ?></h4>
			<div class="activity">
				<a class="program_link" href="#"><?php echo htmlspecialchars($nom); ?></a>
				<div class="activity_duration">Heure de fin : <?php echo date("H:i", $data["time_start"]+$data["duree"]*60); ?></div>
				<div class="restaurant_place_and_cost"><span class="place_available"><?php if ($capacite>0) { ?><strong><?php echo $capacite; ?>/<?php echo $data["capaciteMax"]; ?></strong> places <?php if ($data["time_start"]<time()) { echo "étaient "; } ?>disponibles <?php } else { ?>Pas de limites de places<?php } ?></span>&nbsp;</div>
				<div class="activity_tag w-clearfix">
					<div class="activity_tag_title">Tags :</div>
					<?php
						foreach (explode(" ", $data["type"]) as $tag)
						{
							
							?>
								<a class="tags_name" href="#"><?php echo htmlentities($tag); ?></a>
							<?php
							
						}
						if ($data["mustBeReserved"]==1)
						{
							?>
							<a class="tags_name" href="#">RÉSERVABLE</a>
							<?php
						}
						if ($data["points"]>0)
						{
							?>
							<a class="tags_name" href="#">POINTS</a>
							<?php
						}
					?>
					<a class="tags_name" href="#"><?php if ($data["prix"]>0) { echo 'PAYANTE'; } else { echo 'GRATUITE'; } ?></a>
				</div>
				<p class="p_program"><?php echo htmlentities($desc); ?></p>
				<div class="program_time">
					<div><?php echo date("H:i", $data["time_start"]); ?></div>
				</div>
			</div>
		</li>
	</ul>
	<div class="custom_table">
		<div class="custom_table_head">
			<div class="ctl_head_line_color custom_table_line">
				<div class="custom_table_col">
					<h6 class="head_text">Vacancier</h6>
				</div>
				<div class="custom_table_col">
					<h6 class="head_text">Nombre de places</h6>
				</div>
				<div class="custom_table_col">
					<h6 class="head_text">Options</h6>
				</div>
			</div>
		</div>
		<div class="custom_table_body">
		<?php
			$reserved_people=0;
			$database->selectJoin("reservation AS r", array(' users AS u ON idUser=u.id '), array("type" => $_GET["categorie"], "r.id" => $_GET["id"]), array("r.id", "idUser", "time", "nbrPersonne", "nom", "prenom", "valide"));
			while ($data=$database->fetch())
			{
			?>
				<div class="ctl_body_line custom_table_line">
					<div class="custom_table_col">
						<div><?php echo htmlentities($data["prenom"])." ".htmlentities($data["nom"]); ?></div>
					</div>
					<div class="custom_table_col">
						<div><?php echo $data["nbrPersonne"]; ?></div>
					</div>
					<div class="custom_table_col">
						<?php
						if ($data["valide"]==0)
						{
							?>
							En attente de validation 
							<?php
						}
						if ((!isStaff() && $cuser->can(SEND_MESSAGE)) || (isStaff() && $cuser->can(SEND_MESSAGE_STAFF)))
						{
							?>
							<a class="action_link" href="#" onclick="contact(<?php echo $data["idUser"]; ?>); return false;">[Contacter]</a>
							<?php
						}
						?>
					</div>
				</div>	
			<?php
				$reserved_people+=$data["nbrPersonne"];
			}
			?>
		</div>
	</div>
	<?php
}