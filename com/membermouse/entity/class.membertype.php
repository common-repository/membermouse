<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_MemberType extends MM_Entity
{	
	public static $SUB_TYPE_PAID = "paid";
	public static $SUB_TYPE_FREE = "free";
	
	public static $DFLT_EMAIL_SUBJECT = "Welcome [MM_Member_FirstName]!";
	public static $DFLT_EMAIL_BODY = "Hi [MM_Member_FirstName],\n\nThanks for joining our community!\n\nYou can login with the following credentials:\nUsername: [MM_Member_Username]\nPassword: [MM_Member_Password]\n\nIf you have any questions, feel free to contact us at <a href=\"mailto:[MM_Email_Address]\">[MM_Email_Address]</a>.\n\nThanks!\n[MM_Email_Name]";
	
	private $emailSubject =  "Welcome [MM_Member_FirstName]!";
	private $emailBody = "Hi [MM_Member_FirstName],\n\nThanks for joining our community!\n\nYou can login with the following credentials:\nUsername: [MM_Member_Username]\nPassword: [MM_Member_Password]\n\nIf you have any questions, feel free to contact us at <a href=\"mailto:[MM_Email_Address]\">[MM_Email_Address]</a>.\n\nThanks!\n[MM_Email_Name]";
	private $name = "";
	private $registrationProductId="";
	private $isFreeInd = "1";
	private $isDefaultInd = "0";
	private $includeOnReg = "1";
	private $description = "";
	private $productIds = array();
	private $status = "1";
	private $upgradeToId = "";
	private $downgradeToId = "";
	private $emailFromId = "";
	private $badgeUrl = "";
	private $accountTypeId = "";
	private $accessTags = "";
	private $welcomeEmailEnabled = "1"; //welcome_email_enabled
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_MEMBER_TYPES." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_MemberType.getData(): error retrieving data for member type with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function getDefault()
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_MEMBER_TYPES." WHERE is_default='1' LIMIT 1";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
		}
	}
	
	public function setData($data)
	{
		try 
		{
			$this->id = $data->id;
			$this->name = $data->name;
			$this->isFreeInd = $data->is_free;
			$this->isDefaultInd = $data->is_default;
			$this->includeOnReg = $data->include_on_reg;
			$this->description = $data->description;
			$this->registrationProductId = $data->registration_product_id;
			$this->setProducts();
			$this->status = $data->status;
			$this->upgradeToId = $data->upgrade_to_id;
			$this->downgradeToId = $data->downgrade_to_id;
			$this->emailSubject = $data->email_subject;
			$this->emailBody = $data->email_body;
			$this->emailFromId = $data->email_from_id;
			$this->badgeUrl = $data->badge_url;
			$this->welcomeEmailEnabled = $data->welcome_email_enabled;
			
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	private function setProducts(){
		global $wpdb;
		$sql = "select product_id as id from ".MM_TABLE_MEMBER_TYPE_PRODUCTS." mtp where  member_type_id='{$this->id}' ";
		
		$rows = $wpdb->get_results($sql);
	
		$products = array();
		if(is_array($rows)){
			foreach($rows as $product){
				$products[$product->id] = $product->id;
			}
		}
		$this->setProductIds($products);
	}
	
	public function commitData()
	{
		global $wpdb, $mmSite;
		
		$doUpdate = isset($this->id) && $this->id != "" && intval($this->id) > 0;
		
		MM_Transaction::begin();
		try
		{	
			if(!$doUpdate) 
			{
				$sql = "insert into ".MM_TABLE_MEMBER_TYPES." set " .
			 			"	name = '%s'," .
			 			"	status='%d'," .
			 			"	is_free='%d'," .
			 			"	is_default='%d'," .
			 			"	include_on_reg='%d'," .
			 			"	description='%s'," .
			 			"	registration_product_id='%d'," .
			 			"	upgrade_to_id='%d'," .
			 			"	downgrade_to_id='%d'," .
			 			"	email_subject='%s'," .
			 			"	email_body='%s'," .
			 			"	email_from_id='%d'," .
			 			"	welcome_email_enabled='%d'," .
			 			"	badge_url='%s'" .
			 			"";
			}
		 	else 
		 	{
				$sql = "update ".MM_TABLE_MEMBER_TYPES." set " .
			 			"	name = '%s'," .
			 			"	status='%d'," .
		 				"	is_free='%d'," .
			 			"	is_default='%d'," .
		 				"	include_on_reg='%d'," .
		 				"	description='%s'," .
		 				"	registration_product_id='%d'," .
		 				"	upgrade_to_id='%d'," .
			 			"	downgrade_to_id='%d'," .
			 			"	email_subject='%s'," .
			 			"	email_body='%s'," .
			 			"	email_from_id='%d'," .
			 			"	welcome_email_enabled='%d'," .
			 			"	badge_url='%s' where id='{$this->id}'" .
			 			"";
		 	}
			
		 	$preparedSql = $wpdb->prepare($sql, $this->name, $this->status, $this->isFreeInd, $this->isDefaultInd, $this->includeOnReg, 
		 										$this->description, $this->registrationProductId, $this->upgradeToId, $this->downgradeToId, 
		 										$this->emailSubject, $this->emailBody, $this->emailFromId,$this->welcomeEmailEnabled, $this->badgeUrl);
		 											
		 	$result = $wpdb->query($preparedSql);
		 	
		 	if($result === false)
		 	{
		 		MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create member type (".$preparedSql.")", MM_Response::$ERROR);
		 	}
		 	
		 	if(!$doUpdate) {
		 		$this->id = $wpdb->insert_id;
		 	}
		 	
		 	if(intval($this->id)>0){
			 	$this->removeProducts();
			 	$products = $this->productIds;
			 	if(is_array($products)){
			 		foreach($products as $productId){
			 			$this->addProduct($productId);
			 		}
			 	}
		 	}
		 	
		 	$this->addAccessTags();
			
		 	if($mmSite->isMM()) {
			 	$this->addAccountType();
			}
			
			$campaignsInUse = MM_Campaign::getCampaignsInUse();
			MM_MemberMouseService::updateCampaignUsage($mmSite->getId(),$campaignsInUse);
		}
		catch(Exception $ex)
		{	
		 	MM_Transaction::rollback();
	 		return new MM_Response("ERROR: unable to create member type", MM_Response::$ERROR);
		}
		
		MM_Transaction::commit();
	
		return new MM_Response();
	}
	
	public function addProduct($productId){
		global $wpdb;
		$sql = "insert into ".MM_TABLE_MEMBER_TYPE_PRODUCTS." set 
					member_type_id='{$this->id}', 
					product_id='{$productId}'
					";
		LogMe::write("addProduct() : ".$sql);
		$wpdb->query($sql);
	}
	
	private function removeProducts(){
		global $wpdb;
		$sql = "delete from ".MM_TABLE_MEMBER_TYPE_PRODUCTS." where member_type_id='{$this->id}'";
		$wpdb->query($sql);
	}
	
	private function addAccessTags()
	{
		global $wpdb;
		$this->accessTags = (!is_array($this->accessTags)) ? array($this->accessTags) : $this->accessTags;
		
		$this->removeAccessTags();
		
		if(!empty($this->accessTags) && count($this->accessTags) > 0) 
		{
			foreach($this->accessTags as $tag)
			{
				if($tag != "null") 
				{
					$sql = "insert into ".MM_TABLE_APPLIED_ACCESS_TAGS." set " .
							"	access_type='member_type'," .
							"	access_tag_id='{$tag}'," .
							"	ref_id='{$this->id}'  ";
					
					$wpdb->query($sql);
				}
			}
		}
	}
	
	public function sendWelcomeEmail($userId)
	{
		if($this->welcomeEmailEnabled == '1'){
			$user= new MM_User($userId);
			$emailAccount = MM_EmailAccount::getEmailAccount($this->emailFromId);
			$context = new MM_Context($user, $emailAccount);
			
			$email = new MM_Email();
			$email->setContext($context);
			$email->setSubject($this->emailSubject);
			$email->setBody($this->emailBody);
			$email->setToName($user->getFirstName());
			$email->setToAddress($user->getEmail());
			$email->setFromName($emailAccount->getName());
			$email->setFromAddress($emailAccount->getAddress());
			
			$response = $email->send();
			return $response;
		}
		return new MM_Response("Email not enabled for this member type.", MM_Response::$ERROR);
	}
	
	private function removeAccessTags() 
	{
		global $wpdb;
		
		if(isset($this->id)) 
		{
			$sql = "delete from ".MM_TABLE_APPLIED_ACCESS_TAGS." where access_type='member_type' and ref_id='{$this->id}'";
			$wpdb->query($sql);
		}
	}
 	
 	private function addAccountType()
	{
		global $wpdb;
		
		if(!isset($this->accountTypeId) || !isset($this->accountTypeId)) {
			return false;
		}
		
		$this->removeAccountType();
		
		$sql = "insert into ".MM_TABLE_ACCOUNT_MEMBER_TYPES." set " .
				"	member_type_id='{$this->id}'," .
				"	account_type_id='{$this->accountTypeId}'  ";
		
		$wpdb->query($sql);
	}
	
	private function removeAccountType()
	{
		global $wpdb;
		
		if(isset($this->id)) {
			$sql = "delete from ".MM_TABLE_ACCOUNT_MEMBER_TYPES." where member_type_id='{$this->id}'";
			$wpdb->query($sql);
		}
	}
	
	public function delete()
	{	
		global $wpdb, $mmSite;
		
		if(!$this->hasAssociations())
		{
			$sql = "DELETE FROM ".MM_TABLE_MEMBER_TYPES." WHERE id='%d' LIMIT 1";
			$results = $wpdb->query($wpdb->prepare($sql, $this->id));
			
			// remove account type relationships
			if($mmSite->isMM()) {
				$this->removeAccountType();
			}
			
			// remove access tag relationships
			$this->removeAccessTags();
			
			$this->removeProducts();
			
			if($results) {
				return true;
			}
		}
		
		return false;
	}
	
	public function hasAssociations()
	{
		global $wpdb;
		
		// check if member type is the default
		$sql = "select * from ".MM_TABLE_MEMBER_TYPES." where id='{$this->id}'";
		$row = $wpdb->get_row($sql);
		
		if($row->is_default == "1") {
			return true;
		}
		
		if($this->hasSubscribers()) {
			return true;
		}
		
		// check if member type is associated with one or more posts
		$sql = "SELECT count(*) as total FROM ".MM_TABLE_POSTS_ACCESS." WHERE access_type='member_type' AND access_id='{$this->id}'";
		$row = $wpdb->get_row($sql);
			
		if($row->total > 0) {
			return true;	
		}
		
		return false;
	}
	
	public function hasSubscribers()
	{
		global $wpdb;
		
		// check if member type is associated with one or more users
		if($this->isValid()) {
			$sql = "SELECT count(*) AS total FROM ".$wpdb->users." WHERE mm_member_type_id='{$this->id}'";
			$row = $wpdb->get_row($sql);
			
			if($row && $row->total > 0) {
				return true;
			}
		}
		
		return false;
	}
	
 	public function getAccountTypeId()
 	{
 		if($this->accountTypeId == "") 
 		{
	 		global $wpdb;
	 		
	 		$sql = "select a.* from ".MM_TABLE_ACCOUNT_TYPES." a, ".MM_TABLE_ACCOUNT_MEMBER_TYPES." ma where ma.member_type_id='{$this->id}' and ma.account_type_id=a.id";
	 		$row = $wpdb->get_row($sql);
	 		
	 		if(!$row) {
	 			$this->accountTypeId = 0;
	 		}
	 		else {
	 			$this->accountTypeId = $row->id;
	 		}
 		}
 		
 		return $this->accountTypeId;
 	}
	
	public function getAccessTags()
	{
		$tags = array();
		
		if($this->accessTags == "")
		{
			global $wpdb;
			
			$sql = "select a.* from ".MM_TABLE_APPLIED_ACCESS_TAGS." at, ".MM_TABLE_ACCESS_TAGS." a where at.access_type='member_type' and at.ref_id='{$this->id}' and at.access_tag_id=a.id";
				
			$rows= $wpdb->get_results($sql);
			
			if(!$rows) {
				return array();
			}
			
			if(count($rows)>0)
			{
				foreach($rows as $row) {
					$tags[$row->id] = $row->name;
				}
			}
			
			$this->accessTags = $tags;
		}
		
		return $tags;
	}
	
	public static function getMemberTypesPostAccess()
	{
		global $wpdb;
 		$sql = "SELECT mt.id, mt.name FROM ".MM_TABLE_MEMBER_TYPES." mt, ".MM_TABLE_POSTS_ACCESS." pa 
 					WHERE 
 						mt.status ='1' and 
 						mt.id = pa.access_id and 
 						pa.access_type='member_type'
 						";
 
 		$rows = $wpdb->get_results($sql);
 		
 		$types = array();
 		if($rows===false)
 		{
 			return $types;
 		}
 		if($rows) 
 		{
	 		foreach($rows as $row)
			{
				$types[$row->id] = $row->name;
			}
 		}
 		
 		return $types;
	}
	
	public static function getMemberTypesList($activeStatusOnly=false, $filterBySubType="")
	{
		global $wpdb;
 		
 		$types = array();
 		
 		if($activeStatusOnly) {
 			$sql = "SELECT * FROM ".MM_TABLE_MEMBER_TYPES." WHERE status ='1'";
 		}
 		else {
 			$sql = "SELECT * FROM ".MM_TABLE_MEMBER_TYPES;
 		}
 		
 		if($filterBySubType == self::$SUB_TYPE_FREE) {
 			$sql .= " AND is_free = '1'";
 		}
 		else if($filterBySubType == self::$SUB_TYPE_PAID) {
 			$sql .= " AND is_free != '1'";
 		}
 		
 		$rows = $wpdb->get_results($sql);
 		
 		if($rows===false)
 		{
 			return $types;
 		}
 		if($rows) 
 		{
	 		foreach($rows as $row)
			{
				$types[$row->id] = $row->name;
			}
 		}
 		
 		return $types;
	}
	
	// TODO MATT move to MM_CorePages class
	public function getAvailableTypes($core_page_type_id, $page_id)
	{
		global $wpdb;
		
		$sql = "select * from ".MM_TABLE_MEMBER_TYPES." mt where mt.id NOT IN (select ref_id from ".MM_TABLE_CORE_PAGES." cp where core_page_type_id='{$core_page_type_id}' and ref_type='member_type' and page_id!='{$page_id}' and ref_id>0)";
		return $wpdb->get_results($sql);
	}
	
	public function setWelcomeEmailEnabled($str) 
	{
		$this->welcomeEmailEnabled = $str;
	}
	
	public function getWelcomeEmailEnabled()
	{
		return $this->welcomeEmailEnabled;
	}
	
	public function setName($str) 
	{
		$this->name = $str;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setIsFree($str) 
	{
		$this->isFreeInd = $str;
	}
	
	public function isFree()
	{
		if($this->isFreeInd == "1") {
			return true;
		}
		else {
			return false;
		}
	}
 	
	public function setIsDefault($str) 
	{
		$this->isDefaultInd = $str;
	}
	
	public function isDefault()
	{
		return $this->isDefaultInd;
	}
	
	public function setIncludeOnReg($str) 
	{
		$this->includeOnReg = $str;
	}
	
	public function getIncludeOnReg()
	{
		return $this->includeOnReg;
	}
	
	public function setDescription($str) 
	{
		$this->description = $str;
	}
	
	public function getDescription()
	{
		return $this->description;
	}
	
	public function setProductIds($productIds) 
	{
		if(is_array($productIds)){
			$this->productIds = $productIds;
		}
	}
	
	public function getProductIds()
	{
		return $this->productIds;
	}
	
	public function setStatus($str) 
	{
		$this->status = $str;
	}
	
	public function getRegistrationProduct()
	{
		return $this->registrationProductId;
	}
	
	public function setRegistrationProduct($str) 
	{
		$this->registrationProductId = $str;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function getStatusName()
	{
		return MM_MemberStatus::getName($this->status);
	}
	
	public function setUpgradeId($str) 
	{
		$this->upgradeToId = $str;
	}
	
	public function getUpgradeId()
	{
		return $this->upgradeToId;
	}
	
	public function setDowngradeId($str) 
	{
		$this->downgradeToId = $str;
	}
	
	public function getDowngradeId()
	{
		return $this->downgradeToId;
	}
	
	public function setEmailSubject($str) 
	{
		$this->emailSubject = $str;
	}
	
	public function getEmailSubject()
	{
		return $this->emailSubject;
	}
	
	public function setEmailBody($str) 
	{
		$this->emailBody = $str;
	}
	
	public function getEmailBody()
	{
		return $this->emailBody;
	}
	
	public function setEmailFromId($str) 
	{
		$this->emailFromId = $str;
	}
	
	public function getEmailFromId()
	{
		return $this->emailFromId;
	}
	
	public function setBadgeUrl($str) 
	{
		$this->badgeUrl = $str;
	}
	
	public function getBadgeUrl()
	{
		return $this->badgeUrl;
	}
	
	public function setAccessTags($str) 
	{
		$this->accessTags = $str;
	}
	
	public function setAccountTypeId($str) 
	{
		$this->accountTypeId = $str;
	}
}
?>
