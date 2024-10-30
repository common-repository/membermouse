<?php

/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.

Generated class
 */

class MM_AccessLog extends MM_Entity{
		public static $MM_TYPE_PAGE = "page request";
		public static $MM_TYPE_AUTH = "login";
		
		private $eventType = "";
		private $url = "";
		private $referrer = "";
		private $description = "";
		private $userId = "";
		private $ip = "";

		public static function getTypesForSelect(){
			$types = array(
				self::$MM_TYPE_PAGE=> 'Page Request',
				self::$MM_TYPE_AUTH=> 'Login',
			);
			
			return $types;
		}
		
		public function getData(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_ACCESS_LOGS." WHERE id='".$this->id."'";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}

		public function getFormFields(){
			$info = new stdClass();
			$info->event_type = $this->eventType;
			$info->url = $this->url;
			$info->referrer = $this->referrer;
			$info->description = $this->description;
			$info->user_id = $this->userId;
			$info->ip = $this->ip;

			return $info;
		}

		public function setData($data){
			try 
			{
				$this->eventType = $data->event_type;
				$this->url = $data->url;
				$this->referrer = $data->referrer;
				$this->description = $data->description;
				$this->userId = $data->user_id;
				$this->ip = $data->ip;

				parent::validate();
			}
			catch (Exception $ex) {
				parent::invalidate();
			}
		}

		public function commitData(){

			global $wpdb;

			if(intval($this->id)>0){

				$sql = "update ".MM_TABLE_ACCESS_LOGS." set 
					event_type = '".mysql_escape_string($this->eventType)."',
					url = '".mysql_escape_string($this->url)."',
					referrer = '".mysql_escape_string($this->referrer)."',
					description = '".mysql_escape_string($this->description)."',
					user_id = '".mysql_escape_string($this->userId)."',
					ip = '".mysql_escape_string($this->ip)."',
					date_modified=NOW() 
				where
					id='".$this->id."'";
					
				$wpdb->query($sql);
				return $this->id;
			}
			else{

				$sql = "insert into ".MM_TABLE_ACCESS_LOGS." set 
					event_type = '".mysql_escape_string($this->eventType)."',
					url = '".mysql_escape_string($this->url)."',
					referrer = '".mysql_escape_string($this->referrer)."',
					description = '".mysql_escape_string($this->description)."',
					user_id = '".mysql_escape_string($this->userId)."',
					ip = '".mysql_escape_string($this->ip)."',
					date_modified=NOW()";
				
				$wpdb->query($sql);
				return mysql_insert_id();

			}

			return false;

		}
		
		/** TODO: Move to engine when it makes sense**/
		public static function hasReachedMaxIPCount($userId){
			global $wpdb;
			
			$sql = "select * from ".MM_TABLE_ACCESS_LOGS." where event_type='".self::$MM_TYPE_AUTH."' and user_id='{$userId}' group by ip";
			
			$rows = $wpdb->get_results($sql);
			
			if(is_array($rows) && count($rows)>MM_MAX_LOGIN_IP){
				return true;
			}
			return false;
		}

		public function getEventType(){
			return $this->eventType;
		}

		public function setEventType($val){
			$this->eventType = $val;
		}

		public function getUrl(){
			return $this->url;
		}

		public function setUrl($val){
			$this->url = $val;
		}

		public function getReferrer(){
			return $this->referrer;
		}

		public function setReferrer($val){
			$this->referrer = $val;
		}

		public function getDescription(){
			return $this->description;
		}

		public function setDescription($val){
			$this->description = $val;
		}

		public function getUserId(){
			return $this->userId;
		}

		public function setUserId($val){
			$this->userId = $val;
		}

		public function getIp(){
			return $this->ip;
		}

		public function setIp($val){
			$this->ip = $val;
		}

}?>