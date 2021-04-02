<?php
if(!class_exists('generators'))
{
    class generators
	{
	    var $imagedir;
		//----------------------------------------------------------------------------------------------------
	    public function __construct($page_obj)
		{			
		    $this->imagedir=$page_obj->create_directory("./media/numbers",debug_backtrace());
		}
		//----------------------------------------------------------------------------------------------------
		public function __destruct()
		{
		}
		//----------------------------------------------------------------------------------------------------
		public function makenumberfield($convert,$identify,$name='numer',$id='number')
		{
			$iloscznakow=5;
			$wysokosc=24;
			$strokewidth=2;
			$color="#bbbbbb";
			//----------------------------------------------------------------------------------------------------
			//sprawdzam poprawność programu convert
			//----------------------------------------------------------------------------------------------------
			if($convert=="")
				return "<span style='font-weight:bold;color:red;'>Brak scieżki do programu ImageMagic</span>";
			exec($convert." --help",$output);						
			if( (!isset($output[0])) || (!preg_match("/ImageMagick/i",$output[0])) )
				return "<span style='font-weight:bold;color:red;'>Podana ścieżka nie wskazuje na program ImageMagic</span>";
			//----------------------------------------------------------------------------------------------------
			//generuje numerek
			//----------------------------------------------------------------------------------------------------
			$numer=$this->genref($iloscznakow,'numalf');
			$out=rand(0,10000);
			exec($convert." -font ./media/desktop/courierb.ttf -pointsize $wysokosc -fill \"$color\" -gravity center label:".strtoupper($numer)." ".$this->imagedir."/$out.png");
			//--------------------
			//generuje krzywą linię
			$w=exec($identify." -format \"%w\" \"".$this->imagedir."/$out.png\"");
			$h=exec($identify." -format \"%h\" \"".$this->imagedir."/$out.png\"");
			$skokw=$w%2==0?$w:$w-1;
			$skokw=$skokw/3;
			$l1="0,3";
			$l2=rand(0,$skokw).",".rand(2,$h-3);
			$l3=rand($skokw,2*$skokw).",".rand(2,$h-3);
			$l4=rand(2*$skokw,3*$skokw).",".rand(2,$h-3);
			$l5="$w,".rand(2,$h-3);
			exec($convert." {$this->imagedir}/$out.png -strokewidth $strokewidth -stroke \"$color\" -draw \"line $l1 $l2\" -draw \"line $l2 $l3\" -draw \"line $l3 $l4\" -draw \"line $l4 $l5\" {$this->imagedir}/$out.png");
			//--------------------
			$rettext=" <input type='text' name='$name' style='background:url({$this->imagedir}/$out.png);background-repeat:no-repeat;background-position:0px 2px;border:1px solid #c9c9c9;width:".($w+4)."px;height:".($h+0)."px;font-family:courierb;font-weight:bold;font-size:".$wysokosc."px;text-transform:uppercase;'/>";
			$_SESSION[$id]=$numer;
			//----------------------------------------------------------------------------------------------------
			//czyszcze stare numery
			//----------------------------------------------------------------------------------------------------
			$d = dir($this->imagedir);
			while(false!==($entry = $d->read()))
			{
				if($entry!="." && $entry!="..")
				{
				if((filemtime($this->imagedir."/".$entry)+240)<time())
					unlink($this->imagedir."/".$entry);
				}
				}
				return $rettext;
				}
		//----------------------------------------------------------------------------------------------------
		private function genalfanum()
		{
			$tableoflot=Array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
			foreach($tableoflot as $val)
			{
				$obrot=30-rand(0,60);
				$obrot=0;
				exec($this->strona->ustawienia->convert." -size 15x15 xc:none -fill gray -font courier -pointsize 15 -draw \"gravity center rotate $obrot text 0,0 '".strtoupper($val)."'\" $this->imagedir/$val.png");
			}
			return $rettext;
		}
		//----------------------------------------------------------------------------------------------------
		public function checknumber($numer,$id='number') 
		{
			if($_SESSION[$id]==$numer && $numer!="" && $_SESSION[$id]!="")
				return true;
			return false;
		}
		//----------------------------------------------------------------------------------------------------
		public function genref($iloscznakow,$typ='num',$powtorzenia=true) 
		{
			$rettext="";
			//pierwszy znak to nie moze byc 0
			$pierwszewejscie=true;
			//wybieram rodzaj znakow do losowania
			switch($typ)
			{
				case "numalf":
					$tableoflot=Array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
					break;
				//--------------------
				case "alf":
					$tableoflot=Array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','w','x','y','z');
					break;
				//--------------------
				case "num":
				default:
					$tableoflot=Array('0','1','2','3','4','5','6','7','8','9');
				break;
			}
			//--------------------
			if($iloscznakow>sizeof($tableoflot) && !$powtorzenia)return "iloscznakow wieksz od liczby dostępnych znaków ($iloscznakow>".sizeof($tableoflot).")";
			//--------------------
			for($i=0;$i<$iloscznakow;$i++)
			{
				$lot=rand(0,sizeof($tableoflot)-1);
				//--------------------
				while($pierwszewejscie && $tableoflot[$lot]=='0')
					$lot=rand(0,sizeof($tableoflot));
				$pierwszewejscie=false;
				//--------------------
				$rettext.=$tableoflot[$lot];
				//--------------------
				if(!$powtorzenia)
				{
					for($jk=$lot;$jk<sizeof($tableoflot)-1;$jk++)
						$tableoflot[$jk]=$tableoflot[$jk+1];
					unset($tableoflot[sizeof($tableoflot)-1]);
				}
			}
			return $rettext;
		} 
		//----------------------------------------------------------------------------------------------------		
    }//end class
}//end if
else
    die("Class exists: ".__FILE__);
?>