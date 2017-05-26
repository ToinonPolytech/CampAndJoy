<?php
	######################
	#  BASE DE DONNÉES   #   
	######################
	$prefix_hash="eDodm7";
	$config_db=array();
	$config_db['host']="campandjghroot.mysql.db";
	$config_db['user']="campandjghroot";
	$config_db['pass']="Ek9G2bN50";
	$config_db['name']="campandjghroot";
	######################
	#CONFIG SELON CAMPING#   
	######################
	$staffCanEditOther=true; // Un membre du staff, qui a le droit d'éditer, peut éditer même ce qu'il n'a pas créé (c.f activités)
	$heuresAccueil=array();
	for ($h=8;$h<=12;$h++)
	{
		for ($m=0;$m<60;$m+=15)
			$heuresAccueil[]=$h.":".$m;
	}
	for ($h=14;$h<=18;$h++)
	{
		for ($m=0;$m<60;$m+=15)
			$heuresAccueil[]=$h.":".$m;
	}
	######################
	#     PERMISSIONS    #   
	######################
	$puissance=0;
	/** CAN_ COMMUN **/
	define("CAN_LOG", $puissance); $puissance++;
	define("CAN_CREATE_SUBACCOUNT", $puissance); $puissance++;
	define("CAN_CREATE_ACTIVITIES", $puissance); $puissance++;
	define("CAN_RESERVE_ACTIVITIES", $puissance); $puissance++;
	define("CAN_RESERVE_RESTAURANT", $puissance); $puissance++;
	define("CAN_RESERVE_LIEU_COMMUN", $puissance); $puissance++;
	define("CAN_PAY", $puissance); $puissance++;
	$puissance_commun=$puissance;
	/** CAN_ USER **/
	define("SEND_MESSAGE", $puissance); $puissance++; // Ce droit permet au client d'envoyer des messages aux staffs (mais il doit attendre la réponse) + clients
	define("CAN_CREATE_TEAM", $puissance); $puissance++;
	$puissance_max=$puissance;
	/** CAN_ STAFF **/
	$puissance=$puissance_commun;
	define("CAN_CREATE_ACCOUNT_STAFF", $puissance); $puissance++;
	define("CAN_ADD_RESTAURANT_STAFF", $puissance); $puissance++;
	define("CAN_ADD_LIEU_COMMUN_STAFF", $puissance); $puissance++;
	define("CAN_MANAGE_PROBLEME_TECHNIQUE", $puissance); $puissance++;
	define("SEND_MESSAGE_STAFF", $puissance); $puissance++; // Celui ci => permet d'envoyer autant de messages sans restrictions
	if ($puissance_max>$puissance)
		$puissance=$puissance_max;
	
	// Pour afficher dans les views de manière dynamique
	$CAN_infos=array(CAN_LOG => "Autoriser la connexion",
					CAN_CREATE_SUBACCOUNT => "Autoriser à créer des comptes affiliés",
					CAN_CREATE_ACTIVITIES => "Autoriser à créer des activités",
					CAN_RESERVE_ACTIVITIES => "Autoriser à réserver des activités",
					CAN_RESERVE_RESTAURANT => "Autoriser à réserver des restaurants",
					CAN_PAY => "Autoriser à payer",
					CAN_CREATE_TEAM => "Autoriser à créer des équipes",
					CAN_RESERVE_LIEU_COMMUN => "Autoriser à réserver des installations");
	$CAN_USER_infos=array(SEND_MESSAGE => "Autoriser à envoyer des messages");
	$CAN_STAFF_infos=array(CAN_CREATE_ACCOUNT_STAFF => "Autoriser à créer des comptes",
							CAN_ADD_RESTAURANT_STAFF => "Autoriser à créer des restaurants",
							SEND_MESSAGE_STAFF => "Autoriser à envoyer des messages",
							CAN_MANAGE_PROBLEME_TECHNIQUE => "Autoriser à gérer les problèmes techniques",
							CAN_ADD_LIEU_COMMUN_STAFF => "Autoriser à créer les lieux communs");
							
	ksort($CAN_infos); // Il suffit de modifier l'ordre des defines pour que cela soit afficher de manière logique dans les views
	ksort($CAN_USER_infos);
	ksort($CAN_STAFF_infos);
	######################
	#    LES ACTIVITÉS   #   
	######################
	$listeTypes = array('SPORTIVE','INTELLECTUELLE','DÉTENTE','FESTIVE');
	######################
	#  TYPE DES COMPTES  #   
	######################
	$listesTypes = array('CLIENT' => array(CAN_LOG, CAN_CREATE_SUBACCOUNT, CAN_CREATE_ACTIVITIES, CAN_RESERVE_ACTIVITIES, CAN_RESERVE_RESTAURANT, CAN_PAY, SEND_MESSAGE),
						'ANIMATEUR' => array(CAN_LOG, CAN_CREATE_SUBACCOUNT, CAN_CREATE_ACTIVITIES, CAN_RESERVE_ACTIVITIES, CAN_RESERVE_RESTAURANT, CAN_PAY,
						SEND_MESSAGE_STAFF),
						'TECHNICIEN' => array(CAN_LOG, CAN_CREATE_SUBACCOUNT, CAN_CREATE_ACTIVITIES, CAN_RESERVE_ACTIVITIES, CAN_RESERVE_RESTAURANT, CAN_PAY,
						SEND_MESSAGE_STAFF, CAN_MANAGE_PROBLEME_TECHNIQUE),
						'PATRON' => array(CAN_LOG, CAN_CREATE_SUBACCOUNT, CAN_CREATE_ACTIVITIES, CAN_RESERVE_ACTIVITIES, CAN_RESERVE_RESTAURANT, CAN_PAY,
						SEND_MESSAGE_STAFF, CAN_MANAGE_PROBLEME_TECHNIQUE, CAN_CREATE_ACCOUNT_STAFF, CAN_ADD_RESTAURANT_STAFF, CAN_ADD_LIEU_COMMUN_STAFF),
						'PARTENAIRE' => array(CAN_LOG, CAN_CREATE_SUBACCOUNT, CAN_CREATE_ACTIVITIES, CAN_RESERVE_ACTIVITIES, CAN_RESERVE_RESTAURANT, CAN_PAY));