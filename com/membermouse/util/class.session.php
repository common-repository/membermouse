<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MM_Session
 {
 	public static $KEY_LAST_USER_ID = "MM_LAST_USER_ID";
 	public static $KEY_CAMPAIGN_SETTINGS_ID = "MM_CAMPAIGN_SETTING_CHOICE";
 	public static $KEY_UPDATE_USER_ID = "MM_UPDATE_USER_ID";
	public static $KEY_LAST_CAMPAIGN_ID = "MM_CAMPAIGN_ID";
	public static $KEY_LAST_ORDER_ID = "MM_LAST_ORDER_ID";
	public static $KEY_LAST_ORDER_CUSTOMER_ID = "LL_LAST_ORDER_CUSTOMER_ID";
	public static $KEY_CSV = "mm_csv";
	public static $KEY_ERRORS = "MM_ERRORS";
	public static $KEY_MESSAGES = "MM_MESSAGES";
	public static $KEY_REGISTRATION = "MM_REGISTRATION";
	public static $KEY_ORDER = "MM_ORDER";
	public static $KEY_PREVIEW_MODE = "preview";
	public static $KEY_UNIT_TEST = "unit_test";
	public static $PARAM_USER_ID = "user_id";
	public static $PARAM_ORDER_ID = "order_id";
	public static $PARAM_CONFIRMATION_KEY = "confirm";
	public static $PARAM_MESSAGE_KEY = "message";
	public static $PARAM_COMMAND_DEACTIVATE = "mm-deactivate";
 	
 	public static function value($name, $val=null)
 	{
 		if(!is_null($val)) {
 			$_SESSION[MM_PREFIX.$name] = $val;
 		}
 		
 		if(isset($_SESSION[MM_PREFIX.$name])) {
 			return $_SESSION[MM_PREFIX.$name];
 		}
 		
 		return false;
 	}
 	
 	public static function clear($name)
 	{
 		$_SESSION[MM_PREFIX.$name] = null;
 		unset($_SESSION[MM_PREFIX.$name]);
 	}
 }
?>
