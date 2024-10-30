<?php

/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.

Generated class
 */

class MM_CampaignSetting extends MM_Entity{
		private $campaignId = "";
		private $settingType = "";
		private $name = "";

		public function getData(){
			global $wpdb;
			$sql = "SELECT * FROM ".MM_TABLE_CAMPAIGN_OPTIONS." WHERE id='".$this->id."' and (campaign_id<=0 OR campaign_id='') limit 1";
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
			$info->campaign_id = $this->campaignId;
			$info->setting_type = $this->settingType;
			$info->name = $this->name;

			return $info;
		}

		public function setData($data){
			try 
			{
				$this->campaignId = $data->campaign_id;
				$this->settingType = $data->setting_type;
				$this->name = $data->name;

				parent::validate();
			}
			catch (Exception $ex) {
				parent::invalidate();
			}
		}
		
		public function remove(){
			global $wpdb;
			
			$sql = "delete from ".MM_TABLE_CAMPAIGN_OPTIONS." where id='{$this->id}' and setting_type='{$this->settingType}' and campaign_id='0' limit 1";
			$wpdb->query($sql);
		}

		public function commitData(){

			global $wpdb;

			if(intval($this->id)>0 || (strlen($this->id)>0 && $this->id !='0')){
				$idSql = "";
				if(!preg_match("/[a-zA-Z]+/", $this->id)){
					$sql = "select count(*) as total from ".MM_TABLE_CAMPAIGN_OPTIONS." where campaign_id='0' and id='{$this->id}'";
					$row = $wpdb->get_row($sql);
					if($row->total>0){
						return false;
					}
					$id = $this->id;
				}
				
				
				$sql = "update ".MM_TABLE_CAMPAIGN_OPTIONS." set 
					name = '".mysql_escape_string($this->name)."'
					{$idSql}
				where
					id='".$this->id."' AND 
					(campaign_id='0' OR campaign_id='') 
					";

				$wpdb->query($sql);
				return $this->id;
			}
			else{
				if(!preg_match("/[a-zA-Z]+/", $this->id)){
					$sql = "select id from ".MM_TABLE_CAMPAIGN_OPTIONS." where campaign_id='0' order by id desc limit 1";
					$row = $wpdb->get_row($sql);
					$id = 0;
					if(!isset($row->id) || (isset($row->id) && intval($row->id)<=0)){
						$id = 1;
					}
					else{
						$id = intval($row->id);
						$id++;
					}
				}
				else{
					$sql = "select count(*) as total from ".MM_TABLE_CAMPAIGN_OPTIONS." where campaign_id='0' and id='{$this->id}'";
					$row = $wpdb->get_row($sql);
					if($row->total>0){
						return false;
					}
					$id = $this->id;
				}
				$sql = "insert into ".MM_TABLE_CAMPAIGN_OPTIONS." set 
					id='{$id}', 
					campaign_id = '".mysql_escape_string($this->campaignId)."',
					setting_type = '".mysql_escape_string($this->settingType)."',
					name = '".mysql_escape_string($this->name)."'
			";

				$wpdb->query($sql);
				return $id;

			}

			return false;

		}

		public function getCampaignId(){
			return $this->campaignId;
		}

		public function setCampaignId($val){
			$this->campaignId = $val;
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

}?>