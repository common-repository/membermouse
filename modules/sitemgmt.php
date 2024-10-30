<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

global $current_user;

$acctType = null;
$sites = array();
$paidMembers = 0;
$totalMembers = 0;
$errorMsg = "";

$user = new MM_User($current_user->ID);

if($user->isValid())
{	
	// get account type
	$memberType = new MM_MemberType($user->getMemberTypeId());
	
	if($memberType->isValid())
	{
		$acctType = new MM_AccountType($memberType->getAccountTypeId());
	}
	
	// get sites
	$result = MM_MemberMouseService::getSites($user->getId());
	
	if(!MM_MemberMouseService::isSuccessfulRequest($result)) {
		$errorMsg = $result->response_message;
	}
	else 
	{
		$rows = $result->response_data;
		
		if(isset($rows) && !is_null($rows)) 
		{
			foreach($rows as $row) 
			{
				$site = new MM_Site("", false);
				$site->setData($row);
				
				if($site->isValid()) 
				{
					$paidMembers += intval($site->getPaidMembers());
					$totalMembers += intval($site->getTotalMembers());
					array_push($sites, $site);
				}	
			}
		}
	}
}
else
{
	echo "User is not valid.";
	exit;
}
function renderSiteModule($memberId, MM_Site $site) {
	$siteActive = (intval($site->getStatus()) > 1) ? false:true;
?>
<div class="dashboard">
	<?php if($siteActive) { ?>
	<div class="dashboard-t"></div>
	<?php } ?>
	<div class="dashboard-c" <?php if(!$siteActive) { echo "style=\"background:url('')\""; } ?>>
		<div class="dashboard-content" <?php if(!$siteActive) { echo "style=\"margin:0px; padding:0px;\""; } ?>>
			<!-- block-holder -->
			<div class="block-holder">
				<!-- dashboard heading -->
				<div class="heading">
					<div class="row">
						<div class="title">
							<h1><?php echo $site->getName(); ?></h1>
							
							<?php if($siteActive) { ?>
							<a onclick="sitemgmt_js.edit('<?php echo $memberId; ?>', '<?php echo $site->getId(); ?>')" style="cursor:pointer;">Edit</a>
							<?php } ?>
						</div>
						<dl>
							<dt>Status:</dt>
							<dd><?php 
								if($site->getStatus() == "0") {
									echo "Pending Activation";
								} 
								else if($site->getStatus() == "1") {
									echo "Activated";
								}
								else {
									echo "Deactivated";
								} 
							?></dd>
						</dl>
					</div>
					<div class="b"></div>
				</div>
				<?php if($siteActive) { ?>
				<!-- dashboard columns -->
				<div class="columns">
					<!-- number -->
					<strong class="number"><?php echo number_format($site->getTotalMembers(),0); ?></strong>
					<!-- col -->
					<div class="col">
						<ul class="links">
							<li><a class="configure" href="<?php echo $site->getLocation(); ?>/wp-admin/admin.php?page=<?php echo MM_MODULE_CONFIGURE_SITE ?>" target="_blank">Configure Site</a></li>
							<li><a class="manage" href="<?php echo $site->getLocation(); ?>/wp-admin/admin.php?page=<?php echo MM_MODULE_MANAGE_MEMBERS ?>" target="_blank">Manage Members</a></li>
						</ul>
					</div>
					<!-- col -->
					<div class="col">
						<a href="http://wordpress.org/extend/plugins/membermouse/" class="button" target='_blank'>DOWNLOAD MM PLUGIN</a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	
	<?php if($siteActive) { ?>
	<div class="dashboard-b"></div>
	<?php } ?>
</div>
<?php
}
?>

<?php

if($memberType->isValid() && $acctType->isValid()) {
	
?>

<div class="section-top">
	<?php if(count($sites) < intval($acctType->getNumSites())) { ?>
	<a onclick="sitemgmt_js.create('<?php echo $user->getId(); ?>')" class="button">CREATE A SITE</a>
	<?php } ?>
	
	<!-- section-top area -->
	<div class="area">
		<div class="r"></div>
		<div class="holder">
			<strong class="title"><?php echo $acctType->getName(); ?></strong>
			<span>
				[<?php echo count($sites); ?> of <?php echo $acctType->getNumSites(); ?> sites used, 
				<?php echo number_format($totalMembers); ?> of <?php echo $acctType->getNumTotalMembersStr(); ?> total members used]
			</span>
		</div>
	</div>
</div>

<?php

if($errorMsg == "") 
{ 
	if(isset($sites) && count($sites) > 0) 
	{
		for($i = 0; $i < count($sites); $i++) 
		{
			renderSiteModule($user->getId(), $sites[$i]);
		}
		
		?>
		<div class="text-box-page">
			<div class="t"></div>
			<div class="c">
				<strong class="title">Installation Instructions</strong>
				<p></p>
			</div>
			<div class="b"></div>
		</div>
		
		<?php
		} else {
		?>
		<div class="text-box-page">
			<div class="t"></div>
			<div class="c">
				<strong class="title">Creating a Site</strong>
				<p>To create your first site, click the <i>Create a Site</i> button above and fill out the required information.</p>
			</div>
			<div class="b"></div>
		</div>
		<?php } ?>
	<?php } else { ?>
	<div class="text-box-page">
		<div class="t"></div>
		<div class="c">
			<strong class="title">Error retrieving site information</strong>
			<p><?php echo $errorMsg; ?></p>
		</div>
		<div class="b"></div>
	</div>
	<?php } ?>

<?php } else { ?>
<br/>
<div class="text-box-page">
	<div class="t"></div>
	<div class="c">
		<strong class="title">Error retrieving site information</strong>
		<p>We were unable to verify your account. Please contact customer support.</p>
	</div>
	<div class="b"></div>
</div>
<?php } ?>
