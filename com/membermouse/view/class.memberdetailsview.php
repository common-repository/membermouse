<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_MemberDetailsView extends MM_View
{	
 	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{		
				
				case self::$MM_JSACTION_SEND_PASSWORD:
					return $this->sendPasswordReminder($post);
				
				case self::$MM_JSACTION_DETERMINE_CAMPAIGN:
					return $this->determineCampaign($post);
				
				case self::$MM_JSACTION_UPDATE_MEMBER:
					return $this->updateMember($post);
					
				case self::$MM_JSACTION_LOCK_ACCOUNT:
					return $this->changeMemberStatus($post, MM_MemberStatus::$LOCKED);
					
				case self::$MM_JSACTION_UNLOCK_ACCOUNT:
					return $this->changeMemberStatus($post, MM_MemberStatus::$ACTIVE);
					
				case self::$MM_JSACTION_CANCEL_MEMBERSHIP:
					return $this->cancelMembership($post);
					
				case self::$MM_JSACTION_PAUSE_MEMBERSHIP:
					$membershipView = new MM_MembershipView();
					return $membershipView->pauseMembership($post,true);
					
				case self::$MM_JSACTION_CHANGE_MEMBERSHIP:
					return $this->changeMembership($post);
					
				case self::$MM_JSACTION_ACTIVATE_MEMBERSHIP:
					return $this->activateMembership($post);
					
				case self::$MM_JSACTION_ACTIVATE_ACCESS_TAG:
					return $this->activateAccessTag($post);
					
				case self::$MM_JSACTION_DEACTIVATE_ACCESS_TAG:
					return $this->deactivateAccessTag($post);
					
				case self::$MM_JSACTION_GET_PRODUCT_NAME:
					return $this->getProductName($post);
					
				case self::$MM_JSACTION_ATTACH_ORDER:
					return $this->attachOrder($post);
				
					
				// TODO ERIC remove this functionality once Lime Light is calling updateOrder API call
				case self::$MM_JSACTION_SYNC:
					return $this->syncOrder($post);
					
				case self::$MM_JSACTION_SAVE:
					return $this->activateAccessTag($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function sendPasswordReminder($post){
		if(!isset($post["user_id"])){
			return new MM_Response("Could not find user id.", MM_Response::$ERROR);
		}
		
		$emailAccount = MM_EmailAccount::getDefaultAccount();
		$user = new MM_User($post["user_id"]);
		if(!$user->isValid()){
			return new MM_Response("Invalid user id.", MM_Response::$ERROR);
		}
		
		$context = new MM_Context($user, $emailAccount);
		
		$loginUrl = MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE);
		$emailBody = "Here are your login credentials:
Username: ".$user->getEmail()."
Password: ".$user->getPassword()."

You can login here: ".$loginUrl."

[MM_Email_Name]
[MM_Email_Address]
";
		
		$eMail = new MM_Email();
		$eMail->setBody($emailBody);
		
		$siteUrl = MM_OptionUtils::getOption("siteurl");
		$eMail->setToAddress($user->getEmail());
		$eMail->setToName($user->getFullName());
		$eMail->setSubject("[".$siteUrl."] New Password");
		$eMail->setFromAddress($emailAccount->getAddress());
		$eMail->setFromName($emailAccount->getName());
		$eMail->setContext($context);
		$response = $eMail->send();
		return $response;
	}
	
	private function determineCampaign($post){
		if(!isset($post["mm_membertype_id"])){
			return new MM_Response("Could not find product.", MM_Response::$ERROR);
		}
		$memberType = new MM_MemberType($post["mm_membertype_id"]);
		if($memberType->isValid()){
			$product = new MM_Product($memberType->getRegistrationProduct());
			if($product->isValid()){
				$campaign = new MM_Campaign($product->getCampaignId());
				if($campaign->isValid()){
					$responseArr = array(
									'campaign_name'=>$campaign->getName(),
									'campaign_id'=>$campaign->getId(),
					);
					return new MM_Response($responseArr);
				}
				return new MM_Response("Campaign ID is not valid.", MM_Response::$ERROR);
			}
			return new MM_Response("Product ID is not valid.", MM_Response::$ERROR);
		}
		return new MM_Response("Member Type ID is not valid.", MM_Response::$ERROR);
	}
	
	private function updateMember($post)
	{
		$error = new MM_Response("Error updating member with id '".$post["mm_id"]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_id"])) 
		{
			$user = new MM_User($post["mm_id"]);
			
			if($user->isValid()) 
			{
				$user->setFirstName($post["mm_first_name"]);
				$user->setLastName($post["mm_last_name"]);
				$user->setUsername($post["mm_username"]);
				$user->setEmail($post["mm_email"]);
				$user->setPhone($post["mm_phone"]);
				$user->setNotes($post["mm_notes"]);
				
				$calcMethod = "join_date";
				if(isset($post["mm_calc_method"])){
					$calcMethod = $post["mm_calc_method"];
				}
				$calcValue = "";
				switch($calcMethod){
					case "custom_date":
						$calcValue = Date("Y-m-d", strtotime($post["mm_custom_date"]));
						break;
					case "fixed":
						$calcValue = preg_replace("/[^0-9]+/", "", $post["mm_fixed"]);
						break;
				}
				
				$user->setDaysCalcMethod($calcMethod);
				$user->setDaysCalcValue($calcValue);
				
				if(isset($post["mm_new_password"]) && $post["mm_new_password"] != "") {
					$user->setPassword($post["mm_new_password"]);
				}
				
				if(!MM_Utils::isLimeLightInstall()){
					$user->doUpdateLL = false;
				}
				
				$result = $user->commitData();
				
				if($result->type == MM_Response::$SUCCESS) {
					return new MM_Response("Member '".$user->getUsername()."' updated successfully");
				}
				else {
					return $result;
				}
			}
			else {
				return $error;
			}
		}
		else {
			return $error;
		}
	}
	
	private function getProductName($post)
	{	
		$error = new MM_Response("<i>no member type selected</i>", MM_Response::$ERROR);
		
		if(isset($post["mm_new_membership_paid_selection"])) 
		{
			$memberType = new MM_MemberType($post["mm_new_membership_paid_selection"]);
			
			if($memberType->isValid())
			{
				$product = new MM_Product($memberType->getRegistrationProduct());
				
				if($product->isValid())
				{
					return new MM_Response($product->getName());
				}
			}
		}
		
		return $error;
	}
	
	private function attachOrder($post)
	{	
		$error = new MM_Response("Error attaching order to member.\n\nPlease ensure that '".$post["mm_attach_order_id"]."' is a valid order ID", MM_Response::$ERROR);
		
		if(isset($post["mm_id"]) && isset($post["mm_attach_order_id"])) 
		{
			// validate order ID
			$result = MM_LimeLightService::getOrder($post["mm_attach_order_id"]);
			
			if($result instanceof MM_Response) {
				return $error;
			}
			
			// ensure that order ID is not associated with another member
			$order = new MM_Order();
			$order->setId($post["mm_attach_order_id"]);
			$member = $order->getCustomer();
			
			if($member) {
				return new MM_Response("Order ID '".$post["mm_attach_order_id"]."' is already attached to member ID '".$member->getId()."'", MM_Response::$ERROR);
			}
			
			$user = new MM_User($post["mm_id"]);
			
			if($user->isValid()) 
			{
				$user->setMainOrderId($post["mm_attach_order_id"]);
				$user->setCustomerId($result["customer_id"]);
				$user->setFirstName($result["first_name"]);
				$user->setLastName($result["last_name"]);
				$user->setPhone($result["customers_telephone"]);
				$user->setBillingAddress($result["billing_street_address"]);
				$user->setBillingCity($result["billing_city"]);
				$user->setBillingState($result["billing_state"]);
				$user->setBillingZipCode($result["billing_postcode"]);
				$user->setBillingCountry($result["billing_country"]);
				$user->setShippingAddress($result["shipping_street_address"]);
				$user->setShippingCity($result["shipping_city"]);
				$user->setShippingState($result["shipping_state"]);
				$user->setShippingZipCode($result["shipping_postcode"]);
				$user->setShippingCountry($result["shipping_country"]);
				
				$product = new MM_Product();
				$product->getProductByCampaign($result["main_product_id"],$post["mm_campaign_id"]);
				if(!$product->isValid()) 
				{
					return new MM_Response("Error attaching order '".$post["mm_attach_order_id"]."'. MemberMouse does not recognize the product ID '".$result["main_product_id"]."'", MM_Response::$ERROR);
				}
				
				$memberType = $product->getAssociatedMemberType();
				
				if(!$memberType) {
					return new MM_Response("Error attaching order '".$post["mm_attach_order_id"]."'. There is no member type associated with product ID '".$result["main_product_id"]."'", MM_Response::$ERROR);
				}
				
				$user->setMemberTypeId($memberType->id);
				
				$result = $user->commitData();
				
				if($result->type == MM_Response::$SUCCESS) {
					return new MM_Response("Order attached successfully to '".$user->getUsername()."'");
				}
				else {
					return $result;
				}
			}
		}
		
		return $error;
	}
	
	// TODO ERIC remove this functionality once Lime Light is calling updateOrder API call
	private function syncOrder($post)
	{	
		$error = new MM_Response("Error syncing order with ID '".$post[MM_Session::$PARAM_ORDER_ID]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_id"]) && isset($post[MM_Session::$PARAM_ORDER_ID])) 
		{
			$result = MM_LimeLightService::getOrder($post[MM_Session::$PARAM_ORDER_ID]);
			
			if($result instanceof MM_Response) {
				return $error;
			}
			
			$user = new MM_User($post["mm_id"]);
			
			if($user->isValid())
			{
				if(intval($result["order_status"]) == MM_Order::$STATUS_APPROVED 
					|| intval($result["order_status"]) == MM_Order::$STATUS_APPROVED_SHIPPED)
				{
					$memberType = new MM_MemberType($user->getMemberTypeId());
					
					if($memberType->isValid())
					{
						$product = new MM_Product($memberType->getProductId());
						
						if($product->isValid())
						{
							if($product->isRecurring())
							{
								if($result["is_recurring"] == 1) {
									$user->setStatus(MM_MemberStatus::$ACTIVE);	
								}
								else {
									$user->setStatus(MM_MemberStatus::$CANCELED);
								}
							}
						}
					}
				}
				else {
					$user->setStatus(MM_MemberStatus::$CANCELED);
				}
				
				$result = $user->commitData();
				
				if($result->type == MM_Response::$ERROR) {
					return $result;
				} 
				else {
					return new MM_Response("Order sync'd with Lime Lime successfully");
				}
			}
		}
		return $error;
	}
	
	/*
	 * requires mm_id and mm_order_id
	 */
	public function activateMembership($post)
	{
		$error = new MM_Response("Error activating member with id '".$post["mm_id"]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_id"])) 
		{
			$user = new MM_User($post["mm_id"]);
			
			if($user->isValid()) 
			{
				if(!$user->isFree()) 
				{
					
					/*
					 * if(isRecurringProduct()){
					 * 	if(hasAccessTag()){
					 * 		if(isRefunded()){
					 * 			StartRecurring
					 *  	}
					 *  	else{
					 *  		ResetRecurring
					 *  	}
					 * 	}
					 * 	else{
					 * 		placeOrder()
					 * 	}
					 * }
					 * else{
					 * 	placeOrder()
					 * }
					 */
					
					// reset product recurring in Lime Light
					$user->doUpdateLL = false;
					if(MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INSTALL_TYPE)==MM_Site::$INSTALL_TYPE_LIMELIGHT){
						$memberType = new MM_MemberType($user->getMemberTypeId());
						$product = new MM_Product($memberType->getRegistrationProduct());
						if($product->isRecurring()){
							if($user->getIsRefunded()=="1"){
								$paymentEngine = new MM_PaymentEngine(0);
								$result = $paymentEngine->purchaseProduct($user,$product->getId());
								if(!($result instanceof MM_Response)){
									$user->setIsRefunded("0");
									$user->setMainOrderId($result);
									$user->setLastOrder("");
									$user->commitData();
								}
								else{
									if($result->type == MM_Response::$ERROR){
										return new MM_Response("Error activating member", MM_Response::$ERROR);
									}
								}
							}
							else{
								$result = MM_LimeLightService::updateOrderRecurring($post["mm_order_id"], MM_LimeLightService::$REBILL_STATUS_RESET);
								if(($result instanceof MM_Response) && $result->type == MM_Response::$ERROR){
									return new MM_Response("Error reactivating member", MM_Response::$ERROR);
								}
							}
						}
						else{
							$paymentEngine = new MM_PaymentEngine(0);
							$result = $paymentEngine->purchaseProduct($user,$memberType->getRegistrationProduct());
							$user->setIsRefunded("0");
							$user->setLastOrder("");
							$user->setStatus(MM_MemberStatus::$ACTIVE);
							$user->setMainOrderId($result);
							$user->commitData();
						}
					}
				}
				
				$result = $this->changeMemberStatus($post, MM_MemberStatus::$ACTIVE);
				
				if($result->type == MM_Response::$SUCCESS) {
					$user = new MM_User($post["mm_id"]);
					$user->doUpdateLL = false;
					$user->commitData();
					
					return new MM_Response("Membership for '".$user->getUsername()."' was activated successfully");
				}
				else {
					return $result;
				}
			}
		}
		
		return $error;
	}
	
	private function cancelMembership($post)
	{
		$error = new MM_Response("Error cancelling member with id '".$post["mm_id"]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_id"])) 
		{
			$user = new MM_User($post["mm_id"]);
			
			if($user->isValid()) 
			{
				if(!$user->isFree()) 
				{
					$paymentId = MM_PaymentService::getMembershipPaymentMethodId($user);
					if($paymentId instanceof MM_Response){
                     	return $paymentId;
                    }
					$paymentService = new MM_PaymentEngine($paymentId);
					$result = $paymentService->updateOrderRecurring($post["mm_order_id"], MM_PaymentService::$REBILL_STATUS_STOP, $user->getId());
					//var_dump($result);
					// cancel product recurring in Lime Light
					//$result = MM_LimeLightService::updateOrderRecurring($post["mm_order_id"], MM_LimeLightService::$REBILL_STATUS_STOP);
					
					if($result->type == MM_Response::$ERROR) {
						$opt = $this->changeMemberStatus($post, MM_MemberStatus::$CANCELED);
						if($opt->type != MM_Response::$SUCCESS) {
							$result->message .= "\n".$opt->message;
						}
						else{
							return new MM_Response("Could not find the order ID but the membership for '".$user->getUsername()."' was canceled successfully.");
						}
						return $result;
					}
					
					if(preg_match("/^(http)/", $result->message)){
						return $result;
					}
				}
				
				$result = $this->changeMemberStatus($post, MM_MemberStatus::$CANCELED);
				
				if($result->type == MM_Response::$SUCCESS) {
					return new MM_Response("Membership for '".$user->getUsername()."' was canceled successfully");
				}
				else {
					return $result;
				}
			}
		}
		
		return $error;
	}
	
	public function changeMembership($post)
	{
		$error = new MM_Response("Error updating member with id '".$post["mm_id"]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_id"]) && isset($post["mm_new_membership_selection"]))
		{
			$user = new MM_User($post["mm_id"]);
			$newMemberType = new MM_MemberType($post["mm_new_membership_selection"]);
			
			if($user->isValid() && $newMemberType->isValid()) 
			{	
				$crntMemberType = new MM_MemberType($user->getMemberTypeId());
				
				$paymentId = MM_PaymentService::getMembershipPaymentMethodId($user);
				if($paymentId instanceof MM_Response){
					return $paymentId;
				}
				
				$paymentService = new MM_PaymentEngine($paymentId);
				$updatedUser = $paymentService->changeMembership($user, $crntMemberType, $newMemberType);
				
				//$updatedUser = MM_LimeLightService::changeMembership($user, $crntMemberType, $newMemberType);
				
				if($updatedUser instanceof MM_Response) {
					LogMe::write("changeMembership() : MM_Response returned[1] : ".$updatedUser->message);
					return $updatedUser;
				}
				
				$user->doUpdateLL = false;
				LogMe::write("changeMembership() : setting new order id : ".$updatedUser->getMainOrderId());
				$user->setMemberTypeId($newMemberType->getId());
				$user->setMainOrderId($updatedUser->getMainOrderId());
				$user->setLastOrder("");
				$result = $user->commitData();
				
				if($result instanceof MM_Response && $result->type == MM_Response::$ERROR) {
					LogMe::write("changeMembership() : MM_Response returned[2] : ".$result->message);
					return $result;
				}
				
				return new MM_Response("Membership for '".$user->getUsername()."' was changed successfully to '".$newMemberType->getName()."'");
			}
		}
			
		return $error;
	}
	
	public function activateAccessTag($post)
	{
		LogMe::write("activateAccessTag() : ".json_encode($post));
		
		$error = new MM_Response("Error activating access tag", MM_Response::$ERROR);
		
		if(isset($post["mm_id"]) && isset($post["mm_access_tag_id"]))
		{
			$user = new MM_User($post["mm_id"]);
			$tag = new MM_AccessTag($post["mm_access_tag_id"]);
			
			if($user->isValid() && $tag->isValid()) 
			{
				$orderId = MM_TransactionEngine::$MM_DFLT_ORDER_ID;
				
				if(!$tag->isFree()) 
				{
					// get ID of product to purchase
					if(isset($post["mm_product_id"])) {
						$productId = $post["mm_product_id"];
					}
					else 
					{
						$products = $tag->getAssociatedProducts();
						LogMe::write("activateAccessTag() : products: ".json_encode($products));
						$ctr = 0;
						
						foreach($products as $value=>$key) {
							$productId = $value;
							$ctr++;
						}
						
						if(intval($ctr) > 1) {
							return new MM_Response("Error activating access tag. Expected 1 product, but received ".$ctr.".", MM_Response::$ERROR);
						}
					}
					
					$paymentId = MM_PaymentService::getDefaultPaymentMethodId($user);
					if($paymentId instanceof MM_Response){
                     	return $paymentId;
                    }
					/*
					 * if(isRecurringProduct()){
					 * 	if(hasAccessTag()){
					 * 		if(isRefunded()){
					 * 			StartRecurring
					 *  	}
					 *  	else{
					 *  		ResetRecurring
					 *  	}
					 * 	}
					 * 	else{
					 * 		placeOrder()
					 * 	}
					 * }
					 * else{
					 * 	placeOrder()
					 * }
					 */
					LogMe::write("activateAccessTag() [".__LINE__."] : Product ID: {$productId}");
					$orderId = 0;
					$product = new MM_Product($productId);
					if($product->isValid()){
						if($product->isRecurring()){
							LogMe::write("activateAccessTag() [".__LINE__."] : Is Recurring");
							if($user->hasAccessTag($tag->getId())){
								$tagOrderId = $user->getAccessTagOrderId($tag->getId());
							LogMe::write("activateAccessTag() [".__LINE__."] : Order {$tagOrderId} : User has this tag ".$tag->getId());
								if(MM_AccessTag::IsRefundedForUser($tag->getId(), $user->getId())){
					LogMe::write("activateAccessTag() [".__LINE__."] : this product was refunded for user ".$user->getId(). " REBILL_STATUS_START" );
										
									$paymentEngine = new MM_PaymentEngine($paymentId);
									$orderId = $paymentEngine->purchaseProduct($user,$productId);
								}
								else{ 
					LogMe::write("activateAccessTag() [".__LINE__."] : this product was NOT refunded for user ".$user->getId(). " REBILL_STATUS_RESET" );
									$result = MM_LimeLightService::updateOrderRecurring($tagOrderId, MM_LimeLightService::$REBILL_STATUS_RESET);
									if($result->type == MM_Response::$ERROR){
										$orderResult = MM_LimeLightService::getOrder($tagOrderId);
										if($orderResult instanceof MM_Response){
											return $orderResult;
										}
										
										if(isset($orderResult["order_status"]) && isset($orderResult["is_recurring"])){
											if($orderResult["is_recurring"]=="0"){
												return $result;
											}
										}
										else{
											return $result;
										}
									}
									$orderId = $tagOrderId;
								}
							}
							else{
							LogMe::write("activateAccessTag() [".__LINE__."] : User does not have this tag ".$tag->getId());
								$paymentEngine = new MM_PaymentEngine($paymentId);
								$orderId = $paymentEngine->purchaseProduct($user,$productId);
							}
						}
						else{
							LogMe::write("activateAccessTag() [".__LINE__."] : Is NOT Recurring");
							$paymentEngine = new MM_PaymentEngine($paymentId);
							$orderId = $paymentEngine->purchaseProduct($user,$productId);
						}
					}
					else{
						return new MM_Response("Invalid product.", MM_Response::$ERROR);
					}
				}
				
				// update MemberMouse
				$result = $user->addAccessTag($tag->getId(), $orderId, $productId);
				
				if($result->type == MM_Response::$ERROR) {
					return $result;
				}
				
				return new MM_Response("'".$tag->getName()."' access tag was activated successfully for '".$user->getUsername()."'");
			}
		}
			
		return $error;
	}
	
	public function deactivateAccessTag($post)
	{
		$error = new MM_Response("Error deactivating access tag '".$post["mm_access_tag_id"]."' from member with id '".$post["mm_id"]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_id"]) && isset($post["mm_access_tag_id"]))
		{
			$user = new MM_User($post["mm_id"]);
			$tag = new MM_AccessTag($post["mm_access_tag_id"]);
			
			if($user->isValid() && $tag->isValid()) 
			{	
				// update Lime Light
				$tagOrderId = $user->getAccessTagOrderId($tag->getId());
				
				if(intval($tagOrderId) != MM_TransactionEngine::$MM_DFLT_ORDER_ID || MM_Utils::isLimeLightInstall()) 
				{
					$paymentId = MM_PaymentService::getAccessTagPaymentMethodId($user, $tag);
					if($paymentId instanceof MM_Response){
                     	return $paymentId;
                    }
					$paymentService = new MM_PaymentEngine($paymentId);
					$result = $paymentService->updateOrderRecurring($tagOrderId, MM_PaymentService::$REBILL_STATUS_STOP, $user->getId());
					//$result = MM_LimeLightService::updateOrderRecurring($user->getAccessTagOrderId($tag->getId()), MM_LimeLightService::$REBILL_STATUS_STOP);
						
					if($result->type == MM_Response::$ERROR) {
						return $result;
					}
					if(preg_match("/^(http)/", $result->message)){
						return $result;
					}
				}
				
				
				
				// update MemberMouse
				$applied = new MM_AppliedAccessTag();
				$applied->setAccessTagId($tag->getId());
				$applied->setRefId($user->getId());
				$applied->getDataByTagAndUser();
				$applied->setStatus("0");
				$applied->commitData();
				
				//MM_AccessTag::setRefundedForUser($tagOrderId, $user->getId());
//				$result = $user->removeAccessTag($tag->getId());
//				
//				if($result->type == MM_Response::$ERROR) {
//					return $result;
//				}
				
				return new MM_Response("'".$tag->getName()."' access tag was deactivated successfully for '".$user->getUsername()."'");
			}
		}
		
		return $error;
	}
	
	public function changeMemberStatus($post, $status)
	{
		$error = new MM_Response("Error updating member with id '".$post["mm_id"]."'", MM_Response::$ERROR);
		
		if(isset($post["mm_id"])) 
		{
			$user = new MM_User($post["mm_id"]);
			
			if($user->isValid()) 
			{
				$user->doUpdateLL = false;
				$user->setStatus($status);
				
				$result = $user->commitData();
				
				if($result->type == MM_Response::$SUCCESS) {
					return new MM_Response("Member status for '".$user->getUsername()."' updated successfully");
				}
				else {
					return $result;
				}
			}
			else {
				return $error;
			}
		}
		else {
			return $error;
		}
	}
	
	public function getAccessTags($userId)
	{
		global $wpdb;
		
		$user = new MM_User($userId);
		$results = array();
		
		if($user->isValid())
		{	
			// get applied tags
			$appliedTags = $user->getAccessTags(false,true);
			
			if($appliedTags) 
			{
				foreach($appliedTags as $tag)
				{
					$result = new stdClass();
					
					$result->id = $tag->access_tag_id;
					$result->activationDate = $tag->apply_date;
					$result->isActive = 1;
					
					array_push($results, $result);
				}
			}
			
			// get unapplied tags not associated with the user's member type
			$sql = "SELECT * FROM ".MM_TABLE_ACCESS_TAGS." at ".
				" WHERE at.status = '1' AND at.id NOT IN (SELECT access_tag_id FROM ".MM_TABLE_APPLIED_ACCESS_TAGS.
				" WHERE (access_type='user' and ref_id='".$user->getId()."' and status='1') ".
				" OR (access_type='member_type' and ref_id='".$user->getMemberTypeId()."')) ".
				" ORDER BY name asc";
			
			$unappliedTags = $wpdb->get_results($sql);
			
			if($unappliedTags) 
			{
				foreach($unappliedTags as $tag)
				{
					$result = new stdClass();
					
					$result->id = $tag->id;
					$result->activationDate = MM_NO_DATA;
					$result->isActive = 0;
					
					array_push($results, $result);
				}
			}
		}
		
		return $results;
	}
 }
?>
