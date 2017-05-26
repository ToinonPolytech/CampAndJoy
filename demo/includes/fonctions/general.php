<?php
	function generateRandomCharacters($length, $downcase=false)
	{
		$chaine = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ($downcase)
			$chaine = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		$key = str_shuffle($chaine);
		$key = substr($key, 0, $length);
		return $key;
	}
	function getDataByLang($data, $lang=LANG_USER, $lang_etranger=DEFAULT_LANGUE_ETRANGER)
	{
		$d=unserialize($data);
		$value=current($d);
		if (!isset($d[$lang]))
		{
			if ($lang!=DEFAULT_LANGUE)
			{
				if (isset($d[$lang_etranger]))
				{
					$value=$d[$lang_etranger];
				}
				else if (isset($d[DEFAULT_LANGUE]))
				{
					$value=$d[DEFAULT_LANGUE];
				}
			}
			else if (isset($d[DEFAULT_LANGUE]))
			{
				$value=$d[DEFAULT_LANGUE];
			}
		}
		else
		{
			$value=$d[$lang];
		}
		return $value;
	}
	function auth()
	{
		return (isset($_SESSION["id"]) && is_numeric($_SESSION["id"]) && $_SESSION["id"]>0);
	}
	function getDayFromNumber($w)
	{
		$a=array();
		$a[0]="Dimanche";
		$a[1]="Lundi";
		$a[2]="Mardi";
		$a[3]="Mercredi";
		$a[4]="Jeudi";
		$a[5]="Vendredi";
		$a[6]="Samedi";
		
		return $a[$w];
	}
	function getMonthFromNumber($w)
	{
		$a=array();
		$a[1]="Janvier";
		$a[2]="Février";
		$a[3]="Mars";
		$a[4]="Avril";
		$a[5]="Mai";
		$a[6]="Juin";
		$a[7]="Juillet";
		$a[8]="Août";
		$a[9]="Septembre";
		$a[10]="Octobre";
		$a[11]="Novembre";
		$a[12]="Décembre";
		return $a[$w];
	}
	function getStatutUser($w)
	{
		if ($w==0)
			return "offline";
		if ($w==1)
			return "away";
		if ($w+60*10<time())
			return "offline";	
		if ($w+60<time())
			return "busy";	
		return "online";
	}
	function isStaff()
	{
		return (auth() && $_SESSION["access_level"]!="CLIENT" && $_SESSION["access_level"]!="PARTENAIRE");
	}
	function isPartenaire()
	{
		return (auth() && $_SESSION["access_level"]=="PARTENAIRE");
	}
	function isClient()
	{
		return (auth() && $_SESSION["access_level"]=="CLIENT");
	}
	function isAjax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest');
	}
	function isRestaurateur($id_restau)
	{
		$db = new Database();
		$idsUsers=$db->getValue("restaurant", array("id" => $id_restau), "idsUsers");
		$idsUsersArray=explode(",", $idsUsers);
		foreach ($idsUsersArray as $id)
		{
			if ($id==$_SESSION["id"])
				return true;
		}
		return false;
	}
	function getNumberFromAccessLevel($w)
	{
		if ($w=="CLIENT")
			return 1;
		if ($w=="PARTENAIRE")
			return 2;
		if ($w=="TECHNICIEN" || $w=="ANIMATEUR")
			return 3;
		if ($w=="PATRON")
			return 4;
		
		return -1;
	}
?>