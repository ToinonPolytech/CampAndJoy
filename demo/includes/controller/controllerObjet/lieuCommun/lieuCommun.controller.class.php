<?php
class Controller_LieuCommun {

	private $LC; 
	private $_errors;
	
	public function __construct ($LC){
		$this->LC=$LC;
		$this->_errors="";
	}
	public function isGood(){
		return ($this->nomIsGood() && $this->descriptionIsGood() && $this->estReservableIsGood() && $this->heureReservableIsGood());
	}
	public function nomIsGood(){
		if(!empty($this->LC->getNom()))
		{
			if((strlen($this->LC->getNom())<40) && strlen($this->LC->getNom())>3)
			{
				return true;
			}
			else 
			{
				$this->_errors.='</br>ERREUR : Le nom doit contenir entre 3 et 40 caractères';
				return false;
			}
		}
		else
		{
			$this->_errors.='</br>ERREUR : Le nom du lieu crée est vide';
			return false; 
		}	
	}
	public function descriptionIsGood(){
	if(!empty($this->LC->getDescription()))
		{
			return true;
		}
		else
		{
			$this->_errors.='</br>ERREUR : La description du lieu est vide';
			return false;
		}
	} 	
	public function estReservableIsGood(){
		if (is_bool($this->LC->getEstReservable()))
			return true;
		
		$this->_errors.='</br>ERREUR : Le type de réservation est erroné.';
		return false;
	}
	public function heureReservableIsGood(){
		$temp=@unserialize($this->LC->getHeureReservable()); // @ pour masquer le warning en cas d'erreur
		$returnValue=false;
		if (is_array($temp))
		{
			$returnValue=true;
			for ($i=0;$i<7;$i++)
			{
				for ($j=0;$j<48;$j++)
				{
					if (!isset($temp[$i][$j]) || !is_bool($temp[$i][$j]))
					{
						$returnValue=false;
					}
				}
			}
		}
		if (!$returnValue)
			$this->_errors.='</br>ERREUR : Les heures de réservation sont erronées.';
		
		return $returnValue;
	}
	public function getError(){ return $this->_errors; }
}
?>
