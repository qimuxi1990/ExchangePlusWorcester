<?php
class product implements JsonSerializable
{
	public $_id;
	public $name;
	public $category;
	public $demanding_price;
	public $image;
	public $seller_id;
	public $product_status;
	public $buyList;

	public function __construct(){
		$this->_id = null;
		$this->name = null;
		$this->category = null;
		$this->demanding_price = null;
		$this->image = null;
		$this->seller_id = null;
		$this->product_status = 'pending';
		$this->buyList = array();
	}

	public function jsonSerialize() {
		return $this;
	}

	public function fill($product){
		$this->_id = $product->_id;
		$this->name = $product->name;
		$this->category = $product->category;
		if(property_exists($product, 'demanding_price'))
			$this->demanding_price = $product->demanding_price;
		if(property_exists($product, 'image'))
			$this->image = $product->image;
		if(property_exists($product, 'seller_id'))
			$this->seller_id = $product->seller_id;
		$this->product_status = $product->product_status;
		if(property_exists($product, 'buyList'))
			$this->buyList = $product->buyList;
	}

	// no getter required because of public
	// no modify required becasue only fetch back utility is required in our project

	// TOOD is mongoDB able to insert a buy entry? or sell entry?

}
?>