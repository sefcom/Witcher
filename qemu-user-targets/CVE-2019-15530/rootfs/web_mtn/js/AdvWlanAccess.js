
//data list
function Datalist()
{
	this.list = new Array();
	this.maxrowid = 0;
}

Datalist.prototype .getData = function(rowid){
	var i;
	var data;
	
	for(i = 0; i < this.list.length; i++)
	{
		data = this.list[i];
		if(data.rowid == rowid)
		{
			break;
		}
	}

	return data;
}

Datalist.prototype .getRowNum = function(rowid){
	var rowNum = 0;
	for(rowNum = 0; rowNum < this.list.length; rowNum++)
	{
		if(rowid == this.list[rowNum].rowid)
		{
			break;
		}
	}
	return rowNum;
}

Datalist.prototype.editData = function(id, newdata){
	var rowNum = this.getRowNum(id);
	if(this.checkData(newdata, rowNum) == false)
	{
		return false;
	}
	
	newdata.setRowid(id);
	this.list.splice(rowNum,1,newdata);
	
	newdata.setDataToRow($("#tr"+newdata.rowid));
	return true;
}

Datalist.prototype.deleteData = function(id){
	var rowNum = this.getRowNum(id);
	this.list.splice(rowNum, 1);
	
	$("#tr"+id).remove();
}

Datalist.prototype.push = function(data){
	if(this.checkData(data, null) == false)
	{
		return false;
	}

	data.setRowid(this.maxrowid);
	this.list.push(data);
	
	this.maxrowid++;
	
	data.addRowToHTML('accesslist');
	
	return true;
}

Datalist.prototype.checkData = function(newdata, rowNum){
	var i;
	
	//check
	for(i = 0; i < this.list.length; i++)
	{
		if(i == rowNum)
			continue;
	
		if(this.list[i].macAddress == newdata.macAddress)
		{
			alert("macAddress cannot be the same.");
			return false;
		}
		

		if((this.list[i].macAddress == newdata.macAddress) &&
		    (this.list[i].servicename == newdata.servicename) )
		{
			alert("Rule cannot be the same.");
			return false;	
		}		
	}
	return true;
}

Datalist.prototype.length = function(){
	return this.list.length;
}

//constructor
function Data(servicename, macAddress){
	this.servicename = servicename;
	this.macAddress = macAddress;
}


Data.prototype = 
{
	//property
	rowid:null,
	enable:null,
	servicename:null,
	macAddress:null,
	
	//method
	setRowid : function(rowid)
	{
		this.rowid = rowid;
	},
	
	addRowToHTML : function(table)
	{
		var outputString;
		
		outputString = "<tr id='tr"+ this.rowid + "'></tr>"
		
		var selector = "#"+table+"> tbody";
		$(selector).append(outputString);
		
		this.setDataToRow($("#tr"+this.rowid));
		return;
	},
	
	setDataToRow : function(object)
	{
		var outputString;
	
		outputString = "<td>" + this.rowid + "</td>";
		outputString += "<td>" + this.servicename + "</td>";
		outputString += "<td>" + this.macAddress + "</td>";
		outputString += "<td><img src='image/edit_btn.gif' width=28 height=28 style='cursor:pointer;' onclick='editData("+this.rowid+")'/><img src='image/trash.gif' width=41 height=41 style='cursor:pointer;padding-left:14px;' onclick='deleteData("+this.rowid+")'/></td>";
		//outputString += "<td><img src='image/trash.gif' width=41 height=41 style='cursor:pointer' onclick='deleteData("+this.rowid+")'/></td>";
	
		object.html(outputString);
		return;
	}
}
