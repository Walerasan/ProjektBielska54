<?php
if(!class_exists('uczniowie_opiekunowie'))
{
	class uczniowie_opiekunowie
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
		#region get_ido
		public function get_ido($idu)
		{
			$rettext = array();
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select ido from ".get_class($this)." where idu=$idu and usuniety='nie';");
			if($wynik)
			{
				while(list($ido)=$wynik->fetch_row())
				{
					$rettext[] = array((int)$ido);
				}
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_idu
		public function get_idu($ido)
		{
			$rettext=-1;
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select idu from ".get_class($this)." where ido=$ido and usuniety='nie';");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_iduo
		public function get_iduo($idu,$ido)
		{
			$rettext=-1;
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select iduo from ".get_class($this)." where idu=$idu and ido=$ido;");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region mark_usuniety
		public function mark_usuniety($idu,$confirm)
		{
			$rettext = false;
			//--------------------
			if($confirm == "yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where idu=$idu;"))
				{
					$rettext = true;
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region get_usuniety
		public function get_usuniety($iduo)
		{
			$rettext=-1;
			//--------------------
			$wynik=$this->page_obj->database_obj->get_data("select usuniety from ".get_class($this)." where iduo=$iduo;");
			if($wynik)
			{
				list($rettext)=$wynik->fetch_row();
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region insert
		public function insert($idu,$ido)
		{
			$rettext=-1;
			//--------------------
			/*$old_ido=$this->get_ido($idu);
			if($old_ido != $ido)
			{
				$iduo = $this->get_iduo($idu,$old_ido);
				$zapytanie="update ".get_class($this)." set ido=$ido where iduo=$iduo;";
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$rettext=$iduo;
				}
			}*/
			$iduo = $this->get_iduo($idu,$ido);
			if($iduo<=0)
			{
				$zapytanie="insert into ".get_class($this)."(idu,ido)values($idu,$ido);";
				if($this->page_obj->database_obj->execute_query($zapytanie))
				{
					$rettext=$this->page_obj->database_obj->last_id();
				}
			}
			else
			{
				$usuniety=$this->get_usuniety($iduo);
				if($usuniety=="nie")
				{
					$rettext=$iduo;
				}
				else
				{
					$zapytanie="update ".get_class($this)." set usuniety='nie' where idu=$idu and ido=$ido;";
					if($this->page_obj->database_obj->execute_query($zapytanie))
					{
						$rettext=$iduo;
					}
				}
			}
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region delete
		public function delete($iduo,$confirm)
		{
			$rettext=false;
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='tak' where iduo=$iduo;"))
				{
					$rettext=true;
				}
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region restore
		public function restore($iduo,$confirm)
		{
			$rettext=false;
			//--------------------
			if($confirm=="yes")
			{
				if($this->page_obj->database_obj->execute_query("update ".get_class($this)." set usuniety='nie' where iduo=$iduo;"))
				{
					$rettext=true;
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
			$nazwa="iduo";
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
			
			$nazwa="ido";
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