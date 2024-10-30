<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_CorePageType extends MM_Entity
{	
	public static $NO_PAGE = 0;
	public static $MEMBER_HOME_PAGE = 1;
	public static $CANCELLATION = 2;
	public static $ERROR = 3;
	public static $LOGIN_PAGE = 4;
	public static $FORGOT_PASSWORD = 5;
	public static $REGISTRATION = 6;
	public static $PAID_CONFIRMATION = 7;
	public static $LIMELIGHT_SUCCESS = 8;
	public static $FREE_CONFIRMATION = 9;
	public static $MY_ACCOUNT = 10;
	public static $LOGOUT_PAGE = 11;
	
	private $name;
	private $visible;
	
	public function getData() 
	{	
		//do nothing
	}
	
	public static function getCorePageTypesList()
 	{
 		global $wpdb;
 		
 		$list = array();
 		
 		$sql = "select * from ".MM_TABLE_CORE_PAGE_TYPES." where visible='1'";
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
 	
	public function setData($data)
	{
		try
		{
			$this->name = $data->name;
			$this->visible = $data->visible;
			parent::validate();
		}
		catch (Exception $ex) {
			parent::invalidate();
		}
	}
	
	public function commitData()
	{	
		// do nothing
	}
	
	public function getName()
	{
		return $this->name;
	}
 	
	public function getVisible()
	{
		return $this->visible;
	}
	
}
?>