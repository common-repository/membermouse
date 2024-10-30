<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Api Dialog -->
<div id="mm-payment-dialog"></div>

<script>mmJQuery("#mm-payment-dialog").dialog({autoOpen: false, buttons: {
	"Save Payment Method": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>