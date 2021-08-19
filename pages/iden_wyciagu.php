<?php
if( !class_exists('iden_wyciagu') )
{
	class iden_wyciagu
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
			$content_text = "";
			$template_class_name = $this->page_obj->template."_template";
			//--------------------
			if($this->page_obj->template == "admin")
			{
				switch($this->page_obj->target)
				{
					case "form":
						$idu = isset($_GET['par1']) ? $_GET['par1'] : 0;
						$content_text = $this->form($idu);
						break;
					case "save":
						$idu = isset($_POST['idu']) ? $_POST['idu'] : 0;
						$idiw = isset($_POST['idiw']) ? $_POST['idiw'] : 0;
						$identyfikator = isset($_POST['identyfikator']) ? $_POST['identyfikator'] : 0;
						$content_text = $this->save($idu,$idiw,$identyfikator);
						break;
					default:
						$content_text = "";
					break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region formularz
		public function form($idu,$idiw)
		{
			$rettext = "";
			//--------------------
			if($idu > 0)
			{
				if( isset($idiw) && ($idiw > 0) )
				{
					$wynik = $this->page_obj->database_obj->get_data("select identyfikator from ".get_class($this)." where idiw = $idiw;");
					if($wynik)
					{
						list($identyfikator) = $wynik->fetch_row();
					}
				}
				//--------------------
				$rettext="
					<style>
						div.wiersz{float:left;clear:left;}
						div.formularzkom1{width:150px;text-align:right;margin-right:5px;float:left;clear:left;margin:2px;}
						div.formularzkom2{width:450px;text-align:left;margin-right:5px;float:left;margin:2px;}
					</style>";
				$rettext .= "
					<form method='post' action='".get_class($this).",{$this->page_obj->template},save'>
						<div style='overflow:hidden;'>
							<div class='wiersz'><div class='formularzkom1'>Identyfikator: </div><div class='formularzkom2'><input type='text' name='identyfikator' value='$identyfikator' style='width:800px;'/></div></div>
							<div class='wiersz'>
								<div class='formularzkom1'>&#160;</div>
								<div class='formularzkom2'>
									<input type='submit' name='' title='Zapisz' value='Zapisz' style='font-size:20px;'/>&#160;&#160;&#160;&#160;
									<button title='Anuluj' style='font-size:20px;float:right;' type='button' onclick='window.location=\"".get_class($this).",admin,lista\"'>Anuluj</button>
								</div>
							</div>
						</div>
						<input type='hidden' name='idu' value='$idu' />
						<input type='hidden' name='idiw' value='$idiw' />
					</form>";
			}
			else
			{
				$rettext .= "Zły identyfikator ucznia.";
			}
			//--------------------
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region save
		private function save($idu,$idiw,$identyfikator)
		{
			$rettext = "";
			//--------------------
			if($idu > 0)
			{
				tutaj dokonczyc

			}
			else
			{
				$rettext .= "Zły identyfikator ucznia.";
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
			$nazwatablicy = get_class($this);
			$pola = array();
			
			//definicja tablicy
			$nazwa="idiw";
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
			
			$nazwa="identyfikator";
			$pola[$nazwa][0]="varchar(250)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
			$pola[$nazwa][4]="";//extra
			$pola[$nazwa][5]=$nazwa;

			$nazwa="idu";
			$pola[$nazwa][0]="int(10)";
			$pola[$nazwa][1]="";//null
			$pola[$nazwa][2]="";//key
			$pola[$nazwa][3]="";//default
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