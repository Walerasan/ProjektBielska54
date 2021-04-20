<?php
if(!class_exists('oplaty'))
{
	class oplaty
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
						$idop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->restore($idop,$confirm);
					break;
					case "usun":
						$idop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->delete($idop,$confirm);
					break;
					case "zapisz":
						$idop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$idto=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idto'])?$_POST['idto']:0);
						$nazwa=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$kwota=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['kwota'])?$_POST['kwota']:"");
						$content_text=$this->add($idop,$idto,$nazwa,$kwota);
					break;
					case "formularz":
						$idop=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idop'])?$_POST['idop']:0);
						$idto=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idto'])?$_POST['idto']:0);
						$nazwa=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwa'])?$_POST['nazwa']:"");
						$kwota=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['kwota'])?$_POST['kwota']:"");
						$content_text=$this->form($idop,$idto,$nazwa,$kwota);
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
			$wynik=$this->page_obj->database_obj->get_data("select idop,idto,nazwa,kwota,usuniety from ".get_class($this).";");
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
				while(list($idop,$idto,$nazwa,$kwota,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idop,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:15px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idop,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:15px;'/></a>";
					}
					//--------------------
					$rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idop' onmouseover=\"setopticalwhite50('wiersz$idop')\" onmouseout=\"setoptical0('wiersz$idop')\">
							<td>$lp</td>
							<td>$nazwa</td>
							<td>$kwota</td>
							<td>{$this->page_obj->typy_oplat->get_name($idto)}</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idop'><img src='./media/ikony/edit.png' alt='' style='height:15px;'/></a></td>
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
		public function form($idop,$idto,$nazwa,$kwota)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idto,nazwa,kwota from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($idto,$nazwa,$kwota)=$wynik->fetch_row();
				}
			}
			//--------------------
			$nazwa=$this->page_obj->text_obj->doedycji($nazwa);
			$kwota=$this->page_obj->text_obj->doedycji($kwota);
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
							<div class='wiersz'><div class='formularzkom1'>Typ opłaty: </div><div class='formularzkom2'>{$this->create_select_field_from_typy_oplat($idto)}</div></div>
							<div class='wiersz'><div class='formularzkom1'>Kwota: </div><div class='formularzkom2'><input type='text' name='kwota' value='$kwota' style='width:800px;'/></div></div>							
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idop' value='$idop' />
					</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idop,$idto,$nazwa,$kwota)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$nazwa = $this->page_obj->text_obj->domysql($nazwa);
			$kwota = $this->page_obj->text_obj->domysql($kwota);
			//--------------------
			if( ($idop != "") && is_numeric($idop) && ($idop > 0) )
			{
				$zapytanie="update ".get_class($this)." set nazwa='$nazwa', kwota=$kwota, idto=$idto where idop=$idop;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(nazwa,kwota,idto)values('$nazwa',$kwota,$idto)";//nowy wpis
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
					$rettext.=$this->form($idop,$idto,$nazwa,$kwota);
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
		public function delete($idop,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idop=$idop;"))
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
		public function restore($idop,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idop=$idop;"))
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
		#region create_select_field_from_typy_oplat
		private function create_select_field_from_typy_oplat($idto)
		{
			$rettext="<select name='idto'>";
			//--------------------
			foreach($this->page_obj->typy_oplat->get_list() as $val)
			{
				$rettext.="<option value='$val[0]' ".($val[0]=="$idto"?"selected='selected'":"").">$val[1]</option>";
			}
			//--------------------
			$rettext.="</select>";
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
			$wynik=$this->page_obj->database_obj->get_data("select idop,idto,nazwa,kwota from ".get_class($this)." where usuniety='nie';");
			if($wynik)
			{
				while(list($idop,$idto,$nazwa,$kwota)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idop,(int)$idto,$nazwa,(float)$kwota);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_name
		public function get_name($idop)
		{
			$nazwa='';
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select nazwa from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($nazwa)=$wynik->fetch_row();
				}
			}
			return $nazwa;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_kwota
		public function get_kwota($idop)
		{
			$kwota=NAN;
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select kwota from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($kwota)=$wynik->fetch_row();
				}
			}
			return $kwota;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idto
		public function get_idto($idop)
		{
			$idto=0;
			if($idop!="" && is_numeric($idop) && $idop>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idto from ".get_class($this)." where usuniety='nie' and idop=$idop");
				if($wynik)
				{
					list($idto)=$wynik->fetch_row();
				}
			}
			return $idto;
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
			$nazwa="idop";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idto";
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
			$pola[$nazwa][0]="varchar(150)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="kwota";
			$pola[$nazwa][0]="decimal(5,2)";
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