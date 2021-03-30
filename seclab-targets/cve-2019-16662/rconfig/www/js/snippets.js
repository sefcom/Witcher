$(document).ready(function () {
    if (location.href.match(/\error/)) {
        $('mainformDiv').show();
        $(".show_hide").show();


        // else run a default page load		
    } else {
        $(".mainformDiv").hide();
        $(".show_hide").show();

    }
    $('.show_hide').click(function () {
        $(".mainformDiv").toggle();
    });

    // delete whitespace from Textarea
    var snippetTxtArea = document.getElementById('snippet');
    snippetTxtArea.value = snippetTxtArea.value.replace(/^\s*|\s*$/g, '');
});

// next script is for row highlighting and selection of table rows	
$("#snippetTbl tbody tr").click(function (e) {
    var rowid = $(this).attr('setid');

    $("#snippetTbl tbody tr").removeClass("selected");
    var $checkbox = $(this).find(':checkbox');
    $("#snippetTbl :checkbox").not($checkbox).removeAttr("checked");
    if (e.target.type === "checkbox") {

        // stop the bubbling to prevent firing the row's click event
        e.stopPropagation();
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    } else {
        $checkbox.attr('checked', !$checkbox.attr('checked'));
        $(this).filter(':has(:checkbox)').toggleClass('selected', $checkbox.attr('checked'));
    }
});

function delSnippet() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Snippet?", 'lib/crud/snippets.crud.php', "Please select a Snippet to delete!");
}

function editSnippet() {

    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/crud/snippets.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var snippetName = data.snippetName;
                var snippetDesc = data.snippetDesc;
                var snippet = data.snippet;
                if (snippetName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="snippetName"]').val(snippetName);
                    $('input[name="snippetDesc"]').val(snippetDesc);
                    $('textarea[name="snippet"]').val(snippet);
                    $('input[name="editid"]').val(rowid); // used to populate id input so that edit script will insert
                } else {
                    errorDialog("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Snippet to edit!");
    }
}

// default back to no GETs or POSTS when click i.e. default devices page
function clearSearch() {
    window.location = "snippets.php";
}
