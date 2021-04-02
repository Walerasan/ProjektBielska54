<?php
if(!class_exists('oddzialy'))
{
    class oddzialy
    {
        //----------------------------------------------------------------------------------------------------
        public function __construct($page_obj)
        {
            $this->definicjabazy($page_obj);
        }
        //----------------------------------------------------------------------------------------------------
        public function __destruct()
        {
        }
        //----------------------------------------------------------------------------------------------------
        public function get_content($page_obj)
        {
            //todo
        }
        //----------------------------------------------------------------------------------------------------
        public function form($idod,$name)
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        public function add($idod,$name)
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        public function delete($idod,$confirm)
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        public function get_list()
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        public function get_name($idod)
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        private function definicjabazy($page_obj)
        {
            //funkcja utrzymuje taka sama strukture w bazie danych
            $nazwatablicy=get_class($this);
            
            //definicja tablicy
            $nazwa="idod";
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
            
            $nazwa="nazwa";
            $pola[$nazwa][0]="varchar(50)";
            $pola[$nazwa][1]="";//null
            $pola[$nazwa][2]="";//key
            $pola[$nazwa][3]="";//default
            $pola[$nazwa][4]="";//extra
            $pola[$nazwa][5]=$nazwa;
                        
            //----------------------------------------------------------------------------------------------------
            $page_obj->database_obj->install($nazwatablicy,$pola);
            unset($pola);
            //--------------------            
        }
        //----------------------------------------------------------------------------------------------------
    }
}//end if
else
    die("Class exists: ".__FILE__);
?>