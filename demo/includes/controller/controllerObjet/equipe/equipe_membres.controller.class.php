<?php 
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Controller_Equipe_Membres
{
	private $_equipe_membres;
	private $_errors; 
	public function __construct ($equipe_membres){
		$this->_equipe_membres=$equipe_membres;
		$this->_errors="";
	}
	public function isGood(){
		return ($this->userIsGood() && $this->equipeIsGood());
	}
	public function userIsGood(){
		$db = new Database();
		if($db->count("users", array('id'=>$this->_equipe_membres->getUser()))==1)
		{
			return true; 
		}
		else
		{
			$this->_errors.= "</br>L'utilisateur n'existe pas";
		}
		return false; 
	}
	public function equipeIsGood(){
		$db = new Database();		
		if($db->count("equipe", array('id'=>$this->_equipe_membres->getEquipe()))==1)
		{
			return true; 
		}
		else
		{
			$this->_errors.= "</br>L'equipe n'existe pas";
		}
		return false;	
	}
	public function getError(){
		return $this->_errors;		
	}
}
?>