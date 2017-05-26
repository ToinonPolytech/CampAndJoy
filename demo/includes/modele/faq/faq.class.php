<?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class FAQ {
	private $_id;
	private $_question;
	private $_reponse;
	private $_deleted;	
	
	public function __construct($id, $question=NULL,$reponse=NULL){
		$this->_id=$id;
		if ($id==NULL)
		{
			$this->_question=$question;
			$this->_reponse=$reponse;			
		}
		else
		{
			$database = new Database();
			$database->select('faq', array("id" => $id));
			$data=$database->fetch();
			$this->_question=$data["question"];
			$this->_reponse=$data["reponse"];			
		}
		$this->_deleted=false;
	}
	public function saveToDb(){
		$database = new Database();
		if ($this->_deleted)
		{
			$database->delete('faq', array("id" => $this->_id));
		}	
		else if ($this->_id!=NULL && $database->count('faq', array("id" => $this->_id))) // Existe en db, on update
		{
			$database->update('faq', array("id" => $this->_id), array("question" => $this->_question, "reponse" => $this->_reponse));
		}
		else
		{
			$database->create('faq', array("id" => $this->_id, "question" => $this->_question, "reponse" => $this->_reponse));
			$this->_id=$database->lastInsertId();
		}
		return true;
	}
	public function getId() {
		return $this->_id;
	}
	public function getQuestion() {
		return $this->_question;
	}
	public function getReponse() {
		return $this->_reponse;
	}
	public function getDeleted(){
		return $this->_deleted;
	}
	public function setId($id) {
		$this->_id = $id;
	}
	public function setQuestion($question) {
		$this->_question = $question;
	}
	public function setReponse($reponse) {
		$this->_reponse = $reponse;
	}
	public function setDeleted($deleted){
		$this->_deleted=$deleted;
	}
}
?>