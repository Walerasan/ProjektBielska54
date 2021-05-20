<?php
if(!class_exists('opiekunowie'))
{
	class opiekunowie
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
			$content_text="<p class='title'>OPIEKUNOWIE</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					case "przywroc":
						$ido=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['ido'])?$_POST['ido']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->restore($ido,$confirm);
					break;
					case "usun":
						$ido=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['ido'])?$_POST['ido']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->delete($ido,$confirm);
					break;
					case "zapisz":
						$ido=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['ido'])?$_POST['ido']:0);
						$imie_opiekun=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['imie_opiekun'])?$_POST['imie_opiekun']:"");
						$nazwisko_opiekun=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwisko_opiekun'])?$_POST['nazwisko_opiekun']:"");
						$telefon_opiekun=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['telefon_opiekun'])?$_POST['telefon_opiekun']:"");
						$email_opiekun=isset($_GET['par5'])?$_GET['par5']:(isset($_POST['email_opiekun'])?$_POST['email_opiekun']:"");
						$content_text.=$this->add($ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun);
					break;
					case "formularz":
						$ido=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['ido'])?$_POST['ido']:0);
						$imie_opiekun=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['imie_opiekun'])?$_POST['imie_opiekun']:"");
						$nazwisko_opiekun=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwisko_opiekun'])?$_POST['nazwisko_opiekun']:"");
						$telefon_opiekun=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['telefon_opiekun'])?$_POST['telefon_opiekun']:"");
						$email_opiekun=isset($_GET['par5'])?$_GET['par5']:(isset($_POST['email_opiekun'])?$_POST['email_opiekun']:"");
						$content_text.=$this->form($ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun);
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
			$wynik=$this->page_obj->database_obj->get_data("select ido,imie_opiekun,nazwisko_opiekun,telefon_opiekun,email_opiekun,usuniety from ".get_class($this).";");
			if($wynik)
			{
				$rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext.="<table style='width:100%;font-size:16px;' cellspacing='0'>";
				$rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>nazwa</td>
						<td>telefon</td>
						<td>e-mail</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$ido,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$ido,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
					}
					//--------------------
					$rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$ido' onmouseover=\"setopticalwhite50('wiersz$ido')\" onmouseout=\"setoptical0('wiersz$ido')\">
							<td style='text-align:right;padding-right:10px;color:#555555;'>$lp.</td>
							<td>$imie_opiekun,$nazwisko_opiekun</td>
							<td>$telefon_opiekun</td>
							<td>$email_opiekun</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$ido'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a></td>
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
		public function form($ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($ido!="" && is_numeric($ido) && $ido>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select imie_opiekun,nazwisko_opiekun,telefon_opiekun,email_opiekun from ".get_class($this)." where usuniety='nie' and ido=$ido");
				if($wynik)
				{
					list($imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun)=$wynik->fetch_row();
				}
			}
			//--------------------
			$imie_opiekun=$this->page_obj->text_obj->doedycji($imie_opiekun);
			$nazwisko_opiekun=$this->page_obj->text_obj->doedycji($nazwisko_opiekun);
			$telefon_opiekun=$this->page_obj->text_obj->doedycji($telefon_opiekun);
			$email_opiekun=$this->page_obj->text_obj->doedycji($email_opiekun);
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
						{$this->pola_formularza($imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun)}
						<div class='wiersz'>
							<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
					</div>
					<input type='hidden' name='ido' value='$ido' />
				</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$imie_opiekun = $this->page_obj->text_obj->domysql($imie_opiekun);
			$nazwisko_opiekun = $this->page_obj->text_obj->domysql($nazwisko_opiekun);
			$telefon_opiekun = $this->page_obj->text_obj->domysql($telefon_opiekun);
			$email_opiekun = $this->page_obj->text_obj->domysql($email_opiekun);
			//--------------------
			if( ($ido != "") && is_numeric($ido) && ($ido > 0) )
			{
				$zapytanie="update ".get_class($this)." set imie_opiekun='$imie_opiekun',nazwisko_opiekun='$nazwisko_opiekun',telefon_opiekun='$telefon_opiekun',email_opiekun='$email_opiekun' where ido=$ido;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(imie_opiekun,nazwisko_opiekun,telefon_opiekun,email_opiekun)values('$imie_opiekun','$nazwisko_opiekun','$telefon_opiekun','$email_opiekun')";//nowy wpis
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
					$rettext.=$this->form($ido,$imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun);
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
		public function delete($ido,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where ido=$ido;"))
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
		public function restore($ido,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where ido=$ido;"))
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
			$wynik=$this->page_obj->database_obj->get_data("select ido,imie_opiekun,nazwisko_opiekun from ".get_class($this)." where usuniety='nie';");
			if($wynik)
			{
				while(list($ido,$imie_opiekun,$nazwisko_opiekun)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$ido, "$imie_opiekun $nazwisko_opiekun");
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_imie_opiekun_nazwisko_opiekun
		public function get_imie_opiekun_nazwisko_opiekun($ido)
		{
			$imie_opiekun_nazwisko_opiekun='';
			if($ido!="" && is_numeric($ido) && $ido>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select CONCAT(imie_opiekun,' ',nazwisko_opiekun) from ".get_class($this)." where usuniety='nie' and ido=$ido");
				if($wynik)
				{
					list($imie_opiekun_nazwisko_opiekun)=$wynik->fetch_row();
				}
			}
			return $imie_opiekun_nazwisko_opiekun;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_telefon_opiekun
		public function get_telefon_opiekun($ido)
		{
			$telefon_opiekun='';
			if($ido!="" && is_numeric($ido) && $ido>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select telefon_opiekun from ".get_class($this)." where usuniety='nie' and ido=$ido");
				if($wynik)
				{
					list($telefon_opiekun)=$wynik->fetch_row();
				}
			}
			return $telefon_opiekun;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_email_opiekun
		public function get_email_opiekun($ido)
		{
			$email_opiekun='';
			if($ido!="" && is_numeric($ido) && $ido>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select email_opiekun from ".get_class($this)." where usuniety='nie' and ido=$ido");
				if($wynik)
				{
					list($email_opiekun)=$wynik->fetch_row();
				}
			}
			return $email_opiekun;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region pola_formularza
		public function pola_formularza($imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun)
		{
			$rettext="
						<div class='wiersz'><div class='formularzkom1'>imie: </div><div class='formularzkom2'><input type='text' name='imie_opiekun' value='$imie_opiekun' style='width:800px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>nazwisko: </div><div class='formularzkom2'><input type='text' name='nazwisko_opiekun' value='$nazwisko_opiekun' style='width:800px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>telefon: </div><div class='formularzkom2'><input type='text' name='telefon_opiekun' value='$telefon_opiekun' style='width:800px;'/></div></div>
						<div class='wiersz'><div class='formularzkom1'>e-mail: </div><div class='formularzkom2'><input type='text' name='email_opiekun' value='$email_opiekun' style='width:800px;'/></div></div>
					";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		public function insert($imie_opiekun,$nazwisko_opiekun,$telefon_opiekun,$email_opiekun)
		{
			$imie_opiekun = $this->page_obj->text_obj->domysql($imie_opiekun);
			$nazwisko_opiekun = $this->page_obj->text_obj->domysql($nazwisko_opiekun);
			$telefon_opiekun = $this->page_obj->text_obj->domysql($telefon_opiekun);
			$email_opiekun = $this->page_obj->text_obj->domysql($email_opiekun);
			//--------------------
			$zapytanie="insert into ".get_class($this)."(imie_opiekun,nazwisko_opiekun,telefon_opiekun,email_opiekun)values('$imie_opiekun','$nazwisko_opiekun','$telefon_opiekun','$email_opiekun')";//nowy wpis
			if($this->page_obj->database_obj->execute_query($zapytanie))
			{
				return $this->page_obj->database_obj->last_id();
			}
			return 0;
		}
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy=get_class($this);
			$pola=array();
			
			//definicja tablicy
			$nazwa="ido";
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
			
			$nazwa="imie_opiekun";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="nazwisko_opiekun";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="telefon_opiekun";
			$pola[$nazwa][0]="varchar(20)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="email_opiekun";
			$pola[$nazwa][0]="varchar(100)";
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