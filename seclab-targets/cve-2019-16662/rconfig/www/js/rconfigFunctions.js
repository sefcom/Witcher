// function to open new window based on content passed to the function
function writeConsole(content, filePath) {
    top.consoleRef = window.open('', 'myconsole', 'width=960,height=600' + ',menubar=0' + ',toolbar=0' + ',status=0' + ',scrollbars=1' + ',resizable=1');
    top.consoleRef.document.writeln(
            '<html><head><title>' + filePath +
            '</title><link rel="stylesheet" type="text/css" href="css/fileOutput.css" /></head>' +
            '<body onLoad="self.focus()"><div id="topDiv"><a href="lib/crud/downloadFile.php?download_file=' + filePath + '" rel="nofollow" title="click to download" alt="click to download"><img src="images/download.png" alt="download file..." title="download file..."/></a><strong>' + filePath +
            '</strong></div><div class="spacer"></div><br/>' + '<div style="font-family: Courier, \'Courier New\', monospace; font-size:11px">' +
            '<pre>' + content + '</pre>' + '</div>' + '</body></html>'
            );
    top.consoleRef.document.close();
}

function openHelp() {

    window.open('https://www.rconfig.com/help',
            'rConfig - Documentation',
            'width=, \
            height=800, \
            width=1000, \
            directories=no, \
            location=no, \
            menubar=no, \
            resizable=no, \
            scrollbars=0, \
            status=no, \
            toolbar=no');
    return false;

}

// enter key function
// http://stackoverflow.com/questions/979662/how-to-detect-pressing-enter-on-keyboard-using-jquery
$.fn.enterKey = function (fnc) {
    return this.each(function () {
        $(this).keypress(function (ev) {
            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
            if (keycode == '13') {
                fnc.call(this, ev);
            }
        });
    });
};

// bootbox dialog box standardised for rconfig alerts
function errorDialog(text) {
    var dialog = bootbox.dialog({
        size: 'small',
        title: "Notice!",
        backdrop: false,
        message: text,
        buttons: {
            main: {
                label: "close",
                className: "btn"
            }
        }
    });
    return dialog;
}


// standard deletion function for majority of rconfig forms
function removeItem(message, url, errorMsg) {
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        bootbox.confirm({
            message: message,
            backdrop: false,
            size: 'small',
            title: "Notice!",
            callback: function (result) {
                if (result) {
                    $.post(url, {
                        id: rowid,
                        del: "delete"
                    }, function (result) {
                        if (result.success) {
                            window.location.reload(); // reload the user current page
                        } else {
                            window.location.reload();
                        }
                    }, 'json');
                } else {
                    window.location.reload();
                }
            }
        });
    } else {
        errorDialog(errorMsg);
    }
}



// workaround to avoid refactoring for remove of .live in jquery 1.9 or later
// http://stackoverflow.com/questions/14354040/jquery-1-9-live-is-not-a-function
jQuery.fn.extend({
    live: function (event, callback) {
        if (this.selector) {
            jQuery(document).on(event, this.selector, callback);
        }
    }
});

// get url parameters
function getParameter(paramName) {
    var searchString = window.location.search.substring(1),
            i, val, params = searchString.split("&");

    for (i = 0; i < params.length; i++) {
        val = params[i].split("=");
        if (val[0] === paramName) {
            return val[1];
        }
    }
    return null;
}

// single row selector function for tables
function tblRowSelector(tableName) {
//    console.log('click')
    return $("#" + tableName).on('click', 'tr', function () {
        var checkbox = $(this).find(':checkbox');
        var rowid = checkbox.attr('id');
        var row = $(this);
        if (!row.hasClass('selected')) {
            row.addClass('selected')       //add class to clicked row
                    .siblings()                //get the other rows
                    .removeClass('selected');  //remove their classes
        }
        // clear all check boxes
        $('input[name=tablecheckbox]').each(function () {
            this.checked = false;
        });
        // select single box only
        checkbox.prop('checked', true);
    });
}


