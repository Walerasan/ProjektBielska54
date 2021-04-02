<?php
if(!class_exists('ini'))
{
	class ini
	{
		//----------------------------------------------------------------------------------------------------
	    public function __construct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{			
		}
		//----------------------------------------------------------------------------------------------------
		public function wczytajplikini($pathtofile)
		{
			$this->inifile=parse_ini_file($pathtofile,TRUE);
		}
		//----------------------------------------------------------------------------------------------------
		public function getvalue($section,$name)
		{
			if(!isset($this->inifile[$section][$name]))
				$rettext=null;
			else
				$rettext=$this->inifile[$section][$name];
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function addvalue($section,$name,$val)
		{
			$val=str_replace('"',"&quot;",$val);
			$this->inifile[$section][$name]=$val;
		}
		//----------------------------------------------------------------------------------------------------
		public function zapiszplikini($pathtofile)
		{
			//tworzę backup
			//copy($pathtofile,$pathtofile.".".date("Ymdhis").".backup");
			//otwieram plik 
			$plik=fopen($pathtofile,'w');
			foreach($this->inifile as $key=>$val)
			{
				fwrite($plik,"[$key]\n");
				foreach($val as $key2=>$val2)
					fwrite($plik,"\t$key2=\"$val2\"\n");
			}			
			fclose($plik);
		}
		//----------------------------------------------------------------------------------------------------
		public function tablicaselektorow($pathtofile)
		{
			return parse_ini_file($pathtofile,TRUE);
		}
		//----------------------------------------------------------------------------------------------------
	}//end class	
}//end if
else
    die("Class exists: ".__FILE__);
?>