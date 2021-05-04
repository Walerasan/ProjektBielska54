<?php
if(!class_exists('users'))
{
	class users
	{
		var $message;
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		public function __construct($page_obj) 
		{
			$this->page_obj=$page_obj;
		    $this->definicjabazy();
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content()
		{
		    $rettext="";
		    $template_class_name=$this->page_obj->template."_template";
		    //--------------------
		    if( ($this->page_obj->template=="admin") && ($this->is_login()) )
		    {	
		        switch($this->page_obj->target)
		        {
		            case "przywroc":
                        $idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
                        $confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->restore($idu,$confirm));
                        break;
                    case "usun":
                        $idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
                        $confirm=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['confirm'])?$_POST['confirm']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->delete($idu,$confirm));
                        break;
                    case "zapisz":
                        $idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);                        
                        $imie=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['imie'])?$_POST['imie']:"");
						$nazwisko=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwisko'])?$_POST['nazwisko']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->add($idu,$imie,$nazwisko));                        
                        break;
                    case "formularz":                                                
                        $idu=isset($_GET['par1'])?$_GET['par1']:(isset($_POST['idu'])?$_POST['idu']:0);
                        $imie=isset($_GET['par2'])?$_GET['par2']:(isset($_POST['imie'])?$_POST['imie']:"");
						$nazwisko=isset($_GET['par3'])?$_GET['par3']:(isset($_POST['nazwisko'])?$_POST['nazwisko']:"");
                        $rettext=$this->page_obj->$template_class_name->get_content($this->formularz($idu,$imie,$nazwisko));
                        break;
                    case "lista":
                    default:
                        $rettext=$this->page_obj->$template_class_name->get_content($this->lista());
                        break;
		        }
			}
			//--------------------
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function form()
		{
			$rettext="";
			//--------------------
			if(!$this->is_login())
				$rettext.="<form method='post' action='".get_class($this).",".$this->silnik->szablon.",zaloguj'>
										<input type='text' name='r_login' value='login' class='polelogin' onclick='this.value==\"login\"?this.value=\"\":null'/>&#160;&#160;&#160;
										<input type='password' name='r_haslo' value='hasło' class='polehasla' onclick='this.value==\"hasło\"?this.value=\"\":null'/>&#160;&#160;&#160; 
										<input type='submit' name='' value='zaloguj' class='przyciskzaloguj'/>
									</form>";
			else
				$rettext.="<button title='Wyloguj' type='button' onclick='window.location=\"admin,".$this->silnik->szablon.",wyloguj\"' class='przyciskwyloguj' >Wyloguj</button>";
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function is_login()
		{
			//jezeli jest zalogowany uzytkownik zwraca true jezeli nie zwraca false
			$r_idu=isset($_SESSION['r_idu'])?$_SESSION['r_idu']:'';
		  if($r_idu!=-1 && $r_idu!="")
		  	return true;
		  else
		  	return false;
		}
		//----------------------------------------------------------------------------------------------------
		public function login($page_obj,$r_login,$r_haslo)
		{
		  //zabespieczenie przed cudzoslowiami
		  $r_login=str_replace("'","&quot;",$r_login);
		  $r_login=str_replace("\"","&quot;",$r_login);
		  $r_haslo=str_replace("'","&quot;",$r_haslo);
		  $r_haslo=str_replace("\"","&quot;",$r_haslo);
			// 	funkcja dokonuje loginu przez ustawienie w cookie r_access na oki oraz w sesji r_idu na numer id uzytkownika
			//	funckja zwraca
			// 	-3 jezeli jakis user nie posiada hasla
			//	-2 jezeli jakis user jest zalogowany
			//	-1 jezeli wystapil blad loginu do bazy /polaczenia z silnik->baza/
			//	0  jezeli blad loginu badz hasla
			//	1 gdy wszystko oki i zostal zalogowany
			$r_access=isset($_COOKIE['r_access'])?$_COOKIE['r_access']:'';
			$r_idu=isset($_SESSION['r_idu'])?$_SESSION['r_idu']:'';
			if($r_access=="" || $r_idu==-1 || $r_idu=="")$r_access="no";
			if($this->is_login())
				return -2;
				$r_MySqlLogin=$page_obj->database_obj->get_handle();
			if(!$r_MySqlLogin)
				return -1;
			//mysql_select_db("presinfo");
			//pobranie hasla uzytkownika
				$r_wynik=$r_MySqlLogin->query("select idu,haslo from ".get_class($this)." where login='$r_login';");
			//jezeli puste zglosic info o zmiane hasla jezeli brak to wylot na zewnatrz
			if($r_access=="no" && $r_MySqlLogin->affected_rows!=1)
			{
				return 0;
			}
			else
			{
			    list($idu,$pas)=$r_wynik->fetch_row();
				if($pas=="")
				{
		     	$r_idu=$idu;
		     	$_SESSION['r_idu']=$r_idu;
		    	setcookie("r_access","oki",time()+session_cache_expire());
					return -3;
				};
			}
			//jezeli nie puste to sprawdzamy
			$r_wynik=$r_MySqlLogin->query("select idu from ".get_class($this)." where login='$r_login' and haslo=old_PASSWORD('$r_haslo');");
			if($r_MySqlLogin->affected_rows<1)//jezeli nie ma starego sprawdzamy nowe ?
			    $r_wynik=$r_MySqlLogin->query("select idu from ".get_class($this)." where login='$r_login' and haslo=PASSWORD('$r_haslo');");
			    if($r_access=="no" && $r_MySqlLogin->affected_rows!=1)
			{
				return 0;
			};
			//-------------------------
			if($r_access=="no")
			{
			    $r_idu=$r_wynik->fetch_row();
		     	$r_idu=$r_idu[0];
		     	$_SESSION['r_idu']=$r_idu;
		    	setcookie("r_access","oki",time()+session_cache_expire());
		    	$_SESSION['timeoflogon']=time();
		    	$page_obj->database_obj->execute_query("update ".get_class($this)." set ostatnielogowanie=now() where idu=$r_idu");
					return 1;
			};
			echo("out of range !!");
		}
		//----------------------------------------------------------------------------------------------------
		public function logout()
		{
			 $_SESSION['r_idu']=-1;
			 $_SESSION['timeoflogon']="";
		   setcookie("r_access","no",time()+session_cache_expire());
		   session_destroy();
		   header("Location: .");
		}
		//----------------------------------------------------------------------------------------------------
		public function idu()
		{
			//zwraca idu zalogowanego w przypadku braku zalogowania zwraca 0
			if($this->is_login())
			{
				$r_idu=$_SESSION['r_idu'];
		 		return $r_idu;
			}
			else
				return 0;
		}
		//----------------------------------------------------------------------------------------------------
		public function name()
		{
			//zwraca 0 - gdy nie zalogowany
			if($this->is_login())
			{
			    $wynik=$this->silnik->baza->get_data("select imie,nazwisko from ".get_class($this)." where idu=".$this->idu(),0,0);
				if($wynik)
				    list($i,$n)=$wynik->fetch_row();
				$zmzw="$i $n";
			}
			else
				$zmzw=0;
			return $zmzw;
		}
		//----------------------------------------------------------------------------------------------------
		public function right()
		{
			//zwraca znak odpowiedzialnosci prawnej
			//0 brak zalogowania
			//-1 brak uprawnień
			$prawo=-1;
			if($this->is_login())
			{
			    $wynik=$this->silnik->baza->get_data("select uprawnienia from ".get_class($this)." where idu=".$this->idu().";");
				if($wynik)
				    list($prawo)=$wynik->fetch_row();
				return $prawo;
			}
			else
				return 0;
		}
		//----------------------------------------------------------------------------------------------------
		private function formchangepassword()
		{
			//TODO: dorobić przypomnienia hasła - formularz i cały system
			$outtext.="<form action='index.php' method='post'>";
			$outtext.="<input type='hidden' name='login' value='".$this->uzytkownicy_idu()."' />";
			$outtext.="<input type='hidden' name='zmk' value='chpassword' />";
			$outtext.="Hasło: <input type='password' name='haslo' /><br />";
			$outtext.="Powtórz: <input type='password' name='haslo2' /><br />";
			$outtext.="<input type='submit' value='Zapisz' />";
			$outtext.="</form>";
			return $outtext;
		}
		//----------------------------------------------------------------------------------------------------
		private function zapiszhaslo($haslo1,$haslo2,$idu)
		{
		  if($haslo1==$haslo2)
		  {
		      if($this->silnik->baza->execute_query("update ".get_class($this)." set haslo=PASSWORD('$haslo1'),datarozpoczeciapracy=datarozpoczeciapracy where idu=$idu;"))
		    	$rettext.="Hasło zostało zapisane.<br />";
		    else
		    	$rettext.="Błąd zmniany hasła<br />";
		  }
		  else
		  {
		  	$rettext.="Niezgodność haseł.<br />Proszę spróbować jeszcze raz.<br />";
		  	$rettext.=$this->formchangepassword();
		  }
		  return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function timeoflogon()
		{
		  if(is_numeric($_SESSION['timeoflogon']))
		    return $_SESSION['timeoflogon'];
		  else
		    return time()-session_cache_expire()-1;
		}
		//----------------------------------------------------------------------------------------------------
		public function lefttimetoendofsession()
		{
		  $czaspozostaly=session_cache_expire()-(time()-rettimeoflogon());
		  return $czaspozostaly;
		}
		//----------------------------------------------------------------------------------------------------
		public function lista()
        {
            $rettext="";
            //--------------------
            $rettext.="<button title='dodaj nowy' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},formularz\"'>Dodaj nowy</button><br />";
            //--------------------
            $wynik=$this->page_obj->database_obj->get_data("select idu,imie,nazwisko,usuniety from ".get_class($this).";");
            if($wynik)
            {
                $rettext.="<script type='text/javascript' src='./js/opticaldiv.js'></script>";
                $rettext.="<script type='text/javascript' src='./js/potwierdzenie.js'></script>";                
                $rettext.="<table style='width:100%;font-size:10pt;' cellspacing='0'>";
                $rettext.="
					<tr style='font-weight:bold;'>
						<td style='width:25px;'>Lp.</td>						
						<td>nazwa</td>
						<td style='width:18px;'></td>
						<td style='width:18px;'></td>						
					</tr>";
                $lp=0;
                while(list($idu,$imie,$nazwisko,$usuniety)=$wynik->fetch_row())
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
							<td>$imie,$nazwisko</td>
							<td style='text-align:center;'><a href='".get_class($this).",{$this->page_obj->template},formularz,$idu'><img src='./media/ikony/edit.png' alt='' style='height:15px;'/></a></td>
							<td style='text-align:center;'>$operacja</td>							
						</tr>
					";
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
		//----------------------------------------------------------------------------------------------------
		public function formularz($idu,$imie,$nazwisko,$login,$haslo,$poziom,$usuniety)
        {
            $rettext="";
            //--------------------
            $_SESSION['antyrefresh']=false;
            //--------------------
            if($idu!="" && is_numeric($idu) && $idu>0)
            {
                $wynik=$this->page_obj->database_obj->get_data("select imie,nazwisko,login,haslo,poziom,usuniety from ".get_class($this)." where usuniety='nie' and idu=$idu");
                if($wynik)
                {
                    list($imie,$nazwisko,$login,$haslo,$poziom,$usuniety)=$wynik->fetch_row();
                }
            }
            //--------------------
            $imie=$this->page_obj->text_obj->doedycji($imie);
			$nazwisko=$this->page_obj->text_obj->doedycji($nazwisko);
			$login=$this->page_obj->text_obj->doedycji($login);
			$haslo=$this->page_obj->text_obj->doedycji($haslo);
			$poziom=$this->page_obj->text_obj->doedycji($poziom);
			$usuniety=$this->page_obj->text_obj->doedycji($usuniety);
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
							<div class='wiersz'><div class='formularzkom1'>Imię: </div><div class='formularzkom2'><input type='text' name='imie' value='$imie' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Nazwisko: </div><div class='formularzkom2'><input type='text' name='nazwisko' value='$nazwisko' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Login: </div><div class='formularzkom2'><input type='text' name='login' value='$login' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Hasło: </div><div class='formularzkom2'><input type='text' name='haslo' value='$haslo' style='width:800px;'/></div></div>
							<div class='wiersz'><div class='formularzkom1'>Poziom uprawnień: </div>
								<div class='formularzkom2'>
									<input type='text' name='poziom' value='$poziom' style='width:800px;'/>
									<select name='poziom'>
										<option value='user'>User</option>
										<option value='admin'>Admin</option>
									</select>
								</div>
							</div>
							<div class='wiersz'><div class='formularzkom1'>Usunięty: </div><div class='formularzkom2'><input type='text' name='usuniety' value='$usuniety' style='width:800px;'/></div></div>
							<div class='wiersz'>
                                <div class='formularzkom1'>&#160;</div>
                                <div class='formularzkom2'>
                                    <input type='submit' name='' title='Zapisz' value='Zapisz' />&#160;&#160;&#160;&#160;
                                    <button title='Anuluj' type='button' onclick='window.location=\"".get_class($this).",{$this->page_obj->template},lista\"'>Anuluj</button>
                                </div>
                            </div>
						</div>
						<input type='hidden' name='idu' value='$idu' />						
					</form>";
            //--------------------
            return $rettext;
        }
		//----------------------------------------------------------------------------------------------------
		public function add($idu,$imie,$nazwisko,$login,$haslo,$poziom,$usuniety)
        {
            $rettext = "";
            //--------------------
            // zabezpieczam dane
            //--------------------
			$imie = $this->page_obj->text_obj->domysql($imie);
            $nazwisko = $this->page_obj->text_obj->domysql($nazwisko);
			$login=$this->page_obj->text_obj->domysql($login);
			$haslo=$this->page_obj->text_obj->domysql($haslo);
			$poziom=$this->page_obj->text_obj->domysql($poziom);
			$usuniety=$this->page_obj->text_obj->domysql($usuniety);

			$uprawnienia = $poziom;
            //--------------------
            if( ($idu != "") && is_numeric($idu) && ($idu > 0) )
            {
                $zapytanie="update ".get_class($this)." set imie='$imie',nazwisko='$nazwisko',login='$login',haslo='$haslo',poziom='$poziom',usuniety='$usuniety' where idu=$idu;";//poprawa wpisu
            }
            else
           {
                $zapytanie="insert into ".get_class($this)."(imie,nazwisko,login,haslo,poziom,uprawnienia)values('$imie','$nazwisko','$login','$haslo','$poziom','$uprawnienia')";//nowy wpis
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
					$rettext.=$zapytanie."<br />";
                    $rettext.=$this->formularz($idu,$imie,$nazwisko,$login,$haslo,$poziom,$usuniety);
                }
            }
            else
           {
               $rettext.=$this->lista();
            }
            return $rettext;
        }
		//----------------------------------------------------------------------------------------------------
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
        //----------------------------------------------------------------------------------------------------
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
		//----------------------------------------------------------------------------------------------------
		private function definicjabazy()
		{
			//funkcja utrzymuje takasama strukture w bazie danych
		    $nazwatablicy=get_class($this);
			//definicja tablicy
			$nazwa="idu";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
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
			
			$nazwa="login";
			$pola[$nazwa][0]="varchar(50)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="haslo";
			$pola[$nazwa][0]="varchar(128)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="poziom";
			$pola[$nazwa][0]="enum('user','admin')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'user'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="usuniety";
			$pola[$nazwa][0]="enum('tak','nie','zablokowany')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nie'";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="uprawnienia";
			$pola[$nazwa][0]="varchar(250)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="ostatnielogowanie";
			$pola[$nazwa][0]="datetime";
			$pola[$nazwa][1]="null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//--------------------
			//dodaje defaultowego uzytkownika
			$wynik=$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where login='admin';",0,0);
			if(!$wynik)
			$this->page_obj->database_obj->execute_query("insert into ".get_class($this)."(imie,nazwisko,login,haslo,poziom,uprawnienia)values('Rafał','Oleśkowicz','admin',PASSWORD('administ'),'admin','all');",0,0);
		}
		//----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>