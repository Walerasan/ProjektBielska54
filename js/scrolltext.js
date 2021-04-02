/**
 * Rafał Oleśkowicz
 * przesuwający się text
 * $scrolltext="<script>var scrolltextobj=new LabNode.scrolltext('Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, Pszczyna, Czarków, Kobiór, '); scrolltextobj.pokaz()</script>";
 * <div class='scrolltext'><div class='scrolltextodstep'>$scrolltext</div></div>
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.scrolltext=function(tresc)
{
	this.tresc=tresc;
	this.div=document.currentScript.parentNode;
	this.contentdiv;
	this.triger=true;
}
//----------------------------------------------------------------------------------------------------
LabNode.scrolltext.prototype.pokaz=function()
{
	this.contentdiv = document.createElement('div');
	this.contentdiv.setAttribute('name','scrolltext1');
	this.contentdiv.setAttribute('id','id_scrolltext1');
	this.contentdiv.setAttribute('style','position:absolute;white-space:nowrap;height:30px;overflow:hidden;left:-100px;');
	this.contentdiv.innerHTML=this.tresc;
	this.div.appendChild(this.contentdiv);
	this.scrollleft();
}
//----------------------------------------------------------------------------------------------------
LabNode.scrolltext.prototype.scrollleft=function()
{
	var szerokosc=Math.max(this.contentdiv.clientWidth,this.contentdiv.offsetWidth);
	var szerokoscwyswietlacza=Math.max(this.div.clientWidth,this.div.offsetWidth);
	
	if(this.contentdiv.offsetLeft<szerokoscwyswietlacza-szerokosc) this.triger=false;
	if(this.contentdiv.offsetLeft>0)this.triger=true;
	//--------------------
	if(this.triger)
		this.contentdiv.style.left=(this.contentdiv.offsetLeft-1)+"px";
	else
		this.contentdiv.style.left=(this.contentdiv.offsetLeft+1)+"px";
	//--------------------
	setTimeout(function(par1){return function(){par1.scrollleft();};}(this),20);
}
//----------------------------------------------------------------------------------------------------