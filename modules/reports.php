<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */	
$csvLink = "";
$fromDate = "";
$toDate = "";
$groupByAffiliateCheck = "";

$includeMtCheck = "";
$includeAtCheck = "";
if(!isset($_POST["mm_from_date"])){
	$includeMt = true;
	$includeAt = true;
$includeMtCheck = "checked";
$includeAtCheck = "checked";
}  

$selectedAts = array();
$selectedMts = array();
if(isset($_POST["mm_from_date"])){
	$fromDate = $_POST["mm_from_date"];
	$toDate = $_POST["mm_to_date"];
	
	$groupByAffiliate = (isset($_POST["mm_group_by_affiliate"]))?true:false;
	$includeMt = (isset($_POST["mm_include_member_types"]))?true:false;
	$includeAt = (isset($_POST["mm_include_access_tags"]))?true:false;
	
	
	if($groupByAffiliate){
		$groupByAffiliateCheck = "checked";
	}
	
	if($includeAt){
		$includeAtCheck = "checked";
		if(isset($_POST["mm_access_tags_sel"]) && count($_POST["mm_access_tags_sel"])>0){
			$ats = $_POST["mm_access_tags_sel"];
			for($i=0; $i<count($ats); $i++){
				$selectedAts[] = $ats[$i];
			}
		}
		else{
			$atList =  MM_AccessTag::getAccessTagsList(true);
			foreach($atList as $k=>$v){
				$selectedAts[] = $k;
			}
		}
		
	}
	
	if($includeMt){
		$includeMtCheck = "checked";
		if(isset($_POST["mm_member_types_sel"]) && count($_POST["mm_member_types_sel"])>0){
			$mts = $_POST["mm_member_types_sel"];
			for($i=0; $i<count($mts); $i++){
				$selectedMts[] = $mts[$i];
			}
		}
		else{
			$atList =  MM_MemberType::getMemberTypesList(true);
			foreach($atList as $k=>$v){
				$selectedMts[] = $k;
			}
		}
	}
	$csv= array();
	if(count($selectedMts)>0 || count($selectedAts)>0){
		$typeData = array(
			'member_type'=>array(),
			'access_tag'=>array(),
		);
		
		$affiliateTypeData = array();
		$affiliates = MM_RetentionReport::getAffiliates();
	
		if(!$groupByAffiliate){
			$typeData["member_type"]["NA"]= MM_RetentionReport::generateCsvData(Date("Y-m-d", strtotime($fromDate)), Date("Y-m-d", strtotime($toDate)), $groupByAffiliate, $selectedMts, "member_type");
			$typeData["access_tag"]["NA"]= MM_RetentionReport::generateCsvData(Date("Y-m-d", strtotime($fromDate)), Date("Y-m-d", strtotime($toDate)), $groupByAffiliate, $selectedAts, "access_tag");		
		}
		else{
			$typeData["member_type"]["NA"]= MM_RetentionReport::generateCsvData(Date("Y-m-d", strtotime($fromDate)), Date("Y-m-d", strtotime($toDate)), $groupByAffiliate, $selectedMts, "member_type", "");
			$typeData["access_tag"]["NA"]= MM_RetentionReport::generateCsvData(Date("Y-m-d", strtotime($fromDate)), Date("Y-m-d", strtotime($toDate)), $groupByAffiliate, $selectedAts, "access_tag", "");		
			if(count($affiliates)>0){
				foreach($affiliates as $row){
					if(!isset($typeData["member_type"][$row->affiliate_id])){
						$typeData["member_type"][$row->affiliate_id] = array();
					}
					if(!isset($typeData["member_type"][$row->affiliate_id])){
						$typeData["access_tag"][$row->affiliate_id] = array();
					}
					$typeData["member_type"]["AFFID".$row->affiliate_id]= MM_RetentionReport::generateCsvData(Date("Y-m-d", strtotime($fromDate)), Date("Y-m-d", strtotime($toDate)), $groupByAffiliate, $selectedMts, "member_type", $row->affiliate_id);
					$typeData["access_tag"]["AFFID".$row->affiliate_id]= MM_RetentionReport::generateCsvData(Date("Y-m-d", strtotime($fromDate)), Date("Y-m-d", strtotime($toDate)),$groupByAffiliate, $selectedAts, "access_tag", $row->affiliate_id);
				}
			}
		}
		$rows = array();
		foreach($typeData as $type => $rows){
			foreach($rows as  $affiliateId=>$data){
				if(is_array($data)){
					foreach($data as $typeId=>$monthArr){
						if(empty($monthArr)){
							continue;
						}
						$name = "";
						switch($type){
							case "member_type":
								$memberType = new MM_MemberType($typeId);
								$name = $memberType->getName();
								break;
							case "access_tag":
								$accessTag = new MM_AccessTag($typeId);
								$name = $accessTag->getName();
								break;
						}
						if(!isset($data[$typeId]["total"])){
							$data[$typeId]["total"] = 0;
						}
						$row = "";
						if($groupByAffiliate){
							$row.="\" \",";
						}
						$row .= "\"".$name."\",\"".$data[$typeId]["total"]."\",";
						for($i=1; $i<=12; $i++){
							$index = $i;
							if($i>=12){
								$index=12;
							}
							if(isset($monthArr["months_".$index])){
								$row.= "\"".$monthArr["months_".$index]."\",";
							}
							else{
								$row.="\"0\",";
							}
						}
						if(!isset($csv[$affiliateId])){
							$csv[$affiliateId] = "";
						}
						$csv[$affiliateId] .= preg_replace("/(\,)$/", "", $row)."\n";
					}
				}
			}
		}
	}
	
	
	$headers = array("Products","Total");
	if($groupByAffiliate){
		$headers = array("Affiliate ID", "Products","Total");	
	}
	for($i=1; $i<=12; $i++){
		$headers[] = $i." mths";
	}
	$csvstr = "Retention Report :\n";
	$csvstr.= "{$fromDate} - {$toDate}\n\n\n";
	foreach($headers as $header){
		$csvstr.="\"".$header."\",";
	}
	$csvstr = preg_replace("/(\,)$/", "", $csvstr)."\n";
	$lastAffiliate = "";
	foreach($csv as $affiliate=>$lines){
		if($groupByAffiliate){
			$affiliate = preg_replace("/(AFFID)/", "", $affiliate);
			$csvstr.="\"{$affiliate}\",";
			for($i=0; $i<count($headers)-1; $i++){
				$csvstr.="\"\",";
			}
			$csvstr.="\n";
		}
		$csvstr.=$lines;
	}
//	echo nl2br($csvstr);
	MM_Session::value("retention_csv", $csvstr);
	$csvLink = "?export_file=".MM_GET_KEY."&data=retention_csv&filename=retention_report_".Date("Y-m-d").".csv";
}

$selectedAtOptions = array();
foreach($selectedAts as $at){
	$selectedAtOptions[$at] = $at;
}
$accessTagsArr = MM_AccessTag::getAccessTagsList(true);
$accessTags = MM_HtmlUtils::generateSelectionsList($accessTagsArr,$selectedAtOptions);
 
$selectedMtOptions = array();
foreach($selectedMts as $mt){
	$selectedMtOptions[$mt] = $mt;
}
$memberTypeArr = MM_MemberType::getMemberTypesList(true);
$memberTypes = MM_HtmlUtils::generateSelectionsList($memberTypeArr,$selectedMtOptions); 
 
?>
<script type='text/javascript'>
mmJQuery(document).ready(function(){
	mmJQuery("#mm_from_date").datepicker();
	mmJQuery("#mm_to_date").datepicker();
});
</script>
<div class="wrap">
    <h2 class="mm-header-text">Retention Report <sup style='font-size: 11px;'>beta</sup></h2>
			
			<form name='al' method='post' onsubmit="return mmjs.validateForm();">
			<table cellspacing="5">
				<tr>
					<td width='120px'>From</td>
					<td width='500px'>
						<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
						<input id="mm_from_date"  name="mm_from_date" type="text" value="<?php echo $fromDate; ?>" style="width: 152px" /> 
					</td>
				</tr>
				<tr>
					<td>To</td>
					<td>
						<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
						<input id="mm_to_date" name="mm_to_date" type="text" style="width: 152px"  value='<?php echo $toDate; ?>'/>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan='2'>
						<table>
							<tr>
								<td colspan='2'>
									<input type='checkbox' name='mm_group_by_affiliate' value='1' <?php echo $groupByAffiliateCheck; ?> /> Group by affiliate
								</td>
							</tr>
							<tr>
								<td width='200px' valign='top'>
									<input type='checkbox' id='mm_include_member_types' name='mm_include_member_types' value='1' onchange="mmjs.includeMemberType();" <?php echo $includeMtCheck; ?> /> Include Member Types
									<table id='mm_member_types' style='display:none;'>
										<tr valign='top'>
											<td colspan='2'>
												<input type='radio' id='mm_member_types_opt_all' name='mm_member_types_opt' value='all'  onchange="mmjs.showMemberTypes()" checked /> All	
											</td>
										</tr>
										<tr valign='top'>
											<td colspan='2'>
												<input type='radio' id='mm_member_types_opt_sel' name='mm_member_types_opt' value='selected'  onchange="mmjs.showMemberTypes()" /> Selected
											</td>
										</tr>
										<tr valign='top'>
											<td colspan='2'>
												<select id='mm_member_types_sel[]' name='mm_member_types_sel[]' multiple size='5' style='height: 70px; width: 175px;' disabled='disabled'>
													<?php echo $memberTypes; ?>
												</select>
											</td>
										</tr>
									</table>
								</td>
								<td valign='top'>
									<input type='checkbox' id='mm_include_access_tags' name='mm_include_access_tags' value='1' onchange="mmjs.includeAccessTag();" <?php echo $includeAtCheck; ?>  /> Include Access Tags
									<table id='mm_access_tags' style='display:none;'>
										<tr valign='top'>
											<td colspan='2'>
												<input type='radio' id='mm_access_tags_opt_all' name='mm_access_tags_opt' value='all' onchange="mmjs.showAccessTags()" checked /> All	
											</td>
										</tr>
										<tr>
											<td colspan='2'>
												<input type='radio' id='mm_access_tags_opt_sel' name='mm_access_tags_opt' value='selected' onchange="mmjs.showAccessTags()" /> Selected
											</td>
										</tr>
										<tr valign='top'>
											<td colspan='2'>
												<select id='mm_access_tags_sel[]' name='mm_access_tags_sel[]'  multiple size='5' style='height: 70px; width: 175px;' disabled='disabled'>
													<?php echo $accessTags; ?>
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' name='submit' value='Run Report' class="button-secondary"  />
					</td>
				</tr>
				</table></form>
	<div class="clear"></div>
	<div style='clear:both; height: 20px;'></div>
</div>
<script type='text/javascript'>
mmJQuery(document).ready(function(){
<?php if(!empty($includeAtCheck)){ ?>
mmjs.includeAccessTag();
<?php  } ?>
<?php if(!empty($includeMtCheck)){ ?>
mmjs.includeMemberType();
<?php  } ?>
<?php if(count($selectedMtOptions)>0 && $_POST["mm_member_types_opt"] !='all'){ ?>
mmJQuery("#mm_member_types_opt_sel").attr("checked","checked");
mmjs.showMemberTypes();
<?php  } ?>
<?php if(count($selectedAtOptions)>0 && $_POST["mm_access_tags_opt"] !='all'){?>
mmJQuery("#mm_access_tags_opt_sel").attr("checked","checked");
mmjs.showAccessTags();
<?php  } ?>
<?php if(!empty($csvLink)){?>
document.location.href='<?php echo $csvLink; ?>';
<?php  } ?>
});
</script>