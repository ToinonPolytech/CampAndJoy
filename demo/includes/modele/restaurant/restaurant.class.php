 <?php
if (!isset($require))
{
	$require="";
}
$require.="database.class.php,";
require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
class Restaurant{
	private $_id;
	private $_nom;
	private $_telephone;
	private $_mail;
	private $_gerants;
	private $_capacite;
	private $_hOuv;
	private $_photo;
	private $_menu;
	private $_deleted;
	public function __construct($id, $nom=NULL, $telephone=NULL, $mail=NULL, $gerants=NULL, $capacite=NULL, $hOuv=NULL, $photo=NULL, $menu=NULL){
		$this->_id = $id;
		if ($id==NULL)
		{
			$this->_nom = $nom;
			$this->_capacite = $capacite;
			$this->_telephone = $telephone;
			$this->_mail = $mail;
			$this->_gerants = $gerants;
			$this->_hOuv = $hOuv;
			$this->_photo = $photo;
			$this->_menu=$menu;
		}
		else
		{
			$database = new Database();
			$database->select('restaurant', array("id" => $this->_id));
			$data=$database->fetch();
			$this->_nom = $data['nom'];
			$this->_capacite = $data['capacite'];
			$this->_telephone = $data['telephone'];
			$this->_mail = $data['mail'];
			$this->_gerants = $data['idsUsers'];
			$this->_hOuv = $data['heureOuverture'];
			$this->_photo = $data['photos'];
			$this->_menu=$data['menu'];
		}
		$this->_deleted=false;
	}
	public function saveToDb(){
		$database = new Database();
		if ($this->_deleted)
		{
			$database->delete('restaurant', array("id" => $this->_id));
		}	
		else if ($this->_id!=NULL && $database->count('restaurant', array("id" => $this->_id))) // Existe en db, on update
		{
			$database->update('restaurant', array("id" => $this->_id), array("menu" => $this->_menu, "idsUsers" => $this->_gerants, "telephone" => $this->_telephone, "mail" => $this->_mail, "nom" => $this->_nom, "capacite" => $this->_capacite, "heureOuverture" => $this->_hOuv, "photos" => $this->_photo));
		}
		else
		{
			$database->create('restaurant', array("menu" => $this->_menu, "idsUsers" => $this->_gerants, "telephone" => $this->_telephone, "mail" => $this->_mail, "id" => $this->_id, "nom" => $this->_nom, "capacite" => $this->_capacite, "heureOuverture" => $this->_hOuv, "photos" => $this->_photo));
		}
	}
	public function getId() {
	   return $this->_id;
	}
	public function getMenu() {
	   return $this->_menu;
	}
	public function getTelephone() {
	   return $this->_telephone;
	}
	public function getMail() {
	   return $this->_mail;
	}
	public function getGerant() {
	   return $this->_gerants;
	}
	public function getNom() {
	   return $this->_nom;
	}
	public function getCapacite() {
	   return $this->_capacite;
	}
	public function getHeureOuverture() {
	   return $this->_hOuv;
	}
	public function getPhoto() {
	   return $this->_photo;
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
	public function setCapacite($capacite) {
	   $this->_capacite = $capacite;
	}
	public function setHeureOuverture($hOuv) {
	   $this->_hOuv = $hOuv;
	}
	public function setPhoto($photo) {
	   $this->_photo = $photo;
	}
	public function setDeleted($deleted){
		$this->_deleted=$deleted;
	}
}
