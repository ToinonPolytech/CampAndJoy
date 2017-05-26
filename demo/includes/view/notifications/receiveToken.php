<?php
	if (!isset($require))
	{
		$require="";
	}	
	$is_index=true;
	$paiement=true;
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (isset($_GET["token"]) && isset($_GET["clef"]))
	{
		$database=new Database();
		$token_exists=$database->getValue("users", array("clef" => $_GET["clef"]), "token");
		$exist_token=false;
		foreach (explode(";", $token_exists) as $token)
		{
			if ($token==$_GET["token"])
			{
				$exist_token=true;
			}
		}
		if (!$exist_token)
		{
			$database->update("users", array("clef" => $_GET["clef"]), array("token" => $_GET["token"].";".$token_exists));
		}	
	}
?>