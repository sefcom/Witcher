$(document).ready(function () {
    if (location.href.match(/\error/)) {
        $('mainformDiv').show();
        $(".show_hide").show();
    } else {
        $(".mainformDiv").hide();
        $(".show_hide").show();
    }
    $('.show_hide').click(function () {
        $(".mainformDiv").toggle();
    });
});

// single row selector from rconfigFunctions.js
tblRowSelector('categoryTbl');


function delCategory() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Category?", 'lib/crud/categories.crud.php', "Please select a Category!");
}

function editCategory() {

    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/crud/categories.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {

            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var categoryName = data.categoryName;
                if (categoryName) {

                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="categoryName"]').val(categoryName);
                    $('input[name="editid"]').val(rowid); // used to populate id input so that edit script will insert
                } else {
                    errorDialog("Could not load data");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Category!");
    }
}