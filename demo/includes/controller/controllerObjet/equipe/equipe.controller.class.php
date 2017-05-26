<?php
class Controller_Equipe
{
	private $_equipe;
	private $_errors;
	public function __construct ($equipe){
		$this->_equipe=$equipe;
		$this->_errors="";
	}
	public function isGood(){
		return ($this->nomIsGood() && $this->scoreIsGood());
	}
	public function nomIsGood(){
		if(!empty($this->_equipe->getNom() || preg_match("#^[a-zA-Z0-9]{3,40}$#",$this->_equipe->getNom()) ))
		{	$db = new Database();
			if($db->count('equipe',array('nom' => $this->_equipe->getNom()))==0)
			{
				return true;
			}
			else
			{
				$this->_errors.= "</br>ERREUR : une équipe avec ce nom existe déjà";
			}
		}
		else 
		{
			$this->_errors.= "</br>ERREUR : le nom n'a pas été rempli ou n'a pas la bonne forme (entre 3 et 40 caractères) ";
		}
		return false; 
	}
	
	public function scoreIsGood(){
		if(!empty($this->_equipe->getScore()) ||  preg_match("#^[0-9]{1,255}$#",$this->_equipe->getScore()))
		{
				return true;
		}
		else
		{
			$this->_errors.= "</br>ERREUR : le score de l'équipe est vide ou n'est pas un nombre correct ";
		}
		return false;
	}
	public function getError(){
		return $this->_errors;	
		
	}
}
?>