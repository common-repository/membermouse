<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_ContextualHelpView extends MM_View
{	
	public static $PAGE_ADD_NEW = "addNewPost";
	public static $PAGE_BROWSE = "browsePosts";
	
	public static $SECTION_GLOBAL = "global";
	public static $SECTION_CORE_PAGES = "core_pages";
	public static $SECTION_BROWSE_POSTS = "browse_posts";
	public static $SECTION_BROWSE_PAGES = "browse_pages";
	public static $SECTION_ACCESS_RIGHTS = "access_rights";
	public static $SECTION_SMARTTAG_LIBRARY = "smarttag_library";
	
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{	
				case self::$MM_JSACTION_DISPLAY_VIDEO:
					return new MM_Response($this->displayVideo($post));
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function displayVideo($post)
	{
		$info = new stdClass();
		
		foreach($post as $key=>$value)
		{
			$info->$key = $value;
		}
		
		if(isset($info->embed_code)) {
			return MM_TEMPLATE::generate(MM_MODULES."/video.dialog.php", $info);
		}
		else {
			return new MM_Response("MM_ContextualHelpView.displayVideo(): video URL missing", MM_Response::$ERROR);
		}
	}
	
	public function renderContextualHelp($text) {
		if(isset($_REQUEST["page"])) 
		{
			$page = $_REQUEST["page"];
		
			if(isset($page) && ($page == MM_MODULE_CONFIGURE_SITE || 
				$page == MM_MODULE_MANAGE_MEMBERS || 
				$page == MM_MODULE_SETTINGS || 
				$page == MM_MODULE_DASHBOARD || 
				$page == MM_MODULE_INTEGRATION_TOOLS || $page==MM_MODULE_ECOMMERCE)) 
			{
				$module = MM_ModuleUtils::getModule();
				 
				$text = $this->addHeader("MemberMouse Help");
				
				switch($module) {
					case MM_MODULE_DETAILS_GENERAL:
					case MM_MODULE_DETAILS_CUSTOM_FIELDS:
					case MM_MODULE_DETAILS_ACCESS_RIGHTS:
						$text .= $this->getResources(MM_MODULE_DETAILS_GENERAL);
						$text .= $this->getResources(MM_MODULE_DETAILS_CUSTOM_FIELDS);
						$text .= $this->getResources(MM_MODULE_DETAILS_ACCESS_RIGHTS);
						break;
						
					case MM_MODULE_SETTINGS_TERMS:
					case MM_MODULE_SETTINGS_CUSTOM_FIELDS:
						$text .= $this->getResources(MM_MODULE_SETTINGS_TERMS);
						$text .= $this->getResources(MM_MODULE_SETTINGS_CUSTOM_FIELDS);
						break;
						
					case MM_MODULE_ECOMMERCE:
						$text.=$this->getResource(MM_MODULE_LIMELIGHT);
						break;
					default:
						$text .= $this->getResources($module);
						break;
				}
			}
		}
		else 
		{
			$text = $this->addHeader("MemberMouse Help");
			
			if(strstr($_SERVER["REQUEST_URI"], "post-new.php")) {
				$pageType = self::$PAGE_ADD_NEW;
			}
			else if(strstr($_SERVER["REQUEST_URI"], "edit.php")) {
				$pageType = self::$PAGE_BROWSE;
			}
			
			if(isset($_REQUEST["post_type"]) && $_REQUEST["post_type"] == "page") {
				$isPages = true;
			}
			else {
				$isPages = false;
			}
			
			if(isset($pageType) && $pageType == self::$PAGE_BROWSE) 
			{
				if($isPages) {
					$text .= $this->getResources(self::$SECTION_BROWSE_PAGES);
				}
				else {
					$text .= $this->getResources(self::$SECTION_BROWSE_POSTS);
				}
			}
			else if(isset($pageType) && $pageType == self::$PAGE_ADD_NEW) 
			{
				$text .= $this->getResources(self::$SECTION_ACCESS_RIGHTS);
				$text .= $this->getResources(self::$SECTION_SMARTTAG_LIBRARY);
				
				if($isPages) {
					$text .= $this->getResources(self::$SECTION_CORE_PAGES);
				}		
			}
		}
		
		$text .= $this->getGlobalHelp();
		
		return $text;
	}
	
	private function getResources($sectionId)
	{
		$html = "";
		$response = MM_MemberMouseService::getContextualHelp($sectionId);
		$data = $response->response_data;
		
		$data = is_null($data) ? array() : $data;

		foreach($data as $item)
		{
			$resource = new MM_ContextualHelpResource($item->id);
			$resource->setData($item);
			
			$html .= $resource->generateHtml();
		}
		
		return $html;
	}
	
	private function getGlobalHelp()
	{
		$help = "<div class='mm-divider' style='margin-bottom: 10px;'></div>";
		$help .= $this->getResources(self::$SECTION_GLOBAL);
		
		return $help;
	}
	
	private function addHeader($title)
	{
		$str = "<img class='mm-header-icon' src='".MM_Utils::getImageUrl('support')."' /> ";
		$str .= "<span class='mm-section-header' style='font-size: 20px; font-weight: normal; line-height: 50px; margin-left: 5px;'>".$title."</span>";
		$str .= "<div class='mm-divider' style='margin-bottom: 10px;'></div>";
		
		return $str;
	}
	
	public function addContentContainer()
	{
		echo "<div id=\"mm-help-dialog\" title=\"MemberMouse Resources\"></div>";
		
		echo "<script>";
		echo "mmJQuery(function() {";
		echo "mmJQuery(\"#mm-help-dialog\").dialog({autoOpen: false});";
		echo "});";
		echo "</script>";
	}
	
}
?>
