/**
 * @author Rafal Oleskowicz
 */
function setopticalwhite50(iddiv)
{
	//document.getElementById(iddiv).style.background="url(./media/desktop/przezrocze.png)";
	document.getElementById(iddiv).style.background="#3A709030";
}
function setoptical0(iddiv,kolor)
{
	if(typeof kolor=='undefined')kolor='none';
	document.getElementById(iddiv).style.background=kolor;
}