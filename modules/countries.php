<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$saved= false;
if(isset($_POST["country"])){
	MM_CampaignOptions::removeAll("country");
	
	if(isset($_POST["country"]) && is_array($_POST["country"]) && count($_POST["country"])>0){
		foreach($_POST["country"] as $attribute){
			$name = MM_LimeLightUtils::getCountryName($attribute);
			$co = new MM_CampaignOptions();
			$co->setName($name);
			$co->setAttr($attribute);
			$co->setSettingType("country");
			$co->commitData();
		}
		$saved= true;
	}
}

$title = "Country";
$moduleName = "country";
$uModuleName = ucfirst($moduleName);
MM_Session::value(MM_Session::$KEY_CAMPAIGN_SETTINGS_ID, MM_MODULE_COUNTRIES);

$view = new MM_CampaignSettingsView();
$dataGrid = new MM_DataGrid($_REQUEST, "id", "asc", 10);
$data = $view->getData($dataGrid, $moduleName);

$options = MM_LimeLightUtils::getCountryOptions();
$htmlOptions = MM_HtmlUtils::generateSelectionsList($options, $data);


?>
<div class="wrap">
<form name='savecountry' method='post'>
    <h2 class="mm-header-text"><?php echo $title; ?></h2>
	
	<div class="clear"></div>
	<select name='country[]' multiple size='100' style='height: 300px; width: 500px; font-size: 12px;'>
	<?php echo $htmlOptions; ?>
	</select>
	
	<br /><br />
	<input type='submit' name='save' value='Save Countries' class="button-secondary" />
	</form>
</div>
<?php if($saved){ ?>
<script type='text/javascript'>
	alert("Countries have been saved");
</script>
<?php } ?>