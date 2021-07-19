<?php
	$response = file_get_contents('http://localhost/005_ProjektBielska54/ksiegowosc.nzpe.pl/wyciagi,raw,refresh');
	$response2 = file_get_contents('http://localhost/005_ProjektBielska54/ksiegowosc.nzpe.pl/powiadomienia,raw,refresh');

	$jestkatalognalogi = true;

	if(!file_exists(RootPath()."/logs"))
	{
		if(!mkdir(RootPath()."/logs"))
		{
			$jestkatalognalogi = false;
		}
	}
	if($jestkatalognalogi)
	{
		$fsize = filesize( RootPath()."/logs/crontab.log" );
		if($fsize > 20971520)
		{
			unlink(RootPath()."/logs/crontab.log");
		}

		$plik=fopen(RootPath()."/logs/crontab.log",'a');
		fwrite($plik, "//--------------------------------------------------------------------------------\n");
		fwrite($plik, "// ".date("Y-m-d H:i:s")."\n");
		fwrite($plik, "//--------------------------------------------------------------------------------\n");
		fwrite($plik, $response."\n");
		fwrite($plik, "//################################################################################\n");
		fwrite($plik, $response2."\n");
		fwrite($plik, "//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n\n");
		fclose($plik);
	}

	function RootPath()
	{
		$path_full = dirname($_SERVER['PHP_SELF']);
		$path_tab = explode("/", $path_full);
		$path_count = count($path_tab);
		$path="";
		for($i=2;$i<count($path_tab);$i++)
		$path.="../";
		if($path=="")$path="./";
		return $path;
	}
?>