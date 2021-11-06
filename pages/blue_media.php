<?php
if(!class_exists('blue_media'))
{
	class blue_media
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj = $page_obj;
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
			$content_text="<p class='title'>BLUE MEDIA</p>";
			$template_class_name = $this->page_obj->template."_template";
			//--------------------
			if( $this->page_obj->template == "index" )
			{
				switch($this->page_obj->target)
				{
					default:
						$content_text .= "";
						break;
				}
			}
			if( $this->page_obj->template == "raw" )
			{
				switch($this->page_obj->target)
				{
					default:
						$content_text .= "";
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		public function jest_oplata_rozliczona($iduop)
		{
			$result = false;
			//--------------------
			$zapytanie = "select bm.idbm from " . get_class($this) . " bm, " . get_class($this)."_uczniowie_oplaty bmuo where bm.idbm = bmuo.idbm and status = 'oplacone' and iduop = $iduop;";
			$wynik = $this->page_obj->database_obj->get_data($zapytanie);
			if( $wynik )
			{
				$result = true;
			}
			//--------------------
			return $result;
		}
		//----------------------------------------------------------------------------------------------------
		#region definicjabazy
		private function definicjabazy()
		{
			$nazwatablicy = get_class($this);
			$pola = array();
			
			//definicja tablicy
			$nazwa = "idbm";
			$pola[$nazwa][0] = "int(10)";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "primary key";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "auto_increment";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "usuniety";
			$pola[$nazwa][0] = "enum('tak','nie','zablokowany')";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "'nie'";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "orderID";
			$pola[$nazwa][0] = "varchar(150)";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;
			
			$nazwa = "description";
			$pola[$nazwa][0] = "varchar(250)";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "amount";
			$pola[$nazwa][0] = "decimal(8,2)";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "customerEmail";
			$pola[$nazwa][0] = "varchar(150)";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "payment_link";
			$pola[$nazwa][0] = "varchar(150)";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "status";
			$pola[$nazwa][0] = "enum('nowe','oplacone','blad', 'expired')";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "'nowe'";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "messages";
			$pola[$nazwa][0] = "text";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "date";
			$pola[$nazwa][0] = "datetime";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//--------------------

			$nazwatablicy = get_class($this)."_ITN";
			$pola = array();
			
			//definicja tablicy
			$nazwa = "iditn";
			$pola[$nazwa][0] = "int(10)";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "primary key";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "auto_increment";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "usuniety";
			$pola[$nazwa][0] = "enum('tak','nie','zablokowany')";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "'nie'";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "idbm";
			$pola[$nazwa][0] = "int(10)";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;
			
			$nazwa = "status";
			$pola[$nazwa][0] = "varchar(250)";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "status_details";
			$pola[$nazwa][0] = "varchar(250)";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "payment_date";
			$pola[$nazwa][0] = "datetime";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "amount";
			$pola[$nazwa][0] = "decimal(8,2)";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

			//----------------------------------------------------------------------------------------------------
			$this->page_obj->database_obj->install($nazwatablicy,$pola);
			unset($pola);
			//--------------------

			$nazwatablicy = get_class($this)."_uczniowie_oplaty";
			$pola = array();
			
			//definicja tablicy
			$nazwa = "idbm_u_o";
			$pola[$nazwa][0] = "int(10)";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "primary key";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "auto_increment";//extra
			$pola[$nazwa][5] = $nazwa;

			$nazwa = "idbm";
			$pola[$nazwa][0] = "int(10)";
			$pola[$nazwa][1] = "not null";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;
			
			$nazwa = "iduop";
			$pola[$nazwa][0] = "varchar(250)";
			$pola[$nazwa][1] = "";//null
			$pola[$nazwa][2] = "";//key
			$pola[$nazwa][3] = "";//default
			$pola[$nazwa][4] = "";//extra
			$pola[$nazwa][5] = $nazwa;

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