<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
<!-- Site Management Dialog -->
<div id="mm-site-dialog"></div>

<script>mmJQuery("#mm-site-dialog").dialog({autoOpen: false, buttons: {
	"Save Site": function() { mmjs.saveSite(); },
	"Cancel": function() { mmjs.closeDialog(); }}});
</script>