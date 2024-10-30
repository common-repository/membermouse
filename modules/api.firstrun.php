<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Api Dialog -->
<div id="mm-api-keys-dialog"></div>

<script>mmJQuery("#mm-api-keys-dialog").dialog({autoOpen: false, buttons: {
	"Save API Key": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>