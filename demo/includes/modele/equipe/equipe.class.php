<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Equipe {
	private $_id;
	private $_nom;
	private $_score;
	private $_deleted;
	private $_dateCreation;
	
	public function __construct($id, $nom=NULL,$dateCreation=NULL, $score=NULL){
		$this->_id=$id;
		if ($id==NULL)
		{
			$this->_nom=$nom;
			$this->_score=$score;
			$this->_dateCreation=$dateCreation;
		}
		else
		{
			$database = new Database();
			$database->select('equipe', array("id" => $id));
			$data=$database->fetch();
			$this->_nom=$data["nom"];
			$this->_score=$data["score"];
			$this->_dateCreation=$data["dateCreation"];
		}
		$this->_deleted=false;
	}
	public function saveToDb(){
		$database = new Database();
		if ($this->_deleted)
		{
			$database->delete('equipe', array("id" => $this->_id));
		}	
		else if ($this->_id!=NULL && $database->count('equipe', array("id" => $this->_id))) // Existe en db, on update
		{
			$database->update('equipe', array("id" => $this->_id), array("nom" => $this->_nom, "score" => $this->_score, "dateCreation" => $this->_dateCreation));
		}
		else
		{
			$database->create('equipe', array("id" => $this->_id, "nom" => $this->_nom, "score" => $this->_score, "dateCreation" => $this->_dateCreation));
			$this->_id=$database->lastInsertId();
		}
		return true;
	}
	public function getId() {
		return $this->_id;
	}
	public function getNom() {
		return $this->_nom;
	}
	public function getScore() {
		return $this->_score;
	}
	public function getDateCreation(){
		return $this->_dateCreation;
	}
	public function getDeleted(){
		return $this->_deleted;
	}
	public function setId($id) {
		$this->_id = $id;
	}
	public function setNom($nom) {
		$this->_nom = $nom;
	}
	public function setScore($score) {
		$this->_score = $score;
	}
	public function setDateCreation($date){
		$this->_dateCreation=$date;
	}
	public function setDeleted($deleted){
		$this->_deleted=$deleted;
	}
}
?>