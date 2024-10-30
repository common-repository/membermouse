<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_OptionUtils
{
	public static $OPTION_KEY_INI_DEFAULT_URL = 'mm_ini_default_url';
	public static $OPTION_KEY_SSL_ADMIN = 'mm-ssl-admin';
	public static $OPTION_KEY_SSL = 'mm-ssl';
	public static $OPTION_KEY_ORDERS_MISSING= "mm-missing_orders_flag";
	public static $OPTION_KEY_INSTALL_TYPE= "mm-install_type";
	public static $OPTION_KEY_EVENT_HANDLING_OVERDUE= "mm-event_handling_overdue";
	public static $OPTION_KEY_EVENT_HANDLING_CANCEL= "mm-event_handling_cancel";
	public static $OPTION_KEY_CRON_INSTALLED= "mm-cron_installed";
	public static $OPTION_KEY_MINOR_VERSION= "mm-minor_version";
	public static $OPTION_KEY_MAJOR_VERSION_NOTICE= "mm-major_version_notice";
	public static $OPTION_KEY_MINOR_VERSION_FAILS= "mm-minor_version_fails";
	
	public static function setOption($optionName, $value) 
	{
		if(self::getOption($optionName) === false) {
			add_option($optionName, $value);
		}
		else {
			update_option($optionName, $value);
		}
	}
	
	public static function getOption($optionName)
	{
		return get_option($optionName);
	}
	
	/*
	 * Redefinitions when necessary
	 */
	
//	public function siteurl(){
//		$home = get_option("home");
//		$siteurl = get_option("siteurl");
//		if($home != $siteurl){
//			return $home;
//		}
//		return $siteurl;
//	}
}
?>