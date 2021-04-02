<?php
//----------------------------------------------------------------------------------------------------
include_once("./classes/page.php");
//----------------------------------------------------------------------------------------------------
if(!class_exists('index'))
{
    class index
    {
        var $errors;        
        //----------------------------------------------------------------------------------------------------
        public function __construct()
        {
            session_start();
            ini_set('display_errors', 0);
            error_reporting(E_ALL);
            set_error_handler(array($this,"handleError"));
            set_exception_handler(array($this,"exception_handler"));            
            register_shutdown_function(array($this,"error_alert"));            
        }
        //----------------------------------------------------------------------------------------------------
        public function __destruct()
        {
        }
        //----------------------------------------------------------------------------------------------------
        public function show()
        {            
            $page_obj=new page();
            return $page_obj->show();
        }
        //----------------------------------------------------------------------------------------------------
        public function exception_handler($exception)
        {
            $this->errors.="<u>Uncaught exception:</u> ".$exception->getMessage()." -- ".$exception->getFile()." -- ".$exception->getLine()."<br />\n";
        }
        //----------------------------------------------------------------------------------------------------
        public function handleError($errno, $errstr,$error_file,$error_line)
        {
            $this->errors.="!<u>Error:</u> [$errno] $errstr - $error_file:$error_line <br />\n";
        }
        //----------------------------------------------------------------------------------------------------
        public function error_alert()
        {
            echo($this->errors);
        }
        //----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
//----------------------------------------------------------------------------------------------------
?>