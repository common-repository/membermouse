<?php 
$path = MM_PLUGIN_ABSPATH."/cron";
$ranCron = false;

if(isset($_POST["run_cron"])){
	$cronMessage = "Cron has been initiated.";
	MM_CronEngine::run("notifications", true);
	$ranCron = true;
}

$phpPath = exec("which php");

if(isset($_POST["save_cron_settings"])){
	if(isset($_POST["run_cron_web"]) && $_POST["run_cron_web"]=='1'){
		MM_OptionUtils::setOption("mm-run-cron-web","1");
	}
	else{
		MM_OptionUtils::setOption("mm-run-cron-web","0");
	}
}
$checked = (MM_OptionUtils::getOption("mm-run-cron-web")=='1')?"checked":"";
?>
<form name='ssl' method='post'>
<div class="wrap">
    <img src="<?php echo MM_Utils::getImageUrl('lrg_clock'); ?>" class="mm-header-icon" /> 
    <h2 class="mm-header-text">Cron Settings</h2>
	
	<div id="mm-form-container" style="margin-top: 10px; margin-bottom: 15px;">
		<b>Manual Execution</b><br />
		<p style="width:650px">You can initiate the cron by selecting the button provided here. It will force execution of the cron regardless of the last time it ran and when it is scheduled to run next.</p>
	<?php 
	if($ranCron){
		?>
		<div style='padding-left: 5px; color: green'><?php echo $cronMessage; ?></div><br />
		<?php 
	}
	?>	
	<input type='submit' name='run_cron' value='Manually Run Cron' class="button-primary" />
		
		<div style="width: 100%; margin-top: 20px; margin-bottom: 20px;" class="mm-divider"></div>
		<b>Server-Based Setup</b><br /><br />
		This solution is only if you have Plesk or Cpanel installed on your server.  <br /><br />
		<i>Minute:</i> Enter a minute to represent the minute of each hour and day that you'd like the cron to run. You can use a '*' to indicate every minute.<br />
		<i>Hour:</i> Enter an hour to represent the hour of each day you'd like the cron to run.  You can use a '*' to indicate every hour.<br />
		<i>Day of Month:</i> Enter what day of the month you'd like for the cron to run.  You can use a '*' to indicate every day.<br />
		<i>Month:</i> Enter the month you'd like for the cron to run.  You can use a '*' to indicate every month.<br />
		<i>Day of Week:</i> Enter the day of the week you'd like the cron to run.  You can use a '*' to indicate every day of the week.<br />
		<i>Command:</i> The path and file to execute on the increment.<br /><br />
		<div>
			<ul>
				<li>
				<b><u>Plesk</u></b><br />
					1. Read the howto for <a href="http://www.hosting.com/support/plesk/crontab" target='_blank'>Plesk Task Scheduler</a> (new window) <br />
					2. For the portion where you enter in the crontab information, you simply put the following:<br /><br />
					<div style='padding-left: 30px;'>
						What you should enter for this cron:<br /><br />
						<table width='450'>
							<tr>
								<td width='120px'>Minute:</td>
								<td><input type='text' readonly value='2' /></td>
							</tr>
							<tr>
								<td>Hour:</td>
								<td><input type='text' readonly value='6' /></td>
							</tr>
							<tr>
								<td>Day Of Month:</td>
								<td><input type='text' readonly value='*' /></td>
							</tr>
							<tr>
								<td>Month:</td>
								<td><input type='text' readonly value='*' /></td>
							</tr>
							<tr>
								<td>Day Of Week:</td>
								<td><input type='text' readonly value='*' /></td>
							</tr>
							<tr>
								<td>Command:</td>
								<td><input type='text' readonly value='cd <?php echo $path; ?>; <?php echo $phpPath; ?> runme.php' style='width: 875px;'/></td>
							</tr> 
						</table><br />
					<i>By entering this information it will execute the file inquestion once per day at 6:02am.</i>
						 
					</div>
<br />
				</li>
				<li>
				<b><u>Cpanel</u></b><br />
					1. Read the instructions for <a href="http://www.hosting.com/support/cpanelvps/creating-cron-jobs" target='_blank'>cron jobs in Cpanel</a> (new window)<br />
					2. You can either choose the drop down for "Once per day" or enter the following:<br /><br />
					
					<div style='padding-left: 30px;'>
						<table width='450'>
							<tr>
								<td width='120px'>Minute:</td>
								<td><input type='text' readonly value='2' /></td>
							</tr>
							<tr>
								<td>Hour:</td>
								<td><input type='text' readonly value='6' /></td>
							</tr>
							<tr>
								<td>Day Of Month:</td>
								<td><input type='text' readonly value='*' /></td>
							</tr>
							<tr>
								<td>Month:</td>
								<td><input type='text' readonly value='*' /></td>
							</tr>
							<tr>
								<td>Day Of Week:</td>
								<td><input type='text' readonly value='*' /></td>
							</tr>
							<tr>
								<td>Command:</td>
								<td><input type='text' readonly value='cd <?php echo $path; ?>; <?php echo $phpPath; ?> runme.php' style='width: 875px;'/></td>
							</tr> 
						</table><br />
					<i>By entering this information it will execute the file inquestion once per day at 6:02am.</i>
						 
					</div>
				</li>
			</ul>
		</div>
		<div style="width: 100%; margin-top: 20px; margin-bottom: 20px;" class="mm-divider"></div>
		
		<b>Advanced server-based setup on linux installations</b><br /><br />
		<div>
			<ul>
				<li>
					Step 1: ssh into a terminal for your server.
				</li>
				<li>
					Step 2: type the following command <br /><br />
					<div style='text-align:center; background-color: #eee; width: 500px'>crontab -e</div><br />
				</li>
				<li>
					Step 3: Copy and paste the following line into the cron<br /><br />
					<div style='text-align:center; background-color: #eee; width: 500px'>1 3 * * * cd <?php echo $path; ?>; <?php echo $phpPath; ?> runme.php</div>
					<br /><br />
				</li>
			</ul>
		</div>
	</div>
</div>
</form>