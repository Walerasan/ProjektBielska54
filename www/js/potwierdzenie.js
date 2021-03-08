/**
 * @author Rafał Oleśkowicz
 * użycie: <a href='javascript:potwierdzenie("Czy napewno usunąć?","usunmenu",window);'>usuń</a>
 * użycie: <a href='#' onclick='potwierdzenie("Czy napewno usunąć?","usunmenu",window);'>usuń</a>
 */
function potwierdzenie(pytanie,akcja,ramkaokna)
{
	var answer = confirm(pytanie);
	if(answer)
		ramkaokna.location=akcja;
	return void(null);
}

function pokazpotwierdzenie(warstwanosna,pytanietext,akcjatext)
{

	//ustalam pozycje
	var poztop=(Math.round((wysokoscbody()-130)/2)+(getScrollY()))+"px";
	var pozleft=Math.round((szerokoscbody()-410)/2)+"px";
	var newdiv=document.createElement('div');
	var idwarstwy='potwierdzenie';
	newdiv.setAttribute('id',idwarstwy);
	newdiv.setAttribute('style','width:410px;height:130px;position:absolute;z-index:200;top:'+poztop+';left:'+pozleft+';');
	newdiv.style.width='410px';
	newdiv.style.height='130px';
	newdiv.style.position='absolute';
	newdiv.style.zIndex='200';
	newdiv.style.top=poztop;
	newdiv.style.left=pozleft;
	document.body.appendChild(newdiv);
	var newdiv2=document.createElement('div');
	newdiv2.setAttribute('id','zawartosc');
	newdiv.appendChild(newdiv2);
	//wgrywam flashe
	swfobject.embedSWF("./media/desktop/potwierdzenie.swf","zawartosc", "410", "130", "9.0.0","./media/desktop/expressInstall.swf",{pytanie:pytanietext,warstwa:idwarstwy,akcja:akcjatext},{wmode:"transparent",quality:"high"},{id:"zawartosc"});
}
function zamknijpotwierdzenie(warstwa)
{
	document.getElementById(warstwa).removeChild(document.getElementById('zawartosc'));
	document.body.removeChild(document.getElementById(warstwa));
}

function wykonajpotierdzenie(warstwa,akcja)
{
	zamknijpotwierdzenie(warstwa);
	window.location=akcja;
}

function wysokoscbody()
{
	if( typeof( window.innerWidth ) == 'number' )
		myHeight = window.innerHeight;
	else
		if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
			myHeight = document.documentElement.clientHeight;
		else
			if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
			        myHeight = document.body.clientHeight;
	return myHeight; 
}
function szerokoscbody()
{
	if( typeof( window.innerWidth ) == 'number' )
		myWidth=window.innerWidth;
	else
		if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
			myWidth=document.documentElement.clientWidth; 
		else
			if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
			        myWidth=document.body.clientWidth;
	return myWidth;
}
function getScrollY()
{
	  var scrOfY = 0;
	  if( typeof( window.pageYOffset ) == 'number' )
	  {
	    //Netscape compliant
	    scrOfY = window.pageYOffset;
	  }
	  else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) )
	  {
	    //DOM compliant
	    scrOfY = document.body.scrollTop;
	  }
	  else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) )
	  {
	    //IE6 standards compliant mode
	    scrOfY = document.documentElement.scrollTop;
	  }
	  return scrOfY;
}