<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_LogApi extends MM_Entity
{
	private $message = "";
	private $request = "";
	private $ipaddress = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_LOG_API." WHERE id='".$this->id."';";
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
			$this->message = $data->message;
			$this->request = $data->request;
			$this->ipaddress = $data->ipaddress;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		global $wpdb;
		if(intval($this->id)>0){
			$sql = "update ".MM_TABLE_LOG_API." set 
						message='%s', 
						request='%s',
						ipaddress='%s'
					where 
						id='{$this->id}'
			";
			$wpdb->query($wpdb->prepare($sql, $this->message, $this->request, $this->ipaddress));
		}
		else{
			$sql = "insert into ".MM_TABLE_LOG_API." set 
					message='%s', 
					request='%s',
					ipaddress='%s'
			";
			$wpdb->query($wpdb->prepare($sql, $this->message, $this->request, $this->ipaddress));
		}
	}
	
	public function setMessage($msg){
		$this->message = $msg;
	}
	
	public function getMessage(){
		return $this->message;
	}
	
	public function setIpAddress($req){
		$this->ipaddress = $req;
	}
	
	public function getIpAddress(){
		return $this->ipaddress;
	}
	
	public function setRequest($req){
		$this->request = $req;
	}
	
	public function getRequest(){
		return $this->request;
	}
}