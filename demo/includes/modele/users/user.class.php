<?php 
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,userInfo.class.php,";
include($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class User
{
	protected $_id;
	protected $_userInfos;
	protected $_accessLevel;
	protected $_droits;
	protected $_nom;
	protected $_prenom;
	protected $_code;
	protected $_clef;
	protected $_photo;
	protected $_description;
	protected $_deleted;
	protected $_token;
	
	public function __construct($id, $infoId=NULL, $accessLevel=NULL, $droits=NULL, $nom=NULL, $prenom=NULL, $code=NULL, $clef=NULL,$photo=NULL,$description=NULL, $token=""){
		$this->_id=$id;
		if ($id==NULL)
		{
			if ($infoId>0)
				$this->_userInfos=new UserInfo($infoId);
			else
				$this->_userInfos=new UserInfo(NULL);
			
			$this->_accessLevel=$accessLevel;
			$this->_droits=$droits;
			$this->_nom=$nom;
			$this->_prenom=$prenom;
			$this->_code=$code;
			$this->_clef=$clef;
			$this->_photo=$photo;
			$this->_description=$description;
			$this->_token=$token;
		}
		else
		{
			$database = new Database();
			$database->select('users', array("id" => $id));
			$data=$database->fetch();
			$this->_userInfos =new UserInfo($data["infoId"]);
			$this->_accessLevel=$data["access_level"];
			$this->_droits=$data["droits"];
			$this->_nom=$data["nom"];
			$this->_prenom=$data["prenom"];
			$this->_code=$data["code"];
			$this->_clef=$data["clef"];
			$this->_description=$data["description"];
			$this->_photo=$data["photo"];
			$this->_token=$data["token"];
		}
		$this->_deleted=false;
	}

	public function saveToDb(){
		$database = new Database();
		if ($this->_deleted)
		{
			$database->delete('users', array("id" => $this->_id));
		}	
		else if ($this->_id!=NULL && $database->count('users', array("id" => $this->_id))) // Existe en db, on update
		{
			$database->update('users', array("id" => $this->_id), array("infoId" => $this->_userInfos->getId(), "access_level" => $this->_accessLevel, "droits" => $this->_droits, "nom" => $this->_nom, "prenom" => $this->_prenom, "code" => $this->_code,"description" => $this->_description,"photo" => $this->_photo, "token" => $this->_token));
		}
		else
		{
			$database->create('users', array("clef" => $this->_clef, "id" => $this->_id, "infoId" => $this->_userInfos->getId(), "access_level" => $this->_accessLevel, "droits" => $this->_droits, "nom" => $this->_nom, "prenom" => $this->_prenom, "code" => $this->_code,"description" => $this->_description,"photo" => $this->_photo, "token" => $this->_token));
		}
	}
	public function addDroits($which){
		$controller= new Controller_User($this);
		if (!$controller->can($which)) // Si le droit n'est pas déjà activé
			$this->_droits+=pow(2,$which); // on lui rajoute
	}
	
	public function removeDroits($which){
		$controller = new Controller_User($this);
		if ($controller->can($which)) // Si le droit est activé
			$this->_droits-=pow(2,$which); // on lui enlève 
	}
	
	
	public function getUserInfos(){
		//retourne l'objet userinfo associé 
		return $this->_userInfos;
	}
	public function getId(){
		return $this->_id;
	}
	public function getAccessLevel(){
		return $this->_accessLevel;
	}
	public function getDroits(){
		return $this->_droits;
	}
	public function getNom(){
		return $this->_nom;
	}
	public function getPrenom(){
		return $this->_prenom;
	}
	public function getCode(){
		return $this->_code;
	}
	public function getDeleted(){
		return $this->_deleted;
	}
	public function getClef(){
		return $this->_clef;
	}
	public function getPhoto(){
		return $this->_photo;
	}
	public function getDescription(){
		return $this->_description;
	}
	public function setId($id){
		$this->_id=$id;
	}
	public function setUserInfos($object){
		$this->_userInfos=$object;
	}
	public function setAccessLevel($accessLevel){
		$this->_accessLevel=$accessLevel;
	}
	public function setDroits($droits){
		$this->_droits=$droits;
	}
	public function setNom($nom){
		$this->_nom=$nom;
	}
	public function setPrenom($prenom){
		$this->_prenom=$prenom;
	}
	public function setCode($code){
		$this->_code=$code;
	}
	public function setClef($clef){
		$this->_clef=$clef;
	}
	public function setPhoto($photo){
		$this->_photo=$photo;
	}
	public function setDescription($description){
		$this->_description=$description;
	}
	public function setDeleted($deleted){
		$this->_deleted=$deleted;
	}
	public function setToken($token){
		$this->_token=$token;
	}
	public function getToken(){
		return $this->_token;
	}
}