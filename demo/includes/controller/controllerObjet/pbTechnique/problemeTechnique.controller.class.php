<?php 
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Controller_PbTech {

	private $_PbTech; 
	private $_errors; 
	public function __construct ($pbTech){
		$this->_PbTech=$pbTech; 	
		$this->_errors="";
	}
	public function isGood(){
		return($this->idUserIsGood() && $this->timeIsGood()
		&& $this->descriptionIsGood() && $this->isBungalowIsGood() && $this->solvedIsGood()); 
		
	}

	public function idUserIsGood(){
		$database = new Database(); 
		
		if(!empty($this->_PbTech->getIdUser()))
		{	
			if(is_numeric($this->_PbTech->getIdUser()))
			{
				if($database->count('users', array("id" => $this->_PbTech->getIdUser())==1))
				{
					return true;
				}
				else
				{
					$this->_errors.= "</br>ERREUR : l'utilisateur créant le problème technique n'existe pas dans la base de données ";
					
				}
			}
			else 
			{
				$this->_errors.= "</br>ERREUR : l'id de l'utilisateur créant le problème technique n'est pas un entier ";
				
			}
		}
		else 
		{
			$this->_errors.= "</br>ERREUR : il n a pas d'utilisateur passé en paramètre dans le formulaire ";
			
		}	
		return false;
	}
	
	public function timeIsGood(){
		
		if(!empty($this->_PbTech->getTimeCreated()) )
		{
			if($this->_PbTech->getTimeCreated()<=time())
			{	
				return true;
			}
			else 
			{
				$this->_errors.= "</br>ERREUR : la date de création du problème ne peut pas avoir lieu après la date actuelle ";
				return false; 
			}
		}
		else
		{
			$this->_errors.= "</br>ERREUR : la date de création est vide  ";
			return false; 
			
		}
		
	}	
	public function descriptionIsGood(){
		if(!empty($this->_PbTech->getDescription()))
		{
			if((strlen($this->_PbTech->getDescription())>20) && (strlen($this->_PbTech->getDescription())<1000))
			{
				return true;
			}
			else
			{	
				$this->_errors.= "</br>ERREUR : la description doit être comprise entre 21 et 999 caractères ";
				
			}
		}
		else
		{	$this->_errors.= "</br>ERREUR : la description du problème technique est vide ";
			
		}
		return false;
	}
	public function isBungalowIsGood(){
		if(!empty($this->_PbTech->getIsBungalow()))
		{	
			if(is_bool($this->_PbTech->getIsBungalow()) || is_numeric($this->_PbTech->getIsBungalow()))
			{
				return true;
				
			}
			else
			{
				$this->_errors.= "</br>ERREUR : le critère définissant si le problème est dans un bungalow n'est pas du bon type  ";
				
			}
		}
		else
		{
			$this->_errors.= "</br>ERREUR : vous devez préciser si le problème se passe dans un bungalow ou non   ";
			
			
		}
		return false;
	}
	public function solvedIsGood(){
		if($this->_PbTech->getSolved()=="NON_RESOLU" || $this->_PbTech->getSolved()=="RESOLU" || $this->_PbTech->getSolved()=="EN_COURS")
			return true;
		
		$this->_errors.= "</br>ERREUR : le type résolu du problème est incorrect   ";
		return false;
	}	
	public function isOwner(){
		return ($_SESSION['id']==$this->_PbTech->getIdUser());
	}
	public function getError(){
		return $this->_errors; 
	}
}


?> 


