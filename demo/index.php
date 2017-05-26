<?php
	session_start();
	$is_index=true;
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!isAjax()){ require_once(i("haut.php")); }
	if (auth())
	{
		if (!isAjax()){ require_once(i("header.php")); }
		$page="home";
		if (isset($_GET["page"]))
			$page=$_GET["page"];

		switch ($page)
		{
			default:$urlRequest=i("board.php");break;
			case "board":$urlRequest=i("board.php");break;
			case "paiement":$urlRequest=i("paiement_info.php");break;
			case "planning":$urlRequest=i("activitesCamping.php");break;
			case "service":$urlRequest=i("services.php");break;
			case "restaurant":$urlRequest=i("restaurants.php");break;
			case "ajoutActivite":$urlRequest=i("ajoutActiviteForm.php");break;
			case "partenaire":
				if (isset($_GET["id"]))
					$urlRequest=i("partenaire.php");
				else
					$urlRequest=i("partenaires.php");
			break;
			//comptes 
			case "compte":$urlRequest=i("moncompte.php");break;
			case "compte_ajout":$urlRequest=i("ajout_souscompte.php");break;
			case "compte_modifier":$urlRequest=i("modifier_souscompte.php");break;
			case "profil":$urlRequest=i("profilView.php");break;
			//etat des lieux 
			case "edl":$urlRequest=i("etatDesLieux.php");break;
			//problemes techniques 
			case "probleme":$urlRequest=i("probleme_client.php");break;
			case "FAQ":$urlRequest=i("faqView.php");break;
			case "signalerProbleme":$urlRequest=i("ajoutPbTechForm.php");break;
			case "mesProblemes":$urlRequest=i("mesProblemesView.php");break;
			case "modifierPbTech":$urlRequest=i("modifProblemeTechniqueForm.php");break;
			case "supprimerPbTech":$urlRequest=i("supprimerProblemeTechnique.php");break;
			case "vuePbTech":$urlRequest=i("problemeTechniqueView.php");break;
			//lieux communs
			case "lieuxCommuns":$urlRequest=i("lieuxCommunsView.php");break;
			//équipes
			case "mesEquipes":$urlRequest=i("mesEquipesView.php");break;
			//le camping 
			case "leCamping":$urlRequest=i("leCamping.php");break;
			case "notreEquipe":$urlRequest=i("notreEquipe.php");break;
			//admin 
			case "administration":
				if (isStaff())
				{
					$type="default";
					if (isset($_GET["type"]))
						$type=$_GET["type"];
					
					switch ($type)
					{
						default:$urlRequest=i("accueil.php");break;
						case "ajoutActivite":$urlRequest=i("ajoutActivites_administration.php");break;
						case "modifierActivite":$urlRequest=i("modifierActivites_administration.php");break;
						case "activites":$urlRequest=i("activites_administration.php");break;
						case "rechercheActivite":$urlRequest=i("rechercheActivites_administration.php");break;
						case "reservation":$urlRequest=i("reservation_administration.php");break;
						case "lieuxCommuns":$urlRequest=i("lieux_communs_administration.php");break;
						case "ajoutLieuCommun":$urlRequest=i("ajoutLieuForm.php");break; 
						case "modifierLieuCommun":$urlRequest=i("modifierLieuForm.php");break;
						case "problemesTechniques":$urlRequest=i("problemesTechniquesView.php");break; 
						case "restaurantAjout":$urlRequest=i("ajoutRestaurant_administration.php");break; 
						case "service":$urlRequest=i("services_administration.php");break; 
						case "restaurantModifier":$urlRequest=i("modifierRestaurant_administration.php");break; 
						case "rechercheRestaurant":$urlRequest=i("rechercheRestaurant_administration.php");break; 
						case "compte":$urlRequest=i("comptes_administration.php");break;
						case "compteModifier":$urlRequest=i("comptesModifier_administration.php");break;
						case "compteVoir":$urlRequest=i("comptesVoir_administration.php");break;
						case "compteAdd":$urlRequest=i("comptesAjout_administration.php");break;
						case "pushNotif":$urlRequest=i("testNotifs.php");break;
						case "ajoutPartenaire":$urlRequest=i("ajoutPartenaire.php");break;
						case "modifierPartenaire":$urlRequest=i("modifPartenaire.php");break;
						//faq 
						case "gestion":$urlRequest=i("gestionFAQ.php");break;
						case "rechercheLieuCommun":$urlRequest=i("rechercheLieuCommun_administration.php");break;
					}
				}
				else
				{
					$type="default";
					if (isset($_GET["type"]))
						$type=$_GET["type"];
					
					switch ($type)
					{
						default:$urlRequest=i("accueil.php");break;
						case "ajoutActivite":$urlRequest=i("ajoutActivites_administration.php");break;
						case "modifierActivite":$urlRequest=i("modifierActivites_administration.php");break;
						case "reservation":$urlRequest=i("reservation_administration.php");break;
					}
				}
			break;
		}
		$getInfos="";
		foreach ($_GET as $k => $v)
		{
			if ($getInfos!="")
				$getInfos.="&";
			else
				$getInfos="?";
			
			$getInfos.=$k."=".$v;
		}
		$postInfos="";
		foreach ($_POST as $k => $v)
		{
			if ($postInfos!="")
				$postInfos.=",";
			
			$postInfos.=$k.":".$v;
		}
		$chaines=explode("?", $_SERVER["REQUEST_URI"]);
		if (count($chaines)>1)
		{
			$getChaines=explode("&", $chaines[1]);
			foreach ($getChaines as $v)
			{
				if ($getInfos!="")
					$getInfos.="&";
				else
					$getInfos="?";
				
				$getInfos.=$v;
			}
		}
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				loadToMainRedirect('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $urlRequest).str_replace("&amp;", "&", htmlspecialchars($getInfos)); ?>', {<?php echo htmlspecialchars($postInfos); ?>});
			});
		</script>
		<?php
	}
	else
	{
		$urlRequest=i("connexion.php");
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $urlRequest); ?>', {}, 'body', 'replace');
			});
		</script>
		<?php
	}
	if (!isAjax()){ require_once(i("bas.php")); }
?>