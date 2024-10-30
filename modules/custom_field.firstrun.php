<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Api Dialog -->
<div id="mm-custom-fields-dialog"></div>

<script>mmJQuery("#mm-custom-fields-dialog").dialog({autoOpen: false, buttons: {
	"Save Custom Field": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>