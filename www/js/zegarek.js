/**
 * Rafał Oleśkowicz
 * Pole tekstowe z możliwością nadawania atrybutów tekstowi.
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.zegarek=function(id)
{
	this.id=id;
	this.divobject=document.getElementById(this.id);
	this.tresc=this.divobject.innerHTML;
	this.divobject.innerHTML="";
	//tworzę nową warstwę z zegarkiem
	this.divzegarka=document.createElement('div');
	this.divzegarka.setAttribute('id','divzegarka');
	this.divzegarka.setAttribute('style', 'width:100%;position:relative;');
	this.divzegarka.style.width="100%";
	this.divzegarka.style.position="relative";
	this.divobject.appendChild(this.divzegarka);
	this.pobierzgodzine();
	setInterval(function(par1){return function(){par1.pobierzgodzine();};}(this),1000);
};
//----------------------------------------------------------------------------------------------------
LabNode.zegarek.prototype.pobierzgodzine=function()
{
	var myDate=new Date();
	var miesiac=(myDate.getMonth()+1);
	if(miesiac<10)miesiac="0"+miesiac;
	var dzien=(myDate.getDate());
	if(dzien<10)dzien="0"+dzien;
	var godzina=myDate.getHours();
	if(godzina<10)godzina="0"+godzina;
	var minuta=myDate.getMinutes();
	if(minuta<10)minuta="0"+minuta;
	var sekunda=myDate.getSeconds();
	if(sekunda<10)sekunda="0"+sekunda;
	
	this.divzegarka.innerHTML=this.tresc+" "+myDate.getFullYear()+"-"+miesiac+"-"+dzien+" "+godzina+":"+minuta+":"+sekunda;
};