<?php 
class Controller_User
{
	protected $_user;
	protected $_withInfoId;
	private $_errors;
	public function __construct ($user, $withInfoId=true){
		$this->_user=$user;
		$this->_withInfoId=$withInfoId;
		$this->_errors="";
	}
	public function canEdit($o){
		if ($this->_user->getUserInfos()->getId()==$o->getUserInfos()->getId())
		{
			if ($this->can(CAN_CREATE_SUBACCOUNT))
				return true;
		}
		else if (isStaff())
		{
			if ($this->can(CAN_CREATE_ACCOUNT_STAFF))
				return true;
		}
		return false;
	}
	public function generateKey(){
		$database = new Database();
		do{
			$clef=generateRandomCharacters(6);
		}while($database->count('users', array("clef" => $clef)));
		return $clef;
	}
	public function isGood(){
		return ($this->nomIsGood() && $this->prenomIsGood() && $this->codeIsGood() && $this->droitsIsGood() && (!$this->_withInfoId || $this->infoIdIsGood()));
	}
	public function nomIsGood(){
		if(!empty($this->_user->getNom()))
		{
			if(preg_match("#[a-zA-Z0-9]{3,40}#",$this->_user->getNom()))
			{
					return true;
			}
			else
			{
				$this->_errors.="ERREUR : le nom de l'utilisateur n'est pas de la bonne forme (doit être compris entre 3 et 40 caractères)";
				return false;
			}
		}
		else
		{
			$this->_errors.="ERREUR : le nom de l'utilisateur est vide ";
			return false; 
		}
		
	}
	
	public function prenomIsGood(){
		if(!empty($this->_user->getPrenom()))
		{
			if(preg_match("#^[a-zA-Z0-9]{3,40}$#",$this->_user->getPrenom()))
			{
					return true;
			}
			else
			{
				$this->_errors.="ERREUR : le prénom de l'utilisateur n'est pas de la bonne forme (doit être compris entre 3 et 40 caractères)";
				return false;
			}
		}
		else
		{
			$this->_errors.="ERREUR : le prénom de l'utilisateur est vide ";
			return false; 
		}
		
	}
	
	public function codeIsGood(){
		/*
		if(preg_match("#^[0-9]{4}$#", $this->_user->getCode()) || $this->_user->getCode()==NULL)
		{
				return true;
		}
		else
		{
			$this->_errors.="ERREUR : le code entré n'est pas de la bonne forme (il doit contenir 4 chiffres )";
			return false;
		}
		*/
		return true;
	}
		
	
	public function droitsIsGood(){
		if(!empty($this->_user->getDroits()))
		{
			if(preg_match("#^[0-9]{1,255}$#", $this->_user->getDroits()))
			{
					return true;
			}
			else
			{
				$this->_errors.="ERREUR : les droits n'ont pas le bon format ";
				return false;
			}
		}
		else
		{
			$this->_errors.="ERREUR : aucun droit entré ";
			return false; 
		}
		
	}
		
	
	public function infoIdIsGood(){
		$database = new Database();
		if(!empty($this->_user->getUserInfos()->getId()))
		{
			if($database->count('userinfos', array("id" => $this->_user->getUserInfos()->getId())))
			{
				return true;
			}
			else
			{
				$this->_errors.="ERREUR : les infos de l'utilisateur n'existent pas  ";
				return false;
			}
		}
		else
		{
			$this->_errors.="ERREUR : aucun id correspondant aux infos de l'utilisateur n'ont été entrées  ";
			return false; 
		}
	}
	
	
	/**
		Exemple : can(CAN_CREATE_SUBACCOUNT); grâce au fichier config.php cela donne la puissance adéquate et tout est géré automatiquement.
	**/
	public function can($which){
		global $puissance;
		$etat=false;
		$droits = $this->_user->getDroits();
		$array=array();
		for ($i=0;$i<$puissance;$i++)
		{
			$array[$i]=pow(2,$i);
		}
		for ($i=$puissance-1;$i>=0 && !$etat && $droits>0;$i--)
		{
			if ($droits>=$array[$i])
			{
				if ($i==$which)
					$etat=true;
				
				$droits-=$array[$i];
			}
		}
		return $etat;
	}
	public function getError(){ return $this->_errors; }
}
?>