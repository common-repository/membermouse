<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_InstantNotification extends MM_Entity
{
	private $eventName = "";
	private $status = "";
	private $scriptUrl = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_NOTIFICATION_EVENT_TYPES." WHERE id='".$this->id."';";
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
			$this->eventName = $data->event_name;
			$this->status = $data->status;
			$this->scriptUrl = $data->script_url;
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
			$sql = "update ".MM_TABLE_NOTIFICATION_EVENT_TYPES." set 
						event_name='%s', 
						script_url='%s',
						status='%s'
					where 
						id='{$this->id}'
			";
			$preparedSql = $wpdb->prepare($sql, $this->eventName, $this->scriptUrl, $this->status);
			$ret = $wpdb->query($preparedSql);
			if($ret===false){
				return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
			}
		}
		else{
			$sql = "insert into ".MM_TABLE_NOTIFICATION_EVENT_TYPES." set 
						event_name='%s', 
						script_url='%s',
						status='%s'
			";
			$preparedSql = $wpdb->prepare($sql, $this->eventName, $this->scriptUrl, $this->status);
			$ret = $wpdb->query($preparedSql);
			if($ret===false){
				return new MM_Response("Could not save query {$preparedSql}.", MM_Response::$ERROR);
			}
		}
		return new MM_Response();
	}
	
	public function setEventName($msg){
		$this->eventName = $msg;
	}
	
	public function getEventName(){
		return $this->eventName;
	}
	
	public function setScriptUrl($val){
		$this->scriptUrl = $val;
	}
	
	public function getScriptUrl($checkDefault=false){
		if($checkDefault){
			if(empty($this->scriptUrl)){
				$this->scriptUrl = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INI_DEFAULT_URL);
			}
		}
		return $this->scriptUrl;
	}
	
	public function setStatus($val){
		$this->status = $val;
	}
	
	public function getStatus(){
		return $this->status;
	}
}