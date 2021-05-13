/******************************
Brower Independent functions...
Created By Suresh
Revision 2.0 - Updated to use Prototype.js
*****************************/

var userAgent = navigator.userAgent.toLowerCase();

var DOMtype = '';
if (document.getElementById)
{
	DOMtype = "std";
}
else if (document.all)
{
	DOMtype = "ie4";
}
else if (document.layers)
{
	DOMtype = "ns4";
}

var BrowserObjects = $A();

function fetchObjectById(idname, parentLevel, forceFetch)
{
	return $(idname);
}

function fetchObjectsByTagName(selTagName, parent)
{
	var classArray = new Array();
	selTagName.each(function(tagName) {
		elts = parent.getElementsByTagName(tagName);
		for (var i = 0; i < elts.length; ++i)
		{
			classArray.push(elts[i]);
		}
		//alert("classArray = "+classArray);
	});
	return classArray;
}

function fetchObjectsByName(selName, parentLevel, forceFetch)
{
	parents='';
	for(i=0;i<parentLevel;i++)
		parents+="parent.";
	var BrowserObjects = eval(parents+"document.getElementsBySelector('[name="+selName+"');");
	return BrowserObjects;
}

function getEvent(eventobj)
{

	if (!eventobj || ((userAgent.indexOf("msie") != -1) && (userAgent.indexOf("opera") == -1)))
	{
		window.event.returnValue = false;
		window.event.cancelBubble = true;
		return window.event;
	}
	else
	{
		eventobj.stopPropagation();
		eventobj.preventDefault();
		return eventobj;
	}
}

function getFrame(idname, Source, forceFetch)
{
	if (forceFetch || typeof(BrowserObjects[idname]) == "undefined")
	{
		switch (DOMtype)
		{
			case "std":
			{
				BrowserObjects[idname] = eval(Source+'.frames[idname]');
			}
			break;

			case "ie4":
			{
				BrowserObjects[idname] = eval(Source+'.all[idname]');
			}
			break;

			case "ns4":
			{
				BrowserObjects[idname] = eval(Source+'.layers[idname]');
			}
			break;
		}
	}
	return BrowserObjects[idname];
}

function fetchObjectsByClassName(className)
{
	var classArray = document.getElementsByClassName(className);
	return classArray;
}

function fetchObjectByAttributeValue(attributeName,attributeValue,cellName)
{
	if (cellName=='')
		var elts = document.getElementsByTagName('*');
	else
		var elts = document.getElementsByTagName(cellName);

	var classArray = new Array();
	for (var i = 0; i < elts.length; ++i)
	{
		if (elts[i].getAttribute(attributeName) && elts[i].getAttribute(attributeName)==attributeValue)
		{
			classArray.push(elts[i]);
		}
	}
	return classArray;
}

function fetchObjectsByAttribute(attributeName, cellName)
{
	if (cellName=='')
		var elts = document.getElementsByTagName('*');
	else
		var elts = document.getElementsByTagName(cellName);

	var classArray = new Array();
	for (var i = 0; i < elts.length; ++i)
	{
		if (elts[i].getAttribute(attributeName))
		{
			classArray.push(elts[i]);
		}
	}
	return classArray;
}


function createNode(tagName,TargetPath)
{
	if (!(TargetPath)||(TargetPath==''))
	{
		TargetPath='document';
	}
	else
	{
		TargetPath=TargetPath+".document";
	}
	switch (DOMtype)
	{
		case "std":
		{
			var newObject=eval(TargetPath+".createElement(tagName)");
		}
		break;

		case "ie4":
		{
			var newObject=eval(TargetPath+".createElement(tagName)");
		}
		break;

		case "ns4":
		{
			var newObject=eval(TargetPath+".createElement(tagName)");
		}
		break;
	}
	return newObject;
}

function getSize() {
	height=parseInt($(document.body).getHeight())-224;
	width=parseInt($(document.body).getWidth())-20;

	var size=new Array(height, width);
	return size;
}



function setAttributes()
{
	if (arguments.length<2)
		alert("Minimum 2 arguments required!");
	else
	{
		Source=arguments[0];
		for(var i=1;i<arguments.length;i++)
		{
			var property=arguments[i];
			var propertyList=property.split("=");
			if (propertyList[0]=="style")
			{
				styleProp=propertyList[1].split(";");
				for(var j=0;j<styleProp.length;j++)
				{
					var styleList=styleProp[j].split(":");
					if (styleList[0]&&styleList[1])
						eval("Source.style."+styleList[0]+"=\""+styleList[1]+"\";");
				}
			}
			else if (propertyList[0]=="class")
			{
				Source.className=propertyList[1];
			}
			else
			{
				Source.setAttribute(propertyList[0],propertyList[1])
			}
		}
	}
	return Source;
}

function attachObject(Target,Source)
{
	Target.appendChild(Source);
	return Target;
}


function addEvent(obj,eventName,eventBody)
{
	obj.setAttribute(eventName,new Function(eventBody));
	return obj;
}

function addOptionsList(Target,Source)
{
	for(var y=0;y<Source.options.length;y++)
	{
					var defaultOption=createNode("OPTION");
		defaultOption.text=Source.options[y].text;
		defaultOption.value=Source.options[y].value;
		Target.add(defaultOption);
	}
	if (Source.selectedIndex)
	{
		Target.selectedIndex=Source.selectedIndex;
	}
	return Target;
}

function fetchAllInputFields()
{
	var classArray = new Array();
	var inputs = document.dataForm.elements;
	(inputs.length).times ( function(i)
	{
		if (inputs[i].style.display != 'none' && inputs[i].type!='hidden' && inputs[i].tagName!='FIELDSET')
		{
			classArray.push(inputs[i]);
		}
	});
	var inputs = fetchObjectByAttributeValue('type','image','INPUT');
	(inputs.length).times ( function(i)
	{
		if (inputs[i].style.display != 'none' && inputs[i].type!='hidden' && inputs[i].tagName!='FIELDSET')
		{
			classArray.push(inputs[i]);
		}
	});
	var selects = document.getElementsByTagName('SELECT');
	(selects.length).times ( function(j)
	{
		if (selects[j].style.display != 'none')
		{
			classArray.push(selects[j]);
		}
	});
	return classArray;
}

function fetchInputFieldsInTable(attributeName, attributeValue)
{
	var classArray = new Array();
	if (attributeName) {
		if (attributeValue)
			var tables = fetchObjectByAttributeValue(attributeName, attributeValue, 'TABLE');
		else
			var tables = fetchObjectsByAttribute(attributeName, 'TABLE');
	}
	else {
		var tables = fetchObjectByTagName('TABLE');
	}


	tables.each( function(table) {
		(table.rows.length).times( function(i) {
			(table.rows[i].cells.length).times( function(x) {
				if (table.rows[i].cells[x].childNodes[0].tagName == 'INPUT' || table.rows[i].cells[x].childNodes[0].tagName == 'SELECT') {
					classArray.push(table.rows[i].cells[x].childNodes[0]);
				}
			});
		});
	});

	return classArray;
}
