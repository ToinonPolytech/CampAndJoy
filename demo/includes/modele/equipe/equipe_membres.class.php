<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Equipe_Membres {
	private $_idEquipe;
	private $_idUser;
	private $_peutModifier;
	private $_deleted;

	public function __construct($idEquipe,$idUser,$peutModifier=NULL){
		$database = new Database();
		$this->_idEquipe=$idEquipe;
		$this->_idUser=$idUser;
		if (!$database->count('equipe_membres', array("idEquipe" => $this->_idEquipe, "idUser" => $this->_idUser)))
		{			
			$this->_peutModifier=$peutModifier;
		}
		else
		{
			$db = new Database();
			$database->select("equipe_membres",array("idUser" => $idUser, "idEquipe" => $idEquipe), "peutModifier");
			$data = $database->fetch();
			$this->_peutModifier=$data['peutModifier'];
		}
		$this->_deleted=false; 
	}
	public function saveToDb(){
		$database = new Database();
		if ($this->_deleted)
		{
			$database->delete('equipe_membres', array("idEquipe" => $this->_idEquipe, "idUser" => $this->_idUser));
		}	
		else if($database->count('equipe_membres', array("idEquipe" => $this->_idEquipe, "idUser" => $this->_idUser))) // Existe en db, on update
		{
			$database->update('equipe_membres', array("idEquipe" => $this->_idEquipe, "idUser" => $this->_idUser), array("peutModifier" => $this->_peutModifier));
		}
		else
		{
			$database->create('equipe_membres', array("idEquipe" => $this->_idEquipe, "idUser" => $this->_idUser, "peutModifier" => $this->_peutModifier));
		}
		return true;
	}
	public function getUser() {
		return $this->_idUser;
	}
	public function getEquipe() {
		return $this->_idEquipe;
	}
	public function getPeutModifier(){
		return $this->_peutModifier;
	}
	public function getDeleted(){
		return $this->_deleted;
	}
	public function setUser($id) {
		$this->_idUser = $id;
	}
	public function setEquipe($id) {
		$this->_idEquipe = $id;
	}
	public function setPeutModifier($peutModifier){
		return $this->_peutModifier=$peutModifier;
	}
	public function setDeleted($deleted){
		$this->_deleted=$deleted;
	}
}