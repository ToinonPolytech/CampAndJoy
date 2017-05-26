<?php
class Controller_FAQ
{
	private $_faq;
	private $_errors;
	public function __construct ($faq){
		$this->_faq=$faq;
		$this->_errors="";
	}
	public function isGood(){
		return ($this->questionIsGood() && $this->reponseIsGood());
	}
	public function questionIsGood(){
		if(!empty($this->_faq->getQuestion()))
		{	
			return true; 
		}
		else 
		{
			$this->_errors.= "</br>ERREUR : la question n'a pas été remplie";
		}
		return false; 
	}
	
	public function reponseIsGood(){
		if(!empty($this->_faq->getReponse()))
		{
			if(strlen($this->_faq->getReponse())<=1000 || strlen($this->_faq->getReponse())>=20)
			{
				return true;
			}
			else
			{
				$this->_errors.= "</br>ERREUR : la réponse à la question n'est pas de la bonne taille (entre 20 et 1 000 caractères) ";
			}	
		}
		else
		{
			$this->_errors.= "</br>ERREUR : la réponse à la question est vide";
		}
		return false;
	}
	public function getError(){
		return $this->_errors;	
		
	}
}
?>