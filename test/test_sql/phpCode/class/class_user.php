<?php
class user implements JsonSerializable
{
	public $_id;
	public $password;
	public $name;
	public $tel;
	public $email;
	public $address;
	public $sellList;
	public $buyList;

	public function __construct(){
		$this->_id = null;
		$this->password = null;
		$this->name = null;
		$this->tel = null;
		$this->email = null;
		$this->address = null;
		$this->sellList = array();
		$this->buyList = array();
	}

	public function jsonSerialize() {
		return $this;
	}

	public function fill($user){
		$this->_id = $user->_id;
		$this->password = $user->password;
		$this->name = $user->name;
		if(property_exists($user, 'tel'))
			$this->tel = $user->tel;
		if(property_exists($user, 'email'))
			$this->email = $user->email;
		if(property_exists($user, 'address'))
			$this->address = $user->address;
		if(property_exists($user, 'sellList'))
			$this->sellList = $user->sellList;
		if(property_exists($user, 'buyList'))
			$this->buyList = $user->buyList;
	}

	// no getter required because of public
	// no modify required becasue only fetch back utility is required in our project

	// TOOD is mongoDB able to insert a buy entry? or sell entry?

}
?>