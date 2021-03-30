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
tblRowSelector('userAddTbl');

function delUser() {
    // remove Item Function located at rconfigFunctions.js
    removeItem("Are you sure you want to remove this User?", 'lib/crud/userAdmin.crud.php', "Please select a User!");
}

function editUser() {
    var getRow = "getRow";
    var rowid = $("input:checkbox:checked").attr("id");
    if (rowid) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/crud/userAdmin.crud.php?id=" + rowid + "&getRow=" + getRow, function (data) {
            //loop through all items in the JSON array  
            $.each(data.rows, function (i, data) {
                var username = data.username;
                if (username) {
                    if ($('.mainformDiv').is(':hidden')) {
                        $('.mainformDiv').slideToggle();
                    }
                    $('input[name="username"]').val(username);
                    $('input[name="email"]').val(data.email);
                    $("#ulevelid").val(data.userlevel);
                    $('input[name="editid"]').val(rowid);
                } else {
                    errorDialog("Could not load data");
                }
                $(".show_hide").show();
            });
        });
    } else {
        errorDialog("Please select a User!");
    }
}