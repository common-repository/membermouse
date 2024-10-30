<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MM_AccountTypesView extends MM_View
 {
 	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveAccountType($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeAccountType($post);
					
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
		return parent::getData(MM_TABLE_ACCOUNT_TYPES, null, $sortBy, $sortDir);
	}
	
	private function saveAccountType($post)
	{
		$acctType = new MM_AccountType();
		
		if(isset($post["id"]) && intval($post["id"])>0) {
			$acctType->setId($post["id"]);
		}
		
		if(isset($post["mm_unlimited_paid_members"]) && $post["mm_unlimited_paid_members"]=="yes")
	 	{ 
	 		$post["mm_unlimited_paid_members"] = "1";
	 		$post["mm_num_paid_members"] = "";
	 	} else { 
	 		$post["mm_unlimited_paid_members"] =  "0";
	 	}
	 	
	 	if(isset($post["mm_unlimited_total_members"]) && $post["mm_unlimited_total_members"]=="yes") { 
	 		$post["mm_unlimited_total_members"] = "1";
	 		$post["mm_num_total_members"] = "";
	 	} else { 
	 		$post["mm_unlimited_total_members"] = "";
	 	}
	 	
	 	if($post["mm_status"]=="active") {
	 		$post["mm_status"] = "1";
	 	} else {
	 		$post["mm_status"] = "0";
	 	}
	 	
	 	$acctType->setName($post["mm_display_name"]);
	 	$acctType->setStatus($post["mm_status"]);
	 	$acctType->setNumSites($post["mm_num_sites"]);
	 	$acctType->setNumPaidMembers($post["mm_num_paid_members"]);
	 	$acctType->setNumTotalMembers($post["mm_num_total_members"]);
	 	$acctType->setUnlimitedPaidMembers($post["mm_unlimited_paid_members"]);
	 	$acctType->setUnlimitedTotalMembers($post["mm_unlimited_total_members"]);
		
		return $acctType->commitData();
	}
	
	public function removeAccountType($post)
	{	
		global $wpdb;
		
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$acctType = new MM_AccountType($post["id"], false);
			$result = $acctType->delete();
			
			if($result) {
				return new MM_Response();
			} 
			else {
				return new MM_Response("This account type has existing associations and can't be removed.", MM_Response::$ERROR);
			}
		}
		
		return new MM_Response("Unable to delete account type. No id specified.", MM_Response::$ERROR);
	}
 }
?>
