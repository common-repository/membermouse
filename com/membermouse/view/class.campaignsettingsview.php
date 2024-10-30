<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_CampaignSettingsView extends MM_View
{
	public function performAction($post) 
	{	
		$response = parent::performAction($post);
		
		if(!($response instanceof MM_Response))
		{
			switch($post[self::$MM_JSACTION]) 
			{
				case self::$MM_JSACTION_SAVE:
					return $this->saveSetting($post);
					
				case self::$MM_JSACTION_REMOVE:
					return $this->removeSetting($post);
				
				case self::$MM_JSACTION_GATEWAY_OPTIONS:
					return $this->getGatewayOptions($post);
					break;
				default:
					return new MM_Response($response);
			}
		}
		else 
		{
			return $response;
		}
	}
	
	private function getGatewayOptions($post){
		if(!isset($post["option"]) || !isset($post["id"])){
			return new MM_Response("option is required ", MM_Response::$ERROR);
		}
		$optionSetting = new MM_CampaignOptions($post["option"]);
		$arr = json_decode($optionSetting->getAttr());
		if(is_null($arr) || $arr===false){
			return new MM_Response("option does not have defined attributes ", MM_Response::$ERROR);
		}
		
		$cs = new MM_CampaignOptions($post["id"]);
		$attr =json_decode($cs->getAttr());
		
		$html ="";
		$showTypes = 0;
		foreach($arr as $k=>$v){
			if(preg_match("/(hidden_)/", $k)){
				if(preg_match("/(onsite)/", $k)){
					if($v=='1'){
						$showTypes = 1;
					}
				}
				continue;
			}	
			$value = "";
			if(is_object($attr)){
				if(isset($attr->$k)){
					$value = $attr->$k;
				}
			}	
			$label = "";
			$labelExp = explode("_", $k);
			foreach($labelExp as $val){
				$label.=ucfirst($val)." ";
			}
			$html.= "<tr>
						<td>".$label."</td>
						<td><input type='text' id='{$k}' value='{$value}' /></td>
					</tr>";
		}
		return new MM_Response(array('show_types'=>$showTypes,'html'=>$html));
	}
	
	private function removeSetting($post){
		if(!isset($post["id"])){
			return new MM_Response("ID is required ", MM_Response::$ERROR);
		}
		$cs = new MM_CampaignOptions($post["id"]);
		$cs->remove();
		return new MM_Response();
	}
	
	private function saveSetting($post){
		$req = array('mm_name','mm_id', 'mm_setting_type');
		foreach($req as $field){
			if(!isset($post[$field])){
				return new MM_Response($field." is required", MM_Response::$ERROR);
			}	
		}
		
		if($post["mm_setting_type"]=='payment'){
			return $this->savePayment($post);
		}
		
		$name = $post["mm_name"];
		$attr = "";
		if($post["mm_setting_type"]=='country'){
			$attr = $post["mm_name"];
			$name = MM_LimeLightUtils::getCountryName($attr);
		}
		else if($post["mm_setting_type"]=='shipping'){
			$attr = $post["mm_rate"];
			if(!preg_match("/^[0-9\.]+$/", $attr)){
				return new MM_Response("You must provide a rate", MM_Response::$ERROR);
			}
			$attr = floatval($attr);
		}
		
		$cs = new MM_CampaignOptions($post["mm_id"]);
		$cs->setSettingType($post["mm_setting_type"]);
		$cs->setName($name);
		$cs->setAttr($attr);
		$ret = $cs->commitData();
		if(!$ret){
			return new MM_Response("Unable to save setting", MM_Response::$ERROR);
		}
		return new MM_Response();	
	}
	
	public function savePayment($post){
		$req = array('mm_name','mm_id', 'mm_setting_type','mm_gateways');
		foreach($req as $field){
			if(!isset($post[$field])){
				return new MM_Response($field." is required", MM_Response::$ERROR);
			}	
		}
		
		if($post["mm_name"] == ''){
			return new MM_Response("Name is required", MM_Response::$ERROR);
		}
		
		if($post["mm_gateways"]==""){
			return new MM_Response("Gateway is required", MM_Response::$ERROR);
		}
		
		$optionSetting = new MM_CampaignOptions($post["mm_gateways"]);
		$arr = json_decode($optionSetting->getAttr());
		if(is_object($arr)){
			foreach($arr as $k=>$v){
				if(preg_match("/(hidden_)/", $k)){
					continue;
				}	
				if(!isset($post[$k]) || $post[$k] == ""){
					$label = "";
					$labelExp = explode("_", $k);
					foreach($labelExp as $val){
						$label.=ucfirst($val)." ";
					}
					return new MM_Response($label." is required", MM_Response::$ERROR);
				}
				$arr->$k = $post[$k];
			}
			$arr->gateway_id = $post['mm_gateways'];
		}
		
		$cs = new MM_CampaignOptions($post["mm_id"]);
		$cs->setSettingType($post["mm_setting_type"]);
		$cs->setName($post["mm_name"]);
		$cs->setAttr(json_encode($arr));
		$cs->setShowOnReg($post["mm_show_on_reg"]);
		$ret = $cs->commitData();
		if(!$ret){
			return new MM_Response("Unable to save setting", MM_Response::$ERROR);
		}
		return new MM_Response();	
	}
	
	public function getData(MM_DataGrid $dg, $settingType='shipping')
	{
		global $wpdb;
		
		$rows = parent::getData(MM_TABLE_CAMPAIGN_OPTIONS, null, $dg, "where setting_type='".$settingType."'" );
		if($settingType=="country"){
			$selectRows = array();
			foreach($rows as $row){
				$selectRows[$row->attr] = $row->attr;
			}
			return $selectRows;
		}
		return $rows;
	}
	
}
?>
