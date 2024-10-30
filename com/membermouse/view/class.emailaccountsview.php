<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_EmailAccountsView extends MM_View
{
 	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveEmailAccount($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeEmailAccount($post);
					
				case self::$MM_JSACTION_SET_CONFIRM:
					return $this->setAsConfirmed($post);
					
				case self::$MM_JSACTION_SET_DEFAULT:
					return $this->setAsDefault($post);
				
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
 	
 	public function getData($sortBy=null, $sortDir=null)
	{
		return parent::getData(MM_TABLE_EMAIL_ACCOUNTS, null, $sortBy, $sortDir);
	}
	
	private function saveEmailAccount($post)
	{
		$email = new MM_EmailAccount();
		
		$sendEmail = true;
		if(isset($post["id"]) && intval($post["id"])>0) {
			$email->setId($post["id"]);
			$sendEmail = false;
		}
		
	 	$email->setName($post["mm_display_name"]);
	 	$email->setFullName($post["mm_name"]);
	 	$email->setUsername($post["mm_username"]);
	 	$email->setPassword($post["mm_password"]);
	 	$email->setRoleId($post["mm_role_id"]);
	 	$email->setPhone($post["mm_phone"]);
	 	
	 	$email->setAddress($post["mm_email"]);
	 	$email->setIsDefault($post["mm_is_default"]);
	 	$email->setStatus($post["mm_status"]);
		
	 	$result = $email->commitData();
	 	
	 	if($result instanceof MM_Response && $result->type == MM_Response::$ERROR) {
			return $result;
	 	}
	 	
	 	if(!$sendEmail){
	 		return new MM_Response();	
	 	}
	 	
	 	// send confirmation email
	 	$cpe = new MM_CorePageEngine();
	 	$body = "Please confirm your email account by clicking the link below:".MM_Email::$BR;
	 	
	 	$url = $cpe->getUrl(MM_CorePageType::$LOGIN_PAGE);
	 	
	 	if(strstr($url, "?") == false) {
	 		$url .= "?";
	 	}
	 	else {
	 		$url .= "&";
	 	}
	 	
	 	$body .= $url.MM_Session::$PARAM_CONFIRMATION_KEY."=".urlencode(base64_encode($email->getId()));
	 	
	 	$email = new MM_Email();
			
		$email->setSubject("Please confirm your new email account");
		$email->setBody($body);
		$email->setToName($post["mm_display_name"]);
		$email->setToAddress($post["mm_email"]);
		$email->setFromName(MM_NOTICE_EMAIL_NAME);
		$email->setFromAddress(MM_NOTICE_EMAIL_ADDRESS);
		
		$result = $email->send();
	 	
	 	return $result;
	}
	
	private function removeEmailAccount($post)
	{
		global $wpdb;
		
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$email = new MM_EmailAccount($post["id"], false);
			$result = $email->delete();
			
			if($result) {
				return new MM_Response();
			} 
			else {
				return new MM_Response("This email account has existing associations and can't be removed.", MM_Response::$ERROR);
			}
		}
		
		return new MM_Response("Unable to delete email account. No id specified.", MM_Response::$ERROR);
	}
	
 	private function setAsConfirmed($post)
	{
		global $wpdb;
		
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{	
			$sql = "update ".MM_TABLE_EMAIL_ACCOUNTS." set status='1' where id='%d' limit 1";
			$results = $wpdb->query($wpdb->prepare($sql, $post["id"]));
			
			if($results)
			{
				return new MM_Response();
			}
		}
		
		return new MM_Response("Unable to set email account account as confirmed. No id specified.", MM_Response::$ERROR);
	}
	
 	private function setAsDefault($post)
	{
		global $wpdb;
		
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$sql = "update ".MM_TABLE_EMAIL_ACCOUNTS." set is_default='0'";
			$wpdb->query($sql);
			
			$sql = "update ".MM_TABLE_EMAIL_ACCOUNTS." set is_default='1' where id='%d' limit 1";
			$results = $wpdb->query($wpdb->prepare($sql, $post["id"]));
			
			if($results)
			{
				return new MM_Response();
			}
		}
		
		return new MM_Response("Unable to set email account account as default. No id specified.", MM_Response::$ERROR);
	}
}
?>
