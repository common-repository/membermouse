<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_CustomField extends MM_Entity
{
	public static $SHOW_ON_REG = '1';
	public static $NOT_ON_REG = '0';
	public static $IS_REQUIRED = '1';
	public static $IS_NOT_REQUIRED = '0';
	public static $SHOW_ON_MYACCOUNT= '1';
	public static $NOT_ON_MYACCOUNT= '1';
	
	private $fieldName = "";
	private $isRequired = "";
	private $showOnReg = "";
	private $showOnMyAccount = "";
	private $fieldLabel = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_CUSTOM_FIELDS." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_CustomField(): error retrieving data  with id of {$this->id}. Query run is ".$sql);
		}
	}
	
	public function setDataByFieldName($fieldName){
		global $wpdb;
		
		$sql = "select * from ".MM_TABLE_CUSTOM_FIELDS." where field_name='{$fieldName}'";
		$row = $wpdb->get_row($sql);
		
		if(isset($row->id) && $row->id>0){
			$this->setData($row);
			$this->id = $row->id;
		}
	}

	public function setData($data)
	{
		try 
		{
			$this->fieldName = $data->field_name;
			$this->fieldLabel = $data->field_label;
			$this->isRequired = $data->is_required;
			$this->showOnReg = $data->show_on_reg;
			$this->showOnMyAccount = $data->show_on_myaccount;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function delete(){
		
		global $wpdb;
		if(intval($this->id)>0){
			$sql = "delete from ".MM_TABLE_CUSTOM_FIELDS." where id='{$this->id}' limit 1";
			$result = $wpdb->query($sql);
			if($result===false){
				return new MM_Response("Could not remove custom field due to sql error.", MM_Response::$ERROR);
			}
			return new MM_Response();
		}
		return new MM_Response("Could not remove invalid custom field.", MM_Response::$ERROR);
	}
	
	public function commitData()
	{	
		global $wpdb;
		
		if(!$this->fieldNameExists()){
			if(intval($this->id)>0){
				$sql = "update ".MM_TABLE_CUSTOM_FIELDS." set 
							field_name='%s', 
							field_label='%s',
							is_required='%s',
							show_on_reg='%s',
							show_on_myaccount ='%s'
						where 
							id='{$this->id}'
				";
				$preparedSql = $wpdb->prepare($sql, $this->fieldName, $this->fieldLabel, $this->isRequired, $this->showOnReg, $this->showOnMyAccount);
				$ret = $wpdb->query($preparedSql);
				if($ret===false){
					return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
				}
			}
			else{
				$sql = "insert into ".MM_TABLE_CUSTOM_FIELDS." set 
							field_name='%s', 
							field_label='%s',
							is_required='%s',
							show_on_reg='%s',
							show_on_myaccount ='%s'
				";
				$preparedSql = $wpdb->prepare($sql, $this->fieldName, $this->fieldLabel, $this->isRequired, $this->showOnReg, $this->showOnMyAccount);
				$ret = $wpdb->query($preparedSql);
				if($ret===false){
					return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
				}
			}
		}
		else{
			return new MM_Response("Field Name already exists.",MM_Response::$ERROR);
		}
		return new MM_Response();
	}
	
	public static function getCustomFieldsList(){
		global $wpdb;
		
		$sql = "select 
					cf.id, cf.field_label
				 from 
				 	".MM_TABLE_CUSTOM_FIELDS." cf
		";
		$results = $wpdb->get_results($sql);
		
		$list = array();
		if(is_array($results)){
			foreach($results as $row){
				$list[$row->id] = $row->field_label;
			}
		}
		return $list;
	}
	
	public static function getCustomFields($userId=0,$onRegistrationOnly=false){
		global $wpdb;
		$sql = "select 
						cf.id, cf.field_label, cf.field_name, cf.is_required, cd.value
					 from 
					 	".MM_TABLE_CUSTOM_FIELDS." cf
					 		LEFT JOIN ".MM_TABLE_CUSTOM_FIELD_DATA." cd on cf.id=cd.custom_field_id and cd.user_id='{$userId}'
			";
		if($onRegistrationOnly){
			$sql .= " where cf.show_on_reg='1' ";
		}
		$results = $wpdb->get_results($sql);
		
		if(is_array($results)){
			foreach($results as &$row){
				$row->value = stripslashes($row->value);
			}
			
			return $results;
		}
		return array();
	}
	
	public static function hasCustomFields($onRegistrationOnly=false){
		global $wpdb;
		
		$sql = "select count(id) as total from ".MM_TABLE_CUSTOM_FIELDS." ";
		
		if($onRegistrationOnly){
			$sql.= " where show_on_reg='1'";
		}
		
		$result = $wpdb->get_row($sql);
		if(isset($result->total)){
			if($result->total>0){
				return true;
			}
		}
		return false;
		
	}
	
	public function hasAssociation(){
		global $wpdb;
		
		$sql = "select count(id) as total from ".MM_TABLE_CUSTOM_FIELD_DATA." where custom_field_id='{$this->id}' and LENGTH(value)>0";
		
		$result = $wpdb->get_row($sql);
		if(isset($result->total)){
			if($result->total>0){
				return true;
			}
		}
		return false;
	}
	
	private function fieldNameExists(){
		global $wpdb;
		
		$sql = "select count(id) as total from ".MM_TABLE_CUSTOM_FIELDS." where field_name = '%s' ";
		if($this->id>0){
			$sql.=" and id!='{$this->id}' ";
		}
		$preparedSql = $wpdb->prepare($sql, $this->fieldName);
		
		$result = $wpdb->get_row($preparedSql);
		if(isset($result->total)){
			if($result->total>0){
				return true;
			}
		}
		return false;
	}
	
	public function setFieldName($msg){
		$this->fieldName = $msg;
	}
	
	public function getFieldName(){
		return $this->fieldName;
	}
	
	public function setFieldLabel($msg){
		$this->fieldLabel = $msg;
	}
	
	public function getFieldLabel(){
		return $this->fieldLabel;
	}
	
	public function setRequired($val){
		$this->isRequired = $val;
	}
	
	public function getRequired(){
		return $this->isRequired;
	}
	
	public function setShowOnReg($val){
		$this->showOnReg = $val;
	}
	
	public function getShowOnReg(){
		return $this->showOnReg;
	}
	
	public function setShowOnMyAccount($val){
		$this->showOnMyAccount = $val;
	}
	
	public function getShowOnMyAccount(){
		return $this->showOnMyAccount;
	}
}