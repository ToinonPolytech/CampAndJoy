<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class UserInfo
{	protected $_id; 
	protected $_numPlace;
	protected $_email;
	protected $_solde;
	protected $_timeDepart;
	protected $_comptesMax;
	protected $_clef;
	protected $_deleted;
	protected $_timeArrive;
	
	public function __construct($id, $numPlace=NULL, $email=NULL, $timeDepart=NULL, $timeArrive=NULL, $clef=NULL, $comptesMax=NULL){
		$this->_id=$id;
		if ($id==NULL)
		{
			$this->_numPlace=$numPlace;
			$this->_email=$email;
			$this->_solde=0;
			$this->_timeDepart=$timeDepart;
			$this->_timeArrive=$timeArrive;
			$this->_clef=$clef;		
			$this->_comptesMax=$comptesMax;
		}
		else
		{
			$database = new Database();
			$database->select('userinfos', array("id" => $id));
			$data=$database->fetch();
			$this->_numPlace=$data["emplacement"];
			$this->_email=$data["email"];
			$this->_solde=$data["solde"];
			$this->_timeDepart=$data["time_depart"];
			$this->_clef=$data["clef"];
			$this->_comptesMax=$data["comptesMax"];	
			$this->_timeArrive=$data["time_arrive"];
		}
		$this->_deleted=false;
	}

	public function saveToDb(){
		$database = new Database();
		if ($this->_deleted)
		{
			$database->delete('userinfos', array("id" => $this->_id));
		}	
		else if ($this->_id!=NULL && $database->count('userinfos', array("id" => $this->_id))) // Existe en db, on update
		{
			$database->update('userinfos', array("id" => $this->_id), array("time_arrive" => $this->_timeArrive, "comptesMax" => $this->_comptesMax, "emplacement" => $this->_numPlace, "email" => $this->_email, "solde" => $this->_solde, "time_depart" => $this->_timeDepart, "clef" => $this->_clef));
		}
		else
		{
			$database->create('userinfos', array("time_arrive" => $this->_timeArrive, "comptesMax" => $this->_comptesMax, "clef" => $this->_clef, "id" => $this->_id, "emplacement" => $this->_numPlace, "email" => $this->_email, "solde" => $this->_solde, "time_depart" => $this->_timeDepart));
			$this->_id=$database->lastInsertId(); // Ca marche ça ?
		}
	}
	
	public function setTimeArrive($t){
		$this->_timeArrive=$t;
	}
	public function getId(){
		return $this->_id;
	}
	public function getEmplacement(){
		return $this->_numPlace;
	}
	public function getEmail(){
		return $this->_email;
	}
	public function getComptesMax(){
		return $this->_comptesMax;
	}
	public function getSolde(){
		return $this->_solde;
	}
	public function getTimeDepart(){
		return $this->_timeDepart;
	}
	public function getTimeArrive(){
		return $this->_timeArrive;
	}
	public function getClef(){
		return $this->_clef;
	}
	
	public function getDeleted(){
		return $this->_deleted;
	}
	public function setId($id){
		$this->_id=$id;
	}
	public function setComptesMax($comptesMax){
		$this->_comptesMax=$comptesMax;
	}
	public function setEmplacement($emplacement){
		$this->_numPlace=$emplacement;
	}
	public function setEmail($email){
		$this->_email=$email;
	}
	public function setSolde($solde){
		$this->_solde=$solde;
	}
	public function setTimeDepart($timeDepart){
		$this->_timeDepart=$timeDepart;
	}
	public function setClef($clef){
		$this->_clef=$clef;
	}
	
	public function setDeleted($deleted){
		$this->_deleted=$deleted;
	}
}

?> 