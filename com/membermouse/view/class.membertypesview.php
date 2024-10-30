<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_MemberTypesView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveMemberType($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeMemberType($post);
					
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
	
	public function getData(MM_DataGrid $dg)
	{
		global $wpdb;
		
		$rows = parent::getData(MM_TABLE_MEMBER_TYPES, null, $dg);
		
		foreach($rows as $row)
		{
//			$sql = "select id, name from ".MM_TABLE_PRODUCTS." where id='{$row->registration_product_id}'";
//			$row->product = $wpdb->get_row($sql);

			$sql = "select 
						p.id, p.name
					from 
						".MM_TABLE_MEMBER_TYPE_PRODUCTS." mtp, ".MM_TABLE_PRODUCTS." p 
					where 
						mtp.member_type_id='{$row->id}' and mtp.product_id=p.id
					group by 
						name
					";
		
			$row->products = $wpdb->get_results($sql);
			
			$sql = "select * from ".MM_TABLE_MEMBER_TYPES." where id='{$row->upgrade_to_id}'";
			$row->upgrade_to = $wpdb->get_row($sql);
			
			$sql = "select * from ".MM_TABLE_MEMBER_TYPES." where id='{$row->downgrade_to_id}'";
			$row->downgrade_to = $wpdb->get_row($sql);
			
			$sql = "select a.* from ".MM_TABLE_APPLIED_ACCESS_TAGS." at, ".MM_TABLE_ACCESS_TAGS." a where at.access_type='member_type' and at.ref_id='{$row->id}' and at.access_tag_id=a.id";
			$row->access_tags = $wpdb->get_results($sql);
			
			$sql = "select count(*) as total from ".$wpdb->users." where mm_member_type_id='{$row->id}'";
			$obj =$wpdb->get_row($sql);
			$row->member_count = $obj->total;
		}
		
		return $rows;
	}
	
	private function saveMemberType($post)
	{	
		global $mmSite;
		
		$mt = new MM_MemberType();
		
		if(isset($post["id"]) && intval($post["id"])>0) {
			$mt->setId($post["id"]);
		}
		
		if(isset($post["mm_subscription_type"]) && $post["mm_subscription_type"]=="free") { 
	 		$post["mm_subscription_type"] = '1';
	 		$post["mm_products"] = '';
	 	} else { 
	 		$post["mm_subscription_type"]=  '0';
	 	}
	 	
	 	if($post["mm_status"]=="active") {
	 		$post["mm_status"] = "1";
	 	} else {
	 		$post["mm_status"] = "0";
	 	}
	 	
		if($post["mm_include_on_reg"]=="yes") {
			$post["mm_include_on_reg"] = "1";
		} else {
			$post["mm_include_on_reg"]="0";
		}
	 	$mt->setIsFree($post["mm_subscription_type"]);
	 	$mt->setRegistrationProduct($post["mm_registration_product_id"]);
	 	$mt->setStatus($post["mm_status"]);
	 	$mt->setIsDefault($post["mm_is_default"]);
	 	$mt->setIncludeOnReg($post["mm_include_on_reg"]);
		$mt->setName($post["mm_display_name"]);
	 	$mt->setDescription($post["mm_description"]);
	 	$mt->setUpgradeId($post["mm_upgrade_to"]);
	 	$mt->setDowngradeId($post["mm_downgrade_to"]);
	 	$mt->setEmailSubject($post["mm_email_subject"]);
	 	$mt->setEmailBody($post["mm_email_body"]);
	 	$mt->setEmailFromId($post["mm_email_from"]);		
	 	$mt->setWelcomeEmailEnabled($post["mm_welcome_email_enabled"]);		
	 	$mt->setBadgeUrl($post["badge_url"]);
	 	LogMe::write("POSTVARS: ".json_encode($post));
	 	if(!empty($post["mm_products"]) && is_array($post["mm_products"]) && count($post["mm_products"]) > 0 && $post["mm_subscription_type"]!="1") {
	 		$mtSel = array();
	 		foreach($post["mm_products"] as $key => $val){
	 			$mtSel[$val] = $val;
	 		}
		 	$mt->setProductIds($mtSel);
	 	}
	 	
	 	if(!empty($post["mm_access_tags"]) && is_array($post["mm_access_tags"]) && count($post["mm_access_tags"]) > 0) {
	 		$mt->setAccessTags($post["mm_access_tags"]);
	 	} else {
	 		$mt->setAccessTags(array());
	 	}
	 	
	 	if($mmSite->isMM() && isset($post["mm_account_types"])) {
			$mt->setAccountTypeId($post["mm_account_types"]);
	 	}
		else {
			$mt->setAccountTypeId("");
		}
		
		return $mt->commitData();
	}
	
	private function removeMemberType($post)
	{
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$mt = new MM_MemberType($post["id"], false);
			$result = $mt->delete();
			
			if($result) {
				return new MM_Response();
			} 
			else {
				return new MM_Response("This member type has existing associations and can't be removed.", MM_Response::$ERROR);
			}
		}
		
		return new MM_Response("Unable to delete member type. No id specified.", MM_Response::$ERROR);
	}
	
 	private function setAsDefault($post)
	{
		global $wpdb;
		
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$sql = "update ".MM_TABLE_MEMBER_TYPES." set is_default='0'";
			$wpdb->query($sql);
			
			$sql = "update ".MM_TABLE_MEMBER_TYPES." set is_default='1' where id='%d' limit 1";
			$results = $wpdb->query($wpdb->prepare($sql, $post["id"]));
			
			if($results)
			{
				return new MM_Response();
			}
		}
		
		return new MM_Response("Unable to set member type as default. No id specified.", MM_Response::$ERROR);
	}
}
?>
