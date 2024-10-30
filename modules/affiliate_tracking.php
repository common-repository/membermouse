<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$error = "";
if(isset($_POST[MM_OPTION_TERMS_AFFILIATE_LIFESPAN])){
	if(!MM_Utils::isGetParamAllowed($_POST[MM_OPTION_TERMS_AFFILIATE])){
		$error = $_POST[MM_OPTION_TERMS_AFFILIATE]." is WordPress reserved word";
	}
	if(!MM_Utils::isGetParamAllowed($_POST[MM_OPTION_TERMS_SUB_AFFILIATE])){
		$error = $_POST[MM_OPTION_TERMS_SUB_AFFILIATE]." is WordPress reserved word";
	}
	
	if(empty($error)){
		MM_OptionUtils::setOption(MM_OPTION_TERMS_AFFILIATE, $_POST[MM_OPTION_TERMS_AFFILIATE]);
		MM_OptionUtils::setOption(MM_OPTION_TERMS_SUB_AFFILIATE, $_POST[MM_OPTION_TERMS_SUB_AFFILIATE]);
		MM_OptionUtils::setOption(MM_OPTION_TERMS_AFFILIATE_LIFESPAN, $_POST[MM_OPTION_TERMS_AFFILIATE_LIFESPAN]);
	}
}

$affiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE);
$subAffiliateId = MM_OptionUtils::getOption(MM_OPTION_TERMS_SUB_AFFILIATE);
$lifespan = MM_OptionUtils::getOption(MM_OPTION_TERMS_AFFILIATE_LIFESPAN);

if(!preg_match("/[0-9]+/", $lifespan)){
	$lifespan="1";
}
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_members'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Affiliate Tracking</h2>
	<form method='post'>
	<div id="mm-form-container" style="margin-top: 10px; margin-bottom: 15px;">	
		<div>
			<table>
				<tr>
					<td width='125px'>
						Affiliate ID
					</td>
					<td>
						<input type='text' name='<?php echo MM_OPTION_TERMS_AFFILIATE; ?>' value='<?php echo $affiliateId; ?>' />
					</td>
				</tr>
				<tr>
					<td>
						Sub-Affiliate ID
					</td>
					<td>
						<input type='text' name='<?php echo MM_OPTION_TERMS_SUB_AFFILIATE; ?>' value='<?php echo $subAffiliateId; ?>' />
					</td>
				</tr>
				<tr>
					<td>
						Lifespan
					</td>
					<td>
						<input type='text' style='width: 50px;' name='<?php echo MM_OPTION_TERMS_AFFILIATE_LIFESPAN; ?>' value='<?php echo $lifespan; ?>' /> days
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<input type='submit' name='submit' value='Save Settings' class="button-primary" />
</form>
</div>

<script type='text/javascript'>
<?php if(!empty($error)){ ?>
alert('<?php echo $error; ?>');
<?php  } ?>
</script>