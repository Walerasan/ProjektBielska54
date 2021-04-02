/**
 * Rafał Oleśkowicz
 * Galeria zdjęć
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.galeria=function(ramka,ilosczdjec,idg)
{
	this.ramka=ramka;
	this.ilosczdjec=ilosczdjec;
	this.idg=idg;
	this.pozycjazdjecia=0;
	this.pozycjaramki=0;
	this.poprzedniapozycjaramki=0;
	setTimeout(function(thisObj){thisObj.ustawonlikiminiaturek();},100,this);
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.setpic=function(nr,nrr)
{
	this.pozycjazdjecia=nr;
	this.pozycjaramki=nrr;
	this.setactivmin();
	this.wstaw(this.pozycjazdjecia);
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.nastepne=function()
{
	if(this.pozycjazdjecia<this.ilosczdjec-1)
	{
		this.pozycjazdjecia++;
		if(this.pozycjaramki<4 && this.pozycjaramki<this.ilosczdjec)
			this.pozycjaramki++;
		else //przesuwam miniaturki
			this.przesunminiaturki();	
	}
	this.setactivmin();
	this.wstaw(this.pozycjazdjecia);
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.poprzednie=function()
{
	if(this.pozycjazdjecia>0)
	{
		this.pozycjazdjecia--;
		if(this.pozycjaramki>0)
			this.pozycjaramki--;
		else  //przesuwam miniaturki
			this.przesunminiaturki();
	}
	this.setactivmin();
	this.wstaw(this.pozycjazdjecia);
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.napoczatek=function()
{
	this.pozycjazdjecia=0;
	this.pozycjaramki=0;
	this.setactivmin();
	this.wstaw(this.pozycjazdjecia);
	this.przesunminiaturki();
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.nakoniec=function()
{
	this.pozycjazdjecia=this.ilosczdjec-1;
	if(this.ilosczdjec>5)
		this.pozycjaramki=4;
	else
		this.pozycjaramki=this.ilosczdjec;
	this.setactivmin();
	this.wstaw(this.pozycjazdjecia);
	this.przesunminiaturki();
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.wstaw=function(nrz)
{
	document.getElementById(this.ramka).innerHTML="<a href='"+document.getElementById("zdjecie"+nrz).href+"' rel='lightbox[galeria"+this.idg+"]'><img src='"+document.getElementById("zdjecie"+nrz).href+"' style='width:445px;'/></a>";
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.setactivmin=function()
{
	if(this.pozycjaramki<5 && this.pozycjaramki<this.ilosczdjec)
	{
		if(this.poprzedniapozycjaramki>=0)
			document.getElementById("miniaturka_"+this.idg+"_"+this.poprzedniapozycjaramki).style.border="1px solid white";
		document.getElementById("miniaturka_"+this.idg+"_"+this.pozycjaramki).style.border="1px solid red";
	}
	this.poprzedniapozycjaramki=this.pozycjaramki;
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.przesunminiaturki=function()
{
	for(var i=0;i<5;i++)
	{
		document.getElementById("miniaturka_"+this.idg+"_"+i).src=document.getElementById("miniaturka_link_"+this.idg+"_"+(this.pozycjazdjecia-this.pozycjaramki+i)).href;
	}
	this.ustawonlikiminiaturek();
};
//----------------------------------------------------------------------------------------------------
LabNode.galeria.prototype.ustawonlikiminiaturek=function()
{
	if(document.readyState=="complete" || document.readyState=="interactive")
	{
		for(var i=0;i<5;i++)
		{
			var nrz=this.pozycjazdjecia-this.pozycjaramki+i;
			document.getElementById("miniaturka_"+this.idg+"_"+i).onclick=function(par1,par2,par3){return function(){par1.setpic(par2,par3);};}(this,nrz,i);
		}
	}
	else
		setTimeout(function(thisObj){thisObj.ustawonlikiminiaturek();},100,this);
};
//----------------------------------------------------------------------------------------------------