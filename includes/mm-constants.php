<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

function isLocalInstall($specificServer="localhost"){
	 if(isset($_SERVER["SERVER_NAME"]) && strlen($_SERVER["SERVER_NAME"])>0){
		 if(preg_match("/(".$specificServer.")/", $_SERVER["SERVER_NAME"])){
		 	return true;
		 }
	 }
	 else{
	 	if(preg_match("/(FM100065)/", dirname(__FILE__))){
	 		return true;
	 	}
	 }
	 return false;
}
define("MM_PREFIX", "mm_");   
$centralServer = (isLocalInstall("membermouse.localhost") || isLocalInstall("membermouse2.localhost") || isLocalInstall("wordpress.test"))?"http://mmcentral.localhost/index.php?q=/":"http://64.106.166.99/index.php?q=/";
$centralServerUrl = (isLocalInstall("membermouse.localhost")  || isLocalInstall("membermouse2.localhost") || isLocalInstall("wordpress.test"))?"http://mmcentral.localhost":"http://64.106.166.99";

$reservedGetParams = array(
	's'=>1,
	'p'=>1,
	'page_id'=>1,
	'name'=>1,
);

define("MM_CENTRAL_SERVER_URL", $centralServerUrl);
define("MM_CENTRAL_SERVER", $centralServer);
define("MM_PLUGIN_NAME", array_pop(explode(DIRECTORY_SEPARATOR, dirname(dirname(__FILE__)))));

define("MM_MAX_LOGIN_IP", 5);
define("MM_NO_DATA", "&mdash;");
define("MM_GET_KEY", "345346539284890489234");

define("MM_NOTICE_EMAIL_NAME", "MemberMouse Notice");
define("MM_NOTICE_EMAIL_ADDRESS", "do-not-reply@membermouse.com");

define("MM_TYPE_MEMBER_TYPE", "member_type");
define("MM_TYPE_ACCESS_TAG", "access_tag");
define("MM_TYPE_POST", "post");
define("MM_TYPE_PRODUCT", "product");
define("MM_TYPE_CUSTOM_FIELD", "custom_field");
define("MM_TYPE_EMAIL_ACCOUNT", "email_account");

/** DATABASE TABLE NAMES **/
define("MM_TABLE_ACCESS_TAGS", MM_PREFIX."access_tags");
define("MM_TABLE_CALLBACK_RESPONSES", MM_PREFIX."callback_responses");
define("MM_TABLE_MEMBER_TYPES", MM_PREFIX."member_types");
define("MM_TABLE_RETENTION_REPORTS", MM_PREFIX."retention_reports");
define("MM_TABLE_MEMBER_TYPE_PRODUCTS", MM_PREFIX."member_type_products");
define("MM_TABLE_APPLIED_ACCESS_TAGS", MM_PREFIX."applied_access_tags");
define("MM_TABLE_CORE_PAGES", MM_PREFIX."core_pages");
define("MM_TABLE_ERROR_TYPES", MM_PREFIX."error_types");
define("MM_TABLE_LOG_API", MM_PREFIX."log_api");
define("MM_TABLE_ORDER_HISTORY", MM_PREFIX."order_history");
define("MM_TABLE_CORE_PAGE_TYPES", MM_PREFIX."core_page_types");
define("MM_TABLE_CONTEXTS", MM_PREFIX."contexts");
define("MM_TABLE_CRON", MM_PREFIX."cron");
define("MM_TABLE_POSTS_ACCESS", MM_PREFIX."posts_access");
define("MM_TABLE_ACCESS_TAG_PRODUCTS", MM_PREFIX."access_tag_products");
define("MM_TABLE_SMARTTAGS", MM_PREFIX."smarttags");
define("MM_TABLE_ROLES", MM_PREFIX."roles");
define("MM_TABLE_SMARTTAG_CONTEXTS", MM_PREFIX."smarttag_contexts");
define("MM_TABLE_SMARTTAG_GROUPS", MM_PREFIX."smarttag_groups");
define("MM_TABLE_ACCOUNT_TYPES", MM_PREFIX."account_types");
define("MM_TABLE_PRODUCTS", MM_PREFIX."products");
define("MM_TABLE_CAMPAIGNS", MM_PREFIX."campaigns");
define("MM_TABLE_CAMPAIGN_SETTINGS", MM_PREFIX."campaign_settings");
define("MM_TABLE_CAMPAIGN_OPTIONS", MM_PREFIX."campaign_options");
define("MM_TABLE_ACCOUNT_MEMBER_TYPES", MM_PREFIX."account_member_types");
define("MM_TABLE_MEMBER_STATUS_TYPES", MM_PREFIX."member_status_types");
define("MM_TABLE_EMAIL_ACCOUNTS", MM_PREFIX."email_accounts");
define("MM_TABLE_PERMISSIONS", MM_PREFIX."permissions");
define("MM_TABLE_CORE_PAGE_TAG_REQUIREMENTS", MM_PREFIX."corepage_tag_requirements");
define("MM_TABLE_CONTAINER", MM_PREFIX."container");
define("MM_TABLE_ACCESS_LOGS", MM_PREFIX."access_logs");
define("MM_TABLE_API_KEYS", MM_PREFIX."api_keys");
define("MM_TABLE_NOTIFICATION_EVENT_TYPES", MM_PREFIX."notification_event_types");
define("MM_TABLE_CUSTOM_FIELDS", MM_PREFIX."custom_fields");
define("MM_TABLE_CUSTOM_FIELD_DATA", MM_PREFIX."custom_field_data");
define("MM_TABLE_VERSION_RELEASES", MM_PREFIX."version_releases");

/** MODULE NAMES **/
define("MM_MODULE_DASHBOARD", "mm_dashboard");
define("MM_MODULE_MANAGE_INSTALL", "mm_manage_install");

define("MM_MODULE_DASHBOARD_VIEW", "dashboard");

define("MM_MODULE_CONFIGURE_SITE", "mm_configure_site");
 
define("MM_MODULE_ACCESS_RIGHTS", "access_rights");
define("MM_MODULE_MEMBER_TYPES", "member_types");
define("MM_MODULE_SITE_MANAGEMENT_DIALOG", "sitemgmt");
define("MM_MODULE_SITE_MANAGEMENT", "site_management");
define("MM_MODULE_ACCESS_TAGS", "access_tags");
define("MM_MODULE_API", "api");
define("MM_MODULE_INSTANT_NOTIFICATION", "instant_notification");
define("MM_MODULE_UNIT_TEST", "unit_test");
define("MM_MODULE_CUSTOM_FIELD", "custom_field");
define("MM_MODULE_API_TEST", "api_test");
define("MM_MODULE_ACCOUNT_TYPES", "account_types");
 
define("MM_MODULE_EMAIL_SETTINGS", "email_settings");
define("MM_MODULE_EMAIL_ACCOUNTS", "email_accounts");
define("MM_MODULE_EMAIL_TEMPLATES", "email_templates");
 
define("MM_MODULE_PRODUCTS", "products");
define("MM_MODULE_SHIPPING", "shipping");
define("MM_MODULE_COUNTRIES", "countries");
define("MM_MODULE_PAYMENT", "payment_options");
define("MM_MODULE_GATEWAY", "gateways");

define("MM_MODULE_LIMELIGHT", "limelight");
define("MM_MODULE_DELIVERY_SCHEDULE_MANAGER", "delivery_schedule_manager");
define("MM_MODULE_META", "page_meta");
 
define("MM_MODULE_MANAGE_MEMBERS", "mm_manage_members");
 
define("MM_MODULE_BROWSE_MEMBERS", "members");
define("MM_MODULE_IMPORT_MEMBERS", "import");
define("MM_MODULE_DETAILS", "member_details");
define("MM_MODULE_DETAILS_GENERAL", "details_general");
define("MM_MODULE_DETAILS_CUSTOM_FIELDS", "details_custom_fields");
define("MM_MODULE_DETAILS_BILLING", "details_billing");
define("MM_MODULE_DETAILS_SHIPPING", "details_shipping");
define("MM_MODULE_DETAILS_ACCESS_RIGHTS", "details_access_rights");
define("MM_MODULE_DETAILS_ACCESS_RIGHTS_ACCESS_TAGS", "details_access_rights_access_tags");
define("MM_MODULE_DETAILS_ORDER_HISTORY", "details_order_history");
define("MM_MODULE_DETAILS_BILLING_INFO", "details_billing_info");
define("MM_MODULE_DETAILS_SHIPPING_INFO", "details_shipping_info");
 
define("MM_MODULE_SETTINGS", "mm_additional_settings");

define("MM_MODULE_REGISTRATION_SETTINGS", "mm_registration_settings");
define("MM_MODULE_EVENT_HANDLING", "event_handling");
define("MM_MODULE_SETTINGS_SITE_MANAGEMENT", "site_management");
define("MM_MODULE_SETTINGS_STANDARD_PAYMENT_TEST", "standard_ipn_test");
define("MM_MODULE_SETTINGS_API_TEST", "api_test");
define("MM_MODULE_SETTINGS_TERMS", "terms");
define("MM_MODULE_SETTINGS_SSL", "ssl");
define("MM_MODULE_SETTINGS_AFFILIATE_TRACKING", "affiliate_tracking");
define("MM_MODULE_SETTINGS_MY_ACCOUNT", "my_account_settings");
define("MM_MODULE_SETTINGS_UNINSTALL", "uninstall");
define("MM_MODULE_SETTINGS_CUSTOM_FIELDS", "custom_field");
define("MM_MODULE_SETTINGS_LL_INTEGRATION", "llintegration");
define("MM_MODULE_SETTINGS_UNIT_TEST", "unit_test");

define("MM_MODULE_INTEGRATION_TOOLS", "mm_integration");
define("MM_MODULE_LOGS", "logs_access");
define("MM_MODULE_REPORTS", "reports");
define("MM_MODULE_ECOMMERCE", "ecommerce_settings");
define("MM_MODULE_ADMIN_TOOLS", "mm_admintools");

define("MM_MODULE_SETTINGS_API", "api");
define("MM_MODULE_SETTINGS_CRON_MANAGEMENT", "cron_settings");
define("MM_MODULE_SETTINGS_FREE_MEMBER_FORM", "free_member_form");
define("MM_MODULE_PHP_FUNCTIONS", "php_functions");
 
define("MM_MODULE_ERROR", "error");

/** OPTION NAMES **/
define("MM_OPTION_SHOW_GUIDE", "mm-option-show-guide");
define("MM_OPTION_TERMS_STATUS", "mm-option-terms-status");
define("MM_OPTION_TERMS_CONTENT", "mm-option-terms-content");
define("MM_OPTION_TERMS_AFFILIATE", "mm-option-affiliate");
define("MM_OPTION_TERMS_SUB_AFFILIATE", "mm-option-sub-affiliate");
define("MM_OPTION_TERMS_AFFILIATE_LIFESPAN", "mm-option-affiliate-lifespan");
?>