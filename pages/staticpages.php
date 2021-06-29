<?php
if(!class_exists('staticpages'))
{
	include_once("./classes/generators.php");
	include_once("./classes/data.php");
	include_once("./classes/window.php");
	include_once('./classes/subpages.php');
	include_once('./classes/graphic.php');
	include_once('./classes/picture_gallery.php');	
	
	class staticpages
	{
		var $page_obj;
		//----------------------------------------------------------------------------------------------------
		#region construct
		public function __construct($page_obj)
		{
			$this->page_obj=$page_obj;
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
			$content_text="";
			$template_class_name=$this->page_obj->template."_template";
			//--------------------
			if($this->page_obj->template=="admin")
			{
				switch($this->page_obj->target)
				{
					case "test01":
						$content_text=$this->test01_target($page_obj);
					break;
					default:
						$content_text=$this->default_target();
					break;
				}
				/*if($this->page_obj->users->is_login())
				{
					switch($this->page_obj->target)
					{
						
					}
				}
				else
					$rettext="Proszę się zalogować";*/
			}
			else if($this->page_obj->template=="raw")
			{
				switch($this->page_obj->target)
				{
					case "test01":
						$content_text=$this->test01_target($page_obj);
					break;
					default:
						$content_text=$this->default_target();
					break;
				}
			}
			else if($this->page_obj->template=="index")
			{
				switch($this->page_obj->target)
				{
					case "test01":
						$content_text=$this->test01_target();
					break;
					default:
						$content_text=$this->default_target();
					break;
				}
			}
			return $this->page_obj->$template_class_name->get_content($content_text);
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region default_target
		private function default_target()
		{
			return "Witam";
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
		#region test01_target
		private function test01_target()
		{
			
			$rettext="To jest sprawdzian klasy generatorów <br />";
			$generators_obj=new generators($this->page_obj);
			$rettext.=$generators_obj->makenumberfield($this->page_obj->server_cfg_obj->convert,$this->page_obj->server_cfg_obj->identify);
			
			$rettext.="To jest sprawdzian klasy data <br />";
			$data_obj=new data();
			$rettext.=$data_obj->formatnazwamcgodzina(date("Y-m-d H:i:s"))."<br />";
			$rettext.=$data_obj->formatdatagodzina(date("Y-m-d H:i:s"))."<br />";
			$rettext.=$data_obj->formatgodzina(date("Y-m-d H:i:s"))."<br />";
			$rettext.=$data_obj->timestamp(date("Y-m-d H:i:s"))."<br />";
			$rettext.=$data_obj->dmr(date("Y-m-d H:i:s"))."<br />";
			$rettext.=$data_obj->mcname(date("m"))."<br />";
			$rettext.=$data_obj->mcname(date("m"),true)."<br />";

			/*$window_obj=new window();
			$rettext.=$window_obj->create("tu cos jest");*/
			
			
			/*$subpages=new subpages($this);
			if(!isset($_GET["par1"]))$_GET["par1"]=0;
			$rettext.=$subpages->create(100,10,$_GET["par1"],get_class($this).",index,test01");*/
			
			/*$graphic_obj=new graphic();
			$graphic_obj->backuppicture("./media/desktop/backgroundbuttonfield.gif");*/
			
			//$picture_gallery_obj=new picture_gallery($page_obj);
			
			return $rettext;
		}
		#endregion
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
	die("Class exists: ".__FILE__);
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
?>