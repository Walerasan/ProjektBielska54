<?php
if(!class_exists('uczniowie_oplaty'))
{
	class uczniowie_oplaty
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
		#region get_cntent
		public function get_content()
		{
			$content_text="";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					case "przywroc":
						$iduop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['iduop'])?$_POST['iduop']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->restore($iduop,$confirm);
					break;
					case "usun":
						$iduop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['iduop'])?$_POST['iduop']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->delete($iduop,$confirm);
					break;
					case "zapisz":
						$iduop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['iduop'])?$_POST['iduop']:0);
						$idu=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idu'])?$_POST['idu']:0);
						$rabat_kwota=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['rabat_kwota'])?$_POST['rabat_kwota']:"");
						$rabat_nazwa=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['rabat_nazwa'])?$_POST['rabat_nazwa']:"");
						$content_text=$this->add($iduop,$idu,$rabat_kwota,$rabat_nazwa);
					break;
					case "formularz":
						$iduop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['iduop'])?$_POST['iduop']:0);
						$rabat_kwota=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['rabat_kwota'])?$_POST['rabat_kwota']:"");
						$rabat_nazwa=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['rabat_nazwa'])?$_POST['rabat_nazwa']:"");
						$content_text=$this->form($iduop,$rabat_kwota,$rabat_nazwa);
					break;
					case "lista":
					default:
						$content_text=$this->lista();
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
			$rettext .= "<button title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz\"'>Dodaj nowy</button><br />";
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select iduop,rabat_kwota,rabat_nazwa,usuniety from ".get_class($this).";");
			if($wynik)
			{
				$rettext .= "<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext .= "<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext .= "<table style='width:100%;font-size:10pt;' cellspacing='0'>";
				$rettext .= "
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>rabat nazwa</td>
						<td>rabat kwota</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($iduop,$rabat_kwota,$rabat_nazwa,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$iduop,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:15px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$iduop,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:15px;'/></a>";
					}
					//--------------------
					$rettext .= "
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$iduop' onmouseover=\"setopticalwhite50('wiersz$iduop')\" onmouseout=\"setoptical0('wiersz$iduop')\">
							<td>$lp</td>
							<td>$rabat_nazwa</td>
							<td>$rabat_kwota</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$iduop'><img src='./media/ikony/edit.png' alt='' style='height:15px;'/></a></td>
							<td style='text-align:center;'>$operacja</td>
						</tr>";
				}
				$rettext .= "</table>";
			}
			else
			{
				$rettext .= "<br />Brak wpisów<br />";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region form
		public function form($iduop,$rabat_kwota,$rabat_nazwa)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($iduop!="" && is_numeric($iduop) && $iduop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idu,rabat_kwota,rabat_nazwa from ".get_class($this)." where usuniety='nie' and iduop=$iduop");
				if($wynik)
				{
					list($idu,$rabat_kwota,$rabat_nazwa)=$wynik->fetch_row();
				}
			}
			//--------------------
			$rabat_kwota=$this->page_obj->text_obj->doedycji($rabat_kwota);
			$rabat_nazwa=$this->page_obj->text_obj->doedycji($rabat_nazwa);
			//--------------------
			$rettext .= "
				<style>
					div.wiersz{float:left;clear:left;}
					div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
					div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
				</style>";
			$rettext .= "
				<form method='post' action='".get_class($this).",{$this->page_obj->template},zapisz'>
					<div style='overflow:hidden;'>
						<div class='wiersz'><div class='formularzkom1'>rabat nazwa: </div><div class='formularzkom2'><input type='text' name='rabat_nazwa' value='$rabat_nazwa' style='width:800px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>rabat kwota: </div><div class='formularzkom2'><input type='text' name='rabat_kwota' value='$rabat_kwota' style='width:100px;'/></div></div>
						<div class='wiersz'>
							<div class='formularzkom1'>&#160;</div>
							<div class='formularzkom2'>
								<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
								<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"uczniowie,{$this->page_obj->template},szczegoly,$idu\"'>Anuluj</button>
							</div>
						</div>
					</div>
					<input type='hidden' name='iduop' value='$iduop' />
					<input type='hidden' name='idu' value='$idu' />
				</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($iduop,$idu,$rabat_kwota,$rabat_nazwa)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$rabat_kwota = $this->page_obj->text_obj->domysql($rabat_kwota);
			$rabat_nazwa = $this->page_obj->text_obj->domysql($rabat_nazwa);
			//--------------------
			if( ($iduop != "") && is_numeric($iduop) && ($iduop > 0) )
			{
				$zapytanie="update ".get_class($this)." set rabat_nazwa='$rabat_nazwa',rabat_kwota=$rabat_kwota where iduop=$iduop;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(rabat_nazwa,rabat_kwota,idu,idop)values('$rabat_nazwa',$rabat_kwota,0,0)";//nowy wpis
			}
			//--------------------
			if(!$_SESSION['antyrefresh'])
			{
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$_SESSION['antyrefresh']=true;
					//$rettext .= "Zapisane<br />";
					//$rettext.=$this->lista();
					$rettext .= "<script>window.location='uczniowie,{$this->page_obj->template},szczegoly,$idu';</script>";
					//go to uczniowie,index,szczegoly,1
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					$rettext.=$zapytanie."<br />";
					$rettext.=$this->form($iduop,$rabat_kwota,$rabat_nazwa);
				}
			}
			else
			{
				//$rettext.=$this->lista();
				//go to uczniowie,index,szczegoly,1
				$rettext .= "<script>window.location='uczniowie,{$this->page_obj->template},szczegoly,$idu';</script>";
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region delete
		public function delete($iduop,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where iduop=$iduop;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd usuwania</span><br />";
					$rettext.=$this->lista();
				}
			}
			else
			{
				$rettext .= "This operation need confirm.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region restore
		public function restore($iduop,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where iduop=$iduop;"))
				{
					//$rettext .= "<span style='font-weight:bold;color:green;'>Pozycja została usunięta</span><br />";
					$rettext.=$this->lista();
				}
				else
				{
					$rettext .= "<span style='font-weight:bold;color:red;'>Błąd przywracania</span><br />";
					$rettext.=$this->lista();
				}
			}
			else
			{
				$rettext .= "This operation need confirm.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region mark_delete
		public function mark_delete($idop)
		{
			//don't delete with status == oplacone
			#region execute
			return $this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idop=$idop and status<>'oplacone';");
			#endregion
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region synchronize
		public function synchronize($idop,$idu)
		{
			#region safe
			if( (!isset($idop)) || ($idop == "") ) $idop = 0;
			if( (!isset($idu)) || ($idu == "") ) $idu = 0;
			$deleted = "empty";
			$sql_query = "";
			$rettext = "";
			#endregion

			#region select
			$wynik=$this->page_obj->database_obj->get_data("select iduop,usuniety,status from ".get_class($this)." where idop=$idop and idu=$idu;");
			if($wynik)
			{
				list($iduop,$deleted,$status)=$wynik->fetch_row();
			}
			#endregion

			#region switch
			switch($deleted)
			{
				case "empty":
					$sql_query = "insert into ".get_class($this)."(rabat_nazwa,rabat_kwota,idu,idop)values('',0,$idu,$idop)";
					break;
				case "tak":
					$sql_query = "update ".get_class($this)." set usuniety='nie' where iduop=$iduop and idu=$idu;";//poprawa wpisu
					break;
				case "nie":
					// nothing to do here
					break;
			}
			#endregion

			#region execute
			if( (!$_SESSION['antyrefresh']) && ($sql_query != "") )
			{
				if($this->page_obj->database_obj->execute_query($sql_query))
				{
					//$rettext.=$sql_query."<br />";
				}
				else
				{
					$rettext .= "Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					//$rettext.=$sql_query."<br />";
				}
			}
			#endregion

			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list
		public function get_idu_list($idop)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where idop=$idop and usuniety='nie';");
			if($wynik)
			{
				while(list($idu)=$wynik->fetch_row())
				{
					$rettext[] = (int)$idu;
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_liste_oplat_dla_ucznia
		public function get_liste_oplat_dla_ucznia($idu)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select iduop,idop,rabat_kwota,rabat_nazwa from ".get_class($this)." where idu=$idu and usuniety='nie';");
			if($wynik)
			{
				while(list($iduop,$idop,$rabat_kwota,$rabat_nazwa)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$iduop,(int)$idop, $rabat_kwota, $rabat_nazwa);
				}
			}
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
			$nazwa="iduop";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie','zablokowany')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idu";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idop";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="rabat_kwota";
			$pola[$nazwa][0]="decimal(5,2)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="rabat_nazwa";
			$pola[$nazwa][0]="varchar(150)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="status";
			$pola[$nazwa][0]="enum('nowe','powiadomiono','oplacone')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nowe'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>