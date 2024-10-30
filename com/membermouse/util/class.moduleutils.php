<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_ModuleUtils
{
	public static function getPage()
 	{
 		return isset($_REQUEST["page"]) ? $_REQUEST["page"] : MM_MODULE_DASHBOARD;
 	}
 	
 	public static function getPrimaryTab()
 	{
 		return self::getParentTab(self::getModule());
 	}
 	
	public static function getParentTab($tab) {
		
		switch($tab) {
			case MM_MODULE_LIMELIGHT:
				return MM_MODULE_LIMELIGHT;
				
			case MM_MODULE_MANAGE_INSTALL:
				return MM_MODULE_MANAGE_INSTALL;
			
			case MM_MODULE_SHIPPING:
				return MM_MODULE_SHIPPING;
			case MM_MODULE_COUNTRIES:
				return MM_MODULE_COUNTRIES;
			case MM_MODULE_PAYMENT:
				return MM_MODULE_PAYMENT;
			case MM_MODULE_PRODUCTS:
				return MM_MODULE_PRODUCTS;
				
			case MM_MODULE_REPORTS;
				return MM_MODULE_REPORTS;
			
			case MM_MODULE_LOGS;
				return MM_MODULE_LOGS;
			
			case MM_MODULE_SETTINGS_SITE_MANAGEMENT:
			case MM_MODULE_SETTINGS_STANDARD_PAYMENT_TEST:
			case MM_MODULE_SETTINGS_LL_INTEGRATION:
			case MM_MODULE_SETTINGS_API_TEST:
			case MM_MODULE_SETTINGS_UNIT_TEST:
				return MM_MODULE_ADMIN_TOOLS;
			
			case MM_MODULE_MEMBER_TYPES:
			case MM_MODULE_ACCESS_TAGS:
			case MM_MODULE_ACCOUNT_TYPES:
				return MM_MODULE_ACCESS_RIGHTS;
				
			case MM_MODULE_EMAIL_ACCOUNTS:
			case MM_MODULE_EMAIL_TEMPLATES:
				return MM_MODULE_EMAIL_SETTINGS;
				
			case MM_MODULE_SETTINGS_TERMS:
			case MM_MODULE_SETTINGS_CUSTOM_FIELDS:
				return MM_MODULE_REGISTRATION_SETTINGS;

			case MM_MODULE_SETTINGS_CRON_MANAGEMENT:
			case MM_MODULE_SETTINGS_API:
			case MM_MODULE_INSTANT_NOTIFICATION:
			case MM_MODULE_SETTINGS_FREE_MEMBER_FORM:
			case MM_MODULE_PHP_FUNCTIONS:
				return MM_MODULE_INTEGRATION_TOOLS;

			case MM_MODULE_DELIVERY_SCHEDULE_MANAGER:
				return MM_MODULE_DELIVERY_SCHEDULE_MANAGER;
				
			case MM_MODULE_LIMELIGHT:
				return MM_MODULE_LIMELIGHT;
			
			case MM_MODULE_DETAILS_GENERAL:
			case MM_MODULE_DETAILS_CUSTOM_FIELDS:
			case MM_MODULE_DETAILS_SHIPPING:
			case MM_MODULE_DETAILS_BILLING:
			case MM_MODULE_DETAILS_ACCESS_RIGHTS:
			case MM_MODULE_DETAILS_ORDER_HISTORY:
			case MM_MODULE_DETAILS_BILLING_INFO:
			case MM_MODULE_DETAILS_SHIPPING_INFO:
				return MM_MODULE_DETAILS;
				
			default:
				return "";
		}
	}
 	
 	public static function getModule()
 	{
 		$pageName = self::getPage();
 		
 		$module = isset($_REQUEST["module"]) ? $_REQUEST["module"] : "";
		
		if(!isset($module) || $module == "")
		{
			switch($pageName)
			{
			 	case MM_MODULE_MANAGE_INSTALL:
			 		$module = MM_MODULE_MANAGE_INSTALL;
					break;
					
			 	case MM_MODULE_DASHBOARD:
			 		$module = MM_MODULE_DASHBOARD_VIEW;
					break;
					
			 	case MM_MODULE_CONFIGURE_SITE:
			 		$module = MM_MODULE_MEMBER_TYPES;
					break;
					
			 	case MM_MODULE_ECOMMERCE:
			 		if(MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_INSTALL_TYPE)!=MM_Site::$INSTALL_TYPE_LIMELIGHT){
			 			$module= MM_MODULE_PRODUCTS;
			 		}
			 		else{	
			 			$module = MM_MODULE_LIMELIGHT;
			 		}
					break;
					
				case MM_MODULE_MANAGE_MEMBERS:
			 		$module = MM_MODULE_BROWSE_MEMBERS;
					break;
					
				case MM_MODULE_SETTINGS:
			 		$module = MM_MODULE_SETTINGS_TERMS;
					break;
					
				case MM_MODULE_INTEGRATION_TOOLS:
			 		$module = MM_MODULE_SETTINGS_API;
					break;
					
				case MM_MODULE_ADMIN_TOOLS:
			 		$module = MM_MODULE_SETTINGS_SITE_MANAGEMENT;
					break;
					
				case MM_MODULE_LOGS:
			 		$module = MM_MODULE_LOGS;
					break;
					
				case MM_MODULE_REPORTS:
			 		$module = MM_MODULE_REPORTS;
					break;
						
				default:
					$module = MM_MODULE_MEMBER_TYPES;
					break;
			}
		}
		
		return $module;
 	}
 	
 	public static function getUrl($page, $module)
 	{
 		return "admin.php?page=".$page."&module=".$module;
 	}
}
?>
