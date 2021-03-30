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

});

// single row selector from rconfigFunctions.js
tblRowSelector('polElemTbl');

function delPolElem() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Policy Element?", 'lib/crud/compliancepolicyelements.crud.php', "Please select a Policy Element to delete!");
}

function editPolElem() {

    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/crud/compliancepolicyelements.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var elementName = data.elementName;
                var elementDesc = data.elementDesc;
                var singleParam1 = data.singleParam1;
                var singleLine1 = data.singleLine1;
                if (elementName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="elementName"]').val(elementName);
                    $('input[name="elementDesc"]').val(elementDesc);
                    $('#singleParam1').val(singleParam1).change();
                    $('input[name="singleLine1"]').val(singleLine1);
                    $('input[name="editid"]').val(rowid); // used to populate id input so that edit script will insert
                } else {
                    errorDialog("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Policy Element to edit!");
    }
}



// default back to no GETs or POSTS when click i.e. default devices page
function clearSearch() {
    window.location = "compliancepolicyelements.php";
}
