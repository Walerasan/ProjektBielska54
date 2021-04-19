<?php
if(!class_exists('powiadomienia'))
{
	class powiadomienia
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
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------		
		#region insert
		public function insert($idsop)
		{
			$rettext = "";			
			//--------------------
			$zapytanie="insert into ".get_class($this)."(idsop,date,status)values($idsop,now(),'nowe')";//nowy wpis
			//--------------------
			if($this->page_obj->database_obj->execute_query($zapytanie))
			{
				$rettext.="Zapisane<br />";
			}
			else
			{
				$rettext.="Błąd zapisu - proszę spróbować ponownie - jeżeli błąd występuje nadal proszę zgłosić to twórcy systemu.<br />";
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------		
		#region set_status
		public function set_status($idpo,$new_status)
		{
			$result = false;
			if( ($new_status == "nowe") || ($new_status == "wyslane") || ($new_status == "error") )
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set status='$new_status' where idpo=$idpo;"))
				{
					$result=true;
				}
			}
			//--------------------
			return $result;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_list
		public function get_list($idsop)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idpo,data,status from ".get_class($this)." where idsop=$idsop;");
			if($wynik)
			{
				while(list($idpo,$data,$status)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$idpo, $data, $status);
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_detail
		public function get_detail($idpo)
		{
			$rettext=array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select data,status from ".get_class($this)." where idpo=$idpo;");
			if($wynik)
			{
				while(list($data,$status)=$wynik->fetch_row())
				{
					$rettext[] = array($data, $status);
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
			$nazwa="idpo";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="primary key";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="auto_increment";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idsop";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;	
			
			$nazwa="data";
			$pola[$nazwa][0]="timestamp";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;
			
			$nazwa="status";
			$pola[$nazwa][0]="enum('nowe','wyslane','error')";
			$pola[$nazwa][1]="not null";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="'nowe'";//default
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