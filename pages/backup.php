<?php
if( !class_exists("backup") )
{
	class backup
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj = $page_obj;
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
			if($this->page_obj->template == "raw")
			{
				switch($this->page_obj->target)
				{
					case "get":
						$content_text = $this->get();
						break;
				}
			}
			//--------------------
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		//----------------------------------------------------------------------------------------------------
		private function get()
		{
			$filename = "backup-" . date("d-m-Y") . ".sql.gz";
			$mime = "application/x-gzip";
			header( "Content-Type: " . $mime );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			$cmd = "mysqldump -u " . $this->page_obj->database_cfg_obj->get_login() . " --password=" . $this->page_obj->database_cfg_obj->get_password() . " " . $this->page_obj->database_cfg_obj->get_database_name() . " | gzip --best";
			passthru( $cmd );
		}
	}
}//end if
else
	die("Class exists: ".__FILE__);
?>