<?php 
class Controller_UserInfo
{
	private $_user;
	private $_errors;
	public function __construct ($userInfo){
		$this->userInfo=$userInfo;
		$this->_errors="";
	}
	
	public function isGood(){
		return ($this->numPlaceIsGood() && $this->emailIsGood() && $this->clefIsGood());
	}
	
	public function numPlaceIsGood(){
		if(!empty($this->userInfo->getEmplacement())){
			return true; 
		}
		else
		{
			$this->_errors.="ERREUR : l'emplacement est vide";
		}
		return false; 	
	}
	
	public function emailIsGood(){
		if(!empty($this->userInfo->getEmail()))
		{
			if(preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->userInfo->getEmail()))
			{
				return true;
			}
			else 
			{
				$this->_errors.="ERREUR : le format du mail n'est pas correct";
			}
		}
		else
		{
			$this->_errors.="ERREUR : le mail n'est n'est pas passé en paramètre ";
		}
		return false;
	}
	
	public function clefIsGood(){
		if ($this->userInfo->getClef()!=NULL)
			return true;

		$this->_errors.="ERREUR : la clef ne peut être vide  ";
		return false;
	}
	public function getError(){ return $this->_errors; }
}
?> 