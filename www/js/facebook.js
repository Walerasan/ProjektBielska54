/**
 * @author Rafał Oleśkowicz LabNode.org
 */
function movediv()
{
	var xpos=0;
	aktualnyx=document.getElementById("facebook").offsetLeft;
	if(aktualnyx+4<xpos)
		setTimeout("ustawpozycje("+(aktualnyx+4)+")",1);
	else
	{
		document.getElementById("facebook").onmouseout=movedivback;
		document.getElementById("facebook").onmouseover=null;
	}
}
function movedivback()
{
	var xpos=-210;
	aktualnyx=document.getElementById("facebook").offsetLeft;
	if(aktualnyx>xpos)
		setTimeout("ustawpozycjeback("+(aktualnyx-4)+")",1);
	else
	{
		document.getElementById("facebook").onmouseout=null;
		document.getElementById("facebook").onmouseover=movediv;
	}
}
function ustawpozycje(x)
{
	document.getElementById("facebook").style.left=x+"px";
	movediv();
}
function ustawpozycjeback(x)
{
	document.getElementById("facebook").style.left=x+"px";
	movedivback();
}