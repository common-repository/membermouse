<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_SiteMgmtView extends MM_View
{
	public static $DIALOG_ADMIN_WIDTH = 500;
	public static $DIALOG_ADMIN_HEIGHT = 475;
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_VERIFY_LL:
					return $this->verifyLL($post);
					
				case self::$MM_JSACTION_SAVE_ADMIN:
					return $this->saveSiteFromAdmin($post);
					
				case self::$MM_JSACTION_SAVE:
					return $this->saveSite($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->archiveSite($post);
				
				case self::$MM_JSACTION_SITE_REMOVE_CAMPAIGN:
					return $this->removeCampaignFromSite($post);
					
				case self::$MM_JSACTION_SITE_REFRESH_CAMPAIGN:
					return $this->refreshCampaignList($post);
					
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}

	public function getData(MM_DataGrid $dg)
	{
		global $wpdb;
		
		$start = intval($dg->crntPage)*intval($dg->resultSize);
		$total = $dg->resultSize;
		
		$response = MM_MemberMouseService::getAllSites($dg->sortBy,$dg->sortDir,$start,$total);
		return $response->response_data;
	}
	
	private function saveSiteFromAdmin($post) 
	{
		if(isset($post["mm_member_id"]) && intval($post["mm_member_id"]) > 0) 
		{
			$site = new MM_Site("", false);
			
			if(isset($post["mm_site_id"]) && intval($post["mm_site_id"]) > 0) {
				$site->setId($post["mm_site_id"]);
			}
			
			$site->setName($post["mm_site_name"]);
			$site->setLocation($post["mm_site_url"]);
			$site->setLLUrl($post["mm_ll_url"]);	
			$site->setLLUsername($post["mm_ll_api_key"]);
			$site->setStatus($post["mm_member_status"]);
			$site->setLLPassword($post["mm_ll_api_password"]);
			$site->setCampaignIds($post["mm_campaign_ids"]);
			if(!isset($post["mm_is_dev"]) || (isset($post["mm_is_dev"]) && $post["mm_is_dev"]=="")){
				$post["mm_is_dev"] = "0";
			}
			$site->setIsDev($post["mm_is_dev"]);
			if(!isset($post["mm_is_mm"]) || (isset($post["mm_is_mm"]) && $post["mm_is_mm"]=="")){
				$post["mm_is_mm"] = "0";
			}
			$site->setIsMM($post["mm_is_mm"]);
			
			$result = MM_MemberMouseService::commitSiteData($post["mm_member_id"], $site, true);
			
			if(!MM_MemberMouseService::isSuccessfulRequest($result)) {
				return new MM_Response($result->response_message, MM_Response::$ERROR);
			}
			
			$this->updateProfile($site->getId());
			return new MM_Response("Site saved successfully");
		}
		else {
			return new MM_Response("Unable to save site. Member ID is required.", MM_Response::$ERROR);
		}
	}
	
	private function archiveSite($post)
	{
		if(isset($post["id"]) && intval($post["id"]) > 0)
		{
			$result = MM_MemberMouseService::archiveSite($post["id"]);
			
			if(MM_MemberMouseService::isSuccessfulRequest($result)) {
				return new MM_Response();
			}
			else
			{
				return new MM_Response($result->response_message, MM_Response::$ERROR);
			}
		}
		
		return new MM_Response("Unable to delete site. No id specified.", MM_Response::$ERROR);
	}
	
	private function removeCampaignFromSite($post){
		
		if(isset($post["mm_campaign_ids"]) && intval($post["mm_campaign_ids"]) > 0) 
		{	
			if(isset($post["mm_site_id"]) && intval($post["mm_site_id"]) > 0) 
			{
				$site = new MM_Site($post["mm_site_id"]);
				if($site->isValid()){
					$campaignIds = explode(",", $post["mm_campaign_ids"]);
					if(count($campaignIds)>0){
						$campaigns = explode(",", $site->getCampaignIds());
						for($i=0; $i<count($campaignIds); $i++){
							$key = array_search($campaignIds[$i], $campaigns);
							if($key === false){
								continue;
							}
							unset($campaigns[$key]);
							$site->setCampaignIds(implode(",", $campaigns));
						}
						$result = MM_MemberMouseService::commitSiteData($post["mm_member_id"], $site);
							
						if(!MM_MemberMouseService::isSuccessfulRequest($result)) {
							return new MM_Response($result->response_message, MM_Response::$ERROR);
						}
						$this->updateProfile($site->getId(), true);
					}
					return new MM_Response("Site saved successfully");
				}
			}
			return new MM_Response("Unable to remove campaign. No site id or not valid.", MM_Response::$ERROR);
		}
		return new MM_Response("Unable to remove campaign. No id specified.", MM_Response::$ERROR);
	}
	
	private function saveSite($post) 
	{
		if(isset($post["mm_member_id"]) && intval($post["mm_member_id"]) > 0) 
		{
			$site = new MM_Site("", false);
			
			if(isset($post["mm_site_id"]) && intval($post["mm_site_id"]) > 0) {
				$site->setId($post["mm_site_id"]);
			}
			$siteUrl = preg_replace("/(\/)$/", "", $post["mm_site_url"]);
			$mmLLUrl = preg_replace("/(\/)$/","",  $post["mm_ll_url"]);
			$site->setLocation($siteUrl);
			$site->setLLUrl($mmLLUrl);	
			$site->setName($post["mm_site_name"]);
			$site->setLLUsername($post["mm_ll_api_key"]);
			$site->setLLPassword($post["mm_ll_api_password"]);
			$site->setCampaignIds($post["mm_campaign_ids"]);
			
			$result = MM_MemberMouseService::commitSiteData($post["mm_member_id"], $site);
			
			if(!MM_MemberMouseService::isSuccessfulRequest($result)) {
				return new MM_Response($result->response_message, MM_Response::$ERROR);
			}
			$this->updateProfile($site->getId());
			
			return new MM_Response("Site saved successfully");
		}
		else {
			return new MM_Response("Unable to save site. Member ID is required.", MM_Response::$ERROR);
		}
	}
	
	private function updateProfile($id, $isRemoving=false){
		global $mmSite;
		if(intval($id)>0){
			if($mmSite->isMM()){
				$json = MM_MemberMouseService::getSite($id);
				if(isset($json->status)){
					if($json->status=='1'){
						MM_LimeLightService::setupProfile($isRemoving);
					}
				}
			}
		}
	}
	
	public function refreshCampaignList($post){
		$siteId =$post["mm_site_id"];
		$site = new MM_Site($siteId);
		$campaignArr = array();
		if($site->isValid()) {
			$campaignArr = explode(",", $site->getCampaignIds());
		}
		$campaignHtml = self::getCampaignGroupHtml($campaignArr, true, explode(",", $site->getCampaignsInUse()));
		return new MM_Response($campaignHtml);
	}
	
	public static function getCampaignGroupHtml($selArr=null, $isEdit=false, $noDeleteList=null){
		$selArr = (!is_array($selArr))?array():$selArr;
		$campaigns = MM_LimeLightService::getCampaigns();
		
		$data = array();
		$data[0] = new stdClass();
		$data[0]->total = count($campaigns);
		
		/// initialize data grid
		$dataGrid = new MM_DataGrid(array(), "campaign", "asc", count($campaigns));
		$dataGrid->setTotalRecords($data);
		$dataGrid->showPagingControls = false;
		$dataGrid->recordName = "members";
		
		$rows = array();
		$headers = array
		(	    
			'check'				=> array('content' => ' '),
			'campaign'			=> array('content' => 'Campaign'),
		   	'actions'				=> array('content' => 'Actions'),
		);
		
		if(is_array($campaigns) && count($campaigns)>0){
			foreach($campaigns as $id=>$name){
				$chk = "";
				if(array_search($id, $selArr)!==false){
					$chk = "checked";
				}
				$del = "";
				if($isEdit){
					if(is_null($noDeleteList) || (is_array($noDeleteList) && array_search($id, $noDeleteList) === false)){
						$del = "<a style='cursor: pointer;' onclick=\"sitemgmt_js.removeCampaigns('{$id}');\"><img id='image_{$id}' src='".MM_Utils::getImageUrl('delete')."' /></a>";
					}
				}
				$disabled = "";
				if($isEdit){
					if(!empty($chk)){
						$disabled = "disabled='disabled'";
					}
					else{
						$del = "";
					}
				}
				$checkbox= "<input type='checkbox' name='mm-ll-campaign-id[]' value='{$id}' {$chk} {$disabled} />";
			    $rows[] = array
			    (
			    	array('content' => $checkbox),
			    	array('content' => $name),
			    	array('content' => $del),
			    );
			}
		}
		$dataGrid->setHeaders($headers);
		$dataGrid->setRows($rows);
		
		$dgHtml = $dataGrid->generateHtml();
		
		if($dgHtml == "") {
			$dgHtml = "<p><i>No Campaigns to import.</i></p>";
		}
		return $dgHtml;
	}
	
	private function verifyLL($post) 
	{
		if(isset($post["mm_ll_url"]) && isset($post["mm_ll_api_key"]) && isset($post["mm_ll_api_password"]))
		{
			$result = MM_LimeLightService::validate($post["mm_ll_url"], $post["mm_ll_api_key"], $post["mm_ll_api_password"]);
			
			if($result == true) {
				$html = "<table cellspacing=\"10\" style=\"font-size:12px\">";
				$html .= "<tr><td width=\"100\">Campaign</td>";
				$html .= "<td>";
				$html.= self::getCampaignGroupHtml();
				
				/*$campaigns = MM_LimeLightService::getCampaigns();
				if(count($campaigns)>0){
					foreach($campaigns as $id=>$name){
						$html.= "<tr>
									<td>
										<input type='checkbox' name='mm-ll-campaign-id[]' value='{$id}' />
									</td>
									<td>
										{$name}
									</td>
									<td></td>
								</tr>
						";
					}
				}*/
				
				
				$html .= "</td>";
				$html .= "</tr></table>";
				
				return new MM_Response($html);
			}
			else {
				return new MM_Response("We were unable to connect with Lime Light.\nPlease ensure your information is correct.", MM_Response::$ERROR);
			}
		}
		else {
			return new MM_Response("Unable to verify Lime Light. Some required parameters are missing.", MM_Response::$ERROR);
		}
	}
}
?>
