<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Reservation {
	private $_idAuto;
	private $_id;
	private $_type;
	private $_idUser;
	private $_time;
	private $_nbrPersonne;
	private $_valide;
	private $_duree;
	private $_deleted;
	public function __construct($id, $type=NULL, $idUser=NULL, $nbrPersonne=NULL, $time=NULL, $valide=1, $duree=0){
		$this->_id=$id;
		$this->_idUser=$idUser;
		$this->_type=$type;
		$this->_idAuto=NULL;
		$this->_duree=$duree;
		if ($time!=NULL && $nbrPersonne!=NULL)
		{	
			$this->_nbrPersonne=$nbrPersonne;
			$this->_time=$time;
			$this->_valide=$valide;
		}
		else if ($type==NULL)
		{
			$database = new Database();
			$database->select('reservation', array("idAuto" => $id));
			$data=$database->fetch();
			$this->_id=$data["id"];
			$this->_idUser=$data["idUser"];
			$this->_type=$data["type"];
			$this->_nbrPersonne=$data["nbrPersonne"];
			$this->_time=$data["time"];
			$this->_idAuto=$data["idAuto"];
			$this->_valide=$data["valide"];
			$this->_duree=$data["duree"];
		}
		else
		{
			$database = new Database();
			$database->select('reservation', array("id" => $id, "type" => $type, "idUser" => $idUser), array("duree", "nbrPersonne", "time", "idAuto", "valide"));
			$data=$database->fetch();
			$this->_nbrPersonne=$data["nbrPersonne"];
			$this->_time=$data["time"];
			$this->_idAuto=$data["idAuto"];
			$this->_valide=$data["valide"];
			$this->_duree=$data["duree"];
		}
		$this->_deleted=false;
	}
	public function saveToDb(){
		$database = new Database();
		if ($this->_deleted)
		{
			$database->delete('reservation', array("id" => $this->_id, "type" => $this->_type, "idUser" => $this->_idUser));
		}	
		else if ($database->count('reservation', array("id" => $this->_id, "type" => $this->_type, "idUser" => $this->_idUser))) // Existe en db, on update
		{
			$database->update('reservation', array("id" => $this->_id, "time" => $this->_time, "type" => $this->_type, "idUser" => $this->_idUser), array("valide" => $this->_valide, "duree" => $this->_duree, "nbrPersonne" => $this->_nbrPersonne));
		}
		else
		{
			$database->create('reservation', array("duree" => $this->_duree, "id" => $this->_id, "time" => $this->_time, "type" => $this->_type, "idUser" => $this->_idUser, "nbrPersonne" => $this->_nbrPersonne));
			$this->_idAuto=$database->lastInsertId();
		}
	}
	public function getDuree(){
		return $this->_duree;
	}
	public function setValide($v) {
        $this->_valide=$v;
    }
	public function getValide() {
        return $this->_valide;
    }
    public function getId() {
        return $this->_id;
    }
    public function getIdUser() {
        return $this->_idUser;
    }
	public function getIdAuto() {
        return $this->_idAuto;
    }
	public function getType() {
        return $this->_type;
    }
    public function getNbrPersonne() {
        return $this->_nbrPersonne;
    }
	public function getDeleted(){
		return $this->_deleted;
	}
	public function getTime(){
		return $this->_time;
	}
	public function setId($id) {
        $this->_id=$id;
    }
	public function setType($type) {
        $this->_type=$type;
    }
    public function setIdUser($idUser) {
        $this->_idUser=$idUser;
    }
    public function setNbrPersonne($nbrPersonne) {
        $this->_nbrPersonne=$nbrPersonne;
    }
	public function setDeleted($deleted){
		$this->_deleted=$deleted;
	}
	public function setTime($time){
		$this->_time=$time;
	}
}
?>