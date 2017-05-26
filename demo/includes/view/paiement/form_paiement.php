<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,activities.class.php,user.class.php,user.controller.class.php,reservation.class.php,reservation.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	if (!$cuser->can(CAN_PAY))
	{
		exit();
	}
	if (isset($_POST["choix_type"]) && isset($_POST["choix_type_spec"]) && isset($_POST["type"]) && ($_POST["type"]=="ACTIVITE" && isset($_POST["id"]) && isset($_POST["place"])))
	{
		if ($_POST["type"]=="ACTIVITE")
		{
			$act=new Activite($_POST["id"]);
			$pbx_total = $act->getPrix()*100*$_POST["place"];
			$id_categorie=$_POST["id"];
			$categorie="ACTIVITE";
			$reservation = new Reservation($id_categorie, $categorie, $_SESSION["id"], $_POST["place"], $act->getDate(), 0);
			$reservationController = new Controller_Reservation($reservation);
			if (!$reservationController->isGood())
			{
				?>
				<a href="#" class="primary_btn w-button" onclick="animationBack(<?php echo $_POST["id"]; ?>);"><< Retour</a>
				<div class="message_block error" id='reponse_controller_msg'>
					<p><?php echo $reservationController->getError(); ?></p>
				</div>
				<?php
				exit();	
			}
		}
		else
		{
			?>
			<div class="message_block error" id='reponse_controller_msg'>
				<p>Désolé, mais il semble qu'une erreur se soit produite.</p>
			</div>
			<?php
			exit();	
		}
		$choix_type=$_POST["choix_type"];
		$choix_type_spec=$_POST["choix_type_spec"];
		// --------------- VARIABLES A MODIFIER ---------------

		// Ennonciation de variables
		$pbx_site = '1999888';									//variable de test 1999888
		$pbx_rang = '32';									//variable de test 32
		$pbx_identifiant = '3';				//variable de test 3
		$pbx_porteur = $user->getUserInfos()->getEmail();							//variable de test test@test.fr
		// Suppression des points ou virgules dans le montant						
		$pbx_total = str_replace(",", "", $pbx_total);
		$pbx_total = str_replace(".", "", $pbx_total);
		$pbx_cmd = uniqid().uniqid();								//variable de test cmd_test1
		// Paramétrage des urls de redirection après paiement
		$pbx_effectue = 'https://www.campandjoy.fr/demo/'.LANG_USER.'/paiement/done';
		$pbx_annule = 'https://www.campandjoy.fr/demo/'.LANG_USER.'/paiement/cancel';
		$pbx_refuse = 'https://www.campandjoy.fr/demo/'.LANG_USER.'/paiement/refuse';
		// Paramétrage de l'url de retour back office site
		$pbx_repondre_a = 'https://www.campandjoy.fr/demo/includes/view/paiement/callback.php';
		// Paramétrage du retour back office site
		$pbx_retour = 'Ref:R;Auto:A;Erreur:E;Type:C;DateFin:D;DebutCarte:N;TypePaie:P;Heure:Q;Date:W';
		$type_retour='POST';
		// Connection à la base de données
		// mysql_connect...
		// On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l’on renseigne dans la variable $keyTest;
		//$keyTest = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';
		$keyTest = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';



		// --------------- TESTS DE DISPONIBILITE DES SERVEURS ---------------

		$serveurs = array('tpeweb.paybox.com', //serveur primaire
		'tpeweb1.paybox.com'); //serveur secondaire
		$serveurOK = "";
		//phpinfo(); <== voir paybox
		foreach($serveurs as $serveur){
		$doc = new DOMDocument();
		$doc->loadHTMLFile('https://'.$serveur.'/load.html');
		$server_status = "";
		$element = $doc->getElementById('server_status');
		if($element){
		$server_status = $element->textContent;}
		if($server_status == "OK"){
		// Le serveur est prêt et les services opérationnels
		$serveurOK = $serveur;
		break;}
		// else : La machine est disponible mais les services ne le sont pas.
		}
		//curl_close($ch); <== voir paybox
		if(!$serveurOK){
		die("Erreur : Aucun serveur n'a été trouvé");}
		// Activation de l'univers de préproduction
		//$serveurOK = 'preprod-tpeweb.paybox.com';

		//Création de l'url cgi paybox
		//$serveurOK = 'https://'.$serveurOK.'/cgi/MYchoix_pagepaiement.cgi';
		$serveurOK = 'https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi';
		// echo $serveurOK;



		// --------------- TRAITEMENT DES VARIABLES ---------------

		// On récupère la date au format ISO-8601
		$dateTime = date("c");

		// On crée la chaîne à hacher sans URLencodage
		$msg = "PBX_SITE=".$pbx_site.
		"&PBX_RANG=".$pbx_rang.
		"&PBX_IDENTIFIANT=".$pbx_identifiant.
		"&PBX_TOTAL=".$pbx_total.
		"&PBX_DEVISE=978".
		"&PBX_CMD=".$pbx_cmd.
		"&PBX_PORTEUR=".$pbx_porteur.
		"&PBX_REPONDRE_A=".$pbx_repondre_a.
		"&PBX_RETOUR=".$pbx_retour.
		"&PBX_EFFECTUE=".$pbx_effectue.
		"&PBX_ANNULE=".$pbx_annule.
		"&PBX_REFUSE=".$pbx_refuse.
		"&PBX_HASH=SHA512".
		"&PBX_TIME=".$dateTime.
		"&PBX_LANGUE=".LANGUE_BANQUE.
		"&PBX_TYPEPAIEMENT=".$choix_type.
		"&PBX_TYPECARTE=".$choix_type_spec.
		"&PBX_RUF1=".$type_retour;
		// echo $msg;

		// Si la clé est en ASCII, On la transforme en binaire
		$binKey = pack("H*", $keyTest);

		// On calcule l’empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et //
		// la clé binaire
		// On envoi via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
		// Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne //
		// suivante
		// print_r(hash_algos());
		$hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));

		// La chaîne sera envoyée en majuscule, d'où l'utilisation de strtoupper()
		// On crée le formulaire à envoyer
		// ATTENTION : l'ordre des champs est extrêmement important, il doit
		// correspondre exactement à l'ordre des champs dans la chaîne hachée
		
		$database->create("logs_achats", array("idUser" => $_SESSION["id"], "categorie" => $categorie, "id_categorie" => $id_categorie, "reference" => $pbx_cmd, "timestamp" => time(), "statut" => "EN_ATTENTE", "montant" => $pbx_total, "langue" => LANG_USER));
		$reservation->saveToDb();
	?>
	<p>
		Vous allez être redirigé vers un service de paiement sécurisé.
	</p>
	<form method="POST" id="paiement" action="<?php echo $serveurOK; ?>">
		<input type="hidden" name="PBX_SITE" value="<?php echo $pbx_site; ?>">
		<input type="hidden" name="PBX_RANG" value="<?php echo $pbx_rang; ?>">
		<input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo $pbx_identifiant; ?>">
		<input type="hidden" name="PBX_TOTAL" value="<?php echo $pbx_total; ?>">
		<input type="hidden" name="PBX_DEVISE" value="978">
		<input type="hidden" name="PBX_CMD" value="<?php echo $pbx_cmd; ?>">
		<input type="hidden" name="PBX_PORTEUR" value="<?php echo $pbx_porteur; ?>">
		<input type="hidden" name="PBX_REPONDRE_A" value="<?php echo $pbx_repondre_a; ?>">
		<input type="hidden" name="PBX_RETOUR" value="<?php echo $pbx_retour; ?>">
		<input type="hidden" name="PBX_EFFECTUE" value="<?php echo $pbx_effectue; ?>">
		<input type="hidden" name="PBX_ANNULE" value="<?php echo $pbx_annule; ?>">
		<input type="hidden" name="PBX_REFUSE" value="<?php echo $pbx_refuse; ?>">
		<input type="hidden" name="PBX_HASH" value="SHA512">
		<input type="hidden" name="PBX_TIME" value="<?php echo $dateTime; ?>">
		<input type="hidden" name="PBX_LANGUE" value="<?php echo LANGUE_BANQUE; ?>">
		<input type="hidden" name="PBX_TYPEPAIEMENT" value="<?php echo $choix_type; ?>">
		<input type="hidden" name="PBX_TYPECARTE" value="<?php echo $choix_type_spec; ?>">
		<input type="hidden" name="PBX_RUF1" value="<?php echo $type_retour; ?>">
		<input type="hidden" name="PBX_HMAC" value="<?php echo $hmac; ?>">
	</form>
	<script type="text/javascript">
		$("form[id='paiement']").submit();
	</script>
	<?php
	}
	else if (isset($_POST["type"]) && ($_POST["type"]=="ACTIVITE" && isset($_POST["id"]) && isset($_POST["place"])))
	{
	?>
		<div class="customcaj_separator"></div>
		<a href="#" class="primary_btn w-button" onclick="animationBack(<?php echo $_POST["id"]; ?>);"><< Retour</a>
		<div class="message_block info">
			<p>
				Choisissez votre moyen de paiement. Vous serez redirigé vers un service de paiement sécurisé.<br/>
				/!\ Vous ne pourrez ni modifier, ni annuler cette réservation /!\<br/>
			</p>
		</div>
		<a href="#" t="CARTE" tc="CB"><img src="images/CB.gif" width="50px" height="30px" /></a> <a href="#" t="CARTE" tc="EUROCARD_MASTERCARD"><img src="images/mastercard.png" width="50px" height="30px" /></a>
		<a href="#" t="WALLET" tc="PAYLIB"><img src="images/paylib.jpg" width="50px" height="30px" /></a> <a href="#" t="CARTE" tc="VISA"><img src="images/visa.png" width="50px" height="30px" /></a>
		<script type="text/javascript">
			$("#paiement_<?php echo $_POST["id"]; ?>").find("img").each(function(){
				$(this).parent("a").on("click", function(){
					loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("form_paiement.php")); ?>', {choix_type:$(this).attr("t"), choix_type_spec:$(this).attr("tc"), type:"<?php echo htmlentities($_POST["type"]); ?>", id:<?php echo htmlentities($_POST["id"]); ?>, place:<?php echo htmlentities($_POST["place"]); ?>}, '#paiement_<?php echo htmlentities($_POST["id"]); ?>', 'replace');
				});
			});
		</script>
	<?php
	}
?>