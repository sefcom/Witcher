// create namespace for codemirror.editor use in functions later
var cm = {};

$(document).ready(function () {
    // setup code mirror
    cm.editor = "";
    cm.editor = CodeMirror.fromTextArea(document.getElementById("code"), {
      lineNumbers: true,
      styleActiveLine: true,
      matchBrackets: true,
      scrollbarStyle: null
    });    
    // launch page with blank 'code section
    cm.editor.doc.setValue('');

    $(cm.editor.getWrapperElement()).hide();
    $("#toolbar2").hide();
    
    // Throw alert dialog if create pass or failed
    if (getParameter('status') == 'success') {
        errorDialog('Template created/edited successfully')
    } else if(getParameter('status') == 'failed') {
        errorDialog('Template failed. Try again')
    } else if(getParameter('status') == 'deleted') {
        errorDialog('Template deleted')
    } else if(getParameter('status') == 'duplicateFile') {
        errorDialog('The filename already exists. Try editing file instead, or choose a different filename. Your template has been erased.')
    }
})

function invalid(str){
    var regex = /[ !@#$%^&*()+\=\[\]{};':"\\|,<>\/?]/g;
	return regex.test(str);
}
function hasWhiteSpace(str) {
    return str.indexOf(' ') >= 0;
}

// single row selector from rconfigFunctions.js
tblRowSelector('templatesTbl');

function createTemplate(){
    $('#createEditNotice').show();                                                                           
    $('#createEditNotice').html('You are in create mode&ensp;');      
    $('#createEditNotice').removeClass('warning');               
    $('#createEditNotice').addClass('information');                                                                          
    $('#editID').val('');                                                                          
    $.ajaxSetup({cache: false});
    //retrieve vendor details to display on form from getRow GET variable
    $.getJSON("lib/ajaxHandlers/ajaxGetTemplateForCreate.php", function (data) {
        //loop through all items in the JSON array  
        var code = data;
        if (code) {
            cm.editor.doc.setValue(code);
            $('input[name="fileName"]').val('');
            $(cm.editor.getWrapperElement()).show();
            cm.editor.refresh(); // make sure editor is refreshed after becoming unhidden
            $("#toolbar3").hide();
            $("#toolbar2").show();
        } else {
            errorDialog("Could not load base tempalte!");
        }
    });
}

function saveCreate(){
    var fileName = $('#fileName').val();
    var code = cm.editor.doc.getValue();
    
    if(fileName && !invalid(fileName) && !hasWhiteSpace(fileName)){
        request = $.ajax({
            url: "/lib/ajaxHandlers/ajaxAddTemplate.php",
            type: "post",
            async:false,
            cache:false,
            data: { fileName: fileName, code: code }
        });    
        if(request.responseText === '"success"'){
            window.location.href = "deviceConnTemplates.php?status=success";
        } else if(request.responseText === '"failed"') {
            window.location.href = "deviceConnTemplates.php?status=failed";
        } else if(request.responseText === '"duplicateFile"') {
            window.location.href = "deviceConnTemplates.php?status=duplicateFile";
        }
    } else {
        errorDialog('Please enter a valid filename. Do not use spaces, and only "_" or "-" as special characters')
    }
}

function editTemplate(id){
    if (id) {
    $('#createEditNotice').show();                                                                           
    $('#createEditNotice').html('You are in edit mode&ensp;');                                                                           
    $('#createEditNotice').removeClass('information');               
    $('#createEditNotice').addClass('warning');               
        $.ajaxSetup({cache: false});
        //retrieve vendor details to display on form from getRow GET variable
        $.getJSON("lib/ajaxHandlers/ajaxLoadTemplateforedit.php?id=" + id, function (data) {
            //loop through all items in the JSON array  
            var code = data.code;
            var fileName = data.fileName;
            var id = data.id;
            if (fileName) {
                $('#editID').val(id);                                                                          
                cm.editor.doc.setValue(code);
                $('input[name="fileName"]').val(fileName);
                $(cm.editor.getWrapperElement()).show();
                cm.editor.refresh(); // make sure editor is refreshed after becoming unhidden
                $("#toolbar2").hide();
                $("#toolbar3").show();
            } else {
                errorDialog("Could not load template!");
            }
        });
    } else {
        errorDialog("Please select a tempalte!");
    }
}

function saveEdit(){
    // YOUA ER ABOUT TO EDIT WARNING!!!!!
    var fileName = $('#fileName').val();
    var editId = $('#editID').val();   
    var code = cm.editor.doc.getValue();
    if(fileName && !invalid(fileName) && !hasWhiteSpace(fileName) && editId){
        request = $.ajax({
            url: "/lib/ajaxHandlers/ajaxEditTemplate.php",
            type: "post",
            async:false,
            cache:false,
            data: { fileName: fileName, code: code, id: editId }
        });    
        if(request.responseText === '"success"'){
            window.location.href = "deviceConnTemplates.php?status=success";
        } else if(request.responseText === '"failed"') {
            window.location.href = "deviceConnTemplates.php?status=failed";
        } else if(request.responseText === '"duplicateFile"') {
            window.location.href = "deviceConnTemplates.php?status=duplicateFile";
        }
    } else {
        errorDialog('Please enter a valid filename. Do not use spaces, and only "_" or "-" as special characters')
    }
}

function deleteTemplate(){
    var id = $("#templatesTbl tr.selected").attr('id');
    if(id){
        request = $.ajax({
            url: "/lib/ajaxHandlers/ajaxDeleteTemplate.php",
            type: "post",
            async:false,
            cache:false,
            data: {id: id }
        });    
        if(request.responseText === '"deleted"'){
            window.location.href = "deviceConnTemplates.php?status=deleted";
        }
    } else {
        errorDialog('You must select a row')
    }

}

function cancelCreate(){
    window.location.href = "deviceConnTemplates.php";
}

function backupTemplate(){
    window.location.href = "settingsBackup.php";
}