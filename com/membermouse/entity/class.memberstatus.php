<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_MemberStatus
{
	public static $ACTIVE = 1;
	public static $CANCELED = 2;
	public static $LOCKED = 3;
	public static $PAUSED = 4;
	public static $OVERDUE = 5;
	
	public static function getStatus($statusId)
	{
		global $wpdb;
		
		$sql = "SELECT * FROM ".MM_TABLE_MEMBER_STATUS_TYPES." WHERE id='".$statusId."' LIMIT 1";	
		$row = $wpdb->get_row($sql);
		
		return $row;
	}
	
	public static function getName($statusId)
	{
		$list = array();
		
		$status = MM_MemberStatus::getStatus($statusId);
		
		if($status) {
			return $status->name;
		}
		else {
			return MM_NO_DATA;
		}
	}
	
	public static function getImage($statusId)
	{
		switch($statusId) {
			case self::$ACTIVE:
				return '<img src="'.MM_Utils::getImageUrl("accept").'" style="vertical-align:middle" title="Active" />';
			
			case self::$CANCELED:
				return '<img src="'.MM_Utils::getImageUrl("stop").'" style="vertical-align:middle" title="Canceled" />';
				
			case self::$LOCKED:
				return '<img src="'.MM_Utils::getImageUrl("lock").'" style="vertical-align:middle" title="Locked" />';
				
			case self::$PAUSED:
				return '<img src="'.MM_Utils::getImageUrl("pause").'" style="vertical-align:middle" title="Paused" />';
				
			case self::$OVERDUE:
				return '<img src="'.MM_Utils::getImageUrl("overdue").'" style="vertical-align:middle" title="Overdue" />';
		}
		
		return "";
	}
	
	public static function getStatusTypesList()
	{
		global $wpdb;
		
		$list = array();
		
		$sql = "select * from ".MM_TABLE_MEMBER_STATUS_TYPES;	
		$rows = $wpdb->get_results($sql);
		
		if($rows) 
 		{
	 		foreach($rows as $row)
			{
				$list[$row->id] = $row->name;
			}
 		}
		
		return $list;
	}
}
?>
