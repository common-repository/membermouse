<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Api Dialog -->
<div id="mm-country-dialog"></div>

<script>mmJQuery("#mm-country-dialog").dialog({autoOpen: false, buttons: {
	"Save Country": function() { mmjs.save(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>