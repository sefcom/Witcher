//var Pagination = function(pageEleWrapId, dataArr, itemCount, pageItemCount ) {

//page是从零开始数的
var Pagination = function(_options) {
	var defaults = {
			pageEleWrapId: "",
			dataArr: [], 
			pageItemCount: 0,
			getDataUrl: "",
			handle: null,//每一页数据用户的处理函数
			getAll: false,
			param: ""
		},
		allIitemCount = 0,//总条数
		pageNow = 0,//从0开始数
		pageCount = 0,//总页数
		reInit = true,
		getNum = 0,//当前已经发出去的还未被处理的请求
		gettingData = false,//是否正在取数据
		options = {};

	function init() {
		options = $.extend(defaults, _options);

		/*todo:参数有效性判断*/

		if(options.getAll) {
			reInit = false;
			allIitemCount = options.dataArr.length;
			pageCount = Math.ceil(options.dataArr.length/options.pageItemCount);
		}

		getPageData(0);
		$("#" + options.pageEleWrapId).html('<div class="pagination"><div class="btn first"><<</div><div class="btn previous"><</div><div class="btn next">></div><div class="btn last">>></div></div>').hide();
		bindEvent();
		$("#" + options.pageEleWrapId).show();
		refreshPageBtn();
	}

	function bindEvent() {
		$("#" + options.pageEleWrapId + " .first").on("click", function() {
			if(!$(this).hasClass("warning")){
				getFirstPage();
			}
		});
		$("#" + options.pageEleWrapId + " .previous").on("click", function() {
			if(!$(this).hasClass("warning")){
				getPrePage();
			}
		});
		$("#" + options.pageEleWrapId + " .next").on("click", function() {
			if(!$(this).hasClass("warning")){
				getNextPage();
			}
		});
		$("#" + options.pageEleWrapId + " .last").on("click", function() {
			if(!$(this).hasClass("warning")){
				getLastPage();
			}
		});
	}

	function getNextPage() {
		if(!reInit && !gettingData)
		getPageData(pageNow+1);
	}

	function getPrePage() {
		if(!reInit && !gettingData)
		getPageData(pageNow-1);
	}

	function getFirstPage() {
		if(!reInit && !gettingData)
		getPageData(0);
	}

	function getLastPage() {
		if(!reInit && !gettingData)
		getPageData(pageCount-1);
	}

	function getPageData(pageNum) {
		var thisGetNum,
			itemCount,
			pageData,
			getParam;

		if((pageData = isThisPageDataExist(pageNum)) || options.getAll) {
			options.handle(pageData,(pageNum * options.pageItemCount)+1);
			pageNow = pageNum;
			refreshPageBtn();
		} else {
			pageData = [];
			thisGetNum = ++getNum;
			gettingData = true;
			var getUrl = options.getDataUrl + "?rand=" + Math.random() + "&page=" + (pageNum + 1);
			if(options.param != "") {
				getUrl += "&" + options.param;
			}

			$.get(getUrl, function(data) {		
				var spts = data.split("|"),
					pageDataIndex = 0,
					firstIndex,
					lastIndex;

				if (getNum != thisGetNum) {//如果在这个请求之后还发了请求，则处理后发出的请求，这个不处理
					return;
				} else {
					getNum = 0;
				}

				allIitemCount = itemCount = evalJSON(spts[spts.length-1].split("#")[1]).num;
				pageCount = Math.ceil(itemCount/options.pageItemCount);

				if((pageNum + 1) > pageCount) {
					pageNum = pageCount;
				}

				pageDataIndex = 0;
				firstIndex = (pageNum) * options.pageItemCount;
				lastIndex = ((pageNum + 1) * options.pageItemCount) - 1;
				
				for (var i = firstIndex; i <= lastIndex && i < itemCount; i++) {
					options.dataArr[i] = evalJSON(spts[pageDataIndex]);
					pageData[pageDataIndex] = options.dataArr[i];
					pageDataIndex++;
				}

				if(options.getAll) {
					for (var i = 0; i < spts.length-1; i++) {
						options.dataArr[i] = evalJSON(spts[i]);
						pageData[pageDataIndex] = options.dataArr[i];
						pageDataIndex++;
					}					
				}
				if(pageData.length == 0 && pageNum > 0) {
					getPageData(pageNum-1);return;
				}


				options.dataArr.length = itemCount ;
				options.handle(pageData,firstIndex + 1);//执行用户的处理函数
				pageNow = pageNum;
				gettingData = false;
				reInit = false;
				refreshPageBtn();
			});		
		}
	}

	function isThisPageDataExist(pageNum) {
		var pageData = [],
			pageDataIndex = 0,
			firstIndex = pageNum * options.pageItemCount,
			lastIndex = ((pageNum + 1) * options.pageItemCount) - 1;

		for(var i = firstIndex; i <= lastIndex && i < allIitemCount; i++) {
			if(options.dataArr[i] == null) {
				return false;
			}
			pageData[pageDataIndex] = options.dataArr[i];
			pageDataIndex++;
		}
		return pageData.length == 0 ? false : pageData;
	}

	function refreshPageBtn() {
		if (pageCount <= 1) {
			$("#" + options.pageEleWrapId + " .pagination").hide();
		} else {
			$("#" + options.pageEleWrapId + " .pagination").show();
		}
		if (pageNow == 0) {
			$("#" + options.pageEleWrapId + " .first").addClass("warning").attr("disabled", true);
			$("#" + options.pageEleWrapId + " .previous").addClass("warning").attr("disabled", true);
		} else {
			$("#" + options.pageEleWrapId + " .first").removeClass("warning").attr("disabled", false);
			$("#" + options.pageEleWrapId + " .previous").removeClass("warning").attr("disabled", false);
		}
		if (pageNow == pageCount - 1) {
			$("#" + options.pageEleWrapId + " .last").addClass("warning").attr("disabled", true);
			$("#" + options.pageEleWrapId + " .next").addClass("warning").attr("disabled", true);
		} else {
			$("#" + options.pageEleWrapId + " .last").removeClass("warning").attr("disabled", false);
			$("#" + options.pageEleWrapId + " .next").removeClass("warning").attr("disabled", false);
		}
	}

	//当执行了某操作要刷新分页数据的时候调用：如：删除操作, 有newdata代表数据不用分页对象获取数据
	this.refresh = function(newData) {
		if (newData) {
			options.dataArr = newData;
			allIitemCount = options.dataArr.length;
			pageCount = Math.ceil(newData.length/options.pageItemCount);
			if((pageNow + 1) > pageCount) {
				pageNow = pageCount - 1;
			}
			pageNow = (pageNow < 0 ? 0 : pageNow);
		} else {
			options.dataArr = [];
			reInit = true;			
		}

		getPageData(pageNow);
	}

	this.changeParam = function(paramStr) {
		options.param = paramStr;
		options.dataArr = [];
		reInit = true;		
		getPageData(0);	
	}
	
	this.getPageItemCount = function() {
		return options.pageItemCount;
	}
	this.getAllItemCount = function() {
		return allIitemCount;
	}

	init();
}