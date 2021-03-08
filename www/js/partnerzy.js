/**
 * Rafał Oleśkowicz
 * Pole tekstowe z możliwością nadawania atrybutów tekstowi.
 */
if(!window.LabNode)
	window.LabNode={};
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy=function(idwarstwy,length)
{
	this.dlugosc=length;
	this.kierunek=true;
	this.warstwa=document.getElementById(idwarstwy);
	this.lock=true;
	this.prawo=false;
	this.lewo=false;
	this.timer;
};
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.przesunwprawo=function()
{
	if(this.warstwa.offsetLeft<0)
		this.warstwa.style.left = (this.warstwa.offsetLeft+3)+'px';
	else
		this.kierunek=false;
}
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.przesunwlewo=function()
{
	if(this.warstwa.offsetLeft>((this.dlugosc-855)*-1))
		this.warstwa.style.left = (this.warstwa.offsetLeft-3)+'px';
	else
		this.kierunek=true;
}
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.przesuwanieautomatyczne=function()
{
	if(!this.lock)
	{
		if(this.kierunek)
		{
			this.przesunwprawo();
		}
		else
		{
			this.przesunwlewo();
		}
	}
	else if(this.prawo)
	{
		this.przesunwprawo();
	}
	else if(this.lewo)
	{
		this.przesunwlewo();	
	}
}
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.przesunwpraworecznieon=function()
{
	this.prawo=true;
	this.lock=true;
	clearInterval(this.timer);
}
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.przesunwpraworecznieoff=function()
{
	this.prawo=false;
	this.timer=setTimeout(function(par1){return function(){par1.unlock();};}(this),5000);
}
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.przesunwleworecznieon=function()
{
	this.lewo=true;
	this.lock=true;
	clearInterval(this.timer);
}
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.przesunwleworecznieoff=function()
{
	this.lewo=false;
	this.timer=setTimeout(function(par1){return function(){par1.unlock();};}(this),5000);
}
//----------------------------------------------------------------------------------------------------
LabNode.partnerzy.prototype.unlock=function()
{
	this.lock=false;
}