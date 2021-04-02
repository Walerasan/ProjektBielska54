/**
 * @author Rafal
 */
if(!window.LabNode)
  window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly=function()
{
	var o;
	var p;
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.szerokoscprzegladarki=function()
{
	if( typeof( window.innerWidth ) == 'number' )
		myHeight = window.innerWidth;
	else
		if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
			myHeight = document.documentElement.clientWidth;
		else
			if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
				myHeight = document.body.clientWidth;
	return myHeight;
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.wysokoscprzegladarki=function()
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
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.szerokoscdokumentu=function()
{
	return Math.max(document.documentElement["clientWidth"], document.body["scrollWidth"], document.documentElement["scrollWidth"], document.body["offsetWidth"], document.documentElement["offsetWidth"]);
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.wysokoscdokumentu=function()
{
	return Math.max(document.documentElement["clientHeight"], document.body["scrollHeight"], document.documentElement["scrollHeight"], document.body["offsetHeight"], document.documentElement["offsetHeight"]);
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.scrolltop=function()
{
	var iebody=(document.compatMode && document.compatMode != "BackCompat")? document.documentElement : document.body;
	var dsoctop=document.all? iebody.scrollTop : pageYOffset;
	//var dsocleft=document.all? iebody.scrollLeft : pageXOffset
	return dsoctop;
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.okno=function()
{
	var szerokoscokna=600;
	var wysokoscokna=100;
	//obliczam srodek okna
	var srodek=(this.szerokoscprzegladarki()/2)-(szerokoscokna/2);
	var srodek2=(this.wysokoscprzegladarki()/2)-(wysokoscokna/2)+this.scrolltop();
	//funkcja tworzy dynamiczny div i go ustawia na srodku widoku
	var divek=document.createElement('div');
	divek.setAttribute('id','oknos');
	divek.setAttribute('style', 'position:absolute;z-index:1000;width:'+szerokoscokna+'px;overflow:hidden;background:#ccc;left:'+srodek+'px;top:'+srodek2+'px;');
	document.body.appendChild(divek);
	
	//dodaje X jako zamknij
	var xclose=document.createElement('a');
	xclose.setAttribute('id','xclose');
	xclose.setAttribute('style','float:right;margin:5px;');
	xclose.setAttribute('href','#');
	xclose.innerHTML="X";
	xclose.onclick=function(par1){return function(){par1.zamknij();return false;};}(this);//przesylam zdarzenie i wykorzystuje wartosc alt do przeslania id dla obiektu
	divek.appendChild(xclose);
	return divek;
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.elementp=function(zawartosc,klasa)
{
	var pele=document.createElement('p');
	var formData = new FormData();
	formData.append('par1', zawartosc);
	ajax.pobierzzawartosc(klasa+",raw,szczegoly",pele,formData);//adres,miejsce,formData
	pele.setAttribute('style', 'margin:0px;text-align:left;font-size:10pt;font-weight:bold;padding:10px;');
	return pele;
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.show=function(wartosc,klasa)
{
	//jezeli okno otwarte to zamykamy je i tworzymy nowe
	if(this.o)
		this.zamknij();
	//--------------------
	this.o=this.okno();
	this.p=this.elementp(wartosc,klasa);
	this.o.appendChild(this.p);
};
//----------------------------------------------------------------------------------------------------
LabNode.szczegoly.prototype.zamknij=function()
{
	this.o.removeChild(this.p);
	document.body.removeChild(this.o);
	delete this.p;
	delete this.o;
};
//----------------------------------------------------------------------------------------------------
var szczegoly=new LabNode.szczegoly;