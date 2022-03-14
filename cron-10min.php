<?php
	$date = new DateTime();
	$plik=fopen("./logs/syslog",'a');
	fwrite($plik, $date->format('Y-m-d H:i:s') . ": cron-10min.php");
	fclose($plik);
	
	$response = file_get_contents('http://ksiegowosc.nzpe.pl/wyciagi,raw,refresh');
	$response2 = file_get_contents('http://ksiegowosc.nzpe.pl/powiadomienia,raw,refresh');
?>