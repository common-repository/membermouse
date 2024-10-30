<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_OneClickView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_ONECLICK_PURCHASE:
					return $this->purchase($post);
				case self::$MM_JSACTION_ONECLICK_DIALOG:
					return $this->showOneClick($post);
				case self::$MM_JSACTION_ONECLICK_RESPONSE:
					return $this->alertResponse($post);
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function purchase($post){
		
		if(!isset($post["product_id"])){
			return new MM_Response("Could not find existing product id.", MM_Response::$ERROR);
		}
		if(!isset($post["mm_id"])){
			return new MM_Response("Could not find existing user id.", MM_Response::$ERROR);
		}
		
		$paymentMethodId = MM_PaymentService::getDefaultPaymentMethodId();
		if(isset($post["payment_method"])){
			$paymentMethodId = MM_PaymentService::getDefaultPaymentMethodId($post["payment_method"]);
		}
		
		if($paymentMethodId instanceof MM_Response){
			return $paymentMethodId;	
		}
		
		$productId = $post["product_id"];
		$userId = $post["mm_id"];
		
		$product = new MM_Product($productId);
		$tag = $product->getAssociatedAccessTag();
		if(!isset($tag->id) || (isset($tag->id) && intval($tag->id)<=0)){
			// Purchase the product
			$user = new MM_User($userId);
			
			$paymentEngine = new MM_PaymentEngine($paymentMethodId);
			$orderId = $paymentEngine->purchaseProduct($user,$productId,false, $paymentMethodId, $this->getShippingMethodId());
			if($orderId instanceof MM_Response) {
				if($orderId->type == MM_Response::$ERROR){
					return $orderId;
				}
			}
			
			$history = new MM_OrderHistory();
			if(!($orderId instanceof MM_Response)){
				$history->setId($orderId);
			}
			$history->setOrderDate(Date("Y-m-d h:i:s"));
			$history->setUserId($userId);
			$history->setProductId($productId);
			$history->commitData();
			
			if($orderId instanceof MM_Response){
				return new MM_Response(array("gateway"=>$orderId->message));
			}
			return new MM_Response("Product \"".$product->getName()."\" was purchased successfully.");
		}
		else{
			$accessTag = new MM_AccessTag($tag->id);
			$user = new MM_User($userId);
			if($user->isValid()){
				// Purchase Access Tag
				
				$paymentEngine = new MM_PaymentEngine($paymentMethodId);
				$orderId = $paymentEngine->purchaseProduct($user,$productId, false, $paymentMethodId,$this->getShippingMethodId());
				
				if($orderId instanceof MM_Response) {
					if($orderId->type == MM_Response::$ERROR){
						return $orderId;
					}
					else{
						return new MM_Response(array("gateway"=>$orderId->message));
					}
				}
				
				$result = $user->addAccessTag($accessTag->getId(), $orderId, $productId);
				if($result->type == MM_Response::$ERROR) {
					return $result;
				}
			
				$history = new MM_OrderHistory();
				$history->setId($orderId);
				$history->setOrderDate(Date("Y-m-d h:i:s"));
				$history->setUserId($userId);
				$history->setProductId($productId);
				$history->commitData();
					
				$affiliateId = MM_RetentionReport::getAffiliateCookie(MM_OPTION_TERMS_AFFILIATE);
				$subAffiliateId = MM_RetentionReport::getAffiliateCookie(MM_OPTION_TERMS_SUB_AFFILIATE);
				
				$retentionReport = new MM_RetentionReport();
				$retentionReport->setAffiliateId($affiliateId);
				$retentionReport->setSubAffiliateId($subAffiliateId);
				$retentionReport->setOrderId($orderId);
				$retentionReport->setUserId($userId);
				$retentionReport->setProductId($productId);
				$retentionReport->setRefId($accessTag->getId());
				$retentionReport->setRefType(MM_TYPE_ACCESS_TAG);
				$retentionReport->commitData();
			
				if($orderId instanceof MM_Response){
					return new MM_Response(array("gateway"=>$orderId->message));
				}
				
		        $params = array();
		        
		        $params["isFree"] = false;
		        $params["refType"] = MM_TYPE_PRODUCT;
		        $params["refId"] = $product->getId();
		        
		        LogMe::write("purchase() : ".json_encode($params));
		        
				$cpe = new MM_CorePageEngine();
				$url = $cpe->getUrl(MM_CorePageType::$PAID_CONFIRMATION, $params);
				return new MM_Response(array('url'=>$url));
				
			}
			
		}
	}
	
	
	private function alertResponse($post)
	{
		$info->message = (!isset($post["message"]))?"Oh, something bad happened":$post["message"];
 		$msg = MM_TEMPLATE::generate(MM_MODULES."/one_click_response.php", $info);
 		return new MM_Response($msg);
	}
	
	private function constructErrorMessage($error){
		global $current_user;
		$emailAccount = MM_EmailAccount::getDefaultAccount();
		$user = new MM_User($current_user->ID);
		$context = new MM_Context($user, $emailAccount);
		$content = MM_SmartTagEngine::processContent($error, $context);
		return $content;
	}
	
	private function getShippingMethodId(){
		$shippingMethods = MM_CampaignOptions::getOptionRow("shipping",false);
		if(count($shippingMethods)>0){
			foreach($shippingMethods as $id=>$row){
				if($row->attr>0){
					return $id;
				}
			}
		}
		return false;
	}
	
	private function getPaypalPaymentMethod(){
		$paymentMethods = MM_CampaignOptions::getOptionRow("payment",false);
		if(count($paymentMethods)>0){
			foreach($paymentMethods as $id=>$row){
				$row->attr = json_decode($row->attr);
				if($row->attr->hidden_paymentObject == "MM_PaypalService"){
					return $row;
				}
			}
		}
		return false;
	}
	
	private function showOneClick($post)
	{	
		global $current_user;
		
		// no go for preview mode
		$response = new MM_Response();
		if(MM_Utils::isAdmin()){
			$response = new MM_Response("You cannot 1-click buy in administration mode.", MM_Response::$ERROR);
		}
		else{
			$user = new MM_User($current_user->ID);
			if($user->isValid()){
				/*
				 * This site is not configured to support one click buy.  Please contact support if you have any questions. Email: '.$emailAccount->getAddress()
				 */
			
				$paymentMethodId = MM_PaymentService::getDefaultPaymentMethodId($post["payment_method"]);
				
				// no card on file, sorry.
				if(!$user->hasCardOnFile())
				{
					if($paymentMethodId !==false){
						if(MM_Site::$INSTALL_TYPE_LIMELIGHT == MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INSTALL_TYPE)){
							$redirectUrl =  MM_CorePageEngine::getUrl(MM_CorePageType::$REGISTRATION);
							$redirectUrl = MM_Utils::appendUrlParam($redirectUrl, "product_id", $post["product_id"]);
							$response = new MM_Response(array('url'=>$redirectUrl), MM_Response::$ERROR);
						}
					}
					
				}
				
				if($response->type == MM_Response::$SUCCESS)
				{
					// Does product exist in request?
					if(!isset($post["product_id"]))
					{
						$error = "We were unable to locate the product ID. Please contact customer support at [MM_Email_Address].";
						$response = new MM_Response($this->constructErrorMessage($error), MM_Response::$ERROR);
					}
					// does product exist in MM
					else if(isset($post["product_id"]) && intval($post["product_id"])>=0)
					{
						// check if its valid
						$product = new MM_Product($post["product_id"]);
						if(!$product->isValid())
						{
							$error = "We were unable to locate the product with ID {$post["product_id"]}. Please contact customer support at [MM_Email_Address].";
							$response = new MM_Response($this->constructErrorMessage($error), MM_Response::$ERROR);
						}
						else
						{
							$info->product_id = intval($post["product_id"]);
							$info->price = $product->getPrice(true); 
							$info->payment_method = $post["payment_method"];
							$msg = MM_TEMPLATE::generate(MM_MODULES."/one_click.php", $info);
					 		$response = new MM_Response($msg);
						}
					}
				}
			}
			else{
				$response = new MM_Response("You may have been logged out unexpectedly. Please login to purchase this product.", MM_Response::$ERROR);
			}
		}
		
 		return $response;
	}
	
}