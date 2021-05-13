var Slideshow  = function(id) {
	this.parentDiv = document.getElementById(id);
	this.parentDiv.style.fontSize = '0';
	this.itemContainer;
	this.itemContainerId;
	this.pageDotsContainer;
	this.itemWidth = 164;
	this.width = 690;
	this.height = 86;
	this.slidePos = 0;
	this.slideStep;
	this.isSliding = false;
	this.itemPerPage =	4;
	this.nowPage = 0;
	this.nextBtn;
	this.prevBtn;
	this.btnWidth = 16;
	this.itemArray 		= new Array();
	this.dataArray 		= new Array();
	this.pageDotArray 	= new Array();
	
	// create container
	this.container = document.createElement("div");
	this.container.style.position			= "relative";
	this.container.style.margin				= '0';
	//this.container.style.display 			= "inline-block";
	this.container.style.cssFloat          ="left";
	this.container.style.styleFloat          ="left";
	this.container.style.width				= (this.width - this.btnWidth * 2)+"px";
	this.container.style.height				= this.height+"px";
	this.container.style.verticalAlign		= "top";
	this.container.style.overflow			= "hidden";
	
	this.itemContainer 						= document.createElement("div");
	this.itemContainer.style.position		= 'absolute';
	this.itemContainer.style.top			= '0';
	this.itemContainer.style.left			= '0';
	this.itemContainer.style.margin			= '0';
	this.itemContainer.style.padding		= '0';
	//this.itemContainer.dislay				= "inline-block";
	this.itemContainer.style.cssFloat		= "left";
	this.itemContainer.style.styleFloat		= "left";
	this.itemContainerId 					= "ss_itemContainer";
	this.itemContainer.setAttribute("id", this.itemContainerId);
	this.container.appendChild(this.itemContainer);
	
	// create buttons
	this.nextBtn 						= document.createElement("div");
	this.nextBtn.style.margin			= '0';
	//this.nextBtn.style.display 			= "inline-block";
	this.nextBtn.style.cssFloat 			= "left";
	this.nextBtn.style.styleFloat 			= "left";
	this.nextBtn.style.cursor  			= "pointer";
	this.nextBtn.style.width  			= this.btnWidth+"px";
	this.nextBtn.style.height			= "30px";
	this.nextBtn.style.backgroundImage 	= "url('image/icon_next.png')";
	this.nextBtn.style.backgroundRepeat ="no-repeat";
	this.nextBtn.style.verticalAlign	= "top";
	this.nextBtn.style.marginLeft="21px"; 
	this.nextBtn.style.marginTop="28px";
	var parent = this;
	this.nextBtn.onclick = function() {
		parent.nextPage();
	};
	this.prevBtn 						= document.createElement("div");
	this.prevBtn.style.opacity 			= 0.5;
	this.prevBtn.style.margin			= '0';
	//this.prevBtn.style.display 			= "inline-block";
	this.prevBtn.style.cssFloat 			= "left";
	this.prevBtn.style.styleFloat 			= "left";
	this.prevBtn.style.cursor  			= "pointer";
	this.prevBtn.style.width  			= this.btnWidth+"px";
	this.prevBtn.style.height			= "30px";
	this.prevBtn.style.verticalAlign	= "top";
	this.prevBtn.style.backgroundImage 	= "url('image/icon_last.png')";
	this.prevBtn.style.backgroundRepeat ="no-repeat";
	this.prevBtn.style.marginRight="21px"; 
	this.prevBtn.style.marginTop="28px";
	var parent = this;
	this.prevBtn.onclick = function() {
		parent.prevPage();
	};
	
	// dots
	this.pagedotDiv	= document.createElement("div");
	//this.pagedotDiv.style.display 		= "inline-block";
	this.pagedotDiv.style.cssFloat     ="left";
	this.pagedotDiv.style.styleFloat     ="left";
	this.pagedotDiv.style.width			= this.width + "px";
	this.pagedotDiv.style.minHeight		= "0px";
	this.pagedotDiv.style.textAlign		= "center";
	
	
	this.parentDiv.appendChild(this.prevBtn);
	this.parentDiv.appendChild(this.container);
	this.parentDiv.appendChild(this.nextBtn);
	this.parentDiv.appendChild(this.pagedotDiv);
}
	
// appendChild
Slideshow.prototype.addItem = function(dataobject) {
	
	// append element
	var item = dataobject.element;
	//item.style.display = "inline-block";
	item.style.cssFloat = "left";
	item.style.styleFloat = "left";
	this.itemArray.push(item);
	this.dataArray.push(dataobject);
	this.itemContainer.appendChild(item);

	// extend container width
	this.itemContainer.style.width = (this.itemArray.length * this.itemWidth) + "px";
	
	// create pagedots
	var allItemWidth = 0;
	for(var i=0; i<this.dataArray.length; i++) {
		allItemWidth += this.itemWidth;
	}
	var pageWidth 	= (this.width - this.btnWidth * 2);
	var pageCount = Math.floor((allItemWidth-1) / pageWidth) + 1;
	if (pageCount > this.pageDotArray.length) {
		// create pageDot
		var dot =  document.createElement("div");
		//dot.style.display 	 = "inline-block";
		dot.style.cssFloat="left";
		dot.style.styleFloat="left";
		//dot.style.backgroundImage = "url('image/slideshow_pagedot.png')";
		dot.style.width 	 = "6px";
		dot.style.margin	 = "0 5px";
		dot.style.height 	 = "0px";
		this.pageDotArray.push(dot);
		this.pagedotDiv.appendChild(dot);
	}
	//
	this.update();
}
Slideshow.prototype.update = function() {
	
	// update page dot
	var displayItemWidth = 0;
	for (var i=0; i<this.dataArray.length; i++) {
		if (this.dataArray[i].priority == "none") {
			displayItemWidth +=  this.itemWidth;
		}
	}
	var pageWidth 	= (this.width - this.btnWidth * 2);
	var displayPageCount = Math.floor((displayItemWidth-1) / pageWidth) + 1;

	// 
	for (var i=0; i<this.pageDotArray.length; i++) {
		if (i<=	displayPageCount - 1) {
			// show
			this.pageDotArray[i].style.display = "inline";
			this.pageDotArray[i].style.cssFloat="left";
			this.pageDotArray[i].style.styleFloat="left";
		} else {
			// hide
			this.pageDotArray[i].style.display = "none";
		}
	}
	this.updatePageDotPageBtn();
}
Slideshow.prototype.updatePageDotPageBtn = function() {
	// pagedot
	for (var i=0; i<this.pageDotArray.length; i++) {
		if (i == this.nowPage) {
			this.pageDotArray[i].style.opacity = "1";
		} else {
			this.pageDotArray[i].style.opacity = "0.3";
		}
	}
	// prevBtn
	if (this.nowPage <= 0) {
		this.prevBtn.style.opacity = "0.5";	
	} else {
		this.prevBtn.style.opacity = "1";
	}
	// nextBtn
	var allWidth  	= 0;
	for (var i=0; i<this.dataArray.length; i++) {
		if (this.dataArray[i].priority == "none") {
			allWidth +=  this.itemWidth;
		}
	}
	var pageWidth 	= (this.width - this.btnWidth * 2);
	var pageLastNum = Math.floor((allWidth-1) / pageWidth);
	if (this.nowPage >= pageLastNum) {

		this.nextBtn.style.opacity = "0.5";	
	} else {
		this.nextBtn.style.opacity = "1";	
	}
}


Slideshow.prototype.getAllChildWidth = function() {
	var widthSum = 0;
	for (var i=0; i<this.itemArray.length; i++) {
		widthSum += parseInt(this.itemArray[i].style.width.replace("px", ""));
	}
	return widthSum;
}
// nextPage
Slideshow.prototype.nextPage = function() {
	
	// check if has next page
	var allWidth  	= this.getAllChildWidth();
	var pageWidth 	= (this.width - this.btnWidth * 2);
	var pageLastNum = Math.floor((allWidth-1) / pageWidth);
	var slidePos = 0 - (this.nowPage * pageWidth);
	
	if (this.nowPage < pageLastNum) {
		
		this.nowPage++;
		// animation slide 
		var slidePos = 0 - (this.nowPage * pageWidth);
		$(this.itemContainer).stop();
		$(this.itemContainer).animate({left:slidePos}, 800, 'easeOutCubic');
		
	} else {
		
		// at last page (bounce back)	
		var self = this;
		$("#"+this.itemContainerId).animate({left: (slidePos -25)}, 100, 'swing', 
			function() {
				$("#"+self.itemContainerId).animate({left:slidePos}, 400, 'easeInSine');
			}
		);	
	}
	this.updatePageDotPageBtn();
}
Slideshow.prototype.putAndSlideToLast = function(element, completefunc) {
	
	// get num in itemArray
	var num = this.itemArray.indexOf(element);
	if (num != -1) {
		// re-append (put to last)
		this.itemContainer.appendChild(element); 
		
		// scroll to last
		var allItemWidth = 0;
		for(var i=0; i<this.dataArray.length; i++) {
			if(this.dataArray[i].priority == "none") {
				allItemWidth += this.itemWidth;
			}
		}
		var pageWidth 	= (this.width - this.btnWidth * 2);
		var pageLastNum = Math.floor((allItemWidth-1) / pageWidth);
		if (this.nowPage != pageLastNum) {
			this.nowPage = pageLastNum;
			var slidePos = 0 - (this.nowPage * pageWidth);
			$(this.itemContainer).stop();
			$(this.itemContainer).animate({left:slidePos}, 800, 'easeOutCubic', completefunc);
		} else {
			completefunc();	
		}
		
		this.update();
	}
}
Slideshow.prototype.updateToLastPage = function() {
	// scroll to last
	var allItemWidth = 0;
	for(var i=0; i<this.dataArray.length; i++) {
		if(this.dataArray[i].priority == "none") {
			allItemWidth += this.itemWidth;
		}
	}
	var pageWidth 	= (this.width - this.btnWidth * 2);
	var pageLastNum = Math.floor((allItemWidth-1) / pageWidth);
	if (this.nowPage > pageLastNum && pageLastNum >= 0) {
		this.nowPage = pageLastNum;
		var slidePos = 0 - (this.nowPage * pageWidth);
		$(this.itemContainer).stop();
		$(this.itemContainer).animate({left:slidePos}, 800, 'easeOutCubic');
	}
	this.update();
}
Slideshow.prototype.prevPage = function() {
	
	// check if has next page
	var allWidth  	= this.getAllChildWidth();
	var pageWidth 	= (this.width - this.btnWidth * 2);
	var pageLastNum = Math.floor(allWidth / pageWidth);
	var slidePos = 0 - (this.nowPage * pageWidth);
	
	if (this.nowPage > 0) {
		
		this.nowPage--;
		// animation slide 
		var slidePos = 0 - (this.nowPage * pageWidth);
		$(this.itemContainer).stop();
		$(this.itemContainer).animate({left:slidePos}, 800, 'easeOutCubic');
		
	} else {
		
		// at last page (bounce back)	
		var self = this;
		$("#"+this.itemContainerId).animate({left: (slidePos +25)}, 100, 'swing', 
			function() {
				$("#"+self.itemContainerId).animate({left:slidePos}, 400, 'easeInSine');
			}
		);	
	}
	
	this.updatePageDotPageBtn();
}

$.extend($.easing,
{
    def: 'easeOutQuad',
    swing: function (x, t, b, c, d) {
       // alert($.easing.default);
       // return $.easing[$.easing.def](x, t, b, c, d);
    },
    easeInQuad: function (x, t, b, c, d) {
        return c*(t/=d)*t + b;
    },
    easeOutQuad: function (x, t, b, c, d) {
        return -c *(t/=d)*(t-2) + b;
    },
    easeInOutQuad: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t + b;
        return -c/2 * ((--t)*(t-2) - 1) + b;
    },
    easeInCubic: function (x, t, b, c, d) {
        return c*(t/=d)*t*t + b;
    },
    easeOutCubic: function (x, t, b, c, d) {
        return c*((t=t/d-1)*t*t + 1) + b;
    },
    easeInOutCubic: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t + b;
        return c/2*((t-=2)*t*t + 2) + b;
    },
    easeInQuart: function (x, t, b, c, d) {
        return c*(t/=d)*t*t*t + b;
    },
    easeOutQuart: function (x, t, b, c, d) {
        return -c * ((t=t/d-1)*t*t*t - 1) + b;
    },
    easeInOutQuart: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
        return -c/2 * ((t-=2)*t*t*t - 2) + b;
    },
    easeInQuint: function (x, t, b, c, d) {
        return c*(t/=d)*t*t*t*t + b;
    },
    easeOutQuint: function (x, t, b, c, d) {
        return c*((t=t/d-1)*t*t*t*t + 1) + b;
    },
    easeInOutQuint: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
        return c/2*((t-=2)*t*t*t*t + 2) + b;
    },
    easeInSine: function (x, t, b, c, d) {
        return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
    },
    easeOutSine: function (x, t, b, c, d) {
        return c * Math.sin(t/d * (Math.PI/2)) + b;
    },
    easeInOutSine: function (x, t, b, c, d) {
        return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
    },
    easeInExpo: function (x, t, b, c, d) {
        return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
    },
    easeOutExpo: function (x, t, b, c, d) {
        return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
    },
    easeInOutExpo: function (x, t, b, c, d) {
        if (t==0) return b;
        if (t==d) return b+c;
        if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
        return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
    },
    easeInCirc: function (x, t, b, c, d) {
        return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
    },
    easeOutCirc: function (x, t, b, c, d) {
        return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
    },
    easeInOutCirc: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
        return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
    },
    easeInElastic: function (x, t, b, c, d) {
        var s=1.70158;var p=0;var a=c;
        if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
        if (a < Math.abs(c)) { a=c; var s=p/4; }
        else var s = p/(2*Math.PI) * Math.asin (c/a);
        return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
    },
    easeOutElastic: function (x, t, b, c, d) {
        var s=1.70158;var p=0;var a=c;
        if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
        if (a < Math.abs(c)) { a=c; var s=p/4; }
        else var s = p/(2*Math.PI) * Math.asin (c/a);
        return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
    },
    easeInOutElastic: function (x, t, b, c, d) {
        var s=1.70158;var p=0;var a=c;
        if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
        if (a < Math.abs(c)) { a=c; var s=p/4; }
        else var s = p/(2*Math.PI) * Math.asin (c/a);
        if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
        return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
    },
    easeInBack: function (x, t, b, c, d, s) {
        if (s == undefined) s = 1.70158;
        return c*(t/=d)*t*((s+1)*t - s) + b;
    },
    easeOutBack: function (x, t, b, c, d, s) {
        if (s == undefined) s = 1.70158;
        return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
    },
    easeInOutBack: function (x, t, b, c, d, s) {
        if (s == undefined) s = 1.70158;
        if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
        return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
    },
    easeInBounce: function (x, t, b, c, d) {
        return c - $.easing.easeOutBounce (x, d-t, 0, c, d) + b;
    },
    easeOutBounce: function (x, t, b, c, d) {
        if ((t/=d) < (1/2.75)) {
            return c*(7.5625*t*t) + b;
        } else if (t < (2/2.75)) {
            return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
        } else if (t < (2.5/2.75)) {
            return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
        } else {
            return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
        }
    },
    easeInOutBounce: function (x, t, b, c, d) {
        if (t < d/2) return $.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
        return $.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
    }
});