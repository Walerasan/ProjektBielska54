<?php
/*
 * //pobieram liczbe wszystkich wyników
		$this->baza->pobierzdane("select idd,nazwa,usuniety,najpopularniejszy,symbol from ".get_class($this)." order by idd");
		$iloscwszystkich=$this->baza->iloscwynikow();
		$iloscnastronie=20;
		if($aktualnailosc=="")$aktualnailosc=$_SESSION['aktualnailosc'];
		if($aktualnailosc=="")$aktualnailosc=0;//jezeli dalej pusta to startujemy od 0
		//--------------------
		$wynik=$this->baza->pobierzdane("select idd,nazwa,usuniety,najpopularniejszy,symbol from ".get_class($this)." order by idd limit $aktualnailosc,$iloscnastronie");
		//tu sobie cos jest
		//--------------------
		include_once('../classes/subpages.php');
		$subpages=new subpages($this->baza);
		$rettext.=$subpages->create($iloscwszystkich,$iloscnastronie,$aktualnailosc,get_class($this).",lista");
 */
if(!class_exists('subpages'))
{
    class subpages
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
		public function create($iloscwszystkich,$iloscnastronie,$aktualnailosc,$link)
		{
			$tresc="";
			//zapisuje aktualna ilosc w sesji przyda sie przy powrocie np z edycji			
			$_SESSION['aktualnailosc']=$aktualnailosc;
			//--------------------
			$activepage=(integer)($aktualnailosc/$iloscnastronie);
			//-------------------------
			//jezeli jedna strona
			//-------------------------
			//to by w sumie było nie potrzebne bo wyżej jest definicja pustego rettext
			//if($iloscnastronie>$iloscwszystkich)
			//	$tresc="";
			//-------------------------
			//obliczanie liczby stron
			//-------------------------
			$lpage=(integer)(($iloscwszystkich/$iloscnastronie)+0.99);
			//-------------------------
			//jezeli więcej niz jedna ale mniej niz 9
			//-------------------------
			if($lpage>1 && $lpage<9)
			{
				if($activepage>0)
				{
				    $tresc.=$this->number_view("&laquo;&laquo;","$link,".((($activepage-1)*$iloscnastronie)),true);
					//$tresc.="<a href='$link,".((($activepage-1)*$iloscnastronie))."' style='text-decoration:none;font-size:12px;'>&laquo;&laquo;&laquo;</a>&#160;";
				}
				else
				{
				    $tresc.=$this->number_view("&laquo;&laquo;","",false);
					//$tresc.="<font  style='text-decoration:none;font-size:12px;'>&laquo;&laquo;&laquo;&#160;</font>";
				}
				//-------------------------
				for($i=1;$i<=$lpage;$i++)
					if($activepage==$i-1)
					{
					    $tresc.=$this->number_view($i,"",false);
						//$tresc.="<font  style='text-decoration:none;font-size:12px;'><b>[$i]</b>&#160;</font>";
					}
					else
					{
					    $tresc.=$this->number_view($i,"$link,".(($i-1)*$iloscnastronie),true);
						//$tresc.="<a href='$link,".(($i-1)*$iloscnastronie)."'  style='text-decoration:none;font-size:12px;'>[$i]</a>&#160;";
					}
				//-------------------------
				if($activepage<$lpage-1)
				{
				    $tresc.=$this->number_view("&raquo;&raquo;","$link,".((($activepage+1)*$iloscnastronie)),true);
					//$tresc.="<a href='$link,".((($activepage+1)*$iloscnastronie))."'  style='text-decoration:none;font-size:12px;'>&raquo;&raquo;&raquo;</a>";
				}
				else
				{
				    $tresc.=$this->number_view("&raquo;&raquo;","",false);
					//$tresc.="<font  style='text-decoration:none;font-size:12px;'>&raquo;&raquo;&raquo;</font>";
				}
			};
			//jezeli więcej niz 9 stron
			if($lpage>=9)
			{
				if($activepage>0)
				{
				    $tresc.=$this->number_view("&laquo;&laquo;","$link,".((($activepage-1)*$iloscnastronie)),true);
					//$tresc.="<a href='$link,".((($activepage-1)*$iloscnastronie))."'  style='text-decoration:none;font-size:12px;'>&laquo;&laquo;&laquo;</a>&#160;";
				}
				else
				{
				    $tresc.=$this->number_view("&laquo;&laquo;","",false);
					//$tresc.="<font  style='text-decoration:none;font-size:12px;'>&laquo;&laquo;&laquo;&#160;</font>";
				}
				//------------------------
				if($activepage<5)
				{
					for($it=1;$it<10;$it++)
					{
						if($activepage==$it-1)
						{
						    $tresc.=$this->number_view($it,"",false);
							//$tresc.="<font  style='text-decoration:none;font-size:12px;'><b>[$it]</b>&#160;</font>";
						}
						else
						{
						    $tresc.=$this->number_view($it,"$link,".(($it-1)*$iloscnastronie),true);
							//$tresc.="<a href='$link,".(($it-1)*$iloscnastronie)."'  style='text-decoration:none;font-size:12px;'>[$it]</a>&#160;";
						}
					};
				};
				//-------------------------
				if($activepage>4 && $activepage<$lpage-4)
				{
					for($it=$activepage-3;$it<$activepage+6;$it++)
					{
						if($activepage==$it-1)
						{
						    $tresc.=$this->number_view($it,"",false);
							//$tresc.="<font  style='text-decoration:none;font-size:12px;'><b>[$it]</b>&#160;</font>";
						}
						else
						{
						    $tresc.=$this->number_view($it,"$link,".(($it-1)*$iloscnastronie),true);
							//$tresc.="<a href='$link,".(($it-1)*$iloscnastronie)."'  style='text-decoration:none;font-size:12px;'>[$it]</a>&#160;";
						}
					};
				};
				//-------------------------
				if($activepage>=$lpage-4)
				{
					for($it=$lpage-8;$it<$lpage+1;$it++)
					{
						if($activepage==$it-1)
						{
						    $tresc.=$this->number_view($it,"",false);
							//$tresc.="<font  style='text-decoration:none;font-size:12px;'><b>[$it]</b>&#160;</font>";
						}
						else
						{
						    $tresc.=$this->number_view($it,"$link,".(($it-1)*$iloscnastronie),true);
							//$tresc.="<a href='$link,".(($it-1)*$iloscnastronie)."'  style='text-decoration:none;font-size:12px;'>[$it]</a>&#160;";
						}
					};
				};
				//-------------------------
				if($activepage<$lpage-1)
				{
				    $tresc.=$this->number_view("&raquo;&raquo;","$link,".((($activepage+1)*$iloscnastronie)),true);
					//$tresc.="<a href='$link,".((($activepage+1)*$iloscnastronie))."'  style='text-decoration:none;font-size:12px;'>&raquo;&raquo;&raquo;</a>";
				}
				else
				{
				    $tresc.=$this->number_view("&raquo;&raquo;","",false);
					//$tresc.="<font  style='text-decoration:none;font-size:12px;'>&raquo;&raquo;&raquo;</font>";
				}
			}
			return $tresc;
		}
		//----------------------------------------------------------------------------------------------------
		private function number_view($no,$link,$active)
		{
			$view="";
			//--------------------
			if($active)
				if($link)
				    $view.="<a href='$link'><div style='display:inline-block;width:30px;height:30px;text-align:center;background:#595959;margin:2px;'><div style='padding-top:7px;color:white;font-size:16px;'>$no</div></div></a>";
				else
				    $view.="<div style='display:inline-block;width:30px;height:30px;text-align:center;background:#A6A6A6;margin:2px;'><div style='padding-top:7px;color:white;font-size:16px;'>$no</div></div>";
			else
			    $view.="<div style='display:inline-block;width:30px;height:30px;text-align:center;background:#A6A6A6;margin:2px;'><div style='padding-top:7px;color:white;font-size:16px;'>$no</div></div>";
			//--------------------
            return $view;
		}
		//----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>