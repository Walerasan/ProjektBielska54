/**
 * @author Rafał Oleśkowicz LabNode.org
 */
function clearfield(nazwapola,defaultvalue)
{
	//funkcja czysci pole jezeli w srodku pola jest defaultvalue
	if(document.getElementById(nazwapola).value==defaultvalue)
		document.getElementById(nazwapola).value="";
}