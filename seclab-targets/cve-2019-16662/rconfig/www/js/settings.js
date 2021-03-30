$(function () {
    /* global top */
    /* global bootbox */
    /* global answer */
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getDebugStatus", function (data) {
        $('#debugNoticeDiv').html(data);
    });
    $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getPhpLoggingStatus", function (data) {
        $('#getPhpLoggingStatusDiv').html(data);
    });
    $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getTimeZone", function (dataTime) {
        $("#timeZone").val(dataTime);
    });
    $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getDefaultCredsManualSet", function (dataCredSet) {
        $("#defaultCredsManualSet").val(dataCredSet);
    });
    
    
    $.getJSON("lib/ajaxHandlers/ajaxReadDirtoArr.php?path=/home/rconfig/logs/debugging/&ext=txt", function (data) {
        if ($.isEmptyObject(data) !== true) {
            var html = [];
            $.each(data, function (key, obj) { // example: http://jsfiddle.net/Xu7c4/13/
                var filename = obj.filename;
                var filepath = obj.filepath;
                var filesize = obj.filesize;

                var rowHTML = ['<div>'];
                rowHTML.push('<p><a href="#noLink" onclick="javascript:openFile(\'' + filepath + '\');">' + filename + ' - ' + filesize + '</p>');
                "onclick=javascript:openFile('[link]');";
                rowHTML.push('</div>');
                html.push(rowHTML.join(''));
            });
            $('#settingsDebugLogs').html(html.join(''));
            // pagination here: http://web.enavu.com/js/jquery/jpaginate-jquery-pagination-system-plugin/
            $("#settingsDebugLogs").jPaginate({items: 10, next: '', previous: ''});
        } else {
            $('#settingsDebugLogs').append('<tr><td><font color="red">Turn on debugging to collect debug files</font></td></tr>');
        }
    });

    $.ajaxSetup({cache: false});
    //retrieve vendor details to display on form from getRow GET variable
    $.getJSON("lib/ajaxHandlers/ajaxGetSMTPSettings.php", function (data) {
        //loop through all items in the JSON array  
        $.each(data, function (key, obj) {
            var smtpServerAddr = obj.smtpServerAddr;
            var smtpFromAddr = obj.smtpFromAddr;

            if (smtpServerAddr) {
                //output data to fields
                $('input[name="smtpServerAddr"]').val(smtpServerAddr);
                $('input[name="smtpFromAddr"]').val(obj.smtpFromAddr);
                $("#smtpRecipientAddr").val(obj.smtpRecipientAddr);
                if (obj.smtpAuth === "1") {
                    $('#smtpAuth').attr('checked', 'checked');
                    $('#authDiv').show();
                    $('input[name="smtpAuthUser"]').val(obj.smtpAuthUser);
                    $('input[name="smtpAuthPass"]').val(obj.smtpAuthPass);
                }
                if (obj.smtpLastTest.substring(0, 6) === "Passed") {
                    $("#smtpLastTest").html("<font color=\"green\">" + obj.smtpLastTest + "  - " + obj.smtpLastTestTime + "</font>");
                } else {
                    $("#smtpLastTest").html("<font color=\"red\">" + obj.smtpLastTest + " -  " + obj.smtpLastTestTime + "</font>");
                }
                $("#smtpUpdateButton").show();
                $("#smtpSaveButton").hide();


            } else {

                $("#smtpUpdateButton").hide();
                $("#smtpSaveButton").show();
            }
        });
    });

    // show/hide SMTP auth details based on checkbox
    
    
    $('#smtpAuth').live('change', function () {
        var smtpCheckBoxStatus = document.getElementById('smtpAuth').checked;
        if (smtpCheckBoxStatus === true) {
            $('#authDiv').show();
        } else {
            $('#authDiv').hide();
        }
    });

// when pressing Enter on text box, auto-click relevant Update button
    $(document).ready(function () {
        $.ajaxSetup({cache: false});
    });
});

// Open File by ajax
function openFile(filePath) {

    if (filePath) {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxGetFileByPath.php?path=" + filePath, function (data) {
            writeConsole(data.join('<br/>'), filePath);
        });
    } else {
        errorDialog('File not Selected!');
    }
}


function deleteDebugFiles(filePath, ext) {
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxDeleteAllLoggingFiles.php?path=" + filePath + "&ext=" + ext, function (data) {
        if (data.success === true) {
            errorDialog("Debug files deleted successfully");
        } else {
            errorDialog("Some files could not be deleted");
        }
        window.location.reload();
    });
}

function timeZoneChange() {    
    var timeZone = $('#timeZone').val();
    if (timeZone !== '') {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?timeZoneChange=" + timeZone, function (data) {

            $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getTimeZone", function (data2) {
                var timeZoneNotice = data2;
                $('#timeZoneNoticeDiv').html(data);
            });
        });
    } else {
        errorDialog('Could not set timeZone');
    }
}

function debugOnOff() {
    var debugOnOff = $('#debugOnOff').val();
    if (debugOnOff !== '') {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?debugOnOff=" + debugOnOff, function (data) {
            $.ajaxSetup({cache: false});
            $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getDebugStatus", function (data2) {
                var debugNotice = data2;
                $('#debugNoticeDiv').html(data + "<div class=\"break\"></div>" + debugNotice);
            });
        });
    } else {
        errorDialog('Could not set debug');
    }
}

function enableLDAPAuth() {
    var enableLDAPAuth = $('#enableLDAPAuth').is(':checked');
    var enableLDAPAuth = enableLDAPAuth ? 1 : 0;
    var ldap_host = $('#ldap_host').val();
    var ldap_dn = $('#ldap_dn').val();
    var ldap_user_group = $('#ldap_user_group').val();
    var ldap_admin_group = $('#ldap_admin_group').val();
    var ldap_usr_dom = $('#ldap_usr_dom').val();
    $.ajaxSetup({cache: false});
    $.ajax({
        type: "POST",
        url: "lib/ajaxHandlers/ajaxSettingsProcess.php?enableLDAPAuth=" + enableLDAPAuth,
        data: { enableLDAPAuth : enableLDAPAuth, ldap_host : ldap_host, ldap_dn : ldap_dn, ldap_user_group : ldap_user_group, ldap_admin_group : ldap_admin_group, ldap_usr_dom : ldap_usr_dom},
        dataType: 'json', 
            success: function(data){
                bootbox.hideAll();
//                bootbox.alert({ 
//                    message: '<div class="text-center"><img src="images/tick_32.png"/> LDAP Settings updated</div>' ,
//                    backdrop: false,
//                    size: 'large',
//                    title: 'Device Password Encryption Wizard',
//                 });
                 $('#ldapUpdated').show();
            },
            error:function(req, status, err){
                console.log( 'something went wrong', status, err );
                bootbox.hideAll();
                bootbox.alert({ 
                    message: '<div class="text-center"><img src="images/redCross.png"/> Something went wrong with the LDAP Update process.</div>' ,
                    backdrop: false,
                    size: 'large',
                    title: 'Device LDAP Update process' ,
                 });          
            }   
    });    
}

function showPasswords() {
    var checkBoxStatus = document.getElementById('passwordChkBox').checked;
    if (checkBoxStatus === true) {
        $('#defaultNodePassword').get(0).type = 'text';
        $('#defaultNodeEnable').get(0).type = 'text';
    } else {
        $('#defaultNodePassword').get(0).type = 'password';
        $('#defaultNodeEnable').get(0).type = 'password';
    }
}


function phpLoggingOnOff() {
    var phpLoggingOnOff = $('#phpLoggingOnOff').val();

    if (phpLoggingOnOff !== '') {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?phpLoggingOnOff=" + phpLoggingOnOff, function (data) {
            $.ajaxSetup({cache: false});
            $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?getPhpLoggingStatus", function (data2) {
                var getPhpLoggingStatus = data2;
                $('#getPhpLoggingStatusDiv').html(data + "<div class=\"break\"></div>" + getPhpLoggingStatus);
            });
        });
    } else {
        errorDialog('Could not set debug');
    }
}

function deviceToutGo() {
    var deviceToutVal = $('#deviceTout').val();
    if (deviceToutVal === null || deviceToutVal === '' || deviceToutVal === '0' || deviceToutVal === '00' || deviceToutVal === '000') {
        // if throw error
        errorDialog('Device Connection Timeout must be a value between 1-999');
    } else {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?deviceToutVal=" + deviceToutVal, function (data) {
            $('#deviceToutInfoDiv').html(data);
            $('#updated').slideDown('fast');
        });
    }
} // end deviceToutGo()

function pageTimeoutGo() {
    var pageTimeoutVal = $('#pageTimeout').val();

    if (pageTimeoutVal === null || pageTimeoutVal === '' || pageTimeoutVal === '0' || pageTimeoutVal === '00' || pageTimeoutVal === '000' || pageTimeoutVal <= 119) {
        errorDialog('Device Connection Timeout must be a value between 120 - 999999');
    } else {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?pageTimeoutVal=" + pageTimeoutVal, function (data) {
//            $('#pageTimeoutInfoDiv').html(data);
            $('#pageTimeOutUpdated').slideDown('fast');
        });
    }
} // end deviceToutGo


function pwencryption(){
    // general settings
    var boxsize = 'large';
    var boxTitle = "Device Password Encryption Wizard";
        bootbox.confirm({
        message: "Are you sure you want to enable device password encryption in the database? ",
        backdrop: false,
        size: boxsize,
        title: boxTitle,
        buttons: {
            confirm: {
               label: "Yes",
            },
            cancel: {
               label: "No",
            },
        },
        callback: function (result) {
            if (result == true) {
                bootbox.confirm({
                    message: "<img src='images/yellow-warning-sign_16.jpg'/> Have you backed up rConfig? If not, you need to before you proceed. <br/><br/><a href='settingsBackup.php'>Click here to run a backup!</a>",
                    backdrop: false,
                    size: boxsize,
                    title: boxTitle,
                        buttons: {
                            confirm: {
                               label: "I've already backed up!",
                            },
                            cancel: {
                               label: "I have not backed up!",
                            },
                    },    
                    callback: function (result) {
                        if (result == true) {
                                bootbox.prompt({
                                title: "Enter your encryption key",
                                inputType: 'password',
                                    callback: function (result) {
                                        var secret = result;
                                        /* your callback code */ 
                                        bootbox.confirm({
                                            message: "If you click 'Encrypt', every device password in your DB will be encrypted. If you passwords are already encrypted, this process will encrypt already encrypted passwords. Are you sure you want to proceed?",
                                            backdrop: false,
                                            size: boxsize,
                                            title: boxTitle,
                                            buttons: {
                                                confirm: {
                                                   label: "Encrypt!",
                                                },
                                                cancel: {
                                                   label: "Cancel",
                                                },
                                           },    
                                            callback: function (result) { 
                                                if (result == true) {
                                                    bootbox.dialog({ message: '<div class="text-center"><img src="images/ajax_loader2.gif"/> Encrypting...</div>' })
                                                    encryptPasswords(secret);
                                                }
                                            }
                                        })
                                    }
                                })
                        } else {
                           bootbox.alert({ 
                               message: '<div class="text-center"><img src="images/yellow-warning-sign_16.jpg"/> Please backup before you proceed. It\'s important!</div>' ,
                                backdrop: false,
                                size: boxsize,
                                title: boxTitle,
                           }) 

                        }
                    }
                });
            }
        }
    });
}

function encryptPasswords(secret){
    $.ajaxSetup({cache: false});
    $.ajax({
        type: "POST",
        url: "lib/ajaxHandlers/ajaxEncryptPasswords.php",
        data: "secret=" + secret,
        dataType: 'json',
            success: function(data){
                bootbox.hideAll();
                bootbox.alert({ 
                    message: '<div class="text-center"><img src="images/tick_32.png"/> X devices passwords enrcypted in the database</div>' ,
                    backdrop: false,
                    size: 'large',
                    title: 'Device Password Encryption Wizard',
                 });
                 location.reload();
            },
            error:function(req, status, err){
                console.log( 'something went wrong', status, err );
                bootbox.hideAll();
                bootbox.alert({ 
                    message: '<div class="text-center"><img src="images/redCross.png"/> Something went wrong with the encryption process.</div>' ,
                    backdrop: false,
                    size: 'large',
                    title: 'Device Password Encryption Wizard',
                 });          
            }   
    });
}

// function to open new window based on content passed to the function
function writeConsole(content, filePath) {
    top.consoleRef = window.open('', 'myconsole', 'width=750,height=600' + ',menubar=0' + ',toolbar=0' + ',status=0' + ',scrollbars=1' + ',resizable=1');
    top.consoleRef.document.writeln('<html><head><title>rConfig Debugging Logs</title></head>' + '<body bgcolor=white onLoad="self.focus()">' + filePath + '<br/>' + '<hr/>' + '<div STYLE="font-family: Courier, \'Courier New\', monospace; font-size:11px">' + '<pre style="line-height:.7;">' + content + '</pre>' + '</div>' + '</body></html>');
    top.consoleRef.document.close();
}

function purgeDevice() {
    bootbox.confirm({
        message: "Are you sure you want to purge deleted items from the Database? ",
        backdrop: false,
        size: 'small',
        title: "Notice!",
        callback: function (result) {
            if (result) {
                $.ajaxSetup({cache: false});
                // call ajax purge script and return either success or fail msg
                $.getJSON("lib/ajaxHandlers/ajaxPurgeSqlData.php", function (data) {
                    if (data) {
                        var response = data.response;
                        errorDialog(response);
                    }
                });
            }
        }
    });
}



// Next action when delCategory function is called from Delete button
function smtpClearSettings() {
    bootbox.confirm({
        message: "Are you sure you want to clear SMTP Settings?",
        backdrop: false,
        size: 'small',
        title: "Notice!",
        callback: function (result) {
    if (answer) {
        $.post('lib/crud/settingsEmail.crud.php', {
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
}

//  test SMTP mail sending and return success or fail 
function smtpTest() {
    $('#pleaseWait').slideDown('fast');
    $.ajaxSetup({cache: false});
    $.getJSON("lib/ajaxHandlers/ajaxSmtpTest.php", function (data) {
        if (data.success === true) {
            errorDialog("Email sent successfully");
            $('#pleaseWait').slideUp('fast');
        } else {
            errorDialog("Email failed to send - check logs for errors");
        }
        window.location.reload();
    });
}

function updateDefaultPass(defaultNodeUsername, defaultNodePassword, defaultNodeEnable) {
    var defaultNodeUsername = defaultNodeUsername;
    var defaultNodePassword = defaultNodePassword;
    var defaultNodeEnable = defaultNodeEnable;
    $.ajaxSetup({cache: false});
    $.getJSON('lib/ajaxHandlers/ajaxUpdateDefaultUserPass.php?defaultNodeUsername=' + defaultNodeUsername + '&defaultNodePassword=' + defaultNodePassword + '&defaultNodeEnable=' + defaultNodeEnable, function (data) {
        if (data) {
            var response = data;
            document.getElementById('updatedDefault').innerHTML = response;
            $('#updatedDefault').slideDown('fast');
        }
    });
}

function defaultCredsManualSet() {
    var defaultCredsManualSet = $('#defaultCredsManualSet').val();

    if (defaultCredsManualSet !== '') {
        $.ajaxSetup({cache: false});
        $.getJSON("lib/ajaxHandlers/ajaxSettingsProcess.php?defaultCredsManualSet=" + defaultCredsManualSet, function (data) {
            if (data) {
                var response = data;
                document.getElementById('updatedDefaultCredsManualSet').innerHTML = response;
                $('#updatedDefaultCredsManualSet').slideDown('fast');
            }
        });
    } else {
        errorDialog('Could not set default credentials setting when manually uploading & downloading configs');
    }
}
