<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Order extends MM_Entity
{	
	public static $YES = "YES";
	public static $NO = "NO";
	
	public static $STATUS_APPROVED = 2;
	public static $STATUS_VOID_REFUND = 3;
	public static $STATUS_HOLD = 4;
	public static $STATUS_DECLINED = 7;
	public static $STATUS_APPROVED_SHIPPED = 8;
	public static $STATUS_PARTIAL_REFUND = 6;
	
	private $customer = null;
	private $campaignId = 0;
	private $productId = 0;
	private $ancestorId = 0;
	private $parentId = 0;
	private $childIds = "";
	private $status = "";
	private $isRecurring = 0;
	private $recurringDate = "";  // next recurring date YYYY-MM-DD
	
	private $product = null;
	
	private $paymentMethod = "";
	private $ccNumber = "";
	private $expMonth = "";
	private $expYear = "";
	private $ccSecurityCode = "";
	private $orderTotal = 0;
	
	private $firstName = "";
	private $lastName = "";
	private $phone = "";
	private $timeStamp = "";
	
	private $billingAddress = "";
	private $billingCity = "";
	private $billingState = "";
	private $billingZip = "";
	private $billingCountry = "";
	private $shippingAddress = "";
	private $shippingCity = "";
	private $shippingState = "";
	private $shippingZip = "";
	private $shippingCountry = "";
	private $paymentOptionId = "";
	
	private $shippingMethod = "";
	private $billingSameAsShipping = "YES";
	
 	// TODO Eventually MM will support upsells during the registration process. 
 	// Right now it's not allowed so upsellCount is hard-coded at 0 and no 
 	// methods exist to set these properties
	private $upsellCount = 0; 
	private $upsellIds = "";
	
	
	public function getData() 
	{
		$result = MM_LimeLightService::getOrder($this->getId());
		
		if($result instanceof MM_Response) {
			parent::invalidate();
		}
		else
		{
			$obj = new stdClass();
			
			foreach($result as $k=>$v) {
				$obj->$k = $v;
			}
			
			$this->setData($obj);
		}
	}
	
	public function setData($data)
	{
		global $mmSite;
		
		try 
		{
			$this->productId = $data->main_product_id;
			$this->ancestorId = $data->ancestor_id;
			$this->parentId = $data->parent_id;
			$this->childIds = $data->child_id;
			$this->status = $data->order_status;
			$this->isRecurring = $data->is_recurring;
			$this->recurringDate = $data->recurring_date;
			$this->paymentMethod = "";
			$this->ccNumber = $data->cc_number;
			$this->ccSecurityCode = "";
			
			$this->campaignId = (isset($data->campaign_id))?$data->campaign_id:MM_Session::value(MM_Session::$KEY_LAST_CAMPAIGN_ID);
			$this->product = new MM_Product();
			$this->product->getProductByCampaign($this->productId, $this->campaignId);
			
			
			$this->shippingMethod = $data->shipping_id;
			$this->expMonth = substr($data->cc_expires,0,2);
			$this->expYear = substr($data->cc_expires,2);
			$this->orderTotal = $data->order_total;
			$this->phone = $data->customers_telephone;
			$this->billingAddress = $data->billing_street_address;
			$this->billingCity = $data->billing_city;
			$this->billingState = $data->billing_state;
			$this->billingZip = $data->billing_postcode;
			$this->billingCountry = $data->billing_country;
			$this->shippingAddress = $data->shipping_street_address;
			$this->shippingCity = $data->shipping_city;
			$this->shippingState = $data->shipping_state;
			$this->shippingZip = $data->shipping_postcode;
			$this->shippingCountry = $data->shipping_country;
			$this->firstName = $data->first_name;
			$this->lastName = $data->last_name;
			$this->timeStamp = $data->time_stamp;
			$this->billingSameAsShipping = "";
			$this->paymentOptionId = "0";
			parent::validate();
		}
		catch (Exception $ex)
		{
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		//$response = MM_LimeLightService::placeOrder($this);
		$paymentEngine = new MM_PaymentEngine($this->paymentOptionId);
		$response = $paymentEngine->placeOrder($this);
		LogMe::write("Order::commitData : response: ".json_encode($this)." ::: ".json_encode($response));
		
		if($response instanceof MM_Response) {
			if($response->type != MM_Response::$ERROR){
				$this->customer->setStatus(MM_MemberStatus::$LOCKED);
				$this->customer->commitData();
			}
			return $response;
		}
		else {
			$this->customer->setCustomerId($response->customerId);
			$this->customer->setMainOrderId($response->orderID);
			
			return $this->customer->commitData();
		}
	}
	
	public function getLastRebillId()
	{
		
		if(isset($this->childIds) && $this->childIds != "")
		{
			if(strpos($this->childIds, ",") !== false) {
				$ids = explode(",", $this->childIds);
				return $ids[count($ids)-1];
			}
			else {
				return $this->childIds;
			}
		}
		
		return $this->id;
	}
	
	public function getPaymentOption(){
		return $this->paymentOptionId;
	}
	
	public function setPaymentOption($str){
		$this->paymentOptionId = $str;
	}
	
	public function isRecurring()
	{
		if(intval($this->isRecurring) == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function setProduct(MM_Product $product)
	{
		$this->product = $product;
	}
	
	public function getProduct()
	{
		if($this->product == null) {
			$this->product = new MM_Product($this->productId);
		}
		
		return $this->product;
	}
	
	public function setCustomer(MM_User $customer) 
	{
		$this->customer = $customer;
	}
	
	public function getCustomer()
	{
		if($this->customer == null) {
			global $wpdb;
 		
	 		$sql = "select * from {$wpdb->users} where mm_main_order_id='{$this->id}' limit 1";
	 		$row = $wpdb->get_row($sql);
	 		
	 		if(!$row) {
	 			return false;
	 		}
	 		
	 		$user = new MM_User($row->ID, false);
	 		$user->setData($row);
	 		
	 		$this->customer = $user;
		}
		
		return $this->customer;
	}
	
	public function getDateTime()
	{
		return Date("m/d/Y h:i a", strtotime($this->timeStamp));
	}
	
	public function getNextRebillDate()
	{
		return Date("Y-m-d", strtotime($this->recurringDate));
	}
	
	public function getProductName()
	{
		if($this->product instanceof MM_Product){
			return $this->product->getName();	
		}
		return "";
	}
	
	public function getProductSKU()
	{
		return $this->product->getSku();
	}
	
 	public function setBillingAddress($str)
 	{
 	 	$this->billingAddress = $str;
 	}
 	 
 	public function getBillingAddress()
 	{
 	 	return $this->billingAddress;
 	}
 	 
 	public function setBillingCity($str)
 	{
 	 	$this->billingCity= $str;
 	}
 	 
 	public function getBillingCity()
  	{
 	 	return $this->billingCity;
 	}
 	 
 	public function setBillingState($str)
 	{
 	 	$this->billingState = $str;
 	}
 	 
 	public function getBillingState()
 	{
 	 	return $this->billingState;
 	}
 	 
 	public function setBillingZipCode($str)
 	{
 	 	$this->billingZip = $str;
 	}
 	 
  	public function getBillingZipCode()
 	{
 	 	return $this->billingZip;
 	}
 	 
 	public function setBillingCountry($str)
 	{
 	 	$this->billingCountry = $str;
 	}
 	 
 	public function getBillingCountry()
 	{
 	 	return $this->billingCountry;
 	}
 	 
 	public function getBillingCountryName()
 	{	
 	 	return MM_LimeLightUtils::getCountryName($this->billingCountry);
 	}
 	 
 	public function setShippingAddress($str)
 	{
 	 	$this->shippingAddress = $str;
 	}
 	 
 	public function getShippingAddress()
 	{
 	 	return $this->shippingAddress;
 	}
 	 
 	public function setShippingCity($str)
 	{
 	 	$this->shippingCity= $str;
 	}
 	 
 	public function getShippingCity()
 	{
 	 	return $this->shippingCity;
 	}
 	 
 	public function setShippingState($str)
 	{
 	 	$this->shippingState = $str;
 	}
 	 
 	public function getShippingState()
 	{
 	 	return $this->shippingState;
 	}
 	 
 	public function setShippingZipCode($str)
 	{
 	 	$this->shippingZip = $str;
 	}
 	 
 	public function getShippingZipCode()
 	{
 	 	return $this->shippingZip;
 	}
 	 
 	public function setShippingCountry($code)
 	{
 	 	$this->shippingCountry = $code;
 	}
 	 
 	public function getShippingCountry()
 	{
 	 	return $this->shippingCountry;
 	}
 	
 	public function getShippingCountryName()
 	{
 	 	return MM_LimeLightUtils::getCountryName($this->shippingCountry);
 	}
 	
	public function setTotal($total)
	{
		$this->orderTotal = $total;
	}
	
	public function getTotal()
	{
		return $this->orderTotal;
	}
	
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}
	
	public function getPhone()
	{
		return $this->phone;
	}
	
 	public function setFirstName($str)
 	{
 	 	$this->firstName = $str;
 	}
 	 
  	public function getFirstName()
 	{
 		return $this->firstName;
 	}
 	 
 	public function setLastName($str)
 	{
 	 	$this->lastName = $str;
 	}
 	 
 	public function getLastName()
 	{
 		return $this->lastName;
 	}
 	
	public function setCampaignId($str) 
	{
		$this->campaignId = $str;
	}
	
	public function getCampaignId()
	{
		return $this->campaignId;
	}
 	
	public function setProductId($str) 
	{
		$this->productId = $str;
	}
	
	public function getProuctId()
	{
		return $this->productId;
	}
	
	public function getUpsellCount()
	{
		return $this->upsellCount;
	}
	
	public function getUpsellIds()
	{
		return $this->upsellIds;
	}
 	
	public function setPaymentMethod($str) 
	{
		$this->paymentMethod = $str;
	}
	
	public function getPaymentMethod()
	{
		return $this->paymentMethod;
	}
	
	public function setCCNumber($str) 
	{
		$this->ccNumber = $str;
	}
	
	public function getCCNumber()
	{
		return $this->ccNumber;
	}
	
	public function setExpMonth($str) 
	{
		$this->expMonth = $str;
	}
	
	public function getExpMonth()
	{
		return $this->expMonth;
	}
	
	public function setExpYear($str) 
	{
		$this->expYear = $str;
	}
	
	public function getExpYear()
	{
		return $this->expYear;
	}
	
	public function getExpDate()
	{
		return $this->expMonth.$this->expYear;
	}
	
	public function setSecurityCode($str) 
	{
		$this->ccSecurityCode = $str;
	}
	
	public function getSecurityCode()
	{
		return $this->ccSecurityCode;
	}
	
	public function setShippingMethod($str) 
	{
		$this->shippingMethod = $str;
	}
	
	public function getShippingMethod()
	{
		return $this->shippingMethod;
	}
	
	public function setBillingSameAsShipping($str)
	{
		if($str == self::$YES || $str == self::$NO) {
			$this->billingSameAsShipping = $str;
		}
		else {
			$this->billingSameAsShipping = self::$YES;
		}
	}
	
	public function getBillingSameAsShipping()
	{
		return $this->billingSameAsShipping;
	}
	
}
?>
