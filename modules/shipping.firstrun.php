<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Api Dialog -->
<div id="mm-shipping-dialog"></div>

<script>mmJQuery("#mm-shipping-dialog").dialog({autoOpen: false, buttons: {
	"Save Shipping Method": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>