<?php
if(!class_exists('graphic'))
{
	class graphic
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
		public function savepicture($pic,$pathtosave,$id)
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
						$rettext[1]=$this->strona->jezyk->pobierz('bladkopiowaniazdjeciadokatalogu',true)." ( - {$pic['name']} - $pathtosave).";
					}
				}
				else
				{
					$rettext[0]=3;
					$rettext[1]=$this->strona->jezyk->pobierz('zlyformatzdjecia',true)." ( - {$pic['name']}).";
				}
			}
			else
			{
				$rettext[0]=2;
				$rettext[1]=$this->strona->jezyk->pobierz('brakzdjecia',true)." ( - {$pic['name']}).";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function savepictureinarray($pic,$key,$pathtosave,$id)
		{
			if(is_uploaded_file($pic['tmp_name'][$key]))
			{
				$rozszerzenie=strtolower(strrpos($pic['name'][$key],'.')>0?substr($pic['name'][$key],strrpos($pic['name'][$key],'.')+1):"");
				if($rozszerzenie=="jpg" || $rozszerzenie=="jpeg" || $rozszerzenie=="gif" || $rozszerzenie=="png")
				{
					if(copy($pic['tmp_name'][$key],"$pathtosave/$id.$rozszerzenie"))
					{
						$rettext[0]=1;
						$rettext[1]="Zapisane: $pathtosave/$id.$rozszerzenie";
						$rettext[2]="$id.$rozszerzenie";
					}
					else
					{
						$rettext[0]=0;
						$rettext[1]=$this->strona->jezyk->pobierz('bladkopiowaniazdjeciadokatalogu',true)."( - {$pic['name'][$key]} - $pathtosave).";
						$rettext[2]="";
					}
				}
				else
				{
					$rettext[0]=3;
					$rettext[1]=$this->strona->jezyk->pobierz('zlyformatzdjecia',true)." ( - {$pic['name'][$key]}).";
					$rettext[2]="";
				}
			}
			else
			{
				$rettext[0]=2;
				$rettext[1]=$this->strona->jezyk->pobierz('brakzdjecia',true)." ( - {$pic['name'][$key]}).";
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
		public function resizepicture($pathtopic,$pathtoout,$szerokos,$wysokosc,$wymusformat=0)
		{
			//----------------------------------------------------------------------------------------------------
			//sprawdzam program GD
			//----------------------------------------------------------------------------------------------------
			if($this->sprawdzGD())
			{
				//----------------------------------------------------------------------------------------------------
				//zmieniam rozmiar zdjecia
				//----------------------------------------------------------------------------------------------------
				if(file_exists($pathtopic))
				{
					//pobieram nazwe zdjecia
					if($wymusformat)
					{//zmieniam do tego rozmiaru jaki zadałem
						list($width_orig,$height_orig) = getimagesize($pathtopic);
						
						// Resample
						$image_tmp = imagecreatetruecolor($szerokos, $wysokosc);
						$image = imagecreatefromjpeg($pathtopic);
						imagecopyresampled($image_tmp, $image, 0, 0, 0, 0, $szerokos,$wysokosc, $width_orig, $height_orig);
						
						// Output the image
						imagejpeg($image_tmp,$pathtoout);
						
						// Free up memory
						imagedestroy($image_tmp);
						imagedestroy($image);
					}
					else
					{//zmieniam tylko po szerokości lub wysokosci
						
						list($width_orig,$height_orig) = getimagesize($pathtopic);
						
						$ratio_orig=$width_orig/$height_orig;
						
						if ($szerokos/$wysokosc > $ratio_orig)
							$szerokos = $wysokosc*$ratio_orig;
						else
							$wysokosc = $szerokos/$ratio_orig;
						
						// Resample
						$image_tmp = imagecreatetruecolor($szerokos, $wysokosc);
						$image = imagecreatefromjpeg($pathtopic);
						imagecopyresampled($image_tmp, $image, 0, 0, 0, 0,$szerokos, $wysokosc, $width_orig, $height_orig);
						
						// Output the image
						imagejpeg($image_tmp,$pathtoout);
						
						// Free up memory
						imagedestroy($image_tmp);
						imagedestroy($image);
						
					}
					$rettext[0]=1;
					$rettext[1]=$pathtoout;
				}
				else
				{
					$rettext[0]=2;
					$rettext[1]="Brak pliku. (resizepicture - $pathtopic).";
				}
			}
			else
			{
				$rettext[0]=0;
				$rettext[1]="Brak obsługi plików graficznych";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function croppicture($pathtopic,$pathtoout,$width,$height,$background='',$onlybiger=true)//format background rgba
		{
			//----------------------------------------------------------------------------------------------------
			//sprawdzam program GD
			//----------------------------------------------------------------------------------------------------
			if($this->sprawdzGD())
			{
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
				if($this->picturewidth($pathtopic)==0 || $this->pictureheight($pathtopic)==0)
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
					if($this->picturewidth($pathtopic)<$width && $this->pictureheight($pathtopic)<$height)//gdy zdjęcie jest mniejsze 
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
					$pattern = '/^[0-9]{1,3},[0-9]{1,3},[0-9]{1,3}$/';//rr,gg,bb
					if(preg_match($pattern, $background)==1)
						$skladowe=explode(',',$background);
					else
					{
						$skladowe[0]=255;
						$skladowe[1]=255;
						$skladowe[2]=255;
					}
				}
				//--------------------
				$width_src=$this-> picturewidth($pathtopic);
				$height_src=$this->pictureheight($pathtopic);
				$startx=0;
				$starty=0;
				//--------------------
				if($width>$height)
				{
					if(isset($background) && $background!="")
					{
						$hnew=$height;
						$wnew=($width_src*$height)/$height_src;
						$startx=($width-$wnew)/2;
					}
					else
					{
						$wnew=$width;
						$hnew=($height_src*$width)/$width_src;
						$starty=($height-$hnew)/2;
					}
				}
				else
				{
					if(isset($background) && $background!="")
					{
						$wnew=$width;
						$hnew=($height_src*$width)/$width_src;
						$starty=($height-$hnew)/2;
					}
					else
					{
						$hnew=$height;
						$wnew=($width_src*$height)/$height_src;
						$startx=($width-$wnew)/2;
					}
				}
				//--------------------
				$source_image = imagecreatefromjpeg($pathtopic);
				//tworzę obrazek
				$destination_image = imagecreatetruecolor($width, $height);
				//ustawiam tło
				$kolortla= imagecolorallocate($destination_image, $skladowe[0],$skladowe[1],$skladowe[2]);
				//wypełniam obrazek tłem
				imagefill($destination_image, 0, 0, $kolortla);
				imagecopyresized($destination_image,$source_image,$startx,$starty,0,0,$wnew,$hnew, $width_src,$height_src);
				// Output the image
				imagejpeg($destination_image,$pathtoout);
				// Free up memory
				imagedestroy($destination_image);
				imagedestroy($source_image);
				//--------------------
			}//koniec if GD
			//----------------------------------------------------------------------------------------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function sprawdzGD()
		{
			$rettext=false;
			if(extension_loaded('gd') && function_exists('gd_info'))
				$rettext=true;
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function picturewidth($zdjecie)
		{
			list($width_orig, $height_orig) = getimagesize($zdjecie);
			return $width_orig;
		}
		//----------------------------------------------------------------------------------------------------
		private function pictureheight($zdjecie)
		{
			list($width_orig, $height_orig) = getimagesize($zdjecie);
			return $height_orig;
		}
		//----------------------------------------------------------------------------------------------------
		public function checksize($zdjecie,$w=-1,$h=-1,$strictly=false)
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
							$rettext[1].=$this->strona->jezyk->pobierz('widthdifferent',true)." $w, ";
							$blad=true;
						}
					}
					else if($this->picturewidth($zdjecie['tmp_name'])>$w)
					{
						$rettext[0]=2;
						$rettext[1].=$this->strona->jezyk->pobierz('widthmore',true)." $w, ";
						$blad=true;
					}
					else if($this->picturewidth($zdjecie['tmp_name'])<$w)
					{
						$rettext[0]=-2;
						$rettext[1].=$this->strona->jezyk->pobierz('widthless',true)." $w, ";
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
							$rettext[1].=$this->strona->jezyk->pobierz('heightdifferent',true)." $h, ";
							$blad=true;
						}
					}
					else if($this->pictureheight($zdjecie['tmp_name'])>$h)
					{
						$rettext[0]=4;
						$rettext[1].=$this->strona->jezyk->pobierz('heightmore',true)." $h, ";
						$blad=true;
					}
					else if($this->pictureheight($zdjecie['tmp_name'])>$h)
					{
						$rettext[0]=-4;
						$rettext[1].=$this->strona->jezyk->pobierz('heightless',true)." $h, ";
						$blad=true;
					}
				}
				if(!$blad)
				{
					$rettext[0]=0;
					$rettext[1]=$this->strona->jezyk->pobierz('widthheightok',true);
				}
			}
			else
			{
				$rettext[0]=5;
				$rettext[1].=$this->strona->jezyk->pobierz('brakzdjecia',true);
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function nalozlogo($pathtopic,$pathtoout,$logo)
		{
			//----------------------------------------------------------------------------------------------------
			//sprawdzam program GD
			//----------------------------------------------------------------------------------------------------
			if($this->sprawdzGD())
			{
				//----------------------------------------------------------------------------------------------------
				//zmieniam rozmiar zdjecia
				//----------------------------------------------------------------------------------------------------
				if(file_exists($pathtopic))
				{
					//tworze zdjęcie z zródła, tworzę zdjęcie z loga
					$src = imagecreatefromgif($pathtopic);
					$srclogo = imagecreatefromgif($logo);
					//tworzę zdjęcie wyjsciowe na podstawie wyszokosci i szerokosci zdjęcie wejsciowego
					$dest = imagecreatetruecolor($this->picturewidth($pathtopic), $this->pictureheight($pathtopic));
					//kopiuję zdjęcie a potem logo do tego nowego zdjęcia
					imagecopy($dest, $src, 0, 0, 0, 0, $this->picturewidth($pathtopic), $this->pictureheight($pathtopic));
					imagecopy($dest, $srclogo, 0, 0, 0, 0, $this->picturewidth($logo), $this->pictureheight($logo));
					//zapisuje zdjecie do pliku 
					imagejpeg($dest,$pathtoout);
					//zwalniam pamięć 
					imagedestroy($src);
					imagedestroy($srclogo);
					imagedestroy($dest);
					//zwracam wynik
					$rettext[0]=1;
					$rettext[1]=$pathtoout;
				}
				else
				{
					$rettext[0]=2;
					$rettext[1]="Brak pliku. (resizepicture - $pathtopic).";
				}
			}
			else
			{
				$rettext[0]=0;
				$rettext[1]="Brak modułu obsługi zdjęć.";
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>