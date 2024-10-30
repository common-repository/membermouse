<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_ProductView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveProduct($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeProduct($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function removeProduct($post){
		if(!isset($post["id"])){
			return new MM_Response("ID is required", MM_Response::$ERROR);
		}
		$product = new MM_Product($post["id"]);
		if($product->remove()){
			return new MM_Response();
		}
		return new MM_Response("Product could not be removed due to existing associations", MM_Response::$ERROR);
	}
	
	private function saveProduct($post){
		$req = array(
			'name','sku','price', 'status','is_shippable',
			'is_recurring','is_trial','trial_amount','trial_duration',
			'description','rebill_period','rebill_frequency','trial_frequency'
			);
		foreach($req as $field){
			if(!isset($post[$field])){
				return new MM_Response($field." is required", MM_Response::$ERROR);
			}
		}
		
		if(!preg_match("/^[0-9\.]+$/", $post["price"])){
			return new MM_Response("Price must be a valid number", MM_Response::$ERROR);
		}
		
		if($post["trial_amount"]!="" && !preg_match("/^[0-9\.]+$/", $post["trial_amount"])){
			return new MM_Response("Trial amount must be a valid number", MM_Response::$ERROR);
		}
		
		if($post["trial_duration"]!="" && !preg_match("/^[0-9]+$/", $post["trial_duration"])){
			return new MM_Response("Trial duration must be a valid number", MM_Response::$ERROR);
		}
		
		if($post["rebill_period"]!="" && !preg_match("/^[0-9]+$/", $post["rebill_period"])){
			return new MM_Response("Rebill preiod must be a valid number", MM_Response::$ERROR);
		}
		
		$paymentId = "";
		if(isset($post["mm_clickbank_payment_method"])){
			$paymentId = $post["mm_clickbank_payment_method"];
		}
		
		if($post["is_recurring_val"] != "1"){
			$post["rebill_period"] = "0";
			$post["rebill_frequency"] = "";
		}
		
		if($post["mm_is_clickbank_val"]=="0"){
			$post["product_id"] = "0";
			$paymentId = 0;
		}
		
		$product = new MM_Product();
		$product->isLL = false;
		if(isset($post["id"]) && intval($post["id"])>0){
			$product->setId($post["id"]);
		}
		$product->setName($post["name"]);
		$product->setSku($post["sku"]);
		$product->setIsShippable($post["is_shippable_val"]);
		$product->setIsTrial($post["is_trial_val"]);
		$product->setTrialAmount($post["trial_amount"]);
		$product->setTrialDuration($post["trial_duration"]);
		$product->setDescription($post["description"]);
		$product->setPrice($post["price"]);
		$product->setProductId($post["product_id"]);
		$product->setRebillPeriod($post["rebill_period"]);
		$product->setRebillFrequency($post["rebill_frequency"]);
		$product->setTrialFrequency($post["trial_frequency"]);
		$product->setPaymentId($paymentId);
		$product->commitData();
		return new MM_Response();	
	}
	
	public function getData(MM_DataGrid $dg)
	{
		global $wpdb;
		
		$rows = parent::getData(MM_TABLE_PRODUCTS, null, $dg);
		
		return $rows;
	}
	
}
?>
