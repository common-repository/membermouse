<?php

/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.

Generated class
 */

class MM_CampaignOptions extends MM_Entity{
		private $settingType = "";
		private $name = "";
		private $attr = "";
		private $showOnReg = "1";

		public function getData(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_CAMPAIGN_OPTIONS." WHERE id='".$this->id."'";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}

		public static function removePaymentByService($serviceObj){
			global $wpdb;
			
			$sql = "delete from ".MM_TABLE_CAMPAIGN_OPTIONS." where setting_type='payment' and attr like '%".get_class($serviceObj)."%'";
			$wpdb->query($sql);
		}
		
		public function getDataByNameAndSettingType(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_CAMPAIGN_OPTIONS." WHERE LOWER(name)='".strtolower($this->name)."' and setting_type='".$this->settingType."'";
			$result = $wpdb->get_row($sql);

			if($result){
				$this->setData($result);
			}
			else {
				parent::invalidate();
			}
		}
		
		public static  function getOptionRow($settingType, $includeOnlyShowOnReg=false){
			global $wpdb;
			$sql = "select * from ".MM_TABLE_CAMPAIGN_OPTIONS." where  setting_type='{$settingType}' ";
			if($includeOnlyShowOnReg){
				$sql.= " AND show_on_reg='1' ";
			}
			
			$rows = $wpdb->get_results($sql);
			if(is_array($rows)){
				$options = array();
				foreach($rows as $row){
					$options[$row->id] = $row;
				}
				return $options;
			}
			return array();
		}
		
		public static  function getOptions($settingType, $includeOnlyShowOnReg=false){
			global $wpdb;
			$sql = "select * from ".MM_TABLE_CAMPAIGN_OPTIONS." where  setting_type='{$settingType}' ";
			if($includeOnlyShowOnReg){
				$sql.= " AND show_on_reg='1' ";
			}
			
			$rows = $wpdb->get_results($sql);
			if(is_array($rows)){
				$options = array();
				foreach($rows as $row){
					if($settingType=="country"){
						$options[$row->attr] = $row->name;	
					}
					else{
						$options[$row->id] = $row->name;
					}
				}
				return $options;
			}
			return array();
		}

		public function getFormFields(){
			$info = new stdClass();
			$info->setting_type = $this->settingType;
			$info->name = $this->name;
			$info->attr = $this->attr;
			$info->show_on_reg = $this->showOnReg;

			return $info;
		}

		public static function removeAll($settingType){
			global $wpdb;
			
			$sql = "delete from ".MM_TABLE_CAMPAIGN_OPTIONS." where setting_type='{$settingType}'";
			$wpdb->query($sql);
		}
		
		public function setData($data){
			try 
			{
				$this->settingType = $data->setting_type;
				$this->name = $data->name;
				$this->attr = $data->attr;
				$this->showOnReg = $data->show_on_reg;

				parent::validate();
			}
			catch (Exception $ex) {
				parent::invalidate();
			}
		}
		
		public function remove(){
			global $wpdb;
			
			$sql = "delete from ".MM_TABLE_CAMPAIGN_OPTIONS." where id='{$this->id}' limit 1";
			if(!$wpdb->query($sql)){
				return false;
			}
			return true;
		}

		public function commitData(){

			global $wpdb;

			if(intval($this->id)>0){

				$sql = "update ".MM_TABLE_CAMPAIGN_OPTIONS." set 
					setting_type = '".mysql_escape_string($this->settingType)."',
					name = '".mysql_escape_string($this->name)."',
					attr = '".mysql_escape_string($this->attr)."',
					show_on_reg = '".mysql_escape_string($this->showOnReg)."',
					date_modified=NOW() 
				where
					id='".$this->id."'";

				$wpdb->query($sql);
				return $this->id;
			}
			else{
				$sql = "insert into ".MM_TABLE_CAMPAIGN_OPTIONS." set 
					setting_type = '".mysql_escape_string($this->settingType)."',
					name = '".mysql_escape_string($this->name)."',
					attr = '".mysql_escape_string($this->attr)."',
					show_on_reg = '".mysql_escape_string($this->showOnReg)."',
					date_modified=NOW()";

				$wpdb->query($sql);
				return mysql_insert_id();

			}

			return false;

		}

		public function getSettingType(){
			return $this->settingType;
		}

		public function setSettingType($val){
			$this->settingType = $val;
		}

		public function getName(){
			return $this->name;
		}

		public function setName($val){
			$this->name = $val;
		}

		public function getAttr(){
			return $this->attr;
		}

		public function setAttr($val){
			$this->attr = $val;
		}

		public function getShowOnReg(){
			return $this->showOnReg;
		}

		public function setShowOnReg($val){
			$this->showOnReg = $val;
		}

}?>