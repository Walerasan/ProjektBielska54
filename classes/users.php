<?php
if(!class_exists('users'))
{
	class users
	{
		var $message;
		//----------------------------------------------------------------------------------------------------
		public function __construct($page_obj) 
		{
		    $this->definicjabazy($page_obj);
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function get_content($page_obj)
		{
		    $rettext="";
		    $template_class_name=$page_obj->template."_template";
		    //--------------------
		    if($page_obj->template=="admin")
		    {	
		        switch($page_obj->target)
		        {
		            case "test01":
		                $rettext=$page_obj->$template_class_name->get_content($page_obj,"test");
		                break;
		            default:
		                $rettext=$page_obj->$template_class_name->get_content($page_obj,"default");
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
		private function definicjabazy($page_obj)
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
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//--------------------
			//dodaje defaultowego uzytkownika
			$wynik=$page_obj->database_obj->get_data("select idu from ".get_class($this)." where login='admin';",0,0);
			if(!$wynik)
			    $page_obj->database_obj->execute_query("insert into ".get_class($this)."(imie,nazwisko,login,haslo,poziom,uprawnienia)values('Rafał','Oleśkowicz','admin',PASSWORD('administ'),'admin','all');",0,0);
		}
		//----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>