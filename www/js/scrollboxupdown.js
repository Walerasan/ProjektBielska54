/**
 * Rafał Oleśkowicz
 * Pole tekstowe z możliwością nadawania atrybutów tekstowi.
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.scrollboxupdown=function(id)
{
	this.scrollupbackground='url(./media/desktop/wydarzysieup.gif)';
	this.scrolldownbackground='url(./media/desktop/wydarzysiedown.gif)';
	this.skok=10;
	this.predkoscautoscroll=50;//w ms
	this.id=id;
	this.wysokoscstrzalki=20;
	this.szerokoscstrzalki=280;
	this.myszwcisnieta=false;
	this.DOMmnoznik=1;//to się ustawi wtedy gdy mamy scroll w wartosciach jednostowych (np. ff)
	
	this.divobject=document.getElementById(this.id);
	//pobieram zawartość
	var zawartosc=this.divobject.innerHTML;
	//czyszczę div
	this.divobject.innerHTML="";
	//nadaje mu relatyvna pozycję
	this.divobject.style.position="relative";
	
	//tworzę div ramki przysłony
	this.divprzyslony=document.createElement('div');
	this.divprzyslony.setAttribute('id','scrollboxrunediv');
	this.divprzyslony.setAttribute('style', 'width:100%;position:relative;overflow:hidden;height:'+(this.divobject.offsetHeight-(2*this.wysokoscstrzalki))+'px;');
	this.divprzyslony.style.width="100%";
	this.divprzyslony.style.height=(this.divobject.offsetHeight-(2*this.wysokoscstrzalki))+"px";
	this.divprzyslony.style.position="relative";
	this.divprzyslony.style.overflow="hidden";
	
	//tworzę div do przesuwania
	this.divdoprzesuwania=document.createElement('div');
	this.divdoprzesuwania.setAttribute('id','scrollboxrunediv');
	this.divdoprzesuwania.setAttribute('style', 'width:'+parseInt(this.divobject.style.width)+'px;position:absolute;');
	this.divdoprzesuwania.style.width=parseInt(this.divobject.offsetWidth)+"px";
	this.divdoprzesuwania.style.position="absolute";
	//dodaje zawartość do nowego diva
	this.divdoprzesuwania.innerHTML=zawartosc;
	this.divprzyslony.appendChild(this.divdoprzesuwania);
	
	//włączam obsługę rolki myszy
	if (window.attachEvent) //if IE (and Opera depending on user setting ie<9)
		this.divobject.attachEvent("onmousewheel",function(par1){return function(){var e=arguments[0] || event;return par1.scroll(e);};}(this));
	else if (window.addEventListener) //WC3 browsers
	{
		this.divobject.addEventListener("DOMMouseScroll",function(par1){return function(){var e=arguments[0] || event;return par1.scroll(e);};}(this),false);
		this.divobject.addEventListener("mousewheel",function(par1){return function(){var e=arguments[0] || event;return par1.scroll(e);};}(this),false);
		this.DOMmnoznik=10;
	}
	//tworze div górnej strzałki
	this.strzalkagora=document.createElement('div');
	this.strzalkagora.setAttribute('id','strzalkagora');
	this.strzalkagora.setAttribute('style', 'height:'+this.wysokoscstrzalki+'px;width:'+this.szerokoscstrzalki+'px;position:relative;background:'+this.scrollupbackground+';background-repeat:no-repeat;top:0px;left:0px;cursor:hand;cursor:pointer;');
	this.strzalkagora.style.width=this.szerokoscstrzalki+"px";
	this.strzalkagora.style.height=this.wysokoscstrzalki+"px";
	this.strzalkagora.style.left="0px";
	this.strzalkagora.style.top="0px";
	this.strzalkagora.style.position="relative";
	this.strzalkagora.style.background=this.scrollupbackground;
	this.strzalkagora.style.backgroundRepeat='no-repeat';
	this.strzalkagora.style.cursor='hand';
	this.strzalkagora.onmousedown=function(par1){return function(){par1.startautoscroll();return par1.up();};}(this);
	this.strzalkagora.onmouseup=function(par1){return function(){return par1.stopautoscroll();};}(this);
	
	//tworze div dolnej strzałki
	this.strzalkadol=document.createElement('div');
	this.strzalkadol.setAttribute('id','strzalkadol');
	this.strzalkadol.setAttribute('style', 'height:'+this.wysokoscstrzalki+'px;width:'+this.szerokoscstrzalki+'px;position:relative;background:'+this.scrolldownbackground+';background-repeat:no-repeat;left:0px;bottom:0px;cursor:hand;cursor:pointer;');
	this.strzalkadol.style.width=this.szerokoscstrzalki+"px";
	this.strzalkadol.style.height=this.wysokoscstrzalki+"px";
	this.strzalkadol.style.left="0px";
	this.strzalkadol.style.bottom="0px";
	this.strzalkadol.style.position="relative";
	this.strzalkadol.style.background=this.scrolldownbackground;
	this.strzalkadol.style.backgroundRepeat='no-repeat';
	this.strzalkadol.style.cursor='hand';
	this.strzalkadol.onmousedown=function(par1){return function(){par1.startautoscroll();return par1.down();};}(this);
	this.strzalkadol.onmouseup=function(par1){return function(){return par1.stopautoscroll();};}(this);
	
	//dodaje wszystko do strony
	this.divobject.appendChild(this.strzalkagora);
	this.divobject.appendChild(this.divprzyslony);
	this.divobject.appendChild(this.strzalkadol);
};
//----------------------------------------------------------------------------------------------------
LabNode.scrollboxupdown.prototype.scroll=function(e)
{
	if(!e)e=window.event;
	//alert(e);
	var targ=e.target?e.target:e.srcElement;
	//odszukuje mojego diva - bo mogłem skrolowac np. na tekście lub boldzie i tak dalej
	while(targ.nodeName.toUpperCase()!="BODY" && targ!=this.divdoprzesuwania)
		targ=targ.parentNode;
	if(targ!=this.divdoprzesuwania)return true;
	
	var delta=0;
	
	if (e.wheelDelta)
        delta = e.wheelDelta;
	else if(e.detail)
        delta = -e.detail;
	
	delta=delta*-1;//odwrotność rolki myszy
	
	if(Math.abs(delta)>0 && Math.abs(delta)<10)
		delta=delta*this.DOMmnoznik;
	targ.style.top=targ.offsetTop+delta+'px';
	
	if(targ.offsetTop<(this.divdoprzesuwania.offsetHeight-this.divprzyslony.offsetHeight)*-1)
		targ.style.top=(((this.divdoprzesuwania.offsetHeight-this.divprzyslony.offsetHeight)*-1)-2)+"px";
	//max w górę czyli 0
	if(targ.offsetTop>0)
		targ.style.top="0px";
	
	e.cancelBubble = true;
	if(e.stopPropagation) e.stopPropagation();
	if(e.preventDefault)e.preventDefault();
	return false;
};
//----------------------------------------------------------------------------------------------------
LabNode.scrollboxupdown.prototype.down=function()
{
	if(!this.myszwcisnieta)return false;//gdy puściłem mysz
	if((this.divdoprzesuwania.offsetTop*-1)<this.divdoprzesuwania.offsetHeight-this.divprzyslony.offsetHeight)
		this.divdoprzesuwania.style.top=this.divdoprzesuwania.offsetTop-this.skok+"px";
	setTimeout(function(par1){return function(){return par1.down();};}(this),this.predkoscautoscroll);
	return false;	
};
//----------------------------------------------------------------------------------------------------
LabNode.scrollboxupdown.prototype.up=function()
{
	if(!this.myszwcisnieta)return false;//gdy puściłem mysz
	if((this.divdoprzesuwania.offsetTop*-1)>0)
		this.divdoprzesuwania.style.top=this.divdoprzesuwania.offsetTop+this.skok+"px";
	setTimeout(function(par1){return function(){return par1.up();};}(this),this.predkoscautoscroll);
	return false;
};
//----------------------------------------------------------------------------------------------------
LabNode.scrollboxupdown.prototype.stopautoscroll=function(e)
{
	this.myszwcisnieta=false;
};
//----------------------------------------------------------------------------------------------------
LabNode.scrollboxupdown.prototype.startautoscroll=function(e)
{
	this.myszwcisnieta=true;
};
//----------------------------------------------------------------------------------------------------