if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

//--------------------------------------------------------------
//		Priority Cell (gridPanel)
//--------------------------------------------------------------
var GridPanel = function(_rows, _columns, _container) {

	this.container;
	this.rows 			= _rows;
	this.columns 		= _columns;
	this.rowHeight		= 86;
	this.columnWidth	= 188;
	this.instanceArray 	= new Array();
	this.dataArray 		= new Array();
	
	// get element
	if (typeof(_container) == 'string') {
		this.container = document.getElementById(_container);  // id
	} else if (typeof(_container) == 'object') {
		this.container = _container;							 // element
	}
	
}
// isFull
GridPanel.prototype.isFull = function() {
	if (this.dataArray.length < this.instanceArray.length) {
		return false;
	} else {
		return true;
	}
}
// existInstance
GridPanel.prototype.existData = function(data) {
	if (this.dataArray.indexOf(data) == -1) {
		return false;
	} else {
		return true;
	}
}
// NextCellPosition
GridPanel.prototype.nextCellPosition = function() {
	var x = -1;
	var y = -1;
	var nextCellNum = this.dataArray.length;
	if (nextCellNum <= this.instanceArray.length) {
		var instanceElement = this.instanceArray[nextCellNum].element;
		var pos = getElementXY(instanceElement);
		x = pos.x;
		y = pos.y;
	}
	return {x:x, y:y};
}
// AddInstance
GridPanel.prototype.addInstance = function(object) {

	// check cell space
	if (this.instanceArray.length < this.rows * this.columns) {
		
		// get target cell number
		var cellNum = this.instanceArray.length; 
		
		// count x, y
		var cellrow 	=  cellNum % this.rows;
		var cellcolumn	= Math.floor(cellNum / this.rows);
		var x 			= cellcolumn * this.columnWidth;
		var y 			= cellrow	 * this.rowHeight;
		
		// apply child (object.element)
		var element = object.element;
		element.style.position 	= "absolute";
		element.style.top  		= y + "px";
		element.style.left 		= x + "px";
		this.container.appendChild(element);
		this.instanceArray.push(object);
		
		object.hide();
		object.parent = this;
	}
}
// AddData
GridPanel.prototype.addData = function(data) {
	// check cell space
	if (this.dataArray.length < this.instanceArray.length) {
		
		// get cell instance
		var cellNum = this.dataArray.length; 
		var cellInstance = this.instanceArray[cellNum];
		
		// apply value
		cellInstance.setDeviceData(data)
		this.dataArray.push(data);
		
		// show
		cellInstance.show();
	}
}
// Remove Data By Instance
GridPanel.prototype.removeData = function(data) {
	// get cell num
	var num = this.dataArray.indexOf(data);
	if (num != -1) {
		// remove data
		this.dataArray.splice(num,1); 	// remove target data from dataArray
		this.updateInstance();			// update all instance
	}
}
// Update Instance
GridPanel.prototype.updateInstance = function() {
	for (var i=0; i<this.instanceArray.length; i++) {
		// if data exist
		var inst = this.instanceArray[i];
		if (this.dataArray[i] != undefined ) {
			// re-apply data
			var data = this.dataArray[i];
			inst.setDeviceData(data);
			inst.show();
		
		} else { 
			// data not exist
			inst.removeDeviceData();
			inst.hide();
		}
	}
}