<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_CustomFieldData extends MM_Entity
{
	private $customFieldId = "";
	private $userId = "";
	private $value = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_CUSTOM_FIELD_DATA." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_CustomFieldData(): error retrieving data  with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function setDataByUserFieldId($userId, $customFieldId){
		global $wpdb;
 		
 		$sql = "select * from ".MM_TABLE_CUSTOM_FIELD_DATA." where user_id='{$userId}' and custom_field_id='{$customFieldId}' limit 1";
 		
 		$result = $wpdb->get_row($sql);
 		if(isset($result->user_id)){
 			$this->setData($result);
 			if($this->isValid()){
 				$this->setId($result->id);
 			}
 		}
	}
	
	public function setData($data)
	{
		try 
		{
			$this->customFieldId = $data->custom_field_id;
			$this->userId = $data->user_id;
			$this->value = $data->value;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function delete(){
		
		global $wpdb;
		if(intval($this->id)>0){
			$sql = "delete from ".MM_TABLE_CUSTOM_FIELD_DATA." where id='{$this->id}' limit 1";
			
			$result = $wpdb->query($sql);
			if($result===false){
				return new MM_Response("Could not remove custom field data due to sql error.", MM_Response::$ERROR);
			}
			return new MM_Response();
		}
		return new MM_Response("Could not remove invalid custom field data.", MM_Response::$ERROR);
	}
	
	public function commitData()
	{	
		global $wpdb;
		
		if(intval($this->id)>0){
			$sql = "update ".MM_TABLE_CUSTOM_FIELD_DATA." set 
						custom_field_id='%s', 
						user_id='%s',
						value='%s'
					where 
						id='{$this->id}'
			";
		
			$preparedSql = $wpdb->prepare($sql, $this->customFieldId, $this->userId, $this->value);
				
			$ret = $wpdb->query($preparedSql);
			if($ret===false){
				return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
			}
		}
		else{
			$sql = "insert into ".MM_TABLE_CUSTOM_FIELD_DATA." set 
						custom_field_id='%s', 
						user_id='%s',
						value='%s'
			";
			$preparedSql = $wpdb->prepare($sql, $this->customFieldId, $this->userId, $this->value);
			$ret = $wpdb->query($preparedSql);
			if($ret===false){
				return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
			}
		}
		return new MM_Response();
	}
	
	public function setCustomFieldId($msg){
		$this->customFieldId = $msg;
	}
	
	public function getCustomFieldId(){
		return $this->customFieldId;
	}
	
	public function setUserId($msg){
		$this->userId = $msg;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function setValue($val){
		$this->value = $val;
	}
	
	public function getValue(){
		return $this->value;
	}
}