<?php
if(!class_exists('imagemagic'))
{
    class imagemagic
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
		public function savepicture($page_obj,$pic,$pathtosave,$id)
		{
			if(is_uploaded_file($pic['tmp_name']))
			{
				$rozszerzenie=strtolower(strrpos($pic['name'],'.')>0?substr($pic['name'],strrpos($pic['name'],'.')+1):"");
				if($rozszerzenie=="jpg" || $rozszerzenie=="jpeg" || $rozszerzenie=="gif" || $rozszerzenie=="png")
				{
					if(copy($pic['tmp_name'],"$pathtosave/$id.$rozszerzenie"))
					{
						$rettext[0]=1;
						$rettext[1]="$pathtosave/$id.$rozszerzenie";
						$rettext[2]="$id.$rozszerzenie";
					}
					else
					{
						$rettext[0]=0;
						$rettext[1]=$page_obj->language_obj->pobierz('bladkopiowaniazdjeciadokatalogu',true)." ( - {$pic['name']} - $pathtosave).";
					}
				}
				else
				{
					$rettext[0]=3;
					$rettext[1]=$page_obj->language_obj->pobierz('zlyformatzdjecia',true)." ( - {$pic['name']}).";
				}
			}
			else
			{
				$rettext[0]=2;
				$rettext[1]=$page_obj->language_obj->pobierz('brakzdjecia',true)." ( - {$pic['name']}).";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function savepictureinarray($page_obj,$pic,$key,$pathtosave,$id)
		{
			if(is_uploaded_file($pic['tmp_name'][$key]))
			{
				$rozszerzenie=strtolower(strrpos($pic['name'][$key],'.')>0?substr($pic['name'][$key],strrpos($pic['name'][$key],'.')+1):"");
				if($rozszerzenie=="jpg" || $rozszerzenie=="jpeg" || $rozszerzenie=="gif" || $rozszerzenie=="png")
				{
					if(copy($pic['tmp_name'][$key],"$pathtosave/$id.$rozszerzenie"))
					{
						$rettext[0]=1;
						$rettext[1]="$pathtosave/$id.$rozszerzenie";
						$rettext[2]="$id.$rozszerzenie";
					}
					else
					{
						$rettext[0]=0;
						$rettext[1]=$page_obj->language_obj->pobierz('bladkopiowaniazdjeciadokatalogu',true)."( - {$pic['name'][$key]} - $pathtosave).";
						$rettext[2]="";
					}
				}
				else
				{
					$rettext[0]=3;
					$rettext[1]=$page_obj->language_obj->pobierz('zlyformatzdjecia',true)." ( - {$pic['name'][$key]}).";
					$rettext[2]="";
				}
			}
			else
			{
				$rettext[0]=2;
				$rettext[1]=$page_obj->language_obj->pobierz('brakzdjecia',true)." ( - {$pic['name'][$key]}).";
				$rettext[2]="";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function backuppicture($pathtopic)
		{
			if(file_exists($pathtopic))
			{
				if(copy($pathtopic,$pathtopic.".backup"))
				{
					$rettext[0]=1;
					$rettext[1]="$pathtopic.backup";
				}
				else
				{
					$rettext[0]=0;
					$rettext[1]="Błąd kopiowania zdjęcia do backup. (savepicture - $pathtopic).";
				}
			}
			else
			{
				$rettext[0]=2;
				$rettext[1]="Brak pliku. (savepicture - $pathtopic).";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function resizepicture($page_obj,$pathtopic,$pathtoout,$szerokos,$wysokosc,$wymusformat=0)
		{
			//----------------------------------------------------------------------------------------------------
			//sprawdzam program imagemagic
			//----------------------------------------------------------------------------------------------------
		    $rettext=$this->sprawdzimagemagic($page_obj);
			//----------------------------------------------------------------------------------------------------
			//zmieniam rozmiar zdjecia
			//----------------------------------------------------------------------------------------------------
			if(file_exists($pathtopic))
			{
				//pobieram nazwe zdjecia
				if($wymusformat)
				    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"".$szerokos."x".$wysokosc."!\" $pathtoout",$output);
				else
				{
					if($szerokos==0)
					    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"x".$wysokosc."\" $pathtoout");
					else if($wysokosc==0)
					    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"".$szerokos."\" $pathtoout");
					else
					    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"".$szerokos."x".$wysokosc.">\" $pathtoout",$output);
				}
				
				$rettext[0]=1;
				$rettext[1]=$pathtoout;
			}
			else
			{
				$rettext[0]=2;
				$rettext[1]="Brak pliku. ( - $pathtopic).";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function croppicture($page_obj,$pathtopic,$pathtoout,$width,$height,$background='',$onlybiger=true)//format background rgba
		{
			//----------------------------------------------------------------------------------------------------
			//sprawdzam program imagemagic
			//----------------------------------------------------------------------------------------------------
		    $rettext=$this->sprawdzimagemagic($page_obj);
			//----------------------------------------------------------------------------------------------------
			//sprawdzam czy plik istnieje
			//----------------------------------------------------------------------------------------------------
			if(!file_exists($pathtopic))
			{
				$rettext[0]=2;
				$rettext[1]="Brak pliku. (croppicture - $pathtopic).";
				return $rettext;
			}
			//----------------------------------------------------------------------------------------------------
			//sprawdzam program czy ma wymiar
			//----------------------------------------------------------------------------------------------------
			//pobieram szerokość i wysokość zdjęcia
			$w=exec($page_obj->server_cfg_obj->identify." -format \"%w\" \"$pathtopic\"");
			$h=exec($page_obj->server_cfg_obj->identify." -format \"%h\" \"$pathtopic\"");
			if($w==0 || $h==0)
			{
				$rettext[0]=2;
				$rettext[1]="Plik ma zerowy wymiar. (croppicture - $pathtopic).";
				$rettext[2]="";
				return $rettext;
			}
			//----------------------------------------------------------------------------------------------------
			//sprawdzam czy zdjęcie ma większy rozmiar jeżeli ustawiono onlybiger
			//----------------------------------------------------------------------------------------------------
			if($onlybiger)
			{
				if($w<$width && $h<$height)//gdy zdjęcie jest mniejsze 
				{
					//kopiuje tylko plik i nic nie zmieniam
					copy($pathtopic,$pathtoout);
					$rettext[0]=2;
					$rettext[1]="Plik jest ma mniejszy wymiar. Nie zmieniam nic";
					$rettext[2]=substr($pathtoout,strrpos($pathtoout,"/")+1);//zwracam tylko nazwę pliku;
					return $rettext;
				}	
			}
			//----------------------------------------------------------------------------------------------------
			//przechodzę do kadrowania
			//----------------------------------------------------------------------------------------------------
			//jeden z wymiarów mniejszy od wymaganego
			//----------------------------------------------------------------------------------------------------
			//jeżeli podano tło $fillbackground to dopasowuje dłuższy wymiar wtedy krótszy będzie już ok i podkładam tło
			//----------------------------------------------------------------------------------------------------
			if(isset($background) && $background!="")
			{
				//sprawedzam poprawność tła
				$pattern = '/^[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]{1,3}$/';
				if(preg_match($pattern, $background)==1)
					$tlo="rgba($background)";
				else
					$tlo="rgba(255,255,255,100)";// przjmuje tło za białe
				//tworzę tło
					exec($page_obj->server_cfg_obj->convert." -size ".$width."x".$height." xc:\"$tlo\" PNG32:$pathtoout.png");
				//--------------------
				//szukam krótszy wymiar
				if($w<$h)
				{//dostosowuje po szerokości
					//rozszerzam wysokośc do wymaganego wymiaru
				    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"x".$height."\" $pathtoout");
					//nakładam obrazki razem
				    exec($page_obj->server_cfg_obj->composite." -gravity center $pathtoout $pathtoout.png $pathtoout");
					//usuwam tło
					unlink("$pathtoout.png");
					$rettext[0]=1;
					$rettext[1]="Plik zapisany. (croppicture - $pathtopic).";
					$rettext[2]=substr($pathtoout,strrpos($pathtoout,"/")+1);
				}
				else
				{//dostosowuje po wysokości
					//rozszerzam wysokośc do wymaganego wymiaru
				    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"".$width."\" $pathtoout");
					//nakładam obrazki razem
				    exec($page_obj->server_cfg_obj->composite." -gravity center $pathtoout $pathtoout.png $pathtoout");
					//usuwam tło
					unlink("$pathtoout.png");
					$rettext[0]=1;
					$rettext[1]="Plik zapisany. (croppicture - $pathtopic).";
					$rettext[2]=substr($pathtoout,strrpos($pathtoout,"/")+1);//zwracam tylko nazwę pliku
				}
			}
			else
			{
				//----------------------------------------------------------------------------------------------------
				//jeżeli nie podano tła to dopasowuje krótszy wymiar do wymaganego a drugi przycinam
				//--------------------
				//wybieram wymiar po którym zmniejszyć i przycinam
				//według wzoru $w-$width>$h-$height
				$unlock=true;
				if($height>=(($h*$width)/$w))
				{//dostosowuje po wysokości
					//rozszerzam wysokośc do wymaganego wymiaru
				    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"x".$height."\" $pathtoout");
					//ustalam o ile przesunąć przycięcie
				    $w=exec($page_obj->server_cfg_obj->identify." -format \"%w\" \"$pathtoout\"");
					$przesunieciew=abs((int)($w-$width)/2);
					//teraz przycinam do naszego wymiaru
					$this->strona->syslog("$w<$h",$page_obj->server_cfg_obj->convert." $pathtoout -crop ".$width."x".$height."+$przesunieciew+0 $pathtoout");
					if($unlock)
					    exec($page_obj->server_cfg_obj->convert." $pathtoout -crop ".$width."x".$height."+$przesunieciew+0 $pathtoout");
					$rettext[0]=1;
					$rettext[1]="Plik zapisany. (croppicture - $pathtopic).";
					$rettext[2]=substr($pathtoout,strrpos($pathtoout,"/")+1);//zwracam tylko nazwę pliku
				}
				else
				{//dostosowuje po szerokości
					//rozszerzam szerokość do wymaganego wymiaru
				    exec($page_obj->server_cfg_obj->convert." $pathtopic -resize \"".$width."\" $pathtoout");
					//ustalam o ile przesunąć przycięcie
				    $h=exec($page_obj->server_cfg_obj->identify." -format \"%h\" \"$pathtoout\"");
					$przesuniecieh=abs((int)($h-$height)/2);
					//teraz przycinam do naszego wymiaru
					$this->strona->syslog("$w<$h",$page_obj->server_cfg_obj->convert." $pathtoout -crop ".$width."x".$height."+0+$przesuniecieh $pathtoout");
					if($unlock)
					    exec($page_obj->server_cfg_obj->convert." $pathtoout -crop ".$width."x".$height."+0+$przesuniecieh $pathtoout");
					$rettext[0]=1;
					$rettext[1]="Plik zapisany. (croppicture - $pathtopic).";
					$rettext[2]=substr($pathtoout,strrpos($pathtoout,"/")+1);//zwracam tylko nazwę pliku
				}
			}
			//----------------------------------------------------------------------------------------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function sprawdzimagemagic($page_obj)
		{
			$rettext[0]=1;
			$rettext[1]="";
			//----------------------------------------------------------------------------------------------------
			//sprawdzam poprawność programu convert
			//----------------------------------------------------------------------------------------------------
			if($page_obj->server_cfg_obj->convert=="")
			{
				$rettext[0]=3;
				$rettext[1]="Brak scieżki do programu ImageMagic - convert ";
				return $rettext;
			}
			exec($page_obj->server_cfg_obj->convert." -version",$output);
			if(!eregi("ImageMagick",$output[0]))
			{
				$rettext[0]=4;
				$rettext[1]="Podana ścieżka nie wskazuje na program ImageMagic - convert";
				return $rettext;
			}
				
			//----------------------------------------------------------------------------------------------------
			//sprawdzam poprawność programu identify
			//----------------------------------------------------------------------------------------------------
			if($page_obj->server_cfg_obj->identify=="")
			{
				$rettext[0]=3;
				$rettext[1]="Brak scieżki do programu ImageMagic - identify";
				return $rettext;
			}
			exec($page_obj->server_cfg_obj->identify." -version",$output);
			if(!eregi("ImageMagick",$output[0]))
			{
				$rettext[0]=4;
				$rettext[1]="Podana ścieżka nie wskazuje na program ImageMagic - identify";
				return $rettext;
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function picturewidth($page_obj,$zdjecie)
		{
		    return exec($page_obj->server_cfg_obj->identify." -format \"%w\" \"$zdjecie\"");
		}
		//----------------------------------------------------------------------------------------------------
		public function pictureheight($page_obj,$zdjecie)
		{
		    return exec($page_obj->server_cfg_obj->identify." -format \"%h\" \"$zdjecie\"");
		}
		//----------------------------------------------------------------------------------------------------
		public function checksize($page_obj,$zdjecie,$w=-1,$h=-1,$strictly=false)
		{
			/**
			* funkcja sprawdza width i height zdjęcia i zwraca info o stanie
			* @param zdjecie string, w int, h int, strictly bool
			* @return tab [0]- nr błedu [1] - opis bledu zgodnie z jezykiem
			* @throws brak
			*/
			$blad=false;
			if(is_uploaded_file($zdjecie['tmp_name']))
			{
				if($w>0)
				{
					if($strictly)
					{
						if($this->picturewidth($zdjecie['tmp_name'])!=$w)
						{
							$rettext[0]=1;
							$rettext[1].=$page_obj->language_obj->pobierz('widthdifferent',true)." $w, ";
							$blad=true;
						}
					}
					else if($this->picturewidth($zdjecie['tmp_name'])>$w)
					{
						$rettext[0]=2;
						$rettext[1].=$page_obj->language_obj->pobierz('widthmore',true)." $w, ";
						$blad=true;
					}
					else if($this->picturewidth($zdjecie['tmp_name'])<$w)
					{
						$rettext[0]=-2;
						$rettext[1].=$page_obj->language_obj->pobierz('widthless',true)." $w, ";
						$blad=true;
					}
				}
				if($h>0)
				{
					if($strictly)
					{
						if($this->pictureheight($zdjecie['tmp_name'])!=$h)
						{
							$rettext[0]=3;
							$rettext[1].=$page_obj->language_obj->pobierz('heightdifferent',true)." $h, ";
							$blad=true;
						}
					}
					else if($this->pictureheight($zdjecie['tmp_name'])>$h)
					{
						$rettext[0]=4;
						$rettext[1].=$page_obj->language_obj->pobierz('heightmore',true)." $h, ";
						$blad=true;
					}
					else if($this->pictureheight($zdjecie['tmp_name'])>$h)
					{
						$rettext[0]=-4;
						$rettext[1].=$page_obj->language_obj->pobierz('heightless',true)." $h, ";
						$blad=true;
					}
				}
				if(!$blad)
				{
					$rettext[0]=0;
					$rettext[1]=$page_obj->language_obj->pobierz('widthheightok',true);
				}
			}
			else
			{
				$rettext[0]=5;
				$rettext[1].=$page_obj->language_obj->pobierz('brakzdjecia',true);
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function nalozlogo($page_obj,$pathtopic,$pathtoout,$logo)
		{
			//----------------------------------------------------------------------------------------------------
			//sprawdzam program imagemagic
			//----------------------------------------------------------------------------------------------------
		    $rettext=$this->sprawdzimagemagic($page_obj);
			//----------------------------------------------------------------------------------------------------
			//zmieniam rozmiar zdjecia
			//----------------------------------------------------------------------------------------------------
			if(file_exists($pathtopic))
			{
				//exec($this->strona->ustawienia->composite." -gravity center $logo $pathtopic $pathtoout");
			    exec($page_obj->server_cfg_obj->composite." -gravity NorthWest $logo $pathtopic $pathtoout");
				$rettext[0]=1;
				$rettext[1]=$pathtoout;
			}
			else
			{
				$rettext[0]=2;
				$rettext[1]="Brak pliku. (resizepicture - $pathtopic).";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>