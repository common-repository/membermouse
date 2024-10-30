<?php 
//	$canChangeDaysCalc  = true;
//	$fixedSelected = "";
//	$calcMethod = "";
//	$joinDateSelected = "";
//	$customDateSelected = "";
//	$customDateValue = "";
//	$fixedValue = "";
	$userId = MM_Session::value(MM_Session::$KEY_LAST_USER_ID);
	$user = new MM_User($userId);
	if($user->isValid()){
						$canChangeDaysCalc = true;
						
						$accessTags = $user->getAccessTags();
						foreach($accessTags as $tag){
							if($tag->access_tag_id == $p->tag_id){
								if($tag->status != "1"){
									$canChangeCalc = false;
								}
							}
						}
						
						$appliedTag = new MM_AppliedAccessTag();
						$appliedTag->setRefId($user->getId());
						$appliedTag->setAccessTagId($p->tag_id);
						$appliedTag->getDataByTagAndUser();
						
						
						
						
						$customDateSelected = "";
						$fixedSelected = "";
						$joinDateSelected = "";	
						$customDateValue = "";
						$fixedValue = "";
						$calcMethod = MM_DaysCalculationTypes::$JOIN;
						switch($appliedTag->getDaysCalcMethod()){
							case MM_DaysCalculationTypes::$CUSTOM:
								$calcMethod = MM_DaysCalculationTypes::$CUSTOM;
								$customDateValue  = $appliedTag->getDaysCalcValue();
								$customDateSelected = "checked";
								break;
							case MM_DaysCalculationTypes::$FIXED:
								$calcMethod = MM_DaysCalculationTypes::$FIXED;
								$fixedValue = $appliedTag->getDaysCalcValue();
								$fixedSelected = "checked";
								break;
							default:
								$joinDateSelected = "checked";
								break;
						}
					?>
						<script type='text/javascript'>
							mmJQuery(document).ready(function(){
								mmJQuery("#mm-custom-date").datepicker();
							});
						</script>
						<div id='mm-calc-method-div'>
					<div style="margin-top:8px">
					<h2>'Days as Member' Calculation Method</h2>
					<input type='hidden' id='applied_accesstag_id' value='<?php echo $p->tag_id; ?>' />
					<input type='hidden' id='user_id' value='<?php echo $user->getId(); ?>' />
							<table cellspacing="8"  >
									 <?php if(!$canChangeDaysCalc){ ?>
									<tr>
										<td colspan='2'>
											<div style='width: 600px;'><span style='color:red;'><img src='<?php echo MM_Utils::getImageUrl("exclamation"); ?>' style='vertical-align: middle; '/> You can modify the number of days this member is fixed at, but to change the calculation method you must change the member's status to Active.</span></div>
										</td>
									</tr>
									 <?php } ?>
							<tr>
								<td colspan='2'>
									<input type='radio' <?php echo ((!$canChangeDaysCalc)?"disabled='disabled'":""); ?> onchange="mmjs.setCalcMethod('join_date');" id='mm-calc-method-reg-date' <?php echo $joinDateSelected; ?> name='mm-calc-method' /> By join date<br />
									<input type='radio' <?php echo ((!$canChangeDaysCalc)?"disabled='disabled'":""); ?> onchange="mmjs.setCalcMethod('custom_date');" id='mm-calc-method-custom-date'  <?php echo $customDateSelected; ?> name='mm-calc-method' /> By custom date 
									
											<img src="<?php echo MM_Utils::getImageUrl("calendar") ?>" style="vertical-align: middle" />
											<input <?php echo ((!$canChangeDaysCalc)?"disabled='disabled'":""); ?> id="mm-custom-date" type="text" style="width: 152px" value="<?php echo $customDateValue; ?>" /> 
									<br />
									<input type='radio' onchange="mmjs.setCalcMethod('fixed');" id='mm-calc-method-fixed'  <?php echo $fixedSelected; ?>  name='mm-calc-method' /> Fixed at <input id="mm-fixed" type="text" value="<?php echo $fixedValue; ?>"  style="width: 52px" /> days <br />
									 <input type='hidden' id='mm-calc-method' value="<?php echo $calcMethod; ?>" />
									 
								</td>
							</tr>
						</table>
						</div>
						</div>
					<?php } ?>