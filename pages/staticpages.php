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
		//----------------------------------------------------------------------------------------------------
		public function __construct()
		{
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
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->test01_target($page_obj),"menu");
			            break;
			        default:
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->default_target(),"menu");
			            break;
			    }
			    /*if($page_obj->users->is_login())
				{
				    switch($page_obj->target)
					{
						
					}
				}
				else
					$rettext="Proszę się zalogować";*/
			}
			else if($page_obj->template=="raw")
			{
			    switch($page_obj->target)
			    {
			        case "test01":
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->test01_target($page_obj),"menu");
			            break;
			        default:
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->default_target(),"menu");
			            break;
			    }
			}
			else if($page_obj->template=="index")			
			{			    
			    switch($page_obj->target)
			    {
			        case "test01":
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->test01_target($page_obj),"menu");
			            break;
			        default:
			            $rettext=$page_obj->$template_class_name->get_content($page_obj,$this->default_target(),"menu");
			            break;
			    }
			    
			    
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		private function default_target()
		{
		    return "Tutaj przypiść stronę główna - plik staticpages - linia kodu 55";
		}
		//----------------------------------------------------------------------------------------------------
		private function test01_target($page_obj)
		{		    
		    
		    $rettext="To jest sprawdzian klasy generatorów <br />";		    
		    $generators_obj=new generators($page_obj);		    
		    $rettext.=$generators_obj->makenumberfield($page_obj->server_cfg_obj->convert,$page_obj->server_cfg_obj->identify);
		    
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
		//----------------------------------------------------------------------------------------------------
	}//end class
}//end if
else
    die("Class exists: ".__FILE__);
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
?>