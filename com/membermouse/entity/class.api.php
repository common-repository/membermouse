<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Api extends MM_Entity
{
	public static $STATUS_ACTIVE = '1';
	public static $STATUS_INACTIVE = '0';
	
	private $name = "";
	private $api_key = "";
	private $api_secret = "";
	private $status = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_API_KEYS." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_LogApi(): error retrieving data  with id of {$this->id}. Query run is ".$sql);
		}
	}

	public function setData($data)
	{
		try 
		{
			$this->name = $data->name;
			$this->api_key = $data->api_key;
			$this->api_secret = $data->api_secret;
			$this->status = $data->status;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function delete(){
		
		global $wpdb;
		if(intval($this->id)>0){
			$sql = "delete from ".MM_TABLE_API_KEYS." where id='{$this->id}' limit 1";
			$result = $wpdb->query($sql);
			if($result===false){
				return new MM_Response("Could not remove api set due to sql error.", MM_Response::$ERROR);
			}
			return new MM_Response();
		}
		return new MM_Response("Could not remove invalid api set.", MM_Response::$ERROR);
	}
	
	public function commitData()
	{	
		global $wpdb;
		if(intval($this->id)>0){
			$sql = "update ".MM_TABLE_API_KEYS." set 
						name='%s', 
						api_key='%s',
						api_secret='%s',
						status='%s'
					where 
						id='{$this->id}'
			";
			$preparedSql = $wpdb->prepare($sql, $this->name, $this->api_key, $this->api_secret, $this->status);
			$ret = $wpdb->query($preparedSql);
			if($ret===false){
				return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
			}
		}
		else{
			$sql = "insert into ".MM_TABLE_API_KEYS." set 
						name='%s', 
						api_key='%s',
						api_secret='%s',
						status='%s'
			";
			$preparedSql = $wpdb->prepare($sql, $this->name, $this->api_key, $this->api_secret, $this->status);
			$ret = $wpdb->query($preparedSql);
			if($ret===false){
				return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
			}
		}
		return new MM_Response();
	}
	
	public static function getKeyList($useActiveOnly=false){
		global $wpdb;
		
		$sql = "select * from ".MM_TABLE_API_KEYS." ";
		if($useActiveOnly){
			$sql.= " where status='".self::$STATUS_ACTIVE."'";
		}
		$rows = $wpdb->get_results($sql);
		if(is_array($rows)){
			return $rows;
		}
		return array();
	}
	
	public function getStatusList(){
		$arr =  array(
			self::$STATUS_ACTIVE=>'Active',
			self::$STATUS_INACTIVE=>'Inactive',
		);
		return $arr;
	}
	
	public function setName($msg){
		$this->name = $msg;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setApiKey($val){
		$this->api_key = $val;
	}
	
	public function getApiKey(){
		return $this->api_key;
	}
	
	public function setApiSecret($val){
		$this->api_secret = $val;
	}
	
	public function getApiSecret(){
		return $this->api_secret;
	}
	
	public function setStatus($val){
		$this->status = $val;
	}
	
	public function getStatus(){
		return $this->status;
	}
}