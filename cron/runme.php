<?php
echo "\nStarting Cron Engine\n";
require_once("cron.php");
echo "Loaded cron object.\n";

$cron = new Cron();
echo "Running cron object.\n";
$cron->Run();
?>