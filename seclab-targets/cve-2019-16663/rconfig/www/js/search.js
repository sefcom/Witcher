$(document).ready(function () {

});

function search() {

    var ajax_load = "<img src='images/throbber.gif' alt='loading...' />";
    var searchTerm = $('#searchTerm').val();
    var catId = $('#catId').val();
    var catCommand = $('#catCommand').val();
    var nodeId = $('#nodeId').val();
    var noLines = $('#noLines').val();
    var linesBeforeAfter = $('#linesBeforeAfter').val();


    // validate searchTerm
    if (searchTerm === '' || searchTerm === ' ') { // check is not empty i.e. a node is actually selected
        errorDialog('Please enter a search term!');
        return;
    }


    var optionalGrep = $("input[name='grepField\\[\\]']").map(function () {
        return $(this).val();
    }).get();

    var grepSwitch = $("select[name='containsSelect\\[\\]']").map(function () {
        return $(this).val();
    }).get();

    if (catId === '') {
        errorDialog('Category not selected!');
        return;
    }
    // change the command option to the correct filename format for the grep string/var
    // this filename is inline with the filename created in /home/rconfig/scripts/showCmdScript.php $filenameFull var
    if (catCommand !== '') {
        var catCommand = catCommand.replace(/\s/g, '');
        var catCommand = catCommand + '*' + ".txt";
    } else {
        errorDialog('Command not selected!');
        return;
    }

    // validate no of lines leading/trailing is populated and create full or empty var for it to be added to URL
    if (noLines === null || noLines === '' || noLines === '0' || noLines === '00') { // noLines must not have any of these or return empty var
        // return empty var
        grepNumLine = "";
    } else {
        grepNumLine = " " + linesBeforeAfter + " " + noLines + " ";
    }

    // check if noLines is not an INT
    if (isNaN(noLines)) {
        errorDialog('No. of Lines is not a Number');
        return;
    }

    // if No. Lines has a value, then select for leading/trailing must be selected also or Error
    if (noLines > '0' && linesBeforeAfter == '') {
        errorDialog('Please select leading or trailing lines');
        return;
    }
    // ajax logic below
    if (searchTerm) {
        //retrieve vendor details to display on form from getRow GET variable
        $.ajaxSetup({cache: false});
        $.getJSON("lib/crud/search.crud.php?searchTerm=" + searchTerm + "&catId=" + catId + "&numLinesStr=" + grepNumLine + "&nodeId=" + nodeId + "&catCommand=" + catCommand + "&noLines=" + noLines, function (data) {
            var category = data.category;
            var fileCount = data.fileCount;
            var searchResult = data.searchResult;
            var timeTaken = data.timeTaken;
            var fileCount = data.fileCount;

            if (searchResult !== 'Empty') {
                // next iterate over the JSON array for searchResult:line and then append each line to the Div
                var html = [];

                $.each(data.searchResult, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                    var filePath = data.filePath;
                    var lines = obj.lines.join("");

                    var rowHTML = ['<tr class="row_' + key + '">'];
                    rowHTML.push('<td><a href="lib/crud/downloadFile.php?download_file=' + obj.filePath + '" rel="nofollow" title="click to view file" alt="click to view file">' + obj.device + '</a></td>');
                    rowHTML.push('<td>' + obj.date + '</td>');
                    rowHTML.push('<td style="font-family: Courier, \'Courier New\', monospace; font-size:11px">' + lines + '</td>');
                    rowHTML.push('</tr>');
                    html.push(rowHTML.join(''));
                    $('#timeTaken').html('Search Time:<strong> ' + timeTaken + ' (sec)</strong>');
                    $('#filesSearched').html('Files Searched:<strong> ' + fileCount + '</strong>');
                });
                $('#resultsTable tbody').html(html.join(''));
            } else if (searchResult === 'Empty') {
                var html = [];
                var rowHTML = ['<tr class="row_0">'];
                rowHTML.push('<td></td>');
                rowHTML.push('<td></td>');
                rowHTML.push('<td style="font-family: Courier, \'Courier New\', monospace; font-size:11px"><font color="red">No Results</font></td>');
                rowHTML.push('</tr>');
                html.push(rowHTML.join(''));
                $('#resultsTable tbody').html(html.join(''));
            }

        });
    } else {
        errorDialog("You did not enter a search term");
    }
}


function changeType() {
    var nodeId = document.getElementById('nodeIdDiv');
    var catCommand = document.getElementById('catCommandDiv');
    var catIdSelect = document.getElementById('catId');
    var catId = $('#catId').val();

    catCommand.style.display = catIdSelect.selectedIndex !== '' ? 'block' : 'none'; // check that anything other than '' is selected and display nodes dropdown
    nodeId.style.display = catIdSelect.selectedIndex !== '' ? 'block' : 'none'; // check that anything other than '' is selected and display nodes dropdown

    if (catId !== '') { // if catId is not equal to '' i.e. catId is selected then carry on
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxGetCommandsByCat.php?catId=" + catId, function (data) {
            var command = '';
            command += '<option value="">Please select</option>';
            for (var i = 0; i < data.length; i++) {
                command += '<option value="' + data[i].command + '">' + data[i].command + '</option>'; // need to specify command as value for option as this is what will populate the grep
            }
            $("select#catCommand").html(command);
        });
    }

    if (catId !== '') { // if catId is not equal to '' i.e. catId is selected then carry on
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxGetNodesByCat.php?catId=" + catId, function (data) {
            var options = '';
            options += '<option value="">Please select</option>';
            for (var i = 0; i < data.length; i++) {
                options += '<option value="' + data[i].deviceName + '">' + data[i].deviceName + '</option>'; // need to specify deviceName as value for option as this is what will populate the grep
            }
            $("select#nodeId").html(options);
        });
    }
}

function formReset() {
    document.getElementById("searchForm").reset();
}