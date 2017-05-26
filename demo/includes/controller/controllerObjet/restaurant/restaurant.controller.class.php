 <?php 
class Controller_Restaurant{
	private $resto; 
	private $_errors;
	public function __construct ($resto){
		$this->resto=$resto;
		$this->_errors="";
	}
	
	public function isGood(){
		return ($this->nomIsGood() && $this->capaciteIsgood() && $this->heureOuvertureIsGood() && $this->telephoneIsGood() && $this->mailIsGood() && $this->gerantIsGood()); 
	}
	
	public function nomIsGood(){
		if(!empty($this->resto->getNom()))
		{
			if((strlen($this->resto->getNom())<40) && strlen($this->resto->getNom())>3)
			{
				return true;
			}
			else 
			{
				$this->_errors.='ERREUR : Le nom doit du resto contenir entre 3 et 40 caractères';
			}
		}
		else
		{
			$this->_errors.='ERREUR : Le nom du resto est vide';	
		}	
		return false; 		
	}
	public function capaciteIsGood(){
		if(!empty($this->resto->getCapacite()))
		{
			if(is_numeric($this->resto->getCapacite()))
			{
				if($this->resto->getCapacite()>0 && $this->resto->getCapacite()<1000)
				{
					return true; 	
				}
				else 
				{
					$this->_errors.="ERREUR : ".$this->resto->getCapacite()." semble un nombre peu probable pour un restaurant";
				}
			}
			else
			{
				$this->_errors.="ERREUR : la capacite entrée n'est pas un nombre ";
			}
			
		}
		else
		{
			$this->_errors.="ERREUR : pas de capacite maximale entrée pour votre restaurant "; 
		}
		return false; 
	}
	public function heureOuvertureIsGood(){
		$temp=@unserialize($this->resto->getHeureOuverture()); // @ pour masquer le warning en cas d'erreur
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
			$this->_errors.='ERREUR : Les heures de réservation sont erronées.';
		
		return $returnValue;
	}
	public function telephoneIsGood(){
		if (empty($this->resto->getTelephone()) || preg_match("#^0[1-68]([-. ]?[0-9]{2}){4}$#", $this->resto->getTelephone()))
			return true;
		
		$this->_errors.='ERREUR : Le numéro de téléphone est incorrect.';
		return false;
	}
	public function mailIsGood() {
		if (empty($this->resto->getMail()) || preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->resto->getMail()))
			return true;
		
		$this->_errors.='ERREUR : L\'adresse mail est incorrect.';
		return false;
	}
	public function gerantIsGood(){
		$database=new Database();
		$returnValue=true;
		foreach (explode(",", $this->resto->getGerant()) as $v)
		{
			if ($returnValue && $database->count("users", array("id" => $v))==0)
			{
				$returnValue=false;
			}
		}
		if (!$returnValue)
			$this->_errors.='ERREUR : Certains gérants n\'existent pas.';
		
		return $returnValue;
	}
	public function getErrors(){ return $this->_errors; }
}