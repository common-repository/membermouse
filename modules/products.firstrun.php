<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Api Dialog -->
<div id="mm-products-dialog"></div>

<script>mmJQuery("#mm-products-dialog").dialog({autoOpen: false, buttons: {
	"Save Product": function() { mmjs.saveProduct(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>