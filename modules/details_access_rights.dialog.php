<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
	$tag = new MM_AccessTag($p->accessTagId);
	$products = $tag->getAssociatedProducts();
	

if(MM_Utils::isLimeLightInstall()){	
?>
The <i><?php echo $tag->getName(); ?></i> access tag has multiple products associated with it. Please select the product you'd like to bill the member for:
<?php }
else{
	?>
	The <i><?php echo $tag->getName(); ?></i> access tag has multiple products associated with it. Please select the product you'd like to associate to the member:
	<?php 
} ?>
<div id="mm-select-product-form-container">
	<div id="mm-products-container" style="margin-top: 8px; margin-bottom: 8px; line-height: 22px;">
		<?php 
			$ctr = 0;	
			foreach($products as $value=>$key) { 
		?>
			<input type="radio" name="product" value="<?php echo $value; ?>" onclick="mmjs.processForm()" <?php if($ctr == 0) { echo "checked"; } ?> /> <?php echo $key; ?> <br/>
		<?php 
				$ctr++;
			} 
		?>
	</div>
	
	<input id="mm-product-id" type="hidden" />
	<input id='mm-id' type='hidden' value='<?php echo $p->userId; ?>' />
	<input id='mm-access-tag-id' type='hidden' value='<?php echo $tag->getId(); ?>' />
</div>