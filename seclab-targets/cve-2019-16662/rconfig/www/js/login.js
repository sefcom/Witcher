/* global detect*/
// browser detection http://sixrevisions.com/javascript/browser-detection-javascript/
var browserDetect = detect.parse(navigator.userAgent);

var noticeText = '<img src="images/exclamation.png" style="vertical-align: text-bottom;"/> Your browser (' + browserDetect.browser.family + ' ' + browserDetect.browser.version + ') is <b>out of date</b>. It may not render this site correctly. Learn how to update your browser <a href="compatibility.php"style="cursor:pointer;">here<a/>';
var notice = $("#broswerNotice p").html(noticeText);

checkBrowsers(browserDetect.browser.family, browserDetect.browser.version);

function checkBrowsers(family, version) {
    // set rConfig min browser versions
    var msieMinVer = 7;
    var chromeMinVer = 11;
    var firefoxMinVer = 4;
    var safariMinVer = 3;
    var operaMinVer = 3;

    if (family === 'Chrome' && version <= chromeMinVer
            ||
            family === 'msie' && version <= msieMinVer
            ||
            family === 'IE' && version <= msieMinVer
            ||
            family === 'Mozilla' && version <= firefoxMinVer
            ||
            family === 'Safari' && version <= safariMinVer
            ||
            family === 'Opera' && version <= operaMinVer)
    {
        $('#announcement').hide();
        $("#broswerNotice").show();
    }
}