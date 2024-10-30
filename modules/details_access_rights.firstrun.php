<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>

<!-- Select Product Dialog -->
<div id="mm-select-product-dialog"></div>

<script>mmJQuery("#mm-select-product-dialog").dialog({autoOpen: false, buttons: {
	"Activate Access Tag": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>