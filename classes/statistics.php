<?php
if(!class_exists('statistics'))
{
    class statistics
	{
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
		public function insert($page_obj)
		{
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			else
				$ip=$_SERVER['REMOTE_ADDR'];
				
			$adr=gethostbyaddr($ip);
			
			//--------------------
			//pobieram id adresu
			//--------------------
			$wynik=$page_obj->database_obj->get_data("select idsa from ".get_class($this)."_a where ip='$ip' and adr='$adr'",0,0);
			if($wynik)
			    list($idsa)=$wynik->fetch_row();
			else
			{
			    $page_obj->database_obj->execute_query("insert into ".get_class($this)."_a (ip,adr)values('$ip','$adr');",0,0);
			    $idsa=$page_obj->database_obj->last_id();
			};
			
			//--------------------
			//pobieram id miejsca
			//--------------------
			$link=substr($_SERVER['REQUEST_URI'],1);
			$wynik=$page_obj->database_obj->get_data("select idsm from ".get_class($this)."_m where link='$link'",0,0);
			if($wynik)
			    list($idsm)=$wynik->fetch_row();
			else
			{
			    $page_obj->database_obj->execute_query("insert into ".get_class($this)."_m (link)values('$link');",0,0);
			    $idsm=$page_obj->database_obj->last_id();
			};
			
			//--------------------
			//pobieram id linku
			//--------------------
			$link="";
			if(isset($_SERVER['SCRIPT_URI']) && strlen($_SERVER['SCRIPT_URI'])>0)
			{
				$adresserwera=substr($_SERVER['SCRIPT_URI'],7,strpos($_SERVER['SCRIPT_URI'],'/',8)-7);
				if(isset($_SERVER['HTTP_REFERER']) && !eregi($adresserwera,$_SERVER['HTTP_REFERER']))
					$link=$_SERVER['HTTP_REFERER'];
			}
			
			$wynik=$page_obj->database_obj->get_data("select idsl from ".get_class($this)."_l where link='$link'",0,0);
			if($wynik)
			    list($idsl)=$wynik->fetch_row();
			else
			{
			    $page_obj->database_obj->execute_query("insert into ".get_class($this)."_l (link)values('$link');");
			    $idsl=$page_obj->database_obj->last_id();
			};
			
			//--------------------
			//pobieram id przeglądarki
			//--------------------
			$nazwap=$_SERVER['HTTP_USER_AGENT'];
			$wynik=$page_obj->database_obj->get_data("select idsp from ".get_class($this)."_p where nazwa='$nazwap'",0,0);
			if($wynik)
			    list($idsp)=$wynik->fetch_row();
			else
			{
			    $page_obj->database_obj->execute_query("insert into ".get_class($this)."_p (nazwa)values('$nazwap');",0,0);
			    $idsp=$page_obj->database_obj->last_id();
			};
			
			//dodaje wpis
			$wynik=$page_obj->database_obj->get_data("select idsj from ".get_class($this)."_j where idsa=$idsa and idsm=$idsm and idsl=$idsl and idsp=$idsp ",0,0);
			if($wynik)
			    list($idsj)=$wynik->fetch_row();
			else
			{
			    $page_obj->database_obj->execute_query("insert into ".get_class($this)."_j(idsa,idsm,idsl,idsp)values($idsa,$idsm,$idsl,$idsp)",0,0);
			    $idsj=$page_obj->database_obj->last_id();
			};
			
			$page_obj->database_obj->execute_query("insert into ".get_class($this)."_d(data,idsj)values(now(),$idsj)",0,0);
		}
		//----------------------------------------------------------------------------------------------------
		private function definicjabazy($page_obj)
		{
			//funkcja utrzymuje takasama strukture w bazie danych
			$nazwatablicy=get_class($this)."_a";
			//definicja tablicy
			$nazwa="idsa";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="ip";
			$pola[$nazwa][0]="varchar(16)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="adr";
			$pola[$nazwa][0]="varchar(150)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			//--------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------
				
			$nazwatablicy=get_class($this)."_m";
			//definicja tablicy
			$nazwa="idsm";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
		
			$nazwa="link";
			$pola[$nazwa][0]="varchar(150)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			//--------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------

			$nazwatablicy=get_class($this)."_l";
			//definicja tablicy
			$nazwa="idsl";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="link";
			$pola[$nazwa][0]="varchar(150)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			//--------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------

			$nazwatablicy=get_class($this)."_p";
			//definicja tablicy
			$nazwa="idsp";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="nazwa";
			$pola[$nazwa][0]="varchar(250)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			//--------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------

			$nazwatablicy=get_class($this)."_j";
			//definicja tablicy
			$nazwa="idsj";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idsa";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idsm";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idsl";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idsp";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			//--------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------
			
			$nazwatablicy=get_class($this)."_d";
			//definicja tablicy
			$nazwa="idsd";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="data";
			$pola[$nazwa][0]="datetime";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			$nazwa="idsj";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
				
			//--------------------
			$page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//----------------------------------------------------------------------------------------------------						
		}
		//----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>