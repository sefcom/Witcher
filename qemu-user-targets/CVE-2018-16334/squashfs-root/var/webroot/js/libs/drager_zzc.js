
/********drager**********/
(function(){
	function forbitSelect(event){

		$("body").addClass("no-select");
		document.body.onselectstart = function(event) {
			var e = event||window.event;
			e.preventDefault && e.preventDefault();
			e.stopPropagation && e.stopPropagation();
			return false
		}; 
	}

	function mouseMove(newLeft, newTop){
		if (newLeft < 0) {
			newLeft = 0;
		} 
		if (newLeft + $(this).width() > $(window).width()) {
			newLeft =  $(window).width() - $(this).width();
		}
		if (newTop < 0) {
			newTop = 0;
		} 
		if (newTop + $(this).height() > $(window).height()) {
			newTop =  $(window).height() - $(this).height();
		}
		$(this).css({
			left: newLeft + "px",
			top: newTop + "px",
			margin: 0
		});
	}

	$.fn.drager = function(dragBar) {
		var initSize = {x:0,y:0},
			eleInitSizeX = $(this).offset().left,
			eleInitSizeY = $(this).offset().top,
			dragStart = false,
			newSizeX = 0,
			newSizeX = 0,
			dragEle = this;

		$(dragBar).on("mousedown", function(e) {
			var event = e || window.event;

			dragStart = true;
			eleInitSizeX = $(this).offset().left;
			eleInitSizeY = $(this).offset().top;
			initSize.x = event.clientX;
			initSize.y = event.clientY;
			forbitSelect();

			setTimeout(function() {
				if (!dragStart) return;
				$("<div></div>").addClass("drag-mask").appendTo($("body"));
				document.onmousemove = function(e) {
					var event = e || window.event;

					newSizeX = (event.clientX - initSize.x) + eleInitSizeX;
					newSizeY = (event.clientY - initSize.y) + eleInitSizeY;
					//console.log("new eleInitSize", newSizeX);
					mouseMove.call(dragEle, newSizeX, newSizeY);
				}		
			}, 200);

			document.onmouseup = function() {
				dragStart = false;
				document.onmousemove = null;
				document.onmouseup = null;
				$("body").removeClass("no-select").find(".drag-mask").remove();
				document.body.onselectstart = null; 
			};
		});
	}
})();