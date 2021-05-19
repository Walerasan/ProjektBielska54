<?php
if(!class_exists('wyciagi'))
{
	class wyciagi
	{
		var $page_obj;
		var $katalog;//katalog do uploud dokumentów do przetwarzania
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj=$page_obj;
			$this->definicjabazy();
			$this->katalog=$page_obj->create_directory("./media/filehtml",debug_backtrace());
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
			$content_text="<p class='title'>WYCIAGI BANKOWE</p>";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if( ($this->page_obj->template=="admin") || ($this->page_obj->template=="index") )
			{
				switch($this->page_obj->target)
				{
					case "dodajplik":
						if(isset($_FILES['filehtml']) && !empty($_FILES['filehtml'])){
							$content_text.=$this->uploadfile($_FILES['filehtml']);
						}
					break;
					case "przetwarzanie":
						$content_text.=$this->processingfile();
					break;
					case "przywroc":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->restore($idw,$confirm);
					break;
					case "usun":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
						$content_text.=$this->delete($idw,$confirm);
					break;
					case "zapisz":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$tytul=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['tytul'])?$_POST['tytul']:"");
						$data=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['data'])?$_POST['data']:"");
						$typ=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['typ'])?$_POST['typ']:"");
						$content_text.=$this->add($idw,$tytul,$data,$typ);
					break;
					case "formularz":
						$idw=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idw'])?$_POST['idw']:0);
						$tytul=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['tytul'])?$_POST['tytul']:"");
						$data=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['data'])?$_POST['data']:"");
						$typ=isset($_GET['par4'])?$_GET['par4']:(isset($_POST['typ'])?$_POST['typ']:"");
						$content_text.=$this->form($idw,$tytul,$data,$typ);
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
			$wynik=$this->page_obj->database_obj->get_data("select idw,tytul,data,typ,usuniety from ".get_class($this).";");
			if($wynik)
			{
				$rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
				$rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";
				$rettext.="<table style='width:100%;font-size:16px;' cellspacing='0'>";
				$rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>
						<td>numer konta</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>
					</tr>";
				$lp=0;
				while(list($idw,$tytul,$data,$typ,$usuniety)=$wynik->fetch_row())
				{
					$lp++;
					//--------------------
					if($usuniety=='nie')
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno usunąć?\",\"".get_class($this).",{$this->page_obj->template},usun,$idw,yes\",window)'><img src='./media/ikony/del.png' alt='' style='height:30px;'/></a>";
					}
					else
					{
						$operacja="<a href='javascript:potwierdzenie(\"Czy napewno przywrócić?\",\"".get_class($this).",{$this->page_obj->template},przywroc,$idw,yes\",window)'><img src='./media/ikony/restore.png' alt='' style='height:30px;'/></a>";
					}
					//--------------------
					$rettext.="
						<tr style='".($usuniety=='tak'?"text-decoration:line-through;color:gray;":"")."' id='wiersz$idw' onmouseover=\"setopticalwhite50('wiersz$idw')\" onmouseout=\"setoptical0('wiersz$idw')\">
							<td style='text-align:right;padding-right:10px;color:#555555;'>$lp.</td>
							<td>$tytul</td>
							<td>$data</td>
							<td>$typ</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idw'><img src='./media/ikony/edit.png' alt='' style='height:30px;'/></a></td>
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
		public function form($idw,$tytul,$data,$typ)
		{
			$rettext="";
			//--------------------
			$_SESSION['antyrefresh']=false;
			//--------------------
			if($idw!="" && is_numeric($idw) && $idw>0)
			{
				$wynik=$this->page_obj->database_obj->get_data("select tytul,data,typ from ".get_class($this)." where usuniety='nie' and idw=$idw");
				if($wynik)
				{
					list($tytul,$data,$typ)=$wynik->fetch_row();
				}
			}
			//--------------------
			$tytul=$this->page_obj->text_obj->doedycji($tytul);
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
							<div class='wiersz'><div class='formularzkom1'>Tytuł: </div><div class='formularzkom2'><input type='text' name='tytul' value='$tytul' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Data: </div><div class='formularzkom2'><input type='text' name='data' value='$data' style='width:800px;'/></div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>Typ: </div>
								<div class='formularzkom2'>
									<select name='typ' value='$typ' style='width:800px;'>
										<option value='bankowy'>bankowy</option>
										<option value='reczny'>ręczny</option>
										<option value='inny'>inny</option>
									</select>
								</div>
							</div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
									<button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idw' value='$idw' />
					</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region add
		public function add($idw,$tytul,$data,$typ)
		{
			$rettext = "";
			//--------------------
			// zabezpieczam dane
			//--------------------
			$tytul = $this->page_obj->text_obj->domysql($tytul);
			//--------------------
			if( ($idw != "") && is_numeric($idw) && ($idw > 0) )
			{
				$zapytanie="update ".get_class($this)." set tytul='$tytul', data='$data', typ='$typ' where idw=$idw;";//poprawa wpisu
			}
			else
			{
				$zapytanie="insert into ".get_class($this)."(tytul,data,typ)values('$tytul','$data','$typ')";//nowy wpis
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
					$rettext.=$this->form($idw,$tytul,$data,$typ);
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
		public function delete($idw,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idw=$idw;"))
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
		public function restore($idw,$confirm)
		{
			$rettext="";
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where idw=$idw;"))
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
		//----------------------------------------------------------------------------------------------------
		#region input file to processing 
		public function processingfile()
		{
			$rettext="";
			//--------------------
			$rettext.="<h3>Przetwarzanie pliku HTML</h3><br>";
			$rettext.="<form method='post' action='".get_class($this).",{$this->page_obj->template},dodajplik' enctype='multipart/form-data'>";
			$rettext.="Pobierz HTML: <input type='file' name='filehtml'>";
			$rettext.="<br><input type='submit' name='submit' value='ZAŁADUJ HTML'>";
			$rettext.="</form>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region processing from HTML to SQL db
		public function przetwarzanie_htmlToSql($file)
		{
			$rettext="";
			$rettext.="<hr>";
        	$rettext.="<br>pobieram nazwę pliku: ".$file."<br>";
			include_once("./media/filehtml/$file");
        	//echo("<script>alert('test skryptu');</script>");
			$rettext.="<script src='./js/przetwarzanie.js'></script>";
        	$rettext.="<hr>";
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region uploadfile
		public function uploadfile($file)
		{
			$rettext="";
			//--------------------
			$target_dir = "./media/filehtml/";
			$target_file = $target_dir . basename($file["name"]);
			$rettext.="<hr>Nazwa dokumentu: ".basename($file["name"])."<hr>";

			$uploadOk = 1;
			$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));     
			// Sprawdzam czy istnieje już plik o tej samej nazwie
			if (file_exists($target_file)) {
				$rettext.="<h5 class='warnigs'>Plik istnieje o podanej nazwie.</h5>";
				$uploadOk = 0;
			}
			// Sprawdzam rozmiar pliku
			if ($file["size"] > 500000) {
				$rettext.="<h5 class='warnigs'>Plik jest za duży......</h5>";
				$uploadOk = 0;
			}
			// Przetwarzam tylko pliki o rozszerzeniu html
			if($FileType != "html") {
				$rettext.="<h5 class='warnigs'>Przetwarzanie tylko dla plików o rozszerzeniu html.....</h5>";
				$uploadOk = 0;
			}
			// Sprawdzam jeżeli $uploadOk 1 to ok a jeżeli 0 to error
			if ($uploadOk == 0) {
				$rettext.="<h5 class='warnigs'>Nie można przesłać pliku.</h5>";
			} else {
				if (move_uploaded_file($file["tmp_name"], $target_file)) {
					$rettext.="Plik ". htmlspecialchars( basename( $file["name"])). " został przesłany.";
					$plik = htmlspecialchars( basename( $file["name"]));
					//uruchamiam funkcje do przetwarzania skryptu JS do dalszych operacji
					$this->przetwarzanie_htmlToSql($plik);
				//--------------------------------------------------------------------
				} else {
					$rettext.="<h5 class='warnigs'>błąd przesłania pliku na serwer.</h5>";
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
			$nazwa="idw";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="id_nr_konta";
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
			
			$nazwa="tytul";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="rachuneknadawcy";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="adresnadawcy";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="dataoperacji";
			$pola[$nazwa][0]="timestamp";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="kwota";
			$pola[$nazwa][0]="decimal";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="0";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="opistransakcji";
			$pola[$nazwa][0]="varchar(255)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="typ";
			$pola[$nazwa][0]="enum('bankowy','reczny','inny')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'inny'";//default
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