<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="activities.class.php,user.class.php,user.controller.class.php,reservation.class.php,reservation.controller.class.php,";
	$is_index=true;
	$paiement=true;
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (isset($_POST["Ref"]) && isset($_POST["Auto"]) && isset($_POST["Erreur"]) && isset($_POST["Type"]) && isset($_POST["DebutCarte"]) && isset($_POST["DateFin"])
		 && isset($_POST["TypePaie"]) && isset($_POST["Heure"]) && isset($_POST["Date"]))
	{
		$reference=$_POST["Ref"];
		$Auto=$_POST["Auto"];
		$erreur=$_POST["Erreur"];
		$Type=$_POST["Type"];
		$DateFin=$_POST["DateFin"];
		$DebutCarte=$_POST["DebutCarte"];
		$TypePaie=$_POST["TypePaie"];
		$Heure=$_POST["Heure"];
		$Date=$_POST["Date"];
		$date_strtotime=$Date[4].$Date[5].$Date[6].$Date[7].$Date[2].$Date[3].$Date[0].$Date[1]." ".$Heure;
		$timeNow=strtotime($date_strtotime);
		if ($timeNow===false)
		{
			$timeNow=1;
		}
		$database=new Database();
		if ($database->count("logs_achats", array("reference" => $reference, "statut" => array("!=", "ACCEPTED"))))
		{
			$new_statut="REFUSE";
			if ($erreur=='00000')
			{
				$msg="Opération réussie.";
				$new_statut="ACCEPTED";
				$database->select("logs_achats", array("reference" => $reference), array("idUser", "categorie", "id_categorie", "montant", "langue"));
				$data=$database->fetch();
				$nom="";
				if ($data["categorie"]=="ACTIVITE")
				{
					$act=new Activite($data["id_categorie"]);
					$nbr_places=$data["montant"]/($act->getPrix()*100);
					$reservation = new Reservation($data["id_categorie"], $data["categorie"], $data["idUser"]);
					$reservation->setValide(1);
					$reservation->saveToDb();
					$nomAct=unserialize($act->getNom());
					$nom=current($nomAct);
					if (!isset($nomAct[$data["langue"]]))
					{
						if ($data["langue"]!=DEFAULT_LANGUE)
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
						$nom=$nomAct[$data["langue"]];
					}
				}	
				$database->create("notifications", array("idUser" => $data["idUser"], "titre" => "Votre paiement a été validé.", "message" => "Votre réservation pour ".$nom." est validé.", "lien" => "/demo/".$data["langue"]."/paiement/done?Ref=".$reference, "date" => time()));
			}
			else if ($erreur=='00001')
				$msg="La connexion au centre d’autorisation a échoué ou une erreur interne est survenue. Veuillez réessayer plus tard.";
			else if ($erreur+0>=100 && $erreur+0<200)
			{
				$msg="Paiement refusé par le centre d’autorisation.<br/>";
				if ($TypePaie=="CARTE" && ($Type=="CB" || $Type=="VISA" || $Type=="EUROCARD_MASTERCARD" || $Type=="AMEX" || $Type=="DINERS"))
				{
					$msg.=" Raison : ";
					switch ($erreur+0-100)
					{
						case 12:
						$msg.="Transaction invalide";
						break; case 13:
						$msg.="Montant invalide";
						break; case 14:
						$msg.="Numéro de porteur invalide";
						break; case 15:
						$msg.="Emetteur de carte inconnu";
						break; case 17:
						$msg.="Annulation client";
						break; case 19:
						$msg.="Répéter la transaction ultérieurement";
						break; case 20:
						$msg.="Réponse erronée (erreur dans le domaine serveur)";
						break; case 24:
						$msg.="Mise à jour de fichier non supportée";
						break; case 25:
						$msg.="Impossible de localiser l’enregistrement dans le fichier";
						break; case 26:
						$msg.="Enregistrement dupliqué, ancien enregistrement remplacé";
						break; case 27:
						$msg.="Erreur en « edit » sur champ de mise à jour fichier";
						break; case 28:
						$msg.="Accès interdit au fichier";
						break; case 29:
						$msg.="Mise à jour de fichier impossible";
						break; case 30:
						$msg.="Erreur de format";
						break; case 33:
						$msg.="Carte expirée";
						break; case 38:
						$msg.="Nombre d’essais code confidentiel dépassé";
						break; case 41:
						$msg.="Carte perdue";
						break; case 43:
						$msg.="Carte volée";
						break; case 51:
						$msg.="Provision insuffisante ou crédit dépassé";
						break; case 54:
						$msg.="Date de validité de la carte dépassée";
						break; case 55:
						$msg.="Code confidentiel erroné";
						break; case 56:
						$msg.="Carte absente du fichier";
						break; case 57:
						$msg.="Transaction non permise à ce porteur";
						break; case 58:
						$msg.="Transaction interdite au terminal";
						break; case 59:
						$msg.="Suspicion de fraude";
						break; case 60:
						$msg.="L’accepteur de carte doit contacter l’acquéreur";
						break; case 61:
						$msg.="Dépasse la limite du montant de retrait";
						break; case 63:
						$msg.="Règles de sécurité non respectées";
						break; case 68:
						$msg.="Réponse non parvenue ou reçue trop tard";
						break; case 75:
						$msg.="Nombre d’essais code confidentiel dépassé";
						break; case 76:
						$msg.="Porteur déjà en opposition, ancien enregistrement conservé";
						break; case 89:
						$msg.="Echec de l’authentification";
						break; case 90:
						$msg.="Arrêt momentané du système";
						break; case 91:
						$msg.="Emetteur de cartes inaccessible";
						break; case 94:
						$msg.="Demande dupliquée";
						break; case 96:
						$msg.="Mauvais fonctionnement du système";
						break; case 97:
						$msg.="Echéance de la temporisation de surveillance globale";
						break;
					}
				}
			}
			else if ($erreur=='00003')
				$msg="Erreur de la plateforme.";
			else if ($erreur=='00004')
				$msg="Numéro de porteur ou cryptogramme visuel invalide.";
			else if ($erreur=='00006')
				$msg="Une erreur interne a eu lieu. Merci d'avertir au plus vite l'administration du camping.";
			else if ($erreur=='00008')
				$msg="Date de fin de validité incorrecte.";
			else if ($erreur=='00009')
				$msg="Erreur de création d’un abonnement.";
			else if ($erreur=='00010')
				$msg="Devise inconnue.";
			else if ($erreur=='00011')
				$msg="Montant incorrect.";
			else if ($erreur=='00015')
				$msg="Paiement déjà effectué.";
			else if ($erreur=='00016')
				$msg="Abonné déjà existant (inscription nouvel abonné).";
			else if ($erreur=='00021')
				$msg="Carte non autorisée.";
			else if ($erreur=='00029')
				$msg="Carte non conforme.";
			else if ($erreur=='00030')
				$msg="Délai dépassé.";
			else if ($erreur=='00031')
				$msg="Une erreur est survenue.";
			else if ($erreur=='00032')
				$msg="Une erreur est survenue.";
			else if ($erreur=='00033')
				$msg="Les achats en provenance de votre pays ne sont pas autorisés auprès de notre banque.";
			else if ($erreur=='00040')
				$msg="Opération avortée (Sécurité).";
			else if ($erreur=='99999')
			{
				$msg="Opération en attente de validation par l’émetteur du moyen de paiement.";
				$new_statut="EN_ATTENTE";
			}
			if ($new_statut=="REFUSE")
			{
				$database->select("logs_achats", array("reference" => $reference), array("idUser", "categorie", "id_categorie", "langue"));
				$data=$database->fetch();
				$nom="";
				if ($data["categorie"]=="ACTIVITE")
				{
					$act=new Activite($data["id_categorie"]);
					$reservation = new Reservation($data["id_categorie"], $data["categorie"], $data["idUser"]);
					$reservation->setDeleted(true);
					$reservation->saveToDb();
					$nom=current($nomAct);
					if (!isset($nomAct[$data["langue"]]))
					{
						if ($data["langue"]!=DEFAULT_LANGUE)
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
						$nom=$nomAct[$data["langue"]];
					}
				}	
				$database->create("notifications", array("idUser" => $data["idUser"], "titre" => "Votre paiement a été refusé.", "message" => "Votre réservation pour ".$nom." est annulée.", "lien" => "/demo/".$data["langue"]."/paiement/refuse?Ref=".$reference, "date" => time()));
			}
			$database->update("logs_achats", array("reference" => $reference, "statut" => array("!=", "ACCEPTED")), array("statut" => $new_statut, "erreur" => $msg, "type_paiement" => $TypePaie, "type_carte" => $Type, "auto" => $Auto, "debutCarte" => $DebutCarte, "dateCarte" => $DateFin, "timestamp_effectue" => $timeNow));
		}
	}
?>
