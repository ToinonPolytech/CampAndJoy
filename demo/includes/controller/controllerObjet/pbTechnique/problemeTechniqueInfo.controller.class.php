<?php 
class Controller_PbTechInfo{
	private $_PbTechInfo; 
	private $_errors; 
	public function __construct ($pbTechInfo){
		$this->_PbTechInfo=$pbTechInfo; 
		$this->_errors="";
	}
	public function isGood(){
		return($this->idIsGood() && $this->idPbTechIsGood() && $this->idUserIsGood() && $this->messageIsGood()); 
	}
	public function idIsGood(){
		$database = new Database();
		if($this->_PbTechInfo->getId()==NULL || $database->count('problemes_technique_info', array("id" => $this->_PbTechInfo->getId())))
		{
			return true;
		}
		else
		{
			$this->_errors.='<br/>ERREUR : id du message incorrect';
		}
		return false; 
	}
	public function idPbTechIsGood(){
		$database = new Database();
		if(is_numeric($this->_PbTechInfo->getIdPbTech()))
		{
			if($database->count('problemes_technique', array("id" => $this->_PbTechInfo->getIdPbTech())))
			{
				return true;
			}
			else
			{
				$this->_errors.="<br/>ERREUR : le problème concerné n'existe pas "; 
				
			}
		}
		else
		{
			$this->_errors.="<br/>ERREUR : l'id du problème n'est pas un nombre";
		}
		return false; 
	}
	public function idUserIsGood(){
		$database = new Database();
		if(is_numeric($this->_PbTechInfo->getIdPbTech()))
		{
			if( $database->count('users', array("id" => $this->_PbTechInfo->getIdUser())))
			{
				return true;
			}
			else
			{
				$this->_errors.="<br/>ERREUR : l'utilisateur concerné n'existe pas "; 
				
			}
		}
		else
		{
			$this->_errors.="<br/>ERREUR : l'id de l'utilisateur n'est pas un nombre";
		}
		return false; 
	}
		
	
	public function messageIsGood(){
		//à voir pour vérifications supplémentaires sur le message 
		if(!empty($this->_PbTechInfo->getMessage()))
		{
			return true;
		}
		else
		{
			$this->_errors.='<br/>ERREUR : le message est vide'; 
		}
	}
	public function getErrors(){
		return $this->_errors;
	}
}


?> 


