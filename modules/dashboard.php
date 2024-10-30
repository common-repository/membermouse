<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$showGuide = MM_OptionUtils::getOption(MM_OPTION_SHOW_GUIDE);
?>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_mm'); ?>" style="float:left; margin:0 10px 0 0; vertical-align:middle;" /> 
    <img src="<?php echo MM_Utils::getImageUrl('mm-logo'); ?>" />
	<div style="clear:both;"></div>
	<div id="mm-getting-started-guide" style="margin-top: 10px;">
	<?php if($showGuide == "" || $showGuide == "1") { ?>
		<a onclick="mmjs.toggleGuide(0);" style="cursor:pointer;" style="font-size: 11px;" class="button-secondary">
			<img src="<?php echo MM_Utils::getImageUrl('information'); ?>" />
			Hide Quick Start Guide
		</a>
		
		<div style="padding-top: 5px;">
		<table>
			<tr>
				<td style="font-size:14px; line-height: 22px; vertical-align:top; padding-top: 15px; padding-right: 15px;">
					Step 1: <a href="javascript:window.document['wistia_304447'].videoSeek(62);">Configure Lime Light CRM</a><br/>
Step 2: <a href="javascript:window.document['wistia_304447'].videoSeek(302);">Setup Member Access Rights</a><br/>
Step 3: <a href="javascript:window.document['wistia_304447'].videoSeek(493);">Create &amp; Protect Content</a><br/>
Step 4: <a href="javascript:window.document['wistia_304447'].videoSeek(740);">Create/Edit Core Pages</a><br/>
Step 5: <a href="javascript:window.document['wistia_304447'].videoSeek(964);">Use SmartTags in Your Content</a><br/>
Step 6: <a href="javascript:window.document['wistia_304447'].videoSeek(1115);">Choose Your Registration Method(s)</a><br/>
Step 7: <a href="http://support.membermouse.com/entries/515598-wordpress-theme-providers" target="_blank">Choose a WordPress Theme</a><br/>
Step 8: <a href="http://support.membermouse.com/entries/463718-css-customization" target="_blank">Customize CSS for MemberMouse Pages</a><br/>
Step 9: Test and Launch!
				</td>
				<td>
				<object width="640" height="426" id="wistia_304447" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"><param name="movie" value="http://embed.wistia.com/flash/embed_player_v1.1.swf"/><param name="allowfullscreen" value="true"/><param name="allowscriptaccess" value="always"/><param name="wmode" value="opaque"/><param name="flashvars" value="videoUrl=http://embed.wistia.com/deliveries/1aeae6c81cc4879deaa01c06b3c0377775b6343f.bin&stillUrl=http://embed.wistia.com/deliveries/585acf68f22f2bd1b49080f4f1f3d7f213f8fa72.bin&unbufferedSeek=true&controlsVisibleOnLoad=false&autoPlay=false&endVideoBehavior=default&playButtonVisible=true&embedServiceURL=http://distillery.wistia.com/x&accountKey=wistia-production_4780&mediaID=wistia-production_304447&mediaDuration=1346.3"/><embed src="http://embed.wistia.com/flash/embed_player_v1.1.swf" width="640" height="426" name="wistia_304447" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" wmode="opaque" flashvars="videoUrl=http://embed.wistia.com/deliveries/1aeae6c81cc4879deaa01c06b3c0377775b6343f.bin&stillUrl=http://embed.wistia.com/deliveries/585acf68f22f2bd1b49080f4f1f3d7f213f8fa72.bin&unbufferedSeek=true&controlsVisibleOnLoad=false&autoPlay=false&endVideoBehavior=default&playButtonVisible=true&embedServiceURL=http://distillery.wistia.com/x&accountKey=wistia-production_4780&mediaID=wistia-production_304447&mediaDuration=1346.3"></embed></object>
				</td>
			</tr>
		</table>
</div>
<div style="font-size:14px; line-height: 22px;">

</div>
	<?php } else { ?>
		<a onclick="mmjs.toggleGuide(1);" style="cursor:pointer;" style="font-size: 11px;" class="button-secondary">
			<img src="<?php echo MM_Utils::getImageUrl('information'); ?>" />
			Show Quick Start Guide
		</a>
		<div style="clear:both; margin-bottom: 10px;"></div>
	<?php } ?>
		<div class="mm-divider"></div>
	</div>
	
	<!-- <h2>MemberMouse Overview</h2>
	Coming soon!
	 -->
</div>