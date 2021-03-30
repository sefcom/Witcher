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
tblRowSelector('customPropTbl');

function delCustProp() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this Custom Property, and all data for this property?", 'lib/crud/customProperties.crud.php', "Please select a Custom Property!");
}