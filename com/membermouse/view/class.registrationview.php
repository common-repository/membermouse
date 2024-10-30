	<?php
	/**
	 * 
	 * 
	MemberMouse(TM) (http://www.membermouse.com)
	(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
	 */
	class MM_RegistrationView extends MM_View
	{
		public function performAction($post) 
		{	
			$response = parent::performAction($post);
			
			if(!($response instanceof MM_Response))
			{
				switch($post[self::$MM_JSACTION]) 
				{
					case self::$MM_JSACTION_GATEWAY:
						return $this->getGateway($post);
					
					case self::$MM_JSACTION_NEXT_STEP:
						return $this->getNextDialog($post);
	
					case self::$MM_JSACTION_PREV_STEP:
						return $this->getPrevDialog($post);
						
					case self::$MM_JSACTION_PLACE_NEW_ORDER:
						return $this->createMember($post);
					default:
						return new MM_Response($response);
				}
			}
			else 
			{
				return $response;
			}
		}
		
		public function getGatewayInformation($gatewayId){
		
			$gateway = new MM_CampaignOptions($gatewayId);
			if(!$gateway->isValid()){
				return false;
			}
			
			$attr = $gateway->getAttr();
			$options = json_decode($attr);
			if(is_null($options)){
				return false;
			}
			return $options;
		}
		
		private function getGateway($post){
			if(!isset($post["gateway_id"])){
				return new MM_Response("Payment ID is not found", MM_Response::$ERROR);
			}
			$options = $this->getGatewayInformation($post["gateway_id"]);
			if($options === false){
				return new MM_Response("Invalid payment option", MM_Response::$ERROR);
			}
			return new MM_Response($options);
		}
		
		public static function getHiddenStepsHtml($currentStepKey){
	
			$stepsArr = array(
				'mm_step'=>$currentStepKey,
				'mm_next_step'=>'step2',
				'mm_prev_step'=>'',
			);
			switch($currentStepKey){
				case "step2":
					$stepsArr['mm_next_step'] = (MM_CustomField::hasCustomFields(true))?'additional':'step3';
					$stepsArr['mm_prev_step'] = 'step1';
					break;
				case "additional":
					$stepsArr['mm_next_step'] = 'step3';
					$stepsArr['mm_prev_step'] = 'step2';
					break;
				case "step3":
					$stepsArr['mm_next_step'] = 'step4';
					$stepsArr['mm_prev_step'] = (MM_CustomField::hasCustomFields(true))?'additional':'step2';
					break;
				case "step4":
					$stepsArr['mm_next_step'] = 'done';
					$stepsArr['mm_prev_step'] = 'step3';
					break;
			}
			$html = "";
			foreach($stepsArr as $stepKey=>$stepVal){
				$html.= "<input type='hidden' id='{$stepKey}' value='{$stepVal}' />";
			}
			return $html;
		}
		
		public function createMember($post)
		{
	 		global $current_user,$mmSite;
	 		$user = new MM_User($current_user->ID);
	 		$ret = null;
	 	
			$data = $this->getDataInSession();
			LogMe::write("createMember() : ".json_encode($data));
						LogMe::write("createMember() : POST : ".json_encode($post));
		
			if(!isset($data["mm_order_payment_method"]) && isset($post["mm_order_payment_choice"])){
				$post["mm_order_payment_method"] = (isset($post["mm_order_payment_choice"]))?$post["mm_order_payment_choice"]:"0";
			}

			
			if(isset($data["mm_order_product_id"])){
	 	
	 			if($user->isValid()){
	 	
					if(isset($data["mm_order_no_association"])){
	 	
						$order = MM_TransactionEngine::placeNewOrder($post, $current_user->ID);
						if($order instanceof MM_Response) {
	 	
							return $order;
						}
						$ret = new MM_Response();
	 	
					}
					else if(isset($data["mm_order_access_tag_id"])){	
						$accessTag = new MM_AccessTag($data["mm_order_access_tag_id"]);
						if($accessTag->isValid()){
							$order = MM_TransactionEngine::placeNewOrder($post, $current_user->ID);
							if($order instanceof MM_Response) {
								return $order;
							}
							else if(intval($order->orderID)>0){
								$response = $user->addAccessTag($accessTag->getId(), $order->orderID);
								if($response->type == MM_Response::$ERROR){
									return $response;
								}
								$ret = new MM_Response();
							}
							else{
								$ret = new MM_Response("Could not find valid order id.", MM_Response::$ERROR);
							}
						}
						else{
							$ret = new MM_Response("Could not find valid access tag id.", MM_Response::$ERROR);
						}
					}
					else{
						$ret = MM_TransactionEngine::placeNewOrder($post, $current_user->ID);
					}
	 			}
	 			else{
	 				$ret = new MM_Response("Session has timed out please log back in to purchase this product.", MM_Response::$ERROR);
	 			}
			}
			else{
	 	
				$ret = MM_TransactionEngine::placeNewOrder($post);
			}
			
			if(is_null($ret)){
	 	
				return new MM_Response("Could not place the order.", MM_Response::$ERROR);
			}
			
	 	
			LogMe::write("createMember() : ".json_encode($ret));
		
			if($ret instanceof MM_Response)
			{
			LogMe::write("RegistrationView::CreateMember : ".json_encode($ret));
				if(isset($ret->message["url"]) || isset($ret->message->url)){
			LogMe::write("RegistrationView::CreateMember : returning with URL ");
					return $ret;
				}
				
				if($ret->type == MM_Response::$SUCCESS)
				{
					
					if(isset($data["mm_order_product_id"])){
		 				if(isset($data["mm_order_no_association"])){
					        $params = array();
					        $params["isFree"] = 0;
					        $params["refType"] =MM_TYPE_PRODUCT;
					        $params["refId"] = $data["mm_order_product_id"];
				        
							$cpe = new MM_CorePageEngine();
							
							$url = $cpe->getUrl(MM_CorePageType::$PAID_CONFIRMATION, $params);
							
							if(strlen($url)>0)
							{
								MM_Session::clear(MM_Session::$KEY_REGISTRATION);
							}
							$ret->message = $url;
		 				}
		 				else if(isset($data["mm_order_access_tag_id"])){
	 	
							$at = new MM_AccessTag($data["mm_order_access_tag_id"]);
							
					        $params = array();
					        $params["isFree"] = $at->isFree();
					        $params["refType"] =MM_TYPE_PRODUCT;
					        $params["refId"] = $data["mm_order_product_id"];
					        if($at->isFree()){
						        $params["refType"] =MM_TYPE_ACCESS_TAG;
						        $params["refId"] = $at->getId();
					        }
				        
							$cpe = new MM_CorePageEngine();
							$url = $cpe->getUrl(MM_CorePageType::$PAID_CONFIRMATION, $params);
							
							if(strlen($url)>0)
							{
								MM_Session::clear(MM_Session::$KEY_REGISTRATION);
							}
							
							$ret->message = $url;
		 				}
			 			else{
	 	
							$mt = new MM_MemberType($data["mm_order_member_type"]);
							
					        $params = array();
					        $params["isFree"] = $mt->isFree();
					        $refType = MM_TYPE_PRODUCT;
					        $typeId = $mt->getRegistrationProduct();
					        if((bool)$params["isFree"]){
					        	$refType = 	MM_TYPE_MEMBER_TYPE;
					        	$typeId= $mt->getId();
					        }
					        $params["refType"] =$refType;
					        $params["refId"] = $typeId;
				        
							$cpe = new MM_CorePageEngine();
							$url = $cpe->getUrl(MM_CorePageType::$PAID_CONFIRMATION, $params);
							
							if(strlen($url)>0)
							{
								MM_Session::clear(MM_Session::$KEY_REGISTRATION);
							}
							
							$ret->message = $url;
			 			}
					}
					else{
	 	
						$mt = new MM_MemberType($data["mm_order_member_type"]);
						
				        $params = array();
				        $params["isFree"] = $mt->isFree();
				        $refType = MM_TYPE_PRODUCT;
				        $typeId = $mt->getRegistrationProduct();
				        if((bool)$params["isFree"]){
				        	$refType = 	MM_TYPE_MEMBER_TYPE;
				        	$typeId= $mt->getId();
				        }
				        $params["refType"] =$refType;
				        $params["refId"] = $typeId;
				        
						$cpe = new MM_CorePageEngine();
						$url = $cpe->getUrl(MM_CorePageType::$PAID_CONFIRMATION, $params);
						
						if(strlen($url)>0)
						{
							MM_Session::clear(MM_Session::$KEY_REGISTRATION);
						}
						$ret->message = $url;
					}
				}
			}
			else{
				return new MM_Response("Could not find a valid response", MM_Response::$ERROR);
			}
			return (($ret instanceof MM_Response)?$ret:new MM_Response(json_encode($ret), MM_Response::$ERROR));
		}
		
		public function getNextDialog($post=null)
		{
			$this->saveDataInSession($post);
			$step = (isset($post["step"]))?$post["step"]:'step1';
	 		return MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.{$step}.php", array());
		}
		
		public function getPrevDialog($post=null)
		{
			$this->saveDataInSession($post);
			$step = (isset($post["step"]))?$post["step"]:'step1';
	 		return MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.{$step}.php", array());
		}
		
		public function generateRegistrationHtml()
		{
			$step ='step1';
			$info =new stdClass();
	 		$info->step = $step;
	 		$info->free_reg = (isset($post["free_reg"]))?$post["free_reg"]:0;
	 		if(isset($_GET["member_type_id"]))
	 		{
	 			$mt = new MM_MemberType($_GET["member_type_id"]);
	 			if($mt->getIncludeOnReg())
	 			{
	 				$info->step='step2';
	 				$post = array();
	 				$post["mm_order_member_type"] = $_GET["member_type_id"];
	 				$this->saveDataInSession($post);
	 				return MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.php", $info);
	 			}
	 		}
	 		else if(isset($_GET["product_id"])){
	 			global $current_user;
	 			$user = new MM_User($current_user->ID);
	 			if($user->isValid()){
		 			$product = new MM_Product($_GET["product_id"]);
		 			if($product->isValid()){
		 				$accessTagObj = $product->getAssociatedAccessTag();
		 				$memberTypeObj = $product->getAssociatedMemberType();
		 				$memberTypeId = 0;
		 				$accessTagId = 0;
		 				
		 				if(isset($accessTagObj->id)){
		 					$accessTagId = $accessTagObj->id;
		 				}
		 				if(isset($memberTypeObj->id)){
		 					$memberTypeId = $memberTypeObj->id;
		 				}
		 				
		 				if(intval($accessTagId)>0){
			 				$at = new MM_AccessTag($accessTagId);
				 			$info->step='step3';
				 			$post = array();
				 			$post["mm_order_username"] = $user->getUsername();
				 			$post["mm_order_email"] = $user->getEmail();
				 			$post["mm_order_email_confirm"] = $user->getEmail();
				 			$post["mm_order_password"] = $user->getPassword();
				 			$post["mm_order_password_confirm"] = $user->getPassword(); 
				 			
			 				if($at->isValid()){
					 			$post["mm_order_member_type"] = $user->getMemberTypeId();
					 			$post["mm_order_product_id"] = $_GET["product_id"];
					 			$post["mm_order_access_tag_id"] = $at->getId();
			 				}
			 				else{
					 			$post["mm_order_member_type"] = $user->getMemberTypeId();
					 			$post["mm_order_product_id"] = $_GET["product_id"];
					 			$post["mm_order_no_association"] = 1;
			 				}
					 		$this->saveDataInSession($post);
					 		return MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.php", $info);
		 				}
		 				else if(intval($memberTypeId)>0){
			 				$mt = new MM_MemberType($memberTypeId);
				 			$info->step='step3';
				 			$post = array();
				 			$post["mm_order_username"] = $user->getUsername();
				 			$post["mm_order_email"] = $user->getEmail();
				 			$post["mm_order_email_confirm"] = $user->getEmail();
				 			$post["mm_order_password"] = $user->getPassword();
				 			$post["mm_order_password_confirm"] = $user->getPassword(); 
			 				if($mt->isValid()){
					 			$post["mm_order_member_type"] = $memberTypeId;
					 			$post["mm_order_product_id"] = $_GET["product_id"];
			 				}
			 				else{
					 			$post["mm_order_member_type"] = $user->getMemberTypeId();
					 			$post["mm_order_product_id"] = $_GET["product_id"];
					 			$post["mm_order_no_association"] = 1;
			 				}
				 			$this->saveDataInSession($post);
				 			return MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.php", $info);
		 				}
		 				else{
				 			$info->step='step3';
				 			$post = array();
				 			$post["mm_order_username"] = $user->getUsername();
				 			$post["mm_order_email"] = $user->getEmail();
				 			$post["mm_order_email_confirm"] = $user->getEmail();
				 			$post["mm_order_password"] = $user->getPassword();
				 			$post["mm_order_password_confirm"] = $user->getPassword(); 
					 		$post["mm_order_member_type"] = $user->getMemberTypeId();
			 				$post["mm_order_product_id"] = $_GET["product_id"];
			 				$post["mm_order_no_association"] = 1;
			 				$this->saveDataInSession($post);
				 			return MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.php", $info);
		 				}
		 			}
		 			else{
						$redirectUrl =  MM_CorePageEngine::getUrl(MM_CorePageType::$ERROR);
						$redirectUrl = MM_Utils::appendUrlParam($redirectUrl, "message", "Invalid product id.");
		 				$info->redirectUrl = $redirectUrl;
		 			}
	 			}
	 		}
	 		return MM_TEMPLATE::generate(MM_MODULES."/registrationprocess.php", $info);
		}
		
		public function getData($step, $post=null)
		{
			return call_user_func_array(array($this, "get".ucfirst($step)), array($post));
		}
		
		public function getDataInSession()
		{
			$data = MM_Session::value(MM_Session::$KEY_REGISTRATION, null);
			if(is_serialized(MM_Session::value(MM_Session::$KEY_REGISTRATION)))
			{
				$data = unserialize($data);
			}
			return $data;
		}
		
		private function saveDataInSession($post)
		{
			$data = $this->getDataInSession();
			if(is_null($data)){
				$data = array();
			}
			LogMe::write("saveDataInSession: ".json_encode($post));
			foreach($post as $k=>$v)
			{
				if(preg_match("/(mm_order)/", $k)){
					$data[$k] = $v;	
				}
				else if(preg_match("/(mm_custom_)/", $k)){
					$data[$k] = $v;	
				}
				if(preg_match("/(mm_order_billing_)/", $k)){
					$key  =preg_replace("/(_billing_)/","_shipping_", $k);
					if(!isset($data[$key])){
						$data[$key] = $v;
					}	
				}
			}
			MM_Session::value(MM_Session::$KEY_REGISTRATION, serialize($data));
		}
	
		private function getStep4($post)
		{
			return $this->getStep3($post);
		}
		
		private function getStep3($post)
		{
			$data = $this->getDataInSession();
			
			if(!is_null($data) && isset($data["mm_order_billing_address"]))
			{
				$user = new MM_User();
				$user->setBillingAddress($data["mm_order_billing_address"]);	
				$user->setBillingCity($data["mm_order_billing_city"]);	
				$user->setBillingState($data["mm_order_billing_state"]);	
				$user->setBillingZipCode($data["mm_order_billing_zip"]);	
				$user->setBillingCountry($data["mm_order_billing_country"]);
				
				$user->setShippingAddress($data["mm_order_shipping_address"]);	
				$user->setShippingCity($data["mm_order_shipping_city"]);	
				$user->setShippingState($data["mm_order_shipping_state"]);	
				$user->setShippingZipCode($data["mm_order_shipping_zip"]);		
				$user->setShippingCountry($data["mm_order_shipping_country"]);
				
				$user->setFirstName($data["mm_order_first_name"]);	
				$user->setLastName($data["mm_order_last_name"]);	
				$user->setPhone(preg_replace("/[^0-9]+/", "", $data["mm_order_phone"]));	
				return $user;
			}
			return new MM_User();
		}
		
		private function getStep2($post)
		{
			$data = $this->getDataInSession();
			
			if(!is_null($data) && isset($data["mm_order_email"]))
			{
				$user = new MM_User();
				$user->setEmail($data["mm_order_email"]);	
				$user->setPassword($data["mm_order_password"]);	
				$user->setUsername($data["mm_order_username"]);	
				return $user;
			}
			return new MM_User();
		}
		
		public function getStep1()
		{	
	 		$data = array();
	 		$types = $this->getAllTypes();
	 		
	 		if(is_array($types))
	 		{
	 			foreach($types as $type) 
	 			{
	 				$tr = new stdClass();
	 				$tr->checked = "";
	 				$tr->id = $type->getId();
	 				$tr->name = $type->getName();
	 				$tr->description = $type->getDescription();
	 				$data[] = $tr;
	 			}
	 		}
	 		return $data;
		}
		
		private function getAllTypes()
		{
			global $wpdb;
			
			$sql = "select * from ".MM_TABLE_MEMBER_TYPES." mt where status='1' and include_on_reg='1'";
			$rows = $wpdb->get_results($sql);
			if(is_array($rows))
			{
				foreach($rows as &$row)
				{
					$mt = new MM_MemberType($row->id, false);
					$mt->setData($row);
					$row = $mt;
				}
			}
			return $rows;
		}
	}
	?>