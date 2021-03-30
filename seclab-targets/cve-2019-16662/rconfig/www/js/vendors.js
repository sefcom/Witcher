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
tblRowSelector('vendorsTbl');

// Next action when delVendor function is called from Delete button
function delVendor() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Vendor?", 'lib/crud/vendors.crud.php', "Please select a Vendor!");
}

// Next action when editVendor function is called from Delete button
function editVendor() {
    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.ajaxSetup({cache: false});
        //retrieve vendor details to display on form from getRow GET variable
        $.getJSON("lib/crud/vendors.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var vendorName = data.vendorName;
                if (vendorName) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="vendorName"]').val(vendorName);
                    $('input[name="editid"]').val(rowid);
                } else {
                    errorDialog("Could not load Data!");
                }
                $(".show_hide").show(); // show show_hide class 
            });
        });
    } else {
        errorDialog("Please select a Vendor!");
    }
}