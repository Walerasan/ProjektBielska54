<?php
if(!class_exists('uczniowie'))
{
	class uczniowie
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
			$rettext="";
			$content_text="";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					case "przywroc":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->restore($idu,$confirm);
					break;
					case "usun":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text=$this->delete($idu,$confirm);
					break;
					case "zapisz":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$idkl=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$imie=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['imie'])?$_POST['imie']:"");
						$nazwisko=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['nazwisko'])?$_POST['nazwisko']:"");
						$numer_indeksu=isset($_GET['par5'])?$_GET['par5']:(isset($_POST['numer_indeksu'])?$_POST['numer_indeksu']:"");
						$content_text=$this->add($idu,$idkl,$imie,$nazwisko,$numer_indeksu);
					break;
					case "formularz":
						$idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
						$idkl=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['idkl'])?$_POST['idkl']:0);
						$imie=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['imie'])?$_POST['imie']:"");
						$nazwisko=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['nazwisko'])?$_POST['nazwisko']:"");
						$numer_indeksu=isset($_GET['par5'])?$_GET['par5']:(isset($_POST['numer_indeksu'])?$_POST['numer_indeksu']:"");
						$content_text=$this->form($idu,$idkl,$imie,$nazwisko,$numer_indeksu);
					break;
					case "lista":
					default:
						$content_text=$this->lista();
					break;
				}
				//--------------------
				$rettext=$this->page_obj->$template_class_name->get_content($content_text);
			}
			//--------------------
			return $rettext;
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
			$wynik=$this->page_obj->database_obj->get_data("select idu,idkl,imie,nazwisko,numer_indeksu,usuniety from ".get_class($this).";");
			if($wynik)
			{
				$rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext.="<table style='width:100%;font-size:10pt;' cellspacing='0'>";
				$rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>imie, nazwisko</td>
						<td>klasa</td>
						<td>numer indeksu</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($idu,$idkl,$imie,$nazwisko,$numer_indeksu,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idu,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:15px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idu,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:15px;'/></a>";
					}
					//--------------------
					$rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idu' onmouseover=\"setopticalwhite50('wiersz$idu')\" onmouseout=\"setoptical0('wiersz$idu')\">
							<td>$lp</td>
							<td>$imie, $nazwisko</td>
							<td>{$this->page_obj->klasa->get_name($idkl)} - {$this->page_obj->oddzialy->get_name($this->page_obj->klasa->get_oddzial($idkl))}</td>
							<td>$numer_indeksu</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idu'><img src='./media/ikony/edit.png' alt='' style='height:15px;'/></a></td>
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
		public function form($idu,$idkl,$imie,$nazwisko,$numer_indeksu)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idu!="" && is_numeric($idu) && $idu>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select idkl,imie,nazwisko,numer_indeksu from ".get_class($this)." where usuniety='nie' and idu=$idu");
				if($wynik)
				{
					list($idkl,$imie,$nazwisko,$numer_indeksu)=$wynik->fetch_row();
				}
			}
			//--------------------
			$imie=$this->page_obj->text_obj->doedycji($imie);
			$nazwisko=$this->page_obj->text_obj->doedycji($nazwisko);
			$numer_indeksu=$this->page_obj->text_obj->doedycji($numer_indeksu);
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
						<div class='wiersz'><div class='formularzkom1'>Imie: </div><div class='formularzkom2'><input type='text' name='imie' value='$imie' style='width:800px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>Nazwisko: </div><div class='formularzkom2'><input type='text' name='nazwisko' value='$nazwisko' style='width:800px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>Klasa: </div><div class='formularzkom2'>{$this->create_select_field_from_klasa($idkl)}</div></div>
						<div class='wiersz'><div class='formularzkom1'>numer_indeksu: </div><div class='formularzkom2'><input type='text' name='numer_indeksu' value='$numer_indeksu' style='width:800px;'/></div></div>						
						<div class='wiersz'>
							<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
					</div>
					<input type='hidden' name='idu' value='$idu' />
				</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idu,$idkl,$imie,$nazwisko,$numer_indeksu)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$imie = $this->page_obj->text_obj->domysql($imie);
			$nazwisko = $this->page_obj->text_obj->domysql($nazwisko);
			$numer_indeksu = $this->page_obj->text_obj->domysql($numer_indeksu);			
			//--------------------
			if( ($idu != "") && is_numeric($idu) && ($idu > 0) )
			{
				$zapytanie="update ".get_class($this)." set imie='$imie',nazwisko='$nazwisko',numer_indeksu='$numer_indeksu',idkl=$idkl where idu=$idu;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(imie,nazwisko,numer_indeksu,idkl)values('$imie','$nazwisko','$numer_indeksu',$idkl)";//nowy wpis
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
					$rettext.=$this->form($idu,$idkl,$imie,$nazwisko,$numer_indeksu);
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
		public function delete($idu,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idu=$idu;"))
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
		public function restore($idu,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idu=$idu;"))
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
			$wynik=$this->page_obj->database_obj->get_data("select idu,imie,nazwisko from ".get_class($this)." where usuniety='nie';");
			if($wynik)
			{
				while(list($idu,$imie,$nazwisko)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idu, "$imie $nazwisko");
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_imie_nazwisko
		public function get_imie_nazwisko($idu)
		{
			$imie_nazwisko='';
			if($idu!="" && is_numeric($idu) && $idu>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select CONCAT(imie,' ',nazwisko) from ".get_class($this)." where usuniety='nie' and idu=$idu");
				if($wynik)
				{
					list($imie_nazwisko)=$wynik->fetch_row();
				}
			}
			return $imie_nazwisko;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region
		//----------------------------------------------------------------------------------------------------
		private function create_select_field_from_klasa($idkl)
		{
			$rettext="<select name='idkl'>";
			//--------------------
			foreach($this->page_obj->klasa->get_list() as $val)
			{
				$rettext.="<option value='$val[0]' ".($val[0]=="$idkl"?"selected='selected'":"").">{$this->page_obj->oddzialy->get_name($val[1])} - $val[2]</option>";
			}
			//--------------------
			$rettext.="</select>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region 
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy=get_class($this);
			$pola=array();
			
			//definicja tablicy
			$nazwa="idu";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idkl";
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
			
			$nazwa="imie";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="nazwisko";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="numer_indeksu";
			$pola[$nazwa][0]="varchar(20)";
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