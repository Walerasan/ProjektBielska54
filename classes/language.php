<?php
include_once("./classes/ini.php");
//----------------------------------------------------------------------------------------------------
if(!class_exists('language'))
{
    class language
	{
		var $tableoflanguage;
		var $komunikat;
		var $ini_obj;
		//----------------------------------------------------------------------------------------------------
		public function __construct()
		{
		    //--------------------
		    // checking if the file exists
		    //--------------------	
			if(!file_exists("./configs/".$_SESSION['language'].".ini"))
			{
				$_SESSION['language']="pl";
				if(!file_exists("./configs/".$_SESSION['language'].".ini"))
				    die("Language file ".$_SESSION['language'].".ini not exists");
			}
			//~~~~~~~~~~~~~~~~~~~~
			
			//--------------------
			// create ini object
			//--------------------
			$this->ini_obj=new ini();
			//~~~~~~~~~~~~~~~~~~~~
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function show()
		{
			$rettext="";
			if($this->silnik->szablon=="admin")
			{
				if($this->silnik->users->is_login())
				{
					switch($this->silnik->zmk)
					{
						case "zapisz":
							$key=isset($_POST['key'])?$_POST['key']:'';
							$tresc=isset($_POST['tresc'])?$_POST['tresc']:'';
							$rettext.=$this->zapisz($key,$tresc);
							break;
						case "formularz":
							$par1=isset($_GET['par1'])?$_GET['par1']:'';
							$rettext.=$this->formularz($par1);
							break;
						case "lista":
						default:
							$rettext.=$this->lista();
							break;
					}
				}
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function zapisz($key,$tresc)
		{
			$dane="";
			//--------------------
			$this->ini_obj->wczytajplikini("./configs/".$_SESSION['language'].".ini");
			$this->ini_obj->addvalue(get_class($this),$key,"$tresc");
			$this->ini_obj->zapiszplikini("./configs/".$_SESSION['language'].".ini");
			//--------------------
			$dane.=$this->lista();
			//--------------------
			$rettext="<div>$dane</div>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function formularz($key)
		{
			$dane="";
			//--------------------
			$skrypty="<script type='text/javascript'>tinymceinicjuj(700,200,'textarea.edytorhtml');</script>";
			$style="";
			//--------------------
			$tresc_taga=$this->silnik->string->doedycji($this->ini_obj->getvalue(get_class($this),$key));
			//--------------------
			$formularz="
				<form method='post' action='".get_class($this).",admin,zapisz' name='formularzname' enctype='multipart/form-data'>
					<div style='overflow:hidden;'>
						<textarea name='tresc' class='edytorhtml'>$tresc_taga</textarea> <br />
						<input type='submit' name='zapisz' value='".($this->silnik->jezyk->pobierz(get_class($this)."_przyciskzapisz",false,false))."' />&nbsp;&nbsp;&nbsp;&nbsp;<button title='anuluj' type='button' onclick='window.location=\"".get_class($this).",admin,lista\"'>Anuluj</button>
					</div>
					<input type='hidden' name='key' value='$key'/>
					<br /><br /><br /><br /> 
				</form>";
			//--------------------
			$rettext="<div>$skrypty $style $formularz</div>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function lista()
		{
			$dane="";
			//--------------------
			//wczytuje plik
			$tablica=$this->ini_obj->tablicaselektorow("./configs/".$_SESSION['language'].".ini");
			$dane.="<table style='width:100%;' cellspacing='0' cellpadding='4'>";
			if(is_array($tablica[get_class($this)]))
				foreach($tablica[get_class($this)] as $key=>$val)
				{
					$triger=$triger=="#113653"?"#012643":"#113653";
					$podswietlenie="#315673";
					$dane.="<tr style='background:$triger;' onmouseover='this.style.background=\"$podswietlenie\"' onmouseout='this.style.background=\"$triger\"'><td>$key</td><td style='font-size:12px;'>".strip_tags($tablica[get_class($this)][$key])."</td><td><a href='".get_class($this).",admin,formularz,$key'><img src='./media/ikony/edit.png' alt='' /></a></td></tr>";
				}
			$dane.="</table>";
			//--------------------
			$rettext="<div>$dane</div>";
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function pobierz($nr,$firstupper=false,$html=true)
		{
			if($firstupper)
				$zawartosc=$this->silnik->string->toupper(mb_substr($this->selektor($nr),0,1, 'utf-8')).mb_substr($this->selektor($nr),1,mb_strlen($this->selektor($nr),'utf-8')-1,'utf-8');
			else
				$zawartosc=$this->selektor($nr);
			//--------------------
			if(!$html)
				return strip_tags($zawartosc);
			else
				return $zawartosc;
		}
		//----------------------------------------------------------------------------------------------------
		private function selektor($nr)
		{
			$rettext="";
			$trace=debug_backtrace();
			$caller=isset($trace[1])?$trace[1]:array();
			$owner=isset($trace[2])?$trace[2]:array();
			$class=isset($owner['class'])?$owner['class']:null;
			$function=isset($owner['function'])?$owner['function']:null;
			$line=isset($caller['line'])?$caller['line']:null;
			//--------------------
			$this->ini_obj->wczytajplikini("./configs/".$_SESSION['language'].".ini");
			$inival=$this->ini_obj->getvalue(get_class($this),$nr);
			if(!isset($inival) || $inival=="uzupełnić")
			{
				//jeżeli nie ma w słowniku do dopisuje
			    $this->ini_obj->addvalue(get_class($this),$nr,"$class,$function,$line,$nr");
			    $this->ini_obj->zapiszplikini("./configs/".$_SESSION['language'].".ini");
				$rettext.="Brak w słowniku";
			}
			else
				$rettext.=$inival;
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function zbierzselektory($tresc)
		{
		    $this->ini_obj->wczytajplikini("./configs/".$_SESSION['language'].".ini");
			//wyszukuje selektorów
			if(preg_match_all("/jezyk->pobierz\('([^\)]*)'(,[^\)]*)?\)/",$tresc,$wyniki))// /\('(.*?)'\)/
				if(is_array($wyniki))
					foreach($wyniki as $key=>$val)
						if(is_array($val) && $key==1)
							foreach($val as $key2=>$val2)
							{	
								//sprawdam selektor w słowniku
							    $inival=$this->ini_obj->getvalue(get_class($this),$val2);
								if(!isset($inival))
								{
									//jeżeli nie ma w słowniku do dopisuje
								    $this->ini_obj->addvalue(get_class($this),$val2,"uzupełnić");
								    $this->ini_obj->zapiszplikini("./configs/".$_SESSION['language'].".ini");
								}
							}					
		}
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>