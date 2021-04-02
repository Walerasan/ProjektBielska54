/**
 * @author LabNode - Rafał Oleskowicz
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.ajax={};
//----------------------------------------------------------------------------------------------------
LabNode.ajax=function()
{
	var XMLHttp;
	var wynik;
	this.objajax();
};
//----------------------------------------------------------------------------------------------------
LabNode.ajax.prototype.objajax=function()
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
//----------------------------------------------------------------------------------------------------
LabNode.ajax.prototype.pobierzzawartosc=function(adres,miejsce,formData)
{
	this.wynik="";
	if(this.XMLHttp && adres!="")
	{
		//jeżeli chciał bym to ręcznie składać dla post
		//var formData = new FormData();
		//formData.append('CustomField', 'This is some extra data');
		//--------------------
		//definiuje funkcje ktora odbierze dane
		this.XMLHttp.onreadystatechange=function(par1,par2){return function(){par1.statechange(par1,par2);};}(this,miejsce);
		this.XMLHttp.open("POST",adres,true);
		//wysylam dane
		this.XMLHttp.send(formData);
	}
};
//----------------------------------------------------------------------------------------------------
LabNode.ajax.prototype.statechange=function(obj,zwrot)
{
	if(obj.XMLHttp.readyState==2)
	{//wysyla dane do serwera
		//eval("inobjAjax.Object."+inobjAjax.funkcjasend+"()");
	}
	if(obj.XMLHttp.readyState==3)
	{//odbiera dane z serwera
		//eval("inobjAjax.Object."+inobjAjax.funkcjaget+"()");
	}
	if(obj.XMLHttp.readyState==4)
	{//zakaczyl odbieranie danych
		zwrot.innerHTML=obj.XMLHttp.responseText;
	}
};
//----------------------------------------------------------------------------------------------------
var ajax=new LabNode.ajax();