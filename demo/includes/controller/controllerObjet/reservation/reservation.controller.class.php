<?php 
class Controller_Reservation
{
	private $_reservation;
	private $_errors;
	public function __construct ($reservation){
		$this->_reservation=$reservation;
		$this->_errors="";
	}
	public function isGood(){
		
		return ($this->idIsGood() && $this->typeIsGood() && $this->idUserIsGood() && $this->nbrPersonneIsGood() && $this->reservationIsAvailable() && $this->timeIsGood() && $this->dureeIsGood());
	}
	public function dureeIsGood(){
		if (is_numeric($this->_reservation->getDuree()) && $this->_reservation->getDuree()>=0)
		{
			return true;
		}
		$this->_errors.="<br/>ERREUR : La durée doit être positive.";
		return false;
	}
	public function etatLieuxIsGood(){
		$user = new User($this->_reservation->getIdUser()); 
		$dateDep= date('d/m/Y', $user->getUserInfos()->getTimeDepart());
		$debutJournee=strtotime(date('y-m-d', $user->getUserInfos()->getTimeDepart()));
		$finJournee=$debutJournee+3600*24;
		$db = new Database();
		$db2= new Database();
		$db->select('reservation',array('type' => 'ETAT_LIEUX', 'time' => array($debutJournee, $finJournee)),"time");
		$db2->select('etat_lieux',array('debutTime' => array('>=', $debutJournee), 'finTime' => array('<=', $finJournee)));
		$hDispo=array();
		$hPrise=array();
		while($res=$db->fetch())
		{
			if (isset($hPrise[$res['time']]))
				$hPrise[$res['time']]+=1;
			else
				$hPrise[$res['time']]=1;
		}
		while($edl=$db2->fetch())
		{	
			for($i=$edl['debutTime'];$i<=$edl['finTime'];$i+=60*$edl['duree_moyenne'])
			{
				if (isset($hDispo[$i]))
					$hDispo[$i]+=1;
				else
					$hDispo[$i]=1;
			}
		}
		return (isset($hDispo[$this->_reservation->getTime()]) && $hDispo[$this->_reservation->getTime()]-$hPrise[$this->_reservation->getTime()]>0 && $db->count("etat_lieux", array("idUser" => $this->_reservation->getId(), "debutTime" => array("<=", $this->_reservation->getTime()), "finTime" => array(">=", $this->_reservation->getTime())))>0);
	}
	public function timeIsGood(){
		if (!empty($this->_reservation->getTime()))
		{
			if (is_numeric($this->_reservation->getTime()))
			{
				if ($this->_reservation->getTime()>time())
					return true;
				else
					$this->_errors.="<br/>ERREUR : Vous ne pouvez réserver dans le passé.";
			}
			else
				$this->_errors.="<br/>ERREUR : Merci de sélectionner l'horaire pour la réservation.";
		}
		else
			$this->_errors.="<br/>ERREUR : Merci de sélectionner l'horaire pour la réservation.";
		
		return false;
	}
	public function typeIsGood(){
		if(!empty($this->_reservation->getType()))
		{
			if($this->_reservation->getType()=='ACTIVITE' || $this->_reservation->getType()=='LIEU_COMMUN' 
			|| $this->_reservation->getType()=='RESTAURANT' ||$this->_reservation->getType()=='ETAT_LIEUX')
			{
				return true;
			}
			else
			{
				$this->_errors.="<br/>ERREUR : le service que vous chez à réserver n'existe pas";
				
			}
		}
		else
		{
			$this->_errors.="<br/>ERREUR : aucun service à réserver selectionné"; 
		}
		return false;
		
		
	}
	public function etatLieuxCommunIsGood(){
		$database = new Database();
		if ($database->count("lieu_commun", array("id" => $this->_reservation->getId())))
		{
			$database->select("lieu_commun", array("id" => $this->_reservation->getId()), array("timeReservation"));
			$data=$database->fetch();
			$timestamp=$this->_reservation->getTime();
			$timestamp_end=$this->_reservation->getTime()+$this->_reservation->getDuree();
			$horaires=unserialize($data["timeReservation"]);
			$time_debut_day=strtotime(date("Y-m-d", $timestamp));
			$time_fin_day=$time_debut_day+3600*24;
			$database->select("reservation", array("id" => $this->_reservation->getId(), "type" => "LIEU_COMMUN", "time" => array($time_debut_day, $time_fin_day)), array("time", "duree"));
			while ($data=$database->fetch())
			{
				$hours=date("H", $data["time"])*2+floor(date("i", $data["time"])/30);
				$hours_fin=date("H", $data["time"]+$data["duree"])*2+floor(date("i", $data["time"]+$data["duree"])/30);
				for ($i=$hours;$i<$hours_fin;$i++)
				{
					$horaires[$i]=false;
				}
			}
			$open=true;
			for ($i=$timestamp;$i<=$timestamp_end && $open;$i=$i+30*60)
			{
				if (date("i", $i)>=30)
					$open=$horaires[date("w", $i)][date("H", $i)*2+1];
				else
					$open=$horaires[date("w", $i)][date("H", $i)*2];
			}
			if ($open)
			{
				return true;
			}
			else
			{
				$this->_errors.="<br/>ERREUR : Vous ne pouvez pas réserver lorsque l'installation est fermée.";
			}
		}
		else
		{
			$this->_errors.="<br/>ERREUR : L'installation n'existe pas.";
		}
		return false;
	}
	public function restIsAvailable(){
		$database = new Database();
		if ($database->count("restaurant", array("id" => $this->_reservation->getId())))
		{
			$database->select("restaurant", array("id" => $this->_reservation->getId()), array("capacite", "heureOuverture"));
			$data=$database->fetch();
			$timestamp=$this->_reservation->getTime();
			$horaires=unserialize($data["heureOuverture"]);
			if (date("i", $timestamp)>=30)
				$open=$horaires[date("w", $timestamp)][date("H", $timestamp)*2+1];
			else
				$open=$horaires[date("w", $timestamp)][date("H", $timestamp)*2];
			
			if ($open)
			{
				$capacite=$data["capacite"];
				$database->select("reservation", array("type" => "RESTAURANT", "id" => $this->_reservation->getId(), "time" => array($timestamp, $timestamp+30*60-1)), "nbrPersonne");
				while ($d=$database->fetch()) { $capacite-=$d["nbrPersonne"]; }
				if ($capacite>=$this->_reservation->getNbrPersonne())
				{
					return true;
				}
				else
				{
					$this->_errors.="<br/>ERREUR : Vous ne pouvez pas réserver pour ".htmlentities($this->_reservation->getNbrPersonne())." personnes.<br/> Le restaurant n'a plus assez de places.";
				}
			}
			else
			{
				$this->_errors.="<br/>ERREUR : Vous ne pouvez pas réserver lorsque le restaurant est fermé.";
			}
		}
		else
		{
			$this->_errors.="<br/>ERREUR : Le restaurant n'existe pas.";
		}
		return false;
	}
	public function reservationIsAvailable(){
		if ($this->_reservation->getType()=="ACTIVITE")
			return $this->actIsAvailable();
		else if ($this->_reservation->getType()=="RESTAURANT")
			return $this->restIsAvailable();
		else if ($this->_reservation->getType()=="ETAT_LIEUX")
			return $this->etatLieuxIsGood();
		else if ($this->_reservation->getType()=="LIEU_COMMUN")
			return $this->etatLieuxCommunIsGood();
		
		return false;
	}
	public function idIsGood(){
		if (!empty($this->_reservation->getId()))
		{
			if (is_numeric($this->_reservation->getId()))
			{
				return true;
			}
			else
				$this->_errors.="<br/>ERREUR : Le service réservé n'est pas valide.";
		}
		else
			$this->_errors.="<br/>ERREUR : Vous devez sélectionner un service à réserver .";
		
		return false;
	}
	public function idUserIsGood(){
		if (!empty($this->_reservation->getIdUser()))
		{
			if (is_numeric($this->_reservation->getIdUser()))
			{
				$database=new Database();
				if ($database->count('users', array("id" => $this->_reservation->getIdUser()))==1)
					return true;
				else
					$this->_errors.="<br/>ERREUR : Le client n'existe pas.";
			}
			else
				$this->_errors.="<br/>ERREUR : Le client n'est pas valide.";
		}
		else
			$this->_errors.="<br/>ERREUR : Vous devez être connecté / Le client sélectionné doit être valide.";
		
		return false;
	}
	public function nbrPersonneIsGood(){
		if (!empty($this->_reservation->getNbrPersonne()))
		{
			if (is_numeric($this->_reservation->getNbrPersonne()) && $this->_reservation->getNbrPersonne()>0)
				return true;
			else
				$this->_errors.="<br/>ERREUR : Vous devez rentrer un nombre de personnes supérieur à 1.";
		}
		else
			$this->_errors.="<br/>ERREUR : Vous devez indiquer pour combien vous réservez.";
		
		return false;
	}
	public function actIsAvailable(){
		$database=new Database();
		if (!$database->count('activities', array("id" => $this->_reservation->getId()))==1)
		{
			$this->_errors.="<br/>ERREUR : L'activité n'existe pas.";
			return false;
		}
		$act=new Activite($this->_reservation->getId());
		$user=new User($this->_reservation->getIdUser());
		if (!$act->getMustBeReserved())
		{
			$this->_errors.="<br/>ERREUR : Cette activité ne peut être réservée.";
			return false;
		}
		if ($act->getDebutReservation()>time())
		{
			$this->_errors.="<br/>ERREUR : Cette activité n'est pas encore réservable.";
			return false;
		}
		if ($act->getFinReservation()<time())
		{
			$this->_errors.="<br/>ERREUR : Cette activité n'est plus réservable";
			return false;
		}
		if ($act->getDate()<time())
		{
			$this->_errors.="<br/>ERREUR : Vous ne pouvez pas réserver une activité qui est déjà passé.";
			return false;
		}
		$capacite=$act->getPlacesLim();
		if ($capacite>0)
		{
			if ($this->_reservation->getIdAuto()!=NULL)
			{
				$database->select("reservation", array("type" => "ACTIVITE", "id" => $this->_reservation->getId(), "idAuto" => array("!=", $this->_reservation->getIdAuto())), "nbrPersonne");
			}
			else
			{
				$database->select("reservation", array("type" => "ACTIVITE", "id" => $this->_reservation->getId()), "nbrPersonne");
			}
			while ($d=$database->fetch()) { $capacite-=$d["nbrPersonne"]; }
			if ($capacite<$this->_reservation->getNbrPersonne())
			{
				$this->_errors.="<br/>ERREUR : Il n'y a plus assez de places disponible.";
				return false;
			}
		}
		return true;
	}
	public function getError(){ return $this->_errors; }
}
?>