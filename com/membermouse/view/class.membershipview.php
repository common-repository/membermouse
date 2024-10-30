<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_MembershipView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_FF:
					return $this->fastForwardMembership($post);
					
				case self::$MM_JSACTION_FF_DIALOG:
					return $this->showFastForward($post);
					
				case self::$MM_JSACTION_MEMBERSHIP_CHANGE_DIALOG:
					return $this->showChange($post);
					
				case self::$MM_JSACTION_MEMBERSHIP_CHANGE:
					return $this->changeMembership($post);
					
				case self::$MM_JSACTION_MEMBERSHIP_CANCEL_DIALOG:
					return $this->showCancellation($post);
					
				case self::$MM_JSACTION_MEMBERSHIP_CANCEL:
					return $this->cancelMembership($post);
					
				case self::$MM_JSACTION_MEMBERSHIP_PAUSE_DIALOG:
					return $this->showPause($post);
					
				case self::$MM_JSACTION_MEMBERSHIP_PAUSE:
					return $this->pauseMembership($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	public function changeMembership($post){
		$mmDetailsView = new MM_MemberDetailsView();
		$response = $mmDetailsView->changeMembership($post);
		if($response->type == MM_Response::$SUCCESS){
			if(is_string($response->message) && preg_match("/^(http)/", $response->message)){
				$response->message = array(
					'message'=>"",
					'url' => $response->message,
				);
				return $response;
			}
			if(is_array($response->message) && isset($response->message["url"]) && preg_match("/^(http)/", $response->message["url"])){
				if(MM_Utils::isLimeLightInstall()){
					$response->message = array(
						'message'=>"",
						'url' => $response->message["url"],
					);
				}
				return $response;
			}
			
	        $params = array();
			$mt = new MM_MemberType($post["mm_new_membership_selection"]);
	        $params["isFree"] = $mt->isFree();
	        $refType = MM_TYPE_PRODUCT;
	        $typeId = $mt->getRegistrationProduct();
	        if((bool)$params["isFree"]){
	        	$refType = 	MM_TYPE_MEMBER_TYPE;
	        	$typeId= $mt->getId();
	        }
	        
	        $params["refType"] =$refType;
	        $params["refId"] = $typeId;
	        
	        global $current_user;
	        MM_Session::value(MM_Session::$KEY_LAST_USER_ID, $current_user->ID);
	        
			$cpe = new MM_CorePageEngine();
			$url = $cpe->getUrl(MM_CorePageType::$PAID_CONFIRMATION, $params);
			$response->message = array(
				'message'=>$response->message,
				'url' => $url,
			);
		}
		return $response;
	}
	
	public function cancelMembership($post)
	{
		global $current_user;
					LogMe::write("MM_MembershipView::cancelMembership() : ".json_encode($post));
		if(MM_Utils::isAdmin())
		{
			return new MM_Response("You cannot cancel your account as an admin.", MM_Response::$ERROR);
		}
		if(!isset($current_user->ID) || (isset($current_user->ID) && intval($current_user->ID)<=0))
		{
			return new MM_Response("You must be logged in to cancel your account.", MM_Response::$ERROR);
		}
		$error = new MM_Response("Error cancelling member with id '".$current_user->ID."'", MM_Response::$ERROR);
		
		if(isset($current_user->ID)) 
		{
			$user = new MM_User($current_user->ID);
			
			if($user->isValid()) 
			{
				if(!$user->isFree()) 
				{
					// cancel product recurring in Lime Light
					$lastOrderId = $user->getLastOrderId();
					if(intval($lastOrderId)<=0){
						$lastOrderId = $user->getMainOrderId();
					}
					
					$paymentId = MM_PaymentService::getMembershipPaymentMethodId($user);
					if($paymentId instanceof MM_Response){
                     	return $paymentId;
                    }
				
					LogMe::write("MM_MembershipView::cancelMembership() : Payment ID : ".$paymentId);
					$paymentService = new MM_PaymentEngine($paymentId);
					$result = $paymentService->updateOrderRecurring($lastOrderId, MM_PaymentService::$REBILL_STATUS_STOP, $user->getId());
					LogMe::write("MM_MembershipView::cancelMembership() : call to updateOrderRecurring : result".json_encode($result));
					
					if(is_string($result->message) && preg_match("/^(http)/", $result->message)){
						return new MM_Response($result->message);
					}				
				}
				
				$result = $this->changeMemberStatus($post, MM_MemberStatus::$CANCELED);
					LogMe::write("MM_MembershipView::cancelMembership() : result".json_encode($result));
				
				if($result->type == MM_Response::$SUCCESS) {
						$url = ((isset($post["redirect_url"]))?$post["redirect_url"]:"");
						wp_clear_auth_cookie();
						return new MM_Response($url);
				}
				else {
					return $result;
				}
			}
		}
		
		return $error;
	}
	
	public function pauseMembership($post, $fromAdmin = false)
	{
		global $current_user;
		LogMe::write("pauseMembership() : ".json_encode($post));
		if(MM_Utils::isAdmin() && !$fromAdmin)
		{
			return new MM_Response("You cannot cancel your account as an admin.", MM_Response::$ERROR);
		}
		
		$userId = $current_user->ID;
		if($fromAdmin){
			$userId = $post["mm_id"];
		}
		
		if(!isset($userId) || (isset($userId) && intval($userId)<=0))
		{
			return new MM_Response("You must be logged in to cancel your account.", MM_Response::$ERROR);
		}
		$error = new MM_Response("Error cancel member with id '".$userId."'", MM_Response::$ERROR);
		
		if(isset($userId)) 
		{
			$user = new MM_User($userId);
			
			if($user->isValid()) 
			{
				if(!$user->isFree()) 
				{
					$isLimeLightInstall = MM_OptionUtils::getOption("mm-install_type");
					if($isLimeLightInstall == MM_Site::$INSTALL_TYPE_LIMELIGHT){
						// cancel product recurring in Lime Light
						$lastOrderId = $user->getLastOrderId();
						if(intval($lastOrderId)<=0){
							$lastOrderId = $user->getMainOrderId();
						}
						
						LogMe::write("pauseMembership() : ".$lastOrderId);
						
						$paymentId = MM_PaymentService::getMembershipPaymentMethodId($user);
						
						$paymentService = new MM_PaymentEngine($paymentId);
						$result = $paymentService->updateOrderRecurring($lastOrderId, MM_PaymentService::$REBILL_STATUS_STOP, $user->getId());
						//$result = MM_LimeLightService::updateOrderRecurring($lastOrderId, MM_LimeLightService::$REBILL_STATUS_STOP);
						
						if($result->type == MM_Response::$ERROR) {
							return $result;
						}
					}
				}
				
				$days = $user->getDaysAsMember();
				
				$user->doUpdateLL = false;
				$user->setStatus(MM_MemberStatus::$PAUSED);
				$user->setDaysCalcMethod(MM_DaysCalculationTypes::$FIXED);
				$user->setDaysCalcValue($days);
				$result = $user->commitData();
				if($fromAdmin){
					return $result;
				}
				if($result->type == MM_Response::$SUCCESS) {
					if(!preg_match("/^(http)/", $result->message)){
						$url = ((isset($post["redirect_url"]))?$post["redirect_url"]:"");
						wp_clear_auth_cookie();
						return new MM_Response($url);
					}
					return new MM_Response($result->message);
				}
				else {
					return $result;
				}
			}
		}
		
		return $error;
	}

	private function changeMemberStatus($post, $status)
	{
		global $current_user;
		$error = new MM_Response("Error updating member with id '".$current_user->ID."'", MM_Response::$ERROR);
		
		if(isset($current_user->ID)) 
		{
			$user = new MM_User($current_user->ID);
			
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
	
	public function fastForwardMembership($post,$purchasedOk=false){
		global $current_user;
		$error = new MM_Response("Error fast forwarding member with id '".$current_user->ID."'", MM_Response::$ERROR);
		if(!isset($post["days"]) || !isset($post["price"])){
			return new MM_Response("Could not find required parameters", MM_Response::$ERROR);
		}
		
		$days = intval($post["days"]);
		$price = floatval($post["price"]);
		$user = new MM_User($current_user->ID);
		if(isset($post["user_id"])){
			$user=  new MM_User($post["user_id"]);
		}
		if(intval($days)<=0){
			return new MM_Response("Days must be a positive integer greater than 0", MM_Response::$ERROR);
		}
		
		if($user->isAdmin()){
			return new MM_Response("You cannot fast forward a membership in Administrative Mode", MM_Response::$ERROR);
		}
		
		$urlResponse = new MM_Response();
		if(!$purchasedOk && $price>0){
			$paymentId = MM_PaymentService::getDefaultPaymentMethodId("paypal");
			if(MM_Utils::isLimeLightInstall()){
				$paymentId = MM_PaymentService::getDefaultPaymentMethodId("limelight");
			}
			
			if($paymentId instanceof MM_Response){
				return $paymentId; 
			}
			
			$paymentOption = new MM_CampaignOptions($paymentId);
//			if(!$paymentOption->isValid()){
//				return new MM_Response("You cannot use clickbank ",MM_Response::$ERROR);
//			}
			
			$user = new MM_User($current_user->ID);
			$memberType = new MM_MemberType($user->getMemberTypeId());
			$product = new MM_Product($memberType->getRegistrationProduct());
			if($memberType->isFree()){
				$purchasedOk = true;
			}
			else if($product->isValid()){
				$paymentEngine = new MM_PaymentEngine($paymentId);
				LogMe::write("PRICE: {$price}");
				$urlResponse = $paymentEngine->fastForwardMembership($product, $price, $days);
				if($urlResponse instanceof MM_Response && (isset($urlResponse->message["url"]) || isset($urlResponse->message->url))){
					return $urlResponse;
				}
				$purchasedOk=true;
			}
		}
		else{
			$purchasedOk = true;
		}
		
		LogMe::write("membershipView".__LINE__);
		
		if($purchasedOk){
			LogMe::write("membershipView". __LINE__);
			$user->doUpdateLL = false;
			if($user->isValid()){
				LogMe::write("membershipView USERID: ". $user->getId());
				$dayName = ($days>1)?"days":"day";
				LogMe::write("membershipView Days: ". $days." : ".$user->getDaysCalcMethod());
				switch($user->getDaysCalcMethod()){
					case MM_DaysCalculationTypes::$CUSTOM:
						LogMe::write("membershipView", __LINE__);
						$customOriginalDate = Date("Y-m-d h:i:s", strtotime($user->getDaysCalcValue()));
						$customDate = strtotime("-".$days." ".$dayName, strtotime($customOriginalDate));
						$user->setDaysCalcMethod(MM_DaysCalculationTypes::$CUSTOM);
						$user->setDaysCalcValue(Date("Y-m-d h:i:s", $customDate));
						break;
					case MM_DaysCalculationTypes::$FIXED:
						LogMe::write("membershipView", __LINE__);
						$daysCalc = intval($user->getDaysCalcValue());
						$daysTotal = $days+$daysCalc;
						$customDate = strtotime("-".$daysTotal." ".$dayName);
						$user->setDaysCalcMethod(MM_DaysCalculationTypes::$CUSTOM);
						$user->setDaysCalcValue(Date("Y-m-d h:i:s", $customDate));
						break;
					default:
						LogMe::write("membershipView", __LINE__);
						$registrationDate = Date("Y-m-d h:i:s", strtotime($user->getRegistrationDate()));
						$customDate = strtotime("-".$days." ".$dayName, strtotime($registrationDate));
						
						$user->setDaysCalcMethod(MM_DaysCalculationTypes::$CUSTOM);
						$user->setDaysCalcValue(Date("Y-m-d h:i:s", $customDate));
						break;
				}
				$response = $user->commitData();
				if($response instanceof MM_Response){
					if($response->type == MM_Response::$SUCCESS){
						return $response;
					}
				}
			}
			else{
				return new MM_Response("User is not logged in", MM_Response::$ERROR);
			}
		}
		else{
			return new MM_Response("Could not purchase fast forward", MM_Response::$ERROR);
		}
		
		return $error;
	}
	
	private function showFastForward($post){
		$info =new stdClass();
		$info->price = (isset($post["mm_price"]))?$post["mm_price"]:"0";
		$info->days = (isset($post["mm_days"]))?$post["mm_days"]:"0"; 
 		$msg = MM_TEMPLATE::generate(MM_MODULES."/membership_fastforward.php", $info);
 		return new MM_Response($msg);
	}
	
	private function showCancellation($post)
	{	
		$info =new stdClass();
		$info->redirect_to = (isset($post["redirect_url"]))?$post["redirect_url"]:""; 
 		$msg = MM_TEMPLATE::generate(MM_MODULES."/membership_cancel.php", $info);
 		return new MM_Response($msg);
	}
	
	private function showPause($post)
	{	
		$info =new stdClass();
		$info->redirect_to = (isset($post["redirect_url"]))?$post["redirect_url"]:""; 
 		$msg = MM_TEMPLATE::generate(MM_MODULES."/membership_pause.php", $info);
 		return new MM_Response($msg);
	}
	
	private function showChange($post)
	{	
		$info =new stdClass();
		$info->member_type_id = (isset($post["member_type_id"]))?$post["member_type_id"]:""; 
 		$msg = MM_TEMPLATE::generate(MM_MODULES."/membership_change.php", $info);
 		return new MM_Response($msg);
	}
	
	private function showChoices()
	{
		$info =new stdClass();
 		$msg =  MM_TEMPLATE::generate(MM_MODULES."/membership_choose.php", $info);
 		return new MM_Response($msg);
	}
}