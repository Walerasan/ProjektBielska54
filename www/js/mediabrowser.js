/**
 * Rafał Oleśkowicz
 * Pole tekstowe z możliwością nadawania atrybutów tekstowi.
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser=function(ref)
{
	this.zaciemnienieedytora;
	this.warstwanosnika;
	this.ref=ref;
	this.field_name;
	this.win;
	this.type;
}
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.katalog=function(field_name,win,type)
{
	this.type=type;
	this.field_name=field_name;
	this.win=win;
	//wlanczam zaciemnienie
	this.zaciemnienieedytora=this.warstwazaciemnieniaon();
	this.warstwanosnika=this.warstwanosnikaon();
	//wyswietlam galerie do wyboru
	this.ajax();//aktywuje obiekt ajax
	this.wykonajakcje("pictureslist");
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.warstwanosnikaon=function()
{
	//tworze diva na cala szerokosc strony z zaciemnieniem
	//tworzymy warstwe na ktorej to bedzie lezec
	var nosnikedytora= document.createElement('div');
	nosnikedytora.setAttribute('id','nosnikedytora');
	nosnikedytora.setAttribute('style', 'color:white;text-align:center;width:'+this.documentwidth()+'px;height:'+this.documentheight()+'px;position:absolute;left:0px;top:0px;z-index:200000;');
	//wstawiam div do body
	document.body.appendChild(nosnikedytora);
	return nosnikedytora;
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.warstwazaciemnieniaon=function()
{
	//tworze diva na cala szerokosc strony z zaciemnieniem
	//tworzymy warstwe na ktorej to bedzie lezec
	var zaciemnienieedytora= document.createElement('div');
	zaciemnienieedytora.setAttribute('id','zaciemnienieedytora');
	zaciemnienieedytora.setAttribute('style', 'color:white;text-align:center;width:'+this.documentwidth()+'px;height:'+this.documentheight()+'px;position:absolute;left:0px;top:0px;background:black;z-index:100000;opacity:0.85;filter:alpha(opacity=85);moz-opacity:0.85;');
	//wstawiam div do body
	document.body.appendChild(zaciemnienieedytora);
	document.body.scrollTop = document.documentElement.scrollTop = 0;//lecę na początek strony.
	return zaciemnienieedytora;
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.anulujzaciemnienie=function()
{
	//usuwam warstwy
	this.usunwarstwe(this.warstwanosnika);
	this.usunwarstwe(this.zaciemnienieedytora);
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.usunwarstwe=function(uchwyt)
{
	var rodzic=uchwyt.parentNode;
	rodzic.removeChild(uchwyt);
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.wykonajakcje=function(akcja)
{
	var parametry="";
	for (var i=1; i < arguments.length; i++)
		parametry=parametry+"&par"+i+"="+arguments[i];
	var tosend="typ="+this.type+"&ref="+this.ref+"&id="+this.id+parametry;
	//jezeli udalo sie stworzyc polaczenia ajax
	if(this.XMLHttp)
	{
		//definiuje funkcje ktora odbierze dane
		this.XMLHttp.onreadystatechange=function(par1){return function(){par1.zaladujodpowiedz(par1);};}(this);
		this.XMLHttp.open("POST","mediabrowser,raw,"+akcja,true);
		this.XMLHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		this.XMLHttp.setRequestHeader("Content-length", tosend.length);
		this.XMLHttp.setRequestHeader("Connection", "close");
		//wysylam dane
		this.XMLHttp.send(tosend);
	}
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.zaladujodpowiedz=function(inobjAjax)
{
	if(inobjAjax.XMLHttp.readyState==2)
	{//wysyla dane do serwera
		//eval("inobjAjax.Object."+inobjAjax.funkcjasend+"()");
	}
	if(inobjAjax.XMLHttp.readyState==3)
	{//odbiera dane z serwera
		//eval("inobjAjax.Object."+inobjAjax.funkcjaget+"()");
	}
	if(inobjAjax.XMLHttp.readyState==4)
	{//zakaczyl odbieranie danych
		if(inobjAjax.XMLHttp.responseText!="blad")
		{
			//inobjAjax.wstaw(inobjAjax.XMLHttp.responseText.split("#@#"));
			inobjAjax.wstaw(inobjAjax.XMLHttp.responseText);
		}
		else
			alert("Błąd");
	}
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.wstaw=function(dane)
{
	var trescdowstawienia="<div style=''>";
	trescdowstawienia=trescdowstawienia+dane;
	trescdowstawienia=trescdowstawienia+"</div>";
	this.warstwanosnika.innerHTML=trescdowstawienia;
};
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.wstawzdjecie=function(zdj,tytul,nazwa)
{
	this.usunwarstwe(this.warstwanosnika);
	this.usunwarstwe(this.zaciemnienieedytora);
	//--------------------
	var pole=this.field_name.split("-");
	var poletabofname=pole[0].split("_");
	var poleopisu=poletabofname[0]+"_"+(parseInt(poletabofname[1])+1);
	var poletytulu=poletabofname[0]+"_"+(parseInt(poletabofname[1])+2);
	var szerokosc=poletabofname[0]+"_"+(parseInt(poletabofname[1])+3);
	//--------------------
	if(tytul=="")tytul=nazwa;
	//--------------------
	this.win.document.getElementById(this.field_name).value="./media/mediabrowser/mini/"+zdj;
	this.win.document.getElementById(poletytulu).value=nazwa;
	this.win.document.getElementById(poleopisu).value=tytul;
	this.win.document.getElementById(szerokosc).value="582";
};
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.documentheight=function()
{
	return Math.max( document.body.scrollHeight, document.body.offsetHeight,document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight );
}
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.documentwidth=function()
{
	return Math.max( document.body.scrollWidth, document.body.offsetWidth,document.documentElement.clientWidth, document.documentElement.scrollWidth, document.documentElement.offsetWidth);
}
//----------------------------------------------------------------------------------------------------
LabNode.mediabrowser.prototype.ajax=function()
{
	this.XMLHttp=false;
	//--------------------
	if (window.XMLHttpRequest)
	{ // Mozilla, Safari,...
		this.XMLHttp = new XMLHttpRequest();
		if (this.XMLHttp.overrideMimeType)
			this.XMLHttp.overrideMimeType('text/xml');
	}
	else if(window.ActiveXObject)
	{ // IE
		try
		{
			this.XMLHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				this.XMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e){}
		}
	}
	if (!this.XMLHttp)
	{
		alert('Ajax Error - nie mogę zainisjowac obsługi Asynchronous Javascript');
		return false;
	}
};
