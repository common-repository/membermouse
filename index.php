<?php
/**
 * @package MemberMouse
 * @version 1.2.0
 *
Plugin Name: MemberMouse Plugin
Plugin URI: http://membermouse.com
Description: MemberMouse provides powerful tools for building and managing membership sites
Author: MemberMouse
Version: 1.2.0
Author URI: http://www.membermouse.com
Copyright: 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
*/
require_once("lib/class.utils.php");

if(!class_exists('MemberMouseWP')) {
	class MemberMouseWP {
 		private static $menus=""; 
		private $option_name = 'membermousewp-settings';
		private $defaults = array('count'=>10, 'append'=>1);
		private $metaname = '_associated_membermousewp';

		function MemberMouseWP() {
			$this->addActions();
			$this->addFilters();
		}

		function addFilters() 
		{
			// nothing here....yet
		}
		
		function addActions() {
			add_action("admin_enqueue_scripts", array($this, 'loadCommonResources'));
			add_action('admin_menu', array($this, 'addAdministrativeInterfaceItems'));
		}
				
		public function loadCommonResources()
		{	
			$url = get_option("siteurl");
			
			$custom_js = "";
			echo $custom_js;
			
			if(substr($url, strlen($url)-1, strlen($url))=="/") {
				$url = substr($url, 0, strlen($url)-1);
			}
		}
		
		public function loadModule()
		{
			require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR."view.php");
		}
		
		public function addAdministrativeInterfaceItems() {
			add_menu_page('MemberMouse', __('MemberMouse'), 'manage_options', "mmwp_dashboard", array($this, "loadModule"), MMWP_Utils::getImageUrl('mm_logo'));
			add_submenu_page("mmwp_dashboard", 'Welcome', 'Welcome', 'manage_options', "mmwp_dashboard", array($this, "loadModule"));
		}
	}
}

$MemberMouseWP = new MemberMouseWP();
?>
