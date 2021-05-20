<?php
if(!class_exists('klasa'))
{
	class klasa
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj=$page_obj;
			$this->definicjabazy();
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region destructor
		public function __destruct()
		{
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_content
		public function get_content()
		{
			$content_text="<p class='title'>KLASY</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					case "przywroc":
						$idkl=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->restore($idkl,$confirm);
					break;
					case "usun":
						$idkl=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->delete($idkl,$confirm);
					break;
					case "zapisz":
						$idkl=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$idod=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idod'])?$_POST['idod']:0);
						$nazwa=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$content_text.=$this->add($idkl,$idod,$nazwa);
					break;
					case "formularz":
						$idkl=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$idod=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idod'])?$_POST['idod']:0);
						$nazwa=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$content_text.=$this->form($idkl,$idod,$nazwa);
					break;
					case "lista":
					default:
						$content_text.=$this->lista();
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region lista
		public function lista()
		{
			$rettext="";
			//--------------------
			$rettext.="<button class='test' title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz\"'>Dodaj nowy</button><br />";
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idkl,idod,nazwa,usuniety from ".get_class($this).";");
			if($wynik)
			{
				$rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext.="<table style='width:100%;font-size:16px;' cellspacing='0'>";
				$rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>nazwa</td>
						<td>oddział</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($idkl,$idod,$nazwa,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idkl,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idkl,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
					}
					//--------------------
					$rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idkl' onmouseover=\"setopticalwhite50('wiersz$idkl')\" onmouseout=\"setoptical0('wiersz$idkl')\">
							<td style='text-align:right;padding-right:10px;color:#555555;'>$lp.</td>
							<td>$nazwa</td>
							<td>".$this->page_obj->oddzialy->get_name($idod)."</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idkl'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a></td>
							<td style='text-align:center;'>$operacja</td>
						</tr>";
				}
				$rettext.="</table>";
			}
			else
			{
				$rettext.="<br />Brak wpisów<br />";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region form
		public function form($idkl,$idod,$nazwa)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idkl!="" && is_numeric($idkl) && $idkl>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select nazwa,idod from ".get_class($this)." where usuniety='nie' and idkl=$idkl");
				if($wynik)
				{
					list($nazwa,$idod)=$wynik->fetch_row();
				}
			}
			//--------------------
			$nazwa=$this->page_obj->text_obj->doedycji($nazwa);
			//--------------------
			$rettext="
					<style>
						div.wiersz{float:left;clear:left;}
						div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
						div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
					</style>";
			$rettext.="
					<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz'>
						<div style='overflow:hidden;'>							
							<div class='wiersz'><div class='formularzkom1'>Nazwa: </div><div class='formularzkom2'><input type='text' name='nazwa' value='$nazwa' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Oddział: </div><div class='formularzkom2'>".$this->create_select_field_from_oddzialy($idod)."</div></div>							
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idkl' value='$idkl' />
					</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idkl,$idod,$nazwa)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$nazwa = $this->page_obj->text_obj->domysql($nazwa);			
			//--------------------
			if( ($idkl != "") && is_numeric($idkl) && ($idkl > 0) )
			{
				$zapytanie="update ".get_class($this)." set nazwa='$nazwa',idod=$idod where idkl=$idkl;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(nazwa,idod)values('$nazwa',$idod)";//nowy wpis
			}
			//--------------------
			if(!$_SESSION['antyrefresh'])
			{
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$_SESSION['antyrefresh']=true;
					$rettext.="Zapisane<br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext.="Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$this->form($idkl,$idod,$nazwa);
				}
			}
			else
			{
				$rettext.=$this->lista();
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region delete
		public function delete($idkl,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idkl=$idkl;"))
				{
					//$rettext.="<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext.="<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext.=$this->lista();
				}
			}
			else
			{
				$rettext.="This operation need confirm.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region restore
		public function restore($idkl,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idkl=$idkl;"))
				{
					//$rettext.="<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext.="<span style='font-weight:bold;color:red;'>Błąd przywracania</span><br />";
					$rettext.=$this->lista();
				}
			}
			else
			{
				$rettext.="This operation need confirm.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list
		public function get_list()
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idkl,idod,nazwa from ".get_class($this)." where usuniety='nie' order by idod;");
			if($wynik)
			{
				while(list($idkl,$idod,$nazwa)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idkl, (int)$idod, $nazwa);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list_for_idod
		public function get_list_for_idod($idod)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idkl,nazwa from ".get_class($this)." where usuniety='nie' and idod=$idod;");
			if($wynik)
			{
				while(list($idkl,$nazwa)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idkl, (int)$idod, $nazwa);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_name
		public function get_name($idkl)
		{
			$nazwa='';
			if($idkl!="" && is_numeric($idkl) && $idkl>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select nazwa from ".get_class($this)." where usuniety='nie' and idkl=$idkl");
				if($wynik)
				{
					list($nazwa)=$wynik->fetch_row();
				}
			}
			return $nazwa;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_oddzial
		public function get_oddzial($idkl)
		{
			$idod=0;
			if($idkl!="" && is_numeric($idkl) && $idkl>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idod from ".get_class($this)." where usuniety='nie' and idkl=$idkl");
				if($wynik)
				{
					list($idod)=$wynik->fetch_row();
				}
			}
			return $idod;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region create_select_field_from_oddzialy
		private function create_select_field_from_oddzialy($idod)
		{
			$rettext="<select name='idod'>";
			//--------------------
			foreach($this->page_obj->oddzialy->get_list() as $val)
			{
				$rettext.="<option value='$val[0]' ".($val[0]=="$idod"?"selected='selected'":"").">$val[1]</option>";
			}
			//--------------------
			$rettext.="</select>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy=get_class($this);
			$pola=array();
			
			//definicja tablicy
			$nazwa="idkl";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idod";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie','zablokowany')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="nazwa";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
						
			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//--------------------			
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>