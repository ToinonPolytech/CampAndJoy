<?php
	if (!isset($includeDone))
	{
		$includeDone=false;
	}
	if (!function_exists('i'))
	{
		define("DEFAULT_LANGUE", "fr");
		define("DEFAULT_LANGUE_ETRANGER", "en");
		ini_set('display_errors','on');
		error_reporting(E_ALL);
		if (!isset($_SESSION))
			session_start();
		
		function check_includes_name($dir, $name) // Optimise vitesse recherche fichier (c.f i($name_file))
		{
			if ($name==".git" || $name == "css" || $name == "fonts" || $name == "font-awesome" || $name == "js" || $name == "sql" || $name == "bootstrap")
			{
				return 0;
			}
			else if ($name == '.' || $name == '..')
			{
				return 0;
			}
			else if (!is_dir($dir))
			{	
				return 0;
			}
			return 1;
		}

		function i($name_file, $path = NULL) // Donne le chemin du fichier $name_file
		{		
			if ($path==NULL)
				$path=$_SERVER['DOCUMENT_ROOT']."/demo";

			if (file_exists($path."/".$name_file))
			{
				return $path."/".$name_file;
			}
			else
			{
				$first_dossier = opendir($path);
				while (false !== ($path_add = readdir($first_dossier)))
				{
					if(check_includes_name($path."/".$path_add, $path_add))
					{
						$t=i($name_file, $path."/".$path_add);
						if (!is_numeric($t))
							return $t;
					}
				}
				closedir($first_dossier);
			}
			return 0;
		}
		require_once(i("langues.php"));
		require_once(i("general.php"));
		require_once(i("config.php"));
		require_once(i("database.class.php"));
		require_once(i("loadInformation.php"));
		$includeDone=true;
	}
	if (!isAjax() && !isset($is_index)){ header("Location:index.php"); }
	if (isset($require))
	{
		$array_require=explode(",", $require);
		foreach ($array_require as $file)
		{
			if (!empty($file) && (!isset($included) || !in_array($file, $included)))
			{
				$path=i($file);
				if (!is_numeric($path))
				{
					if (!isset($included))
					{
						$included=array();
					}
					$included[]=$file;
					require_once($path);
				}
				else
					echo "DEBUG : ".$path." --- ".$file." introuvable.<br/>";
			}
		}
	}
?>
