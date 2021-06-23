<?php
if(!class_exists('wyciagi_uczniowie'))
{
	class wyciagi_uczniowie
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
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idu_list_for_idw
		public function get_idu_list_for_idw($idw)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where idw=$idw and usuniety='nie';");
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
		#region synchronize
		public function synchronize($idu,$idw,$set_automatic)
		{
			#region safe
			if( (!isset($idw)) || ($idw == "") ) $idw = 0;
			if( (!isset($idu)) || ($idu == "") ) $idu = 0;
			$deleted = "empty";
			$sql_query = "";
			$rettext = "";
			$sync_type = ($set_automatic) ? "auto" : "manual";			
			#endregion

			#region select
			$wynik=$this->page_obj->database_obj->get_data("select idwu,usuniety from ".get_class($this)." where idw=$idw and idu=$idu;");
			if($wynik)
			{
				list($idwu,$deleted)=$wynik->fetch_row();
			}
			#endregion

			#region switch
			switch($deleted)
			{
				case "empty":
					$sql_query = "insert into ".get_class($this)."(idw,idu,status)values($idw,$idu,'$sync_type')";
					break;
				case "tak":
					$sql_query = "update ".get_class($this)." set usuniety='nie',status='$sync_type' where idw=$idw and idu=$idu;";//poprawa wpisu
					break;
				case "nie":
					// nothing to do here
					break;
			}
			#endregion

			#region execute
			if($sql_query != "")
			{
				if($this->page_obj->database_obj->execute_query($sql_query))
				{
					//$rettext.=$sql_query."<br />";
				}
				else
				{
					$rettext.="Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
					//$rettext.=$sql_query."<br />";
				}
			}
			#endregion

			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region is_assigned
		public function is_assigned_wyciagi($idw)
		{
			$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where idw=$idw and usuniety='nie';");
			if( $this->page_obj->database_obj->result_count() > 0 ) return true;
			//--------------------
			return false;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_liste_wyciagow_dla_ucznia
		public function get_liste_wyciagow_dla_ucznia($idu)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idw from ".get_class($this)." where idu=$idu and usuniety='nie';");
			if($wynik)
			{
				while(list($idw)=$wynik->fetch_row())
				{
					$rettext[] = (int)$idw;
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
			$nazwa="idwu";
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

			$nazwa="idw";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="status";
			$pola[$nazwa][0]="enum('not_asigned','manual','auto')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'not_asigned'";//default
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