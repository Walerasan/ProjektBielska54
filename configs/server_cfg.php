<?php
if(!class_exists('server_cfg'))
{
    class server_cfg
    {
        var $convert;
        var $identify;
        var $xhtmlon;
        var $pathtofavcicon;
        var $createdate;
        var $autor;
        var $autoremail;
        var $revisitafter;
        var $slowakluczowedlaautora;
        var $defaultlanguage;
        var $adresstrony;
        var $log_type;
        var $sendmailwitherror;
        var $showerror;
        var $page_start;
        var $template_start;
        var $target_start;    
        //----------------------------------------------------------------------------------------------------
        public function __construct()
        {
            $this->page_start="staticpages";
            $this->template_start="index";
            $this->target_start="pokaz";            
            
            if( ($_SERVER['REMOTE_ADDR']=="127.0.0.1") || ($_SERVER['REMOTE_ADDR']=="::1") )//dla lokalnego serwera włączam imagemagic z lokalnego dysku
            {
                $this->convert='G:\eclipse_php\imagemagic\convert.exe';
                $this->identify='G:\eclipse_php\imagemagic\identify.exe';
                $this->composite='G:\eclipse_php\imagemagic\composite.exe';
                
                $this->xhtmlon=true;
                //--------------------
                $this->pathtofavcicon="./media/desktop/favico.ico";
                $this->createdate="Cz, 15 lis 2012 12:14:46 +0100";
                $this->autor="Rafał Płatkowski";
                $this->autoremail="rafal@labnode.org";
                $this->revisitafter="1 Day";
                $this->slowakluczowedlaautora="LabNode.org Laboratory - Laboratorium informatyki, matematyki, chemii, mechaniki, elektroniki, psychologii - mgr inż. Rafał Oleśkowicz";
                //--------------------
                $this->defaultlanguage='pl';
                //--------------------
                $this->adresstrony="http://localhost/001_LabNodeOrg/www/src/";
                //--------------------
                $this->logonazdjecia="./media/desktop/logonazdjecia.png";
                //--------------------
                $this->log_type="all"; //[all] [error] [good]
                $this->sendmailwitherror=false;
                $this->showerror=true;
                
            }
            else//ustawienia dla serwera
            {
                $this->convert='convert';
                $this->identify='identify';
                $this->composite='composite';
                
                $this->xhtmlon=true;
                //--------------------
                $this->pathtofavcicon="./media/desktop/favico.ico";
                $this->createdate="Cz, 15 lis 2012 12:14:37 +0100";
                $this->autor="Rafał Płatkowski";
                $this->autoremail="rafal@labnode.org";
                $this->revisitafter="1 Day";
                $this->slowakluczowedlaautora="LabNode.org Laboratory - Laboratorium informatyki, matematyki, chemii, mechaniki, elektroniki, psychologii - mgr inż. Rafał Oleśkowicz";
                //--------------------
                $this->defaultlanguage='pl';
                //--------------------
                $this->adresstrony="";
                //--------------------
                $this->logonazdjecia="./media/desktop/logonazdjecia.png";
                //--------------------
                $this->log_type="error"; //[all] [error] [good]
                $this->sendmailwitherror=false;
                $this->showerror=false;
            }
        }
        //----------------------------------------------------------------------------------------------------
        public function __destruct()
        {
            
        }
        //----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>