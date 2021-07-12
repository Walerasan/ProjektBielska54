<?php
if(!class_exists('uczniowie_konta_bankowe'))
{
	class uczniowie_konta_bankowe
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
			return "";
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region synchronize
		public function synchronize($idk,$idu)
		{
			#region safe
			if( (!isset($idk)) || ($idk == "") ) $idk = 0;
			if( (!isset($idu)) || ($idu == "") ) $idu = 0;
			$deleted = "empty";
			$sql_query = "";
			$rettext = "";
			#endregion

			#region select
			$wynik=$this->page_obj->database_obj->get_data("select idukb,usuniety from ".get_class($this)." where idk=$idk and idu=$idu;");
			if($wynik)
			{
				list($idukb,$deleted)=$wynik->fetch_row();
			}
			#endregion

			#region switch
			switch($deleted)
			{
				case "empty":
					$sql_query = "insert into ".get_class($this)."(idk,idu)values($idk,$idu)";
					break;
				case "tak":
					$sql_query = "update ".get_class($this)." set usuniety='nie' where idk=$idk and idu=$idu;";//poprawa wpisu
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
		#region get_list_of_nr_konta
		public function get_list_of_nr_konta()
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idu,kb.idk,numer_konta from konta_bankowe kb, ".get_class($this)." ukb where kb.idk = ukb.idk and ukb.usuniety = 'nie';");
			if($wynik)
			{
				while(list($idu,$idk,$nr_k)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idu, (int)$idk, $nr_k);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region mark_delete_for_konto
		public function mark_delete_for_konto($idk)
		{
			$this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idk=$idk;");
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idu_list_for_idw
		public function get_idu_list_for_idw($idk)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where idk = $idk and usuniety = 'nie';");
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
		#region definicjabazy
		private function definicjabazy()
		{
			//funkcja utrzymuje taka sama strukture w bazie danych
			$nazwatablicy=get_class($this);
			$pola=array();
			
			//definicja tablicy
			$nazwa="idukb";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idu";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="idk";
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