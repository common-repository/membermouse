<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	$tag = new MM_AccessTag($p->id);
	
	if(!$tag->isFree() && $tag->getAssociatedProducts() > 0 && !$tag->hasSubscribers()) {
		$productsDisabled = "";
	}
	else {
		$productsDisabled = "disabled='disabled'";
	}
	
	if($tag->hasSubscribers()) {
		$subTypeDisabled = "disabled='disabled'";
	} 
	else {
		$subTypeDisabled = "";	
	}
?>
<div id="mm-form-container">
	<table cellspacing="10">
		<tr>
			<td>Display Name</td>
			<td><input id="mm-display-name" type="text" class="long-text" value="<?php echo htmlentities($tag->getName(), ENT_QUOTES); ?>"/></td>
		</tr>
		
		<tr>
			<td>Access Tag Status</td>
			<td>
				<div id="mm-status-container">
					<input type="radio" name="status" value="active" onclick="mmjs.processForm()" <?php echo (($tag->getStatus()=="1")?"checked":""); ?>  /> Active
					<input type="radio" name="status" value="inactive" onclick="mmjs.processForm()" <?php echo (($tag->getStatus()=="0")?"checked":""); ?> /> Inactive
				</div>
				
				<input id="mm-status" type="hidden" />
			</td>
		</tr>

		<tr>
			<td>Access Tag Badge</td>
			<td>
				<?php 
					$badgeUrl = $tag->getBadgeUrl();
					$showUploadForm = !isset($badgeUrl) || (isset($badgeUrl) && $badgeUrl=="");
					
					if($showUploadForm) {
						$badgeUrl = "";
					}
				?>
				<div id="mm-badge-container" <?php if($showUploadForm) { echo "style='display:none;'"; } ?>>
					<img id="mm-badge" src="<?php echo $badgeUrl; ?>" class="badge l" />
					&nbsp;&nbsp;<a onclick="mmjs.clearBadge()" class="button-secondary">Clear</a>
				</div>
				
				<div id="mm-file-upload-container" <?php if(!$showUploadForm) { echo "style='display:none;'"; } ?>>
					<form action="admin-ajax.php" name='badge-upload' method="post" enctype="multipart/form-data" target="upload_target" onsubmit="mmjs.startUpload();" >
	                  	<input id="fileToUpload" name="fileToUpload" type="file" size="30" />
	                  	<input type="submit" name="submitBtn" class="button-secondary" value="Upload" />
	
	                    <input type='hidden' name='method' value='uploadBadge' />
	                    <input type='hidden' name='module' value='MM_AccessTagsView' />
	                    <input type='hidden' name='action' value='module-handle' />
	                    <iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>
	                </form>
                </div>
			</td>	
		</tr>
		
		<tr>
			<td>Subscription Type</td>
			<td>
				<div id="mm-subscription-container">
					<input type="radio" name="subscription-type" value="free" onclick="mmjs.processForm()" <?php echo ($tag->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> Free
					<input type="radio" name="subscription-type" value="paid" onclick="mmjs.processForm()" <?php echo (!$tag->isFree() ? "checked":""); ?> <?php echo $subTypeDisabled; ?> /> Paid
				</div>
				
				<input id="mm-has-associations" type="hidden" value="<?php echo $tag->hasSubscribers() ? "yes" : "no"; ?>" />
				<input id="mm-subscription-type" type="hidden" />
				
				<div style="margin-top:5px">
					<select id="mm-products[]" name="mm-products-list" style='width: 95%;' multiple="multiple" size='6'  onchange="mmjs.setRequiredMemberTypes()">
				<?php
							if(!$tag->isFree()){
								echo MM_HtmlUtils::getAccessTagProducts($tag->getId(), $tag->getAssociatedProducts(), $tag->getAssociatedProducts());
							}
							else{
								echo MM_HtmlUtils::getAccessTagProducts($tag->getId(),$tag->getAssociatedProducts());
							}
						 ?>		
					</select>
				</div>
			</td>
		</tr>	
		
		<tr>
			<td>Description</td>
			<td>
				<textarea id="mm-description" name='description' class="long-text" style="font-size:11px;"><?php echo $tag->getDescription(); ?></textarea>
			</td>
		</tr>
	</table>
	
	<input id='id' type='hidden' value='<?php if($tag->getId() != 0) { echo $tag->getId(); } ?>' />
</div>