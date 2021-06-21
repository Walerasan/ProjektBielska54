<?php
if(!class_exists('konta_bankowe'))
{
	class konta_bankowe
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
			$content_text="";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					case "przywroc":
						$idk=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idk'])?$_POST['idk']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->restore($idk,$confirm);
					break;
					case "usun":
						$idk=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idk'])?$_POST['idk']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->delete($idk,$confirm);
					break;
					case "zapisz":
						$idk=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idk'])?$_POST['idk']:0);
						$numer_konta=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['numer_konta'])?$_POST['numer_konta']:"");
						$content_text=$this->add($idk,$numer_konta);
					break;
					case "formularz":
						$idk=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idk'])?$_POST['idk']:0);
						$numer_konta=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['numer_konta'])?$_POST['numer_konta']:"");
						$content_text=$this->form($idk,$numer_konta);
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
			$rettext.="<button title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz\"'>Dodaj nowy</button><br />";
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idk,numer_konta,usuniety from ".get_class($this).";");
			if($wynik)
			{
				$rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext.="<table style='width:100%;font-size:10pt;' cellspacing='0'>";
				$rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>numer konta</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($idk,$numer_konta,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idk,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:15px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idk,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:15px;'/></a>";
					}
					//--------------------
					$rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idk' onmouseover=\"setopticalwhite50('wiersz$idk')\" onmouseout=\"setoptical0('wiersz$idk')\">
							<td>$lp</td>
							<td>$numer_konta</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idk'><img src='./media/ikony/edit.png' alt='' style='height:15px;'/></a></td>
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
		public function form($idk,$numer_konta)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idk!="" && is_numeric($idk) && $idk>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select numer_konta from ".get_class($this)." where usuniety='nie' and idk=$idk");
				if($wynik)
				{
					list($numer_konta)=$wynik->fetch_row();
				}
			}
			//--------------------
			$numer_konta=$this->page_obj->text_obj->doedycji($numer_konta);
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
							<div class='wiersz'><div class='formularzkom1'>Numer konta: </div><div class='formularzkom2'><input type='text' name='numer_konta' value='$numer_konta' style='width:800px;'/></div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idk' value='$idk' />
					</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idk,$numer_konta)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$numer_konta = $this->page_obj->text_obj->domysql($numer_konta);
			//--------------------
			if( ($idk != "") && is_numeric($idk) && ($idk > 0) )
			{
				$zapytanie="update ".get_class($this)." set numer_konta='$numer_konta' where idk=$idk;";//poprawa wpisu
			}
			else
			{
				$zapytanie = "insert into ".get_class($this)."(numer_konta)values('$numer_konta')";//nowy wpis
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
					$rettext.=$this->form($idk,$numer_konta);
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
		public function delete($idk,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idk=$idk;"))
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
		public function restore($idk,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idk=$idk;"))
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
			$wynik=$this->page_obj->database_obj->get_data("select idk,numer_konta from ".get_class($this)." where usuniety='nie' order by idod;");
			if($wynik)
			{
				while(list($idk,$numer_konta)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idk, $numer_konta);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_numer_konta
		public function get_numer_konta($idk)
		{
			$numer_konta='';
			if($idk!="" && is_numeric($idk) && $idk>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select numer_konta from ".get_class($this)." where usuniety='nie' and idk=$idk");
				if($wynik)
				{
					list($numer_konta)=$wynik->fetch_row();
				}
			}
			return $numer_konta;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idk_konta
		public function get_idk_konta($numer_konta)
		{
			$idk = -1;
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idk from ".get_class($this)." where numer_konta='$numer_konta';");
			if($wynik)
			{
				list($idk)=$wynik->fetch_row();
			}
			else
			{
				if($this->page_obj->database_obj->execute_query("insert into ".get_class($this)."(numer_konta)values('$numer_konta');"))
				{
					$idk = $this->page_obj->database_obj->last_id();
				}
				
			}
			//--------------------
			return $idk;
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
			$nazwa="idk";
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
			
			$nazwa="numer_konta";
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