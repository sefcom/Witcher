$(function () {
    $(document).ready(function () {
        $('#userUsername').focus();
        // when pressing Enter on password field text box, auto-click Submit button
        $('#userPassword').keypress(function (e) {
            if (e.keyCode === 13){
                $('#setUserCredentials').click();
            }
        });
    });
});

function setUserCredentials(userUsername, userPassword) {
    var rid = location.search.split('rid=')[1];
    var userUsername = userUsername;
    var userPassword = userPassword;

    //this function really only opens the configDevice popup
    window.open('configDevice.php?rid=' + rid + '&username=' + userUsername + '&password=' + userPassword,
            'Edit Account',
            'width=600, \
			   height=500, \
			   top=200, \
			   left=600, \
			   directories=no, \
			   location=no, \
			   menubar=no, \
			   resizable=no, \
			   scrollbars=yes, \
			   status=no, \
			   toolbar=no');
    return false;
}