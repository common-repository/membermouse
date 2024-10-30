<?php
require_once("config.php");
class Cron
{
	private $TABLE_NAME = "mm_cron";
	private $forceRun = false;
	public function __construct($force =true){
		$this->forceRun = $force;
	}
	
	private function error($str)
	{
		echo $str."\n";
	}
	
	private function status($str)
	{
		echo $str."\n";
	}
	
	public function Run($name="")
	{
		global $wpdb;
		$query ="select 
						id, last_processed, obj_name, obj_action 
					from 
						".$this->TABLE_NAME ."
					where 
						is_active='1' "; 
		if(!$this->forceRun){
			$query.= " and (next_run<=NOW() OR next_run='0000-00-00 00:00:00') ";
		}
		if(!empty($name)){
			$query.=" and obj_name='{$name}' ";
		}
		$results = $wpdb->get_results($query);
		
		if(!is_array($results))
		{
			$this->error("Unable to query for new cron jobs.");
			return false;
		}
		if(count($results)<=0)
		{
			///nothing to process
			$this->error("No cron jobs to run.");
			return false;
		}
		$this->status("Should check ".count($results)." cron modules..");
		foreach($results as $row)
		{
			$this->status("Checking ".$row->obj_name);
			try
			{
				if(class_exists($row->obj_name))
				{
					$this->status("Using function ".$row->obj_action);
					$objName = trim($row->obj_name);
					$obj = new $objName();
					$this->status("Trying to run  ".$row->obj_action);
					if(method_exists($obj, $row->obj_action))
					{ 
						if(preg_match("/(0000)/", $row->last_processed))
							$row->last_processed = "1969-01-01 01:01:00";
							
						$this->status("Running ".get_class($obj).".".$row->obj_action);
						if(call_user_func_array(array($obj, $row->obj_action), array())){
							$this->status("Updating ".get_class($obj)." Run time");
							$this->UpdateRunTime($row->id);
							$nextRunDateTime = call_user_func_array(array($obj, "getNextRunDate"), array());
							$this->status("Updating ".get_class($obj)." with new run time ".$nextRunDateTime);
							$this->setNextRunTime($row->id, $nextRunDateTime);
						}
						else
							$this->error("Unable to complete function successfully.");
					}
					else{
						$this->error("Could not find method {$row->obj_action}");
					}
				}
				else{
					$this->error("Could not find class {$row->obj_name}");
				}
			}
			catch(Exception $ex){
				$this->error($ex);
			}
		}
	}
	
	private function setNextRunTime($id, $datetime){
		global $wpdb;
		
		$sql = "update ".$this->TABLE_NAME." set next_run='{$datetime}' where id='{$id}'";
        $wpdb->query($sql);
	}
	
	
	/*** Workers ***/
	private function UpdateRunTime($id)
	{
		global $wpdb;
		$query = "update ".$this->TABLE_NAME ." set last_processed = '".Date("Y-m-d h:i:s")."' where id='{$id}'";
        $wpdb->query($query);
	}
}
?>