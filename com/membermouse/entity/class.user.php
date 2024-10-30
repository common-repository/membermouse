<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_User extends MM_Entity
{	
	private $username;
	private $password;
	private $email;
	private $firstName;
	private $lastName;
	private $phone;
	private $notes;
	private $registrationDate;
	private $customerId;
	private $mainOrderId;
	private $lastOrderId = "0";
	private $memberTypeId;
	private $status = "1";
	private $daysCalcMethod;
	private $daysCalcValue;
	private $billingAddress;
	private $billingCity;
	private $billingState;
	private $billingZip;
	private $billingCountry;
	private $shippingAddress;
	private $shippingCity;
	private $shippingState;
	private $shippingZip;
	private $shippingCountry;
	private $ipAddress;
	private $isRefunded = "0";
	private $memberType;
	private $changedToOverdue = false;
	public $doUpdateLL = true;
	public $doSendNotification = true;
	
 	protected function getData()
 	{
 		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->users." WHERE ID='{$this->id}' LIMIT 1;";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_User.getData(): error retrieving data for user with id of {$this->id}. Query run is ".$sql);
		}
 	}
 	
 	private function sendOverdueToCustomer(){
 		$user= new MM_User($this->id);
		$emailAccount  = MM_EmailAccount::getDefaultAccount();
		$context = new MM_Context($user, $emailAccount);
		
		$email = new MM_Email();
		$email->setContext($context);
		$email->setSubject("Account Overdue Notification");
		$email->setBody("Your account is now overdue and you can update your billing information on your <a href=\"[MM_Page_MyAccount]\">My Account</a> page.");
		$email->setToName($user->getFirstName());
		$email->setToAddress($user->getEmail());
		$email->setFromName($emailAccount->getName());
		$email->setFromAddress($emailAccount->getAddress());
		
		$response = $email->send();
		
		return $response;
 	}
 	
 	private function sendOverdueToAdmin(){
 		$user= new MM_User($this->id);
		$emailAccount  = MM_EmailAccount::getDefaultAccount();
		$context = new MM_Context($user, $emailAccount);
		
		$email = new MM_Email();
		$email->setContext($context);
		$email->setSubject("Account Overdue Notification");
		$email->setBody("Customer with ID {$this->id} has been set to overdue.");
		$email->setToName($emailAccount->getName());
		$email->setToAddress($emailAccount->getAddress());
		$email->setFromName("MemberMouse Notification System");
		$email->setFromAddress("noreply@".MM_OptionUtils::getOption("siteurl"));
		
		$response = $email->send();
		
		return $response;
 	}
 	
 	public function canAccessPage($pageName){
 		global $wpdb;
 		
 		$sql = "select 
 					count(*) as total_permissions, acc.role_id as role
 				from 
 					".MM_TABLE_EMAIL_ACCOUNTS." acc, ".MM_TABLE_PERMISSIONS." perms
 				where 
 					acc.user_id='{$this->id}' and 
 					acc.role_id=perms.role_id and 
 					perms.access_type='page'
 			";
 		
 		
 		$row = $wpdb->get_row($sql);
 		if($row->total_permissions<=0){
 			return true;
 		}
 		
 		$sql = "select 
 					count(*) as total_permissions
 				from 
 					".MM_TABLE_PERMISSIONS." perms
 				where 
 					perms.role_id ='{$row->role}' and 
 					perms.access_type='page' and 
 					perms.access_name= '{$pageName}'
 			";
 		
 		$row = $wpdb->get_row($sql);
 		if($row->total_permissions>0){
 			return true;
 		}
 		return false;
 	}
 	
	public function sendOverdueEmail()
	{
		$this->sendOverdueToCustomer();
		$this->sendOverdueToAdmin();
	}
 	
 	public function getDataByEmail()
 	{
 		global $wpdb;
 		
		$sql = "SELECT * FROM ".$wpdb->users." WHERE user_email='{$this->email}' LIMIT 1;";
		//echo $sql;
		$result = $wpdb->get_row($sql);
		
		if($result && isset($result->ID)) {
			LogMe::write("getDataByEmail() : ".json_encode($result));
			$this->setData($result);
		}
		else {
			parent::invalidate();
		}
 	}
 	
 	public static function getLastCustomer(){
 		global $wpdb;
 		$sql = "select id from {$wpdb->users} order by id desc limit 1";
 		$row = $wpdb->get_row($sql);
 		if(isset($row->id) && $row->id>0){
 			return $row->id;
 		}
 		return 0;
 	}
 	
 	public function getDataByCustomerId()
 	{
 		global $wpdb;
 		
		$sql = "SELECT * FROM ".$wpdb->users." WHERE mm_customer_id='{$this->customerId}' LIMIT 1;";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->id = $result->ID;
			$this->setData($result);
		}
		else {
			parent::invalidate();
		}
 	}
 	
 	public function getDataByOrderId()
 	{
 		global $wpdb;
 		
		$sql = "SELECT * FROM ".$wpdb->users." WHERE mm_main_order_id='{$this->mainOrderId}' LIMIT 1;";

		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
		}
 	}
 	
 	public function getDataByLastOrderId()
 	{
 		global $wpdb;
 		
		$sql = "SELECT * FROM ".$wpdb->users." WHERE mm_last_order_id='{$this->lastOrderId}' LIMIT 1";
		
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
 			$this->id = $data->ID;
	 		$this->username = $data->user_login;
			$this->password = MM_Utils::decryptPassword($data->mm_password);
			$this->email = $data->user_email;
			$this->firstName = $data->mm_first_name;
			$this->lastName = $data->mm_last_name;
			$this->phone = $data->mm_phone;
			$this->notes = $data->mm_notes;
			$this->registrationDate = $data->mm_registered;
			$this->customerId = $data->mm_customer_id;
			$this->mainOrderId = $data->mm_main_order_id;
			$this->lastOrderId = $data->mm_last_order_id;
			$this->memberTypeId = $data->mm_member_type_id;
			$this->status = $data->mm_status;
			$this->billingAddress = $data->mm_billing_address;
			$this->billingCity = $data->mm_billing_city;
			$this->billingState = $data->mm_billing_state;
			$this->billingZip = $data->mm_billing_zip;
			$this->billingCountry = $data->mm_billing_country;
			$this->shippingAddress = $data->mm_shipping_address;
			$this->shippingCity = $data->mm_shipping_city;
			$this->shippingState = $data->mm_shipping_state;
			$this->shippingZip = $data->mm_shipping_zip;
			$this->shippingCountry = $data->mm_shipping_country;
			$this->ipAddress = $data->mm_ip_address;
			$this->daysCalcMethod = $data->mm_days_calc_method;
			$this->daysCalcValue = $data->mm_days_calc_value;
			$this->isRefunded = $data->mm_is_refunded;
			parent::validate();
 		}
 		catch (Exception $ex) {
 			parent::invalidate();
 		}
 	}
 	
 	public function commitData()
 	{
 		global $wpdb,$current_user;
 		
		$doUpdate = isset($this->id) && $this->id != "" && intval($this->id) > 0;
		
		if($doUpdate) {
			$memberExists = $this->memberExists($doUpdate);
			
			if($memberExists->type == MM_Response::$SUCCESS && !$this->isFree() && $this->doUpdateLL == true)
			{
				$llResponse = MM_LimeLightService::updateCustomerInfo($this);
				
				if($llResponse->type == MM_Response::$ERROR)
				{
			LogMe::write("commitData(): ERROR : ".json_encode($llResponse));
					return $llResponse;
				}
			}
			else if($memberExists->type == MM_Response::$ERROR) {
			LogMe::write("commitData(): ERROR : ".json_encode($memberExists));
				return $memberExists;
			}
		}
		
		MM_Transaction::begin();
		$sql = "";
		try
		{	
			$preparedSql="";
			if(!$doUpdate) 
			{	
				$userRegisteredSql = "mm_registered=".((preg_match("/[0-9]{4}/", $this->registrationDate))?"'".$this->registrationDate."'":"NOW()").",";
				$sql = "INSERT INTO ".$wpdb->users." SET " .
					"	user_login='%s', ".
					" 	user_pass='%s', " .
					" 	user_nicename='%s', " .
					" 	display_name='%s', " .
					" 	user_email='%s', " .
					$userRegisteredSql.
					" 	mm_password='%s', " .
					"	mm_first_name='%s', " .
					"	mm_last_name='%s', " .
					"	mm_phone='%s', " .
					"	mm_notes='%s', " .
					"	user_registered=NOW(), " .
					"	mm_customer_id='%d', " .
					"	mm_main_order_id='%d', " .
					"	mm_last_order_id='%d', " .
					"	mm_member_type_id='%d', " .
					" 	mm_status='%s', " .
					" 	mm_days_calc_method='%s', " .
					" 	mm_days_calc_value='%s', " .
					"	mm_billing_country='%s', " .
					"	mm_billing_zip='%s', " .
					"	mm_billing_state='%s', " .
					"	mm_billing_city='%s', " .
					"	mm_billing_address='%s', " .
					"	mm_shipping_country='%s', " .
					"	mm_shipping_zip='%s', " .
					"	mm_shipping_state='%s', " .
					"	mm_shipping_city='%s', " .
					"	mm_shipping_address='%s', " .
					"	mm_is_refunded='%s', " .
					" 	mm_ip_address='%s' " .
					"";
		 	$preparedSql = $wpdb->prepare($sql, $this->username, wp_hash_password($this->password), $this->username, $this->username, $this->email, 
				 				MM_Utils::encryptPassword($this->password), $this->firstName, $this->lastName, $this->phone, $this->notes,
				 				$this->customerId, $this->mainOrderId, $this->lastOrderId, $this->memberTypeId, $this->status, $this->daysCalcMethod, $this->daysCalcValue, $this->billingCountry,
				 				$this->billingZip, $this->billingState, $this->billingCity, $this->billingAddress, $this->shippingCountry,
				 				$this->shippingZip, $this->shippingState, $this->shippingCity, $this->shippingAddress,$this->isRefunded, $this->ipAddress);
			}
			else
			{
				$passwordFieldSql = "	user_pass='".wp_hash_password($this->password)."', ";
				if($current_user->ID == $this->id){
					if(wp_check_password($this->password, $current_user->user_pass)!==false){
						$passwordFieldSql = "";
					}
				}
				else if($this->password==""){
					$passwordFieldSql = "";
				}
					
					
				$sql = "UPDATE ".$wpdb->users." SET " .
					"	user_login='%s', ".
					$passwordFieldSql.
					" 	user_nicename='%s', " .
					" 	display_name='%s', " .
					" 	user_email='%s', " .
					" 	mm_password='%s', " .
					"	mm_first_name='%s', " .
					"	mm_last_name='%s', " .
					"	mm_phone='%s', " .
					"	mm_notes='%s', " .
					"	mm_customer_id='%d', " .
					"	mm_main_order_id='%d', " .
					"	mm_last_order_id='%d', " .
					"	mm_member_type_id='%d', " .
					" 	mm_status='%s', " .
					" 	mm_days_calc_method='%s', " .
					" 	mm_days_calc_value='%s', " .
					"	mm_billing_country='%s', " .
					"	mm_billing_zip='%s', " .
					"	mm_billing_state='%s', " .
					"	mm_billing_city='%s', " .
					"	mm_billing_address='%s', " .
					"	mm_shipping_country='%s', " .
					"	mm_shipping_zip='%s', " .
					"	mm_shipping_state='%s', " .
					"	mm_shipping_city='%s', " .
					"	mm_shipping_address='%s', " .
					"	mm_is_refunded='%s', " .
					" 	mm_ip_address='%s' WHERE ID='".$this->id."'" .
					"";
		 	$preparedSql = $wpdb->prepare($sql, $this->username, $this->username, $this->username, $this->email, 
				 				MM_Utils::encryptPassword($this->password), $this->firstName, $this->lastName, $this->phone, $this->notes,
				 				$this->customerId, $this->mainOrderId, $this->lastOrderId, $this->memberTypeId, $this->status,$this->daysCalcMethod, $this->daysCalcValue,  $this->billingCountry,
				 				$this->billingZip, $this->billingState, $this->billingCity, $this->billingAddress, $this->shippingCountry,
				 				$this->shippingZip, $this->shippingState, $this->shippingCity, $this->shippingAddress, $this->isRefunded, $this->ipAddress);
		 	}
		 	
		 	LogMe::write("MM_User PreparedSQL: ".$preparedSql);
		 	
		 	
		 	$result = $wpdb->query($preparedSql);
 			   
			if($result === false)
		 	{
		 		MM_Transaction::rollback();
		 		return new MM_Response("ERROR: unable to create member (".$preparedSql.")", MM_Response::$ERROR);
		 	}
		 	
		 	
		 	if($this->changedToOverdue){
		 		$this->sendOverdueEmail();
		 	}
		 	
		 	if(!$doUpdate){
				$this->id = $wpdb->insert_id;
		 	}
			 
		 	if($this->doSendNotification){
		 		$instantNotification = new MM_InstantNotificationEngine();
			 	if(!$doUpdate) {
			 		LogMe::write("MM_User.commitData() : insert send INI ");
					$response = $instantNotification->sendNotification(MM_InstantNotificationEngine::$INI_EVENT_TYPE_CREATE_MEMBER,$this);
			 		LogMe::write("MM_User.commitData() : insert send INI...Done ");
					
			 	}
			 	else{
			 		LogMe::write("MM_User.commitData() : update send INI ");
			 		$response = $instantNotification->sendNotification(MM_InstantNotificationEngine::$INI_EVENT_TYPE_UPDATE_MEMBER,$this);
			 		
			 	}
		 	}
		 	
		}
		catch(Exception $ex)
		{
		 	MM_Transaction::rollback();
	 		return new MM_Response("ERROR: unable to create member", MM_Response::$ERROR);
		}
		 	
		MM_Transaction::commit();
		
		return new MM_Response();
 	}
 	
 	public function delete()
 	{
 		global $wpdb;
		
		if(!$this->hasActiveSubscriptions())
		{
			// remove access tags
			$sql = "DELETE FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE " .
					"	access_type='user' AND ".
					" 	ref_id='%d'; " .
 					"";
 		
	 		$preparedSql = $wpdb->prepare($sql, $this->getId()); 
			
			$wpdb->query($preparedSql);
			
			// delete user
			$sql = "DELETE FROM ".$wpdb->users." WHERE id='%d' LIMIT 1";
			$results = $wpdb->query($wpdb->prepare($sql, $this->id));
			
			if(!$results) {
				return new MM_Response("Unable to delete '".$this->getUsername()."'", MM_Response::$ERROR);
			}
		}
		
		return new MM_Response("'".$this->getUsername()."' deleted successfully");
 	}
 	
 	/*
 	 * out of the context of a transaction..
 	 */
 	
 	public function exists(){
 		global $wpdb;
 		$sql = "select ID from {$wpdb->users} where user_login='".$this->email."' OR user_email='".$this->email."' limit 1";
 		$row = $wpdb->get_row($sql);
 		if(isset($row->ID) && intval($row->ID)>0){
 			$this->id = $row->ID;
 			return true;
 		}
 		return false;
 	}
 	
 	public function getAffiliateAssociations($productId = 0){
 		global $wpdb;
 		
 		$where = "";
 		if(intval($productId)>0){
 			$where .= " AND  p.id='{$productId}' ";
 		}
 		
 		$sql = "select 
 					p.name, rr.affiliate_id, rr.ref_id, rr.ref_type,
					IF(rr.ref_type = 'access_tag', 
							(select name from ".MM_TABLE_ACCESS_TAGS." at where at.id=rr.ref_id),
							(select name from ".MM_TABLE_MEMBER_TYPES." at where at.id=rr.ref_id)
					) as access_type_name,
 					rr.sub_affiliate_id 
 				from 
 					".MM_TABLE_RETENTION_REPORTS." rr 
 					LEFT JOIN ".MM_TABLE_PRODUCTS."  p on rr.product_id=p.id {$where}
 				where 
 					rr.user_id='{$this->id}' and  
 					(rr.affiliate_id !='' OR rr.sub_affiliate_id != '')
 					
 				";
// 		echo $sql;
// 		exit;
 		$rows = $wpdb->get_results($sql);
 		if(is_array($rows) && count($rows)>0){
 			$affiliates = array();
 			$index =0;
 			foreach($rows as $row){
				if(!empty($row->affiliate_id)){
					if(!isset($affiliates[$index])){
						$affiliates[$index] = array();
					}
					$affiliates[$index]['affiliate_id'] = $row->affiliate_id;
					$affiliates[$index]['product'] = $row->name;
					$affiliates[$index]['access_type_name'] = $row->access_type_name;
				}
				if(!empty($row->sub_affiliate_id)){
					$affiliates[$index]['sub_affiliate_id'] = $row->sub_affiliate_id;
				}	
				$index++;					
 			}
 			return $affiliates;
 		}
 		return array();
 	}
 	
 	public function getCustomDataByName($fieldName){
 		$cf = new MM_CustomField();
 		$cf->setDataByFieldName($fieldName);
 		if($cf->isValid()){
	 		$cfd = new MM_CustomFieldData();
	 		$cfd->setDataByUserFieldId($this->id, $cf->getId());
 			if($cfd->isValid()){
 				return $cfd->getValue();
 			}
 		}
 		return "";
 	}
 	
 	public function setCustomData($customFieldId, $value){
 		$cfd = new MM_CustomFieldData();
 		$cfd->setDataByUserFieldId($this->id, $customFieldId);
 		
 		if($cfd->isValid()){
 			if($value==""){
 				return $cfd->delete();	
 			}
 			else{
 				$cfd->setValue($value);
 				return $cfd->commitData();
 			}
 		}
 		else if($value!=""){
 			$cfd->setUserId($this->id);
 			$cfd->setCustomFieldId($customFieldId);
 			$cfd->setValue($value);
 			return $cfd->commitData();
 		}
 		return new MM_Response("Could not update users custom data.", MM_Response::$ERROR);
 	}
 	
 	public function memberExists($doUpdate=false) 
 	{
 		global $wpdb;
 		LogMe::write("memberExists() : ".$this->username);
 		// check if user with username already exists
 		if(isset($this->username))
 		{
			$sql = "SELECT 
						count(*) as total 
					FROM 
						".$wpdb->users." 
					WHERE 
						id != '".$this->id."' AND 
						(
							user_login='".$this->username."' OR 
							user_email='".$this->username."' OR 
							user_login='".$this->email."' OR 
							user_email='".$this->email."'  
						) 		
			";
			
			LogMe::write($sql);
		
			$result = $wpdb->get_row($sql);
			
			if($result)
			{
				if(intval($result->total)>0)
				{
					MM_Transaction::rollback();
			 		return new MM_Response("A member with username '".$this->username."' or email '".$this->email."' already exists.", MM_Response::$ERROR);
				}
			}
 		}
		
		return new MM_Response();
 	}
 	
 	public static function getAllMembers($activeOnly=false, $useCustomerIdAsKey=false)
 	{
 		global $wpdb;
 		$sql = "select id, user_login from {$wpdb->users} ";
 		if($useCustomerIdAsKey){
 			$sql = "select mm_customer_id as id, CONCAT(user_login,' [',mm_customer_id,'] ') as user_login from {$wpdb->users} ";
 		}
 		if($activeOnly)
 		{
 			$sql.= " where mm_status='".MM_MemberStatus::$ACTIVE."' ";
 		}
 		$rows = $wpdb->get_results($sql);
 		if($rows===false)
 		{
 			return array();
 		}
 		$members = array();
 		foreach($rows as $row)
 		{
 			$members[$row->id] = $row->user_login;
 		}
 		return $members;
 	}
 	
 	public function addAccessTag($tagId, $orderId="", $productId="")
 	{
 		global $wpdb;
 		
 		if($orderId == "") {
 			$orderId = MM_TransactionEngine::$MM_DFLT_ORDER_ID;
 		}
 		
 		$sql = "select count(*) as total
 				 from 
 				 	".MM_TABLE_APPLIED_ACCESS_TAGS."
 				  where 
 				  	access_type='user' AND
 				  	access_tag_id='{$tagId}' AND
 				  	ref_id ='".$this->getId()."' 
 				 ";
 	
 		$row = $wpdb->get_row($sql);
 		unset($sql);
 		
 		$preparedSql = "";
 		if($row->total>0){
 			$sql = "update ".MM_TABLE_APPLIED_ACCESS_TAGS." set 
 								order_id='{$orderId}',  
 								is_refunded='0', status='1', product_id='{$productId}',
 								apply_date=NOW() 
 						where  " .
 						"	access_type='user' AND ".
						" 	access_tag_id='%d' AND " .
						" 	ref_id='%d' "; 
 		$preparedSql = $wpdb->prepare($sql, $tagId, $this->getId()); 
 		}
 		else{
	 		$sql = "INSERT INTO ".MM_TABLE_APPLIED_ACCESS_TAGS." SET " .
						"	access_type='user',  status='1', ".
						" 	access_tag_id='%d', " .
						" 	ref_id='%d', " .
						" 	order_id='%d', " .
						" 	product_id='%d', " .
						"	apply_date=NOW() " .
	 					"";
 		$preparedSql = $wpdb->prepare($sql, $tagId, $this->getId(), $orderId,$productId); 
	 		
 		}
 		
		$result = $wpdb->query($preparedSql);
		LogMe::write("addAccessTag() : ".$preparedSql);
		if(!$result)
		{
			$tagName = "'".$tagId."'";
			$tag = new MM_AccessTag($tagId);
			if($tag->isValid()){
				$tagName = $tag->getName()." ({$tagId})";
			}
			return new MM_Response("Unable to apply access tag {$tagName} to member '".$this->username."'. ", MM_Response::$ERROR);
		}
		
		return new MM_Response();
 	}
 	
 	public function removeAccessTag($tagId)
 	{
 		global $wpdb;
 		
 		$sql = "UPDATE ".MM_TABLE_APPLIED_ACCESS_TAGS."
					set
						status='0' 
 						WHERE " .
					"	access_type='user' AND ".
					" 	access_tag_id='%d' AND " .
					" 	ref_id='%d'; " .
 					"";
 		
// 		$sql = "DELETE FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE " .
//					"	access_type='user' AND ".
//					" 	access_tag_id='%d' AND " .
//					" 	ref_id='%d'; " .
// 					"";
 		
 		$preparedSql = $wpdb->prepare($sql, $tagId, $this->getId()); 
		
		$result = $wpdb->query($preparedSql);
		
		if(!$result)
		{
			return new MM_Response("Unable to remove access tag with ID '".$tagId."' from member '".$this->username."'.", MM_Response::$ERROR);
		}
		
		return new MM_Response();
 	}
 	
 	public function getAccessTagOrderId($tagId) 
 	{
 		global $wpdb;
 		
 		$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE " .
					"	access_type='user' AND ".
					" 	access_tag_id='%d' AND " .
					" 	ref_id='%d'  LIMIT 1; " .
 					"";
 		
 		$preparedSql = $wpdb->prepare($sql, $tagId, $this->getId()); 
		LogMe::write("getAccessTagOrderId() : ".$preparedSql);
		$result = $wpdb->get_row($preparedSql);
		
		if(!$result)
		{
			return MM_TransactionEngine::$MM_DFLT_ORDER_ID;
		}
		
		return $result->order_id;
 	}
 	
 	public function hasActiveSubscriptions()
 	{
 		// determine if member has active subscriptions associated with their member type
 		$mt = new MM_MemberType($this->memberTypeId);
 		
 		if(!$mt->isFree() && $this->status != MM_MemberStatus::$CANCELED) {
 			return true;
 		}
 		
 		$tags = $this->getAccessTags();
 		
 		// determine if member has active subscriptions associated with their access tags
 		foreach($tags as $data)
 		{
 			$tag = new MM_AccessTag($data->access_tag_id);
 			
 			if(!$tag->isFree()) {
 				return true;
 			}
 		}
 		
 		return false;
 	}
 	
 	public function isAdmin()
 	{
 		$capabilities = get_user_meta($this->id, "wp_capabilities", true);
 		
 		if(isset($capabilities["administrator"])) {
 			return $capabilities["administrator"] == "1";
 		}
 		else {
 			return false;
 		}
 	}
 	
 	public function hasAccessTag($tagId, $includeRefunded=true, $includeDeactivated=true)
 	{
 	 	global $wpdb;
 	 	
		$sql = "SELECT 
					count(*) as total 
			  FROM 
			  	".MM_TABLE_APPLIED_ACCESS_TAGS.
			" WHERE 
				access_tag_id='{$tagId}' AND 
				(
					(
						ref_id='{$this->id}' AND 
					  	access_type='user'
					)
				  	OR
				  	(
					  	ref_id='".$this->getMemberTypeId()."' AND 
						access_type='member_type'
					)
				)
				 
			";
		if(!$includeRefunded){
			$sql.= " AND is_refunded='0' ";
		}
		if(!$includeDeactivated){
			$sql.= " AND status='1' ";
		}
		
 	 	$row = $wpdb->get_row($sql);
 	 	
 	 	if(is_null($row)) {
 	 		return false;
 	 	}
 	 	
 	 	return ($row->total>0);
 	}
	 	
	public function getDaysAsMember() 
	{
		if($this->daysCalcMethod=="fixed"){
			return intval($this->daysCalcValue);
		}
		else if($this->daysCalcMethod == "custom_date"){
			$start = Date("Y-m-d", strtotime($this->daysCalcValue)); 
			$end = Date("Y-m-d");
			$start_ts = strtotime($start);
			$end_ts = strtotime($end);
			$diff = $end_ts - $start_ts;
			
			// round returns a - 0
			$days = round($diff / 86400);
			if($days<0){
				$days*=-1;
			}
			return $days;
		}
		else{
			$start = Date("Y-m-d", strtotime($this->getRegistrationDate())); 
			$end = Date("Y-m-d");
			$start_ts = strtotime($start);
			$end_ts = strtotime($end);
			$diff = $end_ts - $start_ts;
			
			// round returns a - 0
			$days = round($diff / 86400);
			if($days<0){
				$days*=-1;
			}
			return $days;
		}
	}
	
	public static function getUserByAccessTagOrderId($orderId){
		global $wpdb;
		
		$sql = "select ref_id from ".MM_TABLE_APPLIED_ACCESS_TAGS." where order_id='{$orderId}' and access_type='user' limit 1";
		
		$row = $wpdb->get_row($sql);
		return (isset($row->ref_id))?$row->ref_id:0;
	}
 	
 	public function getAccessTags($paidOnly=false, $includeRefunded=false)
 	{
 	 	global $wpdb;
 	 	
 	 	if($paidOnly) {
			$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS.
				" WHERE ref_id='{$this->id}' AND order_id != '".MM_TransactionEngine::$MM_DFLT_ORDER_ID."'" .
				" AND access_type='user' and status='1' ";
 	 	}
 	 	else {
 	 		$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." WHERE ref_id='{$this->id}' AND access_type='user' and status='1'";
 	 	}
 	 	
 	 	if(!$includeRefunded){
 	 		$sql.= " AND is_refunded='0' ";
 	 	}
 	 	
 	 	$rows = $wpdb->get_results($sql);
 	 	
 	 	if(is_null($rows)) {
 	 		return false;
 	 	}
 	 	
 	 	return $rows;
	}
 	 
 	public function getAccessTagNames()
 	{
 	 	global $wpdb;
 	 	
		$sql = "SELECT b.name FROM ".MM_TABLE_APPLIED_ACCESS_TAGS." a, ".MM_TABLE_ACCESS_TAGS." b WHERE a.ref_id='{$this->id}' and a.is_refunded='0' and status='1' and a.access_type='user' and a.access_tag_id=b.id";
		$tags = $wpdb->get_results($sql);
 	 	
 	 	if($tags)
 	 	{
 	 		$tagStr = "";
 	 		$ctr = 0;
 	 		
 	 		foreach($tags as $tag) 
 	 		{
 	 			if($ctr > 0) {
 	 				$tagStr .= ", ";
 	 			}
 	 			
 	 			$tagStr .= $tag->name;
 	 			$ctr++;
 	 		}
 	 		
 	 		return $tagStr;
 	 	}
 	 	else 
 	 	{
 	 		return "";
 	 	}
 	}

 	public function isFree()
 	{
 		if(!isset($this->memberType)) {
 			$this->memberType = new MM_MemberType($this->memberTypeId);
 		}
 		
 		return $this->memberType->isFree();
 	}
 	
 	public function getAccessTagOrder(){
 		global $wpdb;
 		
		$sql = "SELECT * FROM ".MM_TABLE_APPLIED_ACCESS_TAGS.
			" WHERE ref_id='{$this->id}' AND order_id>0 and order_id != '".MM_TransactionEngine::$MM_DFLT_ORDER_ID."'" .
			" AND access_type='user' order by apply_date desc limit 1";
		
		$accessTag = $wpdb->get_row($sql);
		if(is_object($accessTag) && isset($accessTag->access_tag_id)){
 			$orderId = $this->getAccessTagOrderId($accessTag->access_tag_id);
 			if(intval($orderId)>0){
 				return $orderId;
 			}
 		}
 		return 0;
 	}
 	
 	public function hasCardOnFile()
 	{
 		if($this->mainOrderId != MM_TransactionEngine::$MM_DFLT_ORDER_ID){
 			return true;
 		}
 		else{
 			$accessTags = $this->getAccessTags(true);
 			if(is_array($accessTags)){
 				foreach($accessTags as $tag){
 					$orderId = $this->getAccessTagOrderId($tag->access_tag_id);
 					if(intval($orderId)>0){
 						return true;
 					}
 				}
 			}
 		}
 		return false;
 	}
	
	public function isActive()
	{
		if($this->getStatus() != MM_MemberStatus::$CANCELED) {
			return true;
		}
		else {
			return false;
		}
	}
 	
 	/** Getters and Setters **/
 	public function setUsername($str)
 	{
 	 	$this->username = $str;
 	}
 	 
 	public function getUsername()
 	{
 	 	return $this->username;
 	}
 	 
 	public function setPassword($str)
 	{
 		$this->password = $str;
 	}
 	 
 	public function getPassword()
 	{
 	 	return $this->password;
 	}
 	 
 	public function setEmail($str)
 	{
 	 	$this->email = $str;
 	}
 	 
 	public function getEmail()
 	{
 	 	return $this->email;
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
 	 
 	public function getFullName()
 	{
 		$name = "";
 		
 		if($this->firstName != "") {
 			$name .= $this->firstName;
 		}
 		
 		if($this->lastName != "") {
 			$name .= " ".$this->lastName;
 		}
 		
 	 	return $name;
 	}
 	 
 	public function setPhone($str)
 	{
 	 	$this->phone = $str;
 	}
 	 
 	public function getPhone()
 	{
 	 	return $this->phone;
 	}
 	
 	public function setNotes($str)
 	{
 	 	$this->notes = $str;
 	}
 	 
  	public function getNotes()
 	{
 	 	return $this->notes;
 	}
 	 
 	public function setRegistrationDate($date)
 	{
 	 	$this->registrationDate = $date;
 	}
 	 
 	public function getRegistrationDate($doFormat=false)
 	{
 	 	if($doFormat == false) {
 	 		return $this->registrationDate;
 	 	}
 	 	else {
 	 		return date("M d, Y g:i a", strtotime($this->registrationDate));
 	 	}
 	}
 	 
 	public function setIsRefunded($str)
 	{
 	 	$this->isRefunded = $str;
 	}
 	 
 	public function getIsRefunded()
 	{
 	 	return $this->isRefunded;
 	}
 	 
 	public function setCustomerId($str)
 	{
 	 	$this->customerId = $str;
 	}
 	 
 	public function getCustomerId()
 	{
 	 	return $this->customerId;
 	}
 	 
 	public function setMainOrderId($str)
 	{
 	 	$this->mainOrderId = $str;
 	}
 	 
 	public function getMainOrderId()
 	{
 	 	return $this->mainOrderId;
 	}
 	
 	public function setLastOrder($str)
 	{
 		$this->lastOrderId = $str;
 	}

 	public function getLastOrderId($includeAccessTagOrders=false)
 	{
 		//1. Check for existing  lastOrderId
 		//2. Look local DB for last order id
 		//3. Finally, do an order_view if nothing is found as a last resort.
 		if($this->lastOrderId == "0") 
 		{
 			// TODO when LL does auto-profile creation, we can remove this code.
 			global $wpdb;
 			$sql = "select mm_last_order_id from {$wpdb->users} where id='{$this->id}'";
 			$row = $wpdb->get_row($sql);
 			if(isset($row->mm_last_order_id) && intval($row->mm_last_order_id)>0){
 				$this->lastOrderId = $row->mm_last_order_id;
 			}
 			else{
 				if($this->mainOrderId>0){
		 			$order = new MM_Order($this->mainOrderId);
					if(!$order->isValid()) {
						$this->lastOrderId = $this->mainOrderId;
					}
					$this->lastOrderId = $order->getLastRebillId();
					
		 			if($this->lastOrderId != $this->mainOrderId){
		 				$user = new MM_User($this->id);
		 				if($user->isValid()){
		 					$user->setLastOrder($this->lastOrderId);
		 					$user->commitData();
		 				}
		 			}
 				}
 			}
 			
 			
 			if($includeAccessTagOrders){
	 			if(intval($this->lastOrderId)<=0){
	 				$this->lastOrderId = $this->getAccessTagOrder();	
	 			}
 			}
 		}
 		return $this->lastOrderId;
 	}
 	 
 	public function setMemberTypeId($str)
 	{
 	 	$this->memberTypeId = $str;
 	}
 	 
 	public function getMemberTypeId()
 	{
 	 	return $this->memberTypeId;
 	}
 	
 	public function getMemberTypeName()
 	{
 		$mt = new MM_MemberType($this->memberTypeId);
 	 	
 		if($mt->isValid()) {
 	 		return $mt->getName();
 	 	} 
 	 	else {
 	 		return MM_NO_DATA;
 	 	}
 	}
 	 
 	public function setDaysCalcValue($str)
 	{
 	 	$this->daysCalcValue = $str;
 	}
 	 
 	public function getDaysCalcValue()
 	{
 	 	return $this->daysCalcValue;
 	}
 	 
 	public function setDaysCalcMethod($str)
 	{
 	 	$this->daysCalcMethod = $str;
 	}
 	 
 	
 	
 	public function getDaysCalcMethod()
 	{
 	 	return $this->daysCalcMethod;
 	}
 	 
 	public function setStatus($str, $onlyNotify=false)
 	{
 		if($str != $this->status && $str == MM_MemberStatus::$OVERDUE){
 			$this->changedToOverdue = true;
 			if(!$onlyNotify){
 				$this->status = $str;
 			}
 		}
 		else if($str != $this->status && ($str == MM_MemberStatus::$CANCELED || $str == MM_MemberStatus::$PAUSED)){
 			
 			$this->setDaysCalcValue($this->getDaysAsMember());
 			$this->setDaysCalcMethod(MM_DaysCalculationTypes::$FIXED);
 			$this->status = $str;
 		}
 		else if(($this->status == MM_MemberStatus::$CANCELED || $this->status == MM_MemberStatus::$PAUSED) && $str==MM_MemberStatus::$ACTIVE){
 			
 			$days = $this->getDaysAsMember();
 			LogMe::write("RETURNING STATUS TO ACTIVE : {$days}");
 			$newDate = strtotime("-".$days." days");
 			$this->setDaysCalcValue(Date("Y-m-d h:i:s",$newDate));
 			$this->setDaysCalcMethod(MM_DaysCalculationTypes::$CUSTOM);
 			$this->status = $str;
 		}
 		else{
 			
 			$this->status = $str;
 		}
 	}
 	 
 	public function getStatus()
 	{
 	 	return $this->status;
 	}
 	 
 	public function getStatusName()
 	{
 	 	switch($this->status){
 	 		case MM_MemberStatus::$ACTIVE:
 	 			return "Active";
 	 		case MM_MemberStatus::$CANCELED:
 	 			return "Canceled";
 	 		case MM_MemberStatus::$LOCKED:
 	 			return "Locked";
 	 		case MM_MemberStatus::$PAUSED:
 	 			return "Paused";
 	 		case MM_MemberStatus::$OVERDUE:
 	 			return "Overdue";
 	 	}
 	 	return "";
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
 	 
 	public function setBillingCountry($code)
 	{
 	 	$this->billingCountry = $code;
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
 	 
 	public function setIpAddress($str)
 	{
 	 	$this->ipAddress = $str;
 	}
 	 
 	public function getIpAddress()
 	{
 	 	return $this->ipAddress;
 	}
}
?>
