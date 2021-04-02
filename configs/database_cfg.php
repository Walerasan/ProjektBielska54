<?php

/*
 CREATE DATABASE labnode CHARACTER SET UTF8;
 CREATE USER user1@localhost IDENTIFIED BY 'password1';
 GRANT ALL PRIVILEGES ON *.* TO 'user1'@localhost IDENTIFIED BY 'password1'; 
 FLUSH PRIVILEGES;
 */

if(!class_exists('database_cfg'))
{
    class database_cfg
    {
        var $server;
        var $login;
        var $password;
        var $database_name;
        var $show_queries_when_error;
        var $show_queries_when_ok;
        //----------------------------------------------------------------------------------------------------
        public function __construct()
        {            
            if($_SERVER['REMOTE_ADDR']=="127.0.0.1")//dla lokalnego serwera włączam imagemagic z lokalnego dysku
            {
                $this->server="localhost";
                $this->login="user1";
                $this->password="password1";
                $this->database_name="labnode";
                $this->show_queries_when_error=true;
                $this->show_queries_when_ok=true;
            }
            else//ustawienia dla serwera
            {                
                $this->server="localhost";
                $this->login="user1";
                $this->password="password1";
                $this->database_name="labnode";
                $this->show_queries_when_error=true;
                $this->show_queries_when_ok=true;
            }
        }
        //----------------------------------------------------------------------------------------------------
        public function __destruct()
        {
            
        }
        //----------------------------------------------------------------------------------------------------
        public function get_server(){return $this->server;}
        //----------------------------------------------------------------------------------------------------
        public function get_login(){return $this->login;}
        //----------------------------------------------------------------------------------------------------
        public function get_password(){return $this->password;}
        //----------------------------------------------------------------------------------------------------
        public function get_database_name(){return $this->database_name;}
        //----------------------------------------------------------------------------------------------------
        public function show_queries_with_error(){return $this->show_queries_when_error;}
        //----------------------------------------------------------------------------------------------------
        public function show_queries_without_error(){return $this->show_queries_when_ok;}
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>