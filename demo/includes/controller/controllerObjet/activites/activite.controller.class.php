<?php
class Controller_Activite {
	
	private $act;
	private $_errors;
	public function __construct ($act){
		$this->act=$act; 
		$this->_errors="";
	}
	
	public function isGood(){		
		return($this->idOwnerIsGood() && $this->timeStartIsGood() && $this->dureeIsGood() && $this->nomIsGood() 
		&& $this->descriptifIsGood()  &&
		$this->typeIsGood() && $this->placesLimIsGood() && $this->prixIsGood() &&
		$this->pointsIsGood() && $this->mustBeReservedIsGood()
		&& $this->lieuIsGood() && $this->dateReservationIsGood() && $this->recurrenteIsGood()); 
		
	}
	
	
	
	public function timeStartIsGood(){
		if(!empty($this->act->getDate())){
			if(is_numeric($this->act->getDate()))
			{	
				$user=new User($_SESSION["id"]);
				$userInfos=$user->getUserInfos();
				$timeArrive=$userInfos->getTimeArrive();
				if ($timeArrive>0 && $this->act->getDate()>=$timeArrive)
				{
					if($this->act->getDate()>=time())
					{		
						return true;
					}
					else
					{ 
						if ($this->act->getId()>0)
						{
							$actBefore=new Activite($this->act->getId());
							if ($actBefore->getDate()<time())
								return true;
						}					
						$this->_errors.='<br/>ERREUR : La date de début est inférieure à la date actuelle';					
					}
				}
				else
				{
					$this->_errors.="<br/>ERREUR : Vous ne pouvez pas créer une activité avant votre arrivée.";
				}
			}
			else
			{
				$this->_errors.="<br/>ERREUR : la date de début n'est pas une forme numerique ";
			}		
		}
		else 
		{ 
			$this->_errors.="<br/>ERREUR : La date de début d'activité est vide ";
			
		}
		return false; 
	}
	
	public function dureeIsGood(){
		if(!empty($this->act->getDuree()))
		{	if(is_numeric($this->act->getDuree()))
			{
				if($this->act->getDuree()>0)
				{	
					$user=new User($_SESSION["id"]);
					$userInfos=$user->getUserInfos();
					$timeDepart=$userInfos->getTimeDepart();
					if ($timeDepart>0 && $this->act->getDate()+$this->act->getDuree()*60<=$timeDepart)
					{			
						return true; 		
					}
					else
					{
						$this->_errors.="<br/>ERREUR : Vous ne pouvez pas créer une activité après votre départ.";
					}
				}
				else
				{ $this->_errors.="<br/>ERREUR : La durée de l'activité ne peut être négative ou nulle";
				   
				}
			}
			else
			{
				$this->_errors.="<br/>ERREUR : la durée entrée n'est pas de la forme numérique";
				
			}
		}
		else 
		{
			$this->_errors.="<br/>ERREUR : La durée de l'activité est vide";
			
		}
		return false; 
		
		
	}
	public function nomIsGood(){
		
		if(!empty($this->act->getNom()))
		{  
			$name_first=unserialize($this->act->getNom());
			$name_first=$name_first[LANG_USER];
			if((strlen($name_first)<40) && strlen($name_first)>3)
			{
				return true;
			
			}
			else 
			{
				$this->_errors.='<br/>ERREUR : Le nom doit contenir entre 3 et 40 caractères';
				
			}
		}
		else
		{
			$this->_errors.='<br/>ERREUR : Le nom de l activité est vide';
			
		}
		
		return false; 		
		
		
	}
	public function descriptifIsGood(){
		if(!empty($this->act->getDescriptif()))
		{
			$desc_first=unserialize($this->act->getDescriptif());
			$desc_first=$desc_first[LANG_USER];
			if(isStaff() || (strlen($desc_first)>=20 && strlen($desc_first)<=300))
			{	
				return true;
			}
			else
			{
				$this->_errors.='<br/>ERREUR : Le descriptif de l activité doit contenir entre 20 et 300 caractères';
				
			}
		}
		else
		{
			$this->_errors.='<br/>ERREUR : Le descriptif de l activite est vide';
			
		}
		
		return false; 
	}
	
	public function lieuIsGood(){
		$database = new Database();
		
		if(!empty($this->act->getLieu()))
			{
				if(strlen($this->act->getLieu())<51 && (strlen($this->act->getLieu())>3))
				{
					return true;
				}
				else 
				{
					$this->_errors.="<br/>ERREUR : le nom du lieu doit être compris entre 4 et 50 caractères  ";
					
				}
			}
			else 
			{	$this->act->setLieu("Lieu non précisé");
				$this->_errors.="<br/>Attention : aucun lieu n'a été précisé. Nous vous conseillons d'ajouter un lieu dans Gérer mes activités";
				return true;
			}
		return false;
		
		
	}
	
	public function typeIsGood(){
		if(!empty($this->act->getType()))
		{ 
			return true;
		}
		else
		{
			$this->_errors.='<br/>ERREUR : Le type de l activite est vide';
			
		}
		
		
		return false;
		
	}
	
	public function placesLimIsGood(){
		
		if($this->act->getMustBeReserved()==1)
		{	if(is_numeric($this->act->getPlacesLim()))
			{
				if($this->act->getPlacesLim()>=0)
				{
					return true; 					
				}
				else
				{ 
					$this->_errors.="<br/>ERREUR : Le nombre de places maximum pour l'activité ne peut être négatif ";
				}
			}
			else
			{
				$this->_errors.="<br/>ERREUR : le nombre de places entré n'est pas un entier ";
				
			}
		}
		else 
		{
			$this->act->setPlacesLim(0);
			return true; 
			
		}
		
		return false;
			
		
		
	}
	public function prixIsGood(){
		
		if(!empty($this->act->getPrix()) || $this->act->getPrix()==0)
		{	if(is_numeric($this->act->getPrix()))
			{
				if($this->act->getPrix()>=0)
				{
					return true; 					
				}
				else
				{ $this->_errors.="<br/>ERREUR : Le prix de l'activité ne peut être négatif";
				  
				}
			}
			else
			{
				$this->_errors.="<br/>ERREUR : le prix de l'activité n'est pas de la forme numérique";
				
			}
		}
		else 
		{	echo $this->act->getPrix();
			$this->_errors.="<br/>ERREUR : Le prix de l'activité est vide";
			
		}
		
		return false; 
		
	}
	
	public function idOwnerIsGood(){
		$database = new Database();
		if(is_numeric($this->act->getIdOwner()))
		{
			if($database->count('users', array("id" => $this->act->getIdOwner())==1))
			{
				return true; 					
			}
			else
			{ $this->_errors.="<br/>ERREUR : Le gérant de l'activité n'existe pas ";
			   
			}
		}
		else
		{
			$this->_errors.="<br/>ERREUR :Le gérant de l'activité passé en paramètre n'est pas un entier ";
			
		}
		return false; 
		
		
		
		
	}
	public function pointsIsGood(){
		
		if(is_numeric($this->act->getPoints()))
		{
			if($this->act->getPoints()>=0 && $this->act->getPoints()<1000000000)
			{	
				if(empty($this->act->getPoints()))
				{
					
					$this->act->setPoints(0);
				}
				return true; 					
			}
			else
			{ $this->_errors.="<br/>ERREUR : le nombre de points pour l'activité doit être compris entre 0 et 1 000 000 000  ";
			   
			}
		}
		else
		{
			$this->_errors.="<br/>ERREUR : le nombre de points entré n'est pas un entier ";
			
		}
		return false; 
	}
		
			
	public function mustBeReservedIsGood(){
		if ($this->act->getMustBeReserved()==0 || $this->act->getMustBeReserved()==1) 
			return true;
		
		$this->_errors.="<br/>ERREUR : Vous devez indiquer si l'activité doit être réserver ou non.";
		return false;
	}	
	
	public function dateReservationIsGood(){
		if($this->act->getMustBeReserved()==0)
		{
			$this->act->setFinReservation(0);
			$this->act->setDebutReservation(0);
			return true;
		}
		else
		{		
			if(is_numeric($this->act->getDebutReservation()) && is_numeric($this->act->getFinReservation()))
			{	
				if($this->act->getDebutReservation()<$this->act->getFinReservation())
				{		
					return true;
					
				}
				else
				{ 
					$this->_errors.='<br/>ERREUR : La date de début de réservation est supérieure à la date de fin de réservation';
					
				}
			}
			else
			{
				$this->_errors.="<br/>ERREUR : l'une des dates de réservation n'est pas au format numérique  ";
			}
				
		}
			
		return false; 
	}
	public function recurrenteIsGood(){
		if(empty($this->act->getIdRecurrente()) || $this->act->getIdRecurrente()==-1)
		{
			$this->act->setIdRecurrente(-1);
			return true; 
		}
		else
		{
			if(is_numeric($this->act->getIdRecurrente()))
			{
				
				$db = new Database();		
				if($db->count('activities',array('id' => $this->act->getIdRecurrente())))
				{
					return true; 
				}
				else
				{
					$this->_errors.="<br/>ERREUR : activité de référence inexistante"; 
				}
			}
			else
			{
				$this->_errors.="<br/>ERREUR : id de l'activité de récurrence de mauvais type" ;
			}
				return false;
		}
	}
	public function getError(){ return $this->_errors; }
}
?> 