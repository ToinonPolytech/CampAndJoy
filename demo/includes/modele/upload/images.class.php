<?php
if (!isset($require))
{
	$require="";
}
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Image_upload{
	private $_error;
	private $_errors;
	private $_count;
	private $_url;
	public function __construct($data, $maxsize, $extensions_valides, $dir)
	{
		$this->_count=0;
		$this->_error=0;
		$this->_errors="";
		$this->_url=array();
		foreach ($data as $image)
		{
			if (!empty($image['name']) && $this->_error==0)
			{
				if ($image['error'] > 0)
				{
					$this->_error++;
					$this->_errors.="Une erreur est survenue lors du transfert des images : ";
					switch ($image['error'])
					{
						default: $this->_errors.="?"; break;
						case UPLOAD_ERR_NO_FILE : $this->_errors.="fichier manquant."; break;
						case UPLOAD_ERR_INI_SIZE : $this->_errors.="fichier dépassant la taille maximale autorisée par PHP."; break;
						case UPLOAD_ERR_FORM_SIZE : $this->_errors.="fichier dépassant la taille maximale autorisée par le formulaire."; break;
						case UPLOAD_ERR_PARTIAL : $this->_errors.="fichier transféré partiellement."; break;
					}
					$this->_errors.="<br/>";
				}
				else
				{
					if ($image['size'] > $maxsize)
					{
						$this->_error++;
						$this->_errors.="Au moins une des images est trop volumineuse.<br/>";
					}
					else
					{
						$extension_upload = strtolower(substr(strrchr($image['name'], '.'),1));
						if (!in_array($extension_upload,$extensions_valides))
						{
							$this->_error++;
							$this->_errors.="Merci de vérifier les formats de vos images.<br/>";
						}
						else
						{
							$location=$_SERVER['DOCUMENT_ROOT']."/demo/images/uploaded/".$_SESSION["id"]."/".$dir."/";
							if (!file_exists($location))
							{
								if (!mkdir($location, 0777, true))
								{
									$this->_error++;
									$this->_errors.="Une erreur est survenue.<br/>";
								}
							}
							if ($this->_error==0)
							{
								$nom = md5(uniqid(rand(), true)).".".$extension_upload;
								$resultat = move_uploaded_file($image['tmp_name'],$location.$nom);
								if (!$resultat)
								{
									$this->_error++;
									$this->_errors.="Une erreur est survenue.<br/>";
								}
								else
								{
									$this->_url[]=$location.$nom;
									$this->_count++;
								}
							}
						}
					}
				}
			}
		}
		if ($this->_error>0)
		{
			$this->cancel();
		}
	}
	public function cancel()
	{
		foreach ($this->_url as $val)
		{
			unlink($val); // Erreur, on supprime tout
		}
	}
	public function getError()
	{
		if ($this->_error>0)
			return true;
		
		return false;
	}
	public function getErrors() { return $this->_errors; }
	public function getUrl()
	{
		$t="";
		foreach ($this->_url as $val)
		{
			$t.=str_replace($_SERVER['DOCUMENT_ROOT'], '', $val).",";
		}
		return $t;
	}
	public function getCount() { return $this->_count; }
}
?>