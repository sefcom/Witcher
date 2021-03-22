$(function() {
	cloneText();
	$("#thunder_net").on("click",gotoThunder);
});


function gotoThunder() {
	window.open("http://yuancheng.xunlei.com");
}

function cloneText() {
 	$("#clone").zclip({
        path:'js/libs/ZeroClipboard.swf',
		beforeCopy: checkData,
        copy:function(){
            return $('#code').val();
        },
		afterCopy: preSubmit
    });
}

function checkData() {
	if($('#code').val() == "") {
		showErrMsg("thunder_msg", _("Please enter an activation code."));
		return ;	
	}	
}

function preSubmit() {
	top.showSaveMsg("0", _("Copied successfully!"));	
}
