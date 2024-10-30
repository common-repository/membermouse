<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
 class MM_UserHooks
 {	
	public function setupDefinitions()
	{	
		global $current_user;
		if (!session_id())
			session_start();
			
		$abspath = ABSPATH;
		
		if(!preg_match("/(\\".DIRECTORY_SEPARATOR.")$/", ABSPATH))
			$abspath .= DIRECTORY_SEPARATOR;
		
		define("MM_TEMPLATE_BASE", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."templates");
		define("MM_TEMPLATE_META", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."metabox");
		define("MM_TEMPLATE_USER", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."user");
		define("MM_TEMPLATE_ADMIN", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."admin");
		define("MM_TEMPLATE_COMMON", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."common");
		define("MM_TEMPLATE_SMARTTAGS", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."smarttags");
		define("MM_MODULES", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."modules");
		define("MM_PLUGIN_ABSPATH", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME);
		define("MM_DATA_DIR", $abspath."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."".MM_PLUGIN_NAME."".DIRECTORY_SEPARATOR."data");
		define("MM_IMAGES_PATH", MM_PLUGIN_ABSPATH."".DIRECTORY_SEPARATOR."resources".DIRECTORY_SEPARATOR."images");
		
		$baseurl = MM_OptionUtils::getOption("siteurl");
		define("MM_WP_ADMIN_URL", $baseurl."/wp-admin/");
		define("MM_MODULES_URL", $baseurl."/wp-content/plugins/".MM_PLUGIN_NAME."/modules");
		define("MM_API_BASE_URL", $baseurl."/wp-content/plugins/".MM_PLUGIN_NAME."/api");
		define("MM_API_URL", $baseurl."/wp-content/plugins/".MM_PLUGIN_NAME."/api/request.php");
		define("MM_RESOURCES_URL", $baseurl."/wp-content/plugins/".MM_PLUGIN_NAME."/resources/");
		define("MM_TEMPLATES_URL", $baseurl."/wp-content/plugins/".MM_PLUGIN_NAME."/templates/");
		define("MM_IMAGES_URL", MM_RESOURCES_URL."images/");
		
		define("MM_NO_SSL_CODE", base64_encode(MM_OptionUtils::getOption("siteurl")));
			
		if(isset($_GET["export_file"]) && $_GET["export_file"]==MM_GET_KEY){
			require_once(MM_MODULES."/export_file.php");
		}	
	
		$user = new MM_User($current_user->ID);
		if(!$user->canAccessPage(MM_MODULE_CONFIGURE_SITE)){
			if(!isset($_GET["page"]) || (isset($_GET["page"]) && !preg_match("/^(mm_)/", $_GET["page"]))){
				wp_redirect("admin.php?page=mm_dashboard");		
				exit;
			}
		}
		
	 	if(!is_admin())
	 	{	
			if(class_exists("MM_SmartTagEngine"))
			{
				// remove_all_shortcodes();
			 	$smartTagLibrary = new MM_SmartTagEngine();
			 	$smartTagLibrary->loadTags();
			}
	 	}
	 //	echo base64_encode("phpobj");
	 	
		if(isset($_GET["show_login_attempts"])){
			global $wpdb;
			$sql = "select * from ".MM_TABLE_ACCESS_LOGS." where event_type='login' and user_id='".$_GET["show_login_attempts"]."' and DATE(date_added)=DATE(NOW()) group by ip";
			$rows = $wpdb->get_results($sql);
			echo $_GET["show_login_attempts"]." attempts at logging in: <br />";
			
			echo "<pre>";
			var_dump($rows);
			echo "</pre>";
			exit;
		}
		
	 	if(isset($_GET["show_log_api"])){
	 		$date1 = (isset($_REQUEST["date1"]))?$_REQUEST["date1"]:Date("Y-m-d");
	 		$date2 = (isset($_REQUEST["date2"]))?$_REQUEST["date2"]:Date("Y-m-d");
	 		$logs = MM_LogApi::printLog($date1, $date2);
	 		echo "<pre>";
	 		var_dump($logs);
	 		echo "</pre>";
	 		exit;
	 	}
	 	
		if(isset($_POST["exportdata"])){
			$data = MM_Session::value(MM_Session::$KEY_CSV);
			
			if($data !==false){
				header("Content-type: text/csv");
			    header("Content-Disposition: filename=mm_export_".Date("Y-m-d").".csv");
			    header("Pragma: no-cache");
			    header("Expires: 0");
				echo $data;
				
				MM_Session::clear(MM_Session::$KEY_CSV);
				exit;
			}
		}
		
		if(MM_Utils::isAdmin()){
			$checkedAdmin = MM_OptionUtils::getOption(MM_OptionUtils::$OPTION_KEY_SSL_ADMIN);
			if($checkedAdmin=='1'){
				if(isset($_GET["nossl"]) && $_GET["nossl"]==MM_NO_SSL_CODE){
					MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_SSL_ADMIN,"0");
					echo "Your admin panel SSL has been deactivated. <a href='".MM_WP_ADMIN_URL."'>Go to admin panel.</a>";
					exit;
				}
				else if(isset($_GET["nossl"])){
					echo MM_NO_SSL_CODE;
					exit;
				}
				
				$urlObj = new MM_Url();
				if(!$urlObj->isSSL()){
					if($urlObj->hasSSL()){
						$urlObj->forceSSL();	
					}
				}
			}
		}
		
		if(!is_admin()){
			$accessLog = new MM_AccessLog();
			$accessLog->setEventType(MM_AccessLog::$MM_TYPE_PAGE);
			$accessLog->setReferrer(MM_Utils::getReferrer());
			$accessLog->setIp(MM_Utils::getClientIPAddress());
			$accessLog->setUrl(MM_Utils::constructPageUrl());
			$accessLog->setUserId($current_user->ID);
			$accessLog->commitData();
		}
	
		if(is_admin()){
			if(isset($_GET["from_mm"])){
				switch($_GET["from_mm"]){
					case "deactivate":
						@deactivate_plugins(ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/index.php", false);
						break;
					case "updateMMContainer":
						global $wpdb;
						
						$sql= "update mm_container set obj = 'Ci8qKgogKiAKICogCk1lbWJlck1vdXNlKFRNKSAoaHR0cDovL3d3dy5tZW1iZXJtb3VzZS5jb20pCihjKSAyMDEwLTIwMTEgUG9wIEZpenogU3R1ZGlvcywgTExDLiBBbGwgcmlnaHRzIHJlc2VydmVkLgogKi8KY2xhc3MgTU1fTWVtYmVyTW91c2VTZXJ2aWNlCnsKCXB1YmxpYyBzdGF0aWMgJFNFUlZFUklQID0gTU1fQ0VOVFJBTF9TRVJWRVI7IAoKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9BREQgPSAiYWRkTU1TaXRlIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9BQ1RJVkFURSA9ICJhY3RpdmF0ZU1NU2l0ZSI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfR0VUID0gImdldE1NU2l0ZSI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfQVVUSCA9ICJhdXRoTU1TaXRlIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9VUERBVEUgPSAidXBkYXRlTU1TaXRlIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9HRVRfU0lURVMgPSAiZ2V0TU1TaXRlcyI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfR0VUX0FMTF9TSVRFUyA9ICJnZXRBbGxNTVNpdGVzIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9HRVRfQ09OVEVYVFVBTF9IRUxQID0gImdldENvbnRleHR1YWxIZWxwIjsKCXB1YmxpYyBzdGF0aWMgJE1FVEhPRF9ERUFDVElWQVRFID0gImRlYWN0aXZhdGVNTVNpdGUiOwoJcHVibGljIHN0YXRpYyAkTUVUSE9EX0FSQ0hJVkUgPSAiYXJjaGl2ZU1NU2l0ZSI7CglwdWJsaWMgc3RhdGljICRNRVRIT0RfVVBEQVRFX0NBTVBBSUdOU19JTl9VU0UgPSAidXBkYXRlQ2FtcGFpZ25zSW5Vc2UiOwoJCglwcml2YXRlIHN0YXRpYyBmdW5jdGlvbiBzZW5kUmVxdWVzdCgkbWV0aG9kLCAkcG9zdHZhcnMpCgl7CgkJJHVybCA9IHNlbGY6OiRTRVJWRVJJUC4kbWV0aG9kOwoJCQoJCUxvZ01lOjp3cml0ZSgiTU1fTWVtYmVyTW91c2VTZXJ2aWNlLnNlbmRSZXF1ZXN0KCk6IFVSTDogIi4kdXJsLiIgOiAiLiRwb3N0dmFycyk7CgkJCgkJJGNoID0gY3VybF9pbml0KCR1cmwpOwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9QT1NUICAgICAgLDEpOwoJCWN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9QT1NURklFTERTICAgICwgJHBvc3R2YXJzKTsKCQljdXJsX3NldG9wdCgkY2gsIENVUkxPUFRfSEVBREVSICAgICAgLDApOyAgLy8gRE8gTk9UIFJFVFVSTiBIVFRQIEhFQURFUlMKCQljdXJsX3NldG9wdCgkY2gsIENVUkxPUFRfUkVUVVJOVFJBTlNGRVIgICwxKTsgIC8vIFJFVFVSTiBUSEUgQ09OVEVOVFMgT0YgVEhFIENBTEwKCQkkY29udGVudHMgPSBjdXJsX2V4ZWMoJGNoKTsKCQljdXJsX2Nsb3NlKCRjaCk7CQoJCQoJCUxvZ01lOjp3cml0ZSgiTU1fTWVtYmVyTW91c2VTZXJ2aWNlOjpzZW5kUmVxdWVzdCA6ICIuJGNvbnRlbnRzKTsKCQkkanNvbiA9IGpzb25fZGVjb2RlKCRjb250ZW50cyk7CgkJJGpzb24tPnJlc3BvbnNlX2RhdGEgPSBqc29uX2RlY29kZSgkanNvbi0+cmVzcG9uc2VfZGF0YSk7CgkJCgkJcmV0dXJuICRqc29uOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGFyY2hpdmVTaXRlKCRpZCkKCXsKCQkkYXBpc2VjcmV0ID0gZ2V0X29wdGlvbigibW0tYXBpc2VjcmV0Iik7CgkJJGFwaWtleSA9IGdldF9vcHRpb24oIm1tLWFwaWtleSIpOwoJCQoJCSR2ZXJzaW9uPSBNTV9TaXRlOjpnZXRQbHVnaW5WZXJzaW9uKCk7CgkJJHBvc3R2YXJzID0gImFwaXNlY3JldD0iLiRhcGlzZWNyZXQuIiZhcGlrZXk9Ii4kYXBpa2V5LiImaWQ9Ii4kaWQ7CgkJcmV0dXJuIHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfQVJDSElWRSwgJHBvc3R2YXJzKTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiB1cGRhdGVDYW1wYWlnblVzYWdlKCRzaXRlSWQsICRjYW1wYWlnbnNJblVzZSl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkdmVyc2lvbj0gTU1fU2l0ZTo6Z2V0UGx1Z2luVmVyc2lvbigpOwoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJmNhbXBhaWduc19pbl91c2U9Ii4kY2FtcGFpZ25zSW5Vc2UuIiZpZD0iLiRzaXRlSWQ7CgkJcmV0dXJuIHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfVVBEQVRFX0NBTVBBSUdOU19JTl9VU0UsICRwb3N0dmFycyk7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gZ2V0U2l0ZXMoJG1lbWJlcklkLCAkb3JkZXJTb3J0Q29sdW1uPSJkYXRlX2FkZGVkIiwgJG9yZGVyU29ydERpcj0iZGVzYyIpCgl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkdmVyc2lvbj0gTU1fU2l0ZTo6Z2V0UGx1Z2luVmVyc2lvbigpOwoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJm1lbWJlcl9pZD0iLiRtZW1iZXJJZC4iJnZlcnNpb249Ii4kdmVyc2lvbjsKCQkKCQlyZXR1cm4gc2VsZjo6c2VuZFJlcXVlc3Qoc2VsZjo6JE1FVEhPRF9HRVRfU0lURVMsICRwb3N0dmFycyk7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gZ2V0QWxsU2l0ZXMoJHNvcnRDb2x1bW49ImRhdGVfYWRkZWQiLCAkc29ydERpcj0iZGVzYyIsICRsaW1pdFN0YXJ0PTAsICRsaW1pdFRvdGFsPTEwKQoJewoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJJHZlcnNpb249IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXkuIiZ2ZXJzaW9uPSIuJHZlcnNpb247CgkJJHBvc3R2YXJzLj0iJnNvcnRfY29sdW1uPSIuJHNvcnRDb2x1bW4uIiZzb3J0X2Rpcj0iLiRzb3J0RGlyLiImbGltaXRfc3RhcnQ9Ii4kbGltaXRTdGFydC4iJmxpbWl0X3RvdGFsPSIuJGxpbWl0VG90YWw7CgkJcmV0dXJuIHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfR0VUX0FMTF9TSVRFUywgJHBvc3R2YXJzKTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBnZXRDb250ZXh0dWFsSGVscCgkc2VjdGlvbklkKQoJewoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJJHZlcnNpb249IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXkuIiZ2ZXJzaW9uPSIuJHZlcnNpb247CgkJJHBvc3R2YXJzLj0iJnNlY3Rpb25faWQ9Ii4kc2VjdGlvbklkOwoJCQoJCXJldHVybiBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX0dFVF9DT05URVhUVUFMX0hFTFAsICRwb3N0dmFycyk7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gZGVhY3RpdmF0ZVNpdGUoKQoJewoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJJHBvc3R2YXJzID0gImFwaXNlY3JldD0iLiRhcGlzZWNyZXQuIiZhcGlrZXk9Ii4kYXBpa2V5OwoJCSRjb250ZW50cyA9IHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfREVBQ1RJVkFURSwgJHBvc3R2YXJzKTsKCQlzZWxmOjpjbGVhblVwT3B0aW9ucygpOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGlzU3VjY2Vzc2Z1bFJlcXVlc3QoJG9iaikKCXsKCQlpZigkb2JqLT5yZXNwb25zZV9jb2RlID09ICIyMDAiKSB7CgkJCXJldHVybiB0cnVlOwoJCX0KCQkKCQlyZXR1cm4gZmFsc2U7Cgl9CgkKCXB1YmxpYyBzdGF0aWMgZnVuY3Rpb24gYWN0aXZhdGVTaXRlKCR1cmwpCgl7CgkJJHZlcnNpb249IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAidXJsPSIudXJsZW5jb2RlKCR1cmwpLiImdmVyc2lvbj0iLiR2ZXJzaW9uOwoJCQoJCSRqc29uX2RhdGEgPSBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX0FDVElWQVRFLCAkcG9zdHZhcnMpOwoJCQoJCWlmKCFzZWxmOjppc1N1Y2Nlc3NmdWxSZXF1ZXN0KCRqc29uX2RhdGEpKQoJCXsKCQkJc2VsZjo6Y2xlYW5VcE9wdGlvbnMoKTsKCQkJcmV0dXJuIGZhbHNlOwoJCX0KCQkkanNvbiA9ICRqc29uX2RhdGEtPnJlc3BvbnNlX2RhdGE7CgkJcmV0dXJuIHNlbGY6OnVwZGF0ZVNpdGVJbmZvKCRqc29uKTsKCX0KCQoJcHJpdmF0ZSBzdGF0aWMgZnVuY3Rpb24gYXV0aG9yaXplU2l0ZSgpCgl7CgkJZ2xvYmFsICR3cGRiOwoJCQoJCSRhcGlzZWNyZXQgPSBnZXRfb3B0aW9uKCJtbS1hcGlzZWNyZXQiKTsKCQkkYXBpa2V5ID0gZ2V0X29wdGlvbigibW0tYXBpa2V5Iik7CgkJCgkJLy8gY2FsY3VsYXRlIGN1cnJlbnQgbnVtYmVyIG9mIHRvdGFsIG1lbWJlcnMKCQkkc3FsID0gIlNFTEVDVCBjb3VudCgqKSBhcyB0b3RhbCBGUk9NICIuJHdwZGItPnVzZXJzLiIgV0hFUkUgbW1fcmVnaXN0ZXJlZCAhPSAnJyBBTkQgbW1fc3RhdHVzICE9IDIiOwoJCSRyZXN1bHQgPSAkd3BkYi0+Z2V0X3Jvdygkc3FsKTsKCQkKCQlpZigkcmVzdWx0KSB7CgkJCSR0b3RhbE1lbWJlcnMgPSAkcmVzdWx0LT50b3RhbDsKCQl9CgkJZWxzZSB7CgkJCSR0b3RhbE1lbWJlcnMgPSAwOwoJCX0KCQkKCQkkc3FsID0gIlNFTEVDVCAKCQkJCQljb3VudCh1LmlkKSBhcyB0b3RhbCAKCQkJCUZST00gCgkJCQkJIi4kd3BkYi0+dXNlcnMuIiB1LCAiLk1NX1RBQkxFX01FTUJFUl9UWVBFUy4iIG0gCgkJCQlXSEVSRSAKCQkJCQl1Lm1tX3JlZ2lzdGVyZWQgIT0gJycgQU5EIAoJCQkJCXUubW1fc3RhdHVzICE9ICIuTU1fTWVtYmVyU3RhdHVzOjokQ0FOQ0VMRUQuIiBBTkQgCgkJCQkJdS5tbV9tZW1iZXJfdHlwZV9pZCA9IG0uaWQgQU5ECgkJCQkJbS5pc19mcmVlICE9ICcxJwoJCQkiOwoJCSRyZXN1bHQgPSAkd3BkYi0+Z2V0X3Jvdygkc3FsKTsKCQkKCQlpZigkcmVzdWx0KSB7CgkJCSRwYWlkTWVtYmVycyA9ICRyZXN1bHQtPnRvdGFsOwoJCX0gCgkJZWxzZSB7CgkJCSRwYWlkTWVtYmVycyA9IDA7CgkJfQoJCQoJCSR2ZXJzaW9uID0gTU1fU2l0ZTo6Z2V0UGx1Z2luVmVyc2lvbigpOwoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJnRvdGFsX21lbWJlcnM9Ii4kdG90YWxNZW1iZXJzLiImcGFpZF9tZW1iZXJzPSIuJHBhaWRNZW1iZXJzLiImdmVyc2lvbj0iLiR2ZXJzaW9uOwoJCUxvZ01lOjp3cml0ZSgiTU1TZXJ2aWNlIC0gcG9zdHZhcnM6ICIuJHBvc3R2YXJzKTsKCQkkY29udGVudHMgPSBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX0FVVEgsICRwb3N0dmFycyk7CgkJCgkJaWYoIXNlbGY6OmlzU3VjY2Vzc2Z1bFJlcXVlc3QoJGNvbnRlbnRzKSkKCQl7CgkJCXNlbGY6OmNsZWFuVXBPcHRpb25zKCk7CgkJCXJldHVybiBmYWxzZTsKCQl9CgkJCgkJJGpzb24gPSAkY29udGVudHMtPnJlc3BvbnNlX2RhdGE7CgkJc2VsZjo6dXBkYXRlU2l0ZUluZm8oJGpzb24pOwoJCQoJCXJldHVybiB0cnVlOwoJfQoJCglwcml2YXRlIHN0YXRpYyBmdW5jdGlvbiB1cGRhdGVTaXRlSW5mbygkanNvbikKCXsKCQlpZihpc3NldCgkanNvbi0+YXBpa2V5KSkKCQl7CQoJCQlmb3JlYWNoKCRqc29uIGFzICRrPT4kdmFsKQoJCQl7CgkJCQlpZihpc19zdHJpbmcoJHZhbCkpIHsKCQkJCQlNTV9PcHRpb25VdGlsczo6c2V0T3B0aW9uKCJtbS0iLiRrLCBzdHJpcHNsYXNoZXMoJHZhbCkpOwoJCQkJfQoJCQl9CgkJCQoJCQlzZWxmOjpzYXZlRHluYW1pY0NsYXNzZXMoJGpzb24tPmNsYXNzZXMpOwoJCQlNTV9PcHRpb25VdGlsczo6c2V0T3B0aW9uKCJtbS1sYXN0X2NoZWNrIiwgZGF0ZSgiWS1tLWQgaDppOnMiKSk7CgkJCgkJCXJldHVybiAkanNvbjsKCQl9CgkJCgkJcmV0dXJuIGZhbHNlOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGNsZWFuVXBPcHRpb25zKCkKCXsKCQkkb3B0aW9ucyA9IGFycmF5KCJtbS1pZCIsICJtbS1uYW1lIiwgIm1tLWxvY2F0aW9uIiwgIm1tLWNhbXBhaWduX2lkcyIsICJtbS1saW1lbGlnaHRfdXJsIiwgIm1tLWxpbWVsaWdodF9wYXNzd29yZCIsICJtbS1saW1lbGlnaHRfdXNlcm5hbWUiLAoJCQkJCQkibW0tc3RhdHVzIiwgIm1tLWlwYWRkcmVzcyIsICJtbS1tZW1iZXJfaWQiLCAibW0tbGFzdF9jaGVjayIsICJtbS1pc19tZW1iZXJtb3VzZSIsICJtbS1pbnRlcnZhbCIsICJtbS1hcGlzZWNyZXQiLCAibW0tYXBpa2V5IiwgCgkJCQkJCSJtbS10b3RhbF9tZW1iZXJzIiwgIm1tLXBhaWRfbWVtYmVycyIsICJtbS1sYXN0X2NoZWNrZWQiLCAibW0tY2FtcGFpZ25zX2luX3VzZSIsICJtbS1pc19kZXYiKTsKCQkKCQlmb3JlYWNoKCRvcHRpb25zIGFzICRvcHRpb24pIHsKCQkJZGVsZXRlX29wdGlvbigkb3B0aW9uKTsKCQl9Cgl9CgkKCXByaXZhdGUgc3RhdGljIGZ1bmN0aW9uIGdldFNpdGVJbmZvKCkKCXsKCQkkZGF0YSA9IG5ldyBzdGRDbGFzcygpOwoJCSRkYXRhLT5pZCA9IGdldF9vcHRpb24oIm1tLWlkIik7CgkJJGRhdGEtPm5hbWUgPSBnZXRfb3B0aW9uKCJtbS1uYW1lIik7CgkJJGRhdGEtPmlzX2RldiA9IGdldF9vcHRpb24oIm1tLWlzX2RldiIpOwoJCSRkYXRhLT5sb2NhdGlvbiA9IGdldF9vcHRpb24oIm1tLWxvY2F0aW9uIik7CgkJJGRhdGEtPmNhbXBhaWduX2lkcyA9IGdldF9vcHRpb24oIm1tLWNhbXBhaWduX2lkcyIpOwoJCSRkYXRhLT5jYW1wYWlnbnNfaW5fdXNlID0gZ2V0X29wdGlvbigibW0tY2FtcGFpZ25zX2luX3VzZSIpOwoJCSRkYXRhLT5saW1lbGlnaHRfdXJsID0gZ2V0X29wdGlvbigibW0tbGltZWxpZ2h0X3VybCIpOwoJCSRkYXRhLT5saW1lbGlnaHRfdXNlcm5hbWUgPSBnZXRfb3B0aW9uKCJtbS1saW1lbGlnaHRfdXNlcm5hbWUiKTsKCQkkZGF0YS0+bGltZWxpZ2h0X3Bhc3N3b3JkID0gZ2V0X29wdGlvbigibW0tbGltZWxpZ2h0X3Bhc3N3b3JkIik7CgkJJGRhdGEtPnN0YXR1cyA9IGdldF9vcHRpb24oIm1tLXN0YXR1cyIpOwoJCSRkYXRhLT5pc19tZW1iZXJtb3VzZSA9IChib29sKWdldF9vcHRpb24oIm1tLWlzX21lbWJlcm1vdXNlIik7CgkJJGRhdGEtPnBhaWRfbWVtYmVycyA9IGdldF9vcHRpb24oIm1tLXBhaWRfbWVtYmVycyIpOwoJCSRkYXRhLT50b3RhbF9tZW1iZXJzID0gZ2V0X29wdGlvbigibW0tdG90YWxfbWVtYmVycyIpOwoJCQoJCXJldHVybiAkZGF0YTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBzaG91bGRBdXRob3JpemUoKQoJewoJCWlmKCFwcmVnX21hdGNoKCIvKHBsdWdpbnNcLnBocCkvIiwgJF9TRVJWRVJbIlBIUF9TRUxGIl0pICYmIGlzX2FkbWluKCkpCgkJewoJCQkkbGFzdENoZWNrZWQgPSBnZXRfb3B0aW9uKCJtbS1sYXN0X2NoZWNrIik7CQoJCQkkbmV4dENoZWNrID0gc3RydG90aW1lKCIrIi5iYXNlNjRfZGVjb2RlKGdldF9vcHRpb24oIm1tLWludGVydmFsIikpLiIgZGF5Iiwgc3RydG90aW1lKCRsYXN0Q2hlY2tlZCkpOwoJCQkkdG9kYXkgID1EYXRlKCJZLW0tZCBoOmk6cyIpOwoJCQkKCQkJaWYoc3RydG90aW1lKCR0b2RheSkgPj0gJG5leHRDaGVjaykgewoJCQkJcmV0dXJuIHRydWU7CgkJCX0KCQkJCgkJCXJldHVybiBmYWxzZTsKCQl9CgkJCgkJcmV0dXJuIGZhbHNlOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGdldFNpdGVEYXRhKCkKCXsKCSAJaWYoc2VsZjo6c2hvdWxkQXV0aG9yaXplKCkpCgkgCXsKCQkJaWYoc2VsZjo6YXV0aG9yaXplU2l0ZSgpKSB7CgkJCQlyZXR1cm4gc2VsZjo6Z2V0U2l0ZUluZm8oKTsKCQkJfQoJCQllbHNlCgkJCXsKCQkJCSRlcnJvciA9ICJUaGUgTWVtYmVyTW91c2UgcGx1Z2luIGNvdWxkIDxiPk5PVDwvYj4gYmUgYXV0aGVudGljYXRlZCBieSBtZW1iZXJtb3VzZS5jb20uIFRoZSBwbHVnaW4gd2lsbCBub3cgYmUgZGVhY3RpdmF0ZWQuIjsKCQkJCWhlYWRlcigiTG9jYXRpb246IHBsdWdpbnMucGhwPyIuTU1fU2Vzc2lvbjo6JFBBUkFNX0NPTU1BTkRfREVBQ1RJVkFURS4iPTEmIi5NTV9TZXNzaW9uOjokUEFSQU1fTUVTU0FHRV9LRVkuIj0iLnVybGVuY29kZSgkZXJyb3IpKTsKCQkJCWV4aXQ7CgkJCX0KCSAJfQoJIAkKCQlyZXR1cm4gc2VsZjo6Z2V0U2l0ZUluZm8oKTsKCX0KCQoJcHVibGljIHN0YXRpYyBmdW5jdGlvbiBnZXRTaXRlKCRzaXRlSWQpCgl7CgkJJGFwaXNlY3JldCA9IGdldF9vcHRpb24oIm1tLWFwaXNlY3JldCIpOwoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkKCQkkdmVyc2lvbiA9IE1NX1NpdGU6OmdldFBsdWdpblZlcnNpb24oKTsKCQkkcG9zdHZhcnMgPSAiYXBpc2VjcmV0PSIuJGFwaXNlY3JldC4iJmFwaWtleT0iLiRhcGlrZXkuIiZpZD0iLiRzaXRlSWQ7CgkJJGNvbnRlbnRzID0gc2VsZjo6c2VuZFJlcXVlc3Qoc2VsZjo6JE1FVEhPRF9HRVQsICRwb3N0dmFycyk7CgkJCgkJaWYoIXNlbGY6OmlzU3VjY2Vzc2Z1bFJlcXVlc3QoJGNvbnRlbnRzKSkKCQl7CgkJCXJldHVybiBmYWxzZTsKCQl9CgkJCgkJJGpzb24gPSAkY29udGVudHMtPnJlc3BvbnNlX2RhdGE7CgkJCgkJcmV0dXJuICRqc29uOwoJfQoJCglwdWJsaWMgc3RhdGljIGZ1bmN0aW9uIGNvbW1pdFNpdGVEYXRhKCRtZW1iZXJJZCwgTU1fU2l0ZSAkc2l0ZSwgJGlzQWRtaW49ZmFsc2UpCgl7CgkJLy8gTU0gb25seQoJCSRhcGlrZXkgPSBnZXRfb3B0aW9uKCJtbS1hcGlrZXkiKTsKCQkkYXBpc2VjcmV0ID0gZ2V0X29wdGlvbigibW0tYXBpc2VjcmV0Iik7CgkJJHNpdGVJZCA9ICRzaXRlLT5nZXRJZCgpOwoJCQoJCSRwb3N0dmFycyA9ICJhcGlzZWNyZXQ9Ii4kYXBpc2VjcmV0LiImYXBpa2V5PSIuJGFwaWtleS4iJiI7CgkJJHBvc3R2YXJzIC49ICJtZW1iZXJfaWQ9Ii4kbWVtYmVySWQuIiYiOwoJCSRwb3N0dmFycyAuPSAiaWQ9Ii4kc2l0ZUlkLiImIjsKCQkkcG9zdHZhcnMgLj0gIm5hbWU9Ii4kc2l0ZS0+Z2V0TmFtZSgpLiImIjsKCQkkcG9zdHZhcnMgLj0gImNhbXBhaWduX2lkcz0iLiRzaXRlLT5nZXRDYW1wYWlnbklkcygpLiImIjsKCQkkcG9zdHZhcnMgLj0gImxvY2F0aW9uPSIuJHNpdGUtPmdldExvY2F0aW9uKCkuIiYiOwoJCSRwb3N0dmFycyAuPSAibGltZWxpZ2h0X3VybD0iLiRzaXRlLT5nZXRMTFVybCgpLiImIjsKCQkkcG9zdHZhcnMgLj0gImxpbWVsaWdodF91c2VybmFtZT0iLiRzaXRlLT5nZXRMTFVzZXJuYW1lKCkuIiYiOwoJCSRwb3N0dmFycyAuPSAibGltZWxpZ2h0X3Bhc3N3b3JkPSIuJHNpdGUtPmdldExMUGFzc3dvcmRFbmNyeXB0ZWQoKS4iJiI7CgkJaWYoJGlzQWRtaW4pewoJCQkkcG9zdHZhcnMgLj0gInN0YXR1cz0iLiRzaXRlLT5nZXRTdGF0dXMoKS4iJiI7CgkJCSRwb3N0dmFycyAuPSAiaXNfZGV2PSIuJHNpdGUtPmlzRGV2KCkuIiYiOwoJCQkkcG9zdHZhcnMgLj0gImlzX21tPSIuJHNpdGUtPmlzTU0oKS4iJiI7CgkJfQoJCSRwb3N0dmFycyAuPSAicGFpZF9tZW1iZXJzPSIuJHNpdGUtPmdldFBhaWRNZW1iZXJzKCkuIiYiOwoJCSRwb3N0dmFycyAuPSAidG90YWxfbWVtYmVycz0iLiRzaXRlLT5nZXRUb3RhbE1lbWJlcnMoKTsKCQlMb2dNZTo6d3JpdGUoImNvbW1pdFNpdGVEYXRhKCkgOiAiLiRwb3N0dmFycyk7CgkJaWYoaXNzZXQoJHNpdGVJZCkgJiYgaW50dmFsKCRzaXRlSWQpID4gMCkgewoJCQkkY29udGVudHMgPSBzZWxmOjpzZW5kUmVxdWVzdChzZWxmOjokTUVUSE9EX1VQREFURSwgJHBvc3R2YXJzKTsKCQl9CgkJZWxzZSB7CgkJCSRjb250ZW50cyA9IHNlbGY6OnNlbmRSZXF1ZXN0KHNlbGY6OiRNRVRIT0RfQURELCAkcG9zdHZhcnMpOwoJCX0KCQkKCQlyZXR1cm4gJGNvbnRlbnRzOwoJfQoJCglwcml2YXRlIHN0YXRpYyBmdW5jdGlvbiBzYXZlRHluYW1pY0NsYXNzZXMoJGNsYXNzZXMpCgl7CgkJZ2xvYmFsICR3cGRiOwoJCQoJCWlmKGlzX29iamVjdCgkY2xhc3NlcykgfHwgaXNfYXJyYXkoJGNsYXNzZXMpKQoJCXsKCQkJJHNxbCA9ICJkZWxldGUgZnJvbSAiLk1NX1RBQkxFX0NPTlRBSU5FUi4iIHdoZXJlIGlzX3N5c3RlbT0nMCciOwoJCQkkd3BkYi0+cXVlcnkoJHNxbCk7CgkJCQoJCQlmb3JlYWNoKCRjbGFzc2VzIGFzICRjbGFzc05hbWU9PiRjbGFzc0VudHJ5KQoJCQl7CgkJCQkkc3FsID0gImluc2VydCBpbnRvICIuTU1fVEFCTEVfQ09OVEFJTkVSLiIgc2V0IAoJCQkJCQkJCW5hbWU9JyVzJywgCgkJCQkJCQkJb2JqPSclcycJCQoJCQkJCQkiOwoJCQkJCgkJCQkkd3BkYi0+cXVlcnkoJHdwZGItPnByZXBhcmUoJHNxbCwgJGNsYXNzTmFtZSwgJGNsYXNzRW50cnkpKTsKCQkJfQoJCX0KCQkKCQlyZXR1cm4gdHJ1ZTsKCX0KIH0KCg==' where name='membermouseservice' limit 1";
						$wpdb->query($sql);
						break;
				}
			}
		}
		
		global $current_user;
		
		$user = new MM_User($current_user->ID);
		$user->isAdmin();
		
		// update affiliate tracking
		if(class_exists("MM_RetentionReport")){
			MM_RetentionReport::setAffiliateCookies();
		}
		
	 }
	 
	public function loginFailed()
	{
		if(class_exists("MM_CorePageEngine"))
		{
			MM_Messages::addError("Invalid login or password.");
			wp_redirect(MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE));
			exit;
		}
	}
	
	
	public function handlePageAccess()
	{
		global $wp_query, $current_user;
		
		if(class_exists("MM_CorePageEngine"))
		{
			if(is_404())
			{
				// TODO custom 404 page?  Core Page?
	//	        status_header( 200 );
	//	        $wp_query->is_404=false;
			}
			
			if(MM_CorePageEngine::isFrontPage()) {
				LogMe::write("handlePageAccess() : check redirect to homepage ");
				MM_CorePageEngine::redirectToHomePage(true);
			}
			else if(isset($wp_query->post->ID) && intval($wp_query->post->ID)>0) {
				LogMe::write("handlePageAccess() : check redirect to cancellation ");
				MM_CorePageEngine::redirectToCancellationPage($wp_query->post->ID);
				
				$userObj = new MM_User($current_user->ID);
				if($userObj->getStatus() == MM_MemberStatus::$OVERDUE){
					$corePageEngine = new MM_CorePageEngine();
					if(!MM_CorePageEngine::isMyAccountCorePage($wp_query->post->ID)){
						if(!MM_CorePageEngine::isErrorCorePage($wp_query->post->ID)){
							$url = $corePageEngine->getUrl(MM_CorePageType::$ERROR, MM_ErrorType::$ACCOUNT_OVERDUE);
							$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_USER_ID, $userObj->getId(), true);
							$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_MESSAGE_KEY, MM_ErrorType::$ERROR_MSG_OVERDUE, true);
							
							header("Location: {$url}");
							exit;
					
						}
					}
				}
			}
			
			
			if(isset($wp_query->post->ID) && MM_CorePageEngine::isRegistrationCorePage($wp_query->post->ID)){
				$urlObj = new MM_Url();
				if(!$urlObj->isSSL()){
					if($urlObj->hasSSL()){
						$urlObj->forceSSL();	
					}
				}	
			}
			else if(isset($wp_query->post->ID) && MM_CorePageEngine::isMyAccountCorePage($wp_query->post->ID)){
				
				$urlObj = new MM_Url();
				if(!$urlObj->isSSL()){
					if($urlObj->hasSSL()){
						$urlObj->forceSSL();	
					}
				}
				
				$savedUserId = MM_Session::value(MM_Session::$KEY_UPDATE_USER_ID);
				if(intval($savedUserId)>0){
					wp_set_auth_cookie($savedUserId, true, is_ssl());
					wp_set_current_user($savedUserId);
					MM_Session::clear(MM_Session::$KEY_UPDATE_USER_ID);
				}
			}
			else{
				$urlObj = new MM_Url();
					
				if($urlObj->isSSL()){
					$siteurl = MM_OptionUtils::getOption("siteurl");
					if(!preg_match("/(https)/", $siteurl)){
						if(preg_match("/(https)/", $urlObj->get())){
							$urlObj->forceHTTP();
						}
					}
				}
			}
			
			
			if(!is_admin()){
			
				$protectedContent = new MM_ProtectedContentEngine();
			
				$postId = $wp_query->query_vars["page_id"];
			
				if(isset($wp_query->post->ID) && intval($wp_query->post->ID)>0){
			
					$postId = $wp_query->post->ID;
				}
				
				if(intval($postId)>0){
			
					if(!is_feed()){
						$protectedContent->protectContent($postId);
					}
				}
			}
		}
	}
	
	function logoutUrl($logout_url, $redirect)
	{
		global $current_user;
		if(class_exists("MM_CorePageEngine"))
		{
			$redirect_url =  MM_CorePageEngine::getUrl(MM_CorePageType::$LOGOUT_PAGE);
			$redirect_url = MM_Utils::appendUrlParam($redirect_url, "user_id",$current_user->ID);
			$redirect = '&amp;redirect_to='.urlencode(wp_make_link_relative($redirect_url));
			$uri = wp_nonce_url( site_url("wp-login.php?action=logout$redirect", 'login'), 'log-out' );
		}
		return $uri;
	}
	
	function loginUrl($login_url, $redirect)
	{
		if(class_exists("MM_CorePageEngine"))
		{
			return MM_CorePageEngine::getUrl(MM_CorePageType::$LOGIN_PAGE);
		}
	}
	
	function loginRedirect($redirectTo, $obj, $user) 
	{	
		if(class_exists("MM_CorePageEngine"))
		{
			if(isset($user->data->ID) && intval($user->data->ID)>0)
			{
				$url = "";
				$user_obj = new MM_User($user->data->ID);
				$corePageEngine = new MM_CorePageEngine();	
				if($user_obj->isAdmin()){
					MM_Preview::clearPreviewMode();
					MM_Preview::getData();
					$url = $corePageEngine->getUrl(MM_CorePageType::$MEMBER_HOME_PAGE);
					
					$accessLog = new MM_AccessLog();
					$accessLog->setEventType(MM_AccessLog::$MM_TYPE_AUTH);
					$accessLog->setReferrer(MM_Utils::getReferrer());
					$accessLog->setIp(MM_Utils::getClientIPAddress());
					$accessLog->setUrl(MM_Utils::constructPageUrl());
					$accessLog->setUserId($user_obj->getId());
					$accessLog->commitData();
					
					return $url;
				}
				
				//// First detect if the user should be cancelled
				if($user_obj->getStatus() == MM_MemberStatus::$CANCELED)
				{
					$url = $corePageEngine->getUrl(MM_CorePageType::$ERROR, MM_ErrorType::$ACCOUNT_CANCELED);
					
					global $current_user, $user;
					$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_USER_ID, $user->ID, true);
					$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_MESSAGE_KEY, MM_ErrorType::$ERROR_MSG_CANCELLED, true);
					wp_clear_auth_cookie();
				}	
				
				//// locked?
				else if($user_obj->getStatus() == MM_MemberStatus::$LOCKED)
				{
					$url = $corePageEngine->getUrl(MM_CorePageType::$ERROR, MM_ErrorType::$ACCOUNT_LOCKED);
					
					global $current_user,$user;
					$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_USER_ID, $user->ID, true);
					$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_MESSAGE_KEY, MM_ErrorType::$ERROR_MSG_LOCKED, true);
					wp_clear_auth_cookie();
				}
				
				//// overdue?
				else if($user_obj->getStatus() == MM_MemberStatus::$OVERDUE)
				{
					$url = $corePageEngine->getUrl(MM_CorePageType::$MY_ACCOUNT, "");
//					$url = $corePageEngine->getUrl(MM_CorePageType::$ERROR, MM_ErrorType::$ACCOUNT_OVERDUE);
//					
//					global $current_user,$user;
//					$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_USER_ID, $user->ID, true);
//					$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_MESSAGE_KEY, MM_ErrorType::$ERROR_MSG_LOCKED, true);
					
				}
				/// user is OK, send to member home.	
				else
				{ 
	 				MM_Session::clear(MM_Session::$KEY_REGISTRATION);
					MM_Preview::clearPreviewMode();
					$url = $corePageEngine->getUrl(MM_CorePageType::$MEMBER_HOME_PAGE);
					
					$accessLog = new MM_AccessLog();
					$accessLog->setEventType(MM_AccessLog::$MM_TYPE_AUTH);
					$accessLog->setReferrer($_SERVER["HTTP_REFERER"]);
					$accessLog->setIp($_SERVER["REMOTE_ADDR"]);
					$accessLog->setUrl($_SERVER["REQUEST_URI"]);
					$accessLog->setUserId($user_obj->getId());
					$accessLog->commitData();
					
					if(MM_AccessLog::hasReachedMaxIPCount($user_obj->getId())){
						global $current_user,$user;
						$user_obj->setStatus(MM_MemberStatus::$LOCKED);
						$user_obj->commitData();
						
						$url = $corePageEngine->getUrl(MM_CorePageType::$ERROR, MM_ErrorType::$ACCOUNT_LOCKED);
						$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_USER_ID, $user->ID, true);
						$url = MM_Utils::appendUrlParam($url, MM_Session::$PARAM_MESSAGE_KEY, MM_ErrorType::$ERROR_MSG_LOCKED, true);
						wp_clear_auth_cookie();
					}
				}
	
				////now determine which home page to take them to.
				if(empty($url))
					$url= get_permalink(get_option("siteurl"));
				
				return $url;
			}
			return $redirectTo;
		}
	}
	
 }
 
?>