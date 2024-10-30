<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_Role
{
	public static $CUSTOMER_SUPPORT = 1;
	public static $ADMINISTRATOR = 2;
	public static function getRoleList()
	{
		global $wpdb;
		
		$list = array();
		
		$sql = "select * from ".MM_TABLE_ROLES;	
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