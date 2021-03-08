/**
 * @author Rafał Oleśkowicz
 */
function createtextfield(c_name,value,place)
{
	var txtCtrl = document.createElement('input');
	txtCtrl.setAttribute('type','text');
	txtCtrl.setAttribute('name',c_name);
	txtCtrl.setAttribute('id','id_'+c_name);
	txtCtrl.setAttribute('size','30');
	txtCtrl.setAttribute('value',value);
	place.appendChild(txtCtrl);
}

function createbr(place)
{
	var brel = document.createElement('br');
	place.appendChild(brel);
}

function createlinkselfdestruction(action,text,place)
{
	var ael = document.createElement('a');
	var linkText = document.createTextNode(text);
	ael.appendChild(linkText);
	ael.title=text;
	//ael.href=action;
	ael.onclick=function(par1,par2){return function(){par1.parentNode.removeChild(par1);eval(par2);};}(ael,action);
	place.appendChild(ael);
}

function createfilefield(c_name,place)
{
	var txtCtrl = document.createElement('input');
	txtCtrl.setAttribute('type','file');
	txtCtrl.setAttribute('name',c_name);
	txtCtrl.setAttribute('id','id_'+c_name);
	place.appendChild(txtCtrl);
}

function createhiddenfield(c_name,value,place)
{
	var txtCtrl = document.createElement('input');
	txtCtrl.setAttribute('type','hidden');
	txtCtrl.setAttribute('name',c_name);
	txtCtrl.setAttribute('id','id_'+c_name);
	txtCtrl.setAttribute('value',value);
	place.appendChild(txtCtrl);
}