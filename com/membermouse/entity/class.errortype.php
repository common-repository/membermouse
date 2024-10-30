<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_ErrorType extends MM_Entity
{
	public static $GENERAL = 1;
	public static $ACCESS_DENIED = 2;
	public static $ACCOUNT_CANCELED = 3;
	public static $ACCOUNT_LOCKED = 4;
	public static $ACCOUNT_PAUSED = 5;
	public static $ACCOUNT_OVERDUE = 6;
	
	public static $ERROR_MSG_DENIED = "You don't have access to view this page";
	public static $ERROR_MSG_LOCKED = "Your account has been locked";
	public static $ERROR_MSG_OVERDUE = "Your account is overdue";
	public static $ERROR_MSG_CANCELLED = "Your account has been canceled";
		
	private $name = "";
	
	public function getData() 
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_ERROR_TYPES." WHERE id='".$this->id."';";
		$result = $wpdb->get_row($sql);
		
		if($result) {
			$this->setData($result);
		}
		else {
			parent::invalidate();
			LogMe::write("MM_EmailAccount.getData(): error retrieving data for email account with id of {$this->id}. Query run is ".$sql);
		}
	}

	public static function getAvailableErrors($exceptions=null)
	{
		global $wpdb;

		$sql_ext = "";
		if(!is_null($exceptions))
		{
			if(is_string($exceptions) && !empty($exceptions))
			{
				$sql_ext = " and ref_id!='{$exceptions}' ";
			}
			else if(is_array($exceptions) && count($exceptions)>0)
			{
				$sql_ext = " and ref_id NOT IN (".implode(",", $exceptions).") ";
			}
		}
		
		$sql = "SELECT * FROM ".MM_TABLE_ERROR_TYPES." e where e.id not in (select ref_id from ".MM_TABLE_CORE_PAGES." where ref_type='error_type' and ref_id IS NOT NULL {$sql_ext}) ";
 		$rows = $wpdb->get_results($sql);
 		LogMe::write("getAvailableErrors : ".json_encode($sql));
 		return $rows;
	}
	
	public static function getAvailableErrorsForPage($pageId)
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_ERROR_TYPES." e where e.id not in (select ref_id from ".MM_TABLE_CORE_PAGES." where ref_type='error_type' and ref_id IS NOT NULL and page_id !='{$pageId}') ";
 		$rows = $wpdb->get_results($sql);
 		
 		if($rows===false || count($rows)<=0)
 			return false;
 			
 		return $rows;
	}
	
	public static function getAll()
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_ERROR_TYPES." ";
 		$rows = $wpdb->get_results($sql);
 		
 		return $rows;
	}
	
	public function setData($data)
	{
		try 
		{
			$this->name = $data->name;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		//do nothing
	}
	
	public function setName($str) 
	{
		$this->name = $str;
	}
	
	public function getName()
	{
		return $this->name;
	}
 	
}
?>