<?php
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
if(!class_exists('text'))
{
    class text
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
        public function xhtmlprotect($input_text)
        {
            $find[0]="/&nbsp;/";
            $replace[0]="&#160;";
            $find[1]="/&ndash;/";
            $replace[1]="&#8211;";
            return preg_replace($find,$replace,$input_text);
        }
        //----------------------------------------------------------------------------------------------------
        //----------------------------------------------------------------------------------------------------
        /**
         * przygotowuje dane do zapisu mysql
         * @param string
         * @return string
         * @throws null
         */
        public function domysql($input)
        {
            //zamienic wszystkie " i ' na &
            $wzor[1]="/\\'/";
            $zamiany[1]="&apos;";
            $wzor[2]="/\\\"/";
            $zamiany[2]="&quot;";
            
            //najpierw pozbyc sie \
            //$wzor[0]="/\\/";
            //$zamiany[0]="";
            //najpier wszystkie \' zamienic na jakis znak np #\#'#
            //$wzor[1]="/\\'/";
            //$zamiany[1]="#apos#";
            //teraz wszystkie samotne \ na \\
            //$wzor[2]="/\\\\/";
            //$zamiany[2]="\\\\\\\\";
            //teraz wszystkie samotne ' na \'
            //$wzor[3]="/'/";
            //$zamiany[3]="\'";
            //teraz spowrotem aposy na \'
            //$wzor[5]="/#apos#/";
            //$zamiany[5]="\'";
            $output=preg_replace($wzor,$zamiany,$input);
            return $output;
        }
        //----------------------------------------------------------------------------------------------------
        /**
         * odtwarza dane z mysql
         * @param string
         * @return string
         * @throws null
         */
        public function zmysql($input)
        {
            /*$wzor="/[\\\\']/";
             $zamiany="'";*/
            
            $wzor[1]="/&apos;/";
            $zamiany[1]="'";
            $wzor[2]="/&quot;/";
            $zamiany[2]="\"";
            
            $output=preg_replace($wzor,$zamiany,$input);
            return $output;
        }
        //----------------------------------------------------------------------------------------------------
        /**
         * przygotowuje dane do edycji
         * @param string
         * @return string
         * @throws null
         */
        public function doedycji($input)
        {
            //przywracam dane pierwotne
            $input=$this->zmysql($input);
            
            //do edycji trzeba zamienic wszystkie tagi na &lt;b&gt;
            //trzeba tez zamienic wszystkie  '
            $wzor[0]="/&oacute;/";
            $zamiany[0]="ó";
            //$wzor[1]="/&gt;/";
            //$zamiany[1]="&#62;";
            $wzor[2]="/</";
            $zamiany[2]="&lt;";
            $wzor[3]="/>/";
            $zamiany[3]="&gt;";
            //$wzor[4]="/'/";
            //$zamiany[4]="&#39;";
            //$wzor[5]="/&nbsp;/";
            //$zamiany[5]="&#160;";
            //$wzor[6]="/\"/";
            //$zamiany[6]="&quot;";
            //$wzor[7]="/'/";
            //$zamiany[7]="&apos;";
            //$wzor[8]="/&/";
            //$zamiany[8]="&amp;";
            
            $output=preg_replace($wzor,$zamiany,$input);
            return $output;
        }
        //----------------------------------------------------------------------------------------------------
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
//----------------------------------------------------------------------------------------------------
?>