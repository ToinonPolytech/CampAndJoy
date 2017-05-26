<?php
	$new_lang=false;
	$lang_available=array("fr" => "Français", "en" => "Anglais");
	if (isset($_GET["lang"]) && !empty($_GET["lang"]) && (!isset($_SESSION["lang"]) || $_SESSION["lang"]!=str_replace("/", "", $_GET["lang"])) && isset($lang_available[str_replace("/", "", $_GET["lang"])]))
	{
		$_SESSION["lang"]=str_replace("/", "", $_GET["lang"]);
	}
	elseif (!isset($_SESSION["lang"]))
	{
		$_SESSION["lang"]=DEFAULT_LANGUE;
		$navigateur_langue=$_SERVER['HTTP_ACCEPT_LANGUAGE'][0].$_SERVER['HTTP_ACCEPT_LANGUAGE'][1];
		if (isset($lang_available[$navigateur_langue]))
		{
			$_SESSION["lang"]=$navigateur_langue;
		}
		else
		{
			$_SESSION["lang"]=DEFAULT_LANGUE_ETRANGER;
		}
	}
	elseif (!isset($lang_available[$_SESSION["lang"]]))
	{
		$_SESSION["lang"]=DEFAULT_LANGUE;
	}
	if (!isset($paiement))
	{
		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest')
		{
			if (!isset($_GET["lang"]) || empty(str_replace("/", "", $_GET["lang"]))) // On a pas de fr/en ect.. dans l'url
			{
				$temp=explode("/", $_SERVER["REQUEST_URI"]);
				if (count($temp)<3 || !isset($lang_available[$temp[2]]))
				{
					header("Location:".str_replace("demo", "demo/".$_SESSION["lang"], $_SERVER["REQUEST_URI"]));
					exit();
				}
			}
		}
	}
	define("LANG_USER", $_SESSION["lang"]);
	require_once(i(LANG_USER.".php"));
	$lang_available=array("fr" => FRANCAIS, "en" => ANGLAIS);
	function __($constant, ...$array_var)
	{
		$texte_final=$constant;
		$array_temp=$array_var;
		$key=0;
		while (preg_match("/%([a-zA-Z0-9_]+)%/", $texte_final) && $key<count($array_temp))
		{
			$texte_final=preg_replace('/%([a-zA-Z0-9_]+)%/', htmlentities($array_temp[$key]), $texte_final, 1);
			$key++;
		}
		echo $texte_final;
	}
?>