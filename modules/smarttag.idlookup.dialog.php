<?php 
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
?>
Lookup 
<select id="mm-object-type-selection" onchange="stl_js.lookupIds();">
<?php echo MM_HtmlUtils::getMMObjectTypes(); ?>
</select>

<div id="mm-lookup-results-container" style="margin-top: 8px; width:100%; height:200px; overflow:auto;"></div>

<script>stl_js.lookupIds();</script>