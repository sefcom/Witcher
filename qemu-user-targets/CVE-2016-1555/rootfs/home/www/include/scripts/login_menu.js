var menuObject;
var menuClass = Class.create({
    data: $H({
        'Login': $H({}),
        'Help': $H({
            "Online Help": $H({
                "Support": []
//                "User Guide": []
            })
        })
	}),
	initialize: function () {
		this.pointer = {first: 0, second: 0, third: 0, fourth: 0};
		this.currentData = { first: null, second: null, third: null, fourth: [] };
		this.initialLoad = true;
        this.colValue = '';
        this.getFirstLevelData();
		//this.updateMenu('first',0);
	},
	updateItem: function(item, value, key) {
		var key = (key == undefined)?0:key;
		switch(item) {
			case 'first':
				this.currentData.first = value;
				break;
			case 'second':
				this.currentData.second = value;
				break;
			case 'third':
				this.currentData.third = value;
				break;
			case 'fourth':
				this.currentData.fourth[key] = value;
				break;
		}
	},
	updatePointer: function(level, value) {
		switch(level) {
			case 'first':
				this.pointer.first = value;
				break;
			case 'second':
				this.pointer.second = value;
				break;
			case 'third':
				this.pointer.third = value;
				break;
			case 'fourth':
				this.pointer.fourth = value;
				break;
		}
	},
	getLevel: function(level,id) {
		switch(level) {
			case 'first':
				return this.currentData.first[id];
				break;
			case 'second':
				return this.currentData.second[id];
				break;
			case 'third':
				return this.currentData.third[id];
				break;
			case 'fourth':
				return this.currentData.fourth[id];
				break;
		}
		return this.first[id];
	},
	getPointer: function(level) {
		switch(level) {
			case 'first':
				return this.pointer.first;
				break;
			case 'second':
				return this.pointer.second;
				break;
			case 'third':
				return this.pointer.third;
				break;
			case 'fourth':
				return this.pointer.fourth;
				break;
		}
	},
	resetPointer: function(level) {
		switch(level) {
			case 'first':
				this.updatePointer('second',0);
				break;
			case 'second':
				this.updatePointer('third',0);
				break;
			case 'third':
				this.updatePointer('fourth',0);
				break;
		}
	},
	getFirstLevelData:  function() {
		this.updateItem('first',this.data.keys());
		this.getSecondLevelData()
	},
	getSecondLevelData: function() {
		var dataValues = this.data.values();
		for (var i=0; i<dataValues.length;i++) {
			if (i == this.getPointer('first')) {
				this.updateItem('second',dataValues[i].keys());
				//alert('Second Level['+i+'] Updated with '+dataValues[i].keys());
			}
		}
		this.getThirdLevelData();
	},
	getThirdLevelData: function() {
		var dataValues = this.data.values();
		for (var key=0; key<dataValues.length;key++) {
			if (key == this.getPointer('first')) {
				var itemValues = dataValues[key].values();
				for (var key2=0; key2<itemValues.length;key2++) {
					if (key2 == this.getPointer('second')) {
						this.updateItem('third',itemValues[key2].keys());
						//alert('Third Level['+key+']['+key2+'] Updated with '+itemValues[key2].keys());
					}
				}
			}
		}
	},
	updateMenu: function(level, pointer, start) {
		if (window.top.frames['master']._disableAll != undefined && window.top.frames['master']._disableAll == true) {
			return ;
		}
/*		if (!window.top.frames['header']._initiateMenu) {
			if (window.top.frames['master'].progressBar == undefined || window.top.frames['master'].progressBar.isOpened() == true) {
				//if (!confirm('Page is currently loading!\nAre you sure you want to navigate to this page?'))
					//return;
			}
		}*/
		if (start!=undefined) {
			this.initialLoad = start;
		}
		this.updatePointer(level,pointer);
		switch(level) {
			case 'first':
				this.getFirstLevelData();
				this.updateFirstMenu();
				break;
			case 'second':
				this.getSecondLevelData();
				this.updateSecondMenu();
				break;
			case 'third':
				this.getThirdLevelData();
                if (!this.initialLoad) {
                    this.updateThirdMenu();
                }
                else {
                    loadThird = setTimeout(loadThirdMenu, 50);
                }
				break;
		}
		return;
	},
	updateFirstMenu: function() {
		var primaryTabs = $('primaryNav').immediateDescendants();
		for (var x=0; x< primaryTabs.length; x++) {
			if (this.getPointer('first') == x) {
				primaryTabs[x].replace("<LI class='Active'><A href='#' onclick=\"javascript:menuObject.updateMenu('first',"+x+", false);\">"+this.getLevel('first',x)+"</A></LI>");
			}
			else {
				primaryTabs[x].replace("<LI><A href='#' onclick=\"javascript:menuObject.updateMenu('first',"+x+",false);\">"+this.getLevel('first',x)+"</A></LI>");
			}
		}
		if (this.getLevel('first', this.getPointer('first')) == 'Monitoring' || this.getLevel('first', this.getPointer('first')) == 'Support') {
			if (typeof(window.top.frames['action'].$) == 'function' && window.top.frames['action'].$('ButtonsDiv') != undefined)
				window.top.frames['action'].$('ButtonsDiv').hide();
		}
		else {
			if (typeof(window.top.frames['action'].$) == 'function' && window.top.frames['action'].$('ButtonsDiv') != undefined)
				window.top.frames['action'].$('ButtonsDiv').show();
		}
		this.updatePointer('second',0);
		this.getSecondLevelData();
		this.updateSecondMenu();
	},
	updateSecondMenu: function() {
		var secondaryTabs = $('secondaryNav').immediateDescendants();
        if (this.getLevel('second',0) == 'Online Help') {
            var str = "<LI><A href='javascript:void(0)' onclick=\"menuObject.updateMenu('second',0,false);\" ";
            str = str + "class='Active'";
            str = str + ">"+this.getLevel('second',0)+"</A>";
            str = str + '</LI>';
            if (this.getLevel('second',0)!=undefined) {
                $('secondaryNav').show();
                $('secondaryNav').innerHTML = str;
            }
            this.colValue="166px,*,11px";
        }
        else {
            $('secondaryNav').hide();
            window.top.frames['master'].location.href = 'index.php?page=master';
            this.colValue="18px,*,11px";
        }
		this.updatePointer('third',0);
		this.getThirdLevelData();

        if (!this.initialLoad)
			this.updateThirdMenu();
	},
	updateThirdMenu: function() {
		var x = 0;
		if (window.top.frames['thirdmenu'].$('thirdMenuTable') == undefined) {
			this.resetPointer('third');
		}
		this.prepareThirdMenu();
        if (this.colValue != '') {
			if (window.top.frames['master'].progressBar != undefined && window.top.frames['master'].progressBar.isOpened() != true)
            	window.top.frames['master'].progressBar.open();
            window.top.setCols(this.colValue);
        }
    },
	prepareThirdMenu: function() {
		var thirdMenuTable = $CE('TABLE',{ className: 'tableStyle', id: 'thirdMenuTable' });
		//thirdMenuTableBody = $CE('TBODY',{ id: 'thirdMenuTableBody' });
		var x = 0;
		var thirdTab = this.currentData.third;
		if (thirdTab != null) {
			for (var x = 0; x < thirdTab.length; x++) {
				var thirdMenuRow = thirdMenuTable.appendRow({
					id: 'TR_Main_' + x
				});
				var str = '';
				var imgStr = 'right';
				if (x == this.getPointer('third')) {
	                                if(config.AWSDAP350.status || config.INDUS.status || config.AUGMENTIX.status){
						str = 'style="color: #B3C188;"';
        	                        }else{
                	                        str = 'style="color: #FFA400;"';
                        	        }
					imgStr = 'right';
					if (this.currentData.first[this.getPointer('first')] != 'Login') {
						if (window.top.frames['master'].progressBar != undefined && window.top.frames['master'].progressBar.isOpened() != true)
							window.top.frames['master'].progressBar.open();
						//window.top.frames['master'].location.href = thirdTab[x].replace(' ', '') + '.html';
                        setTimeout("showPage('','','','Help',[0,0],false);",1000);
					}
				//else
				//  window.top.frames('master').location.href = 'index.php?page=master';
				}
				//alert(this.getLevel('fourth',x));
				var linkStr = '<A onclick="window.top.frames[\'header\'].menuObject.updateMenu(\'third\',' + x + ', false);" href="javascript:void(0)" class="TertiaryNav" ' + str + '><strong>' + thirdTab[x] + '</strong></A>';
				thirdMenuRow.appendCell({
					id: 'TD_PriArrow_' + x,
					width: '10px',
					height: '10px',
					vAlign: 'top',
					className: 'padAll noPadRight'
				}).update('<img src="images/arrow_' + imgStr + '.gif" id="img_Basic" style="border: 0px; margin: 0px; margin-top: 3px; _margin-top: 0px; float: both; vertical-align: middle;">');
				thirdMenuRow.appendCell({
					id: 'TD_Main_' + x,
					colSpan: 2,
					className: 'padAll noPadLeft'
				}).update(linkStr);
			}
		}
		try {
			window.top.frames['thirdmenu'].$('TreeFrame').innerHTML = '';
			if(navigator.appName == 'Microsoft Internet Explorer')
                		window.top.frames['thirdmenu'].$('TreeFrame').update(thirdMenuTable.outerHTML);
            		else
                		window.top.frames['thirdmenu'].$('TreeFrame').appendChild(thirdMenuTable);
		}
		catch(e) {
			window.top.frames['thirdmenu'].$('TreeFrame').update(thirdMenuTable.outerHTML);
		}
		//alert(this.getLevel('first',this.getPointer('first')) + '---' + this.getLevel('second',this.getPointer('second')) + '---' +this.getLevel('third',this.getPointer('third')));
		//alert(window.top.frames['thirdmenu'].$('TreeFrame').innerHTML);
	},
	test: function() {
		alert("First = "+this.pointer.first+"\nSecond = "+this.pointer.second+"\nThird = "+this.pointer.third+"\nFourth = "+this.pointer.fourth);
	}
});

menuObject = new menuClass();
//menuObject.updateFirstMenu();

Event.onDOMReady ( function() {
	if (window.top.frames['header']._initiateMenu != undefined && window.top.frames['header']._initiateMenu != false) {
		window.top.frames['header'].menuObject.updateMenu('first', 0, true);
	//	window.top.frames['header'].menuObject.test();
	}
});
function initiateMenu(start)
{
	window.top.frames['header'].menuObject.updateMenu('first',0, true);
	window.top.frames['header'].menuObject.test();
}


//var menu1 = new menuData();
var loginPage = true;
