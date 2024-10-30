<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
class MM_HtmlUtils
{
	public static function getEmailAccounts($selections=null, $activeStatusOnly=false)
	{
		$emails = MM_EmailAccount::getEmailAccountsList($activeStatusOnly);
		
		if(is_null($selections) || $selections == "") {
			$selections = $emails->default;
		}
		
		return self::generateSelectionsList($emails->list, $selections);
	}
	
	public static function getMemberTypeProducts($memberTypeId, $selections=null, $disabled=null)
	{
		$list = MM_Product::getMemberTypeProductsList($memberTypeId);
		return self::generateSelectionsList($list, $selections,$disabled);
	}
	
	public static function getAccessTagProducts($accessTagId, $selections=null, $disabeld=null)
	{
		$list = MM_Product::getAccessTagProductsList($accessTagId);
		return self::generateSelectionsList($list, $selections,$disabeld);
	}
	
	public static function getAccountTypesList($selections=null)
	{
		$list = MM_AccountType::getAccountTypesList();
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getCustomFieldsList($selections=null)
	{
		$list = MM_CustomField::getCustomFieldsList();
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getAccessTagsList($selections=null, $activeStatusOnly=false)
	{
		$list = MM_AccessTag::getAccessTagsList($activeStatusOnly);
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getMemberTypesList($selections=null, $activeStatusOnly=false, $filterBySubType="")
	{
		
		$list = MM_MemberType::getMemberTypesList($activeStatusOnly, $filterBySubType);
		
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getMemberStatusList($selections=null)
	{
		$list = MM_MemberStatus::getStatusTypesList();
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getCampaignCountryList($campaignId, $selections=null)
	{
		$campaign = new MM_Campaign($campaignId, false);
		$list = $campaign->getSettingsList(MM_Campaign::$SETTING_TYPE_COUNTRY);
		
		if(is_null($selections) || $selections == "") {
			$selections = MM_LimeLightUtils::$COUNTRY_ID_US;
		}
		
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getCampaignShippingList($campaignId, $selections=null)
	{
		$campaign = new MM_Campaign($campaignId, false);
		$list = $campaign->getSettingsList(MM_Campaign::$SETTING_TYPE_SHIPPING);
		
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getCampaignPaymentList($campaignId, $selections=null)
	{
		$campaign = new MM_Campaign($campaignId, false);
		$list = $campaign->getSettingsList(MM_Campaign::$SETTING_TYPE_PAYMENT);
		
		return self::generateSelectionsList($list, $selections);
	}
	
	public static function getCCExpMonthList($selection="")
	{
		$list = array();
		
		$list["01"] = "Jan";
		$list["02"] = "Feb";
		$list["03"] = "Mar";
		$list["04"] = "Apr";
		$list["05"] = "May";
		$list["06"] = "Jun";
		$list["07"] = "Jul";
		$list["08"] = "Aug";
		$list["09"] = "Sep";
		$list["10"] = "Oct";
		$list["11"] = "Nov";
		$list["12"] = "Dec";
		
		return self::generateSelectionsList($list, $selection);
	}
	
	public static function getCCExpYearList($selection="")
	{
		$list = array();
		$crntYear = intval(date("Y"));
		
		for($i=0; $i<=6; $i++)
		{
			$yearIndex = substr($crntYear, 2, 2);
			
			$list[$yearIndex] = $crntYear;
			
			$crntYear++;
		}
		
		return self::generateSelectionsList($list,$selection);
	}
	
	public static function getDataGridResultsCount($selection="")
	{
		$list = array();
		
		$list["10"] = "10";
		$list["20"] = "20";
		$list["50"] = "50";
		$list["100"] = "100";
		$list["500"] = "500";
		$list["1000"] = "1000";
		
		return self::generateSelectionsList($list, $selection);
	}
	
	public static function getMMObjectTypes($selection="")
	{
		$list = array();
		
		$list[MM_TYPE_POST] = "Protected Posts";
		$list[MM_TYPE_ACCESS_TAG] = "Access Tags";
		$list[MM_TYPE_MEMBER_TYPE] = "Member Types";
		if(MM_Utils::isLimeLightInstall()){
			$list[MM_TYPE_PRODUCT] = "Lime Light Products";
		}
		else{
			$list[MM_TYPE_PRODUCT] = "Products";
		}
		$list[MM_TYPE_EMAIL_ACCOUNT] = "Email Accounts";
		
		if(MM_CustomField::hasCustomFields()){
			$list[MM_TYPE_CUSTOM_FIELD] = "Custom Fields";
		}
		asort($list);
		return self::generateSelectionsList($list, $selection);
	}
	
 	public static function generateSelectionsList($list, $selections=null, $disabled=null)
 	{
 		$html = "";
 		if(!empty($list))
 		{
 			foreach($list as $value=>$key)
 			{
 				$isSelected = "";
 				
 				if(!is_null($selections))
 				{
	 				if(is_array($selections))
	 				{
	 					if(array_key_exists($value, $selections))
	 					{
	 						$isSelected = "selected";
	 					}
	 				}
	 				else if($selections == $value) 
	 				{
	 					$isSelected ="selected";
	 				}
 				}
 				
 				$isDisabled = "";
 				if(!is_null($disabled))
 				{
	 				if(is_array($disabled))
	 				{
	 					if(array_key_exists($value, $disabled))
	 					{
	 						$isDisabled = "disabled='disabled'";
	 					}
	 				}
	 				else if($disabled == $value) 
	 				{
	 					$isDisabled ="disabled='disabled'";
	 				}
 				}
 				
 				
 				$html .= "<option value='{$value}' {$isSelected} {$isDisabled}>".stripslashes($key)."</option>";
 			}	
 		}
 		
 		return $html;
 	}
 	
	public static function createCheckboxGroup($obj, $name, $sel=null, $img="", $disabled=null, $onchange="", $valueWidth=false, $asRadioButton=false)
 	{
		$onchange = (!empty($onchange))?"onchange=\"{$onchange}\"":"";
 		$image = "";
		if(!empty($img))
		{
			$image = MM_Utils::getImageUrl($img);
			if(!empty($image))
				$image = "<img src='{$image}' />";
		}
		
 		$select = "<table width='98%'>";
 		if(!empty($obj))
 		{
 			foreach($obj as $k=>$v)
 			{
 				$d = "";
 				$s = "";
 				if(!is_null($sel))
 				{
	 				if(is_array($sel))
	 				{
	 					if(array_key_exists($k, $sel))
	 					{
	 						$s = "checked";
	 					}
	 				}
	 				else if($sel==$k)
	 					$s ="checked";
 				}
 				if(!is_null($disabled))
 				{
	 				if(is_array($disabled))
	 				{
	 					if(array_key_exists($k, $disabled))
	 					{
	 						$d = "disabled";
	 					}
	 				}
	 				else if($disabled==$k)
	 					$d ="disabled";
 				}
 				$value = "";
				$image = "";
				$alt = "";
 				if(is_object($v) && isset($v->value))
 				{
 					$value = $v->value;
					if(isset($v->image) && empty($image))
					{
						$image = MM_Utils::getImageUrl($v->image);
						if(preg_match("/(http)/", $v->image)){
							$image = $v->image;
						}
						if(!empty($image) && !empty($v->image)){
							$image = "<img src='{$image}' />";
						}
						else{
							$image="";
						}
					}
					if(isset($v->alt)){
						$alt = $v->alt;
					}
 				}
 				else if(is_object($v))
 				{
 					continue;
 				}
 				else
 					$value = $v;

 				$title = (!empty($alt))?$alt:$value;
 			 	if(intval($valueWidth)>0){
 			 		if(strlen($value)>$valueWidth){
 			 			$value = substr($value, 0, $valueWidth)."...";
 			 		}
 			 	}
 			 	$fieldType = "checkbox";
 			 	if($asRadioButton){
 			 		$fieldType = "radio";
 			 	}
 				$select.="<tr title='{$title}'><td width='5%'><input type='{$fieldType}' value='{$k}' name='{$name}' {$s} {$d} {$onchange}></td><td width='5%'>{$image}</td><td width='90%'>".stripslashes($value)."</td></tr>";
 			}	
 		}
 		return $select."</table>";
 	}
 	
}
?>