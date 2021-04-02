<?php
if(!class_exists('data'))
{
	class data
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
		public function formatnazwamcgodzina($dataw)
		{
			//rozbijam date na date, godzine
			$rozbitadata=explode(" ",$dataw);
			//rozbijam date na elementy
			$dataelementy=explode("-",$rozbitadata[0]);
			//rozbijam godzinę na elementy
			$godzinaelementy=explode(":",$rozbitadata[1]);
			//tworzę timestamp
			$timestamp=mktime($godzinaelementy[0], $godzinaelementy[1], $godzinaelementy[2], $dataelementy[1], $dataelementy[2], $dataelementy[0]);
			//składam datę i zwracam
			$rettext=$dataelementy[2]."-".$dataelementy[1]."-".$dataelementy[0].", ".$this->dayentopl(date("l",$timestamp)).", godz.: ".$godzinaelementy[0].":".$godzinaelementy[1];
			
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function formatdatagodzina($dataw)
		{
			//rozbijam date na date, godzine
			$rozbitadata=explode(" ",$dataw);
			//rozbijam date na elementy
			$dataelementy=explode("-",$rozbitadata[0]);
			//rozbijam godzinę na elementy
			$godzinaelementy=explode(":",$rozbitadata[1]);
			//tworzę timestamp
			$timestamp=mktime($godzinaelementy[0], $godzinaelementy[1], $godzinaelementy[2], $dataelementy[1], $dataelementy[2], $dataelementy[0]);
			//składam datę i zwracam
			$rettext=$dataelementy[2].".".$dataelementy[1].".".$dataelementy[0]."r. godz. ".$godzinaelementy[0].":".$godzinaelementy[1];
				
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function formatgodzina($dataw)
		{
			//rozbijam date na date, godzine
			$rozbitadata=explode(" ",$dataw);
			//rozbijam date na elementy
			$dataelementy=explode("-",$rozbitadata[0]);
			//rozbijam godzinę na elementy
			$godzinaelementy=explode(":",$rozbitadata[1]);
			//składam datę i zwracam
			$rettext="godz. ".$godzinaelementy[0].":".$godzinaelementy[1];
		
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function timestamp($datainput)
		{
			//rozbijam date na date, godzine
			$rozbitadata=explode(" ",$datainput);
			//rozbijam date na elementy
			$dataelementy=explode("-",$rozbitadata[0]);
			//rozbijam godzinę na elementy
			$godzinaelementy=explode(":",$rozbitadata[1]);
			//zwracam timestamp
			return mktime($godzinaelementy[0], $godzinaelementy[1], $godzinaelementy[2], $dataelementy[1], $dataelementy[2], $dataelementy[0]);
		}
		//----------------------------------------------------------------------------------------------------
		public function dmr($datainput)
		{
			//rozbijam date na date, godzine
			$rozbitadata=explode(" ",$datainput);
			//rozbijam date na elementy
			$dataelementy=explode("-",$rozbitadata[0]);
			//rozbijam godzinę na elementy
			$godzinaelementy=explode(":",$rozbitadata[1]);
			//tworzę timestamp
			$timestamp=mktime($godzinaelementy[0], $godzinaelementy[1], $godzinaelementy[2], $dataelementy[1], $dataelementy[2], $dataelementy[0]);
			//składam datę i zwracam
			$rettext=$dataelementy[2].".".$dataelementy[1].".".$dataelementy[0]."r.";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function dayentopl($name)
		{
			switch($name)
			{
				case "Sunday":
					$rettext="Niedziela";
					break;
				case "Monday":
					$rettext="Poniedziałek";
					break;
				case "Tuesday":
					$rettext="Wtorek";
					break;
				case "Wednesday":
					$rettext="Środa";
					break;
				case "Thursday":
					$rettext="Czwartek";
					break;
				case "Friday":
					$rettext="Piątek";
					break;
				case "Saturday":
					$rettext="Sobota";
					break;
				default:
					$rettext=$name;
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function mcname($mc,$odmiana=false)
		{
			if(isset($mc) && $mc!="" && is_numeric($mc) && $mc>0 && $mc<13)
			{
				switch($mc)
				{
					case 1:
						if(!$odmiana)
							$rettext="Styczeń";
						else
							$rettext="Stycznia";
						break;
					case 2:
						if(!$odmiana)
							$rettext="Luty";
						else
							$rettext="Lutego";
						break;
					case 3:
						if(!$odmiana)
							$rettext="Marzec";
						else
							$rettext="Marca";
						break;
					case 4:
						if(!$odmiana)
							$rettext="Kwiecień";
						else
							$rettext="Kwietnia";
						break;
					case 5:
						if(!$odmiana)
							$rettext="Maj";
						else
							$rettext="Maja";
						break;
					case 6:
						if(!$odmiana)
							$rettext="Czerwiec";
						else
							$rettext="Czerwca";
						break;
					case 7:
						if(!$odmiana)
							$rettext="Lipiec";
						else
							$rettext="Lipca";
						break;
					case 8:
						if(!$odmiana)
							$rettext="Sierpień";
						else
							$rettext="Sierpnia";
						break;
					case 9:
						if(!$odmiana)
							$rettext="Wrzesień";
						else
							$rettext="Września";
						break;
					case 10:
						if(!$odmiana)
							$rettext="Październik";
						else
							$rettext="Października";
						break;
					case 11:
						if(!$odmiana)
							$rettext="Listopad";
						else
							$rettext="Listopada";
						break;
					case 12:
						if(!$odmiana)
							$rettext="Grudzień";
						else
							$rettext="Grudnia";
						break;
					default:
						$rettext=$mc;
				}
			}
			else
				$rettext="Nieprawidłowy miesiąc";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>