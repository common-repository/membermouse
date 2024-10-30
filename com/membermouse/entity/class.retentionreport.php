<?php

/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.

Generated class
 */

class MM_RetentionReport extends MM_Entity{
		public static $COOKIE_AFFILIATE_KEY = 'affiliate_id';
		public static $COOKIE_SUB_AFFILIATE_KEY = 'sub_affiliate_id';
		
		private $affiliateId = "";
		private $subAffiliateId = "";
		private $orderId = "";
		private $userId = "";
		private $productId = "";
		private $lastRebillDate = "";
		private $refType = "";
		private $refId = "";
		private $paymentMethodId = "";
		private $dateAdded = null;

		public function getData(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_RETENTION_REPORTS." WHERE id='".$this->id."'";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}
		
		public function getDataByOrderId($orderId){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_RETENTION_REPORTS." WHERE order_id='".$orderId."' limit 1";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->id = $result->id;
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}

		public function getFormFields(){
			$info = new stdClass();
			$info->affiliate_id = $this->affiliateId;
			$info->sub_affiliate_id = $this->subAffiliateId;
			$info->order_id = $this->orderId;
			$info->user_id = $this->userId;
			$info->product_id = $this->productId;
			$info->last_rebill_date = $this->lastRebillDate;
			$info->ref_type = $this->refType;
			$info->ref_id = $this->refId;
			$info->payment_method_id = $this->paymentMethodId;

			return $info;
		}

		public function setData($data){
			try 
			{
				$this->affiliateId = $data->affiliate_id;
				$this->subAffiliateId = $data->sub_affiliate_id;
				$this->orderId = $data->order_id;
				$this->userId = $data->user_id;
				$this->productId = $data->product_id;
				$this->lastRebillDate = $data->last_rebill_date;
				$this->refId = $data->ref_id;
				$this->refType = $data->ref_type;
				$this->paymentMethodId = $data->payment_method_id;

				parent::validate();
			}
			catch (Exception $ex) {
				parent::invalidate();
			}
		}

		public function commitData(){

			global $wpdb;

			$sqlDateAdded=  "";
			if(!is_null($this->dateAdded)){
				$sqlDateAdded=  "date_added = '".$this->dateAdded."',";	
			}
			if(intval($this->id)>0){

				$sql = "update ".MM_TABLE_RETENTION_REPORTS." set 
					affiliate_id = '".mysql_escape_string($this->affiliateId)."',
					sub_affiliate_id = '".mysql_escape_string($this->subAffiliateId)."',
					order_id = '".mysql_escape_string($this->orderId)."',
					product_id = '".mysql_escape_string($this->productId)."',
					last_rebill_date = '".mysql_escape_string($this->lastRebillDate)."',
					user_id = '".mysql_escape_string($this->userId)."',
					ref_type = '".mysql_escape_string($this->refType)."',
					payment_method_id = '".mysql_escape_string($this->paymentMethodId)."',
					ref_id = '".mysql_escape_string($this->ref_id)."', {$sqlDateAdded}
					date_modified=NOW() 
				where
					id='".$this->id."'";

				$wpdb->query($sql);
				return $this->id;
			}
			else{

				$sql = "insert into ".MM_TABLE_RETENTION_REPORTS." set 
					affiliate_id = '".mysql_escape_string($this->affiliateId)."',
					sub_affiliate_id = '".mysql_escape_string($this->subAffiliateId)."',
					last_rebill_date = '".mysql_escape_string($this->lastRebillDate)."',
					order_id = '".mysql_escape_string($this->orderId)."',
					product_id = '".mysql_escape_string($this->productId)."',
					user_id = '".mysql_escape_string($this->userId)."',
					ref_type = '".mysql_escape_string($this->refType)."',
					payment_method_id = '".mysql_escape_string($this->paymentMethodId)."',
					ref_id = '".mysql_escape_string($this->refId)."', {$sqlDateAdded}
					date_modified=NOW()";

				$wpdb->query($sql);
				return mysql_insert_id();

			}

			return false;

		}
		
		public static function setAffiliateCookies(){
			$affiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE);
			$subAffiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_SUB_AFFILIATE);
			$lifespan = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE_LIFESPAN);
			
			if(intval($lifespan)>0){
				$days = time()+3600*24*intval($lifespan);
				if(isset($_GET[$affiliateId])){
					if(!isset($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY]) || (isset($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY]) && empty($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY]))){
						setcookie(MM_RetentionReport::$COOKIE_AFFILIATE_KEY,$_GET[$affiliateId],$days,"/");
					}
				}
				if(isset($_GET[$subAffiliateId])){
					if(!isset($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY])  || (isset($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY]) && empty($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY]))){
						setcookie(MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY,$_GET[$subAffiliateId],$days,"/");
					}
				}
			}
			
			return true;
		}
		
		public static function getAffiliates(){
			global $wpdb;
			$sql = "select 
						affiliate_id 
					from 
						".MM_TABLE_RETENTION_REPORTS." 
					where 
						affiliate_id !='' and 
						affiliate_id IS NOT NULL
					Union 
					
					select
						sub_affiliate_id as affiliate_id 
					from 
						".MM_TABLE_RETENTION_REPORTS. "  
					where 
						affiliate_id !='' and 
						affiliate_id IS NOT NULL
					group by 
						affiliate_id";
		
			$rows = $wpdb->get_results($sql);
			if(is_array($rows)){
				return $rows;
			}
			return array();
		}
		
		public static function generateCsvData($fromDate, $toDate, $groupByAffiliate, $selectedTypes,$typeName, $affiliateId=null){
			global $wpdb;
			
			$from  ="";
			$where = "";
			switch($typeName){
				case "member_type":
					$from = MM_TABLE_MEMBER_TYPES. " accessType ";
					break;
				case "access_tag":
					$from = MM_TABLE_ACCESS_TAGS. " accessType ";
					break;
			}
			
			if(empty($selectedTypes)){
				return false;
			}
			
			if(empty($from)){
				return false;
			}
			
			$sql = "select 
						accessType.id, accessType.name,r.affiliate_id, r.sub_affiliate_id, TIMESTAMPDIFF(MONTH, DATE(r.date_added), DATE(r.last_rebill_date)) as months_elapsed
					from 
						".MM_TABLE_RETENTION_REPORTS." r, {$from} ";
			
			$sql.= " where 
							DATE(r.date_added) >= DATE('{$fromDate}') AND DATE(r.date_added) <= DATE('{$toDate}') and 
							r.ref_type='{$typeName}' and 
					";
			
			if(!is_null($affiliateId)){
				$qualifier = (strlen($affiliateId)<=0)?"AND":"OR";
				$sql.= " (r.affiliate_id='{$affiliateId}' {$qualifier} r.sub_affiliate_id='{$affiliateId}') and ";
			}
			$typesIn = "";
			foreach($selectedTypes as $selectedId){
				$typesIn.= "{$selectedId},";
			}
			$typesIn = preg_replace("/(\,)$/", "", $typesIn);
			
			$sql.=" r.ref_id IN ({$typesIn}) AND 
					r.ref_id=accessType.id 
					order by 
						TIMESTAMPDIFF(MONTH, DATE(r.date_added), DATE(r.last_rebill_date)) asc
			";
			
			LogMe::write("generateCsvData() : ".$sql);
//			echo $sql."<br /><br />";
//			exit;
			$results = $wpdb->get_results($sql);
			
			if(is_array($results)){
				$types = array();
				foreach($results as $row){
					$row->months_elapsed = intval($row->months_elapsed)+1;
					if(!isset($types[$row->id])){
						$types[$row->id] = array();
						$types[$row->id]["total"] = 0;
					}
					if(!isset($types[$row->id]["months_".$row->months_elapsed])){
						if($row->months_elapsed>=12){
							$types[$row->id]["months_12"] = 1;
							$types[$row->id]["total"]++;
						}
						else if($row->months_elapsed<=1){
							$types[$row->id]["months_1"] = 1;
							$types[$row->id]["total"]++;
						}
						else{
							$types[$row->id]["months_".$row->months_elapsed] = 1;
							$types[$row->id]["total"]++;
						}
					}
					else{
						if($row->months_elapsed>=12){
							$types[$row->id]["months_12"]++;
							$types[$row->id]["total"]++;
						}
						else if($row->months_elapsed<=1){
							$types[$row->id]["months_1"]++;
							$types[$row->id]["total"]++;
						}
						else{
							$types[$row->id]["months_".$row->months_elapsed]++;
							$types[$row->id]["total"]++;
						}
					}
				}
				
				return $types;
			}
		}
		
		public static function clearCookies(){
			if(isset($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY])){
				$_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY] = null;
				unset($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY]);
			}
			if(isset($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY])){
				$_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY] = null;
				unset($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY]);
			}
		}
		
		public static function dumpCookies(){
		}
		
		public static function getAffiliateCookie($optionName){
			switch($optionName){
				case MM_OPTION_TERMS_AFFILIATE:
					if(isset($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY])){
						return $_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY];
					}
					return "";
				case MM_OPTION_TERMS_SUB_AFFILIATE:
					if(isset($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY])){
						return $_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY];
					}
					return "";
			}
			return "";
		}
		
		public static function hasAffiliateCookies(){
			$affiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE);
			$subAffiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_SUB_AFFILIATE);
			$lifespan = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE_LIFESPAN);
			
			if(intval($lifespan)>0){
				if(!empty($affiliateId) || !empty($subAffiliateId)){
					if(isset($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY]) && !empty($_COOKIE[MM_RetentionReport::$COOKIE_AFFILIATE_KEY])){
						return true;
					}
					if(isset($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY]) && !empty($_COOKIE[MM_RetentionReport::$COOKIE_SUB_AFFILIATE_KEY])){
						return true;
					}
				}
			}
			return false;
		}
		
		public function setDateAdded($str){
			$this->dateAdded = $str;
		}

		public function getAffiliateId(){
			return $this->affiliateId;
		}

		public function setAffiliateId($val){
			$this->affiliateId = $val;
		}

		public function getSubAffiliateId(){
			return $this->subAffiliateId;
		}

		public function setSubAffiliateId($val){
			$this->subAffiliateId = $val;
		}

		public function getOrderId(){
			return $this->orderId;
		}

		public function setOrderId($val){
			$this->orderId = $val;
		}

		public function getUserId(){
			return $this->userId;
		}

		public function setUserId($val){
			$this->userId = $val;
		}

		public function getRefId(){
			return $this->refId;
		}

		public function setRefId($val){
			$this->refId = $val;
		}

		public function getRefType(){
			return $this->refType;
		}

		public function setRefType($val){
			$this->refType = $val;
		}

		public function getProductId(){
			return $this->productId;
		}

		public function setProductId($val){
			$this->productId = $val;
		}

		public function getPaymentMethodId(){
			return $this->paymentMethodId;
		}

		public function setPaymentMethodId($val){
			$this->paymentMethodId = $val;
		}

		public function getLastRebillDate(){
			return $this->lastRebillDate;
		}

		public function setLastRebillDate($val){
			$this->lastRebillDate = $val;
		}

}