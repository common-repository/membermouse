<?php
/***
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MM_Install
 {
 	function __construct()
 	{
 		global $mmSite;
		if(class_exists("MM_Site")){
	 		if(!($mmSite instanceof MM_Site)) {
	 			$mmSite = new MM_Site();
	 		}
		}
 	}
 	
 	private function rowExists($table, $whereColumn, $whereValue){
 		global $wpdb;
 		
 		$sql = "select count(*) as total from {$table} where {$whereColumn}='".$whereValue."'";
 		$row = $wpdb->get_row($sql);
 		return $row->total>0;
 	}
 	
 	public function update()
 	{
 		global $wpdb, $mmSite,$current_user;
 		
 		if(isset(MM_OptionUtils::$OPTION_KEY_MINOR_VERSION)){
        	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_MINOR_VERSION,"");
 		}
 		
 		$sql = array();
 	
//	 	$dataJson = array(
//	 		'hidden_onsite'=>'1',
//	 		'login'=>'',
//	 		'transkey'=>'',
//	 		'hidden_paymentObject'=>'MM_AuthorizeService',
//	 	);
//	 	$tmpSql = "insert into ".MM_TABLE_CAMPAIGN_OPTIONS." set name='Authorize.Net', attr='".json_encode($dataJson)."', setting_type='gateway'";
//	 	$wpdb->query($tmpSql);
//	 	unset($dataJson);
	 	
 		if(!$this->field_exists("phone", MM_TABLE_EMAIL_ACCOUNTS)){
	 		$sql[] = "alter table ".MM_TABLE_EMAIL_ACCOUNTS." add column phone VARCHAR(255) NULL DEFAULT NULL after email;";
 		}
 	
 		if(!$this->field_exists("role_id", MM_TABLE_EMAIL_ACCOUNTS)){
	 		$sql[] = "alter table ".MM_TABLE_EMAIL_ACCOUNTS." add column role_id INT NULL DEFAULT NULL after email;";
 		}
 	
 		if(!$this->field_exists("user_id", MM_TABLE_EMAIL_ACCOUNTS)){
	 		$sql[] = "alter table ".MM_TABLE_EMAIL_ACCOUNTS." add column user_id INT NULL DEFAULT NULL after email;";
 		}
 	
 		if(!$this->field_exists("fullname", MM_TABLE_EMAIL_ACCOUNTS)){
	 		$sql[] = "alter table ".MM_TABLE_EMAIL_ACCOUNTS." add column fullname VARCHAR(255) NULL DEFAULT NULL after email;";
 		}
 	
 		if(!$this->rowExists(MM_TABLE_ROLES, "id", MM_Role::$CUSTOMER_SUPPORT)){
 			$sql[] = "INSERT INTO `".MM_TABLE_ROLES."` set name='Customer Support', id='".MM_Role::$CUSTOMER_SUPPORT."';";
 		}
 	
 		if(!$this->rowExists(MM_TABLE_ROLES, "id", MM_Role::$ADMINISTRATOR)){
 			$sql[] = "INSERT INTO `".MM_TABLE_ROLES."` set name='Administrator', id='".MM_Role::$ADMINISTRATOR."';";
 		}
 	
 		if(!$this->rowExists(MM_TABLE_PERMISSIONS, "access_type", "page")){
 			$sql[] = "INSERT INTO `".MM_TABLE_PERMISSIONS."` set access_type='page', role_id='".MM_Role::$CUSTOMER_SUPPORT."', access_name='".MM_MODULE_MANAGE_MEMBERS."', date_added=NOW(), date_modified=NOW();";
 		}
 		
 		if(!$this->rowExists(MM_TABLE_SMARTTAGS, "name", "MM_FastForwardMembership")){
 			$sql[] = "INSERT INTO `".MM_TABLE_SMARTTAGS."` (`id`, `group_id`, `name`, `visible`) VALUES(704, 7, 'MM_FastForwardMembership', 1);";
 		}
		
 		if(!$this->rowExists(MM_TABLE_CORE_PAGE_TAG_REQUIREMENTS, "id", 8)){
 			$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(8, ".MM_CorePageType::$CANCELLATION.", 703, 1);";
 		}
 		
// 		if(!$this->rowExists(MM_TABLE_SMARTTAGS, "name", "MM_Content_LogoutForm")){
// 			$sql[] = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(107, 1, 'MM_Content_LogoutForm', 1);";
// 		}
// 		
// 		if(!$this->rowExists(MM_TABLE_CORE_PAGE_TAG_REQUIREMENTS, "core_page_type_id", MM_CorePageType::$LOGOUT_PAGE))){
//	 		$sql[] = "INSERT INTO `mm_corepage_tag_requirements` (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(7, ".MM_CorePageType::$LOGOUT_PAGE.", 107, 1);";
// 		}
			
	 	$query = "select count(*) as total from {$wpdb->posts} where post_name ='logout'";
	 	$row = $wpdb->get_row($query);
 		if($row->total<=0){
	 		$query = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/logout.html.php", array()))."', 'Member Logout', '".$this->getPostName('logout')."');";
	 			
	 		$wpdb->query($query);
	 		$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$LOGOUT_PAGE);
 		}
 		
 		$query = "select count(*) as total from ".MM_TABLE_CORE_PAGE_TYPES." where id='".MM_CorePageType::$LOGOUT_PAGE."'";
 		$row = $wpdb->get_row($query);
 		if($row->total<=0){
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$LOGOUT_PAGE."','Logout','1');";
 		}
 	
 		$query = "select count(*) as total from ".MM_TABLE_CORE_PAGES." where core_page_type_id='".MM_CorePageType::$LOGOUT_PAGE."'";
 		$row = $wpdb->get_row($query);
 		if($row->total<=0){
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (core_page_type_id) VALUES ('".MM_CorePageType::$LOGOUT_PAGE."');";
 		}
 		
		$query = "select count(*) as total from ".MM_TABLE_MEMBER_STATUS_TYPES." where id='".MM_MemberStatus::$PAUSED."'";
		$row = $wpdb->get_row($query);
		if($row->total<=0){
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$PAUSED."','Paused');";
		}
 		
		$query = "select count(*) as total from ".MM_TABLE_MEMBER_STATUS_TYPES." where id='".MM_MemberStatus::$PENDING."'";
		$row = $wpdb->get_row($query);
		if($row->total<=0){
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$PENDING."','Pending');";
		}
		
		$query = "select count(*) as total from ".MM_TABLE_MEMBER_STATUS_TYPES." where id='".MM_MemberStatus::$OVERDUE."'";
		$row = $wpdb->get_row($query);
		if($row->total<=0){
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$OVERDUE."','Overdue');";
		}
		
		$query = "select count(*) as total from ".MM_TABLE_ERROR_TYPES." where id='".MM_ErrorType::$ACCOUNT_PAUSED."'";
		$row = $wpdb->get_row($query);
		if($row->total<=0){
	 		$sql[] = "insert into ".MM_TABLE_ERROR_TYPES." (id, name) values ('".MM_ErrorType::$ACCOUNT_PAUSED."','Account Paused');";
		}
		
		$query = "select count(*) as total from ".MM_TABLE_ERROR_TYPES." where id='".MM_ErrorType::$ACCOUNT_OVERDUE."'";
		$row = $wpdb->get_row($query);
		if($row->total<=0){
	 		$sql[] = "insert into ".MM_TABLE_ERROR_TYPES." (id, name) values ('".MM_ErrorType::$ACCOUNT_OVERDUE."','Account Overdue');";
		}
 	
 		if(!$this->field_exists("is_refunded", MM_TABLE_APPLIED_ACCESS_TAGS)){
	 		$sql[] = "alter table ".MM_TABLE_APPLIED_ACCESS_TAGS." add column is_refunded TINYINT NULL DEFAULT '0' after order_id;";
 		}
 	
 		if(!$this->field_exists("status", MM_TABLE_APPLIED_ACCESS_TAGS)){
	 		$sql[] = "alter table ".MM_TABLE_APPLIED_ACCESS_TAGS." add column status TINYINT NULL DEFAULT '1' after order_id;";
 		}
 	
 		if(!$this->field_exists("days_calc_value", MM_TABLE_APPLIED_ACCESS_TAGS)){
	 		$sql[] = "alter table ".MM_TABLE_APPLIED_ACCESS_TAGS." add days_calc_value varchar(255) NULL DEFAULT NULL after order_id;";
 		}
 		
 		if(!$this->field_exists("days_calc_method", MM_TABLE_APPLIED_ACCESS_TAGS)){
	 		$sql[] = "alter table ".MM_TABLE_APPLIED_ACCESS_TAGS." add days_calc_method enum('join_date','custom_date','fixed') DEFAULT 'join_date' after order_id;";
 		}
 		
 		if(!$this->field_exists("show_on_reg", MM_TABLE_CAMPAIGN_OPTIONS)){
	 		$sql[] = "alter table ".MM_TABLE_CAMPAIGN_OPTIONS." add column show_on_reg TINYINT NULL DEFAULT NULL after setting_type;";
 		}
 		if(!$this->field_exists("ref_id", MM_TABLE_RETENTION_REPORTS)){
	 		$sql[] = "alter table ".MM_TABLE_RETENTION_REPORTS." add column ref_id INT after user_id;";
 		}
 		if(!$this->field_exists("payment_method_id", MM_TABLE_RETENTION_REPORTS)){
	 		$sql[] = "alter table ".MM_TABLE_RETENTION_REPORTS." add column payment_method_id INT after user_id;";
 		}
 		
 		if(!$this->field_exists("product_id", MM_TABLE_APPLIED_ACCESS_TAGS)){
	 		$sql[] = "alter table ".MM_TABLE_APPLIED_ACCESS_TAGS." add column product_id INT after order_id;";
 		}
 		
 		if(!$this->field_exists("ref_type", MM_TABLE_RETENTION_REPORTS)){
	 		$sql[] = "alter table ".MM_TABLE_RETENTION_REPORTS." add column ref_type enum('access_tag','member_type') after user_id;";
 		}
 		if(!$this->field_exists("last_rebill_date", MM_TABLE_RETENTION_REPORTS)){
	 		$sql[] = "alter table ".MM_TABLE_RETENTION_REPORTS." add column last_rebill_date timestamp NULL default NULL after user_id;";
 		}
 		
 		if(!$this->field_exists("trial_amount", MM_TABLE_PRODUCTS)){
	 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column trial_amount float NULL default NULL after rebill_product_id;";
 		}
 		if(!$this->field_exists("payment_id", MM_TABLE_PRODUCTS)){
	 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column payment_id INT NULL default NULL after rebill_product_id;";
 		}
 		if(!$this->field_exists("duration", MM_TABLE_PRODUCTS)){
	 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column duration float NULL default NULL after rebill_product_id;";
 		}
 		if(!$this->field_exists("trial_duration", MM_TABLE_PRODUCTS)){
	 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column trial_duration INT NULL default NULL after rebill_product_id;";
 		}
 		if(!$this->field_exists("trial_frequency", MM_TABLE_PRODUCTS)){
	 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column trial_frequency enum('days','months','weeks','years') default 'months' after rebill_product_id;";
 		}
 		if(!$this->field_exists("rebill_frequency", MM_TABLE_PRODUCTS)){
	 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column rebill_frequency  enum('days','months','weeks','years') default 'months'  after rebill_product_id;";
 		}
 		if(!$this->field_exists("mm_last_four", $wpdb->users)){
	 		$sql[] = "alter table ".$wpdb->users." add column mm_last_four  smallint null default null  after mm_billing_address;";
 		}
 		
 		
 		if(!$this->field_exists("is_smart_content", MM_TABLE_POSTS_ACCESS)){
	 		$sql[] = "alter table ".MM_TABLE_POSTS_ACCESS." add column is_smart_content tinyint default '0' after days;";
 		}
 		if(!$this->field_exists("name", MM_TABLE_API_KEYS)){
	 		$sql[] = "alter table ".MM_TABLE_API_KEYS." add column name varchar(255) after id;";
 		}
 		if(!$this->field_exists("ip", MM_TABLE_ACCESS_LOGS)){
	 		$sql[] = "alter table ".MM_TABLE_ACCESS_LOGS." add column ip varchar(355);";
 		}
 		if(!$this->field_exists("date_modified", MM_TABLE_ACCESS_LOGS)){
	 		$sql[] = "alter table ".MM_TABLE_ACCESS_LOGS." add column date_modified TIMESTAMP;";
 		}
 		if(!$this->field_exists("registration_product_id", MM_TABLE_MEMBER_TYPES)){
	 		$sql[] = "alter table ".MM_TABLE_MEMBER_TYPES." add column registration_product_id INT default NULL after id;";
	 		$sql[] = "alter table ".MM_TABLE_MEMBER_TYPES." drop column product_id;";
 		}
 		$sql[] = "delete from ".MM_TABLE_PRODUCTS." where id='0';";
 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." modify id INT NOT NULL AUTO_INCREMENT;";
 		if(!$this->field_exists("product_id", MM_TABLE_PRODUCTS)){
 			$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column product_id INT default NULL after id;";
	 		$sql[] = "alter table ".MM_TABLE_PRODUCTS." add column campaign_id INT default NULL after id;";
	 		
 		}
 		if(!$this->field_exists("welcome_email_enabled", MM_TABLE_MEMBER_TYPES)){
	 		$sql[] = "alter table ".MM_TABLE_MEMBER_TYPES." add column welcome_email_enabled TINYINT default '1' after downgrade_to_id;";
 		}
 		
 		if(!$this->field_exists("show_on_myaccount", MM_TABLE_CUSTOM_FIELDS)){
	 		$sql[] = "alter table ".MM_TABLE_CUSTOM_FIELDS." add column show_on_myaccount TINYINT default '1' after show_on_reg;";
 		}
 		
 		$query = "update ".MM_TABLE_SMARTTAG_GROUPS." set name='Custom Fields' where name='Custom Data' limit 1";
 		$result = $wpdb->get_row($query);
 		
 		$query = "select count(*) as total from ".MM_TABLE_SMARTTAG_GROUPS." where name='Custom Fields'";
 		$result = $wpdb->get_row($query);
 		if($result->total<=0){
			$sql[] = "INSERT INTO `".MM_TABLE_SMARTTAG_GROUPS."` (`id`, `parent_id`, `name`, `visible`) VALUES(10, 3, 'Custom Fields', 1);";
 		}
 		
 		$query = "select count(*) as total from ".MM_TABLE_SMARTTAGS." where name='MM_CustomField'";
 		$result = $wpdb->get_row($query);
 		if($result->total<=0){
 			$sql[] = "INSERT INTO `".MM_TABLE_SMARTTAGS."` (`id`, `group_id`, `name`, `visible`) VALUES(1000, 10, 'MM_CustomField', 1);";
 		}
 		
 		/// insert cron
 		$installCrons = array('notifications');
 		if($mmSite->isMM()){
 			$installCrons[]= "accounts";
 		}
 		foreach($installCrons as $cronName){
	 		$query = "select count(*) as total from ".MM_TABLE_CRON." where obj_name='".$cronName."'";
	 		$result = $wpdb->get_row($query);
	 		if($result->total<=0){
	 			$sql[] = "INSERT INTO `".MM_TABLE_CRON."` (`obj_name`, `obj_action`, `is_active`) VALUES('".$cronName."', 'Process', 1);";
	 		}
 		}
 		//end cron installation
 		
 		$sql[] = "delete from ".MM_TABLE_CORE_PAGE_TYPES." where id='".MM_CorePageType::$LIMELIGHT_SUCCESS."' limit 1";
 		$sql[] = "delete from ". MM_TABLE_CORE_PAGES." where core_page_type_id='".MM_CorePageType::$LIMELIGHT_SUCCESS."'";
 	
 		$sqlCheck = "select count(*) as total from ".MM_TABLE_CORE_PAGE_TYPES." where id='".MM_CorePageType::$MY_ACCOUNT."'";
 		$result = $wpdb->get_row($sqlCheck);
 		if($result->total<=0){
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$MY_ACCOUNT."','My Account','1');";
 		}
 		
 		$sql[] = "update ".MM_TABLE_SMARTTAGS." set visible='1' where id='105';";
 		$sql[] = "update ".MM_TABLE_SMARTTAGS." set visible='1' where id='205';";
 		
 		$sqlCheck = "select count(*) as total from ".MM_TABLE_CORE_PAGE_TAG_REQUIREMENTS." where id='6'";
 		$result = $wpdb->get_row($sqlCheck);
 		if($result->total<=0){
	 		$sql[] = "INSERT INTO ".MM_TABLE_CORE_PAGE_TAG_REQUIREMENTS." (`id`, `core_page_type_id`, `smarttag_id`, `is_global`) VALUES(6, ".MM_CorePageType::$MY_ACCOUNT.", 105, 1);";
 		}
 		
 		$sqlCheck = "select count(*) as total from ".MM_TABLE_CORE_PAGES." where core_page_type_id='".MM_CorePageType::$MY_ACCOUNT."'";
 		$result = $wpdb->get_row($sqlCheck);
 		if($result->total<=0){
 			$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('10', '".MM_CorePageType::$MY_ACCOUNT."');";
 		}
 		
 		if(count($sql)>0){
 			foreach($sql as $query){
	 			$wpdb->query($query);
	 		}
 		}
 		unset($sql);
 		
 		// new core page
 		$sqlCheck = "select count(*) as total from {$wpdb->posts} where post_name='myaccount'";
 		$result = $wpdb->get_row($sqlCheck);
 		if($result->total<=0){
 		
	 		$mm_template_base= ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/templates";	
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/myaccount.html.php", array()))."', 'My Account', '".$this->getPostName('myaccount')."');";
	 			
	 		if(!$wpdb->query($sql))
	 		{
	 			return false;
	 		}
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$MY_ACCOUNT);
 		}
 		
 		
 		if(!$mmSite->isMM()){
 			if(!isLocalInstall()){
	 			@unlink(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/modules/site_management.php");
	 			@unlink(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/modules/site_management.firstrun.php");
	 			@unlink(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/modules/sitemgmt.php");
	 			@unlink(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/modules/sitemgmt.dialog.php");
 			}
 		}
 		
 		
 		$optionVal = MM_OptionUtils::getOption("mm-set_refunded");
 		if($optionVal!="1"){
 			$query = "select count(*) as total from ".MM_TABLE_APPLIED_ACCESS_TAGS." where is_refunded='1'";
 			$row = $wpdb->query($query);
 			if($row->total<=0){
		 		$query = "update ".MM_TABLE_APPLIED_ACCESS_TAGS." set is_refunded='1' where order_id>0";
		 		$wpdb->query($query);
 			}
		 	MM_OptionUtils::setOption("mm-set_refunded","1");
 		}
 		
 		
 		//self::updateOrders();
 		//self::updateRetentionReports();
 		return true;
 	}
 	
 	public static function updateRetentionReports(){
 		global $wpdb;
 		
 		
 		$sql = "select * from {$wpdb->users} u ";
 		$rows = $wpdb->get_results($sql);
 		
 		if(is_array($rows)){
 			foreach($rows as $row){
 				$rr = new MM_RetentionReport();
 				if(intval($row->mm_main_order_id)>0){
 					$rr->getDataByOrderId($row->mm_main_order_id);
 					if($rr->isValid()){
 						continue;
 					}
 				}
 				$rr->setOrderId($row->mm_main_order_id);
 				
 				$user = new MM_User();
 				$user->setId($row->ID);
 				$user->setData($row);
 				if($user->isFree() || intval($row->mm_main_order_id)<=0){
	 				$rr->setLastRebillDate(Date("Y-m-d"));
 				}
 				else{
	 				$lastOrderId = $user->getLastOrderId(false);
	 				if(intval($lastOrderId)>0){
	 					$orderView = MM_LimeLightService::getOrder($lastOrderId);
	 					if(isset($orderView["time_stamp"])){
	 						$rr->setLastRebillDate(Date("Y-m-d h:i:s", strtotime($orderView["time_stamp"])));
	 					}
	 				}
	 				else{
	 					$rr->setLastRebillDate(Date("Y-m-d"));
	 				}
 				}
 				
 				$rr->setUserId($row->ID);
 				$rr->setRefType(MM_TYPE_MEMBER_TYPE);
 				$rr->setRefId($row->mm_member_type_id);
 				$rr->setDateAdded(Date("Y-m-d h:i:s", strtotime($row->mm_registered)));
 				
 				$memberType = new MM_MemberType($row->mm_member_type_id);
 				$rr->setProductId($memberType->getRegistrationProduct());
 				$rr->commitData();
 			}
 		}
 	}
 	
 	public static function findMissingOrders(){
 		
 		global $wpdb;
 		if(MM_Utils::isLimeLightInstall()){
	 		$timeSub = time()-1800; 
	 		$thirtyMinutesAgo = Date("h:i:s", $timeSub);
	 		$campaigns = explode(",", MM_OptionUtils::getOption("mm-campaign_ids"));
	 		if(is_array($campaigns)){
	 			foreach($campaigns as $campaignId=>$obj){
	 				if(intval($campaignId)>0){
		 				$result = MM_LimeLightService::findOrder($campaignId, Date("m/d/Y"),  Date("m/d/Y"), $thirtyMinutesAgo, Date("h:i:s"));
		 				if(!($result instanceof MM_Response)){
			 				if(isset($result["order_ids"])){
			 					$orders = explode(",", $result["order_ids"]);
			 					return count($orders);
			 				}
		 				}
	 				}
	 			}
	 		}
 		}
 		return 0;
 	}
 	
 	public static function updateOrders(){
 		global $wpdb;
 		
 		if(MM_Utils::isLimeLightInstall()){
	 		$timeSub = time()-1800; 
	 		$thirtyMinutesAgo = Date("h:i:s", $timeSub);
	 		$campaigns = MM_LimeLightService::getCampaigns();
	 		if(is_array($campaigns)){
	 			foreach($campaigns as $campaignId=>$obj){
	 				$result = MM_LimeLightService::findOrder($campaignId, Date("m/d/Y"),  Date("m/d/Y"), $thirtyMinutesAgo, Date("h:i:s"));
	 				if(!($result instanceof MM_Response)){
		 				if(isset($result["order_ids"])){
		 					$orders = explode(",", $result["order_ids"]);
		 					
		 					if(count($orders)>0){
		 						foreach($orders as $orderId){
		 							
		 							$sql = "select count(*) as total from {$wpdb->users} where mm_main_order_id='{$orderId}'";
		 							$row = $wpdb->get_row($sql);
		 							if($row->total<=0){
		 								$sql = "select count(*) as total from ".MM_TABLE_APPLIED_ACCESS_TAGS." where order_id='{$orderId}'";
			 							$row = $wpdb->get_row($sql);
			 							if($row->total<=0){
		 								
			 								$order = new MM_Order();
			 								$result = MM_LimeLightService::getOrder($orderId);
			 								
											$obj = new stdClass();
											
											foreach($result as $k=>$v) {
												$obj->$k = $v;
											}
											if($result instanceof MM_Response){
												continue;
											}
			 								$order->setData($obj);
			 								if($order->isValid()){
			 									$orderView = new stdClass();
			 									$orderView->billing_address = $order->getBillingAddress();
			 									$orderView->billing_city = $order->getBillingAddress();
			 									$orderView->billing_state = $order->getBillingState();
			 									$orderView->billing_zip = $order->getBillingZipCode();
			 									$orderView->billing_country = $order->getBillingCountry();
			 									
			 									$orderView->shipping_address = $order->getShippingAddress();
			 									$orderView->shipping_city = $order->getShippingAddress();
			 									$orderView->shipping_state = $order->getShippingState();
			 									$orderView->shipping_zip = $order->getShippingZipCode();
			 									$orderView->shipping_country =$order->getShippingCountry();
			 									
			 									$orderView->order_id = $orderId;
			 									$orderView->customer_id = $result["customer_id"];
			 									$orderView->first_name = $order->getFirstName();
			 									$orderView->last_name = $order->getLastName();
			 									$orderView->order_total = $order->getTotal();
			 									$orderView->email = $result["email_address"];
			 									$orderView->phone = $order->getPhone();
			 									
			 									$productIds = "";
			 									foreach($result as $k=>$v){
			 										if(preg_match("/(\[product_id\])/", $k)){	
			 											$productIds.= $v.",";
			 										}
			 									}
			 									$productIds = preg_replace("/(\,)$/", "", $productIds);
			 									
			 									$orderView->product_ids = $productIds;
			 									$orderView->campaign_id = $campaignId;
			 									
			 									$response = MM_APIService::newMember($orderView);
			 									if($response instanceof MM_Response){
			 										if($response->type != MM_Response::$SUCCESS){
			 										}
			 									}
			 								}
		 									
			 							}
		 							}
		 						}
		 					}
	 					}
	 				}
	 			}
	 		}
 		}
 	}
 	
 	public function activate()
 	{
 		global $mmSite;
 		ob_start();
 		
 		if(!defined("MM_TABLE_ACCESS_TAGS")) {
 			require_once(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/includes/mm-constants.php");
 		}
 		
 		$error = "";
 		
		if($this->updateMemberMouseClass()){
	 		if($this->alterMMAPITables())
	 		{
		 		if($this->authenticateWithMM() !== false)
		 		{
				 		if($this->alterWPTables())
				 		{
				 			if($this->alterMMTables())
				 			{
				 				if($this->insertMMDefaultData())
				 				{
			 						if($this->update()){
			 							if(MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INSTALL_TYPE) == MM_Site::$INSTALL_TYPE_LIMELIGHT){
			 								$this->getLimeLightData();
			 							}
			 							$missingOrders = MM_Install::findMissingOrders();
			 							if($missingOrders>0){
			 								MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ORDERS_MISSING,"1");
			 							}
			 							else{
			 								MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_ORDERS_MISSING,"0");
			 							}
			 							
			 							//update version history
			 							$myVersion = MM_Site::getPluginVersion();
			 							$mi = new MM_VersionRelease();
			 							$mi->setVersion($myVersion);
			 							$mi->getDataByVersion();
			 							$mi->commitData();
			 							
			 							ob_end_clean();
				 						return true;	
			 						}
			 						else{
			 							$error = "<b>Could not update MemberMouse.</b>";
			 						}
				 				}
						 		else {
			 						$error = "Could not install default data.";
						 		}
				 			}
					 		else {
			 					$error = "Could not alter MemberMouse tables.";
					 		}
				 		}
				 		else {
			 				$error = "Could not alter WP tables.";
				 		}
		 		}
		 		else {
		 			$error = "<b>Could not authenticate the MemberMouse plugin.</b> Please verify your site configuration on membermouse.com or contact customer support at <i>support@membermouse.com</i>.";
		 		}
	 		}
	 		else {
	 			$error = "Could not alter api tables.";
	 		}
		}
 		else {
 			$error = "Could not setup new mm class.";
 		}
 		
	 	ob_end_clean();
	 	
 		// an error occurred so deactivate the plugin
		$this->showError($error);
		exit;
 	}
 	
 	private function showError($error){
		$vars = new stdClass();
		$vars->content = $error;
		echo $error; 
		@deactivate_plugins(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/index.php", false);
 	}
 	
 	private function alterMMAPITables($doInstall=true)
 	{
 		global $wpdb;
 		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
 		
 		if($doInstall) {
			require_once(ABSPATH.'wp-content/plugins/'.MM_PLUGIN_NAME."/data/api_sql.php");
			
 		}
 	
		if(isset($sql) && count($sql)>0)
		{
			foreach($sql as $query)
			{
				try
				{
					if($wpdb->query($query) === false) 
					{
						
						LogMe::write("MM_Install.alterMMAPITables(): invalid query1: ".$query." : ".mysql_error());
						return false;
					}
				}
				catch(Exception $e)
				{
					LogMe::write("MM_Install.alterMMAPITables(): exception occurred: ".json_encode($e));
					return false;
				}
			}
			
			$countSql = "select count(*) as total from ".MM_TABLE_CONTAINER." where name='membermouseservice'";
			$row = $wpdb->get_row($countSql);
			
			if($row===false){
						LogMe::write("MM_Install.alterMMAPITables(): invalid query2: ".$countSql);
				return false;
			}
			
			
		

		}
		return true;
 	}
 	
 	public function updateMemberMouseClass(){
 		global $wpdb;
		
 		$this->alterMMAPITables();
		
		$removeSql = "delete from ".MM_TABLE_CONTAINER." where name='membermouseservice'";
		$row = $wpdb->get_row($removeSql);
		$wpdb->query($removeSql);
		
		$addSql = "insert into mm_container (name, obj, is_system, date_added) values ('membermouseservice', 'Ci8qKgogKiAKICogCk1lbWJlck1vdXNlKFRNKSAoaHR0cDovL3d3dy5tZW1iZXJtb3VzZS5jb20pCihjKSAyMDEwLTIwMTEgUG9wIEZpenogU3R1ZGlvcywgTExDLiBBbGwgcmlnaHRzIHJlc2VydmVkLgogKi8KY2xhc3MgTU1fTWVtYmVyTW91c2VTZXJ2aWNlCnsKCXB1YmxpYyBzdGF0aWMgJFNFUlZFUklQID0gTU1fQ0VOVFJBTF9TRVJWRVI7IAoJCglwdWJsaWMgc3RhdGljICRNRVRIT0RfQUREID0gImFkZE1NU2l0ZSI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfQUNUSVZBVEUgPSAiYWN0aXZhdGVNTVNpdGUiOwoJcHVibGljIHN0YXRpYyAkTUVUSE9EX0dFVCA9ICJnZXRNTVNpdGUiOwoJcHVibGljIHN0YXRpYyAkTUVUSE9EX0FVVEggPSAiYXV0aE1NU2l0ZSI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfR0VUX1JFTEVBU0UgPSAiZ2V0UmVsZWFzZSI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfR0VUX1JFTEVBU0VTID0gImdldFJlbGVhc2VzIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9VUERBVEUgPSAidXBkYXRlTU1TaXRlIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9HRVRfU0lURVMgPSAiZ2V0TU1TaXRlcyI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfR0VUX0FMTF9TSVRFUyA9ICJnZXRBbGxNTVNpdGVzIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9HRVRfQ09OVEVYVFVBTF9IRUxQID0gImdldENvbnRleHR1YWxIZWxwIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9ERUFDVElWQVRFID0gImRlYWN0aXZhdGVNTVNpdGUiOwoJcHVibGljIHN0YXRpYyAkTUVUSE9EX0FSQ0hJVkUgPSAiYXJjaGl2ZU1NU2l0ZSI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfVVBEQVRFX0NBTVBBSUdOU19JTl9VU0UgPSAidXBkYXRlQ2FtcGFpZ25zSW5Vc2UiOwoJCglwcml2YXRlIHN0YXRpYyBmdW5jdGlvbiBzZW5kUmVxdWVzdCgkbWV0aG9kLCAkcG9zdHZhcnMpCgl7CgkJJHVybCA9IHNlbGY6OiRTRVJWRVJJUC4kbWV0aG9kOwoJCgkJTG9nTWU6OndyaXRlKCJNTV9NZW1iZXJNb3VzZVNlcnZpY2Uuc2VuZFJlcXVlc3QoKTogVVJMOiAiLiR1cmwuIiA6ICIuJHBvc3R2YXJzKTsKCQkKCQkkY2ggPSBjdXJsX2luaXQoJHVybCk7CgkJY3VybF9zZXRvcHQoJGNoLCBDVVJMT1BUX1BPU1QgICAgICAsMSk7CgkJY3VybF9zZXRvcHQoJGNoLCBDVVJMT1BUX1BPU1RGSUVMRFMgICAgLCAkcG9zdHZhcnMpOwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9IRUFERVIgICAgICAsMCk7ICAvLyBETyBOT1QgUkVUVVJOIEhUVFAgSEVBREVSUwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiAgLDEpOyAgLy8gUkVUVVJOIFRIRSBDT05URU5UUyBPRiBUSEUgQ0FMTAoJCSRjb250ZW50cyA9IGN1cmxfZXhlYygkY2gpOwoJCWN1cmxfY2xvc2UoJGNoKTsKLy8JCWVjaG8gJGNvbnRlbnRzOwoJCUxvZ01lOjp3cml0ZSgiTU1fTWVtYmVyTW91c2VTZXJ2aWNlOjpzZW5kUmVxdWVzdCA6ICIuJGNvbnRlbnRzKTsKCQkkanNvbiA9IGpzb25fZGVjb2RlKCRjb250ZW50cyk7CgkJJGpzb24tPnJlc3BvbnNlX2RhdGEgPSBqc29uX2RlY29kZSgkanNvbi0+cmVzcG9uc2VfZGF0YSk7CgkJCgkJcmV0dXJuICRqc29uOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGRlcGxveVJlbGVhc2UoJGRvbWFpbiwkZXhwb3J0ZWRWZXJzaW9uLCAkbWlub3JWYXJzLCRzaXRlSWQpewoJCSRwb3N0dmFycyA9ICJ2ZXJzaW9uPSIuJGV4cG9ydGVkVmVyc2lvbi4kbWlub3JWYXJzOwoJCSR1cmwgPSAkZG9tYWluLiIvd3AtY29udGVudC9wbHVnaW5zL21lbWJlcm1vdXNlL2FwaS9yZXF1ZXN0LnBocD9xPS9kZXBsb3lSZWxlYXNlIjsKCQkkY2ggPSBjdXJsX2luaXQoJHVybCk7CgkJY3VybF9zZXRvcHQoJGNoLCBDVVJMT1BUX1BPU1QsIDEpOwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9QT1NURklFTERTLCAkcG9zdHZhcnMpOwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9TU0xfVkVSSUZZUEVFUiwgZmFsc2UpOwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9IRUFERVIsIDApOwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwgMSk7CgkJJGNvbnRlbnQgPSBjdXJsX2V4ZWMoJGNoKTsKCQlMb2dNZTo6d3JpdGUoIk1NX01lbWJlck1vdXNlU2VydmljZTo6ZGVwbG95UmVsZWFzZSA6ICIuJGNvbnRlbnQpOwoJCS8vL2VjaG8gJGNvbnRlbnQ7CgkJY3VybF9jbG9zZSgkY2gpOyAKCQkKCQkkcmVzdWx0ID0ganNvbl9kZWNvZGUoJGNvbnRlbnQpOwoJCQoJCSRjdXJyZW50RmFpbHMgPSBNTV9PcHRpb25VdGlsczo6Z2V0T3B0aW9uKE1NX09wdGlvblV0aWxzOjokT1BUSU9OX0tFWV9NSU5PUl9WRVJTSU9OX0ZBSUxTKTsKCQkkY2ZBcnIgPSBleHBsb2RlKCIsIiwkY3VycmVudEZhaWxzKTsKCQlpZighaXNfYXJyYXkoJGNmQXJyKSB8fCBlbXB0eSgkY2ZBcnIpKXsKCQkJJGNmQXJyID0gYXJyYXkoKTsKCQl9CgkJaWYoIWlzc2V0KCRyZXN1bHQtPnJlc3BvbnNlX2NvZGUpIHx8IChpc3NldCgkcmVzdWx0LT5yZXNwb25zZV9jb2RlKSAmJiAkcmVzdWx0LT5yZXNwb25zZV9jb2RlIT0iMjAwIikpewoJCQlpZighaW5fYXJyYXkoJHNpdGVJZCwgJGNmQXJyKSl7CgkJCQkkY2ZBcnJbXSA9ICRzaXRlSWQ7CgkJCX0KCQl9CgkJZWxzZXsKCQkJJGtleT0gYXJyYXlfc2VhcmNoKCRzaXRlSWQsICRjZkFycik7CgkJCWlmKCRrZXkhPT1mYWxzZSl7CgkJCQl1bnNldCgkY2ZBcnJbJGtleV0pOwoJCQl9CgkJfQoJCU1NX09wdGlvblV0aWxzOjpzZXRPcHRpb24oTU1fT3B0aW9uVXRpbHM6OiRPUFRJT05fS0VZX01JTk9SX1ZFUlNJT05fRkFJTFMsIGltcGxvZGUoIiwiLCAkY2ZBcnIpKTsKCQlyZXR1cm4gJHJlc3VsdDsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBnZXRSZWxlYXNlcygpCgl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXk7CgkJJGNvbnRlbnRzID0gc2VsZjo6c2VuZFJlcXVlc3Qoc2VsZjo6JE1FVEhPRF9HRVRfUkVMRUFTRVMsICRwb3N0dmFycyk7CgkJCgkJaWYoIXNlbGY6OmlzU3VjY2Vzc2Z1bFJlcXVlc3QoJGNvbnRlbnRzKSkKCQl7CgkJCXJldHVybiBmYWxzZTsKCQl9CgkJCgkJJGpzb24gPSAkY29udGVudHMtPnJlc3BvbnNlX2RhdGE7CgkJaWYoaXNfbnVsbCgkanNvbikpewoJCQlyZXR1cm4gZmFsc2U7CgkJfQoJCXJldHVybiAkanNvbjsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBnZXRSZWxlYXNlKCR2ZXJzaW9uLCRtaW5vclZlcnNpb24pCgl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXkuIiZ2ZXJzaW9uPSIuJHZlcnNpb24uIiZtaW5vcl92ZXJzaW9uPSIuJG1pbm9yVmVyc2lvbjsKCQkkY29udGVudHMgPSBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX0dFVF9SRUxFQVNFLCAkcG9zdHZhcnMpOwoJCQoJCWlmKCFzZWxmOjppc1N1Y2Nlc3NmdWxSZXF1ZXN0KCRjb250ZW50cykpCgkJewoJCQlyZXR1cm4gZmFsc2U7CgkJfQoJCQoJCSRqc29uID0gJGNvbnRlbnRzLT5yZXNwb25zZV9kYXRhOwoJCWlmKGlzX251bGwoJGpzb24pKXsKCQkJcmV0dXJuIGZhbHNlOwoJCX0KCQkkY2hlY2tzdW0gPSAkanNvbi0+Y2hlY2tzdW07CgkJdW5zZXQoJGpzb24tPmNoZWNrc3VtKTsKCQkKCQkkY29tcGFyZVN1bSA9IHN0cmxlbihqc29uX2VuY29kZSgkanNvbikpOwoJCUxvZ01lOjp3cml0ZSgiQ0hFQ0tTVU0gOiAiLiRjaGVja3N1bS4iIHZzLiAiLiRjb21wYXJlU3VtKTsKCQlpZigkY2hlY2tzdW0hPSRjb21wYXJlU3VtKXsKCQkJcmV0dXJuIG5ldyBNTV9SZXNwb25zZSgiQ2hlY2tzdW1zIGFyZSBub3QgZXF1aXZhbGVudCA6IHskY2hlY2tzdW19IHZzLiB7JGNvbXBhcmVTdW19IiwgTU1fUmVzcG9uc2U6OiRFUlJPUik7CgkJfQoJCXJldHVybiBzZWxmOjpzYXZlRHluYW1pY0NsYXNzZXMoJGpzb24tPmNsYXNzZXMpOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGFyY2hpdmVTaXRlKCRpZCkKCXsKCQkkYXBpc2VjcmV0ID0gZ2V0X29wdGlvbigibW0tYXBpc2VjcmV0Iik7CgkJJGFwaWtleSA9IGdldF9vcHRpb24oIm1tLWFwaWtleSIpOwoJCQoJCSR2ZXJzaW9uPSBNTV9TaXRlOjpnZXRQbHVnaW5WZXJzaW9uKCk7CgkJJHBvc3R2YXJzID0gImFwaXNlY3JldD0iLiRhcGlzZWNyZXQuIiZhcGlrZXk9Ii4kYXBpa2V5LiImaWQ9Ii4kaWQ7CgkJcmV0dXJuIHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfQVJDSElWRSwgJHBvc3R2YXJzKTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiB1cGRhdGVDYW1wYWlnblVzYWdlKCRzaXRlSWQsICRjYW1wYWlnbnNJblVzZSl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkdmVyc2lvbj0gTU1fU2l0ZTo6Z2V0UGx1Z2luVmVyc2lvbigpOwoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJmNhbXBhaWduc19pbl91c2U9Ii4kY2FtcGFpZ25zSW5Vc2UuIiZpZD0iLiRzaXRlSWQ7CgkJcmV0dXJuIHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfVVBEQVRFX0NBTVBBSUdOU19JTl9VU0UsICRwb3N0dmFycyk7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gZ2V0U2l0ZXMoJG1lbWJlcklkLCAkb3JkZXJTb3J0Q29sdW1uPSJkYXRlX2FkZGVkIiwgJG9yZGVyU29ydERpcj0iZGVzYyIpCgl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkdmVyc2lvbj0gTU1fU2l0ZTo6Z2V0UGx1Z2luVmVyc2lvbigpOwoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJm1lbWJlcl9pZD0iLiRtZW1iZXJJZC4iJnZlcnNpb249Ii4kdmVyc2lvbjsKCQkKCQlyZXR1cm4gc2VsZjo6c2VuZFJlcXVlc3Qoc2VsZjo6JE1FVEhPRF9HRVRfU0lURVMsICRwb3N0dmFycyk7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gZ2V0QWxsU2l0ZXMoJHNvcnRDb2x1bW49ImRhdGVfYWRkZWQiLCAkc29ydERpcj0iZGVzYyIsICRsaW1pdFN0YXJ0PTAsICRsaW1pdFRvdGFsPTEwKQoJewoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJJHZlcnNpb249IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXkuIiZ2ZXJzaW9uPSIuJHZlcnNpb247CgkJJHBvc3R2YXJzLj0iJnNvcnRfY29sdW1uPSIuJHNvcnRDb2x1bW4uIiZzb3J0X2Rpcj0iLiRzb3J0RGlyLiImbGltaXRfc3RhcnQ9Ii4kbGltaXRTdGFydC4iJmxpbWl0X3RvdGFsPSIuJGxpbWl0VG90YWw7CgkJcmV0dXJuIHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfR0VUX0FMTF9TSVRFUywgJHBvc3R2YXJzKTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBnZXRDb250ZXh0dWFsSGVscCgkc2VjdGlvbklkKQoJewoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJJHZlcnNpb249IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXkuIiZ2ZXJzaW9uPSIuJHZlcnNpb247CgkJJHBvc3R2YXJzLj0iJnNlY3Rpb25faWQ9Ii4kc2VjdGlvbklkOwoJCQoJCXJldHVybiBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX0dFVF9DT05URVhUVUFMX0hFTFAsICRwb3N0dmFycyk7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gZGVhY3RpdmF0ZVNpdGUoKQoJewoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJJHBvc3R2YXJzID0gImFwaXNlY3JldD0iLiRhcGlzZWNyZXQuIiZhcGlrZXk9Ii4kYXBpa2V5OwoJCSRjb250ZW50cyA9IHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfREVBQ1RJVkFURSwgJHBvc3R2YXJzKTsKCQlzZWxmOjpjbGVhblVwT3B0aW9ucygpOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGlzU3VjY2Vzc2Z1bFJlcXVlc3QoJG9iaikKCXsKCQlpZigkb2JqLT5yZXNwb25zZV9jb2RlID09ICIyMDAiKSB7CgkJCXJldHVybiB0cnVlOwoJCX0KCQkKCQlyZXR1cm4gZmFsc2U7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gYWN0aXZhdGVTaXRlKCR1cmwpCgl7CgkJJHZlcnNpb249IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAidXJsPSIudXJsZW5jb2RlKCR1cmwpLiImdmVyc2lvbj0iLiR2ZXJzaW9uOwoJCSRtaW5vclZlcnNpb24gPSBNTV9PcHRpb25VdGlsczo6Z2V0T3B0aW9uKE1NX09wdGlvblV0aWxzOjokT1BUSU9OX0tFWV9NSU5PUl9WRVJTSU9OKTsKCQlpZigkbWlub3JWZXJzaW9uIT09ZmFsc2UgJiYgaW50dmFsKCRtaW5vclZlcnNpb24pPjApewoJCQkkcG9zdHZhcnMuPSImbWlub3JfdmVyc2lvbj0iLiRtaW5vclZlcnNpb247CgkJfQoJCQoJCSRqc29uX2RhdGEgPSBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX0FDVElWQVRFLCAkcG9zdHZhcnMpOwoJCQoJCWlmKCFzZWxmOjppc1N1Y2Nlc3NmdWxSZXF1ZXN0KCRqc29uX2RhdGEpKQoJCXsKCQkJc2VsZjo6Y2xlYW5VcE9wdGlvbnMoKTsKCQkJcmV0dXJuIGZhbHNlOwoJCX0KCQkkanNvbiA9ICRqc29uX2RhdGEtPnJlc3BvbnNlX2RhdGE7CgkJcmV0dXJuIHNlbGY6OnVwZGF0ZVNpdGVJbmZvKCRqc29uKTsKCX0KCQoJcHJpdmF0ZSBzdGF0aWMgZnVuY3Rpb24gYXV0aG9yaXplU2l0ZSgpCgl7CgkJZ2xvYmFsICR3cGRiOwoJCQoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJLy8gY2FsY3VsYXRlIGN1cnJlbnQgbnVtYmVyIG9mIHRvdGFsIG1lbWJlcnMKCQkkc3FsID0gIlNFTEVDVCBjb3VudCgqKSBhcyB0b3RhbCBGUk9NICIuJHdwZGItPnVzZXJzLiIgV0hFUkUgbW1fcmVnaXN0ZXJlZCAhPSAnJyBBTkQgbW1fc3RhdHVzICE9IDIiOwoJCSRyZXN1bHQgPSAkd3BkYi0+Z2V0X3Jvdygkc3FsKTsKCQkKCQlpZigkcmVzdWx0KSB7CgkJCSR0b3RhbE1lbWJlcnMgPSAkcmVzdWx0LT50b3RhbDsKCQl9CgkJZWxzZSB7CgkJCSR0b3RhbE1lbWJlcnMgPSAwOwoJCX0KCQkKCQkkc3FsID0gIlNFTEVDVCAKCQkJCQljb3VudCh1LmlkKSBhcyB0b3RhbCAKCQkJCUZST00gCgkJCQkJIi4kd3BkYi0+dXNlcnMuIiB1LCAiLk1NX1RBQkxFX01FTUJFUl9UWVBFUy4iIG0gCgkJCQlXSEVSRSAKCQkJCQl1Lm1tX3JlZ2lzdGVyZWQgIT0gJycgQU5EIAoJCQkJCXUubW1fc3RhdHVzICE9ICIuTU1fTWVtYmVyU3RhdHVzOjokQ0FOQ0VMRUQuIiBBTkQgCgkJCQkJdS5tbV9tZW1iZXJfdHlwZV9pZCA9IG0uaWQgQU5ECgkJCQkJbS5pc19mcmVlICE9ICcxJwoJCQkiOwoJCSRyZXN1bHQgPSAkd3BkYi0+Z2V0X3Jvdygkc3FsKTsKCQkKCQlpZigkcmVzdWx0KSB7CgkJCSRwYWlkTWVtYmVycyA9ICRyZXN1bHQtPnRvdGFsOwoJCX0gCgkJZWxzZSB7CgkJCSRwYWlkTWVtYmVycyA9IDA7CgkJfQoJCQoJCSR2ZXJzaW9uID0gTU1fU2l0ZTo6Z2V0UGx1Z2luVmVyc2lvbigpOwoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJnRvdGFsX21lbWJlcnM9Ii4kdG90YWxNZW1iZXJzLiImcGFpZF9tZW1iZXJzPSIuJHBhaWRNZW1iZXJzLiImdmVyc2lvbj0iLiR2ZXJzaW9uOwoJCSRtaW5vclZlcnNpb24gPSBNTV9PcHRpb25VdGlsczo6Z2V0T3B0aW9uKE1NX09wdGlvblV0aWxzOjokT1BUSU9OX0tFWV9NSU5PUl9WRVJTSU9OKTsKCQlpZigkbWlub3JWZXJzaW9uIT09ZmFsc2UpewoJCQkkcG9zdHZhcnMuPSImbWlub3JfdmVyc2lvbj0iLiRtaW5vclZlcnNpb247CgkJfQoJCUxvZ01lOjp3cml0ZSgiTU1TZXJ2aWNlIC0gcG9zdHZhcnM6ICIuJHBvc3R2YXJzKTsKCQkkY29udGVudHMgPSBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX0FVVEgsICRwb3N0dmFycyk7CgkJCgkJaWYoIXNlbGY6OmlzU3VjY2Vzc2Z1bFJlcXVlc3QoJGNvbnRlbnRzKSkKCQl7CgkJCXNlbGY6OmNsZWFuVXBPcHRpb25zKCk7CgkJCXJldHVybiBmYWxzZTsKCQl9CgkJCgkJJGpzb24gPSAkY29udGVudHMtPnJlc3BvbnNlX2RhdGE7CgkJc2VsZjo6dXBkYXRlU2l0ZUluZm8oJGpzb24pOwoJCQoJCXJldHVybiB0cnVlOwoJfQoJCglwcml2YXRlIHN0YXRpYyBmdW5jdGlvbiB1cGRhdGVTaXRlSW5mbygkanNvbikKCXsKCQlpZihpc3NldCgkanNvbi0+YXBpa2V5KSkKCQl7CQoJCQlmb3JlYWNoKCRqc29uIGFzICRrPT4kdmFsKQoJCQl7CgkJCQlpZihpc19zdHJpbmcoJHZhbCkpIHsKCQkJCQlNTV9PcHRpb25VdGlsczo6c2V0T3B0aW9uKCJtbS0iLiRrLCBzdHJpcHNsYXNoZXMoJHZhbCkpOwoJCQkJfQoJCQl9CgkJCQoJCQlzZWxmOjpzYXZlRHluYW1pY0NsYXNzZXMoJGpzb24tPmNsYXNzZXMpOwoJCQlNTV9PcHRpb25VdGlsczo6c2V0T3B0aW9uKCJtbS1sYXN0X2NoZWNrIiwgZGF0ZSgiWS1tLWQgaDppOnMiKSk7CgkJCgkJCXJldHVybiAkanNvbjsKCQl9CgkJCgkJcmV0dXJuIGZhbHNlOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGNsZWFuVXBPcHRpb25zKCkKCXsKCQkkb3B0aW9ucyA9IGFycmF5KCJtbS1pZCIsICJtbS1uYW1lIiwgIm1tLWxvY2F0aW9uIiwgIm1tLWNhbXBhaWduX2lkcyIsICJtbS1saW1lbGlnaHRfdXJsIiwgIm1tLWxpbWVsaWdodF9wYXNzd29yZCIsICJtbS1saW1lbGlnaHRfdXNlcm5hbWUiLAoJCQkJCQkibW0tc3RhdHVzIiwgIm1tLWlwYWRkcmVzcyIsICJtbS1tZW1iZXJfaWQiLCAibW0tbGFzdF9jaGVjayIsICJtbS1pc19tZW1iZXJtb3VzZSIsICJtbS1pbnRlcnZhbCIsICJtbS1hcGlzZWNyZXQiLCAibW0tYXBpa2V5IiwgCgkJCQkJCSJtbS10b3RhbF9tZW1iZXJzIiwgIm1tLXBhaWRfbWVtYmVycyIsICJtbS1sYXN0X2NoZWNrZWQiLCAibW0tY2FtcGFpZ25zX2luX3VzZSIsICJtbS1pc19kZXYiKTsKCQkKCQlmb3JlYWNoKCRvcHRpb25zIGFzICRvcHRpb24pIHsKCQkJZGVsZXRlX29wdGlvbigkb3B0aW9uKTsKCQl9Cgl9CgkKCXByaXZhdGUgc3RhdGljIGZ1bmN0aW9uIGdldFNpdGVJbmZvKCkKCXsKCQkkZGF0YSA9IG5ldyBzdGRDbGFzcygpOwoJCSRkYXRhLT5pZCA9IGdldF9vcHRpb24oIm1tLWlkIik7CgkJJGRhdGEtPm5hbWUgPSBnZXRfb3B0aW9uKCJtbS1uYW1lIik7CgkJJGRhdGEtPmlzX2RldiA9IGdldF9vcHRpb24oIm1tLWlzX2RldiIpOwoJCSRkYXRhLT5sb2NhdGlvbiA9IGdldF9vcHRpb24oIm1tLWxvY2F0aW9uIik7CgkJJGRhdGEtPmNhbXBhaWduX2lkcyA9IGdldF9vcHRpb24oIm1tLWNhbXBhaWduX2lkcyIpOwoJCSRkYXRhLT5jYW1wYWlnbnNfaW5fdXNlID0gZ2V0X29wdGlvbigibW0tY2FtcGFpZ25zX2luX3VzZSIpOwoJCSRkYXRhLT5saW1lbGlnaHRfdXJsID0gZ2V0X29wdGlvbigibW0tbGltZWxpZ2h0X3VybCIpOwoJCSRkYXRhLT5saW1lbGlnaHRfdXNlcm5hbWUgPSBnZXRfb3B0aW9uKCJtbS1saW1lbGlnaHRfdXNlcm5hbWUiKTsKCQkkZGF0YS0+bGltZWxpZ2h0X3Bhc3N3b3JkID0gZ2V0X29wdGlvbigibW0tbGltZWxpZ2h0X3Bhc3N3b3JkIik7CgkJJGRhdGEtPnN0YXR1cyA9IGdldF9vcHRpb24oIm1tLXN0YXR1cyIpOwoJCSRkYXRhLT5pc19tZW1iZXJtb3VzZSA9IChib29sKWdldF9vcHRpb24oIm1tLWlzX21lbWJlcm1vdXNlIik7CgkJJGRhdGEtPnBhaWRfbWVtYmVycyA9IGdldF9vcHRpb24oIm1tLXBhaWRfbWVtYmVycyIpOwoJCSRkYXRhLT50b3RhbF9tZW1iZXJzID0gZ2V0X29wdGlvbigibW0tdG90YWxfbWVtYmVycyIpOwoJCQoJCXJldHVybiAkZGF0YTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBzaG91bGRBdXRob3JpemUoKQoJewoJCWlmKCFwcmVnX21hdGNoKCIvKHBsdWdpbnNcLnBocCkvIiwgJF9TRVJWRVJbIlBIUF9TRUxGIl0pICYmIGlzX2FkbWluKCkpCgkJewoJCQkkbGFzdENoZWNrZWQgPSBnZXRfb3B0aW9uKCJtbS1sYXN0X2NoZWNrIik7CQoJCQkkbmV4dENoZWNrID0gc3RydG90aW1lKCIrIi5iYXNlNjRfZGVjb2RlKGdldF9vcHRpb24oIm1tLWludGVydmFsIikpLiIgZGF5Iiwgc3RydG90aW1lKCRsYXN0Q2hlY2tlZCkpOwoJCQkkdG9kYXkgID1EYXRlKCJZLW0tZCBoOmk6cyIpOwoJCQkKCQkJaWYoc3RydG90aW1lKCR0b2RheSkgPj0gJG5leHRDaGVjaykgewoJCQkJcmV0dXJuIHRydWU7CgkJCX0KCQkJCgkJCXJldHVybiBmYWxzZTsKCQl9CgkJCgkJcmV0dXJuIGZhbHNlOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGdldFNpdGVEYXRhKCkKCXsKCSAJaWYoc2VsZjo6c2hvdWxkQXV0aG9yaXplKCkpCgkgCXsKCQkJaWYoc2VsZjo6YXV0aG9yaXplU2l0ZSgpKSB7CgkJCQlyZXR1cm4gc2VsZjo6Z2V0U2l0ZUluZm8oKTsKCQkJfQoJCQllbHNlCgkJCXsKCQkJCSRlcnJvciA9ICJUaGUgTWVtYmVyTW91c2UgcGx1Z2luIGNvdWxkIDxiPk5PVDwvYj4gYmUgYXV0aGVudGljYXRlZCBieSBtZW1iZXJtb3VzZS5jb20uIFRoZSBwbHVnaW4gd2lsbCBub3cgYmUgZGVhY3RpdmF0ZWQuIjsKCQkJCWhlYWRlcigiTG9jYXRpb246IHBsdWdpbnMucGhwPyIuTU1fU2Vzc2lvbjo6JFBBUkFNX0NPTU1BTkRfREVBQ1RJVkFURS4iPTEmIi5NTV9TZXNzaW9uOjokUEFSQU1fTUVTU0FHRV9LRVkuIj0iLnVybGVuY29kZSgkZXJyb3IpKTsKCQkJCWV4aXQ7CgkJCX0KCSAJfQoJIAkKCQlyZXR1cm4gc2VsZjo6Z2V0U2l0ZUluZm8oKTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBnZXRTaXRlKCRzaXRlSWQpCgl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkdmVyc2lvbiA9IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXkuIiZpZD0iLiRzaXRlSWQ7CgkJJGNvbnRlbnRzID0gc2VsZjo6c2VuZFJlcXVlc3Qoc2VsZjo6JE1FVEhPRF9HRVQsICRwb3N0dmFycyk7CgkJCgkJaWYoIXNlbGY6OmlzU3VjY2Vzc2Z1bFJlcXVlc3QoJGNvbnRlbnRzKSkKCQl7CgkJCXJldHVybiBmYWxzZTsKCQl9CgkJCgkJJGpzb24gPSAkY29udGVudHMtPnJlc3BvbnNlX2RhdGE7CgkJCgkJcmV0dXJuICRqc29uOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGNvbW1pdFNpdGVEYXRhKCRtZW1iZXJJZCwgTU1fU2l0ZSAkc2l0ZSwgJGlzQWRtaW49ZmFsc2UpCgl7CgkJLy8gTU0gb25seQoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkkYXBpc2VjcmV0ID0gZ2V0X29wdGlvbigibW0tYXBpc2VjcmV0Iik7CgkJJHNpdGVJZCA9ICRzaXRlLT5nZXRJZCgpOwoJCQoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJiI7CgkJJHBvc3R2YXJzIC49ICJtZW1iZXJfaWQ9Ii4kbWVtYmVySWQuIiYiOwoJCSRwb3N0dmFycyAuPSAiaWQ9Ii4kc2l0ZUlkLiImIjsKCQkkcG9zdHZhcnMgLj0gIm5hbWU9Ii4kc2l0ZS0+Z2V0TmFtZSgpLiImIjsKCQkkcG9zdHZhcnMgLj0gImNhbXBhaWduX2lkcz0iLiRzaXRlLT5nZXRDYW1wYWlnbklkcygpLiImIjsKCQkkcG9zdHZhcnMgLj0gImxvY2F0aW9uPSIuJHNpdGUtPmdldExvY2F0aW9uKCkuIiYiOwoJCSRwb3N0dmFycyAuPSAibGltZWxpZ2h0X3VybD0iLiRzaXRlLT5nZXRMTFVybCgpLiImIjsKCQkkcG9zdHZhcnMgLj0gImxpbWVsaWdodF91c2VybmFtZT0iLiRzaXRlLT5nZXRMTFVzZXJuYW1lKCkuIiYiOwoJCSRwb3N0dmFycyAuPSAibGltZWxpZ2h0X3Bhc3N3b3JkPSIuJHNpdGUtPmdldExMUGFzc3dvcmRFbmNyeXB0ZWQoKS4iJiI7CgkJaWYoJGlzQWRtaW4pewoJCQkkcG9zdHZhcnMgLj0gInN0YXR1cz0iLiRzaXRlLT5nZXRTdGF0dXMoKS4iJiI7CgkJCSRwb3N0dmFycyAuPSAiaXNfZGV2PSIuJHNpdGUtPmlzRGV2KCkuIiYiOwoJCQkkcG9zdHZhcnMgLj0gImlzX21tPSIuJHNpdGUtPmlzTU0oKS4iJiI7CgkJfQoJCSRwb3N0dmFycyAuPSAicGFpZF9tZW1iZXJzPSIuJHNpdGUtPmdldFBhaWRNZW1iZXJzKCkuIiYiOwoJCSRwb3N0dmFycyAuPSAidG90YWxfbWVtYmVycz0iLiRzaXRlLT5nZXRUb3RhbE1lbWJlcnMoKTsKCQlMb2dNZTo6d3JpdGUoImNvbW1pdFNpdGVEYXRhKCkgOiAiLiRwb3N0dmFycyk7CgkJaWYoaXNzZXQoJHNpdGVJZCkgJiYgaW50dmFsKCRzaXRlSWQpID4gMCkgewoJCQkkY29udGVudHMgPSBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX1VQREFURSwgJHBvc3R2YXJzKTsKCQl9CgkJZWxzZSB7CgkJCSRjb250ZW50cyA9IHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfQURELCAkcG9zdHZhcnMpOwoJCX0KCQkKCQlyZXR1cm4gJGNvbnRlbnRzOwoJfQoKCXByaXZhdGUgc3RhdGljIGZ1bmN0aW9uIGNhY2hlQ2xhc3MoJGNsYXNzTmFtZSwgJGNsYXNzRW50cnkpewoJCSRmaWxlUGF0aCA9IEFCU1BBVEguIndwLWNvbnRlbnQvcGx1Z2lucy8iLk1NX1BMVUdJTl9OQU1FLiIvY29tL21lbWJlcm1vdXNlL2NhY2hlIjsKCQlpZihpc19kaXIoJGZpbGVQYXRoKSl7CgkJCWlmKGlzX3dyaXRlYWJsZSgkZmlsZVBhdGgpKXsKCQkJCWlmKHByZWdfbWF0Y2goIi8obWVtYmVybW91c2Vfc2NoZW1hKSQvIiwgJGNsYXNzTmFtZSkpewoJCQkJCSRmaWxlUGF0aCAuPSAiLyIuJGNsYXNzTmFtZS4iLnNxbCI7CgkJCQl9CgkJCQllbHNlewoJCQkJCSRmaWxlUGF0aCAuPSAiLyIuYmFzZTY0X2VuY29kZSgkY2xhc3NOYW1lKS4iLmNhY2hlIjsKCQkJCX0KCQkJCSRmaCA9IGZvcGVuKCRmaWxlUGF0aCwgJ3cnKTsKCQkJCWZ3cml0ZSgkZmgsICRjbGFzc0VudHJ5KTsKCQkJCWZjbG9zZSgkZmgpOwoJCQkJCgkJCQlpZihmaWxlX2V4aXN0cygkZmlsZVBhdGgpKXsKCQkJCQlAY2htb2QoJGZpbGVQYXRoLCAwNzc3KTsKCQkJCX0KCQkJCQoJCQkJcmV0dXJuIHRydWU7CgkJCX0KCQkJZWxzZXsKCQkJCXJldHVybiBmYWxzZTsKCQkJfQoJCX0KCQllbHNlewoJCQlyZXR1cm4gZmFsc2U7CgkJfQoJfQoJCglwcml2YXRlIHN0YXRpYyBmdW5jdGlvbiBzYXZlRHluYW1pY0NsYXNzZXMoJGNsYXNzZXMpCgl7CgkJZ2xvYmFsICR3cGRiOwoJCQoJCWlmKGlzX29iamVjdCgkY2xhc3NlcykgfHwgaXNfYXJyYXkoJGNsYXNzZXMpKQoJCXsKCQkJJHNxbCA9ICJkZWxldGUgZnJvbSAiLk1NX1RBQkxFX0NPTlRBSU5FUi4iIHdoZXJlIGlzX3N5c3RlbT0nMCciOwoJCQkkd3BkYi0+cXVlcnkoJHNxbCk7CgkJCQoJCQlmb3JlYWNoKCRjbGFzc2VzIGFzICRjbGFzc05hbWU9PiRjbGFzc0VudHJ5KQoJCQl7CgkJCQkkc3FsID0gImluc2VydCBpbnRvICIuTU1fVEFCTEVfQ09OVEFJTkVSLiIgc2V0IAoJCQkJCQkJCW5hbWU9JyVzJywgCgkJCQkJCQkJb2JqPSclcycsIGRhdGVfYWRkZWQ9Tk9XKCkJCgkJCQkJCSI7CgkJCQkKCQkJCWlmKHN0cnRvbG93ZXIoJGNsYXNzTmFtZSkgPT0gIm1lbWJlcm1vdXNlc2VydmljZSIpewoJCQkJCSRzcWwgPSAidXBkYXRlICIuTU1fVEFCTEVfQ09OVEFJTkVSLiIgc2V0CgkJCQkJCQkJbmFtZT0nJXMnLCAKCQkJCQkJCQlvYmo9JyVzJywgCgkJCQkJCQkJZGF0ZV9hZGRlZD1OT1coKSB3aGVyZSBuYW1lPSdtZW1iZXJtb3VzZXNlcnZpY2UnIGxpbWl0IDEiOwoJCQkJfQoJCQkJCgkJCQkKCQkJCWlmKCR3cGRiLT5xdWVyeSgkd3BkYi0+cHJlcGFyZSgkc3FsLCAkY2xhc3NOYW1lLCAkY2xhc3NFbnRyeSkpKXsKCQkJCQkkcmV0ID0gc2VsZjo6Y2FjaGVDbGFzcygkY2xhc3NOYW1lLCAkY2xhc3NFbnRyeSk7CgkJCQkJaWYoISRyZXQpewoJCQkJCQllY2hvICJESUQgTk9UIFdSSVRFIHskY2xhc3NOYW1lfTxiciAvPiI7CgkJCQkJfQoJCQkJfQoJCQl9CgkJfQoJCQoJCXJldHVybiB0cnVlOwoJfQogfQoK', '1', NOW());";
		if($wpdb->query($addSql) === false)
		{
			
			return false;
		}
		return true;
 	}
 	
 	public function authenticateWithMM()
 	{
 		$siteurl = get_option("siteurl");
 		if(class_exists("MM_MemberMouseService")){
	 		$obj = MM_MemberMouseService::activateSite($siteurl);
	LogMe::write("authenticateWithMM()".json_encode($obj));
			if(isset($obj->classes) && (is_object($obj->classes) || is_array($obj->classes)))
			{
		 		return true;
			}
 		}
		return false;
 	}
 	
 	public function uninstall()
 	{
 		$options = array("mm-ssl","mm-show_preview", "mm_cron","mm_notification_event_types","mm_ini_default_url");
 		
 		MM_MemberMouseService::cleanUpOptions();
 		
		foreach($options as $option) {
			delete_option($option);
		}
 		
 		if($this->alterMMTables(false)) {
 			if($this->alterWPTables(false)) {
			    @deactivate_plugins(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/index.php", true);
		 		return true;
 			}
 		}
 		
 		return false;
 	}
 	
 	private function alterWPTables($doInstall=true)
 	{
 		global $wpdb;
 		
 		$wp_columns = array();
 		
 		$wp_columns["users"] = array(
 			'shipping_address'=>" varchar(255) AFTER display_name",
			'shipping_city'=>" varchar(125) AFTER display_name",
			'shipping_state'=>" char(30) AFTER display_name",
			'shipping_zip'=>" char(15) AFTER display_name",
			'shipping_country'=>" char(30) AFTER display_name",
			'billing_address'=>" varchar(255) AFTER display_name",
			'billing_city'=>" varchar(125) AFTER display_name",
			'billing_state'=>" char(30) AFTER display_name",
			'billing_zip'=>" char(15) AFTER display_name",
			'billing_country'=>" char(30) AFTER display_name",
 			'last_four'=>" smallint AFTER display_name",
 			'is_paying'=>" smallint AFTER display_name",
			'status'=>" TINYINT AFTER display_name",
			'days_calc_method'=>"ENUM('join_date','custom_date', 'fixed') default 'join_date' AFTER display_name",
			'days_calc_value'=>" varchar(255) AFTER display_name",
			'member_type_id'=>" int(11) NOT NULL DEFAULT '0' AFTER display_name",
			'main_order_id'=>" int(11) NOT NULL DEFAULT '0' AFTER display_name",
			'is_refunded'=>" TINYINT NOT NULL DEFAULT '0' AFTER display_name",
			'last_order_id'=>" int(11) NOT NULL DEFAULT '0' AFTER display_name",
			'customer_id'=>" int(11) NOT NULL DEFAULT '0' AFTER display_name",
			'registered'=>" DATETIME AFTER display_name",
			'notes'=>" TEXT AFTER display_name",
			'phone'=>" char(20) AFTER display_name	",
			'first_name'=>" varchar(255) AFTER display_name	",
			'last_name'=>" varchar(255) AFTER display_name	",
			'password'=>" varchar(255) AFTER display_name	",
			'ip_address'=>" varchar(125) AFTER display_name	",
 		);
 		
 		foreach($wp_columns as $table=>$columns)
 		{
 			foreach($columns as $column_name=>$def)
 			{
 				$column_name = MM_PREFIX.$column_name;
 				
 				if($doInstall) 
 				{
	 				if(!$this->field_exists($column_name, $wpdb->$table)) 
	 				{
	 					$sql = "ALTER TABLE ".$wpdb->$table." ADD COLUMN {$column_name} {$def}";
		 				
	 					if($wpdb->query($sql) === false) {
							return false;
						}
	 				}
 				}
 				else
 				{
	 				if($this->field_exists($column_name, $wpdb->$table)) 
	 				{
	 					$sql = "ALTER TABLE ".$wpdb->$table." DROP COLUMN {$column_name}";
	 					
	 					if($wpdb->query($sql) === false) {
	 						return false;
						}
	 				}
 				}
 			}
 		}
 		
 		if(!$doInstall) 
 		{
	 		// remove MemberMouse options
 			$sql = "DELETE FROM ".$wpdb->options." WHERE option_name LIKE '%mm-%'";
	
 			if($wpdb->query($sql) === false) {
 				return false;
			}
 		}
 		
 		return true;
 	}
 	
 	private function alterMMTables($doInstall=true)
 	{
 		global $wpdb;
 		
 		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
 		
 		if($doInstall) {
			require_once(ABSPATH.'wp-content/plugins/'.MM_PLUGIN_NAME."/data/install_sql.php");
 		}
 		else {
 			require_once(ABSPATH.'wp-content/plugins/'.MM_PLUGIN_NAME."/data/uninstall_sql.php");
 		}

		if(isset($sql) && count($sql)>0)
		{
			foreach($sql as $query)
			{
				$query = preg_replace("/(__POSTSTABLE__)/", $wpdb->posts, $query); 
				$query = preg_replace("/(__USERSTABLE__)/", $wpdb->users, $query); 
				
				try
				{
					if($wpdb->query($query) === false) 
					{
						LogMe::write("MM_Install.alterMMTables(): invalid query0: ".json_encode($query)." : ".mysql_error());
						return false;
					}
				}
				catch(Exception $e)
				{
					LogMe::write("MM_Install.alterMMTables(): exception occurred: ".json_encode($e));
					return false;
				}
			}
		}
		
 		return true;
 	} 	
 	
 	private function hasRecords($table, $column, $where){
 		global $wpdb;
 		
 		$sql = "select count(*) as total from {$table} where {$column}='".addslashes($where)."'"; 
 		$row = $wpdb->get_row($sql);
 		return ($row->total>0);
 	}
 	
 	private function insertMMDefaultData()
 	{
 		global $wpdb, $current_user;
 		
 		$affiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE);
		$subAffiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_SUB_AFFILIATE);
		$lifespan = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE_LIFESPAN);
		
		if(intval($lifespan)<=0 || $lifespan===false){
			MM_OptionUtils::setOption(MM_OPTION_TERMS_AFFILIATE,"affid");
			MM_OptionUtils::setOption(MM_OPTION_TERMS_SUB_AFFILIATE,"sid");
			MM_OptionUtils::setOption(MM_OPTION_TERMS_AFFILIATE_LIFESPAN,"30");
		}
 		
 		
 		$sql = array();
		
 		// create default email account
	 	$install_sql = "select count(*) as total, id from ".MM_TABLE_EMAIL_ACCOUNTS." where is_default='1' LIMIT 1";
	 	$row = $wpdb->get_row($install_sql);
	 	
	 	if($row->total<=0)
	 	{
		 	$install_sql = "insert into ".MM_TABLE_EMAIL_ACCOUNTS." set name='%s', email='%s', is_default='%d', status='%d'";
		 	$name = (!empty($current_user->display_name)) ? $current_user->display_name : $current_user->user_nicename;
		 	$wpdb->query($wpdb->prepare($install_sql, $name, $current_user->user_email, 1,1));
		 	$emailId = $wpdb->insert_id;
	 	}
	 	else {
	 		$emailId = $row->id;
	 	}
	 	
 		// create default member type
	 	$install_sql = "select count(*) as total from ".MM_TABLE_MEMBER_TYPES." where is_default='1'";
	 	$row = $wpdb->get_row($install_sql);
	 	
	 	if($row->total<=0)
	 	{
		 	$install_sql = "insert into ".MM_TABLE_MEMBER_TYPES." set " .
			 			"	name = 'MM Default'," .
			 			"	status='1'," .
			 			"	is_free='1'," .
			 			"	is_default='1'," .
			 			"	include_on_reg='1'," .
			 			"	description='MM Default Membership'," .
			 			"	upgrade_to_id=''," .
			 			"	downgrade_to_id=''," .
			 			"	email_subject='%s'," .
			 			"	email_body='%s'," .
			 			"	email_from_id='%d'," .
			 			"	badge_url=''" .
			 			"";
		 	
		 	$wpdb->query($wpdb->prepare($install_sql, MM_MemberType::$DFLT_EMAIL_SUBJECT, MM_MemberType::$DFLT_EMAIL_BODY, $emailId));
	 	}
	 	
 		// set default error types
 		$install_sql = "select count(*) as total from ".MM_TABLE_ERROR_TYPES;
 		$row = $wpdb->get_row($install_sql);
 		
 		if($row->total<=0)
 		{
	 		$sql[] = "insert into ".MM_TABLE_ERROR_TYPES." (id, name) values ('".MM_ErrorType::$ACCESS_DENIED."','Access Denied');";
	 		$sql[] = "insert into ".MM_TABLE_ERROR_TYPES." (id, name) values ('".MM_ErrorType::$ACCOUNT_CANCELED."','Account Canceled');";
	 		$sql[] = "insert into ".MM_TABLE_ERROR_TYPES." (id, name) values ('".MM_ErrorType::$ACCOUNT_LOCKED."','Account Locked');";
	 		$sql[] = "insert into ".MM_TABLE_ERROR_TYPES." (id, name) values ('".MM_ErrorType::$ACCOUNT_PAUSED."','Account Paused');";
	 		$sql[] = "insert into ".MM_TABLE_ERROR_TYPES." (id, name) values ('".MM_ErrorType::$ACCOUNT_OVERDUE."','Account Overdue');";
 		}
 		
 		// set default member status types
 		$install_sql = "select count(*) as total from ".MM_TABLE_MEMBER_STATUS_TYPES;
 		$row = $wpdb->get_row($install_sql);
 		
 		if($row->total<=0)
 		{
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$ACTIVE."','Active');";
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$CANCELED."','Canceled');";
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$LOCKED."','Locked');";
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$PAUSED."','Paused');";
	 		$sql[] = "insert into ".MM_TABLE_MEMBER_STATUS_TYPES." (id, name) values ('".MM_MemberStatus::$OVERDUE."','Overdue');";
 		}
 		
 		foreach($sql as $query)
 		{
 			if(!$wpdb->query($query))
 			{
 				LogMe::write("MM_Install.insertMMDefaultData(): unable to execute query ".$query);
 				return false;
 			}
 		}
 		
 		unset($sql);
 		
	 	$sql = "select count(*) as total from ".MM_TABLE_SMARTTAGS;
	 	$row = $wpdb->get_row($sql);
	 	
	 	if($row->total<=0)
	 	{
			$sql = $this->addSmartTags();
	 	}
	 	else
	 	{
	 		// 0.0.7 update
	 		$sql = "update ".MM_TABLE_SMARTTAGS." set visible='1' where id='900';";
	 		$wpdb->query($sql);
	 		$sql = "update ".MM_TABLE_SMARTTAG_GROUPS." set visible='1' where id='9';";
	 		$wpdb->query($sql);
	 		
	 		if(!$this->hasRecords(MM_TABLE_SMARTTAGS, "id", "703")){
	 			$query = "INSERT INTO `mm_smarttags` (`id`, `group_id`, `name`, `visible`) VALUES(703, 7, 'MM_PauseMembership', 1);";
	 			$wpdb->query($query);
	 		}
	 	}
	 	
	 	unset($sql);
	 	
	 	$sql = "select count(*) as total from ".MM_TABLE_API_KEYS;
	 	$row = $wpdb->get_row($sql);
	 	
	 	if($row->total<=0)
	 	{
	 		unset($sql);
	 		$apiKey = MM_Utils::createRandomString(10);
	 		$apiSecret = MM_Utils::createRandomString(10);
		 	$sql = "insert into ".MM_TABLE_API_KEYS." set 
		 				name='Default Access',
		 				api_key='".$apiKey."',	
		 				api_secret='".$apiSecret."',	
		 				status='1'				
		 	";
		 	LogMe::write("install() : ".$sql);
		 	$wpdb->query($sql);
		 	delete_option("mm-profile-setup");
	 	}
	 	
	 	$cronOption = MM_OptionUtils::getOption("mm-run-cron-web");
	 	if($cronOption ===false){
	 		MM_OptionUtils::setOption("mm-run-cron-web","1");
	 	}
	 	
	 	$sql = "select count(*) as total from  ".MM_TABLE_CAMPAIGN_OPTIONS." where setting_type='gateway'";
	 	$row = $wpdb->get_row($sql);
	 	
	 	if($row->total<=0)
	 	{
	 	
		 	$dataJson = array(
		 		'hidden_onsite'=>'0',
		 		'email'=>'',
		 		'hidden_paymentObject'=>'MM_PaypalService',
		 	);
		 	$sql = "insert into ".MM_TABLE_CAMPAIGN_OPTIONS." set name='PayPal', attr='".json_encode($dataJson)."', setting_type='gateway'";
		 	$wpdb->query($sql);
		 	unset($dataJson);
		 	
		 	$dataJson = array(
		 		'hidden_onsite'=>'0',
		 		'vendor'=>'',
		 		'developer_key'=>'',
		 		'api_key'=>'',
		 		'hidden_paymentObject'=>'MM_ClickBankService',
		 	);
		 	$sql = "insert into ".MM_TABLE_CAMPAIGN_OPTIONS." set name='ClickBank', attr='".json_encode($dataJson)."', setting_type='gateway'";
		 	$wpdb->query($sql);
		 	unset($dataJson);
		 	
		 	$dataJson = array(
		 		'hidden_onsite'=>'1',
		 		'login'=>'',
		 		'transkey'=>'',
		 		'hidden_paymentObject'=>'MM_AuthorizeService',
		 	);
		 	$sql = "insert into ".MM_TABLE_CAMPAIGN_OPTIONS." set name='Authorize.Net', attr='".json_encode($dataJson)."', setting_type='gateway'";
		 	$wpdb->query($sql);
		 	unset($dataJson);
		 	
		 	if(MM_PaymentEngine::isLimeLightInstall()){
			 	$dataJson = array(
			 		'hidden_onsite'=>'0',
			 		'email'=>'',
			 		'hidden_paymentObject'=>'MM_PaypalService',
			 	);
			 	$dataJson["hidden_paymentObject"] = 'MM_LimeLightService';
			 	$dataJson["hidden_onsite"] = '1';
			 	$dataJson["limelight_url"] = "";
			 	$dataJson["limelight_password"] = "";
			 	$dataJson["limelight_campaigns"] = "";
			 	unset($dataJson["email"]);
			 	
			 	$sql = "insert into ".MM_TABLE_CAMPAIGN_OPTIONS." set name='LimeLight', attr='".json_encode($dataJson)."', setting_type='gateway'";
			 	$wpdb->query($sql);
		 	}
	 	}
	 	
	 	return $this->setupCorePages();
 	}
 	
 	private function getLimeLightData()
 	{
 		if(MM_LimeLightService::sync()){
			MM_LimeLightService::setupProfile();
			return true;
 		}
 		return false;
 	}
 	
 	private function setupCorePages()
 	{
 		global $wpdb, $current_user;
 		
 		$install_sql = "select count(*) as total from  ".MM_TABLE_CORE_PAGE_TYPES."";
 		$row = $wpdb->get_row($install_sql);
 		
	 	$sql = array();
	 	
 		if($row->total<=0)
 		{
	 		// Core Page Types
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$MEMBER_HOME_PAGE."','Member Home','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$CANCELLATION."','Cancellation','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$ERROR."','Error','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$LOGIN_PAGE."','Login','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$FORGOT_PASSWORD."','Forgot Password','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$REGISTRATION."','Registration','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$PAID_CONFIRMATION."','Confirmation','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$FREE_CONFIRMATION."','Free Confirmation','0');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$MY_ACCOUNT."','My Account','1');";
	 		$sql[] = "insert into ".MM_TABLE_CORE_PAGE_TYPES." (id, name, visible) values ('".MM_CorePageType::$LOGOUT_PAGE."','Logout','1');";
 		}
 		
	 	// Default Core Pages
 		$install_sql = "select count(*) as total from ".MM_TABLE_CORE_PAGES;
 		$row = $wpdb->get_row($install_sql);
 		
 		if($row->total<=0)
 		{
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('1', '".MM_CorePageType::$MEMBER_HOME_PAGE."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('2', '".MM_CorePageType::$CANCELLATION."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('3', '".MM_CorePageType::$ERROR."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('4', '".MM_CorePageType::$LOGIN_PAGE."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('5', '".MM_CorePageType::$FORGOT_PASSWORD."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('6', '".MM_CorePageType::$REGISTRATION."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('7', '".MM_CorePageType::$PAID_CONFIRMATION."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('9', '".MM_CorePageType::$FREE_CONFIRMATION."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('10', '".MM_CorePageType::$MY_ACCOUNT."');";
	 		$sql[] ="insert into ".MM_TABLE_CORE_PAGES." (id, core_page_type_id) VALUES ('11', '".MM_CorePageType::$LOGOUT_PAGE."');";
 		}			
 		
 		foreach($sql as $query)
 		{
 			if(!$wpdb->query($query))
 			{
 				echo $query;
 				return false;
 			}
 		}
 		
 		unset($sql);
 		
 		
	 	$instantNotificationSql = "select count(*) as total from ".MM_TABLE_NOTIFICATION_EVENT_TYPES." ";
	 	$row = $wpdb->get_row($instantNotificationSql);
	 	
	 	if($row->total<=0)
	 	{
	 		$sql = "insert into ".MM_TABLE_NOTIFICATION_EVENT_TYPES." set id='1', event_name='Create Member Event', script_url='', status='0'";
 			if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
	 		$sql = "insert into ".MM_TABLE_NOTIFICATION_EVENT_TYPES." set id='2', event_name='Update Member Event', script_url='', status='0'";
 			if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
	 	}
 		
	 	///wp-posts pages	
 		$install_sql = "select count(*) as total from ".MM_TABLE_CORE_PAGES." where page_id IS NOT NULL ";
 		$row = $wpdb->get_row($install_sql);
	 	
	 	if($row->total<=0)
	 	{
	 		$mm_template_base= ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/templates";
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/homepage.html.php", array()))."', '[MM_Member_MemberTypeName] Home Page', '".$this->getPostName('home')."');";
	 		
 			if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$MEMBER_HOME_PAGE);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/cancel.html.php", array()))."', 'Membership Cancellation', '".$this->getPostName('cancel')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$CANCELLATION);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/error.html.php", array()))."', 'Error', '".$this->getPostName('mm-error')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$ERROR);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/login.html.php", array()))."', 'Member Login', '".$this->getPostName('login')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$LOGIN_PAGE);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/logout.html.php", array()))."', 'Member Logout', '".$this->getPostName('logout')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$LOGOUT_PAGE);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/forgot.html.php", array()))."', 'Forgot Password', '".$this->getPostName('forgot-password')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$FORGOT_PASSWORD);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/register.html.php", array()))."', 'Registration', '".$this->getPostName('register')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$REGISTRATION);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/myaccount.html.php", array()))."', 'My Account', '".$this->getPostName('myaccount')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$MY_ACCOUNT);
	 		
	 		$sql = "insert into {$wpdb->posts} (post_type, post_author, post_date, post_status,post_content, post_title, post_name) " .
	 				"		values ('page',$current_user->ID, NOW(), 'publish','".addslashes(MM_TEMPLATE::generate($mm_template_base."/install_templates/confirmation.html.php", array()))."', 'Order Confirmation', '".$this->getPostName('confirmation')."');";
	 			
	 		if(!$wpdb->query($sql))
 			{
 				echo $query;
 				return false;
 			}
 			
 			$this->linkCorePage($wpdb->insert_id, MM_CorePageType::$PAID_CONFIRMATION);
	 	}
	 	
 		return true;
 	}
 	
 	private function addSmartTags()
 	{
 		global $wpdb;
 		require_once(ABSPATH.'wp-content/plugins/'.MM_PLUGIN_NAME."/data/smarttags.sql.php");
 		if(isset($sql) && is_array($sql))
 		{
 			foreach($sql as $query)
 			{
 				if($wpdb->query($query)===false){ 
 					return false;	
 				}
 			}
 		}
 		return true;
 	}
 	
 	private function getPostName($suggestedName=""){
 		global $wpdb;
 		$sql = "select count(*) as total from {$wpdb->posts} where post_name='".$suggestedName."'";
 		$row = $wpdb->get_row($sql);
 		if($row->total>0){
	 		$sql = "select post_name from {$wpdb->posts} where post_name like '".$suggestedName."-%'";
	 		$rows = $wpdb->get_results($sql);
	 		
	 		$index=1;
	 		while(true){
	 			$newName = $suggestedName."-".$index;
	 			$hasName = false;
	 			foreach($rows as $row){
	 				if($row->post_name == $newName){
	 					$hasName = true;		
	 				}
	 			}
	 			if(!$hasName){
	 				return $newName;
	 			}
	 			$index++;
	 		}
 		}
 		return $suggestedName;
 	}
 	
 	private function linkCorePage($page_id, $id)
 	{
		global $wpdb;
		$sql = "update ".MM_TABLE_CORE_PAGES." set page_id='{$page_id}' where id='{$id}'";
		
		if(!$wpdb->query($sql))
		{
			echo $sql;
			return false;
		}
 	}
 	
	private function field_exists($column, $table)
	{
		global $wpdb;
		$sql = "describe {$table}";
		$rows = $wpdb->get_results($sql);
	
		for($i=0; $i<count($rows); $i++)
		{
			if($column == $rows[$i]->Field)
			{
				return true;
			}
		}
		
		return false;
	}
 }